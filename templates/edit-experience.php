<div id="profile-header">
  <?php if ($formSubmission == 'fail') { ?>
    <div class="alert warning">Please finish all required fields before continuing.</div>
  <?php } ?>
</div>
<div class="profile-body">
  <div class="user-profile-edit">
    <div class="page-title no-head">
      <h1>Edit Your Experience</h1>
    </div>
    <div class="page-section-explanation">
      TeachersConnect provides opportunities for great people and ideas to collide. Add the subjects, ages, and places you have worked to help us keep your content relevant and helpful.
    </div>
    <div class="user-profile no-padding">
      <form name="edit-experience-form" class="" method="post">

        <div class="form-node<?php if ($formSubmission == 'fail' AND (empty(trim($teachLocationName)))) echo ' error'; ?>">
          <label for="teachLocation">Where did/do you teach?</label>
          <input type="text" id="teachLocationName" name="teachLocationName" value="<?=$teachLocationName?>">
          <input class="half-width"  type="hidden" id="teachLocationCity" name="teachLocationCity" value="<?=$teachLocationCity?>">
          <input class="half-width"  type="hidden" id="teachLocationState" name="teachLocationState" value="<?=$teachLocationState?>">
          <input type="hidden" id="teachLocationCountry" name="teachLocationCountry" value="<?=$teachLocationCountry?>">
        </div>
        <div class="form-node<?php if ($formSubmission == 'fail' AND (empty(trim($teachGrades)))) echo ' error'; ?>">
          <label for="teachGrades">What grade(s) did/do you teach?</label>
          <div class="form-supplement">Select multiples</div>
          <input type="text" id="teachGrades" name="teachGrades" value="<?=$teachGrades?>">
        </div>
        <div class="form-node<?php if ($formSubmission == 'fail' AND (empty(trim($teachSubjects)))) echo ' error'; ?>">
          <label for="teachSubjects">What subjects(s) did/do you teach?</label>
          <div class="form-supplement">Select multiples</div>
          <input type="text" id="teachSubjects" name="teachSubjects" value="<?=$teachSubjects?>">
        </div>
        <div class="form-node<?php if ($formSubmission == 'fail' AND ( (empty(trim($teachStart))) OR (empty(trim($teachEnd))) ) ) echo ' error'; ?>">
          <label for="teachLength">How much experience did/do you have teaching there?</label>
          <input class="half-width" type="text" id="teachStart" name="teachStart" value="<?=$teachStart?>" placeholder="Start">
          <input class="half-width" type="text" id="teachEnd" name="teachEnd" value="<?=$teachEnd?>" placeholder="End">
        </div>
        <div class="clear">
          &nbsp;
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
