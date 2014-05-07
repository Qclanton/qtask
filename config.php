<?php 
$config = [
	'template' => "basic",
	
	'sanitize_rules' => [
		'action' => [
			'filter' => "string",
			'flags' => FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH
		]
	],
	
	'database_settings' => [
		'host' => 'localhost',
		'user' => 'root',
		'password' => '111222q?',
		'db_name' => 'qtask'
	]
];
?>
