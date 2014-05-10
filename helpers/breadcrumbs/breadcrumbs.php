<?php
namespace Helpers;

class Breadcrumbs extends \System {
	public $separator = " > ";
	
	public function getHtml($elements, $separator=null) {
		if (!$elements) { return false; }
		if (empty($separator)) { $separator = $this->separator; }
		
		$html = "";
		$i=0;
		foreach ($elements as $title=>$link) {
			$i++;
			
			if (count($elements) != $i) {
				$html .= "<a href='$link'>$title</a>$separator";
			}
			else {
				$html .="<a>$title</a>";
			}
		}
		
		return $html;
	}
}
