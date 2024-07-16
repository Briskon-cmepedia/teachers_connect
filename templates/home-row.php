<div class="page-title no-head">
  <?php if ($_SESSION['access'] == 'yes' && ($row_title == 'Trending Posts')) { ?>
  <div class="search-bar">
    <form id="site-search" class="site-search" action="<?php if ($page == 'members') { echo "members.php"; } else { echo "feed.php"; }?>">
      <fieldset style="border: 0px;">
        <legend hidden>Ask a question, search a topic, find a member</legend>
        <div style="display: inline-block;">
          <img class="icon-search" src="img/icon-search.svg" alt="search">
        </div>
        <div style="display: inline-block;">
          <label for="home-text-search" form="site-search" hidden="hidden">
            Textbox. Ask a question, search a topic, find a member
          </label>
          <input id="home-text-search" class="text-search" name="search" type="text" placeholder="Ask a question, search a topic, find a member" value="<?=$search_term?>" tabindex="0">
        </div>      
        <div class="search-dropdown">
          <div class="search-dropdown-option">
            <input name="search-domain" value="contributions" id="search-dropdown-option-contributions" type="radio"<?php if ($page == 'feed') { echo ' checked="checked"'; }?>><label class="search-label" for="search-dropdown-option-contributions">Contributions</label>
          </div>
          <div class="search-dropdown-option">
            <input name="search-domain" value="members" id="search-dropdown-option-members" type="radio"<?php if ($page == 'members') { echo ' checked="checked"'; }?>><label class="search-label" for="search-dropdown-option-members">Members</label>
          </div>
          <input value="Find" class="search-dropdown-button" type="submit">
        </div>
      </fieldset>
    </form>
  </div>
  <?php } ?>
</div>

<?php if ($posts) { ?>
<div class="home-row hide">
<h2 class="carousel-title">
  <div class="show-feed">
    <?php if ($row_url == 'topics.php') { ?>
      <a href="topics.php">View all topics</a>
    <?php } elseif ($row_url) { ?>
      <a href="<?=$row_url?>">View all posts</a>
    <?php } elseif ($view_all) { ?>
      <a href="feed.php">View all unanswered questions</a>
    <?php } ?>
  </div>
  <?php if ($row_url) { ?>
    <a href="<?=$row_url?>">
  <?php } ?>
  <?=$row_title?>
  <?php if ($row_url) { ?>
    </a> <span class="context-text">Recent Posts</span>
  <?php } ?>
</h2>
<div class="carousel" data-slick='{"slidesToShow": 4, "slidesToScroll": 4}'>
  <?php foreach ($posts as $post) {   
$showBlock ="block";
if( Config::FLAG_QUESTIONABLE_ENABLE == 1 && ($userRole=='user'||$userRole=='') && ($post['flag_content'] == "reported" || $post['flag_content'] == "blocked")) //Check admin user
{ 
  $showBlock ="none";
}else{ 
?>
<a href="view.php?id=<?=$post['id']?>">
    <div class="card">
      <div class="content">
        <div class="author">
          <div class="post-header col-avatar small">
            <?php if ($post['anon'] == 1) { ?>
              <img class="avatar" src="img/anon.svg" alt="anonymous avatar">
            <?php } elseif ( (strpos($posts_authors[$post['author']]['avatar'], 'Object') == false) AND ($posts_authors[$post['author']]['avatar'] != NULL) ) { ?>
              <img class="avatar" src="" data-lazy="image.php?id=<?=$posts_authors[$post['author']]['avatar']?>&height=200" src="" onerror="this.src='img/robot.svg'" alt="author avatar">
            <?php } else { ?>
              <img class="avatar" src="img/robot.svg" alt="robot avatar">
            <?php } ?>
     	    </div>
          <div class="post-header">
            <div class="author-name">
              <?php if ($post['anon'] == 1) {
                echo "Anonymous";
              } else {
                echo $posts_authors[$post['author']]['firstName'] . " " . $posts_authors[$post['author']]['lastName'];
              } ?>
            </div>
            <div class="post-time" data-id="<?=$post['date']?>">
              <?=timestamp($post['date']);?>
            </div>
          </div>
        <?php  
        if( Config::FLAG_QUESTIONABLE_ENABLE == 1 && ($userRole=='admin' || $userRole=='super admin') && ($post['flag_content'] == "reported" || $post['flag_content'] == "unblocked"|| $post['flag_content'] == "blocked")) //Check admin user
          { 
            if($post['flag_content'] == "reported"){ 
                $cssStyle = "flag-reported";
                $statusContent ="Reported";
              }else if($post['flag_content'] == "unblocked"){ 
                $cssStyle = "flag-unblocked";
                $statusContent ="Unblocked";
              }else if($post['flag_content'] == "blocked"){ 
                $cssStyle = "flag-blocked";
                $statusContent ="Blocked";
              }
              ?>
              <div class="post-header right">                 
                  <div class="<?php echo $cssStyle; ?>">                
                    <div><?php echo $statusContent; ?></div>
                  </div>                  
              </div>
            <?php 
            }
            ?> 


        </div>
        <?php if ($post['featuredphoto']) { ?>
          <div class="img-preview img-featured img1of1">
            <img src="" data-lazy="image.php?id=<?=$post['featuredphoto']?>&height=400" alt="featured photo">
          </div>
        <?php } elseif (!$post['featuredphoto'] AND $post['youtube']) { ?>
          <div class="img-preview img-featured img1of1">
            <img src="" data-lazy="https://i.ytimg.com/vi/<?=$post['youtube']?>/hqdefault.jpg" alt="featured image preview">
          </div>
        <?php } ?>
        <div class="preview-content">
          <?=$post['text']?>
        </div>
      </div>
      <div class="bottom-bar">

        <div class="post-reactions">
          <?php if ($post['views'] > 0) { ?>
            <!-- <img class="icon-react" src="img/icon-views.svg"><div class="reaction-count"><?=$post['views']?></div> -->
          <?php } ?>
          <?php if ($post['comments'] > 0) { ?>
            <img class="icon-react" src="img/icon-comments.svg" alt="reaction icon comments"><div class="reaction-count"><?=$post['comments']?></div>
          <?php } ?>
          <?php if ($post['sameheres'] > 0) { ?>
            <img class="icon-react" src="img/icon-sameheres.svg" alt="reaction icon good question"><div class="reaction-count"><?=$post['sameheres']?></div>
          <?php } ?>
          <?php if ($post['helpfuls'] > 0) { ?>
            <img class="icon-react" src="img/icon-helpfuls.svg" alt="reaction icon helpful"><div class="reaction-count"><?=$post['helpfuls']?></div>
          <?php } ?>
          <?php if ($post['files'] > 0) { ?>
            <img class="icon-react" src="img/icon-files.svg" alt="icon files"><div class="reaction-count"><?=$post['files']?></div>
          <?php } ?>
        </div>
      </div>
    </div>
    </a>
<?php
}
?>   

<?php } ?>
</div>
<div class="feed-separator"></div>
</div>
<?php } ?>

<script type="text/javascript">
  jQuery(document).ready(function () {
    // Display options for search when search box focussed
    $(document).on('focus', '.text-search', function() {
      $('.search-dropdown').addClass("reveal");
    });
  });
</script>