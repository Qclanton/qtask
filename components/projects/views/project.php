<div id="project">
	<? if ($user_level >=999) { ?>
		<b>Admin Project block</b>
	<? } ?>
	
	<h3><?= $project->title; ?></h3>
	<p><?= $project->description; ?></p>	
</div>
