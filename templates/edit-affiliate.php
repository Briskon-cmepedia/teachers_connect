<div id="profile-header"></div>
<div class="profile-body">
  <div class="user-profile-edit">
    <div class="page-title no-head">
      <h1>Communities You Follow</h1>
    </div>
    <ul class="tile-group tile-group-connector max-width">
      <!-- <li class="tile-single">
        <a href="feed-following.php"><img id="button-connections-feed" class="tile-button large" src="img/tile-your-connections.png"></a>
      </li> -->
      <li class="tile-single">
        <a href="feed.php"><img id="button-community-feed" class="tile-button large" src="img/tile-community-feed.png" alt="button community feed"></a>
      </li>
      <?php if ($affiliates_followed) {
        foreach ($affiliates_followed as $partners => $partner) { ?>
          <li class="tile-single"><a href="feed.php?id=<?=$partner['id']?>"><img id="button-community-feed" class="tile-button large" alt="<?=$partner['name']?>" src="image.php?id=<?=$partner['image']?>"></a></li>
        <?php }
      } ?>
    </ul>
    <div class="horizontal-line"></div>
    <?php if ($affiliates_available) { ?>
    <div class="page-title no-head">
      <h1>Open Communities</h1>
    </div>
    <ul class="tile-group tile-group-connector max-width">
      <?php if ($affiliates_available) {
        foreach ($affiliates_available as $partners => $partner) { ?>
          <li class="tile-single"><a href="feed.php?id=<?=$partner['id']?>"><img id="button-community-feed" class="tile-button large" alt="<?=$partner['name']?>" src="image.php?id=<?=$partner['image']?>"></a></li>
        <?php }
      } ?>
    </ul>
    <div class="horizontal-line"></div>
    <?php } ?>
    <div class="page-title no-head">
      <h1>Private Communities</h1>
    </div>
    <div class="page-section-explanation">
      TeachersConnect provides some partner organizations with dedicated communities. If you are a member of these organizations, join them here to gain access to their online communities.
    </div>
    <div class="user-profile no-padding">
      <div class="affiliate-list">
      <?php if ($affiliates_private) { ?>
        <?php foreach ($affiliates_private as $partners => $partner) { ?>
          <div class="affiliate-list-item">
            <img class="affiliate-list-logo" src="image.php?id=<?=$partner['logo']?>" alt="<?=$partner['name']?>">
            <div class="affiliate-list-text">
              <div class="right">
                <?php if ($partner['knocked'] == 1) { ?>

                  <span class="button" disabled>Pending</span>

                <?php } else {

                  if ($main_menu_referer) { ?>
                    <a href="edit-affiliate.php?id=<?=$partner['id']?>&main_menu_referer=<?=$main_menu_referer?>" class="button">
                  <?php } else { ?>
                    <a href="edit-affiliate.php?id=<?=$partner['id']?>" class="button">
                  <?php } ?>
                    <img class="icon-options" src="img/icon-add.svg" alt="icon join"> Join
                  </a>

                <?php } ?>

                </div>
                <h3><?=$partner['name']?>
                <?php if ($partner['privacy'] == "private") { ?>
                  <!-- <img class="icon-title" src="img/icon-padlock.svg"> -->
                <?php } ?>
                </h3>
                <div class="affiliate-list-description">
                  <?=$partner['description']?>
                  <?php if ($partner['knocked'] == 1) { ?>
                    <div class="notice">Your membership is currently pending approval by the community manager.</div>
                  <?php } ?>
                </div>
                <!-- <div class="affiliate-list-num-users">
                  <?=$partner['num_users']?> Members
                </div> -->
              </div>
            </div>
        <?php } ?>
      <?php } else { ?>
        <h1 class="alert-error">There are no new communities for you to join.</h1>
      <?php } ?>
      </div>
    </div>
  </div>
</div>
