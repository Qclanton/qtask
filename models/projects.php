<?php 
namespace Models;
 
class Projects extends Models {
	const PROJECT_QUERY_BASE_SUBJECT="
		p.*
	";
	const PROJECT_QUERY_BASE_OBJECT="
		`projects` p
	";
	
		
	public function getProjects($user_id=null, $count_only_opened_fl = 'no') {
		if ($user_id) {
			$qty_task_query_part = ", (";
			$qty_task_query_part .= "SELECT COUNT(t.`id`) FROM `tasks` t WHERE t.`project_id`=p.`id`";
			if ($count_only_opened_fl == 'yes') {
				$opened_statuses = $this->getStatuses(null, 'NO', 'yes');
				$qty_task_query_part .= " AND t.`status_id` IN (" . implode(',', $opened_statuses) . ")";
			}
			$qty_task_query_part .= "  AND `assigned_id`=?";
			$qty_task_query_part .= ") AS 'tasks_qty'";
		} 
		else {
			$qty_task_query_part = "";
		}
		
		$vars = [];
		$query = "SELECT " . self::PROJECT_QUERY_BASE_SUBJECT . $qty_task_query_part . " FROM " . self::PROJECT_QUERY_BASE_OBJECT . " WHERE ?";
		if ($user_id) { $vars[] = $user_id; }
		$vars[] = '1';		
		
		$projects = $this->Database->getObject($query, $vars);
		
		return $projects;
	}
	
	public function getProject($id) {
		$query = "SELECT * FROM `projects` WHERE `id`=?";
		$project = $this->Database->getRow($query, [$id]);
		
		return $project;
	}
	
	public function getStatuses($project_id=null, $closed_fl=null, $simple_arr_fl='no') {
		$query = "SELECT s.* FROM `projects_statuses` ps JOIN `statuses` s ON (s.`id`=ps.`status_id`) WHERE ?";
		$vars = ["1"];
		
		if (!empty($project_id)) {
			$query .= " AND ps.`project_id`=?";
			$vars[] = $project_id;
		}
		if (in_array($closed_fl, ["YES", "NO"])) {
			$query .= " AND s.`closed_fl`=?";
			$vars[] = $closed_fl;
		}
		
		$statuses_data = $this->Database->getObject($query, $vars);
		
		// Convert to array if it necessary
		if ($statuses_data && $simple_arr_fl == 'yes') {
			$statuses = [];
			foreach ($statuses_data as $status_data) {
				$statuses[] = $status_data->id;
			}
		}
		else {
			$statuses = $statuses_data;
		}
		
		
		return $statuses;
	}
	
	public function getPriorities($project_id) {
		$query = "SELECT p.* FROM `projects_priorities` pp JOIN `priorities` p ON (p.`id`=pp.`priority_id`) WHERE pp.`project_id`=?";
		$priorities = $this->Database->getObject($query, [$project_id]);
		
		return $priorities;
	}	
}
?>
