<link rel="stylesheet" type="text/css" href="<?= $this->site_url; ?>components/tasks/views/css/task-style.css" />

<div id="task--wrapper">
	<a class="edit-button" href="<?= $this->site_url; ?>index.php?component=tasks&action=showsetform&id=<?= $task->id; ?>">
		<img title="Edit" class="edit-icon" src="<?= $this->site_url; ?>components/tasks/views/images/edit.png"></img>
		<span>Edit Task</span>
	</a>
			
	<h2><?= $task->title; ?></h2>
	<? if (!empty($task->closed_date)) { ?>
		Closed Date = <?= $task->closed_date; ?>
	<? } ?>
	<div class="task-details">
		<h3>Details:</h3>

		<table>
			<tr>
				<td class="first-col">
				<span class="detail-name">Project:</span> 
				</td>
				<td>
				<?= $task->project; ?>
				</td>
			</tr>
			<tr>
				<td class="first-col">
				<span class="detail-name">Assigned to:</span> 
				</td>
				<td>
				<?= strtolower($task->assigned_type); ?> <?= $task->assigned; ?> 
				</td>
			</tr>
			<tr>
				<td class="first-col">
				<span class="detail-name">Status:</span> 
				</td>
				<td>
				<?= $task->status; ?>
				</td>
			</tr>
			<tr>
				<td class="first-col">
				<span class="detail-name">Priority:</span> 
				</td>
				<td>
				<?= $task->priority; ?>
				</td>
			</tr>
			<tr>
				<td class="first-col">
				<span class="detail-name">Creation Date:</span> 
				</td>
				<td>
				<?= $task->creation_date; ?>
				</td>
			</tr>
			<tr>
				<td class="first-col">
				<span class="detail-name">Due date:</span> 
				</td>
				<td>
				<?= (!empty($task->due_date) ? $task->due_date : "&#8734"); ?>
				</td>
			</tr>
		</table>
	</div>
	<h3>Description:</h3> 
	<div id="task-description">
		<?= $task->text; ?>
	</div>
	
	<h3>Subtask:</h3> 
	<div id="task-subtask">
		<div id="task-subtask-button">

		<a class="subtask-button" href="<?= $this->site_url; ?>index.php?component=tasks&action=showsetform&parent_task_id=<?= $task->id; ?>">
			<img title="Create Subtask" class="plus-icon-sub" src="<?= $this->site_url; ?>components/tasks/views/images/plus.png"></img>
			<span>Create Subtask</span>
		</a>
		</div>
		<!-- List of subtasks -->
		<? if (!empty($task->subtasks)) { ?>
			<? foreach ($task->subtasks as $subtask) { ?>
				<a href="<?= $this->site_url; ?>index.php?component=tasks&action=show&id=<?= $subtask->id; ?>"><?= $subtask->title; ?></a>
			<? } ?>
		<? } ?>
	
	</div>
	
	<? if (!empty($task->parent_task_id)) { ?>
	<a href="<?= $this->site_url; ?>index.php?component=tasks&action=show&id=<?= $task->parent_task->id; ?>"><?= $task->parent_task->title; ?></a>
	<? } ?>
</div>
