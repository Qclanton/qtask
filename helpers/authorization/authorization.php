<?php
namespace Components;

class Authorization extends \System {
	public function __construct() {
		$this->loadModels(["Authorization"]);
	}

	public function authorize($login=null, $password=null) {
		$credentials = (isset($_COOKIE['token']) ? $_COOKIE['token']  : ['login'=>$login, 'password'=>$password]);
		$credentials ="123456";
		$user_id = $this->Authorization->getUserId($credentials);
		
		return $user_id;
	}
}
