<?php
namespace Helpers;

class Sanitizer extends \System {
	public $rules = [];
	
	
	public function setRule($rule) {
		$this->rules = array_merge($this->rules, $rule);
	}
	
	public function setRules($rules) {	
		foreach ($rules as $var_name=>$rule_options) {
			$this->setRule([$var_name=>$rule_options]); 
		}
	}
	
	
	public function sanitizeVar($name, $value) {
		$sanitized_var = $value;
		
		if (array_key_exists($name, $this->rules)) {
			$rule = $this->rules[$name];
			
			$filter_type = gettype($rule['filter']);			
			$filter = ($filter_type == "string" ?  $this->getFilter($rule['filter']) : $rule['filter']);
			$flags = (array_key_exists('flags', $rule) ? $rule['flags'] : null);
				
			$sanitized_var = filter_var($value, $filter, $flags);	
		}
		
		return $sanitized_var;
	}
	
	public function sanitizeVars($vars) {
		foreach ($vars as $key=>&$value) {
			$value = $this->sanitizeVar($key, $value);
		}
		
		return $vars;
	}
	
	
	private function getFilter($name) {
		$filters = [
			'email' => FILTER_SANITIZE_EMAIL,
			'encoded' => FILTER_SANITIZE_ENCODED,
			'magic_quotes' => FILTER_SANITIZE_MAGIC_QUOTES,
			'number_float' => FILTER_SANITIZE_NUMBER_FLOAT,
			'number_int' => FILTER_SANITIZE_NUMBER_INT,
			'special_chars' => FILTER_SANITIZE_SPECIAL_CHARS,
			'full_special_chars' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
			'string' => FILTER_SANITIZE_STRING,
			'stripped' => FILTER_SANITIZE_STRIPPED,
			'url' => FILTER_SANITIZE_URL,
			'unsafe_raw' => FILTER_UNSAFE_RAW,			
		];
		
		$filter = (array_key_exists($name, $filters) ? $filters[$name] : false);
		
		return $filter;
	}
} 
