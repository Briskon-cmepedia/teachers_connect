
  <div class="gaw-signup">
    <img class="signup-img-horizontal" src="img/tclogo.svg" alt="Teachers Connect logo">
    <img class="signup-img-horizontal mobile-fullwidth-1024" src="/img/gaw-banner-nt-1.png">
    <div class="signup-text permanent">
      <div class="permanent text0">
        Jenny was looking for answers. She was in a challenging learning environment and didn’t feel supported. Frustrated and desperate for a solution, she reached out to the experienced teachers on TeachersConnect.
        <br>
        <div class="center">
          <h2>How TeachersConnect helped Jenny</h2>
        </div>
        <img class="signup-img-inline mobile-fullwidth-340" src="/img/gaw-feature-1-nt-1.png">The community suggested a range of behavioral strategies she could immediately use in her classroom—including how to convey and apply consequences, and how to decide the best time and method to bring problems to her administration. Community members offered advice on how to approach and engage fellow teachers to help her manage the situation in her classroom.
        <br><br>
        Jenny got thoughtful and direct answers from experienced teachers. She was able to ask follow-up questions on TeachersConnect and exchange private messages with the teachers whose advice she valued most.
        <br>
        <div class="center">
          <h2>What changes happened for Jenny</h2>
        </div>
        <img class="signup-img-inline right mobile-fullwidth-340" src="/img/gaw-feature-2-nt-1.png">Instead of feeling frustrated and overwhelmed, she felt empowered and confident. She implemented several of the suggestions and ultimately found a colleague who was very helpful.
        <br><br>
        By regularly practicing and tracking how her strategies in the classroom were working, Jenny reduced the disruptions and provided a more inclusive and learning-friendly environment.
        <br><br>
        <div class="center">
          <h2>How can TeachersConnect help you?</h2>
        </div>
        As a new teacher, navigating myriad classroom issues can be challenging—and sometimes overwhelming. Become part of an online community of experienced teachers who can help you develop and grow as an educator. Become that teacher who your students remember forever.
        <br><br>
        <div class="center">
          <h2 class="title-tight">Empower yourself today!</h2>
          <h4 class="title-tight">Build a professional learning network that has your back 24/7.</h4>
          <!-- Join thousands of teachers for less than you tip your server - only $3/month. -->
          <br><br>
          <div class="button join-now">Join Now</div><div class="button secondary show-features">Learn More</div>
        </div>
      </div>
      <br><br>
    </div>
  </div>

  <div class="gaw-signup" id="features-overview">
    <div class="signup-text permanent">
      <div class="permanent text0">
        <div class="center">
          <h2>Personal… Responsive… Community.</h2>
        </div>
        TeachersConnect is an online community for prospective and practicing teachers and those who prepare, support, and empower them. The relentlessly positive community grants teachers access to the most powerful resource of all: other teachers.
        <br><br>
        <div class="row">
          <div class="column mobile-fullwidth-480">
            <img src="https://www.teachersconnect.com/wp-content/uploads/2020/01/icon-moderated-community.png" alt="" width="60" height="60">
            <strong>Moderated Community</strong><br>
            Real names. Real people. No advertising. No BS.
          </div>
          <div class="column mobile-fullwidth-480">
            <img src="https://www.teachersconnect.com/wp-content/uploads/2020/01/icon-topic-feeds.png" alt="" width="63" height="60">
            <strong>Topic Feeds</strong><br>
            Filter out the noise. Find just what you need.
          </div>
          <div class="column mobile-fullwidth-480">
            <img src="https://www.teachersconnect.com/wp-content/uploads/2020/01/icon-community-groups.png" alt="" width="60" height="60">
            <strong>Community Groups</strong><br>
            Find kindred spirits in organized affiliations.
          </div>
        </div>
        <div class="row">
          <div class="column mobile-fullwidth-480">
            <img src="https://www.teachersconnect.com/wp-content/uploads/2020/01/icon-anonymous-questions.png" alt="" width="76" height="62">
            <strong>Anonymous Questions</strong><br>
            Go on. Ask the hard questions. We’ve got your back.
          </div>
          <div class="column mobile-fullwidth-480">
            <img src="https://www.teachersconnect.com/wp-content/uploads/2020/01/icon-custom-homepage.png" alt="" width="60" height="60">
            <strong>Custom Homepage</strong><br>
            Stay updated on the people and topics you follow.
          </div>
          <div class="column mobile-fullwidth-480">
            <img src="https://www.teachersconnect.com/wp-content/uploads/2020/01/icon-site-search.png" alt="" width="66" height="66">
            <strong>Site Search</strong><br>
            Get instant answers by searching our archives.
          </div>
        </div>
        <div class="row">
          <div class="column mobile-fullwidth-480">
            <img src="https://www.teachersconnect.com/wp-content/uploads/2020/01/icon-member-profiles.png" alt="" width="60" height="60">
            <strong>Member Profiles</strong><br>
            Learn more about other members quickly.
          </div>
          <div class="column mobile-fullwidth-480">
            <img src="https://www.teachersconnect.com/wp-content/uploads/2020/01/icon-private-messages.png" alt="" width="65" height="60">
            <strong>Private Messages</strong><br>
            Connect with others, individually or in groups.
          </div>
          <div class="column mobile-fullwidth-480">
            <img src="https://www.teachersconnect.com/wp-content/uploads/2020/01/icon-email-notifications.png" alt="" width="53" height="60">
            <strong>Email Notifications</strong><br>
            Get notified about new activity on your interests.
          </div>
        </div>
        <div class="row">
          <div class="center">
            <h2>Find solutions to your problems.</h2>
            <div class="button join-now">Join Now</div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="gaw-signup-form" id="register-form">
    <form id="accordion-form" name="registration-form" class="login-form-block form-signup submit-once" method="post">
      <div class="accordion-tab<?php if ($formSubmission == 'fail' AND ((empty(trim($firstName))) OR (empty(trim($lastName))) OR (empty(trim($user))) OR (!filter_var($user, FILTER_VALIDATE_EMAIL)) OR (empty(trim($pass))) OR !$termsAgreement)) echo ' error'; ?>" id="tab-identity">Become a Member Now</div>
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
        <input type="hidden" id="userRef" name="userRef" value="<?=$refid?>">
        <div class="form-node<?php if ($formSubmission == 'fail' AND !$termsAgreement) echo ' error'; ?>">
          <input type="checkbox" id="termsAgreement" name="termsAgreement" <?php if ($termsAgreement) echo 'checked'; ?>>
          <label for="termsAgreement">I agree to the TeachersConnect <a target="_blank" href="http://www.teachersconnect.com/terms-of-use/">Terms of Use</a> and <a target="_blank" href="http://www.teachersconnect.com/privacy-policy/">Privacy Policy</a>.</label>
        </div>
        <input name="registration-form-submit" type="submit" value="Create">
      </div>
    </form>
  </div>

  <div class="footer-space">&nbsp;</div>
