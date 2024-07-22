<?php

function test(){
    $email = new \SendGrid\Mail\Mail();
    $email->setFrom("kisingh@pcgus.com", "Example User");
    $email->setSubject("Sending with Twilio SendGrid is Fun");
    $email->addTo("kittu.789@gmail.com", "Example User");
    $email->addContent("text/plain", "and easy to do anywhere, even with PHP");
    $email->addContent(
        "text/html", "<strong>and easy to do anywhere, even with PHP</strong>"
    );
    $sendgrid = new \SendGrid(getenv(Config::SENDGRID_KEY));
    try {
        $response = $sendgrid->send($email);
        print $response->statusCode() . "\n";
        print_r($response->headers());
        print $response->body() . "\n";
    } catch (Exception $e) {
        echo 'Caught exception: '. $e->getMessage() ."\n";
    }
  }

// format email for reset passwords
function formatEmailReset($reset_start, $user_firstName) {
	$email_info = [];
	$email_info['subject'] = "Reset your password at TeachersConnect";
	$email_info['body'] = "<p>Hi " . $user_firstName . ", <br><br>We recently received a request to reset your account password at TeachersConnect. In order to complete this request, you will need to provide us with a new password.<br><br>Please click on this link to set your new password:<br><br><a style='background:#E56841;color:#FFFFFF;text-decoration:none;font-weight:bold;font-size:1.5em;padding:5px 10px;border-radius:4px;' href='" . site_url() . '/reset.php?id=' . $reset_start . "'>Reset Password</a><br><br><i>Please note: This link can only be used once and will become inactive in one hour.  If you do not need to reset your password, please ignore this email.</i><br><br>
</p>";
	$email_info['header'] = "<div style='background: #F9CE28; border-bottom: 1px #D4AA07 solid; text-align: center;'><a href='" . site_url() . "/'><img style='width: 265px; padding: 10px;' alt='TeachersConnect' src='cid:header-logo'></a></div>";
	return $email_info;
}

// format email for account verification
function formatEmailVerification($formatEmailVerification='', $user_firstName) {
	$email_info = [];
	$email_info['subject'] = "Click the button to join";
	
	$email_info['body'] = "<p>Hi " . $user_firstName . ", <br><br>You're one step away! Click the button to start connecting. It helps us keep the community safe and secure.
	</i><br><br>
	<center><a style='border-color: #123b45;border: none;background:#123b45;color:#FFFFFF;text-decoration:none;font-weight:bold;font-size:1.5em;padding:5px 10px;border-radius:4px;' href='" . $formatEmailVerification . "'>Finish Registration</a></center>
	</p>";

	$email_info['header'] = "<div style='background: #F9CE28; border-bottom: 1px #D4AA07 solid; text-align: center;'><a href='" . site_url() . "/'><img style='width: 265px; padding: 10px;' alt='TeachersConnect' src='cid:header-logo'></a></div>";
	return $email_info;
}

// format email notifications -katie
function formatEmail($notification_type, $first_name, $responder_name, $link, $notification_settings, $initial_name, $preview_content) {
	$email_info = [];
	foreach ($notification_settings as $notification_name => $notification_setting) {
		if ( ( $notification_type == $notification_name ) AND ( $notification_setting == 1 ) ) {
			if ($notification_type == "answer") {
				$email_info['subject'] = "A question has been answered on TeachersConnect";
				$email_info['body'] = "<p>Hi " . $first_name . ", <br><br>";
				if ($initial_name == "anonymous") {
					$email_info['body'] = $email_info['body'] . "Someone";
				} else {
					$email_info['body'] = $email_info['body'] . ucwords(strtolower($responder_name));
				}
				$email_info['body'] = $email_info['body'] . " wrote an answer to the following question on TeachersConnect:<br><b>\"" . $preview_content . "\"</b><br><br><a href='" . $link . "'>" . $link . "</a><br><br></p>";
			}
			else if ($notification_type == "comment") {
				$email_info['subject'] = "A post has a new comment on TeachersConnect";
				$email_info['body'] = "<p>Hi " . $first_name . ", <br><br>" . ucwords(strtolower($responder_name)) . " commented on the following post on TeachersConnect. Go take a look! <br>\"<b>" . $preview_content . "\"</b><br><br><a href='" . $link . "'>" . $link . "</a><br><br></p>";
			}
			else if ($notification_type == "follow") {
				$email_info['subject'] = "You have a new follower on TeachersConnect";
				$email_info['body'] = "<p>Hi " . $first_name . ", <br><br>" . ucwords(strtolower($responder_name)) . " started following you on TeachersConnect. Go take a look! <br><br><a href='" . $link . "'>" . $link . "</a><br><br></p>";
			}
				
			// $path = site_url() . '/img/tclogo.png';
			// $type = pathinfo($path, PATHINFO_EXTENSION);
			// $imagedata = file_get_contents($path);
			// $base64 = 'data:image/' . $type . ';base64,' . base64_encode($imagedata);
			$email_info['header'] = "<div style='background: #F9CE28; border-bottom: 1px #D4AA07 solid; text-align: center;'><a href='" . site_url() . "/'><img style='width: 265px; padding: 10px;' alt='TeachersConnect' src='cid:header-logo'></a></div>";
			$email_info['footer'] = "<p style='font-size: 10px;color: #993415'><a href='" . site_url() . "/edit-notifications.php'>Click here to update your email notification preferences</a></p>";
			$email_info['footer-plain'] = "\r\nUpdate your email notification preferences here: " . site_url() . "/edit-notifications.php";
		}
	}

	return $email_info;
}

// format email notifications -Kiran
function formatEmailFlagContent($notification_type, $first_name, $responder_name, $link, $notification_settings, $initial_name, $preview_content) {
	$email_info = [];
 
		if ($notification_type == "flag post admin") {
			$subjectText = html_entity_decode($first_name);
			$email_info['subject'] = "REPORTED VIOLATION: ".$subjectText;
			$email_info['body'] = "<p>Report Violation Author: ".$first_name."<br><br>Link to content: <br>
								   <a href='".$link."' target='_blank'>".$link."</a></p>";									
		}else if ($notification_type == "flag post author") {
			$community_guideline = "https://www.teachersconnect.com/2017/08/10/community-guidelines/";
			$email_info['subject'] = "Oops! Possible TC Community Guidelines Violation";
			$email_info['body'] = "<p>Hello ".$first_name.",<br/>
			A TeachersConnect member marked your recent contribution as a possible violation of the <a href='".$community_guideline."' target='_blank'>Community Guidelines</a>. Keeping the community safe, respectful, and positive is important to all teachers. For the moment, your post has been hidden. The TC Team is reviewing your contribution to determine whether it was a violation. 
			We’ll be in touch ASAP with next steps. <br/><br/>
			
			Thank you for your understanding,  <br/><br/>
			The TC Team</p>";
								
		}else if ($notification_type == "block content") {
			$community_guideline = "https://www.teachersconnect.com/2017/08/10/community-guidelines/";
			$email_info['subject'] = "Confirmed TC Community Guidelines Violation";
			$email_info['body'] = "<p>Hello ".$first_name.",<br/>
			After careful review of your recent post that was flagged by another member as “questionable content,
			” we’re writing to confirm that it included content that violates the TeachersConnect 
			<a href='".$community_guideline."' target='_blank'>Community Guidelines</a>. As a result, we have permanently blocked the post. 
			<br/><br/>
			We look forward to reading your future contributions that add to the community, safety, and professional growth of teachers everywhere. 
			<br/><br/>
			Thank you for your understanding,<br/>
			The TC Team</p>";									
		}else if ($notification_type == "unblock content") {
			$community_guideline = "https://www.teachersconnect.com/2017/08/10/community-guidelines/";
			$email_info['subject'] = "Your post has been unblocked";
			$email_info['body'] = "<p>Hello ".$first_name.",<br/>
			After careful review of your recent post that was flagged by another member as “questionable content,
			” we’re writing to let you know that we did not find your contribution to be in violation of the TeachersConnect
			<a href=".$community_guideline." target='_blank'>Community Guidelines</a>. As a result, we have unblocked the <a href='".$link."' target='_blank'>post</a>. 
			<br/><br/>
			We look forward to reading your future contributions that add to the community, safety, and professional growth of teachers everywhere. 
			<br/><br/>
			Thank you! <br/><br/>
			The TC Team</p>";									
		}
		$email_info['header'] = "<div style='background: #F9CE28; border-bottom: 1px #D4AA07 solid; text-align: center;'><a href='" . site_url() . "/'><img style='width: 265px; padding: 10px;' alt='TeachersConnect' src='cid:header-logo'></a></div>";
		$email_info['footer'] = "<p style='font-size: 10px;color: #993415'><a href='" . site_url() . "/edit-notifications.php'>Click here to update your email notification preferences</a></p>";
		$email_info['footer-plain'] = "\r\nUpdate your email notification preferences here: " . site_url() . "/edit-notifications.php";
   
  return $email_info;
}

//send notification email -katie
function sendEmail ($full_name, $email_address, $email_format) {

  $email = new \SendGrid\Mail\Mail();
	$email->addCategory("notification");
  $email->setFrom("hello@teachersconnect.com", "TeachersConnect");
  $email->setSubject($email_format['subject']);
  $email->addTo($email_address, $full_name);
	$email->addContent("text/plain", strip_tags($email_format['body']) . $email_format['footer-plain']);
	$email->addContent("text/html", $email_format['header'] . $email_format['body'] . $email_format['footer']);
	$file_encoded = base64_encode(file_get_contents(site_url() . '/img/tclogo.png'));
	$email->addAttachment(
	    $file_encoded,
	    "application/png",
	    "tclogo.png",
	    "inline",
			"header-logo"
	);
	//trying sendgrid templates
	//$email->setTemplateId("d-f5770013045c47dea29d521233a1ad7f");
  $sendgrid = new \SendGrid(Config::SENDGRID_KEY);

    try {
        $response = $sendgrid->send($email);
        //print $response->statusCode() . "\n";
        //print_r($response->headers());
        //print $response->body() . "\n";
    } catch (Exception $e) {
        //echo 'Caught exception: ',  $e->getMessage(), "\n";
        echo 'Something went wrong';
        die();
    }
  }


//send notification email -katie
function sendEmailTemplate ($full_name, $email_address, $email_format, $template_id) {

  $email = new \SendGrid\Mail\Mail();
//   $email->setFrom("hello@teachersconnect.com", "TeachersConnect");
  $email->setFrom("lakshmikanth.rk@briskon.com", "TeachersConnect");
  $email->setSubject($email_format['subject']);
  $email->addTo($email_address, $full_name);
	$email->addContent("text/plain", strip_tags($email_format['body']) . $email_format['footer-plain']);
	$email->addContent("text/html", $email_format['header'] . $email_format['body'] . $email_format['footer']);
	//changing path to logo png
	$file_encoded = base64_encode(file_get_contents('C:\xampp\htdocs\dev-staging\img\tclogo.png'));
	//$file_encoded = base64_encode(file_get_contents(site_url() . '/img/tclogo.png'));
	$email->addAttachment(
	    $file_encoded,
	    "application/png",
	    "tclogo.png",
	    "inline",
			"header-logo"
	);
	//trying sendgrid templates
	// $email->setTemplateId("d-f5770013045c47dea29d521233a1ad7f");
	$email->setTemplateId("d-687af4be327044dda05f10babb2990b6");
  $sendgrid = new \SendGrid(Config::SENDGRID_KEY);

    try {
        $response = $sendgrid->send($email);
        print $response->statusCode() . "\n";
        print_r($response->headers());
        print $response->body() . "\n";
    } catch (Exception $e) {
        //echo 'Caught exception: ',  $e->getMessage(), "\n";
        echo 'Something went wrong';
        die();
    }
  }

	 

