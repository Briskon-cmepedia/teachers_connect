<?php if($alert_message=='flag-post'){?>  
<div class="profile-body">
  <div class="user-profile-edit">
    <div class="page-title no-head">
      <h1> Post does not exist</h1>
    </div>
  </div>
</div>   
<?php
}else{
  ?>
  <?php if ($post_type=='question') {
  $submit_button = 'Respond';
  $placeholder = 'Respond to this question';
  $response_type = 'response';
} else {
  $submit_button = 'Post';
  $placeholder = 'Comment on this post';
  $response_type = 'comment';
}
if ( ($post_author_original == $post_author) AND ($post_anon_original == 1) ) {
  $placeholder = $placeholder . ' anonymously';
} ?>
<div class="clear">
  &nbsp;
</div>
<div class="comment-box">
  <a name="comment"></a>
  <form id="new-comment-form" class="form-container submit-once" method="post" action="post.php" enctype="multipart/form-data">
    <input type="hidden" name="pid" value="<?=$post_id?>">
    <div class="comment-text">
      <textarea id="new-comment-textarea" name="text" placeholder="<?=$placeholder?>"></textarea>
      <trix-editor id="trix-new-comment-textarea" input="new-comment-textarea" placeholder="<?=$placeholder?>"></trix-editor>
    </div>
    <div class="new-post-bar">
      <div class="right">
        <div class="bt-spinner hide"></div>
        <input type="submit" class="button-comment" value="<?=$submit_button?>">
      </div>
      <div class="question-buttons">
        <img id="comment-button-attach" alt="Files" src="img/icon-attach.svg">
      </div>
      <div class="comment-attachments">
        You may attach up to four files to your <?=$response_type?>. <button class="modal-filetypes" type="button">What file types can I attach?</button>
        <div class="image-preview-container inline attachments">
          <div class="image-preview">
            <label for="FileUpload9"><span hidden>File upload and preview 9</span><div class="preview" id="FileUpload9-preview">&nbsp;</div></label>
            <input id="FileUpload9" type="file" name="file5" />
          </div>
          <div class="image-preview">
            <label for="FileUpload10"><span hidden>File upload and preview 10</span><div class="preview" id="FileUpload10-preview"></div></label>
            <input id="FileUpload10" type="file" name="file6" />
          </div>
          <div class="image-preview">
            <label for="FileUpload11"><span hidden>File upload and preview 11</span><div class="preview" id="FileUpload11-preview"></div></label>
            <input id="FileUpload11" type="file" name="file7" />
          </div>
          <div class="image-preview">
            <label for="FileUpload12"><span hidden>File upload and preview 12</span><div class="preview" id="FileUpload12-preview"></div></label>
            <input id="FileUpload12" type="file" name="file8" />
          </div>
        </div>
      </div>
    </div>
  </form>
</div>

  <?php
}
?>




