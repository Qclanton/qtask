<div id="filter-1-wrapper">
<? if (empty($tasks)) { ?><div id="no-content">There is no tasks</div> <? } else { ?>
	
	<table cellspacing="0" cellpadding="5" class="task-table">
		<thead class="table-header">
			<tr>			
					<th>Title</th>
					<th></th>
					<th>Comments</th>			
					<th>Status</th>
					<th></th>
					<th>Priority</th>
					<th></th>
					<th>Creation Date</th>
					<th>Due Date</th>
					<th></th>
					<th>Assigned To</th>
					<th></th>
					<th>Author</th>
					<th>Project</th>
					<th></th>
					<th>Closed Date</th>
			</tr>
		</thead>
		<tbody class="tbody-content">
			<? foreach ($tasks as $task) { ?>				
				<tr>					
					<td>
						<div id="title-wrapper--<?= $task->id; ?>">
							<a href="<?= $this->site_url; ?>index.php/?component=tasks&action=show&id=<?= $task->id; ?>">
								<?= $task->title; ?>
							</a>							
						</div>
					</td>
					<td><a class="task-edit-property-button" id="task-edit-property-button--<?= $task->id; ?>--title--input">&#9998;</a></td>
					<td>						
						New: <?= $task->comments_qty; ?>
						<div class="comments" id="comments-task--<?= $task->id; ?>">
							<div class="comments-content-wrapper" style="display:none"></div>
							<b><span style="cursor:pointer;" class="comments-qty" id="comments-qty-task--<?= $task->id; ?>">Show</span></b>
						</div>						
					</td>
					<td><div id="status_id-wrapper--<?= $task->id; ?>"><?= $task->status; ?></div></td>
					<td><a class="task-edit-property-button" id="task-edit-property-button--<?= $task->id; ?>--status_id--select">&#9660;</a></td>
					<td><div id="priority_id-wrapper--<?= $task->id; ?>"><?= $task->priority; ?></div></td>
					<td><a class="task-edit-property-button" id="task-edit-property-button--<?= $task->id; ?>--priority_id--select">&#9660;</a></td>
					<td><?= $task->creation_date; ?></td>
					<td><div id="due_date-wrapper--<?= $task->id; ?>"><?= (!empty($task->due_date) ? $task->due_date : "&#8734"); ?></td>
					<td><a class="task-edit-property-button" id="task-edit-property-button--<?= $task->id; ?>--due_date--input">&#9660;</a></td>					
					<td><div id="assigned-wrapper--<?= $task->id; ?>"><?= ucfirst(strtolower($task->assigned_type)); ?> <?= $task->assigned; ?></div></td>
					<td><a class="task-edit-property-button" id="task-edit-property-button--<?= $task->id; ?>--assigned--multiselect">&#9660;</a></td>
					<td><?= $task->author; ?></td>
					<td><div id="project_id-wrapper--<?= $task->id; ?>"><?= $task->project; ?></td>
					<td><a class="task-edit-property-button" id="task-edit-property-button--<?= $task->id; ?>--project_id--select">&#9660;</a></td>
					<td><?= (!empty($task->closed_date) ? $task->closed_date : "&#8734"); ?></td>				
				</tr>
			<? } ?>
		</tbody>
	</table>
	
<? } ?>
</div>
<style>
	.task-edit-property-button {
		cursor: pointer;
	}
</style>
<script>
	$(function() {		
		$(".task-edit-property-button").on("click", function() {
			var attrs =  $(this).attr('id').split("--");
			var id = attrs[1];
			var property = attrs[2];
			var type = attrs[3];
			var getform_url = "<?= $this->site_url; ?>index.php/?load_template_fl=no&component=tasks&action=geteditform&return_url=<?= urlencode($this->current_url); ?>&id=" + id;
			var action_url = "<?= $this->site_url; ?>index.php/?component=tasks&action=fastedit&property=" + property + "&id=" + id + "&redirection_url=<?= urlencode($this->current_url); ?>";
			
			
			// Include form 
			$("#" + property + "-wrapper--" + id).html("<form action='" + action_url + "' method='post'><img class='loading' src='http://i.stack.imgur.com/FhHRx.gif'></img></form>");
			
			// Load form content
			$("#" + property + "-wrapper--" + id + " form").load(getform_url + " #field-" + property, function() {
				if (type == "input" || type == "multiselect") {
					// Load script for chained select
					if (property == "assigned") {
						var url = "<?= $this->site_url; ?>components/tasks/views/js/jquery.chained.js";
						$.getScript(url, function(){
							$("#assigned_id").chained("#assigned_type");
						});
					}
					
					$("#" + property + "-wrapper--" + id + " form").append("<button>Save</button>");				
				}
				else if (type == "select") {
					$("#" + property + "-wrapper--" + id + " form select[name=" + property + "]").on("change", function() {
						$("#" + property + "-wrapper--" + id + " form").submit();
					});
				}
				
				// Hide loading gif
				$(".loading").hide();
			});
		});
	});	
</script>
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
