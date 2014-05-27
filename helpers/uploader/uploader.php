<?php
namespace Helpers;

class Uploader extends \System {
	public $allowed_extensions = ['png', 'jpg', 'gif'];
	public $path = 'uploads/';
	public $uploaded_files = [];
	
	
	public function upload() {
		if (!isset($_FILES['upl']) || empty($_FILES['upl'])) { return false; }

		$result = (isset($_FILES['upl']['error'][0]) ? $this->uploadMulti() : $this->uploadSingle());
		
		return $result;
	}
	
	private function uploadSingle() {
		if ($_FILES['upl']['error'] != 0) { return false; }
			 
		$extension = pathinfo($_FILES['upl']['name'], PATHINFO_EXTENSION);			
		if(!in_array(strtolower($extension), $this->allowed_extensions)) { $result = false; }
		
		$result = (move_uploaded_file($_FILES['upl']['tmp_name'], $this->path . '/' . $_FILES['upl']['name']) ? true : false);
		if ($result) { 
			$this->uploaded_files[] = $this->site_url .  $this->path . '/' . $_FILES['upl']['name']; 
		}

		return $result;
	}
	
	private function uploadMulti() {
		$result = true;
		for ($i=0; $i<count($_FILES['upl']['tmp_name']); $i++) {
			if ($_FILES['upl']['error'][$i] != 0) { return false; }
			 
			$extension = pathinfo($_FILES['upl']['name'][$i], PATHINFO_EXTENSION);			
			if(!in_array(strtolower($extension), $this->allowed_extensions)) { $result = false; }
		
			$result = (move_uploaded_file($_FILES['upl']['tmp_name'][$i], $this->path . '/' . $_FILES['upl']['name'][$i]) ? true : false);
			if ($result) { 
				$this->uploaded_files[] = $this->site_url .  $this->path . '/' . $_FILES['upl']['name'][$i]; 
			}
		}
		
		return $result;	
	}
}
