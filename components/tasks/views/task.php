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
		<!-- End of List of subtasks -->
		
		
		<!-- List of Tied Tasks -->
		<? if (!empty($task->tied_tasks)) { ?>
			<? foreach ($task->tied_tasks as $tied_task) { ?>
				<? if ($tied_task->depended_object == "TASK") { ?>
					This tasks depends of 
				<? } elseif ($tied_task->depended_object == "TIED_TASK") { ?>
					This tasks is source for
				<? } ?>
				
				<a href="<?= $this->site_url; ?>index.php?component=tasks&action=show&id=<?= $tied_task->id; ?>"><?= $tied_task->title; ?></a>
				(<a href="<?= $this->site_url; ?>index.php?component=tasks&action=untie&task_id=<?= $task->id; ?>&tied_task_id=<?= $tied_task->id; ?>&redirection_url=<?= urlencode($this->current_url); ?>">Untie</a>)
			<? } ?>
		<? } ?>
		<!-- End of List of Tied Tasks -->
		
		
		<!-- Tie form -->
		<a style="cursor:pointer;" id="show-tie_task-form-button">Show Tie Form</a>
		<form style="display: none;" action="<?= $this->site_url; ?>index.php?component=tasks&action=tie" method="post" id="tie_task-form">
			<input type="hidden" name="task_id" value="<?= $task->id; ?>"></input>
			<input type="hidden" name="redirection_url" value="<?= $this->current_url; ?>"></input>
			
			
			<select name="tied_task_id">
				<optgroup label="Last Watched">
					<? foreach ($last_watched_tasks as $last_watched_task) { ?>
						<option value="<?= $last_watched_task->id; ?>"><?= $last_watched_task->id; ?>. <?= $last_watched_task->title; ?></option>
					<? } ?>
				</optgroup>
				<optgroup label="Last Created">
					<? foreach ($last_created_tasks as $last_created_task) { ?>
						<option value="<?= $last_created_task->id; ?>"><?= $last_created_task->id; ?>. <?= $last_created_task->title; ?></option>
					<? } ?>
				</optgroup>
			</select>
			
			<select name="depended_object">
				<option selected="selected" value="NONE">None</option>
				<option value="TASK">This Task</option>
				<option value="TIED_TASK">Tied Task</option>
			</select>
			
			<input type="submit" value="Tie"></input>
		</form>
		<!-- End of Tie form -->
			
	</div>
	
	<? if (!empty($task->parent_task_id)) { ?>
	<a href="<?= $this->site_url; ?>index.php?component=tasks&action=show&id=<?= $task->parent_task->id; ?>"><?= $task->parent_task->title; ?></a>
	<? } ?>
</div>
<script>	
	$(function() {
		$("#show-tie_task-form-button").on("click", function() {
			if ($("#tie_task-form").hasClass("opened")) {
				$("#tie_task-form").removeClass("opened").hide();
				$("#show-tie_task-form-button").html("Show Tie Form");
			}
			else {
				$("#tie_task-form").addClass("opened").show();
				$("#show-tie_task-form-button").html("Hide Tie Form");
			}
		});
	});	
</script>
