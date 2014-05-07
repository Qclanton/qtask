<?php
namespace Components;

class Authorization extends Components {
	
	public function prepare() {
		$this->loadModels(["Authorization"]);
	}
	
	public function load() {
		$action = (isset($this->get->action) ? $this->get->action : "login");
		
		switch ($action) { 
			case "login":
				$this->login();
				break;				
			case "logout":
				$this->logout();
				break;
		}
	}
	
	private function login() {
		$redirection_url = (isset($this->post->redirection_url) ? $this->post->redirection_url : $this->site_url);
		$redirection_url_query = parse_url($redirection_url, PHP_URL_QUERY);
		$separator = (empty($redirection_url_query) ? "?": "&");
		$successfull_url = (isset($this->post->successfull_url) ? $this->post->successfull_url : $redirection_url . $separator . "auth=success");
		$unsuccessfull_url = (isset($this->post->unsuccessfull_url) ? $this->post->unsuccessfull_url : $redirection_url . $separator . "auth=failed");
		
		$user_id = $this->authorize($this->post->login, $this->post->password);
		if ($user_id) {
			$this->Authorization->setNewToken($user_id);
			$redirection_url  = $successfull_url;		
		}
		else {
			$redirection_url  = $unsuccessfull_url;	
		}

		$this->redirect($redirection_url);	
	}
	
	private function logout() {
		$this->Authorization->removeToken();
		$this->redirect($this->site_url);
	}
	
	public function authorize($login=null, $password=null) {
		$credentials = (!empty($login) && !empty($password) ? ['login'=>$login, 'password'=>$password] : $this->getToken());
		$user_id = $this->Authorization->getUserId($credentials);
		
		return $user_id;
	}
	
	private function getToken() {
		$token = (isset($_COOKIE['token']) ? $_COOKIE['token'] : null);
		
		return $token;
	}
}
