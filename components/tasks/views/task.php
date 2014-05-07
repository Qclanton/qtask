<link rel="stylesheet" type="text/css" href="<?= $this->site_url; ?>components/tasks/views/css/task-style.css" />

<div id="task--wrapper">
		<h2><?= $task->title; ?></h2>
		<? if (!empty($task->closed_date)) { ?>
			Closed Date = <?= $task->closed_date; ?>
		<? } ?>
		<div class="task-details">
		<h3>Details</h3>
		<hr />
		Creation Date: <?= $task->creation_date; ?>
		<br />	
		Project: <?= $task->project; ?>
		<br />
		Status: <?= $task->status; ?>
		<br />
		Priority: <?= $task->priority; ?>
		<br />
		Due date: <?= (!empty($task->due_date) ? $task->due_date : "&#8734"); ?>
		<br />

		</div>
		Text: <?= $task->text; ?>
		<br />
		Assigned to: <?= strtolower($task->assigned_type); ?> <?= $task->assigned; ?> 
		<br />
		<a href="<?= $this->site_url; ?>index.php?component=tasks&action=showsetform&id=<?= $task->id; ?>">
			<button>Edit</button>
		</a>
</div>
