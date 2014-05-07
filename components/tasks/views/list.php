<link rel="stylesheet" type="text/css" href="<?= $this->site_url; ?>components/tasks/views/css/style.css" />

<div id="tasklist">
<? if (empty($tasks)) { ?><div id="no-content">There is no tasks</div> <? } else { ?>
	Blok with tasks
	<br />
	Tasks:
	<br />
</div>
<a href="<?= $this->site_url; ?>index.php?component=tasks&action=showsetform">
	<div class="plus"><h3>Create New Task</h3><img src="<?= $this->site_url; ?>components/tasks/views/images/plus.png"></img></div>
</a>
	<table cellspacing="0" cellpadding="5" class="task-table">
		<thead class="table-header">
			<tr>
				<? foreach($statuses as $status) { ?>
					<th><?= $status->title; ?></th>
				<? } ?>
			</tr>
		</thead>
		<tbody class="tbody-content">
			<tr>
				<? foreach($statuses as $status) { ?>
					<td>
						<? foreach ($tasks as $task) { ?>
							<? if ($task->status_id == $status->id) { ?>
								<? 
									$due_date_difference = strtotime("now") - strtotime($task->due_date .  " -" . $due_critical_period . " day");
									$due_class = ((!empty($task->due_date) && empty($task->closed_date) && $due_date_difference > 0) ? "due" : "");
								?> 
								<div class="task-block <?= $task->priority_classes; ?> <?= $due_class; ?>">						
									<h3>
										<a href="<?= $this->site_url; ?>index.php?component=tasks&action=show&id=<?= $task->id; ?>">
											<?= $task->title; ?>
										</a>											
									</h3>
									<? if (!empty($this->user_id)) { ?>
										<div class="comments" id="comments-task--<?= $task->id; ?>">
											<div class="comments-content-wrapper" style="display:none"></div>
											<span class="comments-qty" id="comments-qty-task--<?= $task->id; ?>"><?= $task->comments_qty; ?></span>
										</div>
									<? } ?>
									Assigned to: <?= $task->assigned; ?>

								</div>	
							<? } ?>
						<? } ?>	
					</td>								
				<? } ?>
			</tr>
		</tbody>
	</table>
<? } ?>
</div>
<script>
	$(function() {
		$(".comments-qty").on("click", function() {
			var id = $(this).attr('id').split("--")[1];
			
			$("#comments-task--" + id + " .comments-content-wrapper").load( "<?= $this->site_url; ?>index.php/?load_template_fl=no&component=tasks&action=getfastcomments&return_url=<?= urlencode($this->current_url); ?>&task_id=" + id, function() {
				$("#comments-task--" + id + " .comments-content-wrapper").show();
			});
		});
	});	
</script>

