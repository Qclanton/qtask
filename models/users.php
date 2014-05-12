<?php 
namespace Models;
 
class Users extends Models {
	
	public function getUsers() {
		$query = "SELECT * FROM `users`";
		$users = $this->Database->getObject($query);
		
		return $users;
	}
	
	public function getUserGroups() {
		$query = "SELECT * FROM `user_groups`";
		$groups = $this->Database->getObject($query);
		
		return $groups;
	}
	
	public function getUser($id=null) {
		$query = "SELECT `id`, `login`, `name` FROM `users` WHERE `id`=?";
		$user = $this->Database->getRow($query, [$id]);
		
		return $user;
	}
	
	public function getUserLevel($id, $component_id) {
		$query = "
			SELECT
				MAX(ugr.`level`) AS 'level'
			FROM 
				`user_groups_rights` ugr
			JOIN 
				`users_groups` ug ON (ug.`group_id`=ugr.`group_id`)
			WHERE 
				ug.`user_id`=? AND
				ugr.`component_id`=?
		";
		$level = $this->Database->getValue($query, [$id, $component_id]);
		if (empty($level)) { $level = 0; }
		
		return $level;
	}
	
	public function getSettings() {
		$query = "SELECT * FROM `user_settings`"; 
		$unformatted_settings = $this->Database->getObject($query);
		
		$settings = (object)[];
		foreach ($unformatted_settings as $unformatted_setting) {
			$value = ($unformatted_setting->json_fl == "NO" ? $unformatted_setting->value : json_decode($unformatted_setting->value));
			$settings->{$unformatted_setting->key} = $value;
		}
		
		return $settings;
	}
}
