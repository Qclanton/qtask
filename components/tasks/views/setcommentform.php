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
		
		<textarea cols="30" rows="5" name="text" class="textarea-input"><?= $comment->text; ?></textarea>
		<br />
		<button>Set</button>
	</form>
</div>

