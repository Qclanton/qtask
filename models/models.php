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
	
	protected function getConfines($params=[]) {
		$query = "";
		$vars = [];
			
		if (isset($params['order'])) {
			$query .= " ORDER BY";
			
			$i = 0;
			foreach ($params['order'] as $order) {
				$i++;
				$separator = ($i !== count($params['order']) ? "," : "");
				
				$query .= " " . $order['column'];
				
				if (isset($order['side'])) {
					$query .= " " . $order['side'];		
				}
				
				$query .= $separator;
			}
		}
		
		if (isset($params['limit_qty'])) {
			$query .= " LIMIT";
			
			if (isset($params['limit_start'])) {
				$query .= " ?,";
				$vars[] = ['type'=>"i", 'value'=>$params['limit_start']];
			}
			
			$query .= " ?";
			$vars[] = ['type'=>"i", 'value'=>$params['limit_qty']]; 
		}
		
		return [
			'query' => $query,
			'vars'	=> $vars
		];
	}
}
?>
