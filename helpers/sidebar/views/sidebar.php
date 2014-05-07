<ul>
	<? foreach ($items as $title=>$link) { ?>
		<li>
			<a href="<?= $link; ?>"><?= $title; ?></a>		 
		</li>
	<? } ?>
</ul>
