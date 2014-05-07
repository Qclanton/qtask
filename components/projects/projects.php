<?php
namespace Components;
 
class Projects extends Components {
	public function prepare() {
		$this->defineUserLevel("Projects");
		$this->loadModels(["Users", "Components", "Projects"]);		
	}
	
	public function load() {
		$action = (isset($this->get->action) ? $this->get->action : "list");	
		
		switch ($action) { 
			case "list":
				$this->setListContent();
				break;
			case "project":
				$this->setRules($action);
				if ($this->validate()) {
					$project = $this->Projects->getProject($this->get->id);
					if (empty($project)) {
						$this->loadhelpers(["ErrorHandler"]);
						$this->content['error'] = $this->ErrorHandler->getHtml([["project" => "Project doesn't exists"]]);
						return false;
					}
									
					$this->setProjectContent($project);
					
					$this->loadComponents(["Tasks"]);
					$this->Tasks->setListContent($this->get->id);
					$this->Tasks->renderViewContent();
					$this->content['bottom'] = $this->Tasks->View->content;
					$this->setUserLastVisitDate($this->Tasks->name);
				}
				break;
		}
		
		$this->renderViewContent();		
		$this->content['top']  = $this->View->content;		
	}
	
	private function setListContent() {	
		$projects = $this->Projects->getProjects();
				
		$vars = [
			'user_level' => $this->user_level,
			'projects' => $projects		
		];
		
		$this->setView("components/projects/views/list.php", $vars);
	}
	
	private function setProjectContent($project) {		
		$vars = [
			'user_level' => $this->user_level,
			'project' => (object)$project
		];
		
		$this->setView("components/projects/views/project.php", $vars);
	}	

	public function setRules($action) {
		switch ($action) { 
			case "project":
				$this->rules = [
					'id' => [
						'filter' => "int",
						'error_message' => "Project is not specified"
					]
				];
				break;
		}
	}	
}
