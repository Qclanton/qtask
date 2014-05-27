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
		$query = "SELECT " . self::PROJECT_QUERY_BASE_SUBJECT . " FROM " . self::PROJECT_QUERY_BASE_OBJECT . " WHERE ?";
		$vars = ['1'];
		
		if ($user_id) {
			// $statuses_confines = ($tasks_statuses ? " AND t.`status_id` IN (" . implode(',', $tasks_statuses) . ")" : "");
			/*if ($count_only_opened_fl == 'yes') {
				$status_object = "
					`projects_statuses` ps ON (ps.`project_id`=p.`id`) JOIN
					`statuses` s ON (s.`id`=ps.`status_id`)
				";
				$tasks_statuses_confines = ""
			}*/
			
			
			
			$query = "
				SELECT " 
					. self::PROJECT_QUERY_BASE_SUBJECT . ",
					COUNT(t.`id`) AS 'tasks_qty'
				FROM " . 
					self::PROJECT_QUERY_BASE_OBJECT . " JOIN
					`tasks` t ON (t.`project_id`=p.`id` $statuses_confines)
				WHERE ?
				AND t.`assigned_id`=?
				GROUP BY p.`id`
			";
			$vars[] = $user_id;
		}
		
		$projects = $this->Database->getObject($query, $vars);
		
		return $projects;
	}
	
	public function getProject($id) {
		$query = "SELECT * FROM `projects` WHERE `id`=?";
		$project = $this->Database->getRow($query, [$id]);
		
		return $project;
	}
	
	public function getStatuses($project_id=null, $closed_fl=null) {
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
		
		$statuses = $this->Database->getObject($query, $vars);
		
		return $statuses;
	}
	
	public function getPriorities($project_id) {
		$query = "SELECT p.* FROM `projects_priorities` pp JOIN `priorities` p ON (p.`id`=pp.`priority_id`) WHERE pp.`project_id`=?";
		$priorities = $this->Database->getObject($query, [$project_id]);
		
		return $priorities;
	}	
}
?>
