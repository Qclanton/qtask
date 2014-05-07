<?php 
namespace Models;
 
class Components extends Models {
	public function getComponentId($title) {
		$query = "SELECT `id` FROM `components` WHERE `title`=?";
		$component_id = $this->Database->getValue($query, [$title]);
		
		return $component_id;
	}
}
?>
