<link rel="stylesheet" type="text/css" href="<?= $this->site_url; ?>components/filters/views/css/style.css" />
<script src="<?= $this->site_url; ?>components/filters/views/js/jquery.sortcolums.js"></script>

<div id="filter-1-wrapper">
<? if (empty($tasks)) { ?><div id="no-content">There is no tasks</div> <? } else { ?>
	
	<a style="cursor: pointer;" id="sort-cols">Sort Columns</a>
	
	<table cellspacing="0" cellpadding="5" class="task-table">
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
					<td class="task-edit-property-td"><a class="task-edit-property-button" id="task-edit-property-button--<?= $task->id; ?>--assigned--multiselect">&#9660;</a></td>
					<td class="task-edit-property-td"><?= $task->author; ?></td>
					<td><div id="project_id-wrapper--<?= $task->id; ?>"><?= $task->project; ?></td>
					<td class="task-edit-property-td"><a class="task-edit-property-button" id="task-edit-property-button--<?= $task->id; ?>--project_id--select">&#9660;</a></td>
					<td><?= (!empty($task->closed_date) ? $task->closed_date : "&#8734"); ?></td>				
				</tr>
			<? } ?>
		</tbody>
	</table>
	
<? } ?>
</div>
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
<script>
	$(function() {
		// FUNCTION FROM HELL FOR SORT COLUMNS
		// TODO: REMOVE ARROWS FROM THE LAST ELEMENTS
		
		$("#sort-cols").on("click", function() {
			var sortable_columns = $(".task-table .tasks-table-header th.sortable");
			
			if ($(this).hasClass('sorting')) {				
				$.each(sortable_columns, function() {
					var column_title = $(this).children('a.move-right').attr('id').split("--")[1];
					
					$(this).html(column_title);
				});
				
				$(this).removeClass('sorting');				
			}
			else {
				// Make arrows			
				$.each(sortable_columns, function() {
					var column_title = $(this).html();
					$(this).prepend("<a class='move-left' id='column--" + column_title + "'>&#8678;</a>");
					$(this).append("<a class='move-right' id='column--" + column_title + "'>&#8680;</a>");
				});
				
				$(this).addClass('sorting');
				
				
				// Make handlers
				$(".move-right").on("click", function() {
					// Define postiton
					var position = $(this).parent().attr("id").split("--")[1]*1;
					var step = 1;
					var has_toggle_fl = "no";
					
					if (!$(this).parent().next().hasClass("sortable")) {
						has_toggle_fl = "yes";
					}
					
					if (!$(this).parent().next().next().next().hasClass("sortable") || (!$(this).parent().next().next().hasClass("sortable") && has_toggle_fl == "no")) {
						step++;
					}
					
					// Move depends col if it necessary
					if (has_toggle_fl == "yes") {
						var depends_col = $(this).parent().next();
						var depends_col_position = position + 1;
						var depends_col_next_position = depends_col_position + step;
						
						depends_col.attr("id", "col-ordering-position--" + depends_col_next_position);
						depends_col.next().attr("id", "col-ordering-position--" + depends_col_position);
						
						$.moveColumn($(".task-table"), depends_col_position, depends_col_position + step);
					}
					
					
					// Move main col
					$.moveColumn($(".task-table"), position, position + step);
					

					// Set new ids
					next_position = position + step;
					$(this).parent().attr("id", "col-ordering-position--" + next_position);					
					if (step == 1) {
						$(this).parent().prev().attr("id", "col-ordering-position--" + position);
					}
					if (step == 2) {
						$(this).parent().prev().prev().attr("id", "col-ordering-position--" + position);
						$(this).parent().prev().attr("id", "col-ordering-position--" + (position + 1));
					}
				});
				
				$(".move-left").on("click", function() {
					// Define postiton
					var position = $(this).parent().attr("id").split("--")[1]*1;
					var step = 1;
					
					if (!$(this).parent().prev().hasClass("sortable")) {
						step++;
					}
					
					var has_toggle_fl = "no";
					if (!$(this).parent().next().hasClass("sortable") && $(this).parent().next().val() == "") {
						var has_toggle_fl = "yes";
						var depends_col = $(this).parent().next();
						var depends_col_position = position + 1;
					}
					
					// Move main col
					$.moveColumn($(".task-table"), position, position - step);
					
					// Move depends col if it necessary
					if (has_toggle_fl == "yes") {
						var depends_col_prev_position = (depends_col_position - step);
						
						depends_col.attr("id", "col-ordering-position--" + depends_col_prev_position);
						$.moveColumn($(".task-table"), depends_col_position, depends_col_prev_position);
						
					}
					

					// Set new ids
					$(this).parent().attr("id", "col-ordering-position--" + (position - step));	
					alert("Step: " + step + "; Fl: " + has_toggle_fl);			
					if (step == 1 && has_toggle_fl=="no") {
						$(this).parent().next().attr("id", "col-ordering-position--" + position);
					}
					else if (step == 2 && has_toggle_fl=="no") {
						$(this).parent().next().attr("id", "col-ordering-position--" + (position-1));
						$(this).parent().next().next().attr("id", "col-ordering-position--" + (position));
					}
					else if (step == 1 && has_toggle_fl=="yes") {
						$(this).parent().next().next().attr("id", "col-ordering-position--" + (position+1));
					}
					if (step == 2 && has_toggle_fl=="yes") {
						$(this).parent().next().next().attr("id", "col-ordering-position--" + (position));
						$(this).parent().next().next().next().attr("id", "col-ordering-position--" + (position+1));;
					}
				});					
			}
		});		
	});
</script>
