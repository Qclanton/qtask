<?php
$time_start = microtime(true);

require_once "config.php";
require_once "system.php";

require_once "models/models.php";
require_once "components/components.php";
require_once "templates/templates.php";

// Initialize database connection
require_once "helpers/database/database.php";
$Database = new Helpers\Database;
$Database->connect($config['database_settings']);


// Authorize user
require_once "components/authorization/authorization.php";
$Authorization = new Components\Authorization;
$Authorization->setVars(['Database' => $Database]);
$Authorization->prepare();
$user_id = $Authorization->authorize();


// Sanitize and set vars
require_once "helpers/sanitizer/sanitizer.php";
$Sanitizer = new Helpers\Sanitizer;
$Sanitizer->setRules($config['sanitize_rules']);
$vars = [
	'get' => (object)$Sanitizer->sanitizeVars($_GET),
	'post' => (object)$Sanitizer->sanitizeVars($_POST),
	'user_id' => $user_id,
	'Database' => $Database	
];


// Load component 
$component_name = (isset($vars['get']->component) ? $vars['get']->component : "main");
$component_file = "components/" . $component_name . "/" . $component_name . ".php";
if (file_exists($component_file)) {
	require_once "components/" . $component_name . "/" . $component_name . ".php";
	$component_class_name = "Components\\" . ucfirst($component_name);
	$component = new $component_class_name;
	$component->setVars($vars);
	$component->prepare();
	$component->load();
}
else {
	$component = (object)[
		'content' => [
			'error' => "<div id='error'>Component doesn't exists</div>"
		]
	];
}


// Define necessity of loading a template
$load_template_fl = (isset($vars['get']->load_template_fl) ? $vars['get']->load_template_fl : "yes");

if ($load_template_fl == "yes") {
	// Load template
	$template_name = $config['template'];
	require_once "templates/" . $template_name . "/" . $template_name . ".php";
	$template_class_name = "Templates\\" . ucfirst($template_name);
	$template = new $template_class_name;
	$template->setVars($vars);
	$template->load();	

	// Set content from component on the positions
	$content = $template->content;
	foreach ($template->positions as $position) {
		$component_content = (isset($component->content[$position]) ? $component->content[$position] : "");
		
		$content = str_replace($template->start_tag . $position . $template->end_tag, $component_content, $content);
	}
	
	echo $content;
}


/* $time_end = microtime(true); 
$time = $time_end - $time_start;
echo "Time: $time";*/
?>
