<?
	$this->content['head'] .= '<link rel="stylesheet" type="text/css" href="' . $this->site_url . 'components/filters/views/css/style.css" />';
	$this->content['head'] .= '<script src="' . $this->site_url . 'static/js/jquery.cookie.js"></script>';
?>
<div id="filter-1-wrapper">
<? if (empty($tasks)) { ?><div id="no-content">There is no tasks</div> <? } else { ?>	

<a href="<?= $this->current_url; ?>&show_closed_fl=yes">Показывать закрытые задачи</a>
<? foreach ($projects as $project) { ?>
	<h3 
			class="project-title <? if ($project->hidden_fl != 'yes') { echo 'opened'; } ?>"
			id="project-title--<?= $project->id; ?>"
		>
		<?= $project->title; ?>(<?= $project->tasks_qty; ?>)
	</h3>
	
	<table 
			<? if ($project->hidden_fl == 'yes') { echo 'style="display:none;"'; } ?> 
			cellspacing="0" 
			cellpadding="5" 
			class="task-table"
			id="task-table--<?= $project->id; ?>"
		>
		<thead class="table-header">
			<tr class="tasks-table-header">			
					<th id="col-ordering-position--0" class="sortable">Title</th>
					<th id="col-ordering-position--1"></th>
					<th id="col-ordering-position--2" class="sortable">Comments</th>			
					<th id="col-ordering-position--3" class="sortable">Status</th>
					<th id="col-ordering-position--4"></th>
					<th id="col-ordering-position--5" class="sortable">Priority</th>
					<th id="col-ordering-position--6"></th>
					<th id="col-ordering-position--7" class="sortable">Creation Date</th>
					<th id="col-ordering-position--8" class="sortable">Due Date</th>
					<th id="col-ordering-position--9"></th>
					<th id="col-ordering-position--10" class="sortable">Assigned To</th>
					<th id="col-ordering-position--11"></th>
					<th id="col-ordering-position--12" class="sortable">Author</th>
					<th id="col-ordering-position--13" class="sortable">Project</th>
					<th id="col-ordering-position--14"></th>
					<th id="col-ordering-position--15" class="sortable">Closed Date</th>					
			</tr>
		</thead>
		<tbody class="tbody-content">
			<? foreach ($tasks as $task) { ?>
			<? if ($task->project_id == $project->id) { ?>					
				<tr>					
					<td>
						<div id="title-wrapper--<?= $task->id; ?>">
							<a href="<?= $this->site_url; ?>index.php/?component=tasks&action=show&id=<?= $task->id; ?>">
								<?= $task->title; ?>
							</a>							
						</div>
					</td>
					<td class="task-edit-property-td"><a class="task-edit-property-button" id="task-edit-property-button--<?= $task->id; ?>--title--input">&#9998;</a></td>
					<td class="task-edit-property-td">						
						New: <?= $task->comments_qty; ?>
						<div class="comments" id="comments-task--<?= $task->id; ?>">
							<div class="comments-content-wrapper" style="display:none"></div>
							<b><span style="cursor:pointer;" class="comments-qty" id="comments-qty-task--<?= $task->id; ?>">Show</span></b>
						</div>						
					</td>
					<td><div id="status_id-wrapper--<?= $task->id; ?>"><?= $task->status; ?></div></td>
					<td class="task-edit-property-td"><a class="task-edit-property-button" id="task-edit-property-button--<?= $task->id; ?>--status_id--select">&#9660;</a></td>
					<td><div id="priority_id-wrapper--<?= $task->id; ?>"><?= $task->priority; ?></div></td>
					<td class="task-edit-property-td"><a class="task-edit-property-button" id="task-edit-property-button--<?= $task->id; ?>--priority_id--select">&#9660;</a></td>
					<td class="task-edit-property-td"><?= $task->creation_date; ?></td>
					<td><div id="due_date-wrapper--<?= $task->id; ?>"><?= (!empty($task->due_date) ? $task->due_date : "&#8734"); ?></td>
					<td class="task-edit-property-td"><a class="task-edit-property-button" id="task-edit-property-button--<?= $task->id; ?>--due_date--input">&#9660;</a></td>					
					<td><div id="assigned-wrapper--<?= $task->id; ?>"><?= ucfirst(strtolower($task->assigned_type)); ?> <?= $task->assigned; ?></div></td>
					<td class="task-edit-property-td"><a class="task-edit-property-button" id="task-edit-property-button--<?= $task->id; ?>--assigned--chainedselect">&#9660;</a></td>
					<td class="task-edit-property-td"><?= $task->author; ?></td>
					<td><div id="project_id-wrapper--<?= $task->id; ?>"><?= $task->project; ?></td>
					<td class="task-edit-property-td"><a class="task-edit-property-button" id="task-edit-property-button--<?= $task->id; ?>--project_id--select">&#9660;</a></td>
					<td><?= (!empty($task->closed_date) ? $task->closed_date : "&#8734"); ?></td>				
				</tr>
			<? } ?>
			<? } ?>
		</tbody>
	</table>
<? } ?>
	
<? } ?>
</div>
<script>
	$(function() {		
		$('.task-edit-property-button').on('click', function() {
			var attrs =  $(this).attr('id').split('--');
			var id = attrs[1];
			var property = attrs[2];
			var type = attrs[3];
			var getform_url = '<?= $this->site_url; ?>index.php/?load_template_fl=no&component=tasks&action=geteditform&return_url=<?= urlencode($this->current_url); ?>&id=' + id;
			var action_url = '<?= $this->site_url; ?>index.php/?component=tasks&action=fastedit&property=' + property + '&id=' + id + '&redirection_url=<?= urlencode($this->current_url); ?>';
			
			
			// Include form 
			$('#' + property + '-wrapper--' + id).html('<form action="' + action_url + '" method="post"><img class="loading" src="<?= $this->site_url; ?>static/images/loading.gif"></img></form>');
			
			// Load form content
			$('#' + property + '-wrapper--' + id + ' form').load(getform_url + ' #field-' + property, function() {
				if (type == 'input' || type == 'chainedselect') {
					// Load script for chained select
					if (property == 'assigned') {
						var url = '<?= $this->site_url; ?>static/js/jquery.chained.js';
						$.getScript(url, function(){
							$('#assigned_id').chained('#assigned_type');
						});
					}
					
					$('#' + property + '-wrapper--' + id + ' form').append('<button>Save</button>');				
				}
				else if (type == 'select') {
					$('#' + property + '-wrapper--' + id + ' form select[name=' + property + ']').on('change', function() {
						$('#' + property + '-wrapper--' + id + ' form').submit();
					});
				}
				
				// Hide loading gif
				$('.loading').hide();
			});
		});
	});	
</script>
<script>
	$(function() {	
		$('.project-title').on('click', function() {
			var id = $(this).attr('id').split('--')[1];
			var cookie_name = 'Settings-Filters-mytask_hidden_projects';				
			var hidden_projects = ((typeof $.cookie(cookie_name) != 'undefined') ? $.cookie(cookie_name) : '');
			
			if ($(this).hasClass('opened')) {
				// Hide Table
				$('#task-table--' + id).hide('fast');
				
				// Save setting in cookie
				if ($.inArray(id, hidden_projects.split(',')) == -1)	{		
					var cookie_value = ((hidden_projects != '') ? (hidden_projects + ',' + id) : id);				
					$.cookie(cookie_name, cookie_value, { expires: 7 });
				}
				
				// Remove marker-class
				$(this).removeClass('opened');
			}
			else {
				// Show table
				$('#task-table--' + id).show('fast');
				
				// Remove hidden project from settings
				if (hidden_projects != '') {
					var hidden_projects_arr = hidden_projects.split(',');					

					for (i=0; i<hidden_projects_arr.length; i++) {
						if (hidden_projects_arr[i] == id) {
							hidden_projects_arr.splice(i, 1);
						}
					}
					
					// Resave setting in cookie
					var cookie_value = hidden_projects_arr.join(',');
					$.cookie(cookie_name, cookie_value, { expires: 7 });
				}
				
				// Add marker-class
				$(this).addClass('opened');	
			}
		});
	});
</script>
