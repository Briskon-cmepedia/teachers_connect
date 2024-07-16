<?php 
$showBlock ="block";
if( Config::FLAG_QUESTIONABLE_ENABLE == 1 && ($userRole=='user'||$userRole=='') && ($flag_content == "reported" || $flag_content == "blocked")) //Check admin user
{ 
  $showBlock ="none";
}else{ 
?>
  <div class="card<?php if ($post_author_trust == FALSE){echo ' moderated';}?>" data-id="<?=$post_id?>" data-type="post">
    	<div class="content">
        <?php if ($photos_count > 0) { // Display featured photo ?>
          <div class="img-preview">
            <img class="img-preview-featured" src="<?=$photos_display[0][3]?>">
          </div>
          <?php if ($photos_count > 1) { ?>
            <div class="thumbnails">
            <?php foreach ($photos_display as $photo) { // Display post photos ?>
              <div class="img-thumbnail">
                 <img src="<?=$photo[2]?>" id="<?=$photo[3]?>">
              </div>
            <?php } ?>
            </div>
         <?php } ?>
       <?php } ?>
       <?php if ($photos_count == 0 AND $youtube_video) { // Display first youtube link ?>
         <div class="video-embed">
           <iframe width="600" height="480" src="https://www.youtube.com/embed/<?=$youtube_video?>"></iframe>
         </div>
      <?php } ?>

      <?php if ($post_anon == 1) { // Mask author information if posted anonymously ?>
      		<div class="author">
            <div class="post-header col-avatar small">
      	       <img class="avatar anon" src="img/anon.svg" alt="anonymous avatar">
      	    </div>
            <div class="post-header">
      	       <div class="author-name anon">
                 Anonymous
               </div>
      	       <div class="post-time" data-id="<?=$post_time?>">
                 <?=timestamp($post_time);?>
               </div>
               <div class="post-time-edit">
                 <?php if ($post_edit_time) { echo "&nbsp;•&nbsp;Edited"; } ?>
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
                  <div class="<?php echo $cssStyle; ?>" style="margin-right: 60px;">                
                    <div><?php echo $statusContent; ?></div>
                  </div>
                  <a href="javascript:void(0)">
                  <div class="show-flag" data-id="p_<?=$post_id?>" style="margin-left: 150px;">
                    <img src="img/flag.png" class="content-flag-show"/>
                    <div class="dropdown-flag-content">
                      <?php                      
                      $classUn = "class='grayout'";
                      if($flag_content == "reported" || $flag_content == "unblocked" ){                         
                          $classUn = "class='edit-post-block'";
                      }                                          
                      $classBl = "class='grayout'";                     
                      if($flag_content == "reported" || $flag_content == "blocked" ){                         
                          $classBl = "class='edit-post-unblock'";
                      }
                      ?>                      
                      <div <?php echo $classUn;?>  data-id="<?=$post_id?>" data-type="block_post"> Block</div>
                      <div <?php echo $classBl;?>  data-id="<?=$post_id?>" data-type="unblock_post">Unblock</div>                      
                    </div>
                  </div>                  
                </a>
              </div>
            <?php 
            }else if(Config::FLAG_QUESTIONABLE_ENABLE == 1 && ($flag_content == "no"||$flag_content == ""||$flag_content == "unblocked")){ //Check admin user
            ?>
            <div class="post-header right">  
              <a href="#confirm-flag-quest"  class="confirm-flag-quest" rel="modal:open" data-id="<?=$post_id;?>" data-type="flag_post">
                <div class="button-delete">
                  <img src="img/flag.png" class="content-flag-show" style="margin-top:8px;"/>
                </div>
              </a>
            </div>
            <?php } ?>
            <?php if ($_SESSION['uid'] == $post_author) { ?>
              <div class="post-header right">
                <a href="javascript:void(0)">
                  <div class="dropdown">
                    <span class="text-options">Options</span> <img class="icon-button-arrow" src="img/arrow-down.svg" alt="icon. arrow down">
                      <div class="dropdown-content">
                        <a><div id="edit-post-box" class="first button-edit">Edit</div></a>
                        <a href="#confirm-delete" rel="modal:open"><div class="button-delete">Delete</div></a>
                      </div>
                  </div>
                </a>
              </div>
            <?php } ?>
          </div>
        <?php } else { // Display author information ?>

          <div class="author">
            <a href="profile.php?id=<?=$post_author?>">
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
              <div class="post-time-edit">
                <?php if ($post_edit_time) { echo "&nbsp;•&nbsp;Edited"; } ?>
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
              <div class="post-header right" >                 
                  <div class="<?php echo $cssStyle; ?>" style="margin-right: 60px;">                
                    <div><?php echo $statusContent; ?></div>
                  </div>
                  <a href="javascript:void(0)">
                  <div class="show-flag" data-id="p_<?=$post_id?>" style="margin-left: 150px;">
                    <img src="img/flag.png" class="content-flag-show"/>
                    <div class="dropdown-flag-content">
                      <?php                      
                      $classUn = "class='grayout'";
                      if($flag_content == "reported" || $flag_content == "unblocked" ){                         
                          $classUn = "class='edit-post-block'";
                      }                                          
                      $classBl = "class='grayout'";                     
                      if($flag_content == "reported" || $flag_content == "blocked" ){                         
                          $classBl = "class='edit-post-unblock'";
                      }
                      ?>                      
                      <div <?php echo $classUn;?>  data-id="<?=$post_id?>" data-type="block_post"> Block</div>
                      <div <?php echo $classBl;?>  data-id="<?=$post_id?>" data-type="unblock_post">Unblock</div>                      
                    </div>
                  </div>                  
                </a>
              </div>
            <?php 
            }else if(Config::FLAG_QUESTIONABLE_ENABLE == 1 && ($flag_content == "no"|| $flag_content == "" ||$flag_content == "unblocked")){ //Check admin user
            ?>
            <div class="post-header right">  
              <a href="#confirm-flag-quest" rel="modal:open"  class="confirm-flag-quest" data-id="<?=$post_id;?>" data-type="flag_post">
                <div class="button-delete">
                  <img src="img/flag.png" class="content-flag-show" style="margin-top:8px;"/>
                </div>
              </a>
            </div>
            <?php } ?>
            </a>
            <?php if ($_SESSION['uid'] == $post_author) { ?>
              <div class="post-header right">
                <!-- <a href="javascript:void(0)"> -->
                  <div class="dropdown">
                    <span class="text-options">Options</span> <img class="icon-button-arrow" src="img/arrow-down.svg" alt="icon. arrow down">
                      <div class="dropdown-content">
                        <a><div id="edit-post-box" class="first button-edit">Edit</div></a>
                        <a href="#confirm-delete" rel="modal:open"><div class="button-delete">Delete</div></a>
                      </div>
                  </div>
                <!-- </a> -->
              </div>
            <?php } ?>
          </div>
        <?php } ?>
            <div class="post-content">
              <?=nl2br($post_content)?>
            </div>
            <?php if ($_SESSION['uid'] == $post_author) { // if post author, include edit post textarea ?>
              <div class="edit-post-box">
                <form id="edit-post-form" class="submit-once" method="post" action="post.php">
                  <input type="hidden" name="action" value="edit">
                  <input type="hidden" name="pid" value="<?=$post_id?>">
                  <div class="comment-text">
                    <textarea id="edit-post-textarea" name="text"><?=nl2br($post_content)?></textarea>
                    <trix-editor id="trix-edit-post-textarea" input="edit-post-textarea" placeholder="<?=$placeholder?>"></trix-editor>
                  </div>
                  <div class="new-post-bar">
                    <div class="post-instructions">Only text can be updated when editing a post/question.</div>
                    <div class="right">
                      <input class="button-edit-post-cancel button-secondary" value="Cancel" type="button">
                      <input type="submit" class="button-comment" value="Update">
                    </div>
                  </div>
                </form>
              </div>
            <?php } ?>
          </div>
          <?php if ($file_count > 0) { // Display post files ?>
            <div class="file-listing">
              <div class="file-listing-title">
                Attached files:
              </div>
            <?php foreach ($file_display as $file) {
              if ($file[3] > 1048576) {
                $filesize = ' <span class="filesize">(' . number_format((float)($file[3]/MB), 0, '.', '') . 'MB)</span>';
              } elseif ($file[3] > 0) {
                $filesize = ' <span class="filesize">(' . number_format((float)($file[3]/KB), 0, '.', '') . 'KB)</span>';
              } ?>
    			     <div class="file-listing-item" data-id="<?=$file[0]?>" data-name="<?=rawurlencode($file[1])?>">
                 <img class="icon file" src="/img/icon-small-file-<?=$file[2]?>.png">
                 <?=ucfirst($file[1]) . $filesize?>
                 <a class="button right">Download</a>
               </div>

    				<?php } ?>
            </div>
          <?php } ?>
      		<div class="post-footer">
      		<?php if ($post_type == 'question') { // If post is a question ?>

      		    <a href="#comment" class="button btn-answer">Respond</a>
              <?php if ($post_followed == 1) { ?>
                <a class="post-follow button-secondary" id="follow-<?=$post_id?>">Unfollow</a>
              <?php } else { ?>
                <a class="post-follow button-secondary" id="follow-<?=$post_id?>">Follow</a>
              <?php } ?>
              <?php if ($_SESSION['uid'] == $post_author) { ?>
                <div class="post-reactions">
                  <img class="button-react" src="img/metoo-blue.svg"><div class="reaction-count" id="react-<?=$post_id?>"><?=$post_metoo_count?></div>
                </div>
              <?php } else { ?>
        			  <div class="post-reactions react-button">
                  <img class="button-react" src="img/metoo.svg" alt="reaction button good question"><div class="reaction-count" id="react-<?=$post_id?>"><?=$post_metoo_count?></div>
                </div>
              <?php } ?>

          <?php } else { // If post is not a question ?>

            <a href="#comment" class="button btn-answer">Comment</a>
            <?php if ($post_followed == 1) { ?>
              <a class="post-follow button-secondary" id="follow-<?=$post_id?>">Unfollow</a>
            <?php } else { ?>
              <a class="post-follow button-secondary" id="follow-<?=$post_id?>">Follow</a>
            <?php } ?>
            <?php if ($_SESSION['uid'] == $post_author) { ?>
              <div class="post-reactions">
                <img class="button-react" src="img/helpful-blue.svg"><div class="reaction-count" id="react-<?=$post_id?>"><?=$post_helpful_count?></div>
         			</div>
            <?php } else { ?>
              <div class="post-reactions react-button">
                <img class="button-react" src="img/helpful.svg" alt="reaction button helpuful"><div class="reaction-count" id="react-<?=$post_id?>"><?=$post_helpful_count?></div>
         			</div>
            <?php } ?>

      		<?php } ?>
      		</div>
      </div>
      <div class="private-post-id">
        <?php if ($post_audience) { ?>
        Posted to <a href="feed.php?id=<?=$_SESSION['accessibleGroupNames'][$post_audience]['id']?>"><?=$_SESSION['accessibleGroupNames'][$post_audience]['name']?> Community</a>
        <?php } ?>
        <?php if ($post_author_trust == FALSE AND $post_author_violations == 1) { ?>
          There is a problem with your account that may reduce the visibility of your posts to the community. <a href="<?=site_url()?>/violations.php">Please resolve this problem by clicking here.</a>
        <?php } elseif ($post_author_trust == FALSE) { ?>
          It looks like this is one of your first contributions. Thanks! Please give us a few days to verify you’re not a spammer before your contributions will appear to others.
        <?php } elseif ($_SESSION['uid'] == $post_author) { ?>
        <div class="post-metadata">Viewed <?=$post_views?> times</div>
        <?php } ?>
      </div>
<?php
}

include_once("flag-content.php");
?>        
