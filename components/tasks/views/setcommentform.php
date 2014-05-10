<link rel="stylesheet" type="text/css" href="<?= $this->site_url; ?>components/tasks/views/css/setcommentform-style.css" />

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
		
		<textarea cols="30" rows="5" name="text" class="textarea-input"><?= $comment->text; ?></textarea>
		<br />
		<button>Set</button>
	</form>
</div>
<script>
	// Load form content
	$(function() {
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

