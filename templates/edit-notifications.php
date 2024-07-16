<div id="profile-header"></div>
<div class="profile-body">
	<div class="user-profile-edit">
	<div class="page-title no-head">
		<a href="profile.php?id=<?=$_SESSION['uid']?>"><div class="button-secondary back-profile"><img class="icon-button-arrow icon-left svg" src="img/arrow-down.svg" alt="icon. arrow down"> Back to Profile</div></a>
		<h1>Email Notifications</h1>
	</div>

<div class="page-section-explanation">Get notified by email when important events happen. Use the settings below to control when you are notified.</div>

<div class="user-profile no-padding">
	<form id="edit-notification-settings" name="edit-notification-settings" method="post" class="list-settings">
		<div class="form-node">
			<label>I want to receive email notifications: </label></br>
		</div>
		<div class="form-node">
			<label class="checkbox"><input type='hidden' value='0' name='comment'><input type="checkbox" name="comment" value="1" <?php if($comment == true) echo "checked"; ?>> When someone comments on a post I made.</label></br>
		</div>
		<div class="form-node">
			<label class="checkbox"><input type='hidden' value='0' name='answer'><input type="checkbox" name="answer" value="1" <?php if($answer == true) echo "checked"; ?>> When someone responds to a question I asked.</label></br>
		</div>
		<div class="form-node">
			<label class="checkbox"><input type='hidden' value='0' name='interact'><input type="checkbox" name="interact" value="1" <?php if($interact == true) echo "checked"; ?>> When someone responds to another member's post I have contributed to.</label></br>
		</div>
		<div class="form-node">
			<label class="checkbox"><input type='hidden' value='0' name='follow'><input type="checkbox" name="follow" value="1" <?php if($follow == true) echo "checked"; ?>> When someone follows me.</label></br>
		</div>
		<br><br>
		<div class="form-node center">
			<input type="submit" class="button-comment" value="Save" disabled="disabled">
		</div>
	</form>
</div>
</div>
</div>
