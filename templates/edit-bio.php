<div id="profile-header"></div>
<div class="profile-body">
  <div class="user-profile-edit">
    <div class="page-title no-head">
      <h1>About Me</h1>
    </div>
    <div class="page-section-explanation">
      TeachersConnect members can view your profile and learn more about you. Add some text to introduce yourself to other members who find you on the community.
    </div>
    <div class="user-profile no-padding">
      <form name="edit-bio-form" class="" method="post">
        <div class="form-node form-textarea">
          <label for="bioText" hidden="hidden">Textbox. User bio</label>
          <textarea id="bioText" name="bioText"><?=$user_bio?></textarea>
        </div>
        <div class="form-node">
          <div class="inline left">
            <input type="button" class="button-cancel" value="Cancel">
          </div>
          <div class="right">
            <input type="submit" class="button-comment" id="edit-bio-submit" value="Save">
          </div>
          <div class="clear">
            &nbsp;
          </div>
        </div>
      </form>
    </div>
  </div>
</div>
