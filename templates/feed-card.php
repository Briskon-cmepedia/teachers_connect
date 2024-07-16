<?php 
$showBlock ="block";
if( Config::FLAG_QUESTIONABLE_ENABLE == 1 && ($userRole=='user'||$userRole=='') && ($flag_content == "reported" || $flag_content == "blocked")) //Check admin user
{ 
  $showBlock ="none";
}else{
?>
<div  class="card grid-item<?php if ($post_author_trust == FALSE){echo ' moderated';}?>" data-id="<?=$post_id?>" data-type="post">
 <div class="content">
    <?php if ($post_photos_count > 0) { // Display post photos

      $i = 1;
      foreach ($post_photos as $post_photo) { ?>
        <div class="img-preview <?php echo 'img' . $i . 'of' . $post_photos_count; ?>">
          <img src="image.php?id=<?=$post_photo?>&height=400" alt="featured image preview">
        </div>
      <?php
          $i++;
      }
    }

    if ($post_photos_count == 0 AND $youtube_video) { // Display image preview from first youtube link (if any) ?>

      <div class="img-preview img1of1">
        <img src="https://i.ytimg.com/vi/<?=$youtube_video?>/hqdefault.jpg" alt="youtube link image preview">
      </div>
    <?php }

    if ($post_files_count > 0) { // Display file count ?>
      <div class="file-attachment-count"><img class="icon" src="img/icon-small-file-paperclip.svg" alt="icon files"> <?=$post_files_count?> File<?php if ($post_files_count > 1) { echo 's'; } ?> Attached</div>
    <?php }

    if ($post_anon == 1) { // Mask author information if posted anonymously ?>
      <div class="author">
        <div class="post-header col-avatar small">
            <img class="avatar" src="img/anon.svg" alt="anonymous avatar">
        </div>
        <div class="post-header">
            <div class="author-name">
              Anonymous
            </div>
            <div class="post-time" data-id="<?=$post_time?>">
              <?=timestamp($post_time);?>
            </div>
        </div>
        <?php 
        if( Config::FLAG_QUESTIONABLE_ENABLE == 1 && ($userRole=='admin' || $userRole=='super admin') && ($flag_content == "reported" || $flag_content == "unblocked"|| $flag_content == "blocked")) //Check admin user
          { 
            if($flag_content == "reported"){ 
                $cssStyle = "flag-reported";
                $statusContent ="Reported";
              }else if($flag_content == "unblocked"){ 
                $cssStyle = "flag-unblocked";
                $statusContent ="Unblocked";
              }else if($flag_content == "blocked"){ 
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
    <?php } else { // Display author information ?>
      <div class="author">
        <div class="post-header col-avatar small">
            <?php if ( (strpos($post_author_avatar, 'Object') == false) AND ($post_author_avatar != NULL) ) { ?>
            <img class="avatar" alt="avatar" src="image.php?id=<?=$post_author_avatar?>&height=200" onerror="this.src='img/robot.svg'">
          <?php } else { ?>
            <img class="avatar" src="img/robot.svg" alt="robot avatar">
          <?php } ?>
        </div>
        <div class="post-header">
          <div class="author-name">
            <?=$post_author_fullname?>
          </div>
          <div class="post-time" data-id="<?=$post_time?>">
            <?=timestamp($post_time);?>
          </div>
        </div>        
        <?php 
        if( Config::FLAG_QUESTIONABLE_ENABLE == 1 && ($userRole=='admin' || $userRole=='super admin') && ($flag_content == "reported" || $flag_content == "unblocked"|| $flag_content == "blocked")) //Check admin user
        { 
          if($flag_content == "reported"){ 
            $cssStyle = "flag-reported";
            $statusContent ="Reported";
          }else if($flag_content == "unblocked"){ 
            $cssStyle = "flag-unblocked";
            $statusContent ="Unblocked";
          }else if($flag_content == "blocked"){ 
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
          }?>         
      </div>
    <?php } ?>
        <div class="preview-content"><?=$preview_content?></div>
      </div>
      <?php if($notice_search_deeper) { ?>
        <div class="notice-search">
          <?=$notice_search_deeper?>
        </div>
      <?php } ?>

      <div class="post-footer">
      <?php if ($post_type == 'question') { // If post is a question ?>

        <?php if ($post_author_trust == TRUE){ ?>
          <?=$post_comment_count?> Response<?php if ($post_comment_count != 1) { ?>s<?php } ?>
        <?php } ?>

          <?php if ($_SESSION['uid'] == $post_author) { ?>
            <?php if ($my_posts_feed == 1 AND $post_author_trust == TRUE) { ?>
            - <?=$post_views?>
            <?php } ?>
            <div class="post-reactions">
              <img class="button-react" src="img/metoo-blue.svg" alt="reaction button good question"><div class="reaction-count" id="react-<?=$post_id?>"><?=$post_metoo_count?></div>
            </div>

          <?php } else { ?>

            <div class="post-reactions react-button">
              <img class="button-react" src="img/metoo.svg" alt="reaction button good question"><div class="reaction-count" id="react-<?=$post_id?>"><?=$post_metoo_count?></div>
            </div>

          <?php } ?>

      <?php } else { // If post is not a question ?>

        <?php if ($post_author_trust == TRUE){ ?>
          <?=$post_comment_count?> Comment<?php if ($post_comment_count != 1) { ?>s<?php } ?>
        <?php } ?>

        <?php if ($_SESSION['uid'] == $post_author) { ?>
          <?php if ($my_posts_feed == 1 AND $post_author_trust == TRUE) { ?>
          - <?=$post_views?>
          <?php } ?>
          <div class="post-reactions">
            <img class="button-react" src="img/helpful-blue.svg" alt="reaction button helpuful"><div class="reaction-count" id="react-<?=$post_id?>"><?=$post_helpful_count?></div>
          </div>
        <?php } else { ?>
          <div class="post-reactions react-button">
            <img class="button-react" src="img/helpful.svg" alt="reaction button helpuful"><div class="reaction-count" id="react-<?=$post_id?>"><?=$post_helpful_count?></div>
          </div>
        <?php } ?>

      <?php } ?>
      </div>

  </div>
<!-- </a> -->
<?php
}
?>

<?php
include_once("flag-content.php");
?>        
