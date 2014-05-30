<?php
namespace Components;
 
class Filters extends Components {
	public $name = 'Filters';
	
	public function prepare() {
		$this->defineUserLevel($this->name);
		$this->loadModels(['Filters', 'Tasks', 'Projects']);
		$this->loadHelpers(['Breadcrumbs']);
	}
	
	public function load() {
		if (empty($this->user_id)) { $this->redirect($this->site_url); }
		
		$filter = (isset($this->get->filter) ? $this->get->filter : 'mytasks');
		
		switch($filter) {
			case 'showactions':			
			case 'mytasks':
				$this->showMyTasks();		
				break;
		}
	}
	
	private function showMyTasks() {
		$show_closed_fl = (isset($this->get->show_closed_fl) ? $this->get->show_closed_fl : 'no');
		$params = $this->getPreparedParams('mytasks', ['show_closed_fl'=>$show_closed_fl]);
		$tasks = $this->Filters->getTasks($params);				
		$tasks = $this->Tasks->attachCommentsQty($tasks, $this->user_id);
		
		// Flush Temporary Settings
		$this->flushSetting('mytask_hidden_projects');
				
		// Get Settings
		$hidden_projects = $this->Settings->getSetting('mytask_hidden_projects', 'user', ['id' => $this->user_id]);
						
		// Set Temporary Settings
		setcookie('Settings-' . $this->name . '-mytask_hidden_projects', implode(',', $hidden_projects), time()+604800);
		
		// Get Projects
		$projects = $this->Projects->getProjects($this->user_id, ($show_closed_fl == 'no' ? 'yes' : 'no'));
		foreach ($projects as &$project) {
			$project->hidden_fl = (in_array($project->id, $hidden_projects) ? 'yes' : 'no');					
		}
		
		$vars = [
			'tasks' => $tasks,
			'projects' => $projects
		];
		
		$this->setView('components/filters/views/tasks.php', $vars);
		$this->renderViewContent();
		$this->content['top'] = $this->View->content;
		
		$breadcrumbs = [
			'Filters' => $this->site_url . 'index.php?component=filters&action=showactions',
			'My Tasks' => ''
		];
		$this->content['breadcrumbs'] = $this->Breadcrumbs->getHtml($breadcrumbs);		
	}

	private function flushSetting($name) {
		$fullname = 'Settings-' . $this->name . '-' . $name;
		
		$this->loadModels(['Settings']);
		
		// Save Settings
		if (isset($_COOKIE[$fullname])) {					
			$setting = [
				'id' => null,
				'layout' => 'user',
				'unique_key' => 'id',
				'unique_value' => $this->user_id,
				'key' => $name,
				'value' => json_encode(explode(',', $_COOKIE[$fullname])),
				'json_fl' => 'YES'
			];
			$this->Settings->setSetting($setting);
			
			// Remove cookie
			setcookie($fullname, '', time()-1000000000000);
		}
	}
	
	private function getPreparedParams($filter, $vars) {
		switch($filter) {
			case 'mytasks':				
				$closed_statuses = [];
				$projects_closed_statuses = $this->Projects->getStatuses(null, 'YES');
				foreach ($projects_closed_statuses as $project_closed_status) {
					$closed_statuses[] = [
						'comparsion_sign' => '!=',
						'value' => $project_closed_status->id
					];						
				}
				
				$params = [
					'assigned' => [
						'type' => [
							'comparsion_sign' => '=',
							'value' => 'USER'
						],
						'id' => [
							'comparsion_sign' => '=',
							'value' => $this->user_id		
						]
					]
				];
				
				if ($vars['show_closed_fl'] == 'yes') {
					$params['confines'] = [
						'order'=>[
							['column'=>'status_id', 'side'=>'ASC'],
							['column'=>'closed_date', 'side'=>'DESC'],
							['column'=>'creation_date', 'side'=>'DESC'],
						]
					];				
				}
				else {
					$params['statuses'] = $closed_statuses;
				}
				break;
		}
		
		return $params;	
	}
}
?>
