<?php 
namespace Models;
 
class Projects extends Models {
	public function getProjects() {
		$query = "SELECT * FROM `projects`";
		$projects = $this->Database->getObject($query);
		
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
