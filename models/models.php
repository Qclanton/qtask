<?php 
namespace Models;

class Models extends \System {
	protected function nullValues($object, $elements = []) {
		foreach ($object as $key=>&$value) {
			if (in_array($key, $elements) && empty($value)) {
				$value = null;
			}
		}
		
		return $object;
	}
}
?>
