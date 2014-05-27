<?php 
class System {
	public $site_url;
	public $current_url;
	public $config;
	public $get;
	public $post;
	public $user_id;
	public $Database;
	public $View;
	
	public function __construct() {
		$this->setSiteUrl();
		$this->setCurrentUrl();
	}
	
	
	public function setVars($vars) {
		foreach ($vars as $name=>$value) {
			$this->{$name} = $value;
		}
	}

	private function setSiteUrl() {
		$protocol = ($_SERVER['HTTPS'] === "off" || empty($_SERVER['HTTPS']) ? "http://" : "https://");
		$name = $_SERVER['SERVER_NAME'];
		$path = strstr($_SERVER['PHP_SELF'], "index.php", true);		
		
		$this->site_url = $protocol . $name . $path;
	}
	
	private function setCurrentUrl() {
		$query_string = (isset($_SERVER['QUERY_STRING']) ? "index.php/?" . $_SERVER['QUERY_STRING'] : "");
		$this->current_url = $this->site_url . $query_string;
	}
	
	protected function loadConfig() {
		include "config.php";

		$this->config = $config;
	}
	
	public function convertFromCamelCase($string) {
		$converted_string = substr(strtolower(preg_replace("/([A-Z])/", "_$1", $string)), 1);
		
		return $converted_string;
	}
	
	public function convertToCamelCase($string) {
		$converted_string = str_replace(' ', '', ucwords(str_replace('_', ' ', $string)));
		
		return $converted_string;
	}
	
	public function redirect($url) {
		header('Location: ' . $url, true, 302);
	}

	
	// Functions with View
	protected function setView($path, $vars=[]) {
		$this->View = (object)[];
		$this->setViewPath($path);
		$this->setViewVars($vars);
	}
	
	protected function setViewPath($path) {
		$this->View->path = $path;
	}
	
	protected function setViewVars($vars) {
		if (!isset($this->View->vars)) { $this->View->vars = []; }
		
		$this->View->vars[] = $vars;
	}
	
	protected function renderViewContent() {
		foreach ($this->View->vars as $var) {
			foreach ($var as $name=>$value) {
				${$name} = $value;
			}
		}
		ob_start();		
		require_once($this->View->path);
		$this->View->content = ob_get_contents();
		ob_end_clean();
	}
	
	
	// Functions for load
	protected function loadModels($models = []) {
		foreach ($models as $model) {
			require_once "models/" . $this->convertFromCamelCase($model) . ".php";			
			$model_name = "Models\\" . $model;
			
			$this->{$model} = new $model_name;
			$this->{$model}->Database = $this->Database;
		}
	}
	
	protected function loadHelpers($helpers = []) {		
		foreach ($helpers as $helper) {
			require_once "helpers/" . $this->convertFromCamelCase($helper) . "/" . $this->convertFromCamelCase($helper) . ".php";			
			$helper_name = "Helpers\\" . $helper;
			
			$this->{$helper} = new $helper_name;
		}
	}
	
	protected function loadComponents($components = []) {
		foreach ($components as $component) {
			require_once "components/" . $this->convertFromCamelCase($component) . "/" . $this->convertFromCamelCase($component) . ".php";			
			$component_name = "Components\\" . $component;
			
			$this->{$component} = new $component_name;
			$this->{$component}->user_id = $this->user_id;
			$this->{$component}->Database = $this->Database;			
			$this->{$component}->prepare();
		}
	}
}	
?>
