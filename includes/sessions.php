<?php
ini_set('session.use_strict_mode', TRUE);
ini_set('session.use_only_cookies', TRUE);
ini_set('session.cookie_samesite', 'Lax');
if (Config::SECURE) {
    ini_set('session.cookie_secure', TRUE);
}

session_start();

class Sessions {
    private string $userAgent;
    private string $userIP;
    private int $userTime;
    private $userId;
    private $userLastLogin;

    public function __construct() {
        $this->userTime = time();
        $this->userAgent = $_SERVER['HTTP_USER_AGENT'];
        $this->userIP = isset($_SERVER['HTTP_X_FORWARDED_FOR']) && $_SERVER['HTTP_X_FORWARDED_FOR'] ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR'];
    }

    public function newSession($user) {
        $user_role = $user[0]['role'];
       
        $this->userId = $user[0]['_id']['$oid'];
        $user_firstName = $user[0]['firstName'];
        $user_lastName = $user[0]['lastName'];
        $user_email = $user[0]['email'];
        $user_avatar = $user[0]['avatar'];
        $conversation_views = $user[0]['conversationViews'];
        $notification_timestamp = $user[0]['notificationTimestamp']['$date'];
        $user_trusted = $user[0]['trusted'];
        $this->userLastLogin = $user[0]['lastLogin']['$date'] / 1000;
     
        $this->regenerateSession();

        // Connect to database
        try {
            $groups = json_decode(json_encode(get_groups()), TRUE);
            $user_groups = json_decode(json_encode(get_groups_by_user($this->userId)), TRUE);
            
        } catch (Exception $e) {
            echo $e->getMessage();
            die();
        }

        // Build list of affiliated partners
        $_SESSION['partners'] = [];
        $_SESSION['myGroups'] = [];
        $_SESSION['groups'] = [];
        foreach ($user_groups as $group) {
            $_SESSION['partners'][] = [
                'id' => $group['_id']['$oid'],
                'name' => $group['name'],
                'image' => $group['tile'],
            ];
            $_SESSION['myGroups'][] = $group['_id']['$oid'];
        }
        foreach ($groups as $group1) {
            $_SESSION['groups'][$group1['_id']['$oid']] = $group1['name'];
        }

        $_SESSION['uid'] = $this->userId;
        $_SESSION['firstName'] = $user_firstName;
        $_SESSION['lastName'] = $user_lastName;
        $_SESSION['avatar'] = $user_avatar;
        $_SESSION['email'] = $user_email;
        $_SESSION['conversations'] = $conversation_views;
        $_SESSION['userRole'] = $user_role;
        if ($user_trusted == TRUE) {
            $_SESSION['trusted'] = 'yes';
        }
        else {
            $_SESSION['trusted'] = 'no';
        }

        // Setup notifications status
        if ($notification_timestamp == NULL) {
            $new_timestamp = json_decode(json_encode(update_notifications_timestamp()), TRUE);

            if ($new_timestamp != FALSE) {
                $notification_timestamp = $new_timestamp['$date']['$numberLong'];
            }
            else {
                $notification_timestamp = "nothing";
            }
        }

        $_SESSION['notificationTimestamp'] = $notification_timestamp;

        update_last_login_timestamp();
        update_ip_address();
        update_total_logins();

        $activity_data[] = $this->userIP;
        $activity_data[] = $this->userAgent;
        $activity_data[] = $user_email;
        new_activity_log($_SESSION['uid'], 'logged in', $activity_data);

        if ($_GET['status'] == 'bsm') { // Bypass server maintenance
            $_SESSION['bsm'] = 1;
        }
    }

    public function regenerateSession($reload=FALSE) {
        if (!isset($_SESSION['userIP']) || $reload || !Config::SESSION_IP_CHECK) {
            $_SESSION['userIP'] = $this->userIP;
        }

        if (!isset($_SESSION['userAgent']) || $reload) {
            $_SESSION['userAgent'] = $_SERVER['HTTP_USER_AGENT'];
        }

        // Set current session to expire in 1 minute
        $_SESSION['OBSOLETE'] = TRUE;
        $_SESSION['OLD_EXPIRES'] = $this->userTime + Config::SESSION_OLD_EXPIRE;
        $_SESSION['EXPIRES'] = $this->userTime + Config::SESSION_EXPIRE;

        // Create new session without destroying the old one
        session_regenerate_id(FALSE);

        // Grab current session ID and close both sessions to allow other scripts to use them
        $newSession = session_id();
        session_write_close();

        // Set session ID to the new one, and start it back up again
        session_id($newSession);
        session_start();

        // Remove from the new session, since it isn't obsolete
        $_SESSION['OBSOLETE'] = FALSE;
        unset($_SESSION['OLD_EXPIRES']);
    }

    public function sessionCheck() {
        try {
            if (Config::SESSION_IP_CHECK && $_SESSION['userIP'] != $this->userIP) {
            
                throw new Exception('IP Address mixmatch (possible session hijacking attempt).');
            }

            if ($_SESSION['userAgent'] != $this->userAgent) {
                
                throw new Exception('Useragent mixmatch (possible session hijacking attempt).');
            }

            if ($_SESSION['OBSOLETE'] && ($_SESSION['OLD_EXPIRES'] < $this->userTime)) {
               
                throw new Exception('Attempt to use expired session.');
            } elseif ($_SESSION['OBSOLETE']) {
                $this->regenerateSession();
            }

            if (!$_SESSION['OBSOLETE'] && $_SESSION['EXPIRES'] < $this->userTime) {
                $this->regenerateSession();
            }

            return true;

        } catch(Exception $e) {
            $activity_data[] = $this->userIP;
            $activity_data[] = $this->userAgent;
            $activity_data[] = $e->getMessage();
            new_activity_log(0, 'failed session check', $activity_data);
            return false;
        }
    }

    /**
     * @return mixed|string
     */
    public function getUserAgent() {
        return $this->userAgent;
    }

    /**
     * @param mixed|string $userAgent
     */
    public function setUserAgent($userAgent): void {
        $this->userAgent = $userAgent;
    }

    /**
     * @return mixed|string
     */
    public function getUserIP() {
        return $this->userIP;
    }

    /**
     * @param mixed|string $userIP
     */
    public function setUserIP($userIP): void {
        $this->userIP = $userIP;
    }

    /**
     * @return int
     */
    public function getUserTime(): int {
        return $this->userTime;
    }

    /**
     * @param int $userTime
     */
    public function setUserTime(int $userTime): void {
        $this->userTime = $userTime;
    }

    /**
     * @return mixed
     */
    public function getUserLastLogin() {
        return $this->userLastLogin;
    }

    /**
     * @param mixed $userLastLogin
     */
    public function setUserLastLogin($userLastLogin): void {
        $this->userLastLogin = $userLastLogin;
    }

    /**
     * @return mixed
     */
    public function getUserId() {
        return $this->userId;
    }

    /**
     * @param mixed $userId
     */
    public function setUserId($userId): void {
        $this->userId = $userId;
    }
}
