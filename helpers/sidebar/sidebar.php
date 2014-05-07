<?php
namespace Helpers;

class Sidebar extends \System {
	public $items=[];
		
	public function setItems($items) {
		$this->items = array_merge($this->items, $items);
	}
	
	public function getSidebar($items=[]) {
		if (empty($items)) { $items = $this->items; }
		if (empty($items)) { return ""; }
		
		$this->setView("helpers/sidebar/views/sidebar.php", ['items'=>$items]);
		$this->renderViewContent();
		$sidebar = $this->View->content;
		
		return $sidebar;
	}
}
