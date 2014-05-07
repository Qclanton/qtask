<?
namespace Helpers;

class ErrorHandler extends \System {
	public $errors = [];
	
	
	public function setError($error) {
		$this->errors[] = $error;
	}
	
	public function setErrors($errors) {
		foreach ($errors as $error) {
			$this->setError($error);
		}
	}
	
	public function getErrors() {
		return $this->errors;
	}
	
	public function getHtml($errors=[]) {
		$errors = array_merge($errors, $this->errors);
		
		$this->setView("helpers/error_handler/views/error.php", ['errors'=>$errors]);
		$this->renderViewContent();
		
		return $this->View->content;
	}	
}
?>
