<?php

// Get config settings
function get_config() {
  $mongo = new MongoDB\Driver\Manager(Config::MONGODB);
  $query = new MongoDB\Driver\Query([], []);
	$cursor = $mongo->executeQuery('tc.config', $query);
	$config = [];
	foreach ($cursor as $document) {
		array_push($config, json_decode(MongoDB\BSON\toJSON(MongoDB\BSON\fromPHP($document))));
	}
	return $config;
}

// Return user-friendly time
function timestamp($string, $format = NULL) {
  //return date('D j M Y g:iA', $string/1000);
  if ($format) {
    return date($format, $string/1000);
  } else {
    return date('D j M Y', $string/1000);
  }
}

// Test for valid BSON id
function isValid($value) {
    if ($value instanceof \MongoDB\BSON\ObjectID) {
        return true;
    }
    try {
        new \MongoDB\BSON\ObjectID($value);
        return true;
    } catch (\Exception $e) {
        return false;
    }
}

// Highlight keywords
function highlight($text, $words) {
    preg_match_all('~[A-Za-z0-9_äöüÄÖÜ]+~', $words, $m);
    if (!$m)
        return $text;
    $re = '~(' . implode('|', $m[0]) . ')~i';
    return preg_replace($re, '<span class="search-highlight">$0</span>', $text);
}

// Rotate images to correct orientation if provided
function autorotate(Imagick $image) {
    switch ($image->getImageOrientation()) {
    case Imagick::ORIENTATION_TOPLEFT:
        break;
    case Imagick::ORIENTATION_TOPRIGHT:
        $image->flopImage();
        break;
    case Imagick::ORIENTATION_BOTTOMRIGHT:
        $image->rotateImage("#000", 180);
        break;
    case Imagick::ORIENTATION_BOTTOMLEFT:
        $image->flopImage();
        $image->rotateImage("#000", 180);
        break;
    case Imagick::ORIENTATION_LEFTTOP:
        $image->flopImage();
        $image->rotateImage("#000", -90);
        break;
    case Imagick::ORIENTATION_RIGHTTOP:
        $image->rotateImage("#000", 90);
        break;
    case Imagick::ORIENTATION_RIGHTBOTTOM:
        $image->flopImage();
        $image->rotateImage("#000", 90);
        break;
    case Imagick::ORIENTATION_LEFTBOTTOM:
        $image->rotateImage("#000", -90);
        break;
    default: // Invalid orientation
        break;
    }
    $image->setImageOrientation(Imagick::ORIENTATION_TOPLEFT);
    return $image;
}


// Extracts the youtube id from a youtube url.
// Returns false if the url is not recognized as a youtube url.
function getYoutubeId($url)
{
  if (strpos($url, '/channel/') !== false) {

  } else {
    $parts = parse_url($url);
    if (isset($parts['host'])) {
        $host = $parts['host'];
        if (
            false === strpos($host, 'youtube') &&
            false === strpos($host, 'youtu.be')
        ) {
            return false;
        }
    }
    if (isset($parts['query'])) {
        parse_str($parts['query'], $qs);
        if (isset($qs['v'])) {
            return $qs['v'];
        }
        else if (isset($qs['vi'])) {
            return $qs['vi'];
        }
    }
    if (isset($parts['path'])) {
        $path = explode('/', trim($parts['path'], '/'));
        return $path[count($path) - 1];
    }
    return false;
  }
}

// Update user password
function update_user_password($uid, $pass) {
  $mongo = new MongoDB\Driver\Manager(Config::MONGODB);
	$bulk = new MongoDB\Driver\BulkWrite;
	$bulk->update(
		['_id' => new MongoDB\BSON\ObjectID($uid)],
		['$set' =>
			[
				'password' => password_hash($pass, PASSWORD_DEFAULT),
			]
		],
		[
			'multi' => false,
			'upsert' => false
		]
	);
	$result = $mongo->executeBulkWrite('tc.users', $bulk);
	if ($result->getModifiedCount() >= 1) {
		return true;
	} else {
		return false;
	}
}

// Update reset log
function update_reset_log($id, $status) {
  $mongo = new MongoDB\Driver\Manager(Config::MONGODB);
	$bulk = new MongoDB\Driver\BulkWrite;
	$bulk->update(
		['_id' => new MongoDB\BSON\ObjectID($id)],
		['$set' =>
			[
				'status' => $status,
			]
		],
		[
			'multi' => false,
			'upsert' => false
		]
	);
	$result = $mongo->executeBulkWrite('tc.resets', $bulk);
	if ($result->getModifiedCount() >= 1) {
		return true;
	} else {
		return false;
	}
}

// Update groups session variables
function update_groups_session() {
  // Connect to database
  try {
    $groups = json_decode(json_encode(get_groups()), true);
    $user_groups = json_decode(json_encode(get_groups_by_user($_SESSION['uid'])), true);
  } catch (Exception $e) {
    echo $e->getMessage();
    die();
  }
  $_SESSION['partners'] = [];
  $_SESSION['myGroups'] = [];
  $_SESSION['groups'] = [];
  foreach ($user_groups as $group) {
    $_SESSION['partners'][] = array('id' => $group['_id']['$oid'], 'name' => $group['name'], 'image' => $group['tile']);
    $_SESSION['myGroups'][] = $group['_id']['$oid'];
  }
  foreach ($groups as $group) {
    $_SESSION['groups'][$group['_id']['$oid']] = $group['name'];
  }
}

// Update read timestamp for conversations
function update_conversation_timestamp($cid) {
  // $newDate = new MongoDB\BSON\UTCDateTime(time()*1000);
  $newDate = (int)round(microtime(true) * 1000);
  $_SESSION['conversations'][$cid] = $newDate;
  $mongo = new MongoDB\Driver\Manager(Config::MONGODB);
	$bulk = new MongoDB\Driver\BulkWrite;
	$bulk->update(
		['_id' => new MongoDB\BSON\ObjectID($_SESSION['uid'])],
		['$set' =>
			[
        'conversationViews' => $_SESSION['conversations']
			]
		],
		[
			'multi' => false,
			'upsert' => false
		]
	);
	$result = $mongo->executeBulkWrite('tc.users', $bulk);
	if ($result->getModifiedCount() >= 1) {
		return true;
	} else {
		return false;
	}
}

// Update read timestamp for notifications
function update_notifications_timestamp($uid = NULL) {
  if (!$uid) {
    $uid = $_SESSION['uid'];
  }
  $newDate = new MongoDB\BSON\UTCDateTime(time()*1000);
  $mongo = new MongoDB\Driver\Manager(Config::MONGODB);
	$bulk = new MongoDB\Driver\BulkWrite;
	$bulk->update(
		['_id' => new MongoDB\BSON\ObjectID($uid)],
		['$set' =>
			[
				'notificationTimestamp' => $newDate,
			]
		],
		[
			'multi' => false,
			'upsert' => false
		]
	);
	$result = $mongo->executeBulkWrite('tc.users', $bulk);
	if ($result->getModifiedCount() >= 1) {
    return (string)$newDate;
	} else {
    return false;
  }
}

// Update last page view time on user profile
function update_last_active_timestamp() {
  $newDate = new MongoDB\BSON\UTCDateTime(time()*1000);
  $mongo = new MongoDB\Driver\Manager(Config::MONGODB);
	$bulk = new MongoDB\Driver\BulkWrite;
	$bulk->update(
		['_id' => new MongoDB\BSON\ObjectID($_SESSION['uid'])],
		['$set' =>
			[
				'lastActive' => $newDate
			]
		],
		[
			'multi' => false,
			'upsert' => false
		]
	);
	$result = $mongo->executeBulkWrite('tc.users', $bulk);
	if ($result->getModifiedCount() >= 1) {
		return true;
	} else {
		return false;
	}
}

// Update last login time on user profile
function update_last_login_timestamp() {
  $newDate = new MongoDB\BSON\UTCDateTime(time()*1000);
  $mongo = new MongoDB\Driver\Manager(Config::MONGODB);
	$bulk = new MongoDB\Driver\BulkWrite;
	$bulk->update(
		['_id' => new MongoDB\BSON\ObjectID($_SESSION['uid'])],
		['$set' =>
			[
				'lastLogin' => $newDate
			]
		],
		[
			'multi' => false,
			'upsert' => false
		]
	);
	$result = $mongo->executeBulkWrite('tc.users', $bulk);
	if ($result->getModifiedCount() >= 1) {
		return true;
	} else {
		return false;
	}
}

// Update ip address on user profile
function update_ip_address() {
  $user_ip = get_user_ip_address();
  $mongo = new MongoDB\Driver\Manager(Config::MONGODB);
	$bulk = new MongoDB\Driver\BulkWrite;
	$bulk->update(
		['_id' => new MongoDB\BSON\ObjectID($_SESSION['uid'])],
		['$set' =>
			[
				'ipAddress' => $user_ip
			]
		],
		[
			'multi' => false,
			'upsert' => false
		]
	);
	$result = $mongo->executeBulkWrite('tc.users', $bulk);
	if ($result->getModifiedCount() >= 1) {
		return true;
	} else {
		return false;
	}
}

// Update total logins on user profile
function update_total_logins() {
  $newDate = new MongoDB\BSON\UTCDateTime(time()*1000);
  $mongo = new MongoDB\Driver\Manager(Config::MONGODB);
	$bulk = new MongoDB\Driver\BulkWrite;
	$bulk->update(
		['_id' => new MongoDB\BSON\ObjectID($_SESSION['uid'])],
		['$inc' =>
			[
				'totalLogins' => 1
			]
		],
		[
			'multi' => false,
			'upsert' => false
		]
	);
	$result = $mongo->executeBulkWrite('tc.users', $bulk);
	if ($result->getModifiedCount() >= 1) {
		return true;
	} else {
		return false;
	}
}

// Update view count on single item view
function update_view_count($pid) {
  $mongo = new MongoDB\Driver\Manager(Config::MONGODB);
	$bulk = new MongoDB\Driver\BulkWrite;
	$bulk->update(
		['_id' => new MongoDB\BSON\ObjectID($pid)],
		['$inc' =>
			[
				'totalViews' => 1
			]
		],
		[
			'multi' => false,
			'upsert' => false
		]
	);
	$result = $mongo->executeBulkWrite('tc.posts', $bulk);
	if ($result->getModifiedCount() >= 1) {
		return true;
	} else {
		return false;
	}
}

// Update member view count (single item view only)
function update_member_view_count() {
  $mongo = new MongoDB\Driver\Manager(Config::MONGODB);
	$bulk = new MongoDB\Driver\BulkWrite;
	$bulk->update(
		['_id' => new MongoDB\BSON\ObjectID($_SESSION['uid'])],
		['$inc' =>
			[
				'postViews' => 1
			]
		],
		[
			'multi' => false,
			'upsert' => false
		]
	);
	$result = $mongo->executeBulkWrite('tc.users', $bulk);
	if ($result->getModifiedCount() >= 1) {
		return true;
	} else {
		return false;
	}
}

// Get all groups data
function get_groups() {
	$mongo = new MongoDB\Driver\Manager(Config::MONGODB);
  $query = new MongoDB\Driver\Query([], []);
	$cursor = $mongo->executeQuery('tc.partners', $query);
	$groups = [];
	foreach ($cursor as $document) {
		array_push($groups, json_decode(MongoDB\BSON\toJSON(MongoDB\BSON\fromPHP($document))));
	}
	return $groups;
}

// Get all groups data ordered by display_order
function get_groups_ordered() {
	$mongo = new MongoDB\Driver\Manager(Config::MONGODB);
  // $query = new MongoDB\Driver\Query([], []);
  $query = new MongoDB\Driver\Query(['hidden' => ['$ne' => '1']], ['sort' => ['display_order' => -1]]);
	$cursor = $mongo->executeQuery('tc.partners', $query);
	$groups = [];
	foreach ($cursor as $document) {
		array_push($groups, json_decode(MongoDB\BSON\toJSON(MongoDB\BSON\fromPHP($document))));
	}
	return $groups;
}

// Get group data by id
function get_group($gid) {
	$mongo = new MongoDB\Driver\Manager(Config::MONGODB);
	$query = new MongoDB\Driver\Query(['_id' => new MongoDB\BSON\ObjectID($gid)], []);
	$cursor = $mongo->executeQuery('tc.partners', $query);
	$group = [];
	foreach ($cursor as $document) {
		array_push($group, json_decode(MongoDB\BSON\toJSON(MongoDB\BSON\fromPHP($document))));
	}
	return $group;
}

// Get individual group data by userid
function get_open_groups() {
	$mongo = new MongoDB\Driver\Manager(Config::MONGODB);
  $query = new MongoDB\Driver\Query(
    [
      'privacy' => 'public'
    ],
    []
  );
	$cursor = $mongo->executeQuery('tc.partners', $query);
	$groups = [];
	foreach ($cursor as $document) {
		array_push($groups, json_decode(MongoDB\BSON\toJSON(MongoDB\BSON\fromPHP($document))));
	}
	return $groups;
}

// Get individual group data by userid
function get_groups_by_user($uid) {
	$mongo = new MongoDB\Driver\Manager(Config::MONGODB);
  $uid = explode(" ",$uid);
  $query = new MongoDB\Driver\Query(['users' => ['$in' => $uid]], []);
	$cursor = $mongo->executeQuery('tc.partners', $query);
	$groups = [];
	foreach ($cursor as $document) {
		array_push($groups, json_decode(MongoDB\BSON\toJSON(MongoDB\BSON\fromPHP($document))));
	}
	return $groups;
}

// Get group knocks by userid
function get_knocks_by_user($uid) {
	$mongo = new MongoDB\Driver\Manager(Config::MONGODB);
  $uid = explode(" ",$uid);
  $query = new MongoDB\Driver\Query(['users_knocking' => ['$in' => $uid]], []);
	$cursor = $mongo->executeQuery('tc.partners', $query);
	$groups = [];
	foreach ($cursor as $document) {
		array_push($groups, json_decode(MongoDB\BSON\toJSON(MongoDB\BSON\fromPHP($document))));
	}
	return $groups;
}

// Get group data by id
function get_reset_log($id) {
	$mongo = new MongoDB\Driver\Manager(Config::MONGODB);
	$query = new MongoDB\Driver\Query(['_id' => new MongoDB\BSON\ObjectID($id)], []);
	$cursor = $mongo->executeQuery('tc.resets', $query);
	$reset = [];
	foreach ($cursor as $document) {
		array_push($reset, json_decode(MongoDB\BSON\toJSON(MongoDB\BSON\fromPHP($document))));
	}
	return $reset;
}

// Get group data by id
function get_last_reset_log($uid) {
	$mongo = new MongoDB\Driver\Manager(Config::MONGODB);
	$query = new MongoDB\Driver\Query(['user_id' => $uid], ['limit' => 1, 'sort' => ['time' => -1], 'projection' => []]);
	$cursor = $mongo->executeQuery('tc.resets', $query);
	$reset = [];
	foreach ($cursor as $document) {
		array_push($reset, json_decode(MongoDB\BSON\toJSON(MongoDB\BSON\fromPHP($document))));
	}
	return $reset;
}

// Get group data by name
function find_group($name) {
	$mongo = new MongoDB\Driver\Manager(Config::MONGODB);
	$query = new MongoDB\Driver\Query(['name' => $name], ['limit' => 1, 'projection' => []]);
	$cursor = $mongo->executeQuery('tc.partners', $query);
  $group = [];
	foreach ($cursor as $document) {
		array_push($group, json_decode(MongoDB\BSON\toJSON(MongoDB\BSON\fromPHP($document))));
	}
  return $group;
}

// Remove user from group
function remove_group_user($gid, $user) {
	$mongo = new MongoDB\Driver\Manager(Config::MONGODB);
  $bulk = new MongoDB\Driver\BulkWrite;
	$bulk->update(['_id' => new MongoDB\BSON\ObjectID($gid)], ['$pull' => ['users' => ['$in' => $user]]]);
  $result = $mongo->executeBulkWrite('tc.partners', $bulk);

	if ($result->getModifiedCount() >= 1) {

		return true;

	} else {

		return false;

	}
}

// Remove user from knocking
function remove_knocking_user($gid, $user) {
  // Make sure user_array is an actual array
  if ($user == NULL) {
    $user = [];
  }
  if (is_array($user)) { } else {
    $user = array($user);
  }
	$mongo = new MongoDB\Driver\Manager(Config::MONGODB);
  $bulk = new MongoDB\Driver\BulkWrite;
	$bulk->update(['_id' => new MongoDB\BSON\ObjectID($gid)], ['$pull' => ['users_knocking' => ['$in' => $user]]]);
  $result = $mongo->executeBulkWrite('tc.partners', $bulk);

	if ($result->getModifiedCount() >= 1) {

		return true;

	} else {

		return false;

	}
}

// Add user to group
function add_group_user($gid, $user) {
	$mongo = new MongoDB\Driver\Manager(Config::MONGODB);
 	$bulk = new MongoDB\Driver\BulkWrite;
	$bulk->update(
    ['_id' => new MongoDB\BSON\ObjectID($gid)],
		['$addToSet' =>
			[
        'users' => $user
      ]
		]);
  $result = $mongo->executeBulkWrite('tc.partners', $bulk);

	if ($result->getModifiedCount() >= 1) {

		return true;

	} else {

		return false;

	}
}

// Add user to knocking list
function add_knocking_user($gid, $user) {
	$mongo = new MongoDB\Driver\Manager(Config::MONGODB);
  $bulk = new MongoDB\Driver\BulkWrite;
	$bulk->update(
    ['_id' => new MongoDB\BSON\ObjectID($gid)],
    ['$addToSet' =>
      [
        'users_knocking' => $user
      ]
    ]);
  $result = $mongo->executeBulkWrite('tc.partners', $bulk);

	if ($result->getModifiedCount() >= 1) {

		return true;

	} else {

		return false;

	}
}

// Update group data
function update_group($gid, $user_array) {
  // Make sure user_array is an actual array
  if ($user_array == NULL) {
    $user_array = [];
  }
  if (is_array($user_array)) { } else {
    $user_array = array($user_array);
  }
	$mongo = new MongoDB\Driver\Manager(Config::MONGODB);
	$bulk = new MongoDB\Driver\BulkWrite;
	$bulk->update(
		['_id' => new MongoDB\BSON\ObjectID($gid)],
		['$set' =>
			[
				'users'	=> $user_array
			]
		],
		[
			'multi' => false,
			'upsert' => false
		]
	);
	$result = $mongo->executeBulkWrite('tc.partners', $bulk);

	if ($result->getModifiedCount() >= 1) {

		return count($user_array);

	} else {

		return false;

	}
}

// Add user to following
function add_user_following($userId) {
	$mongo = new MongoDB\Driver\Manager(Config::MONGODB);
  $bulk = new MongoDB\Driver\BulkWrite;
	$bulk->update(['_id' => new MongoDB\BSON\ObjectID($_SESSION['uid'])], ['$addToSet' => ['following' => $userId]]);
  $result = $mongo->executeBulkWrite('tc.users', $bulk);

	if ($result->getModifiedCount() >= 1) {

		return true;

	} else {

		return false;

	}
}

// Add user to following another user
function add_user_to_user_following($follower, $followee) {
	$mongo = new MongoDB\Driver\Manager(Config::MONGODB);
  $bulk = new MongoDB\Driver\BulkWrite;
	$bulk->update(['_id' => new MongoDB\BSON\ObjectID($follower)], ['$addToSet' => ['following' => $followee]]);
  $result = $mongo->executeBulkWrite('tc.users', $bulk);

	if ($result->getModifiedCount() >= 1) {

		return true;

	} else {

		return false;

	}
}

// Remove user from following
function remove_user_following($userId) {
	$mongo = new MongoDB\Driver\Manager(Config::MONGODB);
  $bulk = new MongoDB\Driver\BulkWrite;
  $bulk->update(['_id' => new MongoDB\BSON\ObjectID($_SESSION['uid'])], ['$pull' => ['following' => ['$in' => $userId]]]);
  $result = $mongo->executeBulkWrite('tc.users', $bulk);

	if ($result->getModifiedCount() >= 1) {

		return true;

	} else {

		return false;

	}
}

// Get number of followers by userid
function get_followers_by_user($userId, $limit = NULL, $sort = NULL, $sort_order = NULL, $offset = NULL) {
  $userId = explode(" ",$userId);
  if ($sort == NULL) { $sort = 'firstName'; }
  if ($sort_order == NULL) { $sort_order = 1; }
	$mongo = new MongoDB\Driver\Manager(Config::MONGODB);
  $query = new MongoDB\Driver\Query(['following' => ['$in' => $userId]], ['limit' => $limit, 'sort' => [$sort => $sort_order], 'skip' => $offset]);
	$cursor = $mongo->executeQuery('tc.users', $query);
	$groups = [];
	foreach ($cursor as $document) {
		array_push($groups, json_decode(MongoDB\BSON\toJSON(MongoDB\BSON\fromPHP($document))));
	}
	return $groups;
}

// Get number of helpfuls by user  NOT CURRENTLY USED!
function get_number_helpfuls_by_user($userId) {
  $userId = explode(" ",$userId);
	$mongo = new MongoDB\Driver\Manager(Config::MONGODB);
	$query = new MongoDB\Driver\Query(['reactions.thumbsup' => ['$in' => $userId]], []);
	$cursor = $mongo->executeQuery('tc.posts', $query);
	$posts = [];
	foreach ($cursor as $document) {
		array_push($posts, json_decode(MongoDB\BSON\toJSON(MongoDB\BSON\fromPHP($document))));
	}
	return $posts;
}

// Get number of posts by user
function get_number_posts_by_user($userId) {
	$mongo = new MongoDB\Driver\Manager(Config::MONGODB);
	$query = new MongoDB\Driver\Query(['userId' => $userId], ['projection' => ['type' => 1, 'isAnonymous' => 1]]);
	$cursor = $mongo->executeQuery('tc.posts', $query);
	$posts = [];
	foreach ($cursor as $document) {
		array_push($posts, json_decode(MongoDB\BSON\toJSON(MongoDB\BSON\fromPHP($document))));
	}
	return $posts;
}

// Get topics
function get_all_topics() {
	$mongo = new MongoDB\Driver\Manager(Config::MONGODB);
	$query = new MongoDB\Driver\Query([], []);
	$cursor = $mongo->executeQuery('tc.topics', $query);
	$posts = [];
	foreach ($cursor as $document) {
		array_push($posts, json_decode(MongoDB\BSON\toJSON(MongoDB\BSON\fromPHP($document))));
	}
	return $posts;
}

// Get posts
function get_posts($limit, $offset = NULL) {
	$mongo = new MongoDB\Driver\Manager(Config::MONGODB);
	$query = new MongoDB\Driver\Query(['audience' => NULL], ['limit' => $limit, 'sort' => ['time' => -1], 'skip' => $offset]);
	$cursor = $mongo->executeQuery('tc.posts', $query);
	$posts = [];
	foreach ($cursor as $document) {
		array_push($posts, json_decode(MongoDB\BSON\toJSON(MongoDB\BSON\fromPHP($document))));
	}
	return $posts;
}

// Get posts by topic
function get_posts_by_topic($topic, $groups, $limit, $offset = NULL) {
	$mongo = new MongoDB\Driver\Manager(Config::MONGODB);
	$query = new MongoDB\Driver\Query(
    [ '$and' =>
        [
          [
            'categories' => [ '$in' => $topic ],
            '$or' =>
              [
                ['audience' => ['$in' => $groups]],
                ['audience' => NULL]
              ]
          ]
        ]
    ],
    [
      'limit' => $limit,
      'sort' => ['time' => -1],
      'skip' => $offset
    ]
  );
	$cursor = $mongo->executeQuery('tc.posts', $query);
	$posts = [];
	foreach ($cursor as $document) {
		array_push($posts, json_decode(MongoDB\BSON\toJSON(MongoDB\BSON\fromPHP($document))));
	}
	return $posts;
}

// Get posts by topic
function get_posts_by_topics($topics, $groups, $limit, $offset = NULL) {
	$mongo = new MongoDB\Driver\Manager(Config::MONGODB);
	$query = new MongoDB\Driver\Query(
    [ '$and' =>
        [
          [
            'categories' => [ '$in' => $topics ],
            '$or' =>
              [
                ['audience' => ['$in' => $groups]],
                ['audience' => NULL]
              ]
          ]
        ]
    ],
    [
      'limit' => $limit,
      'sort' => ['time' => -1],
      'skip' => $offset
    ]
  );
	$cursor = $mongo->executeQuery('tc.posts', $query);
	$posts = [];
	foreach ($cursor as $document) {
		array_push($posts, json_decode(MongoDB\BSON\toJSON(MongoDB\BSON\fromPHP($document))));
	}
	return $posts;
}

// Get posts
function get_all_posts() {
	$mongo = new MongoDB\Driver\Manager(Config::MONGODB);
	$query = new MongoDB\Driver\Query([], []);
	$cursor = $mongo->executeQuery('tc.posts', $query);
	$posts = [];
	foreach ($cursor as $document) {
		array_push($posts, json_decode(MongoDB\BSON\toJSON(MongoDB\BSON\fromPHP($document))));
	}
	return $posts;
}

// Get unanswered questions
function get_unanswered_questions($offset = NULL) {
	$mongo = new MongoDB\Driver\Manager(Config::MONGODB);
	$query = new MongoDB\Driver\Query(
    [ '$and' =>
        [
          [ 'audience' => NULL ],
          [ 'type' => 'question' ],
          [ 'comments' => [ '$size' => 0 ] ]
        ]
    ],
    [
      'sort' => ['time' => -1],
      'skip' => $offset
    ]
  );
	$cursor = $mongo->executeQuery('tc.posts', $query);
	$posts = [];
	foreach ($cursor as $document) {
		array_push($posts, json_decode(MongoDB\BSON\toJSON(MongoDB\BSON\fromPHP($document))));
	}
	return $posts;
}

// Get most popular posts
function get_most_popular_posts($dateStart, $dateEnd, $limit, $offset = NULL) {
	$mongo = new MongoDB\Driver\Manager(Config::MONGODB);
  $command = new MongoDB\Driver\Command(
    ['aggregate' => 'posts',
      'cursor' => [ "batchSize" => 0 ],
      'pipeline' => [
        ['$match' =>
          ['$and' =>
            [
              ['time' =>
                [
                  '$gte' => new MongoDB\BSON\UTCDateTime($dateStart*1000),
                  '$lte' => new MongoDB\BSON\UTCDateTime($dateEnd*1000)
                ]
              ],
              ['audience' => NULL]
            ]
          ]
        ],
        ['$unwind' => '$reactions'],
        ['$group' =>
          [
            '_id' => '$_id',
            'totalViews' => ['$sum' => '$totalViews'],
            'reactions' => ['$first' => '$reactions'],
            'text' => ['$first' => '$text'],
            'userId' => ['$first' => '$userId'],
            'isAnonymous' => ['$first' => '$isAnonymous'],
            'time' => ['$first' => '$time'],
            'photos' => ['$first' => '$photos'],
            'comments' => ['$first' => '$comments'],
            'categories' => ['$first' => '$categories'],
            'countComments' => [ '$sum' => ['$size' => '$comments'] ],
			'countReactions' => [ '$sum' => '$reactions' ],
			'highfive' => ['$first' => '$highfive']
          ]
        ],
		['$addFields' =>
			[
				'commentsScore' => [ '$multiply' => ['$countComments', 10] ],
				'helpfulsScore' => [ '$multiply' => ['$countReactions', 3] ],
				'viewsScore' => [ '$sum' => '$totalViews' ]
			]
		],
		['$addFields' =>
			[
				'totalScore' => [ '$add' => ['$commentsScore', '$helpfulsScore', '$viewsScore'] ]
			]
		],
        ['$sort' =>
          [
			'totalScore' => -1,
            'countComments' => -1,
            'countHelpful' => -1,
            'countSamehere' => -1,
            'totalViews' => -1
          ]
        ],
        ['$limit' => $limit]
      ]
    ]
  );
  $cursor = $mongo->executeCommand('tc', $command);
	$posts = [];
	foreach ($cursor->toArray() as $document) {
		array_push($posts, json_decode(MongoDB\BSON\toJSON(MongoDB\BSON\fromPHP($document))));
	}
	return $posts;
}

// Get posts by private group
function get_posts_by_exclusive_group($groupId, $limit, $offset = NULL) {
	$mongo = new MongoDB\Driver\Manager(Config::MONGODB);
  $query = new MongoDB\Driver\Query(
    ['audience' => $groupId],
    [
      'limit' => $limit,
      'sort' => ['time' => -1],
      'skip' => $offset
    ]
  );
	$cursor = $mongo->executeQuery('tc.posts', $query);
	$posts = [];
	foreach ($cursor as $document) {
		array_push($posts, json_decode(MongoDB\BSON\toJSON(MongoDB\BSON\fromPHP($document))));
	}
	return $posts;
}

// Get posts by group
function get_posts_by_group($groupId, $group, $limit, $offset = NULL) {
  $group = array_values($group);
	$mongo = new MongoDB\Driver\Manager(Config::MONGODB);
	//$query = new MongoDB\Driver\Query(['$or' => [['userId' => ['$in' => $group]], ['audience' => $groupId]]], ['limit' => $limit, 'sort' => ['time' => -1], 'skip' => $offset]);
  $query = new MongoDB\Driver\Query(['$or' => [['$and' => [['userId' => ['$in' => $group]], ['audience' => NULL]]], ['audience' => $groupId]]], ['limit' => $limit, 'sort' => ['time' => -1], 'skip' => $offset]);
	$cursor = $mongo->executeQuery('tc.posts', $query);
	$posts = [];
	foreach ($cursor as $document) {
		array_push($posts, json_decode(MongoDB\BSON\toJSON(MongoDB\BSON\fromPHP($document))));
	}
	return $posts;
}

// Get posts by user
function get_posts_by_user($limit, $userId, $offset = NULL) {
	$mongo = new MongoDB\Driver\Manager(Config::MONGODB);
	$query = new MongoDB\Driver\Query(['userId' => $userId], ['limit' => $limit, 'sort' => ['time' => -1], 'skip' => $offset]);
	$cursor = $mongo->executeQuery('tc.posts', $query);
	$posts = [];
	foreach ($cursor as $document) {
		array_push($posts, json_decode(MongoDB\BSON\toJSON(MongoDB\BSON\fromPHP($document))));
	}
	return $posts;
}

// Get posts and comments by user
function get_posts_and_comments_by_user($userId, $limit = NULL, $offset = NULL) {
	$mongo = new MongoDB\Driver\Manager(Config::MONGODB);
	$query = new MongoDB\Driver\Query(
    ['$or' =>
      [
        [ 'userId' => $userId ],
        [ 'comments.userId' => $userId ]
      ]
    ],
    [
      'limit' => $limit,
      'sort' => [ 'time' => -1 ],
      'skip' => $offset
    ]
  );
	$cursor = $mongo->executeQuery('tc.posts', $query);
	$posts = [];
	foreach ($cursor as $document) {
		array_push($posts, json_decode(MongoDB\BSON\toJSON(MongoDB\BSON\fromPHP($document))));
	}
	return $posts;
}

// Get posts by user filtered by own group membership
function get_filtered_posts_by_user($limit, $userId, $groups, $offset = NULL) {
	$mongo = new MongoDB\Driver\Manager(Config::MONGODB);
  $query = new MongoDB\Driver\Query(
    ['$or' =>
      [
        [ '$and' =>
            [
              [ 'userId' => $userId ],
              [ 'isAnonymous' => false ],
              [ 'audience' => NULL ]
            ]
        ],
        [ '$and' =>
            [
              [ 'userId' => $userId ],
              [ 'isAnonymous' => false ],
              [ 'audience' =>
                  [ '$in' => $groups ]
              ]
            ]
        ]
      ]
    ],
    [ 'limit' => $limit,
      'sort' =>
        [ 'time' => -1 ],
      'skip' => $offset
    ]
  );
	// $query = new MongoDB\Driver\Query(['$or' => [['$and' => [['userId' => $userId], ['audience' => NULL]]], ['$and' => [['userId' => $userId], ['audience' => ['$in' => $groups]]]]]], ['limit' => $limit, 'sort' => ['time' => -1], 'skip' => $offset]);
	$cursor = $mongo->executeQuery('tc.posts', $query);
	$posts = [];
	foreach ($cursor as $document) {
		array_push($posts, json_decode(MongoDB\BSON\toJSON(MongoDB\BSON\fromPHP($document))));
	}
	return $posts;
}

// Get posts by a list of users filtered by own group membership
function get_filtered_posts_by_users($limit, $users, $groups, $offset = NULL) {
	$mongo = new MongoDB\Driver\Manager(Config::MONGODB);
  $query = new MongoDB\Driver\Query(
    ['$or' =>
      [
        [ '$and' =>
            [
              [ 'userId' =>
                [ '$in' => $users ]
              ],
              [ 'isAnonymous' => false ],
              [ 'audience' => NULL ]
            ]
        ],
        [ '$and' =>
            [
              [ 'userId' =>
                [ '$in' => $users ]
              ],
              [ 'isAnonymous' => false ],
              [ 'audience' =>
                  [ '$in' => $groups ]
              ]
            ]
        ]
      ]
    ],
    [ 'limit' => $limit,
      'sort' =>
        [ 'time' => -1 ],
      'skip' => $offset
    ]
  );
	// $query = new MongoDB\Driver\Query(['$or' => [['$and' => [['userId' => $userId], ['audience' => NULL]]], ['$and' => [['userId' => $userId], ['audience' => ['$in' => $groups]]]]]], ['limit' => $limit, 'sort' => ['time' => -1], 'skip' => $offset]);
	$cursor = $mongo->executeQuery('tc.posts', $query);
	$posts = [];
	foreach ($cursor as $document) {
		array_push($posts, json_decode(MongoDB\BSON\toJSON(MongoDB\BSON\fromPHP($document))));
	}
	return $posts;
}

// Get individual post
function get_post($pid) {
	$mongo = new MongoDB\Driver\Manager(Config::MONGODB);
	$query = new MongoDB\Driver\Query(['_id' => new MongoDB\BSON\ObjectID($pid)], []);
	$cursor = $mongo->executeQuery('tc.posts', $query);
	$post = [];
	foreach ($cursor as $document) {
		array_push($post, json_decode(MongoDB\BSON\toJSON(MongoDB\BSON\fromPHP($document))));
	}
	return $post;
}

// Delete individual post
function delete_post($pid) {
	$mongo = new MongoDB\Driver\Manager(Config::MONGODB);
	$bulk = new MongoDB\Driver\BulkWrite;
	$bulk->delete(['_id' => new MongoDB\BSON\ObjectID($pid)], ['limit' => 1]);
	$result = $mongo->executeBulkWrite('tc.posts', $bulk);

	if ($result->getDeletedCount() >= 1) {
		SearchEngine::boot()->delete_doc(SearchEngine::POSTS_INDEX, $pid);
		return true;

	} else {

		return false;

	}
}

// Search posts by keyword filtered by own group membership and ordered by recency
function search_filtered_posts($keywords, $groups, $limit, $offset = NULL) {
	$mongo = new MongoDB\Driver\Manager(Config::MONGODB);
	$query = new MongoDB\Driver\Query(
    ['$and' =>
        [
          [
            '$text' => ['$search' => $keywords],
            // 'score' => ['$meta' => 'textScore'],
            '$or' =>
              [
                ['audience' => ['$in' => $groups]],
                ['audience' => NULL]
              ]
          ]
        ]
    ],
    [
      'projection' => ['score' => ['$meta' => 'textScore']],
      'limit' => $limit,
	  'sort' => ['time' => -1, 'score' => ['$meta' => 'textScore']],
      'skip' => $offset
    ]
  );

	$cursor = $mongo->executeQuery('tc.posts', $query);

	$posts = [];
	foreach ($cursor as $document) {
		array_push($posts, json_decode(MongoDB\BSON\toJSON(MongoDB\BSON\fromPHP($document))));
	}
	return $posts;
}

// Search posts by keyword filtered by own group membership and ordered by relevancy
function search_filtered_posts_ordered_relevancy($keywords, $groups, $limit, $offset = NULL) {
  // $replacement = '\"';
  // $keywords = str_replace('&#34;', $replacement, $keywords);
  // $keywords = explode(',', $keywords);
  // $keywords = '"' . implode(',', $keywords) . '"';
  // $keywords = json_encode($keywords);
  // print_r($keywords);

	$mongo = new MongoDB\Driver\Manager(Config::MONGODB);
	$query = new MongoDB\Driver\Query(
    ['$and' =>
        [
			[
				'$text' => ['$search' => $keywords],
				'$or' =>
				[
					['audience' => ['$in' => $groups]],
					['audience' => NULL]
				]
			]
        ]
    ],
    [
    	'projection' => ['score' => ['$meta' => 'textScore']],
		'limit' => $limit,
		'sort' => ['score' => ['$meta' => 'textScore'], 'time' => -1],
		'skip' => $offset
	]
  );
//   echo '<pre>';
// 	print_r($query);
	$cursor = $mongo->executeQuery('tc.posts', $query);
	// print_r($cursor);
	$posts = [];
	foreach ($cursor as $document) {
		array_push($posts, json_decode(MongoDB\BSON\toJSON(MongoDB\BSON\fromPHP($document))));
	}
	// print_r($posts);
	// exit();
	return $posts;
}

// Search posts by keyword
function search_posts($keywords, $limit, $offset = NULL) {
	$mongo = new MongoDB\Driver\Manager(Config::MONGODB);
	$query = new MongoDB\Driver\Query(['$text' => ['$search' => $keywords]], ['limit' => $limit, 'sort' => ['time' => -1], 'skip' => $offset]);
	$cursor = $mongo->executeQuery('tc.posts', $query);
	$posts = [];
	foreach ($cursor as $document) {
		array_push($posts, json_decode(MongoDB\BSON\toJSON(MongoDB\BSON\fromPHP($document))));
	}
	return $posts;
}

// Search members by keyword
function search_members($keywords, $limit, $offset = NULL) {
	$mongo = new MongoDB\Driver\Manager(Config::MONGODB);
	$query = new MongoDB\Driver\Query(
    ['$text' =>
            ['$search' => $keywords]
    ],
    [
      'projection' => ['score' => ['$meta' => 'textScore']],
      'limit' => $limit,
      'sort' => ['score' => ['$meta' => 'textScore']],
      'skip' => $offset
    ]
  );
	$cursor = $mongo->executeQuery('tc.users', $query);
	$members = [];
	foreach ($cursor as $document) {
		array_push($members, json_decode(MongoDB\BSON\toJSON(MongoDB\BSON\fromPHP($document))));
	}
	return $members;
}

// Get individual comment
function get_comment($pid) {
	$mongo = new MongoDB\Driver\Manager(Config::MONGODB);
	$query = new MongoDB\Driver\Query(['comments._id' => new MongoDB\BSON\ObjectID($pid)], []);
	$cursor = $mongo->executeQuery('tc.posts', $query);
	$post = [];
	foreach ($cursor as $document) {
		array_push($post, json_decode(MongoDB\BSON\toJSON(MongoDB\BSON\fromPHP($document))));
	}
	return $post;
}

// Get individual comment
function get_posts_by_id($pid) {
	$mongo = new MongoDB\Driver\Manager(Config::MONGODB);
	$query = new MongoDB\Driver\Query(['_id' => new MongoDB\BSON\ObjectID($pid)], []);
	$cursor = $mongo->executeQuery('tc.posts', $query);
	$post = [];
	foreach ($cursor as $document) {
		array_push($post, json_decode(MongoDB\BSON\toJSON(MongoDB\BSON\fromPHP($document))));
	}
	return $post;
}

// Delete individual comment
function delete_comment($pid, $cid) {
	$mongo = new MongoDB\Driver\Manager(Config::MONGODB);
	$bulk = new MongoDB\Driver\BulkWrite;
	$bulk->update(
		['_id' => new MongoDB\BSON\ObjectID($pid)],
		['$pull' =>
			[
        'comments' =>
					[
							'_id' => new MongoDB\BSON\ObjectID($cid)
					]
			]
		],
		[
			'multi' => false,
			'upsert' => false
		]
	);
	$result = $mongo->executeBulkWrite('tc.posts', $bulk);
	if ($result->getModifiedCount() >= 1) {
		SearchEngine::boot()->create_index(SearchEngine::POSTS_INDEX, get_post($pid));
		return true;
	} else {
		return false;
	}
}

// Get conversations by user
function get_conversations_by_user($limit = NULL, $offset = NULL) {
  $user_array[] = $_SESSION['uid'];
	$mongo = new MongoDB\Driver\Manager(Config::MONGODB);
	$query = new MongoDB\Driver\Query(
    ['participants' =>
      ['$in' => $user_array]
    ],
    [
      'limit' => $limit,
      'sort' => ['lastUpdated' => -1],
      'skip' => $offset
    ]
  );
	$cursor = $mongo->executeQuery('tc.conversations', $query);
	$conversations = [];
	foreach ($cursor as $document) {
		array_push($conversations, json_decode(MongoDB\BSON\toJSON(MongoDB\BSON\fromPHP($document))));
	}
	return $conversations;
}

// Get conversation by id
function get_conversation_by_id($id) {
  $user_array[] = $_SESSION['uid'];
	$mongo = new MongoDB\Driver\Manager(Config::MONGODB);
	$query = new MongoDB\Driver\Query(
    ['$and' =>
        [
          [
            '_id' => new MongoDB\BSON\ObjectID($id),
            'participants' => ['$in' => $user_array]
          ]
        ]
    ],
    []
  );
	$cursor = $mongo->executeQuery('tc.conversations', $query);
	$conversations = [];
	foreach ($cursor as $document) {
		array_push($conversations, json_decode(MongoDB\BSON\toJSON(MongoDB\BSON\fromPHP($document))));
	}
	return $conversations;
}

// Get conversation messages by id
function get_messages_by_conversation($id) {
  $user_array[] = $_SESSION['uid'];
  $mongo = new MongoDB\Driver\Manager(Config::MONGODB);
  $query = new MongoDB\Driver\Query(
    ['$and' =>
      [
        [
          '_id' => new MongoDB\BSON\ObjectID($id),
          'participants' => ['$in' => $user_array]
        ]
      ]
    ],
    ['projection' =>
      [
        'messages' => 1,
        '_id' => 0
      ]
    ]
  );
  $cursor = $mongo->executeQuery('tc.conversations', $query);
  $conversations = [];
  foreach ($cursor as $document) {
    array_push($conversations, json_decode(MongoDB\BSON\toJSON(MongoDB\BSON\fromPHP($document))));
  }
  return $conversations;
}

// Update conversation by id
function update_conversation_name($cid, $name) {
  $mongo = new MongoDB\Driver\Manager(Config::MONGODB);
  $bulk = new MongoDB\Driver\BulkWrite;
  $bulk->update(
    ['_id' => new MongoDB\BSON\ObjectID($cid)],
    ['$set' =>
      [
        'name' => $name
      ]
    ],
    [
      'multi' => false,
      'upsert' => false
    ]
  );
  $result = $mongo->executeBulkWrite('tc.conversations', $bulk);
	return $result;
}

// Add new members to a conversation
function add_conversation_members($cid, $participants) {
	$mongo = new MongoDB\Driver\Manager(Config::MONGODB);
	$bulk = new MongoDB\Driver\BulkWrite;
	$bulk->update(
		['_id' => new MongoDB\BSON\ObjectID($cid)],
		['$push' =>
			[
				'participants' => $participants
			],
		],
		[
			'multi' => false,
			'upsert' => false
		]
	);
	$result = $mongo->executeBulkWrite('tc.conversations', $bulk);

	if ($result->getModifiedCount() >= 1 OR $result->getMatchedCount() >= 1) {

		return true;

	} else {

		return false;

	}
}

// Remove selected members from a conversation
function remove_conversation_members($cid, $participants) {
	$mongo = new MongoDB\Driver\Manager(Config::MONGODB);
	$bulk = new MongoDB\Driver\BulkWrite;
	$bulk->update(
		['_id' => new MongoDB\BSON\ObjectID($cid)],
		['$pull' =>
			[
				'participants' => $participants
			],
		],
		[
			'multi' => false,
			'upsert' => false
		]
	);
	$result = $mongo->executeBulkWrite('tc.conversations', $bulk);

	if ($result->getModifiedCount() >= 1 OR $result->getMatchedCount() >= 1) {

		return true;

	} else {

		return false;

	}
}

// Leave conversation
function leave_conversation($cid) {
	$mongo = new MongoDB\Driver\Manager(Config::MONGODB);
	$bulk = new MongoDB\Driver\BulkWrite;
	$bulk->update(
		['_id' => new MongoDB\BSON\ObjectID($cid)],
		['$pull' =>
			[
				'participants' => $_SESSION['uid']
			],
		],
		[
			'multi' => false,
			'upsert' => false
		]
	);
	$result = $mongo->executeBulkWrite('tc.conversations', $bulk);

	if ($result->getModifiedCount() >= 1 OR $result->getMatchedCount() >= 1) {

		return true;

	} else {

		return false;

	}
}

// Delete conversation
function delete_conversation($cid) {
	$mongo = new MongoDB\Driver\Manager(Config::MONGODB);
	$bulk = new MongoDB\Driver\BulkWrite;
	$bulk->delete(
    ['_id' => new MongoDB\BSON\ObjectID($cid)],
    ['limit' => 1]
  );
	$result = $mongo->executeBulkWrite('tc.conversations', $bulk);

	if ($result->getDeletedCount() >= 1) {

		return true;

	} else {

		return false;

	}
}

// Get all notifications
function get_all_notifications() {
	$mongo = new MongoDB\Driver\Manager(Config::MONGODB);
  $query = new MongoDB\Driver\Query(
    [],
    [
      'limit' => $limit,
      'sort' => ['time' => -1],
      'skip' => $offset
    ]
  );
	$cursor = $mongo->executeQuery('tc.notifications', $query);
	$notifications = [];
	foreach ($cursor as $document) {
		array_push($notifications, json_decode(MongoDB\BSON\toJSON(MongoDB\BSON\fromPHP($document))));
	}
	return $notifications;
}

// Get notifications
function get_notifications($limit, $offset = NULL) {
  $user_array[] = $_SESSION['uid'];
	$mongo = new MongoDB\Driver\Manager(Config::MONGODB);
	//$query = new MongoDB\Driver\Query(['notificationList' => ['$in' => $user_array]], ['limit' => $limit, 'sort' => ['time' => -1], 'skip' => $offset]);
  $query = new MongoDB\Driver\Query(['$and' => [['notificationList' => ['$in' => $user_array], 'responderId' => [ '$ne' => $_SESSION['uid']]]]], ['limit' => $limit, 'sort' => ['time' => -1], 'skip' => $offset]);
	$cursor = $mongo->executeQuery('tc.notifications', $query);
	$notifications = [];
	foreach ($cursor as $document) {
		array_push($notifications, json_decode(MongoDB\BSON\toJSON(MongoDB\BSON\fromPHP($document))));
	}
	return $notifications;
}

// Get notifications for user
function get_notifications_by_user($uid, $limit, $offset = NULL) {
  $user_array[] = $uid;
	$mongo = new MongoDB\Driver\Manager(Config::MONGODB);
	//$query = new MongoDB\Driver\Query(['notificationList' => ['$in' => $user_array]], ['limit' => $limit, 'sort' => ['time' => -1], 'skip' => $offset]);
  $query = new MongoDB\Driver\Query(['$and' => [['notificationList' => ['$in' => $user_array], 'responderId' => [ '$ne' => $_SESSION['uid']]]]], ['limit' => $limit, 'sort' => ['time' => -1], 'skip' => $offset]);
	$cursor = $mongo->executeQuery('tc.notifications', $query);
	$notifications = [];
	foreach ($cursor as $document) {
		array_push($notifications, json_decode(MongoDB\BSON\toJSON(MongoDB\BSON\fromPHP($document))));
	}
	return $notifications;
}

// Get notifications count
function get_notifications_count() {
  $user_array[] = $_SESSION['uid'];
  $lastDate = new MongoDB\BSON\UTCDateTime($_SESSION['notificationTimestamp']);
	$mongo = new MongoDB\Driver\Manager(Config::MONGODB);
	//$query = new MongoDB\Driver\Query(['$and' => [['notificationList' => ['$in' => $user_array], 'initialId' => [ '$ne' => $_SESSION['uid']], 'responderId' => [ '$ne' => $_SESSION['uid']]]]], ['limit' => $limit, 'sort' => ['time' => -1], 'skip' => $offset]);
  $query = new MongoDB\Driver\Query(['$and' => [['notificationList' => ['$in' => $user_array], 'responderId' => ['$ne' => $_SESSION['uid']], 'time' => ['$gt' => $lastDate]]]], []);
	$cursor = $mongo->executeQuery('tc.notifications', $query);
	$notifications = [];
	foreach ($cursor as $document) {
		array_push($notifications, json_decode(MongoDB\BSON\toJSON(MongoDB\BSON\fromPHP($document))));
	}
  $notification_count = count($notifications);
  if ($notification_count > 0) {
   return $notification_count;
  }
}

// Get unread conversations by user
function get_conversations_count() {
  $conversationUnread = 0;
  $user_conversations = json_decode(json_encode(get_conversations_by_user($_SESSION['uid'])), true);
  foreach ($user_conversations as $user_conversation) {
    if ($_SESSION['conversations'][$user_conversation['_id']['$oid']] > $user_conversation['lastUpdated']['$date']) {
    } else {
        $conversationUnread++;
    }
  }
  if ($conversationUnread > 0) {
   return $conversationUnread;
  }
}

// Process post reaction
function react_post($pid, $react_array, $react_type) {
	$mongo = new MongoDB\Driver\Manager(Config::MONGODB);
	$bulk = new MongoDB\Driver\BulkWrite;
	$reactions = [];
	if ($react_type == 'highfive') {
		$reactions = [
			'highfive' => $react_array,
			'thumbsup' => []
		];
		$bulk->update(
			['_id' => new MongoDB\BSON\ObjectID($pid)],
			['$set' =>
				[
					'reactions'	=> $reactions
				]
			],
			[
				'multi' => false,
				'upsert' => false
			]
		);
	} else {
		$reactions = [
			'highfive' => [],
			'thumbsup' => $react_array
		];
		$bulk->update(
			['_id' => new MongoDB\BSON\ObjectID($pid)],
			['$set' =>
				[
					'reactions'	=> $reactions
				]
			],
			[
				'multi' => false,
				'upsert' => false
			]
		);
	}
	$result = $mongo->executeBulkWrite('tc.posts', $bulk);

	if ($result->getModifiedCount() >= 1) {
		SearchEngine::boot()->create_index(SearchEngine::POSTS_INDEX, get_post($pid));
		return count($react_array);

	} else {

		return false;

	}
}

// Process post following
function follow_post($pid, $react_array) {
	$mongo = new MongoDB\Driver\Manager(Config::MONGODB);
	$bulk = new MongoDB\Driver\BulkWrite;
	$bulk->update(
		['_id' => new MongoDB\BSON\ObjectID($pid)],
		['$set' =>
			[
				'following'	=> $react_array
			]
		],
		[
			'multi' => false,
			'upsert' => false
		]
	);
	$result = $mongo->executeBulkWrite('tc.posts', $bulk);

	if ($result->getModifiedCount() >= 1) {
		SearchEngine::boot()->create_index(SearchEngine::POSTS_INDEX, get_post($pid));
		return true;

	} else {

		return false;

	}
}

// Process post reaction
function react_comment($cid, $react_array, $pid = null) {
	$mongo = new MongoDB\Driver\Manager(Config::MONGODB);
	$bulk = new MongoDB\Driver\BulkWrite;
	$bulk->update(
		['comments._id' => new MongoDB\BSON\ObjectID($cid)],
		['$set' =>
			[
				'comments.$.reactions'	=>
				[
					'highfive' => [],
					'thumbsup' => $react_array
				]
			]
		],
		[
			'multi' => false,
			'upsert' => false
		]
	);
	$result = $mongo->executeBulkWrite('tc.posts', $bulk);

	if ($result->getModifiedCount() >= 1) {
		if ($pid) {
			SearchEngine::boot()->create_index(SearchEngine::POSTS_INDEX, get_post($pid));
		}
		return count($react_array);

	} else {

		return false;

	}
}

// Get authors
function get_authors($authors, $limit = NULL, $sort = NULL, $sort_order = NULL, $offset = NULL) {
  if ($sort == NULL) { $sort = 'firstName'; }
  if ($sort_order == NULL) { $sort_order = 1; }
	$mongo = new MongoDB\Driver\Manager(Config::MONGODB);
	// $query = new MongoDB\Driver\Query(['_id' => ['$in' => $authors]], ['projection' => ['_id' => 1, 'firstName' => 1, 'lastName' => 1, 'avatar' => 1, 'bio' => 1, 'avatar' => 1, 'lastActive' => 1, 'time' => 1]]);
  $query = new MongoDB\Driver\Query(['_id' => ['$in' => $authors]], ['limit' => $limit, 'sort' => [$sort => $sort_order], 'skip' => $offset]);
	$cursor = $mongo->executeQuery('tc.users', $query);
  //$post_author = '';
	$post_author = Array();
	foreach ($cursor as $document) {
		$mongo_record = json_decode(MongoDB\BSON\toJSON(MongoDB\BSON\fromPHP($document)));
		$mongo_array = json_decode(json_encode($mongo_record), true);
		$mongo_id = $mongo_array['_id']['$oid'];
		$post_author[$mongo_id] = $mongo_record;

	}
  return $post_author;
}

// Get members by email array
function get_members_by_email($emails) {
	$mongo = new MongoDB\Driver\Manager(Config::MONGODB);
  $query = new MongoDB\Driver\Query(
    ['email' =>
      ['$in' => $emails]
    ],
    []
  );
	$cursor = $mongo->executeQuery('tc.users', $query);
  $members = '';
	foreach ($cursor as $document) {
		$mongo_record = json_decode(MongoDB\BSON\toJSON(MongoDB\BSON\fromPHP($document)));
		$mongo_array = json_decode(json_encode($mongo_record), true);
		$mongo_email = $mongo_array['email'];
		$members[$mongo_email] = $mongo_record;
	}
  return $members;
}

// Get members by id
function get_members_by_id($ids) {
	$mongo = new MongoDB\Driver\Manager(Config::MONGODB);
  $query = new MongoDB\Driver\Query(
    ['_id' =>
      ['$in' => $ids]
    ],
    []
  );
	$cursor = $mongo->executeQuery('tc.users', $query);
  $members = [];
	foreach ($cursor as $document) {
		array_push($members, json_decode(MongoDB\BSON\toJSON(MongoDB\BSON\fromPHP($document))));
	}
  return $members;
}

// Get users by id (array of mongoids)
function get_users_by_id($userIds) {
  if ($userIds == NULL) {
    $userIds = [];
  }
	$mongo = new MongoDB\Driver\Manager(Config::MONGODB);
	$query = new MongoDB\Driver\Query(
    ['_id' =>
      [ '$in' => $userIds ]
    ],
    []
  );
	$cursor = $mongo->executeQuery('tc.users', $query);
  $user = [];
	foreach ($cursor as $document) {
		array_push($user, json_decode(MongoDB\BSON\toJSON(MongoDB\BSON\fromPHP($document))));
	}
  return $user;
}

// Get author
function get_author($userId) {
	$mongo = new MongoDB\Driver\Manager(Config::MONGODB);
	$query = new MongoDB\Driver\Query(['_id' => new MongoDB\BSON\ObjectID($userId)], ['limit' => 1, 'projection' => ['_id' => 1, 'firstName' => 1, 'lastName' => 1, 'avatar' => 1]]);
	$cursor = $mongo->executeQuery('tc.users', $query);
  $author = [];
	foreach ($cursor as $document) {
		array_push($author, json_decode(MongoDB\BSON\toJSON(MongoDB\BSON\fromPHP($document))));
	}
  return $author;
}

// Get user
function get_user($userId) {
	$mongo = new MongoDB\Driver\Manager(Config::MONGODB);
	$query = new MongoDB\Driver\Query(['_id' => new MongoDB\BSON\ObjectID($userId)], ['limit' => 1, 'projection' => []]);
	$cursor = $mongo->executeQuery('tc.users', $query);
  $user = [];
	foreach ($cursor as $document) {
		array_push($user, json_decode(MongoDB\BSON\toJSON(MongoDB\BSON\fromPHP($document))));
	}
  return $user;
}

// Find user
function find_user($email) {
	$mongo = new MongoDB\Driver\Manager(Config::MONGODB);
	$query = new MongoDB\Driver\Query(	
	['$and' =>
		[
			['email' => $email],
			['userStatus' => 'verified']
		]
	],
	['limit' => 1, 'projection' => []]);
	$cursor = $mongo->executeQuery('tc.users', $query);
    $user = [];
	foreach ($cursor as $document) {
		array_push($user, json_decode(MongoDB\BSON\toJSON(MongoDB\BSON\fromPHP($document))));
	}
  return $user;
}

// Find all user
function find_existing_user($email) {
	$mongo = new MongoDB\Driver\Manager(Config::MONGODB);
	$query = new MongoDB\Driver\Query(		
	[
		'email' => $email
	],
	['limit' => 1, 'projection' => []]);
	$cursor = $mongo->executeQuery('tc.users', $query);
    $user = [];
	foreach ($cursor as $document) {
		array_push($user, json_decode(MongoDB\BSON\toJSON(MongoDB\BSON\fromPHP($document))));
	}
  return $user;
}

// Find all unverified user
function find_unverified_user($email) {
	$mongo = new MongoDB\Driver\Manager(Config::MONGODB);
	$query = new MongoDB\Driver\Query(		
	['$and' =>
		[
			['email' => $email],
			['userStatus' => 'unverified']
		]
	],
	['limit' => 1, 'projection' => []]);
	$cursor = $mongo->executeQuery('tc.users', $query);
    $user = [];
	foreach ($cursor as $document) {
		array_push($user, json_decode(MongoDB\BSON\toJSON(MongoDB\BSON\fromPHP($document))));
	}
  return $user;
}


// Read trust for member
function read_user_trust($uid) {
  $mongo = new MongoDB\Driver\Manager(Config::MONGODB);
	$query = new MongoDB\Driver\Query(
    [
      '_id' => new MongoDB\BSON\ObjectID($uid)
    ],
    [
      'limit' => 1,
      'projection' => ['trusted' => 1, 'access' => 1]
    ]
  );
	$cursor = $mongo->executeQuery('tc.users', $query);
  $user = [];
	foreach ($cursor as $document) {
		array_push($user, json_decode(MongoDB\BSON\toJSON(MongoDB\BSON\fromPHP($document))));
	}
	
  return $user;
}

// Write trust for member
function write_user_trust($uid, $level) {
  $level = (boolean)$level;
	$mongo = new MongoDB\Driver\Manager(Config::MONGODB);
	$bulk = new MongoDB\Driver\BulkWrite;
	$bulk->update(
		['_id' => new MongoDB\BSON\ObjectID($uid)],
		['$set' =>
			[
				'trusted' => $level
			]
		],
		[
			'multi' => false,
			'upsert' => false
		]
	);
	$result = $mongo->executeBulkWrite('tc.users', $bulk);
	if ($result->getModifiedCount() >= 1) {
		return true;
	} else {
		return false;
	}
}

// Write payment status for member
function write_access_level($uid, $level) {
  // $level = (boolean)$level;
	$mongo = new MongoDB\Driver\Manager(Config::MONGODB);
	$bulk = new MongoDB\Driver\BulkWrite;
	$bulk->update(
		['_id' => new MongoDB\BSON\ObjectID($uid)],
		['$set' =>
			[
				'access' => $level
			]
		],
		[
			'multi' => false,
			'upsert' => false
		]
	);
	$result = $mongo->executeBulkWrite('tc.users', $bulk);
	if ($result->getModifiedCount() >= 1) {
		return true;
	} else {
		return false;
	}
}

// Write payment status for member
function write_access_payment($uid, $level) {
  // $level = (boolean)$level;
	$mongo = new MongoDB\Driver\Manager(Config::MONGODB);
	$bulk = new MongoDB\Driver\BulkWrite;
	$bulk->update(
		['_id' => new MongoDB\BSON\ObjectID($uid)],
		['$addToSet' =>
			[
				'payment' => $level
			]
		],
		[
			'multi' => false,
			'upsert' => false
		]
	);
	$result = $mongo->executeBulkWrite('tc.users', $bulk);
	if ($result->getModifiedCount() >= 1) {
		return true;
	} else {
		return false;
	}
}

// Find current mobile token
function find_mobile_token($userId, $deviceId) {
	$mongo = new MongoDB\Driver\Manager(Config::MONGODB);
	$query = new MongoDB\Driver\Query(
    ['$and' =>
        [
          ['userId' => $userId],
          ['deviceId' => $deviceId]
        ]
    ],
    [
      'limit' => 1,
      'projection' => [],
      'sort' => ['dateCreated' => -1],
    ]
  );
	$cursor = $mongo->executeQuery('tc.mobile', $query);
  $token = [];
	foreach ($cursor as $document) {
		array_push($token, json_decode(MongoDB\BSON\toJSON(MongoDB\BSON\fromPHP($document))));
	}
  return $token;
}

// Find current mobile token
function check_mobile_token($userId, $tokenId) {
	$mongo = new MongoDB\Driver\Manager(Config::MONGODB);
	$query = new MongoDB\Driver\Query(
    ['$and' =>
        [
          ['userId' => $userId],
          ['tokenId' => $tokenId]
        ]
    ],
    [
      'limit' => 1,
      'projection' => [],
      'sort' => ['dateCreated' => -1],
    ]
  );
	$cursor = $mongo->executeQuery('tc.mobile', $query);
  $token = [];
	foreach ($cursor as $document) {
		array_push($token, json_decode(MongoDB\BSON\toJSON(MongoDB\BSON\fromPHP($document))));
	}
  return $token;
}

// Create new mobile token
function new_mobile_token($userId, $deviceId, $metaData = NULL) {
  $objectId = new MongoDB\BSON\ObjectID;
  $tokenId = bin2hex(openssl_random_pseudo_bytes(16));
  $currentTime = new MongoDB\BSON\UTCDateTime(time()*1000);
	$mongo = new MongoDB\Driver\Manager(Config::MONGODB);
	$bulk = new MongoDB\Driver\BulkWrite;
  $bulk->insert(
		[
      '_id' => $objectId,
      'userId' => $userId,
      'tokenId' => $tokenId,
      'deviceId' => $deviceId,
      'metaData' => $metaData,
      'dateCreated' => $currentTime
		]
	);

	$result = $mongo->executeBulkWrite('tc.mobile', $bulk);

	if ($result->getInsertedCount() >= 1) {

		return (string)$tokenId;

	} else {

		return false;

	}
}

// Update mobile token
function update_mobile_token($tokenId, $metaData = NULL) {
  $newDate = new MongoDB\BSON\UTCDateTime(time()*1000);
  $mongo = new MongoDB\Driver\Manager(Config::MONGODB);
	$bulk = new MongoDB\Driver\BulkWrite;
	$bulk->update(
		['_id' => new MongoDB\BSON\ObjectID($tokenId)],
		['$set' =>
			[
				'lastLogin' => $newDate,
        'metaData' => $metaData
			]
		],
		[
			'multi' => false,
			'upsert' => false
		]
	);
	$result = $mongo->executeBulkWrite('tc.mobile', $bulk);
	if ($result->getModifiedCount() >= 1) {
		return true;
	} else {
		return false;
	}
}

// Delete mobile token
function delete_mobile_token($tid) {
	$mongo = new MongoDB\Driver\Manager(Config::MONGODB);
	$bulk = new MongoDB\Driver\BulkWrite;
	$bulk->delete(['_id' => new MongoDB\BSON\ObjectID($tid)], ['limit' => 1]);
	$result = $mongo->executeBulkWrite('tc.mobile', $bulk);

	if ($result->getDeletedCount() >= 1) {

		return true;

	} else {

		return false;

	}
}

// Post comment/answer
function new_comment($pid, $text, $images = NULL) {
	if (!$images) {
		$images = [];
	}
  $objectId = new MongoDB\BSON\ObjectID;
	$mongo = new MongoDB\Driver\Manager(Config::MONGODB);
	$bulk = new MongoDB\Driver\BulkWrite;
	$savedTime = time()*1000;
	$newComment = [
							'text' => $text,
							'userId' => $_SESSION['uid'],
							'_id' => $objectId,
							'reactions'	=>
							[
								'highfive' => [],
								'thumbsup' => []
							],
		'time' => new MongoDB\BSON\UTCDateTime($savedTime),
							'photos' => $images
	];
	$bulk->update(
		['_id' => new MongoDB\BSON\ObjectID($pid)],
		['$push' =>
			[
				'comments' => $newComment
			]
		],
		[
			'multi' => false,
			'upsert' => false
		]
	);
	$result = $mongo->executeBulkWrite('tc.posts', $bulk);
	if ($result->getModifiedCount() >= 1 OR $result->getMatchedCount() >= 1) {
		$newComment['_id'] = [
			'$oid' => (string) $objectId
		];
		$newComment['time'] = [
			'$date' => $savedTime
		];
		SearchEngine::boot()->create_index(SearchEngine::POSTS_INDEX, get_post($pid));
		return (string)$objectId;
	} else {
		return false;
	}
}

// Create new conversation
function new_conversation($participants, $text, $images = NULL) {
	if (!$images) {
		$images = [];
	}
  $objectId = new MongoDB\BSON\ObjectID;
  $messageId = new MongoDB\BSON\ObjectID;
  $currentTime = new MongoDB\BSON\UTCDateTime(time()*1000);
	$mongo = new MongoDB\Driver\Manager(Config::MONGODB);
	$bulk = new MongoDB\Driver\BulkWrite;
  $bulk->insert(
		[
      '_id' => $objectId,
      'owner' => $_SESSION['uid'],
      'participants' => $participants,
      'messages' =>
        [
          [
            'text' => $text,
            'userId' => $_SESSION['uid'],
            '_id' => $messageId,
            'time' => $currentTime,
            'photos' => $images
          ]
        ],
      'lastUpdated' => $currentTime
		]
	);

	$result = $mongo->executeBulkWrite('tc.conversations', $bulk);

	if ($result->getInsertedCount() >= 1) {

		return (string)$objectId;

	} else {

		return false;

	}
}

// Post new message to conversation
function new_message($cid, $text, $images = NULL) {
	if (!$images) {
		$images = [];
	}
  $objectId = new MongoDB\BSON\ObjectID;
  $currentTime = new MongoDB\BSON\UTCDateTime(time()*1000);
	$mongo = new MongoDB\Driver\Manager(Config::MONGODB);
	$bulk = new MongoDB\Driver\BulkWrite;
	$bulk->update(
		['_id' => new MongoDB\BSON\ObjectID($cid)],
		['$push' =>
			[
				'messages' =>
					[
							'text' => $text,
							'userId' => $_SESSION['uid'],
							'_id' => $objectId,
							'time' => $currentTime,
							'photos' => $images
					]
			],
		],
		[
			'multi' => false,
			'upsert' => false
		]
	);
	$result = $mongo->executeBulkWrite('tc.conversations', $bulk);

	if ($result->getModifiedCount() >= 1 OR $result->getMatchedCount() >= 1) {

    $mongo = new MongoDB\Driver\Manager(Config::MONGODB);
  	$bulk = new MongoDB\Driver\BulkWrite;
  	$bulk->update(
  		['_id' => new MongoDB\BSON\ObjectID($cid)],
      ['$set' =>
  			[
  				'lastUpdated' => $currentTime
  			]
  		],
  		[
  			'multi' => false,
  			'upsert' => false
  		]
  	);
  	$result = $mongo->executeBulkWrite('tc.conversations', $bulk);

		return (string)$objectId;

	} else {

		return false;

	}
}

// Update topics followed by user
function update_topics_followed($uid, $topics_array) {
  // Make sure user_array is an actual array
  if ($topics_array == NULL) {
    $topics_array = [];
  }
  if (is_array($topics_array)) { } else {
    $topics_array = array($topics_array);
  }
	$mongo = new MongoDB\Driver\Manager(Config::MONGODB);
	$bulk = new MongoDB\Driver\BulkWrite;
	$bulk->update(
		['_id' => new MongoDB\BSON\ObjectID($uid)],
		['$set' =>
			[
				'topicsFollowed'	=> $topics_array
			]
		],
		[
			'multi' => false,
			'upsert' => false
		]
	);
	$result = $mongo->executeBulkWrite('tc.users', $bulk);

	if ($result->getModifiedCount() >= 1) {

		return true;

	} else {

		return false;

	}
}

// Update user profile image
function update_userImage($uid, $image = NULL) {
	if (!$image) {
		$image = "";
	}
	$mongo = new MongoDB\Driver\Manager(Config::MONGODB);
	$bulk = new MongoDB\Driver\BulkWrite;
	$bulk->update(
		['_id' => new MongoDB\BSON\ObjectID($uid)],
		['$set' =>
			[
				'avatar' => $image
			]
		],
		[
			'multi' => false,
			'upsert' => false
		]
	);
	$result = $mongo->executeBulkWrite('tc.users', $bulk);
	if ($result->getModifiedCount() >= 1) {
		SearchEngine::boot()->create_index(SearchEngine::USERS_INDEX, get_user($uid));
		return true;
	} else {
		return false;
	}
}

// Update user profile image
function update_user_education ($pid, $name, $year) {
	$mongo = new MongoDB\Driver\Manager(Config::MONGODB);
	$bulk = new MongoDB\Driver\BulkWrite;
	$bulk->update(
		['educations._id' => new MongoDB\BSON\ObjectID($pid)],
		['$set' =>
      [
        'educations.$.institude' => $name,
        'educations.$.yearCompleted' => $year
      ]
		],
		[
			'multi' => false,
			'upsert' => false
		]
	);
	$result = $mongo->executeBulkWrite('tc.users', $bulk);
	if ($result->getModifiedCount() >= 1 OR $result->getMatchedCount() >= 1) {
		return true;
	} else {
		return false;
	}
}

// Update user profile bio text
function update_user_bio($bioText) {
	$mongo = new MongoDB\Driver\Manager(Config::MONGODB);
	$bulk = new MongoDB\Driver\BulkWrite;
	$bulk->update(
		['_id' => new MongoDB\BSON\ObjectID($_SESSION['uid'])],
		['$set' =>
      [
        'bio' => $bioText
      ]
		],
		[
			'multi' => false,
			'upsert' => false
		]
	);
	$result = $mongo->executeBulkWrite('tc.users', $bulk);
	if ($result->getModifiedCount() >= 1 OR $result->getMatchedCount() >= 1) {
		return true;
	} else {
		return false;
	}
}

// Update user profile information
function update_user_information($firstName, $lastName, $emailAddress, $newPassword = NULL) {
	$mongo = new MongoDB\Driver\Manager(Config::MONGODB);
	$bulk = new MongoDB\Driver\BulkWrite;
	$userBasicInfo = [
		'firstName' => $firstName,
		'lastName' => $lastName,
		'email' => $emailAddress
	];
  if ($newPassword) {
	$userBasicInfo['password'] = password_hash($newPassword, PASSWORD_DEFAULT);
  	$bulk->update(
  		['_id' => new MongoDB\BSON\ObjectID($_SESSION['uid'])],
  		['$set' => $userBasicInfo ],
  		[
  			'multi' => false,
  			'upsert' => false
  		]
  	);
  	$result = $mongo->executeBulkWrite('tc.users', $bulk);
  	if ($result->getModifiedCount() >= 1 OR $result->getMatchedCount() >= 1) {
  		return true;
  	} else {
  		return false;
  	}
  } else {
    $bulk->update(
  		['_id' => new MongoDB\BSON\ObjectID($_SESSION['uid'])],
  		['$set' => $userBasicInfo],
  		[
  			'multi' => false,
  			'upsert' => false
  		]
  	);
  	$result = $mongo->executeBulkWrite('tc.users', $bulk);
  	if ($result->getModifiedCount() >= 1 OR $result->getMatchedCount() >= 1) {
		SearchEngine::boot()->create_index(SearchEngine::USERS_INDEX, get_user($_SESSION['uid']));
  		return true;
  	} else {
  		return false;
  	}
  }
}

// Add education to user
function add_user_education($uid, $teachLicenseLocation, $teachLicenseComplete) {
	$mongo = new MongoDB\Driver\Manager(Config::MONGODB);
	$bulk = new MongoDB\Driver\BulkWrite;
	$bulk->update(
		['_id' => new MongoDB\BSON\ObjectID($uid)],
		['$push' =>
			[
				'educations' =>
					[
							'yearCompleted' => $teachLicenseComplete,
							'institude' => $teachLicenseLocation,
							'_id' => new MongoDB\BSON\ObjectID
					]
			]
		],
		[
			'multi' => false,
			'upsert' => false
		]
	);
	$result = $mongo->executeBulkWrite('tc.users', $bulk);
	if ($result->getModifiedCount() >= 1) {
		return true;
	} else {
		return false;
	}
}

// Delete education from user
function delete_user_education($uid, $pid) {
	$mongo = new MongoDB\Driver\Manager(Config::MONGODB);
	$bulk = new MongoDB\Driver\BulkWrite;
	$bulk->update(
		['_id' => new MongoDB\BSON\ObjectID($uid)],
		['$pull' =>
			[
        'educations' =>
					[
							'_id' => new MongoDB\BSON\ObjectID($pid)
					]
			]
		],
		[
			'multi' => false,
			'upsert' => false
		]
	);
	$result = $mongo->executeBulkWrite('tc.users', $bulk);
	if ($result->getModifiedCount() >= 1) {
		return true;
	} else {
		return false;
	}
}


// Add experience to user
function add_user_experience($uid, $teachLocationName, $teachLocationCity, $teachLocationState, $teachGrades, $teachSubjects, $teachStart, $teachEnd) {
	$mongo = new MongoDB\Driver\Manager(Config::MONGODB);
	$bulk = new MongoDB\Driver\BulkWrite;
	$bulk->update(
    ['_id' => new MongoDB\BSON\ObjectID($uid)],
		['$push' =>
			[
        'experience' =>
					[
              '_id' => new MongoDB\BSON\ObjectID($pid),
              'datesWorked' =>
              [
                'selectedStart' => $teachStart,
                'selectedEnd' => $teachEnd
              ],
              'subjects' => $teachSubjects = explode(',', $teachSubjects),
              'grade' => $teachGrades = explode(',', $teachGrades),
              'school' =>
              [
                'name' => $teachLocationName,
                'city' => $teachLocationCity,
                'state' => $teachLocationState
              ]
          ]
			]
		],
		[
			'multi' => false,
			'upsert' => false
		]
	);
	$result = $mongo->executeBulkWrite('tc.users', $bulk);
	if ($result->getModifiedCount() >= 1) {
		return true;
	} else {
		return false;
	}
}

// Delete experience from user
function delete_user_experience($uid, $pid) {
	$mongo = new MongoDB\Driver\Manager(Config::MONGODB);
	$bulk = new MongoDB\Driver\BulkWrite;
	$bulk->update(
		['_id' => new MongoDB\BSON\ObjectID($uid)],
		['$pull' =>
			[
        'experience' =>
					[
							'_id' => new MongoDB\BSON\ObjectID($pid)
					]
			]
		],
		[
			'multi' => false,
			'upsert' => false
		]
	);
	$result = $mongo->executeBulkWrite('tc.users', $bulk);
	if ($result->getModifiedCount() >= 1) {
		return true;
	} else {
		return false;
	}
}

// Create new post (app)
function new_post_app($text, $uid, $type = NULL, $anon = NULL, $images = NULL, $audience = NULL) {
	if ($anon == 'anonymous') {
		$isAnonymous = true;
	} else {
		$isAnonymous = false;
	}
	if (!$images) {
		$images = [];
	}
  if ($audience == '') {
		$audience = NULL;
	}
	$mongo = new MongoDB\Driver\Manager(Config::MONGODB);
	$bulk = new MongoDB\Driver\BulkWrite;
	$objectId = new MongoDB\BSON\ObjectID;
	$bulk->insert(
		[
			'text' => $text,
			'type' => $type,
			'userId' => $uid,
			'_id' => $objectId,
			'time' => new MongoDB\BSON\UTCDateTime(time()*1000),
			'comments' => [],
			'reactions'	=>
			[
				'highfive' => [],
				'thumbsup' => []
			],
			'isAnonymous'	=> $isAnonymous,
			'photos'	=> $images,
      'audience' => $audience,
			'__v' => 0
		]
	);
	$result = $mongo->executeBulkWrite('tc.posts', $bulk);
	if ($result->getInsertedCount() >= 1) {

		return (string)$objectId;

	} else {

		return false;

	}
}

// Post comment/answer (app)
function new_comment_app($pid, $uid, $text, $images = NULL) {
	if (!$images) {
		$images = [];
	}
  $objectId = new MongoDB\BSON\ObjectID;
	$mongo = new MongoDB\Driver\Manager(Config::MONGODB);
	$bulk = new MongoDB\Driver\BulkWrite;
	$bulk->update(
		['_id' => new MongoDB\BSON\ObjectID($pid)],
		['$push' =>
			[
				'comments' =>
					[
							'text' => $text,
							'userId' =>  $uid,
							'_id' => $objectId,
							'reactions'	=>
							[
								'highfive' => [],
								'thumbsup' => []
							],
							'time' => new MongoDB\BSON\UTCDateTime(time()*1000),
							'photos' => $images
					]
			]
		],
		[
			'multi' => false,
			'upsert' => false
		]
	);
	$result = $mongo->executeBulkWrite('tc.posts', $bulk);
	if ($result->getModifiedCount() >= 1 OR $result->getMatchedCount() >= 1) {
		return (string)$objectId;
	} else {
		return false;
	}
}

// Create new post
function new_post($text, $type = NULL, $anon = NULL, $images = NULL, $audience = NULL) {
	if ($anon == 'anonymous') {
		$isAnonymous = true;
	} else {
		$isAnonymous = false;
	}
	if ($type) {
		$postType = 'question';
	} else {
		$postType = 'text';
	}
	if (!$images) {
		$images = [];
	}
  if ($audience == '') {
		$audience = NULL;
	}
  $notify_users[] = $_SESSION['uid'];
	$mongo = new MongoDB\Driver\Manager(Config::MONGODB);
	$bulk = new MongoDB\Driver\BulkWrite;
	$objectId = new MongoDB\BSON\ObjectID;
	$savedTime = time()*1000;
	$newPost = [
			'text' => $text,
			'type' => $postType,
			'userId' => $_SESSION['uid'],
			'_id' => $objectId,
		'time' => new MongoDB\BSON\UTCDateTime($savedTime),
			'comments' => [],
			'reactions'	=>
			[
				'highfive' => [],
				'thumbsup' => []
			],
			'isAnonymous'	=> $isAnonymous,
			'photos'	=> $images,
      'audience' => $audience,
      'following' => $notify_users,
			'__v' => 0
	];
	$bulk->insert($newPost);
	$result = $mongo->executeBulkWrite('tc.posts', $bulk);
	if ($result->getInsertedCount() >= 1) {
		SearchEngine::boot()->create_index(SearchEngine::POSTS_INDEX, get_post((string) $objectId));
		return (string)$objectId;

	} else {

		return false;

	}
}

// Update existing post
function update_post($pid, $text) {
	$savedTime = time()*1000;
	$newDate = new MongoDB\BSON\UTCDateTime($savedTime);
  $mongo = new MongoDB\Driver\Manager(Config::MONGODB);
	$bulk = new MongoDB\Driver\BulkWrite;
	$sectionToUpdate = [
		'text' => $text,
		'last-edit' => $newDate
	];
	$bulk->update(
		['_id' => new MongoDB\BSON\ObjectID($pid)],
		['$set' => $sectionToUpdate ],
		[
			'multi' => false,
			'upsert' => false
		]
	);
	$result = $mongo->executeBulkWrite('tc.posts', $bulk);
	if ($result->getModifiedCount() >= 1) {
		SearchEngine::boot()->create_index(SearchEngine::POSTS_INDEX, get_post($pid));
		return true;
	} else {
		return false;
	}

}

// Update existing comment
function update_comment($cid, $text, $pid = null) {
	$savedTime = time()*1000;
  	$newDate = new MongoDB\BSON\UTCDateTime($savedTime);
  $mongo = new MongoDB\Driver\Manager(Config::MONGODB);
	$bulk = new MongoDB\Driver\BulkWrite;
	$bulk->update(
		['comments._id' => new MongoDB\BSON\ObjectID($cid)],
		['$set' => [
        'comments.$.text'	=> $text,
        'comments.$.last-edit' => $newDate
			]
		],
		[
			'multi' => false,
			'upsert' => false
		]
	);
	$result = $mongo->executeBulkWrite('tc.posts', $bulk);
	if ($result->getModifiedCount() >= 1) {
		if ($pid) {
			SearchEngine::boot()->create_index(SearchEngine::POSTS_INDEX, get_post($pid));
		}
		return true;
	} else {
		return false;
	}

}

// Create notification
function new_notification($responder_name, $responder_id, $responder_pid, $responder_image, $notification_type, $initial_name, $initial_id, $initial_pid, $initial_ptype, $initial_pcontent, $notification_list) {

  $mongo = new MongoDB\Driver\Manager(Config::MONGODB);
	$bulk = new MongoDB\Driver\BulkWrite;
	$objectId = new MongoDB\BSON\ObjectID;
	$bulk->insert(
		[
      '_id' => $objectId,
			'responderName' => $responder_name,
      'responderId' => $responder_id,
      'responderPid' => $responder_pid,
      'responderImage' => $responder_image,
      'notificationType' => $notification_type,
      'initialName' => $initial_name,
      'initialId' => $initial_id,
      'initialPid' => $initial_pid,
      'initialPtype' => $initial_ptype,
      'initialPcontent' => $initial_pcontent,
      'notificationList' => $notification_list,
      'time' => new MongoDB\BSON\UTCDateTime(time()*1000),
			'__v' => 0
		]
	);
	$result = $mongo->executeBulkWrite('tc.notifications', $bulk);
	if ($result->getInsertedCount() >= 1) {

		return (string)$objectId;

	} else {

		return false;

	}

}

// Create notification
function new_activity_log($initiator_id, $activity_name, $activity_data = NULL, $time = NULL) {
  $mongo = new MongoDB\Driver\Manager(Config::MONGODB);
	$bulk = new MongoDB\Driver\BulkWrite;
	$objectId = new MongoDB\BSON\ObjectID;
	$bulk->insert(
		[
      '_id' => $objectId,
			'initiator' => $initiator_id,
      'activity_name' => $activity_name,
      'activity_data' => $activity_data,
      'time' => new MongoDB\BSON\UTCDateTime(time()*1000),
			'__v' => 0
		]
	);
	$result = $mongo->executeBulkWrite('tc.activity', $bulk);
	if ($result->getInsertedCount() >= 1) {

		return (string)$objectId;

	} else {

		return false;

	}

}

// Create log for password reset
function new_reset_log($user_id, $user_email, $user_ip, $user_agent) {

  $mongo = new MongoDB\Driver\Manager(Config::MONGODB);
	$bulk = new MongoDB\Driver\BulkWrite;
	$objectId = new MongoDB\BSON\ObjectID;
	$bulk->insert(
		[
      '_id' => $objectId,
			'user_id' => $user_id,
      'user_email' => $user_email,
      'user_ip' => $user_ip,
      'user_agent' => $user_agent,
      'status' => 'started',
      'time' => new MongoDB\BSON\UTCDateTime(time()*1000),
			'__v' => 0
		]
	);
	$result = $mongo->executeBulkWrite('tc.resets', $bulk);
	if ($result->getInsertedCount() >= 1) {

		return (string)$objectId;

	} else {

		return false;

	}

}

// Create new user
function new_user($firstName, $lastName, $user, $pass, $teachLocationName, $teachLocationCity, $teachLocationState, $teachGrades, $teachSubjects, $teachStart, $teachEnd, $teachLicenseLocation, $teachLicenseComplete, $country = NULL, $referrer = NULL, $verification_token= NULL) {

  $trust_points = 0;
  $blocked = 0;

  // Get config settings
  try {

    $config = json_decode(json_encode(get_config()), true);

  } catch (Exception $e) {

    echo $e->getMessage();
    die();

  }

  // Review for authentic email
  if (!filter_var($user, FILTER_VALIDATE_EMAIL)) {
    $blocked++;
  } else {
    $user_email = explode("@", $user);
  }

  $first_names = preg_split("/[_,\- ]+/", $firstName);
  $last_names = preg_split("/[_,\- ]+/", $lastName);
  $names = array_merge($first_names, $last_names);
  $names_email = array_merge($first_names, $last_names, $user_email);
  $names_email_flat = implode(" ", $names_email);

  // Review for blocked words
  foreach ($config[0]['wordMatchBlocked'] as $word) {
    if (stripos($names_email_flat, $word) !== false) {
      $blocked++;
    }
  }

  // Evaluate trust rating
  if ($blocked < 1) {

    foreach ($config[0]['wordMatchPositive'] as $word) {
      if (stripos($user, $word) !== false) {
        $trust_points++;
      }
    }

    if ($referrer) {
      $trust_points++;
    }

    if ($country == "United States") {
      $trust_points++;
    }

    // if (stripos($user, $firstName) !== false) {
    //   $trust_points++;
    // }
    // if (stripos($user, $lastName) !== false) {
    //   $trust_points++;
    // }

    // foreach ($names as $name) {
    //   if (stripos($user, $name) !== false) {
    //     $trust_points++;
    //   }
    // }

    foreach ($config[0]['wordMatchNegative'] as $word) {
      if (stripos($firstName, $word) !== false) {
        $trust_points = $trust_points-2;
        $flag_name = 1;
      }
      if (stripos($lastName, $word) !== false) {
        $trust_points = $trust_points-2;
        $flag_name = 1;
      }
    }

    if ($flag_name != 1) {
      foreach ($names as $name) {
        if (stripos($user, $name) !== false) {
          $trust_points++;
        }
      }
    }

    if (strlen($firstName) < 3) {
      // $trust_points--;
      $trust_points = $trust_points-2;
    }
    if (strlen($lastName) < 3) {
      // $trust_points--;
      $trust_points = $trust_points-2;
    }

    if (strlen($user_email[0]) < 3) {
      $trust_points--;
    }
    if (strlen($user_email[1]) < 5) {
      $trust_points--;
    }

  }

  if ($trust_points > 1) {
    $trusted_state = TRUE;
  } else {
    $trusted_state = FALSE;
  }


  $objectId = new MongoDB\BSON\ObjectID;
  $user_ip =  get_user_ip_address();

	$insert = array(
		'_id' => $objectId,
		'username' => $firstName,
		'firstName' => $firstName,
		'lastName' => $lastName,
		'email' => $user,
		'password' => password_hash($pass, PASSWORD_DEFAULT),
		'avatar' => '',
		'websites' => [],
		'classroomPhotos' => [],
		'favoriteEdtech' => [],
		'interests' => [],
    'emailNotifications' => [
        'comment' => 1,
        'answer' => 1,
        'follow' => 1
    ],
		'__v' => 0,
    'time' => new MongoDB\BSON\UTCDateTime(time()*1000),
    'referrer' => $referrer,
    'trusted' => $trusted_state,
    'ipAddress' => $user_ip,
	'userStatus' =>'unverified',
	'emailToken' => $verification_token,
	'emailSentAt' => new MongoDB\BSON\UTCDateTime(time()*1000),
    'access' => 0,
		'role' =>"user"
	);

	if ($teachLicenseLocation) {
		$insert['educations'] =
			[
				[
					'institude' => $teachLicenseLocation,
					'yearCompleted' => $teachLicenseComplete,
					'_id' => new MongoDB\BSON\ObjectID
				]
			];
	} else {
		$insert['educations'] = [];
	}
	if ($teachLocationName) {
		$insert['experience'] =
		[
			[
				'_id' => new MongoDB\BSON\ObjectID,
				'datesWorked' =>
				[
					'selectedStart' => $teachStart,
					'selectedEnd' => $teachEnd
				],
				'subjects' => $teachSubjects = explode(',', $teachSubjects),
				'grade' => $teachGrades = explode(',', $teachGrades),
				'school' =>
				[
					'name' => $teachLocationName,
					'city' => $teachLocationCity,
					'state' => $teachLocationState
				]
			]
		];
	} else {
		$insert['experience'] = [];
	}

	$mongo = new MongoDB\Driver\Manager(Config::MONGODB);
	$bulk = new MongoDB\Driver\BulkWrite;
	$bulk->insert($insert);
	$result = $mongo->executeBulkWrite('tc.users', $bulk);

	if ($result->getInsertedCount() == 1) {
		SearchEngine::boot()->create_index(SearchEngine::USERS_INDEX, get_user((string) $objectId));
    	return (string)$objectId;
	} else {
    	return false;
	}
}

//update email notification settings -katie
function update_email_notifications($email_settings) {
	// Make sure email_notifications is an actual array
		if ($email_settings == NULL) {
		$email_settings = [];
		}
		if (is_array($email_settings)) { } else {
		$email_settings = array($email_settings);
		}
	$mongo = new MongoDB\Driver\Manager(Config::MONGODB);
	$bulk = new MongoDB\Driver\BulkWrite;
	$bulk->update(
		['_id' => new MongoDB\BSON\ObjectID($_SESSION['uid'])],
		['$set' =>
  	[
  		'emailNotifications' => $email_settings
  	]
		],
		[
			'multi' => false,
			'upsert' => false
		]
	);
	$result = $mongo->executeBulkWrite('tc.users', $bulk);
	if ($result->getModifiedCount() >= 1 OR $result->getMatchedCount() >= 1) {
		return true;

	} else {
		return false;
	}
}

/* Created by Kiran Sing TC-38*/
function get_groups_by_privacy() {
	$mongo = new MongoDB\Driver\Manager(Config::MONGODB);
	$query = new MongoDB\Driver\Query(
    [ '$and' =>  
				[
          [ 'privacy' => 'public' ],
          [ 'hidden' => '0' ]
         
        ]          
    ]   
  ); 

	$cursor = $mongo->executeQuery('tc.partners', $query);
	$groups = [];
	foreach ($cursor as $document) {
		array_push($groups, json_decode(MongoDB\BSON\toJSON(MongoDB\BSON\fromPHP($document))));
	}
	return $groups;
}

function get_all_public_posts($limit, $offset = NULL, $groups) {
	$mongo = new MongoDB\Driver\Manager(Config::MONGODB);
	$query = new MongoDB\Driver\Query(
    [ 
		 '$or' =>
					[
						['audience' => ['$in' => $groups]],
						['audience' => NULL]
					]       
    ],   
 	  ['limit' => $limit,
	  'sort' => ['time' => -1], 
		'skip' => $offset]);	
	
	$cursor = $mongo->executeQuery('tc.posts', $query);
	$posts = [];
	foreach ($cursor as $document) {
		array_push($posts, json_decode(MongoDB\BSON\toJSON(MongoDB\BSON\fromPHP($document))));
	}
	return $posts;
}

function update_post_flag($pid, $status, $userId) {	
	$mongo = new MongoDB\Driver\Manager(Config::MONGODB);
	$bulk = new MongoDB\Driver\BulkWrite;
	$newDate = new MongoDB\BSON\UTCDateTime(time()*1000);
  $bulk->update(
		['_id' => new MongoDB\BSON\ObjectID($pid)],
		['$set' =>
			[				
				'questionableContent.flagContent' =>$status,
				'questionableContent.activityBy' =>$userId,
				'questionableContent.activityOn' =>$newDate
			]
		],	
		[
			'multi' => false,
			'upsert' => false
		]
	);

	$result = $mongo->executeBulkWrite('tc.posts', $bulk);
	if ($result->getModifiedCount() >= 1) {
		return true;
	} else {
		return false;
	}	
}

function update_comment_flag($cid, $status, $userId) {
	$mongo = new MongoDB\Driver\Manager(Config::MONGODB);
	$bulk = new MongoDB\Driver\BulkWrite;
	$newDate = new MongoDB\BSON\UTCDateTime(time()*1000);
 	$bulk->update(
		['comments._id' => new MongoDB\BSON\ObjectID($cid)],
		['$set' =>
			[				
				'comments.$.questionableContent.flagContent' =>$status,
				'comments.$.questionableContent.activityBy' =>$userId,
				'comments.$.questionableContent.activityOn' =>$newDate
			]
		],
		[
			'multi' => false,
			'upsert' => false
		]
	);

	$result = $mongo->executeBulkWrite('tc.posts', $bulk);

	if ($result->getModifiedCount() >= 1) {
		return true;
	} else {
		return false;
	}	
}

// Flag post comment
function flag_comment($cid, $flag_status, $userId) {
  $newDate = new MongoDB\BSON\UTCDateTime(time()*1000);
  $mongo = new MongoDB\Driver\Manager(Config::MONGODB);
	$bulk = new MongoDB\Driver\BulkWrite;
	$bulk->update(
		['comments._id' => new MongoDB\BSON\ObjectID($cid)],
		['$set' =>
			[
        'comments.$.questionableContent'	=>
				[
					'flagContent' => $flag_status,
					'flagBy' => $userId,
					'flagOn' => $newDate
				]
			]
		],
		[
			'multi' => false,
			'upsert' => false
		]
	);
	$result = $mongo->executeBulkWrite('tc.posts', $bulk);
	if ($result->getModifiedCount() >= 1) {
		return true;
	} else {
		return false;
	}

}

// Flag post content
function flag_post($pid, $flag_status, $userId) {
  $newDate = new MongoDB\BSON\UTCDateTime(time()*1000);
  $mongo = new MongoDB\Driver\Manager(Config::MONGODB);
	$bulk = new MongoDB\Driver\BulkWrite;
	$bulk->update(
		['_id' => new MongoDB\BSON\ObjectID($pid)],
		['$set' =>
			[
        'questionableContent'	=>
				[
					'flagContent' => $flag_status,
					'flagBy' => $userId,
					'flagOn' => $newDate
				]
			]
		],
		[
			'multi' => false,
			'upsert' => false
		]
	);
	$result = $mongo->executeBulkWrite('tc.posts', $bulk);
	if ($result->getModifiedCount() >= 1) {
		return true;
	} else {
		return false;
	}

}

// Get users by role(array of user type/role)
function get_users_by_role($userType) {
  if ($userType == NULL) {
    $userType = [];
  }
	$mongo = new MongoDB\Driver\Manager(Config::MONGODB);
	$query = new MongoDB\Driver\Query(
    ['role' =>
      [ '$in' => $userType ]
    ],
    []
  );
	$cursor = $mongo->executeQuery('tc.users', $query);
  $user = [];
	foreach ($cursor as $document) {
		array_push($user, json_decode(MongoDB\BSON\toJSON(MongoDB\BSON\fromPHP($document))));
	}
  return $user;
}

// Get users by id (array of numbers)
function get_users_by_id_raw($userIds) {
  $userIdsMongo = [];
  foreach ($userIds as $userId) {
    $userIdsMongo[] = new MongoDB\BSON\ObjectID($userId);
  }
	$mongo = new MongoDB\Driver\Manager(Config::MONGODB);
	$query = new MongoDB\Driver\Query(
    ['_id' =>
      [ '$in' => $userIdsMongo ]
    ],
    []
  );
	$cursor = $mongo->executeQuery('tc.users', $query);
  $user = [];
	foreach ($cursor as $document) {
		array_push($user, json_decode(MongoDB\BSON\toJSON(MongoDB\BSON\fromPHP($document))));
	}
  return $user;
}

// Update account verification
function update_account_verification($userId) {
	$newDate = new MongoDB\BSON\UTCDateTime(time()*1000);
	$mongo = new MongoDB\Driver\Manager(Config::MONGODB);
	$bulk = new MongoDB\Driver\BulkWrite;
	$bulk->update(
		['_id' => new MongoDB\BSON\ObjectID($userId)],
		['$set' =>
			[
				'emailVerifiedAt' => $newDate,
				'userStatus' => 'verified',
				'notificationTimestamp' => $newDate
			]
		],
		[
			'multi' => false,
			'upsert' => false
		]
		);
		$result = $mongo->executeBulkWrite('tc.users', $bulk);
		if ($result->getModifiedCount() >= 1) {
			SearchEngine::boot()->create_index(SearchEngine::USERS_INDEX, get_user($userId));
			return true;
		} else {
			return false;
		}
  }

	// Update user profile information
	function update_resend_link($emailToken, $userId) {
		$mongo = new MongoDB\Driver\Manager(Config::MONGODB);
		$bulk = new MongoDB\Driver\BulkWrite;
		$newDate = new MongoDB\BSON\UTCDateTime(time()*1000);
		$user_ip = get_user_ip_address();
		
		$bulk->update(
			['_id' => new MongoDB\BSON\ObjectID($userId)],
			['$set' =>
				[
					'emailToken' => $emailToken,
					'emailSentAt' => $newDate,
					'ipAddress'	=> $user_ip
				]
			],
			[
				'multi' => false,
				'upsert' => false
			]
		);
		$result = $mongo->executeBulkWrite('tc.users', $bulk);
		if ($result->getModifiedCount() >= 1 OR $result->getMatchedCount() >= 1) {
			return true;
		} else {
			return false;
		}
	}

	// Save uri for requested post
	function save_request_uri($uri) {
		$mongo = new MongoDB\Driver\Manager(Config::MONGODB);
		$bulk = new MongoDB\Driver\BulkWrite;
		$objectId = new MongoDB\BSON\ObjectID;
		$newDate = new MongoDB\BSON\UTCDateTime(time()*1000);
		$user_ip = get_user_ip_address();

		// Create record if non exists with same ip address, post/view id for post that
		// hasn't been accessed by the user.
		$check_query = new MongoDB\Driver\Query(
			[
				'user_ip' => $user_ip, 'visited' => 0, 'request_uri' => filter_var($uri, FILTER_SANITIZE_STRING),
			],
			['sort' => ['requestTimestamp' => -1], 'limit' => 1, 'projection' => ['request_uri' => 1, 'path' => 1]]
		);
		$cursor = $mongo->executeQuery('tc.requests', $check_query);
		if($cursor->isDead()) {
			// Remove existing non-visitted records for current ip address.
			$bulk->delete(['user_ip' => $user_ip, 'visited' => 0], []);

			$uri_parts = explode('?',$_SERVER['REQUEST_URI']);
			$path = substr($uri_parts[0], 1);
			$uri_array = explode('&',$uri_parts[1]);
			$uri = explode('=',$uri_array[0])[1];
			$ref_param = count($uri_array) == 2 ? explode('=',$uri_array[1]) : NULL;
			$ref_id = $ref_param && $ref_param[0] === 'ref' ? $ref_param[1] : NULL;
			
			$bulk->insert(
				[
					'_id' => $objectId,
					'requestTimestamp' => $newDate,
					'path' => filter_var($path, FILTER_SANITIZE_STRING),
					'request_uri' => filter_var($uri, FILTER_SANITIZE_STRING),
					'referrer' => str_replace( "%20", " ", filter_var($ref_id, FILTER_SANITIZE_STRING)),
					'user_ip' => $user_ip,
					'visited' => 0
				]
			);
			$result = $mongo->executeBulkWrite('tc.requests', $bulk);
		}
		
		if ($result->getModifiedCount() >= 1) {
			// echo 'saved';
			// die();
			return true;
		} else {
			// echo 'not saved';
			// die();
			return false;
		}
	}

	// Get referrer id from requests table. Will have the id 
	// if it was part of request containing a post id.
	function get_user_referrer($user_ip) {	
		$mongo = new MongoDB\Driver\Manager(Config::MONGODB);
		$query = new MongoDB\Driver\Query(
			[
				'user_ip' => $user_ip, 'visited' => 0
			],
			['sort' => ['requestTimestamp' => -1], 'limit' => 1, 'projection' => ['referrer' => 1]]
		);
		$cursor = $mongo->executeQuery('tc.requests', $query);
		$referrer = [];
		
		foreach ($cursor as $document) {
			$referrer_object = json_decode(MongoDB\BSON\toJSON(MongoDB\BSON\fromPHP($document)));
			$request_object = json_decode(json_encode($referrer_object));
			$ref_id = $request_object->referrer;
			array_push($referrer, $ref_id);
		}
		return $referrer;
	}

	// Update requested uri document with user's email. 
	// Perform update on the last document that contains current user's ip
	function update_request_uri($user_ip, $user_email) {	
		try{
			$mongo = new MongoDB\Client(Config::MONGODB);
			$requests = $mongo->tc->requests;

			$document = $requests->findOneAndUpdate(
    		['user_ip' => $user_ip, 'visited' => 0],
    		['$set' => ['email' => $user_email]],
			['$sort' => ['requestTimestamp' => -1]]);

			return !!$document;
		} catch(MongoResultException $e) {
			return false;
		}	
	}

	// Fetch requested post uri using user's email
	function get_request_uri($user_email) {
		try{
			$mongo = new MongoDB\Driver\Manager(Config::MONGODB);
			$query = new MongoDB\Driver\Query(
				[
					'email' => $user_email, 'visited' => 0
				],
				['sort' => ['requestTimestamp' => -1], 'limit' => 1, 'projection' => ['request_uri' => 1, 'path' => 1]]
			);
			$cursor = $mongo->executeQuery('tc.requests', $query);
			$uri = [];
			foreach ($cursor as $document) {
				$uri_object = json_decode(MongoDB\BSON\toJSON(MongoDB\BSON\fromPHP($document)));
				$request_object = json_decode(json_encode($uri_object));
				$uri_path = $request_object->path;
				$uri_id = $request_object->request_uri;
				array_push($uri, $uri_path.'?id='.$uri_id);
				update_initial_visited($user_email, $uri_id);
			}
			return $uri;
		} catch(MongoResultException $e) {		
			return false;
		}
	}

function update_initial_visited($user_email, $uri_id) {
	try{
			$mongo = new MongoDB\Client(Config::MONGODB);
			$requests = $mongo->tc->requests;

			$document = $requests->findOneAndUpdate(
    		['email' => $user_email, 'request_uri' => $uri_id],
    		['$set' => ['visited' => 1]],
			['$sort' => ['requestTimestamp' => -1]]);

			return !!$document;
		} catch(Exception $e) {
			return false;
		}
}