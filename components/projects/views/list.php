<link rel="stylesheet" type="text/css" href="<?= $this->site_url; ?>components/projects/views/css/style.css" />
<div id="projectlist">
	<table cellspacing="0" cellpadding="5" class="task-table">
	<thead>	
		<tr>
			<th>Projects:</th>
		</tr>
	</thead>
	
	<tbody>
		<tr>
			<? foreach ($projects as $project) { ?>
				<td>
				<div class="project-td">
					<h3>
						<a href="<?= $this->site_url . 'index.php?component=projects&action=project&id=' . $project->id; ?>"><?= $project->title; ?></a>
					</h3>
				</div>
				</td>
			<? } ?>
		</tr>
	</tbody>
	</table>
	<? if ($user_level >=999) { ?>
		<b>Admin block</b>
	<? } ?>
</div>
