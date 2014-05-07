<?php
namespace Models;

class Authorization extends Models {
	public function getUserId($credentials) {
		if (is_array($credentials)) {
			$query = "SELECT `id` FROM `users` WHERE `login`=? AND `password`=PASSWORD(?)";
			$vars = [$credentials['login'], $credentials['password']];
		}
		else {
			$query = "SELECT `user_id` FROM `users_tokens` WHERE `expiration_date` >= NOW() AND `token`=? AND `secret`=SHA(?)";
			$vars = [$credentials, $this->getSecret()];
		}

		$user_id = $this->Database->getValue($query, $vars);
		if ($user_id) { $this->checkVisit($user_id); }
		
		return $user_id;
	}
	
	private function checkVisit($user_id) {
		$query = "UPDATE `users` SET `prelast_visit_date`=`last_visit_date`, `last_visit_date`=NOW() WHERE `id`=?";
		$this->Database->executeQuery($query, [$user_id]);
	}
	
	public function getNewToken() {
		$token = uniqid("token_", true);
		
		return $token;		
	}
	
	public function setNewToken($user_id) {
		$this->loadModels(["Settings"]);
		$settings = $this->Settings->getSettings("user");
		$token = $this->getNewToken();
		$secret = $this->getSecret();
		
		// Remove existed token from DB
		$query = "DELETE FROM `users_tokens` WHERE `user_id`=? AND `secret`=SHA(?)";
		$this->Database->executeQuery($query, [$user_id, $secret]);
		
		
		$this->normalizeTokenQuantity($user_id, $settings->token_max_qty);
				
		$query = "
			INSERT INTO `users_tokens` (
				`user_id`,
				`token`,
				`expiration_date`,
				`secret`
			)
			VALUES (
				?,
				?,
				DATE_ADD(NOW(), INTERVAL ? SECOND),
				SHA(?)
			)
		";
		$vars = [
			['type'=>"i", 'value'=>$user_id],
			['type'=>"s", 'value'=>$token],
			['type'=>"i", 'value'=>$settings->token_life],			
			['type'=>"s", 'value'=>$secret]
		];
		$result = $this->Database->executeQuery($query, $vars);
		
		if ($result) {
			setcookie("token", $token, time()+$settings->token_life);
		}
		
		return $result;
	}
	
	private function normalizeTokenQuantity($user_id, $qty_limit) {
		$query = "DELETE FROM `users_tokens` WHERE `expiration_date` < NOW() AND `user_id`=?";
		$this->Database->executeQuery($query, [$user_id]);
		
		$query = "SELECT COUNT(`id`) AS 'qty' FROM `users_tokens` WHERE `user_id`=? AND `expiration_date`>=NOW()";
		$qty = $this->Database->getValue($query, [$user_id]);

		if ($qty >= $qty_limit) {
			$qty_excess = $qty-($qty_limit-1);
			
			$query = "DELETE FROM `users_tokens` WHERE `user_id`=? ORDER BY `expiration_date` ASC LIMIT $qty_excess";
			$this->Database->executeQuery($query, [$user_id]);
		}
	}
	
	public function removeToken() {
		setcookie("token", "", time()-1000000000000);
	}
	
	private function getSecret() {
		return $_SERVER['HTTP_USER_AGENT'] . $_SERVER['HTTP_ACCEPT_LANGUAGE'] . $_SERVER['HTTP_ACCEPT_ENCODING'];
	}
}
