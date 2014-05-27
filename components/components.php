<?php 
namespace Components;

class Components extends \System {
	public $content = ['head'=>''];
	public $user_level;
	protected $rules = [];
	protected $necessary_vars;
	
	public function defineUserLevel($component_name) {
		$this->loadModels(["Components", "Users"]);
		$component_id = $this->Components->getComponentId($component_name);
		$this->user_level = $this->Users->getUserLevel($this->user_id, $component_id);
	}
	
	public function prepare() { }
	public function load() { }
	
	public function setRules($rules) {
		$this->rules = $rules;
	}
	
	public function setNecessaryVars($vars) {
		$this->necessary_vars = $vars;
	}
	
	
	protected function validate() {
		$this->loadHelpers(["Validator", "ErrorHandler"]);		
	
		if (!empty($this->necessary_vars)) { if (!$this->validateIsset()) { return false; }}
		
		$this->Validator->setRules($this->rules);
		$this->Validator->validateVars($this->post);
		$this->Validator->validateVars($this->get);	
				
		if ($this->Validator->errors) {
			$this->content['error'] = $this->ErrorHandler->getHtml($this->Validator->errors);
			return false;
		}
		
		return true;
	}
	
	protected function validateIsset() {
		$this->loadHelpers(["Validator", "ErrorHandler"]);
	
		foreach ($this->necessary_vars as $source=>$vars) {
			$result = $this->Validator->validateIsset($vars, $this->{$source});		
			
			if (!$result) {
				$this->content['error'] = $this->ErrorHandler->getHtml($this->Validator->errors);
				return false;
			}
		}
		
		return true;
	}
	
	protected function setUserLastVisitDate($layout) {
		setcookie($layout . "_last_visit_date", date("Y-m-d H:i:s"), time()+31536000);
	}
	
	protected function getUserLastVisitDate($layout) {
		$date = (isset($_COOKIE[$layout . '_last_visit_date']) ? $_COOKIE[$layout . '_last_visit_date'] : null);
		
		return $date;
	}
	
	protected function uploadImage() {
		$this->loadHelpers(['Uploader']);
		$this->Uploader->allowed_extensions = ['png', 'jpg', 'gif'];		
		if(!file_exists('uploads/user_' . $this->user_id)) { mkdir('uploads/user_' . $this->user_id); }
		$this->Uploader->path = 'uploads/user_' . $this->user_id;
		
		$result = $this->Uploader->upload();
		
		if ($result) {
			$this->loadModels(['Users']);
			$this->Users->saveUnattachedImages($this->user_id, $this->Uploader->uploaded_files);
		}
		
		return $result;  
	}		
}
?>
