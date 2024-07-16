
    <?php if ($page == 'reset-start') { // Display account ready message ?>
      <img class="login-callout-img-logo" src="img/tclogo.svg" alt="Teachers Connect logo">
      <img class="login-callout-img-alert" src="img/icon-alert-green.svg" alt="icon alert green">
      <div class="login-callout-alert-text">
        <h2 class="login-callout-alert green">Password reset started</h2>
        Please check your email inbox for further instructions on how to reset your TeachersConnect account password.<br><br>Remember to check your spam folder if it looks like you haven't received our response. If you need further help, please <a target="_blank" href="http://www.teachersconnect.com/support-request/">contact us here</a>.
      </div>
    <?php } ?>

    <?php if ($page == 'reset-pass') { // Display password confirmation form ?>
      <div class="login-form reset-pass">
        <img class="login-callout-img-logo" src="img/tclogo.svg" alt="Teachers Connect logo">
        <div class="login-form-block">
          <h2>Reset your password</h2>
          Please provide a new password to reset on your account.<br><br>
          <form method="post">
            <label for="user">New Password</label>
            <input type="password" id="user" name="pass1" value=""><br><br>
            <label for="user">Confirm New Password</label>
            <input type="password" id="user" name="pass2" value="">
            <input type="submit" value="Reset">
            <input type="hidden" id="id" name="id" value="<?=$id?>">
          </form>
        </div>
      </div>
    <?php } ?>

    <?php if ($page == 'reset-request') { // Display password reset form ?>
      <div class="login-form reset-pass">
        <img class="login-callout-img-logo" src="img/tclogo.svg" alt="Teachers Connect logo">
        <div class="login-form-block">
          <h2>Reset your password</h2>
          Please provide the email address for the account you want to reset.<br><br>
          <form method="post">
            <label for="user">Email Address</label>
            <input type="text" id="user" name="user" value="<?=$email?>">
            <input type="submit" value="Reset">
          </form>
        </div>
      </div>
    <?php } ?>


    <?php if ($page == 'reset-error') { // Display reset error ?>
      <div class="login-form reset-pass">
        <img class="login-callout-img-logo" src="img/tclogo.svg" alt="Teachers Connect logo">
        <div class="login-form-block">
          <h2>Error</h2>
          There was an error processing your request due to an invalid or expired link. Please try again.<br><br>
        </div>
      </div>
    <?php } ?>

    <?php if ($page == 'reset-error-pass') { // Display reset error ?>
      <div class="login-form reset-pass">
        <img class="login-callout-img-logo" src="img/tclogo.svg" alt="Teachers Connect logo">
        <div class="login-form-block">
          <h2>Error</h2>
          There was an error processing your request due to non-matching or empty passwords. Please go back and ensure your new password is entered correctly.<br><br>
        </div>
      </div>
    <?php } ?>
