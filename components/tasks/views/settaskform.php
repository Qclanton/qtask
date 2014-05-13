<link rel="stylesheet" type="text/css" href="<?= $this->site_url; ?>components/tasks/views/css/task-style.css" />
<link rel="stylesheet" type="text/css" href="<?= $this->site_url; ?>components/tasks/views/css/setcommentform-style.css" />
<div id="task--form">
	<form action="<?= $this->site_url; ?>index.php?component=tasks&action=set" method="post">
		<input type="hidden" name="id" value="<?= $task->id; ?>"></input>
		<input type="hidden" name="parent_task_id" value="<?= $task->parent_task_id; ?>"></input>
		<input type="hidden" name="author_id" value="<?= $task->author_id; ?>"></input>
		<input type="hidden" name="creation_date" value="<?= $task->creation_date; ?>"></input>
		<input type="hidden" name="closed_date" value="<?= $task->closed_date; ?>"></input>
		
		<h2>Title:</h2> <div id="field-title"><input type="text" name="title" value="<?= $task->title; ?>"></input></div>

		<? if (!empty($task->closed_date)) { ?>
			Closed Date = <?= $task->closed_date; ?>
		<? } ?>
		<div class="task-details">
			<h3>Details: </h3>		
			
			<table>
				<tr>
					<td class="first-col">
						<span class="detail-name">Project:</span> 
					</td>
					<td>
						<div id="field-project_id">
							<select name="project_id">
								<? foreach ($projects as $project) { ?>
									<option 
											<? if($project->id == $task->project_id) { echo "selected='selected'"; } ?>
											value="<?= $project->id; ?>" 
										>
										<?= $project->title; ?>
									</option>
								<? } ?>
							</select>
						</div>
					</td>
				</tr>
				<tr>
					<td class="first-col">
						<span class="detail-name">Assigned to:</span> 
					</td>
					<td>
						<div id="field-assigned"> 
							<select name="assigned_type" id="assigned_type">
								<option selected="selected" value="USER">User</option>
								<option value="GROUP">Group</option>
							</select> 

							<select name="assigned_id" id="assigned_id">
								<? foreach ($users as $user) { ?>
									<option 
											<? if($user->id == $task->assigned_id && $task->assigned_type=="USER") { echo "selected='selected'"; } ?>
											value="<?= $user->id; ?>" 
											class="USER"
										>
										<?= $user->name; ?>
									</option>
								<? } ?>
								<? foreach ($groups as $group) { ?>
									<option 
											<? if($group->id == $task->assigned_id && $task->assigned_type=="GROUP") { echo "selected='selected'"; } ?>
											value="<?= $group->id; ?>" 
											class="GROUP"
										>
										<?= $group->title; ?>
									</option>
								<? } ?>
							</select>
						</div>
					</td>
				</tr>
				<tr>
					<td class="first-col">
						<span class="detail-name">Status:</span> 
					</td>
					<td>
						<div id="field-status_id"> 
							<select name="status_id">
								<? foreach ($statuses as $status) { ?>
									<option 
											<? if($status->id == $task->status_id) { echo "selected='selected'"; } ?>
											value="<?= $status->id; ?>" 
										>
										<?= $status->title; ?>
									</option>
								<? } ?>
							</select>
						</div>
					</td>
				</tr>
				<tr>
					<td class="first-col">
						<span class="detail-name">Priority:</span> 
					</td>
					<td>
						<div id="field-priority_id"> 
							<select name="priority_id">
								<? foreach ($priorities as $priority) { ?>
									<option 
											<? if($priority->id == $task->priority_id) { echo "selected='selected'"; } ?>
											value="<?= $priority->id; ?>" 
										>
										<?= $priority->title; ?>
									</option>
								<? } ?>
							</select>
						</div>
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
						<div id="field-due_date"><input type="date" name="due_date" value="<?= substr($task->due_date, 0, 10); ?>"></input></div>
					</td>
				</tr>
			</table>
		</div>
			
		<h3>Description:</h3> 
		<div id="task-description">
			<div id="field-text"><textarea cols="30" rows="5" name="text" class="textarea-input"><?= $task->text; ?></textarea></div>
		</div>

		<button class="set-button">
			<img title="Set" class="set-icon" src="<?= $this->site_url; ?>components/tasks/views/images/set.png"></img>
			<span>Set</span>
		</button>
	</form>	
</div>
<script src="<?= $this->site_url; ?>components/tasks/views/js/jquery.chained.js"></script>
<script> $("#assigned_id").chained("#assigned_type"); </script>
