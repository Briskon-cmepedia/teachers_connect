<div id="profile-header">
  <?php if ($formSubmission == 'fail') { ?>
    <div class="alert warning">Please finish all required fields before continuing.</div>
  <?php } ?>
</div>
<div class="profile-body">
  <div class="user-profile-edit">
    <div class="page-title no-head">
      <h1>Edit Your Personal Information</h1>
    </div>
    <div class="user-profile no-padding">

      <form name="ProfilePicUpload" id="ProfilePicUpload" action="upload.php" method="post" enctype="multipart/form-data">
        <div class="form-node">
          <div class="pic85 center">
          <img class="edit-profile-pic" src="img/icon-camera.svg" alt="icon. edit profile picture">
          <?php if ( (strpos($user_avatar, 'Object') == false) AND ($user_avatar != NULL) ) { ?>
            <img class="avatar" alt="avatar" src="image.php?id=<?=$user_avatar?>" onerror="this.src='img/robot.svg'">
          <?php } else { ?>
            <img class="avatar" src="img/robot.svg" alt="robot avatar">
          <?php } ?>
          </div>
          <label for="ProfilePic" hidden>Profile picture</label>
          <input id="ProfilePic" type="file" name="profilepic" />
        </div>
      </form>
      <div class="clear"></div>
      <form name="edit-information-form" class="form-normal" method="post">

        <div class="form-node<?php if ($formSubmission == 'fail' AND (empty(trim($user_firstName)))) echo ' error'; ?>">
          <label for="user_firstName">First Name</label>
          <input type="text" id="user_firstName" name="user_firstName" value="<?=$user_firstName?>">
        </div>
        <div class="form-node<?php if ($formSubmission == 'fail' AND (empty(trim($user_lastName)))) echo ' error'; ?>">
          <label for="user_lastName">Last Name</label>
          <input type="text" id="user_lastName" name="user_lastName" value="<?=$user_lastName?>">
        </div>
        <div class="form-node<?php if ($formSubmission == 'fail' AND ((empty(trim($user_email))) OR (!filter_var($user_email, FILTER_VALIDATE_EMAIL)))) echo ' error'; ?>">
          <label for="user_email">Your Email Address</label>
          <input type="text" id="user_email" name="user_email" value="<?=$user_email?>" autocomplete="new-password">
        </div>
        <div class="form-node">
          <label for="password">Change Password</label>
          <input type="text" id="password" name="password" value="" placeholder="New Password" autocomplete="new-password">
        </div>
        <div class="clear">
          &nbsp;
        </div>
        <div class="form-node">
          <div class="inline left">
            <input type="button" class="button-cancel" value="Cancel">
          </div>
          <div class="right">
            <input type="submit" class="button-comment" id="edit-information-submit" value="Save">
          </div>
          <div class="clear">
            &nbsp;
          </div>
        </div>
      </form>
    </div>
  </div>
</div>
