<div id="profile-header"></div>

<div class="messages-window">

		<button class="button-edit-mobile button-secondary"><img class="icon-button-arrow icon-right svg" src="img/arrow-down.svg" alt="icon. arrow down"></button>
		<button class="button-secondary button-message-close back"><img class="icon-button-arrow svg" src="img/icon-cross.svg"></button>
		<!-- <a href="messages-inbox.php"><div class="icon-close"><img class="icon-cancel right" src="img/icon-cancel.svg"></div></a> -->

		<div class="column-left">
			<?php if ($conversation[0]['owner'] == $_SESSION['uid']) { ?>
			<button class="button-edit button-secondary right"><img class="icon-options" src="img/icon-edit.svg" alt="icon edit">Edit</button> <button class="control-edit button-save button right">Save</button>
			<!-- <a href="messages-inbox.php"><div class="button-back"><img class="icon-left svg" src="img/arrow-down.svg" alt="icon. arrow down"> Back</div></a> -->
		<?php } ?>

			<div class="conversation-details">
				<h2 id="conversation-name"><?php if($conversation[0]['name']) { echo $conversation[0]['name']; } ?></h2>
				<input type="text" name="conversationName" class="conversation-name control-edit field-edit" value="<?php if($conversation[0]['name']) { echo $conversation[0]['name']; } ?>" placeholder="Conversation name (optional)">

				<div class="line-bottom"></div>
			</div>

			<h3>Participants</h3>
			<input type="hidden" id="conversationParticipants" name="conversationParticipants" class="" placeholder="Add a participant">


			<div id="participants-view">

				<?php if($participantInfo) { ?>

					<?php foreach ($participantInfo as $participant) { ?>

						<div class="author" id="<?=$participant['_id']['$oid']?>">
				      <!-- <a href="#"> -->
				      <div class="post-header col-avatar small">
								<?php if ($participant['avatar']) { ?>
									<img class="avatar" alt="avatar" src="image.php?id=<?=$participant['avatar']?>&height=200" onerror="this.src='img/robot.svg'">
								<?php } else { ?>
									<img class="avatar" src="img/robot.svg" alt="robot avatar">
								<?php } ?>
				      </div>
				      <div class="post-header">
				        <div class="author-name"><?=$participant['firstName']?> <?=$participant['lastName']?></div>
				      </div>
							<?php if ($participant['_id']['$oid'] !== $_SESSION['uid']) { ?>
							<div class="checkbox-action">
								<input class="checkbox-remove" type="checkbox" name="remove[]" value="<?=$participant['_id']['$oid']?>" id="box-<?=$participant['_id']['$oid']?>"><label for="box-<?=$participant['_id']['$oid']?>"></label>
							</div>
							<?php } ?>
				      <!-- </a> -->
				     </div>

					<?php } ?>

				<?php } ?>

		 </div>

		 <div class="line-bottom"></div>

		 <?php if ($conversation[0]['owner'] == $_SESSION['uid']) { ?>
			 <a href="#confirm-delete" rel="modal:open">
				 <div class="controls-delete">
					 <button class="button-delete control-edit button-secondary" data-id="<?=$conversation[0]['_id']['$oid']?>"><img class="icon-options" src="img/icon-delete.svg" alt="icon. delete conversation">Delete Conversation</button>
				 </div>
			 </a>
		 <?php } else { ?>
			 <a href="#confirm-leave" rel="modal:open">
				 <div class="controls-delete">
					 <button class="button-leave button-secondary" data-id="<?=$conversation[0]['_id']['$oid']?>"><img class="icon-options" src="img/icon-delete.svg" alt="icon. leave conversation">Leave Conversation</button>
				 </div>
			 </a>
		 <?php } ?>

	  </div>

	<div class="column right messages-thread">

		<?php if($conversation) { ?>

			<?php if (count($conversation[0]['messages']) > 20) { ?>

				<div class="load-more-container">
					<button id="load-more" class="button-secondary">Load Previous</button>
				</div>

			<?php } ?>

			<div class="message-thread-container">

				<div id="20">

				<?php //foreach ($conversation[0]['messages'] as $message) { ?>

				<?php foreach ($messages as $message) { ?>

					<div class="notification">
				  	<div class="author">
				      	<a href="#">
				      	<div class="post-header col-avatar small">
									<?php if ($participantInfo[ $message['userId'] ]['avatar']) { ?>
										<img class="avatar" alt="avatar" src="image.php?id=<?=$participantInfo[ $message['userId'] ]['avatar']?>&height=200" onerror="this.src='img/robot.svg'">
									<?php } else { ?>
										<img class="avatar" src="img/robot.svg" alt="robot avatar">
									<?php } ?>
				      	</div>
				      	<div class="post-header">
					        <div class="author-name">
										<?php if (in_array($message['userId'], $participants)) { ?>
											<?=$participantInfo[ $message['userId'] ]['firstName']?> <?=$participantInfo[ $message['userId'] ]['lastName']?>
										<?php } else { ?>
											Previous Participant
										<?php } ?>
									</div>
					        <div class="post-time notification-date" data-id="<?=$message['time']['$date']?>"><span class="timestamp"><?=timestamp($message['time']['$date'], 'j M g:iA')?></span></div>
				     	</div>
				      	</a>
				    </div>

				    <div class="content">
				     	<div class="comment-content">
				        	<div><?=preg_replace("/<a(.*?)>/", "<a$1 target=\"_blank\">", $message['text'])?></div>
				    	</div>
				    </div>
				  </div>

				<?php } ?>

			</div>

		<?php } ?>

		</div>

	</div>

	<div class="comment-box">
	  <a name="comment"></a>
	  <form id="new-message-form" class="form-container" method="post" action="" enctype="multipart/form-data">
	    <input type="hidden" id="cid" name="cid" value="<?=$conversation[0]['_id']['$oid']?>">
	    <div class="comment-text">
	      <textarea id="new-message-textarea" name="text" placeholder="Message User"></textarea>
				<input type="submit" class="button-message-new button-comment-mobile button right" value="Send">
	      <trix-toolbar id="trix-toolbar-4" class="hide-mobile"><div class="trix-button-row">
	  <span class="trix-button-group trix-button-group--text-tools" data-trix-button-group="text-tools">
	    <button type="button" class="trix-button trix-button--icon trix-button--icon-bold" data-trix-attribute="bold" data-trix-key="b" title="Bold" tabindex="-1">Bold</button>
	    <button type="button" class="trix-button trix-button--icon trix-button--icon-italic" data-trix-attribute="italic" data-trix-key="i" title="Italic" tabindex="-1">Italic</button>
	    <button type="button" class="trix-button trix-button--icon trix-button--icon-strike" data-trix-attribute="strike" title="Strikethrough" tabindex="-1">Strikethrough</button>
	    <button type="button" class="trix-button trix-button--icon trix-button--icon-link" data-trix-attribute="href" data-trix-action="link" data-trix-key="k" title="Link" tabindex="-1">Link</button>
	  </span>

	  <span class="trix-button-group trix-button-group--history-tools" data-trix-button-group="history-tools">
	    <button type="button" class="trix-button trix-button--icon trix-button--icon-undo" data-trix-action="undo" data-trix-key="z" title="Undo" tabindex="-1">Undo</button>
	    <button type="button" class="trix-button trix-button--icon trix-button--icon-redo" data-trix-action="redo" data-trix-key="shift+z" title="Redo" tabindex="-1">Redo</button>
	  </span>
	</div>

	<div class="trix-dialogs" data-trix-dialogs="">
		  <label for="trix-input" hidden="hidden">
            Enter a URL
          </label>
	  <div class="trix-dialog trix-dialog--link" data-trix-dialog="href" data-trix-dialog-attribute="href">
	    <div class="trix-dialog__link-fields">
	      <input id="trix-input" type="url" name="href" class="trix-input trix-input--dialog" placeholder="Enter a URL…" required="" data-trix-input="" disabled="disabled">
	      <div class="trix-button-group">
	        <input type="button" class="trix-button trix-button--dialog" value="Link" data-trix-method="setAttribute">
	        <input type="button" class="trix-button trix-button--dialog" value="Unlink" data-trix-method="removeAttribute">
	      </div>
	    </div>
	  </div>
	</div></trix-toolbar><trix-editor id="trix-new-message-textarea" input="new-message-textarea" placeholder="Write your new message here" contenteditable="" trix-id="4" toolbar="trix-toolbar-4" autofocus></trix-editor>
	    </div>
	    <div class="new-post-bar hide-mobile">
	      <div class="right">
	        <input type="button" class="button-message-new button" value="Send">
	      </div>
				<div class="enter-send">
					<input type="checkbox" name="enter-send" id="enter-send"><label for="enter-send"> Enter to Send </label>&nbsp;&nbsp;<span class="tip">(shift+enter for a new line)</span>
				</div>
	    </div>
	  </form>
	</div>

</div>

</form>


	  <div class="page-bottom"></div>
</div>
</div>
