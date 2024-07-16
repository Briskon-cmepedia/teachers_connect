<div id="profile-header"></div>
<div class="profile-body">

	<div id="conversation-new-form">

<div class="page-title no-head">
	<h1>Send New Message</h1>
</div>

<div class="messages-window">
		<button class="button-secondary button-message-close back"><img class="icon-button-arrow svg" src="img/icon-cross.svg"></button>

		<form id="new-message-form" class="form-container submit-once" method="post" action="" enctype="multipart/form-data">

			<?php if ($uid) { ?>
			<h3>To:</h3>
			<input type="text" class="hide" name="conversationParticipants" value="<?=$uid?>">
			<div class="item"><?=$fullName?></div>
			<?php } else { ?>
			<h3>To:</h3>
			<input type="text" id="conversationParticipants" name="conversationParticipants" placeholder="Search by name">
		<?php } ?>
			<h3>Message:</h3>
			<div class="message-new-box">
				<a name="comment"></a>

					<div class="comment-text">
						<textarea id="new-message-textarea" name="text" placeholder="Message User"></textarea>
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
			</div></trix-toolbar><trix-editor id="trix-new-message-textarea" input="new-message-textarea" placeholder="Write message here" contenteditable="" trix-id="4" toolbar="trix-toolbar-4"></trix-editor>
					</div>
					<div class="new-post-bar hide-mobile">
						<div class="right">
							<input type="submit" class="button" value="Send">
						</div>

					</div>
				</form>
			</div>

	 </form>


	  </div>

	</div>

	  <div class="page-bottom clear"></div>
</div>
