<div id="profile-header"></div>
<div class="profile-body">
  <div class="user-profile-edit">
    <div class="page-title no-head center">
      <img class="icon-large" src="img/icon-accepted.svg">
    </div>
    <div class="box-center">
      <?php if ($group_logo) { ?>
        <div class="feed-logo"><img class="logo-partner" src="image.php?id=<?=$group_logo?>"></div>
      <?php } ?>
      <div class="page-title no-head">
        <h1><?=$this->e($title)?></h1>
      </div>
      <div class="page-section-explanation">
        The <?=$this->e($title)?> is a private community and you have automatically been approved by the community manager.<br><br>  To make posts to just this private community, remember to select the community name from the Audience controls when you create new posts or questions.
      </div>
      <div class="form-node">
        <div>
          <a class="button center" href="feed.php?id=<?=$group_id?>">Visit Community</a><br><br>
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
