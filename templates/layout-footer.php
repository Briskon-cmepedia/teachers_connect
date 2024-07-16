      <?php if ($page == 'feed' OR $page == 'members' OR $page == 'posts' OR $page == 'following' OR $page == 'followers' OR $page == 'notifications') { // #feed-view ?>
        </div>
      <?php } ?>

      <?php if ($page == 'feed' OR $page == 'view' OR $page == 'members' OR $page == 'posts' OR $page == 'following' OR $page == 'followers' OR $page == 'notifications') { // .single-post ?>
        </div>
      <?php } ?>

      <?php if ($page == 'feed' OR $page == 'members' OR $page == 'posts' OR $page == 'following' OR $page == 'followers' OR $page == 'notifications' OR $view == 'inbox') {  ?>

        <div id="pagination">

          <?php if ( ($page != 'notifications' AND $total_count == 30) OR ($page == 'notifications' AND $total_count == 20) OR ($view == 'inbox' AND $total_count == 10) ) { ?>
            <a class="show-older-link" href="<?=$url_prev?>"><div class="show-older-container"><img class="icon-button-arrow icon-left svg" src="img/arrow-down.svg" alt="icon. arrow down"> Show Previous</div></a>
          <?php } ?>

          <?php if ($current_page >= 2) { ?>
            <a class="show-newer-link" href="<?=$url_next?>"><div class="show-newer-container">Show Next <img class="icon-button-arrow icon-right svg" src="img/arrow-down.svg" alt="icon. arrow down"></div></a><div class="clear"></div>
          <?php } ?>

        </div>
        <div class="clear"></div>
      <?php } ?>


      <?php if ($page == 'members' AND !$community_admin) { ?>
      <div class="invite-colleague">
        <div class="invite-cta">Can't find who you are looking for?</div>
        <a href="mailto:email@example.com?subject=Join%20me%20on%20TeachersConnect&body=Hey%2C%0D%0DI%20thought%20you%20might%20want%20to%20check%20out%20TeachersConnect%2E%0DIt%20is%20a%20free%20online%20community%20of%20teachers%20who%20share%20ideas%2C%20answer%20questions%2C%20and%20collaborate%20through%20group%20discussions%2E%0DJoin%20me%20there%20and%20we%20can%20connect%21%0D%0DJoin%20for%20free%3A%20http%3A%2F%2Fwww%2Eteachersconnect%2Eonline"><div class="button">Invite a Colleague</div></a>
      </div>
      <?php } ?>

    </div>

    <?php if ($page != 'messages') { ?>
    <div id="site-footer" class="site-footer hide">
      <ul class="footer-menu">
        <li><a target="_blank" rel="noopener" href="<?php echo Config::MARKETING_URL; ?>/terms-of-use/">Terms of Use</a></li>
        <li><a target="_blank" rel="noopener" href="<?php echo Config::MARKETING_URL; ?>/privacy-policy/">Privacy Policy</a></li>
        <li><a target="_blank" rel="noopener" href="<?php echo Config::MARKETING_URL; ?>/2017/08/10/community-guidelines/">Community Guidelines</a></li>
        <li><a rel="noopener" href="/tour/menu.php">Guided Tour</a></li>
        <li><a target="_blank" rel="noopener" href="<?php echo Config::MARKETING_URL; ?>/support/">Support</a></li>
        <li><a rel="noopener" href="<?=site_url()?>/auth.php?logout=1">Logout</a></li>
      </ul>
      <div class="footer-note">
        <div style="float: left;  width:33.33333%;  text-align:left; margin-left:20px">
          <img alt="Teachers Connect logo" style="width:280px;height:47px;" src="img/TeachersConnect_logo.svg"></div>
        <div style="float: left; width:33.33333%;  text-align:center;">&nbsp;</div>
        <div style="float: right; width:33.33333%; text-align:right; margin-right:20px; color: #4C3F06;">© Copyright <?php echo date("Y");?> Public Consulting Group <br/>148 State St. 10<sup>th</sup> Floor Boston, MA 02109 
        </div>
      </div>     
    </div>
    <?php } ?>

  <div id="alert-file-unsupported" class="modal hide">
    <div class="modal-text">
      <div class="modal-title">What file types can I attach?</div>
      <ul>
        <li>Documents (pdf, doc, docx, pages)</li>
        <li>Presentations (ppt, pptx, keynote)</li>
        <li>Spreadsheets (xls, xlsx, numbers)</li>
        <li>Images (jpeg, jpg, png, gif, bmp)</li>
      </ul>
      <div class="note note-modal">Having problems uploading Pages, Numbers and Keynote files? Open and save them in the latest version of that software before uploading.</div>
    </div>
    <div class="modal-actions">
      <button class="modal-button modal-button-outline">Continue</button>
    </div>
    <div class="clear">
      &nbsp;
    </div>
  </div>

  <!-- <script type="text/javascript" src="js/jquery-3.2.1.min.js"></script> -->
  <script type="text/javascript" src="js/jquery.modal.min.js" async></script>
  <link rel="stylesheet" type="text/css" href="css/jquery.modal.min.css">
  <link rel="stylesheet" type="text/css" href="css/trix.css">
  <link rel="stylesheet" type="text/css" href="css/animate.css">
  <link rel="stylesheet" type="text/css" href="css/styles.css">
  <?php if ($page == 'profile' AND $action) { ?>
    <link rel="stylesheet" type="text/css" href="css/selectize.custom.css">
  <?php } ?>
  <!-- <script type="text/javascript" src="js/jquery.sidr.min.js"></script> -->
  <!-- <script type="text/javascript" src="js/jquery.touchSwipe.min.js"></script> -->
  <script type="text/javascript" src="js/linkify.min.js"></script>
  <script type="text/javascript" src="js/linkify-jquery.min.js"></script>
  <script type="text/javascript" src="js/linkify-plugin-hashtag.min.js"></script>
  <script type="text/javascript" src="js/jquery.mark.min.js"></script>
  <script type="text/javascript" src="js/moment.min.js"></script>
  <script type="text/javascript" src="js/trix.js" async></script>

  <?php if ($community_admin == 1 AND $page == 'members') { ?>
    <link rel="stylesheet" type="text/css" href="css/bootstrapcustom.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs4/dt-1.10.16/b-1.5.1/b-html5-1.5.1/b-print-1.5.1/fh-3.1.3/r-2.2.1/datatables.min.css"/>
    <link type="text/css" href="//gyrocode.github.io/jquery-datatables-checkboxes/1.2.10/css/dataTables.checkboxes.css" rel="stylesheet"/>
    <script type="text/javascript" src="https://cdn.datatables.net/v/bs4/dt-1.10.16/b-1.5.1/b-html5-1.5.1/b-print-1.5.1/fh-3.1.3/r-2.2.1/datatables.min.js"></script>
    <script type="text/javascript" src="//gyrocode.github.io/jquery-datatables-checkboxes/1.2.10/js/dataTables.checkboxes.min.js"></script>
    <script type="text/javascript">
      jQuery(document).ready(function () {
        var tableSelect = $('.table-select').DataTable({
         fixedHeader: true,
         responsive: false,
         dom: 'iBfptlp',
         buttons: [ 'csv', 'print' ],
         bAutoWidth: false,
         'columnDefs': [
          {
            'targets': 0,
            'checkboxes': {
              'selectRow': true
            }
          }
         ],
         'select': {
            'style': 'multi'
         }
       });
       // Only show the Remove button on community members table when rows selected
        $('#button-member-remove').hide();
        $(document).on('change', '#table-community-members', function(){
          var rows_selected = tableSelect.column(0).checkboxes.selected();
          var count = rows_selected.length;
          if (count > 0) {
            $('#button-member-remove').show();
          } else {
            $('#button-member-remove').hide();
          }
        });
        // Handle form submission event
         $('#table-community-members').on('submit', function(e){
            var form = this;
            var rows_selected = tableSelect.column(0).checkboxes.selected();

            // Iterate over all selected checkboxes
            $.each(rows_selected, function(index, rowId){
               // Create a hidden element
               $(form).append(
                   $('<input>')
                      .attr('type', 'hidden')
                      .attr('name', 'id[]')
                      .val(rowId)
               );
            });
        });
       });
     </script>
  <?php } ?>

  <?php if ($page == 'home' AND $search_focus) { ?>

    <script type="text/javascript">
    jQuery(document).ready(function () {
      $("#text-search").focus();
    });
    </script>

  <?php } ?>

  <?php if ($page == 'home') { ?>

    <script type="text/javascript">
    jQuery(document).ready(function () {
      // $(".home-row-top").show();
      $(".home-row").fadeIn();
    });
    </script>

  <?php } ?>

  <script type="text/javascript">

  // Add browser version to body metadata (primarily to target IE)
  var doc = document.documentElement;
  doc.setAttribute('data-useragent', navigator.userAgent);

  // Extend Jquery with animate.css end animation function
  $.fn.extend({
      animateCss: function(animationName, callback) {
        var animationEnd = (function(el) {
          var animations = {
            animation: 'animationend',
            OAnimation: 'oAnimationEnd',
            MozAnimation: 'mozAnimationEnd',
            WebkitAnimation: 'webkitAnimationEnd',
          };

          for (var t in animations) {
            if (el.style[t] !== undefined) {
              return animations[t];
            }
          }
        })(document.createElement('div'));

        this.addClass('animated ' + animationName).one(animationEnd, function() {
          $(this).removeClass('animated ' + animationName);

          if (typeof callback === 'function') callback();
        });

        return this;
      },
    });

    var notificationCount = '<?=get_notifications_count();?>';
    if (notificationCount > 99) {
      var notificationCount = 99;
    }
    if (notificationCount > 0) {
      $(".notification-count").text( notificationCount );
      // $("#alert-number").text( notificationCount );
      $(".notification-count").show();
      // $(".tile-notification-count").addClass('animated infinite pulse tile-notification-active');
      $(".notification-count").addClass('animated infinite pulse');
      // $("#alert-number").addClass('alert-number');
    }

    var conversationCount = '<?=get_conversations_count();?>';
    if (conversationCount > 99) {
      var conversationCount = 99;
    }
    if (conversationCount > 0) {
      $(".conversation-count").text( conversationCount );
      $(".conversation-count").show();
      $(".conversation-count").addClass('animated infinite pulse');
    }


    // Parse variables from urls
    function getSearchParams(k){
     var p={};
     location.search.replace(/[?&]+([^=&]+)=([^&]*)/gi,function(s,k,v){p[k]=v})
     return k?p[k]:p;
    }

    // Update post reaction counts when clicked
    $(document).on('click', '.react-button', function() {
      $(this).animateCss('rubberBand', function() {
        $(this).removeClass('rubberBand');
      });

      var postId = $(this).closest(".card").attr("data-id");
      var postType = $(this).closest(".card").attr("data-type");
      var reactCount = parseInt($("#react-" + postId).text());
      var userId = "<?=$_SESSION['uid']?>";
      var url = "react.php";
      var pushData = { "pid": postId, "uid": userId, "pt": postType };
      $.post("react.php", pushData).done(function( data ) {

        if (data != 0) {

          $("#react-" + postId).text(data);

        } else {

          $("#react-" + postId).text("0");

        }

      });

    });

    // Update post follow status when clicked
    $(document).on('click', '.post-follow', function() {

      var postId = $(this).closest(".card").attr("data-id");
      var reactCount = parseInt($("#follow-" + postId).text());
      var userId = "<?=$_SESSION['uid']?>";
      var url = "react.php";
      var pushData = { "pid": postId, "uid": userId, "pt": "followpost" };
      $.post("react.php", pushData).done(function( data ) {

        if (data != 0) {

          $("#follow-" + postId).text(data);

        }

      });

    });

    // Update community follow status when clicked
    $(document).on('click', '.community-follow', function() {

      var commId = $(this).attr("data-id");
      var userId = "<?=$_SESSION['uid']?>";
      var url = "react.php";
      var pushData = { "pid": commId, "uid": userId, "pt": "followcommunity" };
      $.post("react.php", pushData).done(function( data ) {

        if (data != 0) {

          $("#follow-" + commId).text(data);
          if (data == 'Follow') {
            $("#follow-" + commId).addClass('button').removeClass('button-secondary');
          } else {
            $("#follow-" + commId).addClass('button-secondary').removeClass('button');
          }

        }

      });

    });

    // Update topic follow status when clicked
    $(document).on('click', '.topic-follow', function() {

      var commId = $(this).attr("data-id");
      var domId = commId.replace(/\s+/g, '-').toLowerCase();
      var userId = "<?=$_SESSION['uid']?>";
      var url = "react.php";
      var pushData = { "pid": commId, "uid": userId, "pt": "followtopic" };
      $.post("react.php", pushData).done(function( data ) {

        if (data != 0) {

          $("#topic-follow-" + domId).text(data);
          if (data == 'Follow') {
            $("#topic-follow-" + domId).addClass('button').removeClass('button-secondary');
          } else {
            $("#topic-follow-" + domId).addClass('button-secondary').removeClass('button');
          }

        }

      });

    });

    // Display/hide options for search when search box focussed
    $(document).on('focus', '#text-search', function() {
      $('.search-dropdown').addClass("reveal");
    });
    $(document).on('focus', '#text-search-top', function() {
      $('.search-dropdown-top').addClass("reveal");
    });

    // Change search form target page dependent on user selection
    $("#search-dropdown-option-contributions, #search-dropdown-option-members, #search-mobile-option-contributions, #search-mobile-option-members").change(function() {
      var selected = $(this).val();
      switch (selected) {
        case "contributions":
        $(".site-search").attr('action', 'feed.php');
        $('#search-dropdown-option-contributions, #search-mobile-option-contributions').prop('checked', true);
        break;
        case "members":
        $(".site-search").attr('action', 'members.php');
        $('#search-dropdown-option-members, #search-mobile-option-members').prop('checked', true);
        break;
      }
    });

    $("#search-dropdown-top-option-contributions, #search-dropdown-top-option-members, #search-mobile-option-contributions, #search-mobile-option-members").change(function() {
      var selected = $(this).val();
      switch (selected) {
        case "contributions":
        $(".site-search-top").attr('action', 'feed.php');
        $('#search-dropdown-top-option-contributions, #search-mobile-option-contributions').prop('checked', true);
        break;
        case "members":
        $(".site-search-top").attr('action', 'members.php');
        $('#search-dropdown-top-option-members, #search-mobile-option-members').prop('checked', true);
        break;
      }
    });

    // Submit form only once
    $('form.submit-once').on('submit', function(e){
      if( $(this).hasClass('form-submitted') ){
        e.preventDefault();
        return;
      }
      $(this).addClass('form-submitted');
    });

    // Go back one page when pressing Cancel button
    $('.button-cancel').on('click', function(e){
      history.go(-1);
      return false;
    });

    // Display options menu for users (posts/profile)
    $(document).on('click', '.dropdown, .community-dropdown, .sort-dropdown', function() {
      $(this).find('.dropdown-content').toggle();
      $(this).toggleClass("dropdown-active");
    });

     $(document).on('click', '.show-flag', function() {
      $(this).find('.dropdown-flag-content').toggle();
      $(this).toggleClass("dropdown-flag-active");
    });

    // Display post edit box for authors
    $(document).on('click', '#edit-post-box', function() {
      $(".post-content").toggle();
      $(".edit-post-box").toggle();
    });

    // Hide edit box when pressing Cancel on post edit view
    $(document).on('click', '.button-edit-post-cancel', function() {
      $(".post-content").toggle();
      $(".edit-post-box").toggle();
    });

    // Display comment edit box for authors
    $(document).on('click', '#edit-comment-box', function() {
      $(this).closest('.card').find(".comment-content").toggle();
      $(this).closest('.card').find(".edit-comment-box").toggle();
    });

    // Hide edit box when pressing Cancel on comment edit view
    $(document).on('click', '.button-edit-comment-cancel', function() {
      $(this).closest('.card').find(".comment-content").toggle();
      $(this).closest('.card').find(".edit-comment-box").toggle();
    });

    // Push process data to "confirm delete" button
    $(document).on('click', '.button-delete', function() {
      <?php if ($page == 'profile' OR $page == 'messages') { ?>
      var postId = $(this).attr('data-id');
      var postType = $(this).attr('data-type');
      <?php } else { ?>
      var postId = $(this).closest('.card').attr('data-id');
      var postType = $(this).closest('.card').attr('data-type');
      <?php } ?>
      $('#confirm-delete-button').attr('data-id', postId);
      $('#confirm-delete-button').attr('data-type', postType);
    });
    $(document).on('click', '.button-leave', function() {
      var postId = $(this).attr('data-id');
      var postType = $(this).attr('data-type');
      $('#confirm-leave-button').attr('data-id', postId);
      $('#confirm-leave-button').attr('data-type', postType);
    });

 // Push delete data for processing
    $(document).on('click', '#confirm-delete-button, #confirm-leave-button', function() {
      window.location = "<?php site_url();?>/tc_app/process.php?type=" + $(this).attr('data-type') + "&id=" + $(this).attr('data-id');
    });

    //Back to browsing
    $(document).on('click', '.flag-refresh', function() {
      var type = $('#content_type').val();     
      var postId = $('#content_id').val();
      if(type=='flag_comment'){
        var ids = postId.split("_");   
        id = ids[1];
      }else{
        id = postId;
      }         
      if(type=='flag_post'){
        window.history.back()
      }else{
        window.location = "<?php site_url();?>/view.php?id=" +id ;
      }
     
    });    

    //Update flag
    $(document).on('click', '.edit-post-unblock, .edit-post-block, .edit-comment-block, .edit-comment-unblock', function() {
      window.location = "<?php site_url();?>/process.php?type=" + $(this).attr('data-type') + "&id=" + $(this).attr('data-id');
    });

    $(document).on('click', '#confirm-flag-button1', function() {       
      window.location = "<?php site_url();?>/process.php?type=" + $(this).attr('data-type') + "&id=" + $(this).attr('data-id');
    });

   $(document).on('click', '#confirm-flag-button', function() {     
      $.ajax({
              url: '<?php site_url();?>/process.php',
              type: 'GET',
              //dataType: 'json',
              data: {
                  'type': $('#content_type').val(),
                  'id': $('#content_id').val()                        
              },
              error: function() {
                  callback();
              },
              success: function(result) {
                  $('.submit-flag').hide();
                  $('#show_message').show();        
                  $( ".close-modal" ).addClass("flag-refresh" );
          
              }
          });
    });
    
    $(document).on('click', '.confirm-flag-quest', function() {  
      $('#content_type').val($(this).attr('data-type'));
      $('#content_id').val($(this).attr('data-id')); 
      $('.submit-flag').show();
      $('#show_message').hide();
    });

    // Display thumbnail image as featured image
    $(document).on('click', '.img-thumbnail img', function() {
      // var imageUrl = 'image.php?id=' + $(this).attr('id');
      var imageUrl = $(this).attr('id');
      var imageSrc = $(this).closest('.content').find('.img-preview-featured').attr('src');
      var featuredImage = $(this).closest('.content').find('.img-preview-featured');

      if (imageSrc != imageUrl) {
        $(featuredImage).fadeOut(200, function() {
          $(featuredImage).attr('src', imageUrl);
          $(featuredImage).fadeIn(200);
        });
      }
    });

    // Ignore clicks on featured preview images (for now)
    $(document).on('click', '.img-preview a', function(e) {
      e.preventDefault();
    });

    // Download files from posts
    $(document).on('click', '.file-listing-item', function(e) {
      window.location = "<?php site_url();?>/file.php?name=" + $(this).attr('data-name') + "&id=" + $(this).attr('data-id');
    });

    // Knock to enter private community
    $(document).on('click', '#private-community-knock', function(e) {
      window.location = "<?php site_url();?>/edit-affiliate.php?knock=1&id=" + $(this).attr('data-id');
    });

    // Focus on textarea when answer button clicked
    $(document).on('click', '.btn-answer', function(e) {
      e.preventDefault();
      $("#trix-new-comment-textarea").focus();
      $('html, body').animate({
        scrollTop: $("#trix-new-comment-textarea").offset().top-70
    }, 0);
    });

    // Display supported file types on attach tab when creating posts/questions
    $(document).on('click', '.modal-filetypes', function(e) {
      $('#alert-file-unsupported').modal();
    });

    // Display dropdown options when clicking on Contribute button
    $(document).on('click', '#button-contribute', function(e) {
      $('.contribute-dropdown').toggleClass('reveal');
    });

    // Show preview of images to be uploaded on posts
    $(document).on('change', '#FileUpload1, #FileUpload2, #FileUpload3, #FileUpload4, #FileUpload5, #FileUpload6, #FileUpload7, #FileUpload8, #FileUpload9, #FileUpload10, #FileUpload11, #FileUpload12', function(){
      var imagePreview = $("#"+$(this).attr('id')+"-preview");
      var files = !!this.files ? this.files : [];
      if (!files.length || !window.FileReader) return;

       var filename = $(this).val();
       var extension = filename.replace(/^.*\./, '');
       if (extension == filename) {
           extension = '';
       } else {
           extension = extension.toLowerCase();
       }

       switch (extension) {
           case 'jpg':
           case 'jpeg':
           case 'png':
           case 'gif':
           case 'bmp':
               var ReaderObj = new FileReader();
               ReaderObj.readAsDataURL(files[0]);
               ReaderObj.onloadend = function(){
                 imagePreview.css("background-image", "url("+this.result+")");
               };
               break;

          case 'pdf':
              imagePreview.css("background-image", "url(/img/icon-small-file-pdf.png)");
              break;

          case 'doc':
          case 'docx':
              imagePreview.css("background-image", "url(/img/icon-small-file-doc.png)");
              break;

          case 'ppt':
          case 'pptx':
              imagePreview.css("background-image", "url(/img/icon-small-file-ppt.png)");
              break;

          case 'xls':
          case 'xlsx':
              imagePreview.css("background-image", "url(/img/icon-small-file-xls.png)");
              break;

          case 'pages':
              imagePreview.css("background-image", "url(/img/icon-small-file-pages.png)");
              break;

          case 'key':
              imagePreview.css("background-image", "url(/img/icon-small-file-key.png)");
              break;

          case 'numbers':
              imagePreview.css("background-image", "url(/img/icon-small-file-numbers.png)");
              break;

          default:
              $('#alert-file-unsupported').modal();
              $(this).val("");
              break;
       }

    });

    // Activate label clicks on IE for profile picture upload functionality
    var ua = window.navigator.userAgent;
    var msie = ua.indexOf("MSIE ");
    var trident = ua.indexOf('Trident/');

    if (msie > 0 || trident > 0) // If Internet Explorer, return version number
    {
      $(document).on('click', 'label', function() {
      if ($(this).attr("for") != "")
        $("#" + $(this).attr("for")).click();
      });
    }

    $(document).on('click', '.edit-profile-pic', function(){
      $('#ProfilePic').trigger('click');
    });
    // Upload new picture when added to profile
    $(document).on('change', '#ProfilePic', function(){
      var files = !!this.files ? this.files : [];
      if (!files.length || !window.FileReader) return;
      if (/^image/.test( files[0].type)){
        $("#ProfilePicUpload")[0].submit();        
      } else {
        alert('You can only upload images');
        $(this).val("");
      }
    });

    // Stop attachments from happening in wysiwyg editor
    $(document).on('trix-attachment-add', function($event) {
      $event.originalEvent.attachment.remove();
    });
    $(document).on('trix-file-accept', function($event) {
      $event.preventDefault();
    });

    // Enable Save button on notification settings form change
    $(document).on('change', '#edit-notification-settings', function(){
      $('input[type="submit"]').prop('disabled', false);
    });


    jQuery(document).ready(function () {

      <?php if ($page != 'messages') { ?>
        $('#site-footer').show();
      <?php } ?>

      <?php if ($create_post) { ?>
        // Display Create New Post Onload
          $(".post-creator").addClass("reveal");
          $("#new-post").addClass("reveal");
          $(".post-audience").addClass("reveal");
          $("#post-button-audience").addClass("selected-post-tab");
          $(".post-audience").addClass("selected-corner");
          $('#post-button-audience').attr('src', "img/icon-audience-active.svg");
          $('#post-button-photos').attr('src', "img/icon-attach.svg");
          $('#post-audience-select').val('<?=$group_id?>');
          $("#trix-new-post-textarea").focus();
      <?php } ?>

      /*
     * Replace all SVG images with inline SVG
     */
      jQuery('img.svg').each(function(){
          var $img = jQuery(this);
          var imgID = $img.attr('id');
          var imgClass = $img.attr('class');
          var imgURL = $img.attr('src');

          jQuery.get(imgURL, function(data) {
              // Get the SVG tag, ignore the rest
              var $svg = jQuery(data).find('svg');

              // Add replaced image's ID to the new SVG
              if(typeof imgID !== 'undefined') {
                  $svg = $svg.attr('id', imgID);
              }
              // Add replaced image's classes to the new SVG
              if(typeof imgClass !== 'undefined') {
                  $svg = $svg.attr('class', imgClass+' replaced-svg');
              }

              // Remove any invalid XML tags as per http://validator.w3.org
              $svg = $svg.removeAttr('xmlns:a');

              // Replace image with new SVG
              $img.replaceWith($svg);

          }, 'xml');

      });

      // $(window).on('resize', function(){
      //   if($(this).width() > 2000){
      //     $.sidr('open', 'site-menu');
      //   } else {
      //     $.sidr('close', 'site-menu');
      //   }
      // });

      $(window).scroll(function(){
        $('#site-menu')[0].scrollTop=$(window).scrollTop();
      });

      // $('#top-bar-button-communities').sidr({
      //   name: 'site-menu',
      //   side: 'right',
      //   speed: 500,
      //   displace: false
      // });
      // if (msie > 0 || trident > 0) // If Internet Explorer, ignore touchSwipe
      // { } else {
      //   $('body').swipe( {
      //       swipeLeft: function () {
      //           $.sidr('open', 'site-menu');
      //       },
      //       swipeRight: function () {
      //           $.sidr('close', 'site-menu');
      //       },
      //       threshold: 175,
      //       fallbackToMouseEvents: false
      //   });
      // }
      $("#site-search-button").click(function() {
        // $.sidr('close', 'site-menu');
        $("#new-question").removeClass("reveal");
        $("#new-post").removeClass("reveal");
        $("#site-search-mobile").toggleClass("reveal");
        if ($("#text-search").is(":focus")) {
          $("#text-search").blur();
        } else {
          window.scrollTo(0, 0);
          $("#text-search").focus();
        }
      });
      $('#post-button-audience').click(function() {
        $(".post-audience").addClass("reveal");
        $(".post-attachments").removeClass("reveal");
        $("#post-button-audience").addClass("selected-post-tab");
        $("#post-button-photos").removeClass("selected-post-tab");
        $(".post-audience").addClass("selected-corner");
        $('#post-button-audience').attr('src', "img/icon-audience-active.svg");
        $('#post-button-photos').attr('src', "img/icon-attach.svg");
      });
      $('#post-button-photos').click(function() {
        $(".post-audience").removeClass("reveal");
        $(".post-attachments").addClass("reveal");
        $("#post-button-audience").removeClass("selected-post-tab");
        $("#post-button-photos").addClass("selected-post-tab");
        <?php if (!$_SESSION['partners']) { ?>
          $(".post-attachments").addClass("selected-corner");
        <?php } else { ?>
          $(".post-audience").removeClass("selected-corner");
        <?php  } ?>
        $('#post-button-audience').attr('src', "img/icon-audience.svg");
        $('#post-button-photos').attr('src', "img/icon-attach-active.svg");
      });
      $('#question-button-audience').click(function() {
        $(".question-audience").addClass("reveal");
        $(".question-attachments").removeClass("reveal");
        $(".question-anonymous").removeClass("reveal");
        $("#question-button-audience").addClass("selected-post-tab");
        $("#question-button-photos").removeClass("selected-post-tab");
        $("#question-button-anonymous").removeClass("selected-post-tab");
        $(".question-audience").addClass("selected-corner");
        $('#question-button-audience').attr('src', "img/icon-audience-active.svg");
        $('#question-button-photos').attr('src', "img/icon-attach.svg");
        $('#question-button-anonymous').attr('src', "img/icon-anonymous.svg");
      });
      $('#question-button-photos').click(function() {
        $(".question-audience").removeClass("reveal");
        $(".question-anonymous").removeClass("reveal");
        $(".question-attachments").addClass("reveal");
        $("#question-button-audience").removeClass("selected-post-tab");
        $("#question-button-photos").addClass("selected-post-tab");
        $("#question-button-anonymous").removeClass("selected-post-tab");
        <?php if (!$_SESSION['partners']) { ?>
          $(".question-attachments").addClass("selected-corner");
        <?php } else { ?>
          $(".question-audience").removeClass("selected-corner");
        <?php  } ?>
        $('#question-button-audience').attr('src', "img/icon-audience.svg");
        $('#question-button-photos').attr('src', "img/icon-attach-active.svg");
        $('#question-button-anonymous').attr('src', "img/icon-anonymous.svg");
      });
      $('#question-button-anonymous').click(function() {
        $(".question-audience").removeClass("reveal");
        $(".question-attachments").removeClass("reveal");
        $(".question-anonymous").addClass("reveal");
        $("#question-button-audience").removeClass("selected-post-tab");
        $("#question-button-photos").removeClass("selected-post-tab");
        $("#question-button-anonymous").addClass("selected-post-tab");
        <?php if (!$_SESSION['partners']) { ?>
          $(".question-attachments").removeClass("selected-corner");
        <?php } else { ?>
          $(".question-audience").removeClass("selected-corner");
        <?php  } ?>
        $('#question-button-audience').attr('src', "img/icon-audience.svg");
        $('#question-button-photos').attr('src', "img/icon-attach.svg");
        $('#question-button-anonymous').attr('src', "img/icon-anonymous-active.svg");
      });
      $('#comment-button-attach').click(function() {
        $(".comment-attachments").addClass("selected-corner");
        $("#comment-button-attach").addClass("selected-post-tab");
        $(".comment-attachments").addClass("reveal");
      });
      $("#button-new-post, .link-new-post").click(function() {
        // $.sidr('close', 'site-menu');
        // $('html, body').animate({ scrollTop: 0 }, 0);
        $("#site-search").removeClass("reveal");
        $('.contribute-dropdown').removeClass('reveal');
        $(".post-creator").addClass("reveal");
        $("#new-question").removeClass("reveal");
        $("#new-post").addClass("reveal");
        $("#trix-new-post-textarea").focus();
      });
      $("#button-new-question, .link-new-question").click(function() {
        // $.sidr('close', 'site-menu');
        // $('html, body').animate({ scrollTop: 0 }, 0);
        $("#site-search").removeClass("reveal");
        $('.contribute-dropdown').removeClass('reveal');
        $(".post-creator").addClass("reveal");
        $("#new-post").removeClass("reveal");
        $("#new-question").addClass("reveal");
        $("#trix-new-question-textarea").focus();
      });
      $("#contibute-modal-button").click(function() {
        $('#contribution-reminder-modal').modal('hide');
        $(".post-creator").addClass("reveal");
        $('.contribute-dropdown').removeClass('reveal');
        $("#new-question").addClass("reveal");
        $("#trix-new-question-textarea").focus();
      });
      $(".new-post-close").click(function() {
        $("#new-post, #new-question, .post-creator").removeClass("reveal");
        $("#trix-new-post-textarea, #trix-new-question-textarea").blur();
      });
      $(".new-post-submit").click(function() {
          $("#new-post, #new-question, .post-creator").removeClass("reveal");
          $("#trix-new-post-textarea, #trix-new-question-textarea").blur();
      });
      $("#new-post-form").submit(function(e) {
        if (!$.trim($("#new-post-textarea").val())) {
          e.preventDefault();
          alert('Your post cannot be empty.');
          $(this).removeClass('form-submitted');
        } else {
          $(this).find('.bt-spinner').removeClass('hide');
          $(this).find('.button-comment').addClass('text-red');
        }
      });
      $("#new-question-form").submit(function(e) {
        if (!$.trim($("#new-question-textarea").val())) {
          e.preventDefault();
          alert('Your post cannot be empty.');
          $(this).removeClass('form-submitted');
        } else {
          $(this).find('.bt-spinner').removeClass('hide');
          $(this).find('.button-comment').addClass('text-red');
        }
      });
      $("#new-comment-form").submit(function(e) {
        if (!$.trim($("#new-comment-textarea").val())) {
          e.preventDefault();
          alert('Your post cannot be empty.');
          $(this).removeClass('form-submitted');
        } else {
          $(this).find('.bt-spinner').removeClass('hide');
          $(this).find('.button-comment').addClass('text-red');
        }
      });
      $("#edit-post-form").submit(function(e) {
        if (!$.trim($("#edit-post-textarea").val())) {
          e.preventDefault();
          alert('Your post cannot be empty.');
        }
      });
      $(".edit-comment-form").submit(function(e) {
        if (!$.trim($(this).find("textarea").val())) {
          e.preventDefault();
          alert('Your post cannot be empty.');
        }
      });
      $(".cb-value").click(function() {
        var mainParent = $(this).parent(".toggle-btn");
        if($(mainParent).find("input.cb-value").is(":checked")) {
          $(mainParent).addClass("active");
        } else {
          $(mainParent).removeClass("active");
        }
      });
    });
  </script>

  <?php if ($page == 'messages') { ?>

    <link rel="stylesheet" type="text/css" href="css/selectize.custom.css">
    <script type="text/javascript" src="js/jquery-ui-1.12.1.custom.min.js"></script>
    <script type="text/javascript" src="js/selectize.min.js"></script>
    <script type="text/javascript" src="js/js.cookie.js"></script>

    <script type="text/javascript">

      jQuery(document).ready(function () {

        $.postJSON = function(url, data, success, args) {
          args = $.extend({
            url: url,
            type: 'POST',
            data: JSON.stringify(data),
            contentType: 'application/json; charset=utf-8',
            dataType: 'json',
            async: true,
            success: success
          }, args);
          return $.ajax(args);
        };

        // Scroll conversation window to bottom
        if ($('.messages-thread').length) {
          $('.messages-thread').scrollTop($('.messages-thread')[0].scrollHeight);
        }

        // Toggle conversation settings in mobile
        $('.messages-window').on('click', '.button-edit-mobile' ,function() {
          if ($('.column-left').hasClass('show')) {
            $(".column-left").addClass('slideOutLeft');
            setTimeout(function (){
              $(".column-left").removeClass('show animated slideInLeft slideOutLeft');
              $(".button-edit-mobile .icon-button-arrow").removeClass('icon-left');
              $(".button-edit-mobile .icon-button-arrow").addClass('icon-right');
            }, 500);
          } else {
            $(".column-left").addClass('show animated slideInLeft');
            $(".button-edit-mobile .icon-button-arrow").addClass('icon-left');
            $(".button-edit-mobile .icon-button-arrow").removeClass('icon-right');
          };
        });

        $('.messages-window').on('click', '.button-edit, .button-save' ,function() {
          $('.control-edit').toggle();
          $('.selectize-input').toggle();
          $('#conversation-name').toggle();
          $('.checkbox-remove + label').toggle();
          if($('.button-edit').text()=="Edit") {
            $('.button-edit').text("Cancel");
          } else {
            $('.button-edit').text("Edit");
            $('#conversationParticipants').val('');
            $('#conversationParticipants')[0].selectize.clear();
            if($(window).width() <= 1023) {
              $('.messages-thread').show();
              $('.column-left').hide();
            }
          }
        });

        $(document).on('change', '.checkbox-action input[type=checkbox]', function() {
            if($(this).is(":checked")){
                $(this).parent().parent().addClass("remove-selected");
            }else{
                $(this).parent().parent().removeClass("remove-selected");
            }
        });

        // Make sure conversation settings display correctly
        $(window).resize(function() {
          if($(window).width() >= 1024) {
            $('.messages-thread').show();
            $('.column-left').show();
          }
          if($(window).width() <= 1023) {
            $('.messages-thread').show();
            $('.column-left').hide();
          }
        });

        // Display new message in conversation and send to server
        function sendMsg() {
          var message = $('#new-message-textarea').val();
          <?php if ( (strpos($_SESSION['avatar'], 'Object') == false) AND ($_SESSION['avatar'] != NULL) ) { ?>
          var avatar = 'image.php?id=<?=$_SESSION['avatar']?>&height=200';
          <?php } else { ?>
          var avatar = 'img/robot.svg';
          <?php } ?>
          if (message != '') {
            var cid = $('#cid').val();
            var messageNew = '<div class="notification animated fadeIn"><div class="author"><a href="#"><div class="post-header col-avatar small"><img class="avatar" src="'+ avatar +'" alt="avatar"></div><div class="post-header"><div class="author-name"><?=$_SESSION['firstName'].' '.$_SESSION['lastName']?></div><div class="post-time">'+ moment().format('DD MMM h:mma') +'</div></div></a></div><div class="content"><div class="comment-content"><div>'+ message +'</div></div></div></div>';
            $(".message-thread-container").append(messageNew);
            $('#trix-new-message-textarea').val(null);
            $('.messages-thread').scrollTop($('.messages-thread')[0].scrollHeight);
            $(".message-thread-container .notification a").each(function() {
              $(this).attr('target','_blank');
            });
            var data = { action : JSON.stringify('new_message'), text : JSON.stringify(message), cid : JSON.stringify(cid) };
            $.postJSON('message.php', data, function(result) {
              // console.log(result);
            });
          }
        }


        $('.button-message-new').on('click', function(e){
          e.preventDefault();
          $('#trix-new-message-textarea').focus();
          sendMsg();
        });

        $('#trix-new-message-textarea').keydown(function(event) {
          if (event.keyCode == 13 && !event.shiftKey && $('#enter-send').is(':checked')) {
            sendMsg();
            return false;
          }
        });

        $('#enter-send').change(function() {
          if(this.checked) {
            Cookies.set('enter-send', '1');
            $('.tip').show();
          } else {
            Cookies.remove('enter-send');
            $('.tip').hide();
          }
          $('#trix-new-message-textarea').focus();
        });

        var enterSend = Cookies.get('enter-send');
        if (enterSend == 1) {
          $('#enter-send').prop('checked', true);
          $('.tip').show();
        }

        // Update conversation settings and send to server
        $('.button-save').on('click', function(){
          // e.preventDefault();
          var conversationName = $('.conversation-name').val();
          var addParticipants = $('#conversationParticipants').val();
          var cid = $('#cid').val();
          var removeParticipants = $('.checkbox-remove:checked').map(function() {return this.value;}).get().join(',')

          var data = { action : JSON.stringify('update_conversation'), conversationName : JSON.stringify(conversationName), addParticipants : JSON.stringify(addParticipants), cid : JSON.stringify(cid), removeParticipants : JSON.stringify(removeParticipants) };
          $.postJSON('message.php', data, function(result) {
            // Add members to participants view
            $('#participants-view').append(result['additions']);
            // Remove members from participants view
            $.each(result['removals'], function( index, value ) {
              $('#' + value).remove();
            });
            // Clear participant search form
            $('#conversationParticipants').val('');
            $('#conversationParticipants')[0].selectize.clear();
          });
          // Update conversation name
          $('#conversation-name').text(conversationName);
        });

        $('.back').on('click', function(e){
          e.preventDefault();
          var referrer = document.referrer;
          if (referrer.indexOf("auth.php") > -1 || referrer.indexOf("messages-new.php") > -1 || referrer.indexOf("messages-conversation.php") > -1) {
            window.location.href='messages-inbox.php';
          } else {
            window.history.back();
          }
        });

        if( $('#conversationParticipants').length ) {
          $('#conversationParticipants').selectize({
            delimiter: ',',
            persist: false,
            maxOptions: 50,
            preload: true,
            maxItems: 20,
            valueField: 'id',
            labelField: 'fullName',
            searchField: ['fullName'],
            selectOnTab: false,
            closeAfterSelect: true,
            options: [],
            render: {
              option: function(item, escape) {
                return '<div class="dropdown-title"><div class="small left"><img class="avatar" src="' + escape(item.avatarImage) + '"></div><div class="dropdown-name">' + escape(item.fullName) + '</div></div>';
              }
            },
            load: function(query, callback) {
                if (!query.length) return callback();
                $.ajax({
                    url: '<?php site_url();?>/tc_app/names.php',
                    type: 'GET',
                    dataType: 'json',
                    data: {
                        'terms': query,
                        'limit': 50,
                        'participants': <?=json_encode($participants)?>
                    },
                    error: function() {
                        callback();
                    },
                    success: function(result) {
                        callback(result);
                    }
                });
            },
            create: false
          });
          $('.selectize-input').toggle();
        }

        <?php if ($view == 'conversation') { ?>

          var messages_check = function() {
            var cid = $('#cid').val();
            var data = { action : JSON.stringify('update'), cid : JSON.stringify(cid) };
            $.postJSON('messages-check.php', data, function(result) {
              if(result) {
                $(".message-thread-container").append(result);
                $('.messages-thread').scrollTop($('.messages-thread')[0].scrollHeight);
                audio.play();
                $( ".notification-date" ).each(function() {
                  var notificationDate = parseInt($(this).attr('data-id'));
                  var localTime = moment.utc(notificationDate).toDate();
                  localTime = moment(localTime).format('DD MMM hh:mma');
                  $(this).find('.timestamp').text(localTime);
                });
                $(".message-thread-container .notification a").each(function() {
                  $(this).attr('target','_blank');
                });
              };
            });
          };

          var interval = 1000 * 60 * 0.25; // every X minutes

          var audio = new Audio('/audio/new_message.mp3');

          setInterval(messages_check, interval);

          var msgNum = 20;

          // Load previous messages in conversation
          $('#load-more').on('click', function(){
            var cid = $('#cid').val();
            var data = { action : JSON.stringify('load'), cid : JSON.stringify(cid), msgNum : JSON.stringify(msgNum) };
            $.postJSON('messages-check.php', data, function(result) {
              if(result) {
                if (result['stop'] == 1) {
                  $('#load-more').hide();
                }
                $(".message-thread-container").prepend(result['additions']);
                $( ".notification-date" ).each(function() {
                  var notificationDate = parseInt($(this).attr('data-id'));
                  var localTime = moment.utc(notificationDate).toDate();
                  localTime = moment(localTime).format('DD MMM hh:mma');
                  $(this).find('.timestamp').text(localTime);
                });
                $('.messages-thread').scrollTop($("#" + msgNum).prop("scrollHeight"));
                $('.messages-thread').animate({scrollTop: '-=250px'}, 800);
                msgNum = msgNum + 20;
                $('#trix-new-message-textarea').focus();
              };
            });
          });

        <?php } ?>

      });
    </script>

  <?php } ?>

  <?php if ($page == 'messages-new') {
   
    ?>

    <link rel="stylesheet" type="text/css" href="css/selectize.custom.css">
    <script type="text/javascript" src="js/jquery-ui-1.12.1.custom.min.js"></script>
    <script type="text/javascript" src="js/selectize.min.js"></script>

    <script type="text/javascript">

      jQuery(document).ready(function () {

        $('.back').on('click', function(e){
          e.preventDefault();
          var referrer = document.referrer;
          var search = "auth.php";
          if (referrer.indexOf(search) > -1)
              window.location.href='messages-inbox.php';
          else
              window.history.back();
        });

        if( $('#conversationParticipants').length ) {
          $('#conversationParticipants').selectize({
            delimiter: ',',
            persist: false,
            maxOptions: 50,
            preload: true,
            maxItems: 20,
            valueField: 'id',
            labelField: 'fullName',
            searchField: ['fullName'],
            selectOnTab: false,
            closeAfterSelect: true,
            options: [],
            render: {
              option: function(item, escape) {
                // Replace special characters in user names, specifically " and '
                item.fullName = item.fullName.replace("&#34;", "\"").replace("&#39;", "\'").replace("&#34;", "\"");
                return '<div class="dropdown-title"><div class="small left"><img class="avatar" src="' + escape(item.avatarImage) + '"></div><div class="dropdown-name">' + escape(item.fullName) + '</div></div>';
              }
            },
            load: function(query, callback) {
                if (!query.length) return callback();
                $.ajax({
                    url: '<?php site_url();?>/tc_app/names.php',
                    type: 'GET',
                    dataType: 'json',
                    data: {
                        'terms': query,
                        'limit': 50,
                    },
                    error: function() {
                        callback();
                    },
                    success: function(result) {
                        callback(result);
                    }
                });
            },
            create: false
          });
        }

        <?php if ($uid) { ?>
        $('#trix-new-message-textarea').focus();
        <?php } else { ?>
        $('#conversationParticipants-selectized').focus();
        <?php } ?>

      });

    </script>

    <?php } ?>

  <?php if ($page == 'notifications') { ?>

    <script type="text/javascript">
      jQuery(document).ready(function () { // Load posts on notification clicks

        $('.notifications-list').on('click', '.post' ,function() {
          window.location.href = 'view.php?id=' + $(this).attr('data-pid') + '#' + $(this).attr('data-cid');
        });

        $('.notifications-list').on('click', '.follow' ,function() {
          window.location.href = 'profile.php?id=' + $(this).attr('data-pid');
        });

        $('.notifications-list').on('click', '.approved' ,function() {
          window.location.href = 'feed.php?id=' + $(this).attr('data-pid');
        });

        $('.notifications-list').on('click', '.violation' ,function() {
          window.location.href = 'violations.php';
        });

        $( ".notification-date" ).each(function() {
          var notificationDate = parseInt($(this).attr('data-id'));
          var localTime = moment.utc(notificationDate).toDate();
          localTime = moment(localTime).format('DD MMM hh:mma');
          $(this).find('.timestamp').text(localTime);
        });

        $( ".date-range" ).each(function() {
          var startDateRaw = parseInt($(this).attr('data-sid'));
          var endDateRaw = parseInt($(this).attr('data-eid'));
          var startDate = moment.utc(startDateRaw).toDate();
          var endDate = moment.utc(endDateRaw).toDate();
          var localStartDate = moment(startDate).format('DD MMM Y');
          var localEndDate = moment(endDate).format('DD MMM Y');
          $(this).find('.start-date').text(localStartDate);
          $(this).find('.end-date').text(localEndDate);
        });

      });
    </script>

  <?php } ?>

  <?php if ($page == 'messages' OR 'inbox') { ?>

    <script type="text/javascript">
      jQuery(document).ready(function () { // Load conversation on inbox clicks

        $('.notifications-list').on('click', '.conversation' ,function() {
          window.location.href = 'messages-conversation.php?id=' + $(this).attr('data-pid');
        });

        $( ".notification-date" ).each(function() {
          var notificationDate = parseInt($(this).attr('data-id'));
          var localTime = moment.utc(notificationDate).toDate();
          localTime = moment(localTime).format('DD MMM hh:mma');
          $(this).find('.timestamp').text(localTime);
        });

        $( ".date-range" ).each(function() {
          var startDateRaw = parseInt($(this).attr('data-sid'));
          var endDateRaw = parseInt($(this).attr('data-eid'));
          var startDate = moment.utc(startDateRaw).toDate();
          var endDate = moment.utc(endDateRaw).toDate();
          var localStartDate = moment(startDate).format('DD MMM Y');
          var localEndDate = moment(endDate).format('DD MMM Y');
          $(this).find('.start-date').text(localStartDate);
          $(this).find('.end-date').text(localEndDate);
        });

      });
    </script>

  <?php } ?>

    <?php if ($page == 'feed' OR $page == 'members' OR $page == 'posts' OR $page == 'following' OR $page == 'followers') { ?>
      <div id="single-card"></div>
    <!-- <script src="js/infinite-scroll.pkgd.min.js"></script> -->
    <script src="js/masonry.pkgd.min.js"></script>
    <script type="text/javascript">
      jQuery(document).ready(function () {

        $('.feed-title .feed-description').linkify({
          formatHref: function (href, type) {
            return href;
          }
        });

        $( ".post-time" ).each(function() {
          var notificationDate = parseInt($(this).attr('data-id'));
          var localTime = moment.utc(notificationDate).toDate();
          localTime = moment(localTime).format('ddd D MMM Y');
          $(this).text(localTime);
        });

        $( ".date-range" ).each(function() {
          var startDateRaw = parseInt($(this).attr('data-sid'));
          var endDateRaw = parseInt($(this).attr('data-eid'));
          var startDate = moment.utc(startDateRaw).toDate();
          var endDate = moment.utc(endDateRaw).toDate();
          var localStartDate = moment(startDate).format('DD MMM Y');
          var localEndDate = moment(endDate).format('DD MMM Y');
          $(this).find('.start-date').text(localStartDate);
          $(this).find('.end-date').text(localEndDate);
        });

        var $grid = $('.grid').masonry({
          itemSelector: '.grid-item',
          percentPosition: true,
        });

        $('.back').on('click', function(e){
          e.preventDefault();
          window.history.back();
        });

        // Display Create New Post with community audience pre-selected
        $(document).on('click', '#community-create-post', function() {
          // $.sidr('close', 'site-menu');
          $("#site-search").removeClass("reveal");
          $('.contribute-dropdown').removeClass('reveal');
          $(".post-creator").addClass("reveal");
          $("#new-question").removeClass("reveal");
          $("#new-post").addClass("reveal");
          $(".post-audience").addClass("reveal");
          $(".post-attachments").removeClass("reveal");
          $("#post-button-audience").addClass("selected-post-tab");
          $("#post-button-photos").removeClass("selected-post-tab");
          $(".post-audience").addClass("selected-corner");
          $('#post-button-audience').attr('src', "img/icon-audience-active.svg");
          $('#post-button-photos').attr('src', "img/icon-attach.svg");
          $('#post-audience-select').val( $(this).attr('data-id') );
          $("#trix-new-post-textarea").focus();
        });

        // Display Create New Question with community audience pre-selected
        $(document).on('click', '#community-ask-question', function() {
          // $.sidr('close', 'site-menu');
          $("#site-search").removeClass("reveal");
          $('.contribute-dropdown').removeClass('reveal');
          $(".post-creator").addClass("reveal");
          $("#new-question").addClass("reveal");
          $("#new-post").removeClass("reveal");
          $(".question-audience").addClass("reveal");
          $(".question-attachments").removeClass("reveal");
          $(".question-anonymous").removeClass("reveal");
          $("#question-button-audience").addClass("selected-post-tab");
          $("#question-button-photos").removeClass("selected-post-tab");
          $("#question-button-anonymous").removeClass("selected-post-tab");
          $(".question-audience").addClass("selected-corner");
          $('#question-button-audience').attr('src', "img/icon-audience-active.svg");
          $('#question-button-photos').attr('src', "img/icon-attach.svg");
          $('#question-button-anonymous').attr('src', "img/icon-anonymous.svg");
          $('#question-audience-select').val( $(this).attr('data-id') );
          $("#trix-new-question-textarea").focus();
        });

        // Load feed cards in #single-card
        $('.grid').on('click', '.card .content' ,function() {

          // Load url for viewing content from feed cards
          <?php if ($page == 'members' OR $page == 'following' OR $page == 'followers') { ?>
            window.location.href = 'profile.php?id=' + $(this).closest(".card").attr("data-id");
          <?php } elseif ($search_term) { ?>
            window.location.href = 'view.php?id=' + $(this).closest(".card").attr("data-id") + '&search=' + encodeURI('<?=$search_term?>').replace(/%20/g, '+');
          <?php } else { ?>
            window.location.href = 'view.php?id=' + $(this).closest(".card").attr("data-id");
          <?php } ?>

        });

      });
      </script>

    <?php } ?>


    <?php if ($page == 'profile') { ?>

      <script type="text/javascript">
        jQuery(document).ready(function () {

          $(".more-toggle").click(function(){
            $(this).closest(".profile-block").find(".section-body").toggleClass("profile-reveal");
            if ($.trim($(this).text()) === 'Show More') {
                $(this).text('Show Less');
            } else {
                $(this).text('Show More');
            }
          });

          $('.profile-block').each( function() {
              if ($('.section-body', this).prop('scrollHeight') > $('.section-body', this).prop('clientHeight'))
              $('.more-toggle', this).css('display', 'block');
          });

          $('.user-profile .profile-section-bio-listing, .user-profile .affiliate-list-description').linkify({
            formatHref: function (href, type) {
              return href;
            }
          });

        });
      </script>

    <?php } ?>


    <?php if ($page == 'view' OR $page == 'feed' OR ($page == 'profile' AND !$action)) { ?>
      <div id="confirm-delete" class="modal">
        <div class="modal-text">
          Are you sure you want to delete this post?
        </div>
        <div class="modal-actions">
          <button id="confirm-delete-button" class="modal-button modal-button-red" data-id="<?=$post_id?>" data-type="post">Yes</button> <button class="modal-button modal-button-outline">No</button>
        </div>
        <div class="clear">
          &nbsp;
        </div>
      </div>
      <?php } ?>

      <?php if ($view == 'conversation') { ?>
        <div id="confirm-delete" class="modal">
          <div class="modal-text">
            Are you sure you want to delete this conversation?
          </div>
          <div class="modal-actions">
            <button id="confirm-delete-button" class="modal-button modal-button-red" data-id="<?=$post_id?>" data-type="conversation">Yes</button> <button class="modal-button modal-button-outline">No</button>
          </div>
          <div class="clear">
            &nbsp;
          </div>
        </div>
        <div id="confirm-leave" class="modal">
          <div class="modal-text">
            Are you sure you want to leave this conversation?
          </div>
          <div class="modal-actions">
            <button id="confirm-leave-button" class="modal-button modal-button-red" data-id="<?=$post_id?>" data-type="conversation">Yes</button> <button class="modal-button modal-button-outline">No</button>
          </div>
          <div class="clear">
            &nbsp;
          </div>
        </div>
      <?php } ?>

      <?php if ($page == 'feed' AND $search) {

        $search_terms = explode(' ', $search); ?>

        <script type="text/javascript">

          jQuery(document).ready(function () {

            $('.content .preview-content').mark(
              <?=json_encode($search_terms)?>,
              {
                'className': 'search-highlight'
              }
            );

          });

        </script>

      <?php } ?>

      <?php if ($page == 'view') { ?>
      <script type="text/javascript">

        jQuery(document).ready(function () {

          $('.content .post-content, .content .comment-content').linkify({
            formatHref: function (href, type) {
              if (type === 'hashtag') {
                href = '<?php site_url();?>/feed.php?search=%23' + href.substring(1);
              }
              return href;
            }
          });

          <?php if ($search) {
            $search_terms = explode(' ', $search); ?>
            $('.content .post-content, .content .comment-content').mark(
              <?=json_encode($search_terms)?>,
              {
                'className': 'search-highlight'
              }
            );
          <?php } ?>


          $( ".post-time" ).each(function() {
            var notificationDate = parseInt($(this).attr('data-id'));
            var localTime = moment.utc(notificationDate).toDate();
            localTime = moment(localTime).format('ddd D MMM Y');
            $(this).text(localTime);
          });

        });

      </script>

    <?php } ?>

    <?php if ($page == 'profile' AND $action) { ?>

      <div id="confirm-delete" class="modal">
        <div class="modal-text">
          Are you sure you want to delete this profile information?
        </div>
        <div class="modal-actions">
          <button id="confirm-delete-button" class="modal-button modal-button-red">Yes</button> <button class="modal-button modal-button-outline">No</button>
        </div>
        <div class="clear">
          &nbsp;
        </div>
      </div>

      <script type="text/javascript" src="js/jquery-ui-1.12.1.custom.min.js"></script>
      <script type="text/javascript" src="js/selectize.min.js"></script>

      <?php
      $yearStart = 1900;
      $yearEnd = date('Y') + 100;
      $yearCurrent = date('Y');
      $yearRange = range($yearCurrent, $yearStart);
      ?>

      <script type="text/javascript">
        jQuery(document).ready(function () {
          $('.site-footer').css({'display':'block'});

          if( $('#teachLocationName').length ) {
            $('#teachLocationName').selectize({
              delimiter: ',',
              persist: false,
              maxOptions: 50,
              preload: true,
              maxItems: 1,
              valueField: 'name',
              labelField: 'name',
              searchField: 'name',
              selectOnTab: true,
              options: [],
              render: {
                option: function(item, escape) {
                  return '<div class="dropdown-title">' + escape(item.name) + '<div class="dropdown-subtitle">' + escape(item.city) + ', ' + escape(item.state) + '</div></div>';
                }
              },
              onItemAdd: function(value, $item) {
                // console.log(this.options[value]);
                var city = this.options[value]['city'];
                var state = this.options[value]['state'];
                if (city) document.getElementById('teachLocationCity').value=city;
                if (state) document.getElementById('teachLocationState').value=state;
              },
              onItemRemove: function(value) {
                document.getElementById('teachLocationCity').value='';
                document.getElementById('teachLocationState').value='';
              },
              load: function(query, callback) {
                  if (!query.length) return callback();
                  $.ajax({
                      url: '<?php site_url();?>/schools.php',
                      type: 'GET',
                      dataType: 'json',
                      data: {
                          'terms': query,
                          'limit': 50,
                      },
                      error: function() {
                          callback();
                      },
                      success: function(result) {
                          callback(result);
                      }
                  });
              },
              create: function(input) {
                  return {name: input}
              }
            });
          }

          if( $('#teachGrades').length ) {
            $('#teachGrades').selectize({
              delimiter: ',',
              persist: false,
              valueField: 'name',
              labelField: 'name',
              searchField: 'name',
              selectOnTab: true,
              options: [
                <?php include('grades.php'); ?>
              ],
              render: {
                option: function(item, escape) {
                  return '<div class="dropdown-title">' + escape(item.name) + '</div>';
                }
              },
              create: false
            });
          }

          if( $('#teachSubjects').length ) {
            $('#teachSubjects').selectize({
              delimiter: ',',
              persist: false,
              valueField: 'name',
              labelField: 'name',
              searchField: 'name',
              selectOnTab: true,
              options: [
                <?php include('subjects.php'); ?>
              ],
              render: {
                option: function(item, escape) {
                  return '<div class="dropdown-title">' + escape(item.name) + '</div>';
                }
              },
              create: function(input) {
                  return {name: input}
              }
            });
          }

          if( $('#teachStart').length ) {
            $('#teachStart').selectize({
              delimiter: ',',
              persist: false,
              maxItems: 1,
              valueField: 'name',
              labelField: 'name',
              searchField: 'name',
              selectOnTab: true,
              options: [
                <?php
                for ($count = $yearCurrent; $count >= $yearStart; $count--) {
                    echo "{name: '{$count}'},";
                }
                ?>
              ],
              render: {
                option: function(item, escape) {
                  return '<div class="dropdown-title">' + escape(item.name) + '</div>';
                }
              },
              create: false
            });
          }

          if( $('#teachEnd').length ) {
            $('#teachEnd').selectize({
              delimiter: ',',
              persist: false,
              maxItems: 1,
              valueField: 'name',
              labelField: 'name',
              searchField: 'name',
              selectOnTab: true,
              options: [
                {name: 'Present'},
                <?php
                for ($count = $yearCurrent; $count >= $yearStart; $count--) {
                    echo "{name: '{$count}'},";
                }
                ?>
              ],
              render: {
                option: function(item, escape) {
                  return '<div class="dropdown-title">' + escape(item.name) + '</div>';
                }
              },
              create: false
            });
          }

          if( $('#teachLicenseLocation').length ) {
            $('#teachLicenseLocation').selectize({
              delimiter: ',',
              persist: false,
              maxItems: 1,
              valueField: 'name',
              labelField: 'name',
              searchField: 'name',
              options: [
                <?php include('institutes.php'); ?>
              ],
              render: {
                option: function(item, escape) {
                  return '<div class="dropdown-title">' + escape(item.name) + '</div>';
                }
              },
              create: function(input) {
                  return {name: input}
              }
            });
          }

          if( $('#teachLicenseComplete').length ) {
            $('#teachLicenseComplete').selectize({
              delimiter: ',',
              persist: false,
              maxItems: 1,
              valueField: 'name',
              labelField: 'name',
              searchField: 'name',
              options: [
                <?php
                  for ($count = $yearStart; $count <= $yearEnd; $count++) {
                      echo "{name: '{$count}'},";
                  }
                ?>
              ],
              render: {
                option: function(item, escape) {
                  return '<div class="dropdown-title">' + escape(item.name) + '</div>';
                }
              },
              create: false
            });
          }

          if( $('#affiliateName').length ) {
            $('#affiliateName').selectize({
              delimiter: ',',
              persist: false,
              maxItems: 1,
              valueField: 'name',
              labelField: 'name',
              searchField: 'name',
              options: [
                <?=$affiliates?>
              ],
              render: {
                option: function(item, escape) {
                  return '<div class="dropdown-title">' + escape(item.name) + '</div>';
                }
              },
              create: false
            });
          }

        });

      </script>
    <?php } ?>

    <script type="text/javascript">
        jQuery(document).ready(function () {
          $('body').on('click', '.modal-button-outline', function(){
            $.modal.close();
        });
      });
    </script>
  </body>
</html>
<?php
// Write general activity to log
$url = parse_url($_SERVER['HTTP_REFERER']);
$referer = $url['path'];
if ($url['query']) {
  $referer = $referer.'?'.$url['query'];
}

$activity_data[] = $referer;
$activity_data[] = $this->e($title);
$activity_data[] = $_SERVER['REQUEST_URI'];
$activity_data[] = get_user_ip_address();
update_last_active_timestamp();
new_activity_log($_SESSION['uid'], 'viewed page', $activity_data);
?>
