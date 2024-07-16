<div class="profile-body">

	<div class="user-profile messages-inbox no-padding">

		<div class="notifications-list">

			<div class="notification-top"></div>

			<?php foreach ($conversations as $conversation) { ?>

				<div class="notification conversation" data-pid="<?=$conversation['_id']['$oid']?>">
					<div class="notification-user-pic pic55">
						<?php if (count($conversation['otherParticipants']) == 0) { ?>
							<?php if ($_SESSION['avatar']) { ?>
								<img class="avatar" alt="avatar" src="image.php?id=<?=$_SESSION['avatar']?>&height=200" onerror="this.src='img/robot.svg'">
							<?php } else { ?>
								<img class="avatar" src="img/robot.svg" alt="robot avatar">
							<?php } ?>
						<?php } elseif (count($conversation['otherParticipants']) == 1) { ?>
							<?php if ($participantInfo[$conversation['otherParticipants'][0]]['avatar']) { ?>
								<img class="avatar" alt="avatar" src="image.php?id=<?=$participantInfo[$conversation['otherParticipants'][0]]['avatar']?>&height=200"  onerror="this.src='img/robot.svg'">
							<?php } else { ?>
								<img class="avatar" src="img/robot.svg" alt="robot avatar">
							<?php } ?>
						<?php } else { ?>
							<img class="avatar" src="img/icon-conversation-group.svg">
						<?php } ?>
					</div>
					<div class="notification-text">
						<?php if ($_SESSION['conversations'][$conversation['_id']['$oid']] < $conversation['lastUpdated']['$date']) { ?>
							<div class="stamp-new"></div>
						<?php } ?>
						<div class="notification-text-header">
							<?php if ($conversation['name']) {
								echo $conversation['name'];
							} else {
								if (count($conversation['otherParticipants']) == 0) {
									echo $_SESSION['firstName'] . " " . $_SESSION['lastName'];
								} else {
									echo $conversation['firstParticipants'];
								}
							} ?>
						</div>
						<div class="notification-text-content">
							<?=$conversation['firstMessage']?>
						</div>
					</div>
					<div class="notification-date right" data-id="<?=$conversation['rawTime']?>"><span class="timestamp"><?=$conversation['firstTime']?></span><img class="arrow-right" src="/img/arrow-right.svg">
					</div>
				</div>

			<?php } ?>

		</div>


	</div>

</div>
