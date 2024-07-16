<div id="profile-header"></div>
<div class="profile-body">
  <div class="user-profile-header">
    <div class="page-title no-head">
      <a href="profile.php?id=<?=$user_id?>"><div class="button-secondary back-profile"><img class="icon-button-arrow icon-left svg" src="img/arrow-down.svg" alt="icon. arrow down"> Back to Profile</div></a>
      <h1>Account Violations</h1>
    </div>

    <div class="page-section-explanation">
      As a community of educators, we have made a shared commitment that our space is a safe community in which each member is accountable for his or her contributions. This commitment is outlined in our community guidelines and you agreed to be bound by these terms when you created your account with us. It looks like your account might currently be in violation of this agreement.
    </div>

    <div class="page-section-text">
      <h4>What We Have Noticed</h4>
      <?php foreach ($violations as $violation) {
        if ($violation == 'realname') { ?>
          <div class="violation-alert"><img class="icon-violation" src="img/icon-identity.svg"><h3>Authentic Identity Violation<div class="subheading">It appears that your profile information may be incomplete or not authentic.</div></h3></div>
          Our guidelines require that you have an authentic identity online. People connect to other real, authentic people on TeachersConnect. When people stand behind their opinions and actions with their authentic name and reputation, our community is more accountable and so are your posts. Our real name requirement creates a safer environment for everybody.<br><br>
      <?php } } ?>
    </div>
    <div class="page-section-text">
      <h4>What Happens Now</h4>
      <?php foreach ($violations as $violation) {
        if ($violation == 'realname') { ?>
          <div class="violation-action"><img class="icon-violation" src="img/icon-alert-action.svg"><h3>Please verify your profile information is accurate and make any changes required.</h3></div>
          You must include your current full legal name in your profile first and last name fields. Initial letters, name abbreviations, business names, organization names, and non-name terms are not acceptable on individual educator accounts.<br><br><br>
          <div class="center"><a class="button" href="edit-information.php">Update Your Profile Now</a></div>
          <br><br>
          <span class="text-em">Note: Your site access and community visibility may be reduced while violations are active on your account.</span>
      <?php } } ?>
    </div>
    <div class="page-section-explanation note-boxed">
      If you have received this message in error or believe you have resolved this issue, please reach out to us at <a href="mailto:hello@teachersconnect.com">hello@teachersconnect.com</a>
    </div>
  </div>
</div>
