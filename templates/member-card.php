
    <div class="card grid-item" data-id="<?=$user_id?>">
    	<div class="content">
          <div class="author">
            <div class="table-cell w80">
              <div class="medium">
                 <?php if ( (strpos($user_avatar, 'Object') == false) AND ($user_avatar != NULL) ) { ?>
        	       <img class="avatar" alt="avatar" src="image.php?id=<?=$user_avatar?>&width=200" onerror="this.src='img/robot.svg'"> 
               <?php } else { ?>
                 <img class="avatar" src="img/robot.svg" alt="robot avatar">
               <?php } ?>
        	    </div>
            </div>
            <div class="table-cell">
              <div class="author-name">
                <?=$user_fullname?>
              </div>
              <div class="author-bio">
                <?=$preview_bio?>
              </div>
            </div>
          </div>

      </div>
  </div>
