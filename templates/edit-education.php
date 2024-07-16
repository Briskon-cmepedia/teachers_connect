<div id="profile-header">
  <?php if ($formSubmission == 'fail') { ?>
    <div class="alert warning">Please finish all required fields before continuing.</div>
  <?php } ?>
</div>
<div class="profile-body">
  <div class="user-profile-edit">
    <div class="page-title no-head">
      <h1>Edit Your Education</h1>
    </div>
    <div class="page-section-explanation">
      TeachersConnect provides opportunities to connect with people and places you already know. Add the places you have studied to connect to your fellow alumni in the community.
    </div>
    <div class="user-profile no-padding">
      <form name="edit-education-form" class="" method="post">
        <input type="hidden" id="educationId" name="educationId" value="<?=$education_id?>">
        <div class="form-node<?php if ($formSubmission == 'fail' AND (empty(trim($teachLicenseLocation)))) echo ' error'; ?>">
          <label for="teachLicenseLocation">Where did/will you earn your teaching license?</label>
          <input type="text" id="teachLicenseLocation" name="teachLicenseLocation" value="<?=$teachLicenseLocation?>">
        </div>
        <div class="form-node<?php if ($formSubmission == 'fail' AND (empty(trim($teachLicenseComplete)))) echo ' error'; ?>">
          <label for="teachLicenseComplete">What year did/will you complete your program there?</label>
          <input type="text" id="teachLicenseComplete" name="teachLicenseComplete" value="<?=$teachLicenseComplete?>">
        </div>
        <div class="form-node">
          <div class="inline left">
            <input type="button" class="button-cancel" value="Cancel">
          </div>
          <div class="right">
            <input type="submit" class="button-comment" id="edit-education-submit" value="Save">
          </div>
          <div class="clear">
            &nbsp;
          </div>
        </div>
      </form>
    </div>
  </div>
</div>
