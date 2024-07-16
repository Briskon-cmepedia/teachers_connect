<div id="profile-header">
  <?php if ($alert == 'success' AND $comid) { ?>
    <div class="alert">
      Teacher community added to your profile.
      <a class="button-alert" href="feed.php?id=<?=$comid?>">View Community</a>
    </div>
  <?php } elseif ($alert == 'success' OR $alert == 'removed') { ?>
    <div class="alert">Your profile has been saved and updated.</div>
  <?php } ?>
</div>
<div class="profile-body">
  <div class="user-profile-header">
    <div class="page-title no-head">
      <a href="profile.php?id=<?=$user_id?>"><div class="button-secondary back-profile"><img class="icon-button-arrow icon-left svg" src="img/arrow-down.svg" alt="icon. arrow down"> Back to Profile</div></a>
      <h1>Edit Your Profile</h1>
    </div>
    <div class="user-profile no-padding">
      <div class="col">
        <div class="section-header">
          <div class="right">
            <a href="edit-information.php" class="button"><img class="icon-options" src="img/icon-edit.svg" alt="icon. edit personal information"> Edit</a>
          </div>
          <h2>Personal Information</h2>
        </div>

        <div class="profile-section-info-listing">
          <div class="profile-pic-container pic85 left">
          <?php if ( (strpos($user_avatar, 'Object') == false) AND ($user_avatar != NULL) ) { ?>
            <img class="avatar" alt="avatar" src="image.php?id=<?=$user_avatar?>&height=300" onerror="this.src='img/robot.svg'">
          <?php } else { ?>
            <img class="avatar" src="img/robot.svg" alt="robot avatar">
          <?php } ?>
          </div>

        <b><?=ucfirst($user_firstName)?> <?=ucfirst($user_lastName)?></b><br>
        <span class="profile-field-title">Username: </span> <?=$user_email?><br>
        <span class="profile-field-title">Password: </span> ****************<br>

        </div>

        <div class="section-header">
          <?php if ($_SESSION['access'] == 'yes') { ?>
            <div class="right">
              <a href="edit-bio.php" class="button"><img class="icon-options" src="img/icon-edit.svg" alt="icon. edit bio"> Edit</a>
            </div>
          <?php } ?>
          <h2>About Me</h2>
        </div>

        <?php
        if ($user_bio) { ?>
          <div class="profile-section-bio-listing">
            <?=nl2br($user_bio)?>
          </div>
        <?php } else { ?>
          <div class="profile-section-explanation">
            TeachersConnect members can view your profile and learn more about you. Add some text to introduce yourself to other members and tell them more about you.
          </div>
        <?php } ?>

        <div class="section-header">
          <?php if ($_SESSION['access'] == 'yes') { ?>
            <div class="right">
              <a href="edit-affiliate.php" class="button"><img class="icon-options" src="img/icon-add.svg" alt="icon. add introduction"> Add</a>
            </div>
          <?php } ?>
          <h2>Teacher Communities</h2>
        </div>

        <?php
        if ($affiliates_subscribed) {
          foreach ($affiliates_subscribed as $affiliate_subscribed) { ?>
            <div class="profile-section-affiliate-listing">
            <?php if ($_SESSION['access'] == 'yes') { ?>
              <div class="post-header right">
                <a href="javascript:void(0)">
                <div class="dropdown">
                <span class="text-options">Options</span> <img class="icon-button-arrow" src="img/arrow-down.svg" alt="icon. arrow down">
                  <div class="dropdown-content">
                    <a href="#confirm-delete" rel="modal:open">
                    <div class="button-delete" data-id="<?=$affiliate_subscribed['_id']['$oid']?>" data-type="affiliate">
                      Delete
                    </div></a>
                  </div>
                </div></a>
              </div>
            <?php } ?>
            <b><?=$affiliate_subscribed['name']?></b><br>
          </div>
          <?php } } else { ?>
          <div class="profile-section-explanation">
            TeachersConnect provides some partner organizations with dedicated communities. If you are a member of these organizations, add them here to gain access to their online communities.
          </div>
        <?php } ?>


      </div>
      <div class="col">
        <div class="section-header">
          <?php if ($_SESSION['access'] == 'yes') { ?>
            <div class="right">
              <a href="edit-education.php" class="button"><img class="icon-options" src="img/icon-add.svg" alt="icon. add education"> Add</a>
            </div>
          <?php } ?>
          <h2>Education</h2>
        </div>

        <?php
        if ($user_education) {
          foreach ($user_education as $education) { ?>
            <div class="profile-section-education-listing">
              <?php if ($_SESSION['access'] == 'yes') { ?>
                <div class="post-header right">
                  <a href="javascript:void(0)">
                    <div class="dropdown">
                      <span class="text-options">Options</span> <img class="icon-button-arrow" src="img/arrow-down.svg" alt="icon. arrow down">
                        <div class="dropdown-content">
                          <a href="#confirm-delete" rel="modal:open"><div class="button-delete" data-id="<?=$education['_id']['$oid']?>" data-type="education">Delete</div></a>
                        </div>
                    </div>
                </div>
              <?php } ?>
            <b><?=$education['institude']?></b><br>
            <?php if ($education['yearCompleted'] < date("Y")) {
              echo 'Graduated ' . $education['yearCompleted'] . '<br>';
            } elseif ($education['yearCompleted']) {
              echo 'Graduating ' . $education['yearCompleted'] . '<br>';
            } ?>
            </div>
          <?php } } else { ?>
            <div class="profile-section-explanation">
              TeachersConnect provides opportunities to connect with people and places you already know. Add the places you have studied to connect to your fellow alumni in our community.
            </div>
        <?php } ?>


        <div class="section-header">
          <?php if ($_SESSION['access'] == 'yes') { ?>
            <div class="right">
              <a href="edit-experience.php" class="button"><img class="icon-options" src="img/icon-add.svg" alt="icon. add experience"> Add</a>
            </div>
          <?php } ?>
          <h2>Teaching Experience</h2>
        </div>

        <?php
        if ($user_experience) {
          foreach ($user_experience as $experience) { ?>
            <div class="profile-section-experience-listing">
              <?php if ($_SESSION['access'] == 'yes') { ?>
                <div class="post-header right">
                  <a href="javascript:void(0)">
                    <div class="dropdown">
                      <span class="text-options">Options</span> <img class="icon-button-arrow" src="img/arrow-down.svg" alt="icon. arrow down">
                        <div class="dropdown-content">
                          <a href="#confirm-delete" rel="modal:open"><div class="button-delete" data-id="<?=$experience['_id']['$oid']?>" data-type="experience">Delete</div></a>
                        </div>
                    </div>
                </div>
              <?php } ?>
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
              <br>
              <?=$experience['datesWorked']['selectedStart']?> - <?=$experience['datesWorked']['selectedEnd']?>
            </div>
        	<?php } } else { ?>
          <div class="profile-section-explanation">
            TeachersConnect provides opportunities for great people and ideas to collide. Add the subjects, ages, and places you have worked to help us keep your content relevant and helpful.
          </div>
        <?php } ?>

      </div>
    </div>
  </div>
</div>
