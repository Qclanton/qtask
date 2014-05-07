<?php
namespace Components;
 
class Filters extends Components {
	public $name = "Filters";
	
	public function prepare() {
		$this->defineUserLevel($this->name);
		$this->loadModels(["Filters", "Tasks", "Projects"]);
		$this->loadHelpers(["Sidebar"]);
	}
	
	public function load() {
		$filter = (isset($this->get->filter) ? $this->get->filter : "mytasks");		
		
		switch($filter) {
			case "mytasks":
				if (empty($this->user_id)) { $this->redirect($this->site_url); }				
			
				$params = $this->getPreparedParams($filter);
				$tasks = $this->Filters->getTasks($params);				
				$tasks = $this->Tasks->attachCommentsQty($tasks, $this->user_id, $this->getUserLastVisitDate($filter . $this->name));
				$this->setView("components/filters/views/tasks.php", ['tasks'=>$tasks]);
				$this->renderViewContent();
				$this->content['top'] = $this->View->content;
				
				$this->setUserLastVisitDate($filter . $this->name);	
				break;
		}
	}
	

	private function getPreparedParams($filter) {
		switch($filter) {
			case "mytasks":				
				$closed_statuses = [];
				$projects_closed_statuses = $this->Projects->getStatuses(null, "YES");
				foreach ($projects_closed_statuses as $project_closed_status) {
					$closed_statuses[] = [
						'comparsion_sign' => "!=",
						'value' => $project_closed_status->id
					];						
				}
				
				$params = [
					'assigned' => [
						'type' => [
							'comparsion_sign' => "=",
							'value' => "USER"
						],
						'id' => [
							'comparsion_sign' => "=",
							'value' => $this->user_id		
						]
					],
					'statuses' => $closed_statuses
				];
				break;
		}
		
		return $params;	
	}
}
?>
