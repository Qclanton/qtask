<div id="task-comments-wrapper">
<? if (empty($comments)) { ?><div id="no-content">There is no comments</div><? } else { ?>	
	
	<? foreach ($comments as $comment) { ?>
		<div class="task-comment-wrapper" id="task-comment-wrapper--<?= $comment->id; ?>">
			<div class="comment-top">
				<? if ($this->user_level >= 900 || $this->user_id == $comment->user_id) { ?>
					<a class="task-comment-delete" href="<?= $this->site_url; ?>index.php/?component=tasks&action=removecomment&id=<?= $comment->id; ?>">
						<img title="Delete" class="delete-comments" src="<?= $this->site_url; ?>components/tasks/views/images/delete.png"></img>
						<span>Delete Comment</span>
					</a>
					<a class="task-comment-edit" id="task-comment-edit--<?= $comment->id; ?>">
						<img title="Edit" class="edit-comments" src="<?= $this->site_url; ?>components/tasks/views/images/edit-comments.png"></img>
						<span>Edit Comment</span>
					</a>
				<? } ?>
				<div class="comment-info-top">
					<span class="detail-name">Author:</span> <span class="detail-value"><?= $comment->user_name; ?></span>
					<span class="detail-name">Commented at: </span> <span class="detail-value"><?= $comment->creation_date; ?></span>
				</div>
			</div>
			<p><?= $comment->text; ?></p>
			
			<? if (!empty($comment->modification_date)) { ?>
				<div class="comment-info-bottom">
					<span class="detail-name">Last modified at</span> <span class="detail-value"><?= $comment->modification_date; ?></span>
				</div>
			<? } ?>
		</div>
	<? } ?>
	
<? } ?>
</div>
<script>
	$(function() {
		$(".task-comment-edit").on("click", function() {
			var id = $(this).attr('id').split("--")[1];
			
			$("#task-comment-wrapper--" + id).load( "<?= $this->site_url; ?>index.php/?load_template_fl=no&component=tasks&action=getcommentform&task_id=<?= $task_id; ?>&comment_id=" + id, function() {
				$("#task-comment-wrapper--" + id + " input[name='redirection_url']").val("<?= $this->current_url; ?>");
				$("#comment-form-wrapper--" + id + " h3").html("Edit Comment");
			});
		});
	});	
</script>
