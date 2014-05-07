<?php
namespace Templates;
 
class Templates extends \System {
	public $content = "";
	public $start_tag = "%";
	public $end_tag = "%";
	public $tag_pattern = "";
	public $positions = [];	
	

	
	public function load() {
		$this->loadConfig();		
		
		$this->setView("templates/" . $this->config['template'] . "/views/template.php");
		$this->renderViewContent();
		$this->content = $this->View->content;
		
		$this->setTagPattern();
		$this->setPositions();
	}
	
	protected function setTagPattern() {
		$this->tag_pattern = "/(\\$this->start_tag.*\\$this->end_tag)/";
	}
	
	protected function setPositions() {
		preg_match_all($this->tag_pattern, $this->content, $matches);
		$positions = $matches[1];
		
		foreach($positions as &$position) {
			$position = str_replace($this->end_tag, '', str_replace($this->start_tag, '', $position));
		}
		
		$this->positions = $positions;
	}
}
?>
