<?php
namespace Helpers;

class Database {
	public $connection;
	public $error;
	
	
	public function connect($settings) {
		$connection = new \mysqli($settings['host'], $settings['user'], $settings['password'], $settings['db_name']);
				
		if (mysqli_connect_error()) {
			$this->error =  [['database' => "Connection failed: " . mysqli_connect_error()]];
			$result = false;
		}
		else {
			$this->setConnection($connection);
			$result = true;
		}
		
		return $result;	
	}
	
	public function setConnection($connection) {		
		$this->connection = $connection;
	}
	
	public function closeConnection($connection = null) {
		if (empty($connection)) { $connection = $this->connection; }
		
		$connection->close;
	}
	
	private function getReferences($array) {  
		$references = [];
		 
		foreach($array as $key => $value) {
			$references[$key] = &$array[$key];
		}
		
		return $references; 
	}
	
	private function prepareStatement($query, array $params = []) {
		$statement = $this->connection->prepare($query);		
		
		if (!$statement) {
			$this->error = [['database' => "Prepare failed"]];
			return false;
		}
		
		if (!empty($params)) {
			$params_types = "";
			$params_values = [];
			foreach ($params as $param) {
				$param_type = (isset($param['type']) ? $param['type'] : "s");
				$param_value = (isset($param['value']) ? $param['value'] : $param);
				
				$params_types .= $param_type;
				$params_values[] = $param_value;
			}
			array_unshift($params_values, $params_types);
			call_user_func_array([$statement, 'bind_param'], $this->getReferences($params_values)); 
		}		
		
		
		return $statement;		
	}
	
	private function getData($query, array $params = [], $type = "assoc") {
		$statement = $this->prepareStatement($query, $params);
		if (!$statement) { return false; }
		$statement->execute();		
		   
		$meta = $statement->result_metadata();
		while ($field = $meta->fetch_field()) { 
			 $columns[] = &$row[$field->name]; 
		} 

		call_user_func_array([$statement, 'bind_result'], $columns); 

		$result = [];
		while ($statement->fetch()) {			
			foreach ($row as $key=>$value) {
				$row_data[$key] = $value;
			}
					 
			$result[] = ($type == "assoc" ? $row_data : (object)$row_data);
		}
				
		$statement->close();
		
		
		return $result;
	}
	
	
	public function getObject($query, array $params = []) {
		$result = $this->getData($query, $params, "object");		
		if (!$result) { return false; }
		
		return $result;
	}
	
	public function getRows($query, array $params = [], $type = "assoc") {
		$result = $this->getData($query, $params, $type);		
		if (!$result) { return false; }
		
		return $result;
	}
		
	public function getRow($query, array $params = []) {
		$result = $this->getData($query, $params);		
		if (!$result) { return false; }
		
		return $result[0];
	}
	
	public function getValue($query, array $params = []) {
		$result = $this->getData($query, $params);
		if (!$result) { return false; }
		$num_result = array_values($result[0]);
		
		return $num_result[0];
	}
	
	/*
	private function executeTransaction($queries) {
		$this->connection->autocommit(false);
		foreach ($queries as $query) {
			$this->connection->query($query);
		}
		
		if (!$this->connection->commit()) {
			return false;
		}
		
		return true;		
	}
	*/

	public function executeQuery($query, array $params = []) {
		$statement = $this->prepareStatement($query, $params);
		if (!$statement) { return false; }
		
		$statement->execute();
		$statement->close();
		
		return true;	
	}
}
?>
