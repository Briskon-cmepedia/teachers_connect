
    <div class="login-become-member">
      <img class="login-callout-img-logo-horizontal" src="img/tclogo.svg" alt="Teachers Connect logo">
      <img class="login-callout-img-logo-horizontal" src="https://www.teachersconnect.com/wp-content/uploads/2017/06/TC-Online-Mac-Tab-Phone-1024x470.png" alt="image. devices">
      <form id="accordion-form" name="registration-form" class="login-form-block form-signup submit-once" method="post">
        <div class="accordion-tab<?php if ($formSubmission == 'fail' AND ((empty(trim($firstName))) OR (empty(trim($lastName))) OR (empty(trim($user))) OR (!filter_var($user, FILTER_VALIDATE_EMAIL)) OR (empty(trim($pass))) OR !$termsAgreement)) echo ' error'; ?>" id="tab-identity">Become a Member Now</div>
        <div class="accordion-panel panel-identity">
          <div class="form-node<?php if ($formSubmission == 'fail' AND (empty(trim($firstName)))) {echo ' error'; } elseif($formSubmission== 'fail_username' AND !$firstNameCheck){ echo " error"; }  ?>">
            <label class="create-account-label" for="firstName">First Name</label>
            <input type="text" id="firstName" name="firstName" value="<?=$firstName?>">            
            <span style="color:#ca461c;" id='fail_firstname' ><?php if($formSubmission== 'fail_username' AND !$firstNameCheck){ ?>2-25 characters, must start with a letter<?php }
            ?></span>            
          </div>
          <div class="form-node<?php if ($formSubmission == 'fail' AND (empty(trim($lastName)))) {echo ' error';} elseif($formSubmission== 'fail_username' AND !$lastNameCheck){ echo " error"; }  ?>">
            <label class="create-account-label" for="lastName">Last Name</label>
            <input type="text" id="lastName" name="lastName" value="<?=$lastName?>">            
            <span style="color:#ca461c;" id='fail_lastname'><?php if($formSubmission== 'fail_username' AND !$lastNameCheck){ ?>2-25 characters, must start with a letter<?php }
            ?></span>            
          </div>
          <div class="form-node<?php if ($formSubmission == 'fail' AND ((empty(trim($user))) OR (!filter_var($_POST['user'], FILTER_VALIDATE_EMAIL)))) echo ' error'; ?>">
            <label class="create-account-label" for="user">Email Address</label>
            <input type="text" id="user" name="user" value="<?=$user?>">
            <label class="create-account-label" class="form-check" for="user-check">Confirm Email Address</label>
            <input type="text" id="user-check" name="user-check" value="">
          </div>
          <div class="form-node<?php if ($formSubmission == 'fail' AND (empty(trim($pass)))) echo ' error'; ?>">
            <label class="create-account-label" for="pass">Password</label>
            <input type="password" id="pass" name="pass" value="<?=$pass?>">
            <label class="create-account-label" class="form-check" for="pass-check">Confirm Password</label>
            <input type="password" id="pass-check" name="pass-check" value="">
          </div>
          <input type="hidden" id="userType" name="userType" value="other">
          <input type="hidden" id="userRef" name="userRef" value="<?=$refid?>">
          <div class="form-node<?php if ($formSubmission == 'fail' AND !$termsAgreement) echo ' error'; ?>">
            <input type="checkbox" id="termsAgreement" name="termsAgreement" <?php if ($termsAgreement) echo 'checked'; ?>>
            <label for="termsAgreement">I agree to the TeachersConnect <a class="terms-agreement" 
            target="_blank" href="<?php echo Config::MARKETING_URL; ?>/terms-of-use/">Terms of Use</a> and <a class="terms-agreement" target="_blank" href="<?php echo Config::MARKETING_URL; ?>/privacy-policy/">Privacy Policy</a>.</label>
          </div>
          <input class="registration-form-submit" name="registration-form-submit" type="submit" value="Create">
        </div>
      </form>
    </div>

    

    <!-- <hr class="clear">
    <div>
      <h1 style="text-align: center;">What is TeachersConnect?</h1>
<p>TeachersConnect is an online community for any teacher and those who prepare, support, and empower them in the classroom. The vibrant community grants teachers access to the most powerful resource of all: each other.</p>
<p>Share ideas and collaborate with other teachers through group discussions and community posts on any device. We believe that every teacher has something to contribute and every teacher who joins makes our teaching community more powerful.</p>
    </div> -->
