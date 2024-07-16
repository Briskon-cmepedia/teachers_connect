
    <div class="login-callout">
        <img class="login-callout-img-logo" src="img/tclogo2.svg" alt="Teachers Connect logo">
        <h1>Create Your Account</h1>
        <div class="login-callout-text">
          <div class="supplement text0">
            <h2>Step 1</h2>
            <h3>Your Identity Information</h3>
            We use your full name and profile photo to represent you to other members in the community.
            <br><br>
            Your email address and password is private and not published to other members.  You use your email address to login to TeachersConnect.
            <br><br>
            <span class="note">For more information about how TeachersConnect manages member data and individual privacy please <a href="http://www.teachersconnect.com/privacy-policy/" target="_blank">view our privacy policy</a>.</span>
          </div>
          <div class="supplement text1">
            <h2>Step 2</h2>
            <h3>Your Current Role</h3>
            We display information about your current teaching role so that other teachers with complimentary expertise and interests can find you.
            <br><br>
            We also use your grade and subject information to personalize the content that is displayed to you in the community.
          </div>
          <div class="supplement text2">
            <h2>Step 3</h2>
            <h3>Your Teaching License</h3>
            We use this information to inform you of fellow alumni in our community and to provide you with opportunities to interact with them.
            <br><br>
            All members of TeachersConnect are required to read, understand and follow our <a target="_blank" href="http://www.teachersconnect.com/terms-of-use/">Terms of Use</a> and <a target="_blank" href="http://www.teachersconnect.com/privacy-policy/">Privacy Policy</a>.  These rules have been setup to ensure our community is safe and respectful for everybody.
          </div>
        </div>
    </div>

    <div class="login-form">
      <form id="accordion-form" name="registration-form" class="login-form-block form-signup submit-once" method="post">
        <div class="accordion-tab<?php if ($formSubmission == 'fail' AND ((empty(trim($firstName))) OR (empty(trim($lastName))) OR (empty(trim($user))) OR (!filter_var($user, FILTER_VALIDATE_EMAIL)) OR (empty(trim($pass))))) echo ' error'; ?>" id="tab-identity"><div class="circle">1</div> Your Identity Information</div>
        <div class="accordion-panel panel-identity">
          <div class="form-node<?php if ($formSubmission == 'fail' AND (empty(trim($firstName)))) echo ' error'; ?>">
            <label for="firstName">First Name</label>
            <input type="text" id="firstName" name="firstName" value="<?=$firstName?>">
          </div>
          <div class="form-node<?php if ($formSubmission == 'fail' AND (empty(trim($lastName)))) echo ' error'; ?>">
            <label for="lastName">Last Name</label>
            <input type="text" id="lastName" name="lastName" value="<?=$lastName?>">
          </div>
          <div class="form-node<?php if ($formSubmission == 'fail' AND ((empty(trim($user))) OR (!filter_var($_POST['user'], FILTER_VALIDATE_EMAIL)))) echo ' error'; ?>">
            <label for="user">Email Address</label>
            <input type="text" id="user" name="user" value="<?=$user?>">
            <label class="form-check" for="user-check">Confirm Email Address</label>
            <input type="text" id="user-check" name="user-check" value="">
          </div>
          <div class="form-node<?php if ($formSubmission == 'fail' AND (empty(trim($pass)))) echo ' error'; ?>">
            <label for="pass">Password</label>
            <input type="password" id="pass" name="pass" value="<?=$pass?>">
            <label class="form-check" for="pass-check">Confirm Password</label>
            <input type="password" id="pass-check" name="pass-check" value="">
          </div>
          <input type="hidden" id="userType" name="userType" value="other">
          <button type="button" class="next">Next</button>
        </div>
        <div class="accordion-tab" id="tab-role"><div class="circle">2</div> Your Current Role</div>
        <div class="accordion-panel panel-role">
          <div class="form-node">
            <label for="teachLocation">Where do you currently teach?</label>
            <input type="text" id="teachLocationName" name="teachLocationName" value="<?=$teachLocationName?>" placeholder="optional">
            <input class="half-width"  type="hidden" id="teachLocationCity" name="teachLocationCity" value="<?=$teachLocationCity?>">
            <input class="half-width"  type="hidden" id="teachLocationState" name="teachLocationState" value="<?=$teachLocationState?>">
            <input type="hidden" id="teachLocationCountry" name="teachLocationCountry" value="<?=$teachLocationCountry?>">
          </div>
          <div class="form-node">
            <label for="teachGrades">What grade(s) do you teach?</label>
            <div class="form-supplement">Select multiples</div>
            <input type="text" id="teachGrades" name="teachGrades" value="<?=$teachGrades?>" placeholder="optional">
          </div>
          <div class="form-node">
            <label for="teachSubjects">What subjects(s) do you teach?</label>
            <div class="form-supplement">Select multiples</div>
            <input type="text" id="teachSubjects" name="teachSubjects" value="<?=$teachSubjects?>" placeholder="optional">
          </div>
          <div class="form-node">
            <label for="teachLength">How much experience do you have teaching there?</label>
            <input class="half-width" type="text" id="teachStart" name="teachStart" value="<?=$teachStart?>" placeholder="Start (optional)">
            <input class="half-width" type="text" id="teachEnd" name="teachEnd" value="<?=$teachEnd?>" placeholder="End (optional)">
          </div>
          <div class="clear">&nbsp;</div>
          <button type="button" class="next">Next</button>
        </div>
        <div class="accordion-tab<?php if ($formSubmission == 'fail' AND !$termsAgreement) echo ' error'; ?>" id="tab-license"><div class="circle">3</div> Your Teaching License</div>
        <div class="accordion-panel panel-license">
          <div class="form-node">
            <label for="teachLicenseLocation">Where did you earn your initial teaching license?</label>
            <input type="text" id="teachLicenseLocation" name="teachLicenseLocation" value="<?=$teachLicenseLocation?>" placeholder="optional">
          </div>
          <div class="form-node">
            <label for="teachLicenseComplete">What year did you complete your program there?</label>
            <input type="text" id="teachLicenseComplete" name="teachLicenseComplete" value="<?=$teachLicenseComplete?>" placeholder="optional">
          </div>
          <hr>
          <div class="form-node<?php if ($formSubmission == 'fail' AND !$termsAgreement) echo ' error'; ?>">
            <input type="checkbox" id="termsAgreement" name="termsAgreement" <?php if ($termsAgreement) echo 'checked'; ?>>
            <label for="termsAgreement">I agree to the TeachersConnect <a target="_blank" href="http://www.teachersconnect.com/terms-of-use/">Terms of Use</a> and <a target="_blank" href="http://www.teachersconnect.com/privacy-policy/">Privacy Policy</a>.</label>
          </div>
          <input name="registration-form-submit" type="submit" value="Finish">
        </div>
      </form>
    </div>
