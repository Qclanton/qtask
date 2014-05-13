<link rel="stylesheet" type="text/css" href="<?= $this->site_url; ?>components/tasks/views/css/setcommentform-style.css" />
<link rel="stylesheet" type="text/css" href="<?= $this->site_url; ?>components/tasks/views/css/qeditor.css" />

<div 
		<? if ($show_newcomment_form_fl == "NO") { echo "style='display:none;'"; } ?>
		class="editor-content" 
		id="editor-content--<?= $comment->id; ?>"
	>
	<div class="task-comment-wrapper">
		<div class="comment-top">
			<div class="comment-info-top">
				<span class="detail-name">Author:</span>
				<span class="detail-value"><?= $comment->user_name; ?></span>
				<span class="detail-name">Commented at: </span>
				<span class="detail-value">the future</span>
			</div>
		</div>
			
		<p></p>
	</div>
</div>

<div class="comment-form--wrapper" id="comment-form-wrapper--<?= $comment->id; ?>">
	<h3>Add Comment</h3>
	<form action="<?= $this->site_url; ?>index.php?component=tasks&action=setcomment" method="post">
		<input type="hidden" name="id" value="<?= $comment->id; ?>"></input>
		<input type="hidden" name="task_id" value="<?= $comment->task_id; ?>"></input>
		<input type="hidden" name="user_id" value="<?= $comment->user_id; ?>"></input>
		<input type="hidden" name="creation_date" value="<?= $comment->creation_date; ?>"></input>
		<input type="hidden" name="modification_date" value="<?= $comment->modification_date; ?>"></input>
		<input type="hidden" name="redirection_url" value="<?= $this->current_url; ?>"></input>
		
		Status: <div id="comments-status_id-wrapper--<?= $comment->id; ?>"><img class="loading" src="http://i.stack.imgur.com/FhHRx.gif"></img></div>
		Assigned To: <div id="comments-assigned-wrapper--<?= $comment->id; ?>"><img class="loading" src="http://i.stack.imgur.com/FhHRx.gif"></img></div>
		
		<div class="editor-wrapper" id="editor-wrapper--<?= $comment->id; ?>">
			<div class="editor-toolbar">
				<a class="editor-button-bold">Bold</a>
				<a class="editor-button-italic">Italic</a>
				<a class="editor-button-quote">Quote</a>
				<a class="editor-button-code">Code</a>
				<a class="editor-button-bullet_list">Bullet List</a>
				<a class="editor-button-numbered_list">Numbered List</a>
				<a class="editor-button-result <? if ($show_newcomment_form_fl == "YES") { echo "edtitor-result-opened"; } ?>">Result</a>
			</div>
			
			<textarea cols="30" rows="5" name="text" class="textarea-input editor-textarea"><?= $comment->text; ?></textarea>			
		</div>
		<button class="set-button">
			<img title="Set" class="set-icon" src="<?= $this->site_url; ?>components/tasks/views/images/set.png"></img>
			<span>Set</span>
		</button>
	</form>
</div>

<script src="<?= $this->site_url; ?>components/tasks/views/js/jquery.selection.js"></script>
<script src="<?= $this->site_url; ?>components/tasks/views/js/qeditor.js"></script>
<script>	
	$(function() {
		// Load form content
		var getform_url = "<?= $this->site_url; ?>index.php/?load_template_fl=no&component=tasks&action=geteditform&return_url=<?= urlencode($this->current_url); ?>&id=<?= $comment->task_id; ?>";
		
		$("#comments-status_id-wrapper--<?= $comment->id; ?>").load(getform_url + " #field-status_id");				
		$("#comments-assigned-wrapper--<?= $comment->id; ?>").load(getform_url + " #field-assigned", function() {
			// Load script for chained select
			var url = "<?= $this->site_url; ?>components/tasks/views/js/jquery.chained.js";
			$.getScript(url, function(){
				$("#assigned_id").chained("#assigned_type");
			});				
		});		
		$(".loading").hide();
	});
</script>

