<div id="profile-header"></div>
<div class="profile-body">
  <div class="user-profile-edit">
    <div class="page-title no-head center">
      <img class="icon-large" src="img/icon-knock.svg">
    </div>
    <div class="box-center">
      <div class="page-section-explanation">
        You have successfully knocked on the door of
      </div>
      <?php if ($group_logo) { ?>
        <div class="feed-logo"><img class="logo-partner" src="image.php?id=<?=$group_logo?>"></div>
      <?php } ?>
      <div class="page-title no-head">
        <h1><?=$this->e($title)?></h1>
      </div>
      <div class="page-section-explanation">
        We have sent your request to join this private community to the community manager. Once you are approved, you will receive a notification and be able to access the community freely.
      </div>
      <div class="form-node">
        <div>
          <div class="button-cancel button-secondary back-general center"><img class="icon-button-arrow icon-left svg" src="img/arrow-down.svg" alt="icon. arrow down"> Back</div>
        </div>
        <div class="clear">
          &nbsp;
        </div>
      </div>
    </div>
    <div class="clear page_bottom">
      &nbsp;
    </div>
  </div>
</div>
