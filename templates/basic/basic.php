<?php
namespace Templates;
 
class Basic extends Templates {
	public $logged_fl = "no";
	
	public function load() {
		$this->defineUserStatus();
		$this->setView("templates/basic/views/template.php");
		$this->setViewVars(['logged_fl'=>$this->logged_fl]);
		$this->renderViewContent();
		$this->content = $this->View->content;
		
		$this->setTagPattern();
		$this->setPositions();
	}
	
	private function defineUserStatus() {
		if (!empty($this->user_id)) {
			$this->logged_fl = "yes";
		}
	}
}
