<html>
  <head>
    <title><?=$this->e($title)?></title>
    <meta name="viewport" content="width=device-width, initial-scale = 1.0, maximum-scale=1.0, user-scalable=no" />
    <meta name="apple-mobile-web-app-title" content="TeachersConnect">
    <!-- <meta name="apple-mobile-web-app-capable" content="yes"> -->
    <link rel="apple-touch-icon" href="img/icon-tc-app.png">
    <link rel="manifest" href="manifest.json">
    <link rel="shortcut icon" href="img/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="css/normalize.css">
    <link rel="stylesheet" type="text/css" href="css/animate.css">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,700" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="css/trix.css">
    <link rel="stylesheet" type="text/css" href="css/slick.css"/>
    <link rel="stylesheet" type="text/css" href="css/slick-theme.css"/>
    <?php if ($page == 'profile' AND $action) { ?>
      <link rel="stylesheet" type="text/css" href="css/selectize.custom.css">
    <?php } ?>
    <link rel="stylesheet" type="text/css" href="css/styles.css?04042018">
    <?php if (Config::SERVER == 'production') { ?>
    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-69936049-13"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());
      gtag('config', 'UA-69936049-13');
    </script>
    <!-- Google Tag Manager -->
    <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
    new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
    j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
    'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
    })(window,document,'script','dataLayer','GTM-53H3NSS');</script>
    <?php } ?>
  </head>
  <body class="<?=$page.' '.$view?>">
    <?php if (Config::SERVER == 'staging') {
      echo "<h1 class='server_env'>STAGING</h1>";
    } else { ?>
      <!-- Google Tag Manager (noscript) -->
      <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-53H3NSS"
      height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
      <!-- End Google Tag Manager (noscript) -->
    <?php } ?>
    <img style="display:none;" src="img/icon-menu-notifications.png">
    <div class="post-creator">
      <div class="" id="new-post">
        <form id="new-post-form" class="form-container submit-once" method="post" action="post.php" enctype="multipart/form-data">
          <div class="new-post-title">
            Create New Post <img class="icon-cancel right new-post-close" src="img/icon-cancel.svg">
          </div>
          <div class="new-post-intro">
            Share an idea, resource, lesson, or story with others on TeachersConnect. They will be able to see your post in the feed and start a conversation by commenting on what you share. You can attach up to 4 files to your post.
          </div>
          <div class="comment-text">
            <textarea id="new-post-textarea" name="text" placeholder="Write your new post here"></textarea>
            <trix-editor id="trix-new-post-textarea" input="new-post-textarea" placeholder="Write your new post here"></trix-editor>
            <!-- <textarea id="new-post-textarea" name="text" placeholder="Write your new post here"></textarea> -->
          </div>
          <div class="new-post-bar">
            <div class="right">
              <div class="bt-spinner hide"></div>
              <input type="submit" class="button-comment" id="new-post-submit" value="Post">
            </div>
            <div class="post-buttons">
              <?php if ($_SESSION['partners']) { ?><img id="post-button-audience" alt="Audience" src="img/icon-audience.svg"><?php } ?><img id="post-button-photos" alt="Photos" src="img/icon-attach.svg">
            </div>
            <div class="post-attachments">
              You may attach up to four files to your post. <a href="#!" class="modal-filetypes">What file types can I attach?</a>
              <div class="image-preview-container">
                <div class="image-preview">
                  <label for="FileUpload1"><div class="preview" id="FileUpload1-preview">&nbsp;</div></label>
                  <input id="FileUpload1" type="file" name="file1" />
                </div>
                <div class="image-preview">
                  <label for="FileUpload2"><div class="preview" id="FileUpload2-preview"></div></label>
                  <input id="FileUpload2" type="file" name="file2" />
                </div>
                <div class="image-preview">
                  <label for="FileUpload3"><div class="preview" id="FileUpload3-preview"></div></label>
                  <input id="FileUpload3" type="file" name="file3" />
                </div>
                <div class="image-preview">
                  <label for="FileUpload4"><div class="preview" id="FileUpload4-preview"></div></label>
                  <input id="FileUpload4" type="file" name="file4" />
                </div>
              </div>

            </div>
            <?php if ($_SESSION['partners']) { ?>
            <div class="post-audience">
              Only members of the selected community will see your post.
              <select class="audience-dropdown" name="audience" id="post-audience-select">
                <option value="0">All TeachersConnect Members</option>
                <?php foreach ($_SESSION['partners'] as $partners => $partner) {
                  echo '<option value="' . $partner['id'] . '">' . $partner['name'] . '</option>';
                } ?>
              </select>
            </div>
          <?php } ?>
            <div class="clear">
              &nbsp;
            </div>
          </div>
        </form>
      </div>
      <div class="" id="new-question">
        <form id="new-question-form" class="form-container submit-once" method="post" action="post.php" enctype="multipart/form-data">
          <div class="new-post-title">
            Ask a Question <img class="icon-cancel right new-post-close" src="img/icon-cancel.svg">
          </div>
          <div class="new-post-intro">
            Share your question with others on TeachersConnect. Use the Privacy controls to protect your identity when asking sensitive questions. You can attach up to 4 files to your question. <i>Shorter questions are more likely to be read.</i>
          </div>
          <input type="hidden" name="type" value="question">
          <div class="comment-text">
            <textarea id="new-question-textarea" name="text" placeholder="Write your new question here"></textarea>
            <trix-editor id="trix-new-question-textarea" input="new-question-textarea" placeholder="Write your new question here"></trix-editor>
          </div>
          <div class="new-post-bar">
            <div class="right">
              <div class="bt-spinner hide"></div>
              <input type="submit" class="button-comment" id="new-question-submit" value="Ask">
            </div>
            <div class="question-buttons">
              <?php if ($_SESSION['partners']) { ?><img id="question-button-audience" alt="Audience" src="img/icon-audience.svg"><?php } ?><img id="question-button-photos" alt="Files" src="img/icon-attach.svg"><img id="question-button-anonymous" alt="Anonymous" src="img/icon-anonymous.svg">
            </div>
            <div class="question-attachments">
              You may attach up to four files to your question. <a href="#!" class="modal-filetypes">What file types can I attach?</a>
              <div class="image-preview-container">
                <div class="image-preview">
                  <label for="FileUpload5"><div class="preview" id="FileUpload5-preview">&nbsp;</div></label>
                  <input id="FileUpload5" type="file" name="file5" />
                </div>
                <div class="image-preview">
                  <label for="FileUpload6"><div class="preview" id="FileUpload6-preview"></div></label>
                  <input id="FileUpload6" type="file" name="file6" />
                </div>
                <div class="image-preview">
                  <label for="FileUpload7"><div class="preview" id="FileUpload7-preview"></div></label>
                  <input id="FileUpload7" type="file" name="file7" />
                </div>
                <div class="image-preview">
                  <label for="FileUpload8"><div class="preview" id="FileUpload8-preview"></div></label>
                  <input id="FileUpload8" type="file" name="file8" />
                </div>
              </div>
            </div>
            <?php if ($_SESSION['partners']) { ?>
            <div class="question-audience">
              Only members of the selected community will see your question.
              <label for="question-audience-select" hidden>Select question audience</label>
              <select class="audience-dropdown" name="audience" id="question-audience-select">
                <option value="0">All TeachersConnect Members</option>
                <?php foreach ($_SESSION['partners'] as $partners => $partner) {
                  echo '<option value="' . $partner['id'] . '">' . $partner['name'] . '</option>';
                } ?>
              </select>
            </div>
          <?php } ?>
            <div class="question-anonymous">
              You can publish your question with your name or ask anonymously.
              <!-- <div class="anon-box">
                <label for="anonymous">Your Name</label>
                <div class="toggle-btn">
                  <input name="anon" type="checkbox" class="cb-value" />
                  <span class="round-btn"></span>
                </div>
                <label for="anonymous">Anonymous</label>
              </div> -->
              <select class="privacy-dropdown" name="privacy">
                <option selected value="attributed">Publish as <?=ucfirst($_SESSION['firstName'])?> <?=ucfirst($_SESSION['lastName'])?></option>
                <option value="anonymous">Publish anonymously</option>
              </select>
            </div>
          </div>
        </form>
      </div>
    </div>

    <div id="nav-bar" class="nav-bar row">
      <div class="header-block header-logo">
        <a href="home.php"><img class="logo" alt="TeachersConnect" src="img/tclogo-small.png"></a>
      </div>

      <div class="header-block site-search-container">
        <form id="site-search" class="site-search" action="<?php if ($page == 'members') { echo "members.php"; } else { echo "feed.php"; }?>">
          <input id="text-search" class="text-search" name="search" type="text" placeholder="Search TeachersConnect" value="<?=$search_term?>">
          <div class="search-dropdown">
            <div class="search-dropdown-option">
              <input name="search-domain" value="contributions" id="search-dropdown-option-contributions" type="radio"<?php if ($page == 'feed') { echo ' checked="checked"'; }?>><label class="search-label" for="search-dropdown-option-contributions">Contributions</label>
            </div>
            <div class="search-dropdown-option">
              <input name="search-domain" value="members" id="search-dropdown-option-members" type="radio"<?php if ($page == 'members') { echo ' checked="checked"'; }?>><label class="search-label" for="search-dropdown-option-members">Members</label>
            </div>
            <input value="Find" style="" class="search-dropdown-button" type="submit">
          </div>
        </form>
      </div>

      <!-- <a id="site-search-button"><img class="button-menu" src="img/icon-search-text.svg"></a> -->

      <div class="header-block right top-bar-button-group">
        <a class="top-bar-button home-button" href="home.php">
          <img class="top-bar-profile" src="img/icon-menu-home.png">
        </a>
        <a class="top-bar-button search-button">
          <img id="site-search-button" class="top-bar-profile" src="img/icon-menu-search.png">
        </a>
        <a class="top-bar-button" id="button-contribute">
          <img class="top-bar-profile" src="img/icon-menu-contribute.png">
        </a>
        <div class="contribute-dropdown">
          <div id="button-new-post" class="contribute-dropdown-option separator-yellow"><a>Create Post</a></div>
          <div id="button-new-question" class="contribute-dropdown-option"><a>Create Question</a></div>
        </div>
        <?php if ($_SESSION['trusted'] == 'yes') { ?>
        <a class="top-bar-button conversations-container" href="messages-inbox.php">
          <div class="hide conversation-count"></div>
        </a>
        <?php } ?>
        <a class="top-bar-button notifications-container" href="notifications.php">
          <div class="hide notification-count"></div>
        </a>
        <a class="top-bar-button" href="profile.php?id=<?=$_SESSION['uid']?>">
          <img class="top-bar-profile" src="img/icon-menu-profile.png">
        </a>
        <a class="top-bar-button" href="edit-affiliate.php">
          <img class="top-bar-communities" src="img/icon-menu-communities.png">
        </a>
      </div>
    </div>

    <?php if ($_SESSION['mobile']) { ?>
    <style>#nav-bar{display:none;}</style>
    <?php } ?>

    <nav id="site-menu" class="row nav-down">
      <!-- <div class="user-acc">
        <div class="user-acc-picture pic35">
          <?php if ( (strpos($_SESSION['avatar'], 'Object') == false) AND ($_SESSION['avatar'] != NULL) ) { ?>
            <img class="avatar" src="image.php?id=<?=$_SESSION['avatar']?>&width=200">
          <?php } else { ?>
            <img class="avatar" src="img/robot.svg">
          <?php } ?>
        </div>
        <div class="user-acc-name">
          <?=ucfirst($_SESSION['firstName']) . ' ' . ucfirst($_SESSION['lastName']);?>
        </div>
      </div> -->
      <div class="tile-row">
        <!-- <ul class="tile-group">
          <li class="tile-single">
            <a href="profile.php?id=<?=$_SESSION['uid']?>"><img id="button-view-profile" class="tile-button" src="img/tile-profile.png"></a>
          </li>
          <li class="tile-single">
            <a href="notifications.php"><div id="button-recent-notifications" class="tile-button tile-notification-count"><div id="alert-number" class=""></div></div></a>
          </li>
          <li class="tile-single">
            <a href="auth.php?logout=1"><img id="button-logout" class="tile-button" src="img/tile-logout.png"></a>
          </li>
          <li class="tile-single clear-left">
            <img id="button-new-post" class="tile-button" src="img/tile-new-post.png">
          </li>
          <li class="tile-single">
            <img id="button-new-question" class="tile-button" src="img/tile-new-question.png">
          </li>
        </ul> -->
        <div class="tile-group-title">Communities</div>
        <ul class="tile-group tile-group-connector">
          <li class="tile-single">
            <a href="feed-following.php"><img id="button-connections-feed" class="tile-button<?php if ($page == 'feed-following') { echo ' active'; }?>" src="img/tile-your-connections.png"></a>
          </li>
          <li class="tile-single">
            <a href="feed.php"><img id="button-community-feed" class="tile-button<?php if ($page == 'feed' AND !$feed_number AND !$search_term) { echo ' active'; }?>" src="img/tile-community-feed.png"></a>
          </li>
          <?php if ($_SESSION['partners']) {
            $counter = 3;
            foreach ($_SESSION['partners'] as $partners => $partner) {
              echo '<li class="tile-single';
              if ($counter == 4) {  echo ' clear-left'; $counter = 1; }
              echo '"><a href="feed.php?id=' . $partner['id'] . '"><img id="button-community-feed" class="tile-button';
              if ($page == 'feed' AND ($partner['id'] == $feed_number)) { echo ' active'; }
              echo '" alt="' . $partner['name'] . '" src="img/' . $partner['image'] . '"></a></li>';
              $counter++;
            }
          }
          if ($counter == 4) {
            echo '<li class="tile-single clear-left">';
          } else {
            echo '<li class="tile-single">';
          }
          ?>

            <a href="edit-affiliate.php?menu=1"><img id="button-add-community" class="tile-button no-shadow" src="img/tile-add-community.png"></a>
          </li>
        </ul>
      </div>
    </nav>
      <div class="container-search">
        <form id="site-search-mobile" class="site-search" action="<?php if ($page == 'members') { echo "members.php"; } else { echo "feed.php"; }?>">
          <input id="text-search" class="text-search" name="search" type="text" placeholder="Search TeachersConnect" value="<?=$search_term?>">
          <div class="search-mobile-option">
            <input name="search-domain" value="contributions" id="search-mobile-option-contributions" type="radio"<?php if ($page == 'feed') { echo ' checked="checked"'; }?>><label class="search-label" for="search-mobile-option-contributions">Contributions</label>
          </div>
          <div class="search-mobile-option">
            <input name="search-domain" value="members" id="search-mobile-option-members" type="radio"<?php if ($page == 'members') { echo ' checked="checked"'; }?>><label class="search-label" for="search-mobile-option-members">Members</label>
          </div>
          <input value="Find" style="" class="search-dropdown-button" type="submit">
        </form>
      </div>

      <?php if ($page == 'view') { ?>
        <?php if ($alert == 'success-post-edited') { ?>
          <div class="alert">Your post has been successfully updated.</div>
        <?php } ?>
        <?php if ($alert == 'success-comment-deleted') { ?>
          <div class="alert">Your comment has been successfully deleted.</div>
        <?php } ?>
        <?php if ($alert == 'success-comment-edited') { ?>
          <div class="alert">Your comment has been successfully updated.</div>
        <?php } ?>
      <?php } ?>

      <?php if ($view == 'inbox') { ?>
        <?php if ($alert == 'success-conversation-deleted') { ?>
          <div class="alert">Your conversation has been successfully deleted.</div>
        <?php } ?>
        <?php if ($alert == 'success-conversation-left') { ?>
          <div class="alert">You have successfully left that conversation.</div>
        <?php } ?>
      <?php } ?>

      <?php if ($page == 'home') { ?>

      <div class="home-title-wrapper">

        <div class="clear"></div>

      </div>

      <?php } ?>

      <?php if ($page == 'feed') { ?>

      <div class="feed-title-wrapper">
        <?php if ($alert == 'success') { ?>
          <div class="alert">This community has been added to your profile.</div>
        <?php } ?>
        <?php if ($alert == 'success-post-created') { ?>
          <div class="alert">Your post has been published to the <?=$title?>.</div>
        <?php } ?>
        <?php if ($alert == 'success-question-created') { ?>
          <div class="alert">Your question has been published to the <?=$title?>.</div>
        <?php } ?>
        <?php if ($alert == 'success-post-deleted') { ?>
          <div class="alert">Your post has been deleted from the <?=$title?>.</div>
        <?php } ?>
        <?php if ($search_term) { ?>
          <div class="follow-title">
        <?php } else { ?>
          <div class="feed-title">
        <?php } ?>
        <?php if ($_SESSION['mobile'] AND $search_term) { } elseif ($search_term) { ?>
          <div class="feed-logo"><img class="logo-partner" src="img/<?=$partner_logo?>"></div>
        <?php } elseif ($partner_logo) { ?>
          <div class="feed-logo"><a href="feed.php?id=<?=$feed_number?>"><img class="logo-partner" src="img/<?=$partner_logo?>"></a></div>
        <?php } ?>
          <?php if (!$search_term) { ?>
            <h1><a href="feed.php?id=<?=$feed_number?>"><?=$this->e($title)?></a></h1>
          <?php } else { ?>
            <h1><?=$this->e($title)?></h1>
          <?php } ?>
          <?php if ($_SESSION['mobile']) { } elseif ($partner_description) { ?>
          <div class="feed-description"><?=$partner_description?></div>
          <?php } ?>
          <div class="clear"></div>
          <?php if ($feed_number != "0") { ?>
            <ul class="menu-sub-community">
            <li class="menu-sub-current"><a href="feed.php?id=<?=$feed_number?>">Posts & Questions</a></li>
            <li><a href="members.php?id=<?=$feed_number?>">Community Members</a></li>
            </ul>
          <?php } ?>
        </div>

        <?php if ($_SESSION['mobile']) { } else { ?>
        <div class="feed-controls">
          <?php if (!$search_term) { ?>

          <div class="group-buttons">
            <div id="community-create-post" class="button-secondary" data-id="<?=$feed_number?>"><img class="button-icon svg" src="img/icon-create-post.svg"> Create Post</div>
            <div id="community-ask-question" class="button-secondary" data-id="<?=$feed_number?>"><img class="button-icon svg" src="img/icon-ask-question.svg"> Ask Question</div>

        <div class="community-dropdown inline">
        <div>Invite Colleagues <img class="icon-button-arrow" src="img/arrow-down.svg"></div>

        <div class="dropdown-content community-dropdown-content">

          <div>
          <a href="http://twitter.com/home?status=Join%20me%20on%20TeachersConnect%20-%20an%20uncompromisingly%20teacher-centric%20online%20community%20built%20for%20us.+https%3A%2F%2Fwww%2Eteachersconnect%2Eonline%2Frefer%2Ephp?ref=<?=$_SESSION['uid']?>" onclick="javascript:window.open(this.href,'', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');return false;">
            <div id="twitter-share">On Twitter</div></a>
          </div>

        <div>
          <a href="http://www.facebook.com/share.php?title=Join%20me%20on%20TeachersConnect%20-%20an%20uncompromisingly%20teacher-centric%20online%20community%20built%20for%20us.&u=https%3A%2F%2Fwww%2Eteachersconnect%2Eonline%2Frefer%2Ephp?ref=<?=$_SESSION['uid']?>" onclick="javascript:window.open(this.href,'', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');return false;">
            <div id="facebook-share">On Facebook</div></a>
        </div>

          <div>
          <a href="http://www.linkedin.com/shareArticle?mini=true&title=Join%20me%20on%20TeachersConnect%20-%20an%20uncompromisingly%20teacher-centric%20online%20community%20built%20for%20us.&url=https%3A%2F%2Fwww%2Eteachersconnect%2Eonline%2Frefer%2Ephp?ref=<?=$_SESSION['uid']?>" onclick="javascript:window.open(this.href,'', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');return false;">
            <div id="linkedin-share">On Linkedin</div></a>
        </div>

          <div>
          <a href="mailto:email@example.com?subject=&body=Join%20me%20on%20TeachersConnect%20-%20an%20uncompromisingly%20teacher-centric%20online%20community%20built%20for%20us.%20%20https%3A%2F%2Fwww%2Eteachersconnect%2Eonline%2Frefer%2Ephp?ref=<?=$_SESSION['uid']?>">
            <div id="email-share">Via Email</div></a>
        </div>

        </div>



</div>

<div class="clear"></div>



          <?php } ?>
          <?php if ( (!$search_term) OR ($search_term AND $total_count != 0) ) { ?>
            <div class="feed-metadata-container">
              <div class="feed-metadata">
                <div class="metadata-title">Sort Order</div>
                  <?php if ($search_term) { ?>
                    <div class="sort-dropdown inline">
                    <?php if ($_GET['sort-order'] == 'newest') { ?>
                    <div>
                      Newest to Oldest <img class="icon-button-arrow" src="img/arrow-down.svg">
                    </div>
                    <div class="dropdown-content sort-dropdown-content">
                      <a href="feed.php?search=<?=$search_term?>&search-domain=contributions">Most Relevant</a>
                    </div>
                    <?php } else { ?>
                    <div>
                      Most Relevant <img class="icon-button-arrow" src="img/arrow-down.svg">
                    </div>
                    <div class="dropdown-content sort-dropdown-content">
                      <a href="feed.php?search=<?=$search_term?>&search-domain=contributions&sort-order=newest">Newest to Oldest</a>
                    </div>
                    <?php } ?>
                  <?php } else { ?>
                    <div class="button-secondary select-list-gray">
                    <?php if ($_GET['sort-order'] == 'relevant') { ?>
                    Most Relevant
                    <?php } else { ?>
                    Newest to Oldest
                    <?php } ?>
                  <?php } ?>
                </div>
              </div>
              <!-- <div class="feed-metadata">
                Filter
                <div class="button-secondary select-list-gray">
                  All Types
                </div>
              </div> -->
              <?php if ($last_page == 1 AND $current_page == 1) { ?>

              <?php } elseif ( ($_GET['sort-order'] == 'newest') OR (!$search_term) ) { ?>
                <div class="date-range" data-sid="<?=$start_date_raw?>" data-eid="<?=$end_date_raw?>">
                  <div class="metadata-title">Post Date</div>
                  <?php if ($total_count == 30) { ?>
                    <a href="<?=$url_prev?>"><div class="show-older-link-small"><img class="icon-button-arrow icon-left svg" src="img/arrow-down.svg"></div></a>
                  <?php } ?>
                  <div class="button-secondary select-list-gray">
                    <?php if ($end_date == $start_date) { ?>
                      <span class="end-date"><?=$end_date?></span>
                    <?php } else { ?>
                      <span class="end-date"><?=$end_date?></span> - <span class="start-date"><?=$start_date?></span>
                    <?php } ?>
                  </div>
                  <?php if ($current_page >= 2) { ?>
                    <a href="<?=$url_next?>"><div class="show-newer-link-small"><img class="icon-button-arrow icon-right svg" src="img/arrow-down.svg"></div></a>
                  <?php } ?>
                </div>
              <?php } else { ?>
                <div class="date-range">
                  <div class="metadata-title">Page Number</div>
                  <?php if ($total_count == 30) { ?>
                    <a href="<?=$url_prev?>"><div class="show-older-link-small"><img class="icon-button-arrow icon-left svg" src="img/arrow-down.svg"></div></a>
                  <?php } ?>
                  <div class="button-secondary select-list-gray">
                      <span class=""><?=$current_page?></span>
                  </div>
                  <?php if ($current_page >= 2) { ?>
                    <a href="<?=$url_next?>"><div class="show-newer-link-small"><img class="icon-button-arrow icon-right svg" src="img/arrow-down.svg"></div></a>
                  <?php } ?>
                </div>
              <?php } ?>
            </div>
          <?php } ?>

        </div>
      </div>
      <?php } ?>
      <div class="clear"></div>
      <div id="feed-view">
        <div class="grid">
      <?php } ?>

      <?php if ($page == 'view') { ?>
      <div id="single-post" class="single-post">
      <?php } ?>

      <?php if ($page == 'feed-following') { ?>

      <div class="feed-title-wrapper">
        <div class="follow-title">
          <div class="profile-page-header">
            <img class="large" src="img/icon-your-connections.svg">
            <h1>Your Connections</h1>
            <?php if ($_SESSION['mobile']) { } else { ?>
          <div class="post-display-explanation">
            Learn from other teachers and make a greater impact in your classroom. Follow anyone in the community by clicking “follow” in their profile. Then check back here to see their contributions.
            <!-- This does not include comments, responses and anonymous questions. -->
          </div>

        <div class="feed-controls">

            <div class="feed-metadata-container">
              <div class="feed-metadata">
                <div class="metadata-title">Sort Order</div>
                <div class="button-secondary select-list-gray">
                  Newest to Oldest
                </div>
              </div>
              <!-- <div class="feed-metadata">
                Filter
                <div class="button-secondary select-list-gray">
                  All Types
                </div>
              </div> -->
              <?php if ($last_page == 1 AND $current_page == 1) { ?>

              <?php } else { ?>
                <div class="date-range" data-sid="<?=$start_date_raw?>" data-eid="<?=$end_date_raw?>">
                  <div class="metadata-title">Post Date</div>
                  <?php if ($total_count == 30) { ?>
                    <a href="<?=$url_prev?>"><div class="show-older-link-small"><img class="icon-button-arrow icon-left svg" src="img/arrow-down.svg"></div></a>
                  <?php } ?>
                  <div class="button-secondary select-list-gray">
                    <?php if ($end_date == $start_date) { ?>
                      <span class="end-date"><?=$end_date?></span>
                    <?php } else { ?>
                      <span class="end-date"><?=$end_date?></span> - <span class="start-date"><?=$start_date?></span>
                    <?php } ?>
                  </div>
                  <?php if ($current_page >= 2) { ?>
                    <a href="<?=$url_next?>"><div class="show-newer-link-small"><img class="icon-button-arrow icon-right svg" src="img/arrow-down.svg"></div></a>
                  <?php } ?>
                </div>
              <?php } ?>
            </div>


        </div>
        <?php } ?>
      </div>
      <div class="clear"></div>
      </div>
      </div>
      <div id="feed-view">
        <div class="grid">
      <?php } ?>

      <?php if ($page == 'members') { ?>
        <div class="feed-title-wrapper">
          <?php if ($search_term) { ?>
            <div class="follow-title">
              <?php if ($partner_logo) { ?>
                <div class="feed-logo"><img class="logo-partner" src="img/<?=$partner_logo?>"></div>
              <?php } ?>
          <?php } else { ?>
            <div class="feed-title">
              <?php if ($partner_logo) { ?>
                <div class="feed-logo"><a href="feed.php?id=<?=$feed_number?>"><img class="logo-partner" src="img/<?=$partner_logo?>"></a></div>
              <?php } ?>
          <?php } ?>
            <?php if ($search_term) { ?>
              <h1><?=$partner_name?></h1>
            <?php } else { ?>
              <h1><a href="feed.php?id=<?=$feed_number?>"><?=$partner_name?> Community</a></h1>
            <?php } ?>
            <?php if ($_SESSION['mobile']) { } else { ?>
              <?php if ($partner_description AND $current_page == 1) { ?>
                <div class="feed-description"><?=$partner_description?></div>
              <?php } ?>
            <?php } ?>
            <div class="clear"></div>
            <?php if ($feed_number != "0") { ?>
              <ul class="menu-sub-community">
              <li><a href="feed.php?id=<?=$feed_number?>">Posts & Questions</a></li>
              <li class="menu-sub-current"><a href="members.php?id=<?=$feed_number?>">Community Members</a></li>
              </ul>
            <?php } ?>
          </div>
          <?php if ($_SESSION['mobile']) { } else { ?>
            <div class="feed-controls">
            <?php if ($total_count > 0) { ?>
            <div class="feed-metadata-container">
              <div class="feed-metadata">
                <div class="metadata-title">Sort Order</div>
                <div class="button-secondary select-list-gray">
                  Most Relevant
                </div>
              </div>
              <!-- <div class="feed-metadata">
                Filter
                <div class="button-secondary select-list-gray">
                  All Types
                </div>
              </div> -->
              <?php if ($last_page == 1 AND $current_page == 1) { ?>

              <?php } else { ?>
              <div class="member-range" data-sid="<?=$start_date_raw?>" data-eid="<?=$end_date_raw?>">
                <div class="metadata-title">First Name</div>
                <?php if ($total_count == 30) { ?>
                  <a href="<?=$url_prev?>"><div class="show-older-link-small"><img class="icon-button-arrow icon-left svg" src="img/arrow-down.svg"></div></a>
                <?php } ?>
                <div class="button-secondary select-list-gray">
                  <?php if ($from_first_name == $to_first_name) { ?>
                    <span class="end-date"><?=ucwords($from_first_name)?></span>
                  <?php } else { ?>
                    <span class="end-date"><?=ucwords($from_first_name)?></span> - <span class="start-date"><?=ucwords($to_first_name)?></span>
                  <?php } ?>
                </div>
                <?php if ($current_page >= 2) { ?>
                  <a href="<?=$url_next?>"><div class="show-newer-link-small"><img class="icon-button-arrow icon-right svg" src="img/arrow-down.svg"></div></a>
                <?php } ?>
              </div>
              <?php } ?>
            </div>
            <?php } ?>
            <div class="clear"></div>
          </div>
        <?php } ?>
        <div id="members-view">
          <div class="grid">
      <?php } ?>

      <?php if ($view == 'inbox') { ?>

        <div id="messages-inbox">
        	<div class="page-title no-head">
        		<img class="icon-large" src="img/icon-messages-inbox.svg">
        		<h1>Conversations</h1>
        	</div>
        	<a href="messages-new.php" id="button-new-message" class="button right">New<span class="hide-mobile"> Message</span></a>
      	</div>

        <?php if ($total_count > 0) { ?>

        <div class="feed-metadata-container">
          <div class="feed-metadata">
            <div class="metadata-title">Sort Order</div>
            <div class="button-secondary select-list-gray">
              Newest to Oldest
            </div>
          </div>
          <?php if ($last_page == 1 AND $current_page == 1) { ?>

          <?php } else { ?>
            <div class="date-range">
              <?php if ($total_count == 10) { ?>
                <a href="<?=$url_prev?>"><div class="show-older-link-small"><img class="icon-button-arrow icon-left svg" src="img/arrow-down.svg"></div></a>
              <?php } ?>

              <?php if ($current_page >= 2) { ?>
                <a href="<?=$url_next?>"><div class="show-newer-link-small"><img class="icon-button-arrow icon-right svg" src="img/arrow-down.svg"></div></a>
              <?php } ?>
            </div>
          <?php } ?>
        </div>

        <?php } ?>

        <div class="clear"></div>

      <?php } ?>

      <?php if ($page == 'notifications') { ?>
      <div id="notifications">

              <div class="page-title no-head">
                <h1>Your Notifications</h1>
                <div class="post-display-explanation">
                  <?php if ($_SESSION['mobile']) { } else { ?>
                  Notifications keep you informed about new followers, comments on your posts, and answers to your questions. <a href="<?= site_url()?>/edit-notifications.php">Click here to manage your email notifications.</a>
                </div>
                <div class="feed-metadata-container">
                  <div class="feed-metadata">
                    <div class="metadata-title">Sort Order</div>
                    <div class="button-secondary select-list-gray">
                      Newest to Oldest
                    </div>
                  </div>
                  <!-- <div class="feed-metadata">
                    Filter
                    <div class="button-secondary select-list-gray">
                      All Types
                    </div>
                  </div> -->
                  <div class="date-range" data-sid="<?=$start_date_raw?>" data-eid="<?=$end_date_raw?>">
                    <div class="metadata-title">Notification Date</div>
                    <?php if ($total_count == 20) { ?>
                      <a href="<?=$url_prev?>"><div class="show-older-link-small"><img class="icon-button-arrow icon-left svg" src="img/arrow-down.svg"></div></a>
                    <?php } ?>
                    <div class="button-secondary select-list-gray">
                      <?php if ($end_date == $start_date) { ?>
                        <span class="end-date"><?=$end_date?></span>
                      <?php } else { ?>
                        <span class="end-date"><?=$end_date?></span> - <span class="start-date"><?=$start_date?></span>
                      <?php } ?>
                    </div>
                    <?php if ($current_page >= 2) { ?>
                      <a href="<?=$url_next?>"><div class="show-newer-link-small"><img class="icon-button-arrow icon-right svg" src="img/arrow-down.svg"></div></a>
                    <?php } ?>
                  </div>
                </div>
                <?php } ?>
              </div>

      <?php } ?>

      <?php if ($page == 'posts') { ?>
        <div class="feed-title-wrapper">
          <div class="follow-title">
            <div class="profile-page-header">
              <a href="posts.php?id=<?=$user_id?>"><div class="post-header profile-pic">
                <?php if ( (strpos($user_avatar, 'Object') == false) AND ($user_avatar != NULL) ) { ?>
                  <?php if ($user_id == $_SESSION['uid']) { ?>
                    <img class="avatar large" src="image.php?id=<?=$user_avatar?>&width=300">
                  <?php } else { ?>
                    <img class="avatar large" src="image.php?id=<?=$user_avatar?>&width=300">
                  <?php } ?>
                <?php } else { ?>
                  <?php if ($user_id == $_SESSION['uid']) { ?>
                      <img class="avatar large" src="img/robot.svg">
                  <?php } else { ?>
                    <img class="avatar large" src="img/robot.svg">
                  <?php } ?>
                <?php } ?>
              </div></a>
              <?php if ($_SESSION['uid'] == $_GET["id"]) { ?>
                <a href="posts.php?id=<?=$user_id?>"><h1>Your Posts</h1></a>
              <?php } else { ?>
                <a href="posts.php?id=<?=$user_id?>"><h1>Posts by <?=$fullName?></h1></a>
              <?php } ?>
              <div class="post-display-explanation">
                <?=$posts_display_explanation?>
              </div>
              <?php if ($total_count > 0) { ?>
              <div class="feed-metadata-container">
                <div class="feed-metadata">
                  <div class="metadata-title">Sort Order</div>
                  <div class="button-secondary select-list-gray">
                    Newest to Oldest
                  </div>
                </div>
                <!-- <div class="feed-metadata">
                  Filter
                  <div class="button-secondary select-list-gray">
                    All Types
                  </div>
                </div> -->
                <div class="date-range" data-sid="<?=$start_date_raw?>" data-eid="<?=$end_date_raw?>">
                  <div class="metadata-title">Post Date</div>
                  <?php if ($total_count == 30) { ?>
                    <a href="<?=$url_prev?>"><div class="show-older-link-small"><img class="icon-button-arrow icon-left svg" src="img/arrow-down.svg"></div></a>
                  <?php } ?>
                  <div class="button-secondary select-list-gray">
                    <?php if ($end_date == $start_date) { ?>
                      <span class="end-date"><?=$end_date?></span>
                    <?php } else { ?>
                      <span class="end-date"><?=$end_date?></span> - <span class="start-date"><?=$start_date?></span>
                    <?php } ?>
                  </div>
                  <?php if ($current_page >= 2) { ?>
                    <a href="<?=$url_next?>"><div class="show-newer-link-small"><img class="icon-button-arrow icon-right svg" src="img/arrow-down.svg"></div></a>
                  <?php } ?>
                </div>
              </div>
              <?php } ?>
              <div class="clear"></div>
            </div>
          </div>
        </div>
        <div id="feed-view">
          <div class="grid">
      <?php } ?>

      <?php if ($page == 'following') { ?>
        <div class="feed-title-wrapper">
          <div class="follow-title">
            <div class="profile-page-header">
              <div class="post-header profile-pic">
                <?php if ( (strpos($user_avatar, 'Object') == false) AND ($user_avatar != NULL) ) { ?>
                  <?php if ($user_id == $_SESSION['uid']) { ?>
                    <img class="avatar large" src="image.php?id=<?=$user_avatar?>&width=300">
                  <?php } else { ?>
                    <img class="avatar large" src="image.php?id=<?=$user_avatar?>&width=300">
                  <?php } ?>
                <?php } else { ?>
                  <?php if ($user_id == $_SESSION['uid']) { ?>
                      <img class="avatar large" src="img/robot.svg">
                  <?php } else { ?>
                    <img class="avatar large" src="img/robot.svg">
                  <?php } ?>
                <?php } ?>
              </div>
              <?php if ($_SESSION['uid'] == $_GET["id"]) { ?>
                <h1>People You Follow</h1>
              <?php } else { ?>
                <h1>People <?=$fullName?> Follows</h1>
              <?php } ?>
              <div class="post-display-explanation">
                <?=$posts_display_explanation?>
              </div>
              <?php if ($total_count > 0) { ?>
              <div class="feed-metadata-container">
                <div class="feed-metadata">
                  <div class="metadata-title">Sort Order</div>
                  <div class="button-secondary select-list-gray">
                    First Name Ascending
                  </div>
                </div>
                <!-- <div class="feed-metadata">
                  Filter
                  <div class="button-secondary select-list-gray">
                    All Types
                  </div>
                </div> -->
                <?php if ($last_page == 1 AND $current_page == 1) { ?>

                <?php } else { ?>
                  <div class="member-range" data-sid="<?=$start_date_raw?>" data-eid="<?=$end_date_raw?>">
                    <div class="metadata-title">First Name</div>
                    <?php if ($total_count == 30) { ?>
                      <a href="<?=$url_prev?>"><div class="show-older-link-small"><img class="icon-button-arrow icon-left svg" src="img/arrow-down.svg"></div></a>
                    <?php } ?>
                    <div class="button-secondary select-list-gray">
                      <?php if ($from_first_name == $to_first_name) { ?>
                        <span class="end-date"><?=ucwords($from_first_name)?></span>
                      <?php } else { ?>
                        <span class="end-date"><?=ucwords($from_first_name)?></span> - <span class="start-date"><?=ucwords($to_first_name)?></span>
                      <?php } ?>
                    </div>
                    <?php if ($current_page >= 2) { ?>
                      <a href="<?=$url_next?>"><div class="show-newer-link-small"><img class="icon-button-arrow icon-right svg" src="img/arrow-down.svg"></div></a>
                    <?php } ?>
                  </div>
                <?php } ?>
              </div>
              <?php } ?>
              <div class="clear"></div>
            </div>
          </div>
        </div>
        <div id="feed-view">
          <div class="grid">
      <?php } ?>

      <?php if ($page == 'followers') { ?>
        <div class="feed-title-wrapper">
          <div class="follow-title">
            <div class="profile-page-header">
              <div class="post-header profile-pic">
                <?php if ( (strpos($user_avatar, 'Object') == false) AND ($user_avatar != NULL) ) { ?>
                  <?php if ($user_id == $_SESSION['uid']) { ?>
                    <img class="avatar large" src="image.php?id=<?=$user_avatar?>&width=300">
                  <?php } else { ?>
                    <img class="avatar large" src="image.php?id=<?=$user_avatar?>&width=300">
                  <?php } ?>
                <?php } else { ?>
                  <?php if ($user_id == $_SESSION['uid']) { ?>
                      <img class="avatar large" src="img/robot.svg">
                  <?php } else { ?>
                    <img class="avatar large" src="img/robot.svg">
                  <?php } ?>
                <?php } ?>
              </div>
              <?php if ($_SESSION['uid'] == $_GET["id"]) { ?>
                <h1>People Following You</h1>
              <?php } else { ?>
                <h1>People Following <?=$fullName?></h1>
              <?php } ?>
              <div class="post-display-explanation">
                <?=$posts_display_explanation?>
              </div>
              <?php if ($total_count > 0) { ?>
              <div class="feed-metadata-container">
                <div class="feed-metadata">
                  <div class="metadata-title">Sort Order</div>
                  <div class="button-secondary select-list-gray">
                    First Name Ascending
                  </div>
                </div>
                <!-- <div class="feed-metadata">
                  Filter
                  <div class="button-secondary select-list-gray">
                    All Types
                  </div>
                </div> -->
                <?php if ($last_page == 1 AND $current_page == 1) { ?>

                <?php } else { ?>
                  <div class="member-range" data-sid="<?=$start_date_raw?>" data-eid="<?=$end_date_raw?>">
                    <div class="metadata-title">First Name</div>
                    <?php if ($total_count == 30) { ?>
                      <a href="<?=$url_prev?>"><div class="show-older-link-small"><img class="icon-button-arrow icon-left svg" src="img/arrow-down.svg"></div></a>
                    <?php } ?>
                    <div class="button-secondary select-list-gray">
                      <?php if ($from_first_name == $to_first_name) { ?>
                        <span class="end-date"><?=ucwords($from_first_name)?></span>
                      <?php } else { ?>
                        <span class="end-date"><?=ucwords($from_first_name)?></span> - <span class="start-date"><?=ucwords($to_first_name)?></span>
                      <?php } ?>
                    </div>
                    <?php if ($current_page >= 2) { ?>
                      <a href="<?=$url_next?>"><div class="show-newer-link-small"><img class="icon-button-arrow icon-right svg" src="img/arrow-down.svg"></div></a>
                    <?php } ?>
                  </div>
                <?php } ?>
              </div>
              <?php } ?>
              <div class="clear"></div>
            </div>
          </div>
        </div>
        <div id="feed-view">
          <div class="grid">
      <?php } ?>
