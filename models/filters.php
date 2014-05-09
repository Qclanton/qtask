<?php
namespace Models;
 
class Filters extends Models {
	public function getTasks($params=[]) {
		$this->loadModels(["Tasks"]);
		$query = "SELECT " . Tasks::TASK_QUERY_BASE_SUBJECT . " FROM " . Tasks::TASK_QUERY_BASE_OBJECT  . " WHERE ?";
		$vars = ["1"];
		
		if (isset($params['project'])) {
			$query .= " AND t.`project_id` " . $params['project']['comparsion_sign'] . " ?";
			$vars[] = $params['project']['value'];
		}
		if (isset($params['statuses'])) {
			foreach ($params['statuses'] as $status) {
				$query .= " AND t.`status_id` " . $status['comparsion_sign'] . " ?";
				$vars[] = $status['value'];
			}
		}
		if (isset($params['priority'])) {
			$query .= " AND t.`priority_id` " . $params['priority']['comparsion_sign'] . " ?";
			$vars[] = $params['priority']['value'];
		}
		if (isset($params['author'])) {
			$query .= " AND t.`author_id` " . $params['author']['comparsion_sign'] . " ?";
			$vars[] = $params['author']['value'];
		}
		
		if (isset($params['assigned'])) {
			$query .= " AND t.`assigned_type` " . $params['assigned']['type']['comparsion_sign'] . " ?";
			$vars[] = $params['assigned']['type']['value'];
			
			$query .= " AND t.`assigned_id` " . $params['assigned']['id']['comparsion_sign'] . " ?";
			$vars[] = $params['assigned']['id']['value'];
		}
		
		if (isset($params['creation_date'])) {
			$query .=  " AND t.`creation_date` " . $params['creation_date']['comparsion_sign'] . " ?";
			$vars[] = $params['creation_date']['value'];
		}
		if (isset($params['due_date'])) {
			$query .=  " AND t.`due_date` " . $params['due_date']['comparsion_sign'] . " ?";
			$vars[] = $params['due_date']['value'];
		}
		if (isset($params['closed_date'])) {
			$query .=  " AND t.`closed_date` " . $params['closed_date']['comparsion_sign'] . " ?";
			$vars[] = $params['closed_date']['value'];
		}
		
		if (isset($params['title'])) {
			$query .=  " AND t.`title` " . $params['closed_date']['comparsion_sign'] . " ?";
			$vars[] = $params['title']['value'];
		}	
		if (isset($params['text'])) {
			$query .=  " AND t.`text` " . $params['text']['comparsion_sign'] . " ?";
			$vars[] = $params['text']['value'];
		}
				
		
		$result = $this->Database->getObject($query, $vars);

		return $result;				
	}
	
	/*MORE POWERFULL
	 * 		if (isset($params['project'])) {
			foreach ($params['project'] as $project) {
				$query .= " " . $project['conjunction'] . " t.`project_id`" . $project['comparsion_sign'] . "?";
				$vars[] = $project['id'];
			}
		}
		if (isset($params['status'])) {
			foreach ($params['status'] as $status) {
				$query .= " " . $status['conjunction'] . " t.`status_id`" . $status['comparsion_sign'] . "?";
				$vars[] = $status['id'];
			}
		}
		if (isset($params['priority'])) {
			foreach ($params['priority'] as $priority) {
				$query .= " " . $priority['conjunction'] . " t.`priority_id`" . $priority['comparsion_sign'] . "?";
				$vars[] = $priority['id'];
			}
		}
		if (isset($params['author'])) {
			foreach ($params['author'] as $author) {
				$query .= " " . $author['conjunction'] . " t.`author_id`" . $author['comparsion_sign'] . "?";
				$vars[] = $author['id'];
			}
		}
		if (isset($params['assigned']) {
			foreach ($params['author'] as $author) {
				$query .= " " . $project['conjunction'] . " t.`assigned_type`" . $params['assigned']['type']['comparsion_sign'] . "?";
				$vars[] = $params['assigned']['type']['value'];
				
				$query .= " " . $project['conjunction'] . " t.`assigned_id`" . $params['assigned']['id']['comparsion_sign'] . "?";
				$vars[] = $params['assigned']['id']['value'];
			}
		}
		if (isset($params['project'])) {
			foreach ($params['project'] as $project) {
				$query .= " AND t.`project_id`" . $project['comparsion_sign'] . "?";
				$vars[] = $project['id'];
			}
		}
	*/
	/*
	public function setTasksDefaultParams($params=[]) {
		if (!isset($params['project_id'])) { $params['project_id'] = null }
	}*/
	
}
?>
