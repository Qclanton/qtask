<ul>
	<? foreach ($errors as $error_pack) { ?>
		<? foreach ($error_pack as $source=>$data) { ?>
			<li>
				<b>Error Source: </b><?= $source ?>
				<b>Error Data: </b><?= $data ?>
			</li>
		<? } ?>
	<? } ?>
</ul>
