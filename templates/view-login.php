
    <div class="login-callout">
      <?php if ($status == 'duplicate') { // Display duplicate email message ?>
        <img class="login-callout-img-alert" src="img/icon-alert-red.svg" alt="icon alert red">
        <div class="login-callout-alert-text">
          <h2 class="login-callout-alert red">An account with that email already exists.</h2>
          The email you provided while signing up already has an account on TeachersConnect. Please try logging in with your existing account password. If you continue to experience issues, please <a target="_blank" href="http://www.teachersconnect.com/support-request/">contact us here</a>.
        </div>
      <?php } elseif ($status == 'duplicate-unverified') { // Display duplicate email message ?>
        <img class="login-callout-img-alert" src="img/icon-alert-red.svg" alt="icon alert red">
        <div class="login-callout-alert-text">
          <h2 class="login-callout-alert red">An account with that email already exists.</h2>
          It looks like you've already created a TeachersConnect account. Use the button below to receive a fresh email link* to complete the registration process. ​
          <br><br>
	        <center><button type="button" style='border-color: #123b45;border: none;background:#123b45;color:#fff;text-decoration:none;;font-size:1.0em;padding:5px 10px;border-radius:4px;'  class="confirm-validation-popup" data-id="<?php echo $email;?>">Send registration email</button></center>
          <br><br>
          <small>
          *The link expires in one hour. Use the email you used to create the account. Sender is Dave from TeachersConnect. Check your junk folder. ​
          </small>
        </div>
      <?php }elseif ($status == 'duplicate-inactive') { // Display duplicate email message ?>
        <img class="login-callout-img-alert" src="img/icon-alert-red.svg" alt="icon alert red">
        <div class="login-callout-alert-text">
          <h2 class="login-callout-alert red">An account with that email already exists.</h2>
          Whoops! We've hit a snag. Send a note to <a target="_blank" href="mailto:hello@teachersconnect.com">hello@teachersconnect.com</a> and we'll get this figured out ASAP.
        </div>
      <?php }          
      elseif ($status == 'ready') { // Display account ready message ?>
        <img class="login-callout-img-alert" src="img/icon-alert-green.svg" alt="icon alert green">
        <div class="login-callout-alert-text">
          <h2 class="login-callout-alert green">You’ve successfully created your account.</h2>
          You are ready to join the community. Please sign in to confirm your account.
        </div>
      <?php } elseif ($status == 'reset') { // Display password reset message ?>
        <img class="login-callout-img-alert" src="img/icon-alert-green.svg" alt="icon alert green">
        <div class="login-callout-alert-text">
          <h2 class="login-callout-alert green">You’ve successfully reset your password.</h2>
          Welcome back to the community. Please sign in to confirm your new password is working correctly.
        </div>
      <?php } else { // Display welcome screen ?>
        <img class="login-callout-img-logo" src="img/tclogo2.svg" alt="Teachers Connect logo">
        <img class="login-callout-img-community" src="img/tccommunity.png" alt="image. community">
        <div class="login-callout-text">
          Uncompromisingly Teacher-centric. Moderated and trustworthy. No ads. No anonymous trolls. Only teachers solving classroom problems. Ask a question, share a victory, or seek a collaborator in a community of problem-solvers. Stay clear of the negativity and “venting” that can come with the Teachers’ Lounge.
          <br><br>
          <span class="note">TeachersConnect requires a modern web browser with cookies and javascript enabled.</span>
        </div>
      <?php } ?>
    </div>
    <div id="confirm-validation-popup" class="modal">  
      <div class="modal-content">Email successfully sent. Check your email inbox.</div>
    </div> 
    <div id="confirm-validation-error-popup" class="modal">  
      <div class="modal-content">Please try again!</div>
    </div>
    <div class="login-form">
      <div class="login-form-block">
        <h2>Sign in to TeachersConnect.</h2>
        <form method="post">
          <label for="user">Email Address</label>
          <input type="text" id="user" name="user" value="<?=$email?>">
          <label for="pass">Password</label>
          <input type="password" id="pass" name="pass">
          <input type="submit" value="Sign In">
        </form>
      </div>
      <?php if ($status != 'ready') { ?>
        <div class="login-form-signup">
          <h2>Not a member?  Join today.</h2>
          <a href="<?php site_url();?>/refer.php?ref=auth"><button>Sign Up</button></a>
        </div>
      <?php } ?>
    </div>
