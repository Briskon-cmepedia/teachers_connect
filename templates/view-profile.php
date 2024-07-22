<div id="profile">
  <div id="profile-header" class="profile-header">

  <?php if (($user_id == $_SESSION['uid']) AND $alert == 'success' AND $comid) { ?>
    <div class="alert">
      Teacher community added to your profile.
      <a class="button-alert" href="feed.php?id=<?=$comid?>">View Community</a>
    </div>
  <?php } elseif (($user_id == $_SESSION['uid']) AND $alert == 'success') { ?>
    <div class="alert">Your profile has been saved and updated.</div>
  <?php } ?>
  <?php if ($action == 'follow' AND $alert == 'success') { ?>
    <div class="alert">You are now following <?=ucwords($user_firstName)?> <?=ucwords($user_lastName)?>.</div>
  <?php } ?>
  <?php if ($action == 'unfollow' AND $alert == 'success') { ?>
    <div class="alert">You are no longer following <?=ucwords($user_firstName)?> <?=ucwords($user_lastName)?>.</div>
  <?php } ?>
  <?php if ($violations == 1) { ?>
    <div class="alert warning">There is a problem with your account. <a href="violations.php">Please click here to find out more.</a></div>
  <?php } ?>

    <div class="user-profile-header">
      <div class="user-profile">
        <div class="user-profile-row">
          <div class="profile-left profile-block">
            <div class="post-header profile-pic">
              <?php if ( (strpos($user_avatar, 'Object') == false) AND ($user_avatar != NULL) ) { ?>

                  <img class="avatar large" alt="avatar" src="image.php?id=<?=$user_avatar?>&height=300">

              <?php } else { ?>

                  <img class="avatar large" src="img/robot.svg" alt="robot avatar">

              <?php } ?>
            </div>
            <div class="profile-metadata">
              <div class="user-name section-heading">
                <?=ucwords($user_firstName)?> <?=ucwords($user_lastName)?>
              </div>
              <div class="user-location">
                <?php if ($user_school['city'] AND $user_school['state']) { ?>
                  <img class="icon-small" src="img/icon-pin.svg"><?=$user_school['city']?>, <?=$user_school['state']?>
                <?php } ?>
              </div>

                <?php if ($user_id == $_SESSION['uid']) { ?>
                  <div class="profile-actions">
                    <a href="edit-profile.php"><button class="button-secondary"><img class="icon-options" src="img/icon-edit.svg" alt="icon. edit profile"> Edit Profile</button></a>
                    <a href="edit-notifications.php"><button class="button-secondary">Notification Settings</button></a>
                  </div>
                <?php } elseif ($user_followed == 1 AND $visitor_trusted == TRUE AND $_SESSION['access'] == 'yes') { ?>
                  <a href="javascript:void(0)">
                    <div class="profile-actions dropdown">
                      <div id="button-follow">Following <img class="icon-button-arrow" src="img/arrow-down.svg" alt="icon. arrow down"></div>
                      <div class="dropdown-content">
                        <a href="process.php?type=unfollow&id=<?=$user_id?>"><div id="button-follow"><img class="icon-options" src="img/icon-unfollow-white.svg" alt="icon. unfollow"> Unfollow</div></a>
                      </div>
                    </div>
                  </a>
                <?php } elseif ($visitor_trusted == TRUE AND $_SESSION['access'] == 'yes') { ?>
                  <div class="profile-actions">
                    <a href="process.php?type=follow&id=<?=$user_id?>"><button id="button-follow" class="button"><img class="icon-options" src="img/icon-follow-white.svg" alt="icon. follow"> Follow</button></a>
                  </div>
                <?php } ?>
                <?php if ($user_id !== $_SESSION['uid'] AND $visitor_trusted == TRUE AND $_SESSION['access'] == 'yes') { ?>
                  <div class="profile-actions">
                    <a href="messages-new.php?id=<?=$user_id?>"><button id="button-message" class="button">Message</button></a>
                  </div>
                <?php } ?>

            </div>
          </div>
          <div class="profile-right profile-block">
            <div class="section-heading">

            </div>
            <div class="section-body">

              <?php if ($_SESSION['access'] == 'yes') { ?>

                <?php if ($user_follower_count > 0) { ?>
                  <a href="followers.php?id=<?=$user_id?>">
                    <div class="stats-container">
                      <div class="stats-icon">
                        <img class="icon-stats" src="img/icon-followers.svg" alt="icon. followers">
                      </div>
                      <div class="stats-text">
                        <span class="stat-num"><?=$user_follower_count?></span><br>Follower<?php if ($user_follower_count > 1) { echo "s"; } ?>
                      </div>
                    </div>
                  </a>
                <?php } elseif ($user_id == $_SESSION['uid']) { ?>
                  <div class="stats-container">
                    <div class="stats-icon">
                      <img class="icon-stats" src="img/icon-followers-blue.svg" alt="icon. followers">
                    </div>
                    <div class="stats-text">
                      <span class="stat-num"><?=$user_follower_count?></span><br>Followers
                    </div>
                  </div>
                <?php } ?>

                <?php if ($user_following_count > 0) { ?>
                  <a href="following.php?id=<?=$user_id?>">
                    <div class="stats-container">
                      <div class="stats-icon">
                        <img class="icon-stats" src="img/icon-following.svg" alt="icon. following">
                      </div>
                      <div class="stats-text">
                          <span class="stat-num"><?=$user_following_count?></span><br>Following
                      </div>
                    </div>
                  </a>
                <?php } elseif ($user_id == $_SESSION['uid']) { ?>
                  <div class="stats-container">
                    <div class="stats-icon">
                      <img class="icon-stats" src="img/icon-following-blue.svg" alt="icon. following">
                    </div>
                    <div class="stats-text">
                      <span class="stat-num"><?=$user_following_count?></span><br>Following
                    </div>
                  </div>
                <?php } ?>

                <?php if ($post_count > 0) { ?>
                  <a href="posts.php?id=<?=$user_id?>">
                    <div class="stats-container">
                      <div class="stats-icon">
                        <img class="icon-stats" src="img/icon-posts.svg" alt="icon. posts">
                      </div>
                      <div class="stats-text">
                        <span class="stat-num"><?=$post_count?></span><br>Contributions
                      </div>
                    </div>
                  </a>
                <?php } elseif ($user_id == $_SESSION['uid']) { ?>
                  <div class="stats-container">
                    <div class="stats-icon">
                      <img class="icon-stats" src="img/icon-posts-blue.svg" alt="icon. posts">
                    </div>
                    <div class="stats-text">
                      <span class="stat-num"><?=$post_count?></span><br>Contributions
                    </div>
                  </div>
                <?php } ?>

              <?php } ?>

            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="profile-body">
    <?php if ( $user_id == $_SESSION['uid'] AND $_SESSION['access'] == 'yes' ) { // Show referral link on your own profile ?>
      <div class="referral-link">
        <h4 class="referral-title">Your Personal Referral Link:</h4>
        <div class="box-border link-referral">
          <span class="text-em">https://www.teachersconnect.online/refer.php?ref=<?=$_SESSION['uid']?></span>
        </div>
      </div>
    <?php } ?>
    <div class="user-profile no-padding">
      <div class="col">

        <?php if ( ( $user_id == $_SESSION['uid'] AND $_SESSION['access'] == 'yes' ) AND ( $user_helpfuls_count > 0 ) ) { // Show impact statistics on your own profile ?>

        <div class="profile-section-overview">
          <div class="stats-helpful-block">
            <img class="icon" src="img/icon-helpful-stats.svg"> Your contributions have been <span class="text-focus">marked helpful <?=$user_helpfuls_count?> times</span> by other members.
          </div>
        </div>

      <?php } elseif ( $user_id != $_SESSION['uid'] ) { // Show general activity information for all other profiles ?>

        <div class="profile-section-overview">
          <img class="icon" src="img/icon-history.svg" alt="icon. history"> <?=$user_status?>
        </div>

        <?php } ?>

        <div class="section-header">
          <h2>About Me</h2>
        </div>

        <?php
        if ($user_bio) { ?>
          <div class="profile-section-bio-listing">
            <?=nl2br($user_bio)?>
          </div>
        <?php } else { ?>
          <div class="profile-section-empty">
            <?php if ($user_id == $_SESSION['uid'] AND $_SESSION['access'] == 'yes') { ?>
              <a href="edit-bio.php" class="prebold">Click here to introduce yourself</a> so that other teachers can get to know you.
            <?php } else { ?>
              <?=ucwords($user_firstName)?> has not filled out this profile section yet.
            <?php } ?>
          </div>
        <?php } ?>

        <div class="section-header">
          <h2>Teacher Communities</h2>
        </div>

        <?php
        if ($affiliates_subscribed) {
          foreach ($affiliates_subscribed as $affiliate_subscribed) { ?>
            <div class="profile-section-affiliate-listing">
            <b><?=$affiliate_subscribed['name']?></b><br>
          </div>
          <?php } } else { ?>
          <div class="profile-section-empty">
            <?php if ($user_id == $_SESSION['uid'] AND $_SESSION['access'] == 'yes') { ?>
              <a href="edit-affiliate.php" class="prebold">Click here to join a community</a> that offers a dedicated space on TeachersConnect.
            <?php } else { ?>
              <?=ucwords($user_firstName)?> has not joined any teacher communities yet.
            <?php } ?>
          </div>
        <?php } ?>


      </div>
      <div class="col">
        <div class="section-header">
          <h2>Education</h2>
        </div>

        <?php
        if ($user_education) {
          foreach ($user_education as $education) { ?>
            <div class="profile-section-education-listing">
            <b><?=$education['institude']?></b><br>
            </div>
          <?php } } else { ?>
            <div class="profile-section-empty">
              <?php if ($user_id == $_SESSION['uid'] AND $_SESSION['access'] == 'yes') { ?>
                <a href="edit-education.php" class="prebold">Click here to add your education</a> to your profile to help others find you.
              <?php } else { ?>
                <?=ucwords($user_firstName)?> has not filled out this profile section yet.
              <?php } ?>
            </div>
        <?php } ?>


        <div class="section-header">
          <h2>Teaching Experience</h2>
        </div>

        <?php
        if ($user_experience) {
          foreach ($user_experience as $experience) { ?>
            <div class="profile-section-experience-listing">
              <b><?=$experience['school']['name']?></b><br>
              <?php foreach ($experience['grade'] as $grade) {
                if ($grade !== end($experience['grade'])) {
                  echo $grade . ', ';
                } else {
                  echo $grade;
                }
              } ?>
              <br>
              <?php foreach ($experience['subjects'] as $subjects) {
                if ($subjects !== end($experience['subjects'])) {
                  echo $subjects . ', ';
                } else {
                  echo $subjects;
                }
              } ?>
            </div>
        	<?php } } else { ?>
          <div class="profile-section-empty">
            <?php if ($user_id == $_SESSION['uid'] AND $_SESSION['access'] == 'yes') { ?>
              <a href="edit-experience.php" class="prebold">Click here to add your experience</a> to your profile to help others find you.
            <?php } else { ?>
              <?=ucwords($user_firstName)?> has not filled out this profile section yet.
            <?php } ?>
          </div>
        <?php } ?>

      </div>
    </div>
  </div>
</div>
