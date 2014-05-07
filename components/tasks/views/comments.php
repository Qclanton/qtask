<div id="task-comments-wrapper">
<? if (empty($comments)) { ?><div id="no-content">There is no comments</div><? } else { ?>	
	
	<? foreach ($comments as $comment) { ?>
		<div class="task-comment-wrapper" id="task-comment-wrapper--<?= $comment->id; ?>">
			<? if ($this->user_level >= 900 || $this->user_id == $comment->user_id) { ?>
				<button class="task-comment-edit" id="task-comment-edit--<?= $comment->id; ?>">Edit</button>
				<a href="<?= $this->site_url; ?>index.php/?component=tasks&action=removecomment&id=<?= $comment->id; ?>">Delete</a>
			<? } ?>

			Author: <b><?= $comment->user_name; ?></b>
			<p><?= $comment->text; ?></p>
		</div>
	<? } ?>
	
<? } ?>
</div>
<script>
	$(function() {
		$(".task-comment-edit").on("click", function() {
			var id = $(this).attr('id').split("--")[1];
			
			$("#task-comment-wrapper--" + id).load( "<?= $this->site_url; ?>index.php/?load_template_fl=no&component=tasks&action=getcommentform&task_id=<?= $task_id; ?>&comment_id=" + id, function() {
				$("#task-comment-wrapper--" + id + " input[name='redirection_url']").val("<?= $this->current_url; ?>")
			});
		});
	});	
</script>
