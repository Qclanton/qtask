<?php 
namespace Models;
 
class Settings extends Models {	
	public function getSettings($layout, $unique=[], $key=null) {
		$unformatted_settings=[];
		$query_base = "SELECT * FROM `settings` WHERE `layout`=?";
		$vars_base = [$layout];
		
		if (!empty($key)) {
			$query_base .= " AND `key`=?";
			$vars_base[] = $key; 
		}
		
		if (!empty($unique)) {
			$query = $query_base . " AND `unique_key`=? AND `unique_value`=?";
			$unique_key = key($unique);
			$vars = $vars_base;
			$vars[] = $unique_key;
			$vars[] = $unique[$unique_key];
			$unformatted_settings = $this->Database->getObject($query, $vars);
		}
		
		if (!$unformatted_settings) {
			$query = $query_base;
			$vars = $vars_base;
			
			if (empty($key)) {
				$query .= " AND `unique_key` IS NULL AND `unique_value` IS NULL";
			}
			
			$unformatted_settings = $this->Database->getObject($query, $vars);
		}
		
		$settings = (object)[];
		foreach ($unformatted_settings as $unformatted_setting) {
			$value = ($unformatted_setting->json_fl == "NO" ? $unformatted_setting->value : json_decode($unformatted_setting->value));
			$unique = (!empty($unformatted_setting->unique_key) ? "_" . $unformatted_setting->unique_key . ":" . $unformatted_setting->unique_value : "");
			$postfix = (!empty($key) ? "-" . $unformatted_setting->layout . $unique : "");
			$settings->{$unformatted_setting->key . $postfix} = $value;
		}
		
		return $settings;
	}
	
	public function getSetting($key, $layout, $unique=[]) {
		$unformatted_setting=[];
		$query_base = "SELECT * FROM `settings` WHERE `key`=? AND `layout`=?";
		$vars_base = [$key, $layout];
		
		if (!empty($unique)) {
			$query = $query_base . " AND `unique_key`=? AND `unique_value`=?";
			$unique_key = key($unique);
			$vars = $vars_base;
			$vars[] = $unique_key;
			$vars[] = $unique[$unique_key];
			$unformatted_setting = $this->Database->getRow($query, $vars);
		}
		
		if (!$unformatted_setting) {
			$query = $query_base . " AND `unique_key` IS NULL AND `unique_value` IS NULL";
			$vars = $vars_base;
			$unformatted_setting = $this->Database->getRow($query, $vars);
		}
		
		
		$setting = ($unformatted_setting['json_fl'] == "NO" ? $unformatted_setting['value'] : json_decode($unformatted_setting['value'])); 
		
		return $setting;
	}
}
