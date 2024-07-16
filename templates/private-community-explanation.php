<div id="profile-header"></div>
<div class="profile-body">
  <div class="user-profile-edit">
    <div class="page-title no-head center">
      <img class="icon-large" src="img/icon-padlock.svg" alt="padlock icon">
    </div>
    <div class="box-center">
      <?php if ($group_logo) { ?>
        <div class="feed-logo"><img class="logo-partner" src="image.php?id=<?=$group_logo?>"></div>
      <?php } ?>
      <div class="page-title no-head">
        <h1><?=$this->e($title)?></h1>
      </div>
      <div class="page-section-explanation">
        The <?=$this->e($title)?> is a private community and all new members are required to be approved by the community manager. Once you are approved, you will receive a notification and be able to access the community freely.
        <br><br>
        If you want to become a member of this online community, please click the Knock button below.  This will send your name, email address and general profile information to the community manager.
      </div>
      <div class="form-node">
        <div class="inline left">
          <div class="button-cancel button-secondary back-general"><img class="icon-button-arrow icon-left svg" src="img/arrow-down.svg" alt="icon. arrow down"> Back</div>
        </div>
        <div class="right">
          <input type="button" class="button button-knock" id="private-community-knock" data-id="<?=$group_id?>" value="Knock">
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
