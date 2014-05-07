<?php
namespace Helpers;

class Validator extends \System {
	public $rules = [];	
	public $errors = [];
	
	public function setRule($rule) {
		$this->rules = array_merge($this->rules, $rule);
	}
	
	public function setRules($rules) {	
		foreach ($rules as $var_name=>$rule_options) {
			$this->setRule([$var_name=>$rule_options]); 
		}
	}
	
	public function setError($error) {
		$this->errors[] = $error;
	}
	
	
	private function getFilter($name) {
		$filters = [
			'boolean' => FILTER_VALIDATE_BOOLEAN,
			'validate_email' => FILTER_VALIDATE_EMAIL,
			'float' => FILTER_VALIDATE_FLOAT,
			'int' => FILTER_VALIDATE_INT,
			'validate_ip' => FILTER_VALIDATE_IP,
			'validate_regexp' => FILTER_VALIDATE_REGEXP,
			'validate_url' => FILTER_VALIDATE_URL			
		];
		
		$filter = (array_key_exists($name, $filters) ? $filters[$name] : false);
		
		return $filter;
	}
	
	public function validateIsset($list=[], $vars=[]) {
		if (empty($list) || empty($vars)) { return false; }
		
		$vars = (object)$vars;
		$result = true;
		foreach ($list as $element) {
			if (!isset($vars->{$element})) {
				$this->setError([$element => $element . " doesn't specified"]);
				$result = false;
			}
		}
		
		return $result;
	}
	
	public function validateVar($name, $value) {
		$result = false;
		
		if (array_key_exists($name, $this->rules)) {
			$rule = $this->rules[$name];
			
			$filter_type = gettype($rule['filter']);			
			$filter = ($filter_type == "string" ?  $this->getFilter($rule['filter']) : $rule['filter']);
			$flags = (array_key_exists('flags', $rule) ? $rule['flags'] : null);
			$options = (array_key_exists('options', $rule) ? $rule['options'] : null);
			$canbeempty_fl =  (array_key_exists('canbeempty_fl', $rule) ? $rule['canbeempty_fl'] : "no");
			
			if ($canbeempty_fl == "yes" && empty($value)) { return true; }
			
			if ($filter == FILTER_VALIDATE_REGEXP && array_key_exists('regexp', $rule)) {
				$options = ['regexp' => $this->getPreparedRegexp($rule['regexp'])];
			}
		
		
			$result = filter_var($value, $filter, ['flags'=>$flags, 'options'=>$options]);
			if ($result === false) {
				$error_message = (array_key_exists('error_message', $rule) ? $rule['error_message'] : "Something wrong");
				$this->setError([$name => $error_message]);
			}
			
		}
		
		return $result;
	}
	
	private function getPreparedRegexp($name) {
		$regexps = [
			'phone' => "/^[0-9 -\.\(\)]{6,20}$/",
			'date' => "/(19|20)\d\d-((0[1-9]|1[012])-(0[1-9]|[12]\d)|(0[13-9]|1[012])-30|(0[13578]|1[02])-31)/",
			'shortstring' => "/^(.){1,255}$/",
			'longstring' => "/^(.){1,4096}$/"
		];
		
		return $regexps[$name];
	}
	
	public function validateVars($vars) {
		$result = true;
		foreach ($vars as $key=>$value) {
			$result = $this->validateVar($key, $value);
		}
		
		return $result;
	}
}
