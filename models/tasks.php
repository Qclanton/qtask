<?php
namespace Models;
 
class Tasks extends Models {
	
	public function setTask($data) {
		$this->loadModels(['Projects']);
		
		$task = (object)$data;
		$task = $this->nullValues($task, ['id', 'parent_task_id', 'due_date', 'closed_date']);
		
		$closed_statuses = $this->Projects->getStatuses($task->project_id, 'YES', 'yes');
		
		if (in_array($task->status_id, $closed_statuses)) {
			$task->closed_date = date('Y-m-d H:i:s');
		}
		if ($task->id == null) {
			$task->creation_date = date('Y-m-d H:i:s');
		}

		$query = "
			INSERT INTO `tasks` (
				`id`,
				`parent_task_id`,
				`project_id`,
				`status_id`,
				`priority_id`,
				`author_id`,
				`assigned_type`,
				`assigned_id`,
				`creation_date`,
				`due_date`,
				`closed_date`,
				`title`,
				`text`
			)
			VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
			ON DUPLICATE KEY UPDATE
				`parent_task_id`=?,
				`project_id`=?,
				`status_id`=?,
				`priority_id`=?,
				`author_id`=?,
				`assigned_type`=?,
				`assigned_id`=?,
				`creation_date`=?,
				`due_date`=?,
				`closed_date`=?,
				`title`=?,
				`text`=?
			";
			
			$vars = [
				$task->id,
				$task->parent_task_id,
				$task->project_id,
				$task->status_id,
				$task->priority_id,
				$task->author_id,
				$task->assigned_type,
				$task->assigned_id,
				$task->creation_date,
				$task->due_date,
				$task->closed_date,
				$task->title,
				$task->text,
				$task->parent_task_id,
				$task->project_id,
				$task->status_id,
				$task->priority_id,
				$task->author_id,
				$task->assigned_type,
				$task->assigned_id,
				$task->creation_date,
				$task->due_date,
				$task->closed_date,
				$task->title,
				$task->text
			];
			
			$result = $this->Database->executeQuery($query, $vars);
			
			return $result;
	}
	
	public function tieTask($task_id, $tied_task_id, $depended_object="NONE") {
		$query = "INSERT INTO `tasks_tied` VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE `depended_object`=?";
		$vars = [$task_id, $tied_task_id, $depended_object, $depended_object];
		$result = $this->Database->executeQuery($query, $vars);		
		
		return $result;
	}
	
	public function untieTask($task_id, $tied_task_id) {
		$query = "DELETE FROM `tasks_tied` WHERE (`task_id`=? AND `tied_task_id`=?) OR (`tied_task_id`=? AND `task_id`=?)";
		$vars = [$task_id, $tied_task_id, $task_id, $tied_task_id];
		$result = $this->Database->executeQuery($query, $vars);		
		
		return $result;
	}
	
	
	// Select section
	const TASK_QUERY_BASE_SUBJECT="
			t.*,
			p.`title` AS 'project',
			s.`title` AS 'status',
			s.`classes` as 'status_classes',
			pri.`title` AS 'priority',
			pri.`weight` AS 'priority_weight',
			pri.`classes` AS 'priority_classes',
			author.`name` as 'author',
				CASE 
					WHEN (t.`assigned_type`='GROUP') THEN
						(SELECT `title` FROM `user_groups` ug WHERE ug. `id`=t.`assigned_id`)
					WHEN (t.`assigned_type`='USER') THEN
						(SELECT `name` FROM `users` u WHERE u. `id`=t.`assigned_id`)
				END 
			AS 'assigned' 
	";
	const TASK_QUERY_BASE_OBJECT="
			`tasks` t JOIN 
			`projects` p ON (p.`id`=t.`project_id`) JOIN
			`statuses` s ON (s.`id`=t.`status_id`) JOIN
			`priorities` pri ON (pri.`id`=t.`priority_id`) JOIN 
			`users` author ON (author.`id`=t.`author_id`)
	";	
	const TASK_QUERY_BASE_COLUMNS = "id|parent_task_id|project_id|status_id|priority_id|author_id|assigned_type|assigned_id|due_date|closed_date|title|text|project|status|status_classes|priority|priority_weight|priority_classes";
	
	
	
	public function getTasks($project_id=null, $params=[]) {
		$query = "SELECT " . self::TASK_QUERY_BASE_SUBJECT . " FROM " . self::TASK_QUERY_BASE_OBJECT . " WHERE ?";
		$vars = ["1"];		
		if (!empty($project_id)) {
			$query .= " AND t.`project_id`=?";
			$vars[] = $project_id;
		}	
	
		$confines = $this->getConfines($params);
		$query .= $confines['query'];
		$vars = array_merge($vars, $confines['vars']);
		
		$tasks = $this->Database->getObject($query, $vars);
		
		return $tasks;
	}
	
	
	public function getTask($id) {
		$query = "SELECT " . self::TASK_QUERY_BASE_SUBJECT . " FROM " . self::TASK_QUERY_BASE_OBJECT . " WHERE t.`id`=?";
		$task = $this->Database->getRow($query, [$id]);
		
		return $task;	
	}
	
	public function getSubtasks($task_id) {
		$query = "SELECT " . self::TASK_QUERY_BASE_SUBJECT . " FROM " . self::TASK_QUERY_BASE_OBJECT . " WHERE t.`parent_task_id`=?";
		$subtasks = $this->Database->getObject($query, [$task_id]);
		
		return $subtasks; 
	}
	
	public function attachSubtasks($tasks) {
		foreach ($tasks as &$task) {
			$task->subtasks = $this->getSubtasks($task->id);
		}
		
		return $tasks;
	}
	
	public function getTasksForTie($task_id=null, $user_id=null) {
		$params = [
			'order'=>[
				['column'=>"creation_date", 'side'=>"DESC"]
			],
			'limit_qty' => 10
		];
			
		$query = "SELECT " . self::TASK_QUERY_BASE_SUBJECT . " FROM " . self::TASK_QUERY_BASE_OBJECT . " WHERE ?";
		$vars = ["1"];
		
		if (!empty($task_id)) {
			$query .= " AND t.`id`!=?";
			$vars[] = $task_id;
		}
		
		$confines = $this->getConfines($params);
		$query .= $confines['query'];
		$vars = array_merge($vars, $confines['vars']);
		
		$tasks = $this->Database->getObject($query, $vars);
		
		return $tasks;
	}
	
	public function getTiedTasks($task_id) {
		$query = "
			SELECT " . 
				self::TASK_QUERY_BASE_SUBJECT . 
				", 				
					CASE 
						WHEN (tt.`task_id`=t.`id` AND tt.`depended_object`='TIED_TASK') THEN 'TASK'
						WHEN (tt.`task_id`=t.`id` AND tt.`depended_object`='TASK') THEN 'TIED_TASK'
						ELSE  tt.`depended_object`
					END
				AS 'depended_object'
			FROM " . 
				self::TASK_QUERY_BASE_OBJECT . " 
			JOIN
				`tasks_tied` tt ON (
					(tt.`task_id`=t.`id` AND tt.`tied_task_id`=?) OR 
					(tt.`tied_task_id`=t.`id` AND tt.`task_id`=?)
				)
		";
	
		$tied_tasks = $this->Database->getObject($query, [$task_id, $task_id]);
		
		return $tied_tasks;
	}
	
	
	public function getDefaultTask($user_id="1", $project_id="1") {
		$this->loadModels(["Settings"]);
		
		$settings = $this->Settings->getSettings('project', ['id'=>$project_id]);
		
		$task = (object)[
			'id' => null,
			'parent_task_id' => null,
			'project_id' => $project_id,
			'status_id' => $settings->default_status_id,
			'priority_id' => $settings->default_priority_id,
			'author_id' => $user_id,
			'assigned_type' => "USER",
			'assigned_id' => $user_id,
			'creation_date' => date("Y-m-d H:i:s"),
			'due_date' => null,
			'closed_date' => null,
			'title' => "",
			'text' => ""
		];
		
		return $task;	
	}	



	// Last watched tasks
	public function setLastWatchedTask($task_id, $user_id) {
		$query = "INSERT INTO `history` VALUES(NULL, NOW(), 'user', 'id', ?, 'task_watch', ?)";
		$vars = [$user_id, $task_id];
		
		$result = $this->Database->executeQuery($query, $vars);
		
		return $result;
	}
	public function getLastWatchedTasks($user_id, $limit=10) {	
		$query = "
			SELECT DISTINCT
				" . self::TASK_QUERY_BASE_SUBJECT . "
			FROM 
				 " . self::TASK_QUERY_BASE_OBJECT . " JOIN
				 `history` h ON (h.`value`=t.`id`)
			WHERE 
				h.`layout`='user' AND
				h.`unique_key`='id' AND
				h.`unique_value`=? AND
				h.`key`='task_watch'
		";
		$vars = [$user_id];
		
		$confines = $this->getConfines(['limit_qty' => (int)$limit]);
		$query .= $confines['query'];
		$vars = array_merge($vars, $confines['vars']);
				
		$tasks = $this->Database->getObject($query, $vars);
		
		return $tasks;
	}



	// Functions for Comments
	public function setComment($data) {
		$comment = (object)$data;
		$comment = $this->nullValues($comment, ["id", "modification_date"]);
		if (empty($comment->id)) {
			$comment->creation_date = date("Y-m-d H:i:s"); 
		}
		else { 
			$comment->modification_date = date("Y-m-d H:i:s"); 
		}
		
		$query = "
			INSERT INTO `tasks_comments` (`id`, `task_id`, `user_id`, `text`, `creation_date`, `modification_date`) VALUES (?, ?, ?, ?, ?, ?)			
			ON DUPLICATE KEY UPDATE `text`=?, `modification_date`=?				
		";
		$vars = [
			$comment->id, 
			$comment->task_id, 
			$comment->user_id, 
			$comment->text, 
			$comment->creation_date, 
			$comment->modification_date,
			$comment->text,
			$comment->modification_date 
		];
		
		$result = $this->Database->executeQuery($query, $vars);
		
		return $result;
	}
	
	public function getTaskCommentsQty($task_id, $user_id, $from_date=null) {
		$query = "SELECT COUNT(`id`) FROM `tasks_comments` WHERE `task_id`=? AND `user_id`!=?";
		$vars = [$task_id, $user_id];
		
		if (!empty($from_date)) { 
			$query .= " AND `creation_date`>=?";
			$vars[] = $from_date;
		}
		
		$result = $this->Database->getValue($query, $vars);
		
		return $result;
	}
	
	public function attachCommentsQty($tasks, $user_id, $from_date=null) {
		if (!$tasks) { return false; }
		
		foreach ($tasks as &$task) {
			$task->comments_qty = $this->getTaskCommentsQty($task->id, $user_id, $from_date);
		}

		return $tasks;
	}
	
	
	const COMMENT_QUERY_BASE = "	
		SELECT 
			tc.*,
			u.name as 'user_name' 
		FROM 
			`tasks_comments` tc JOIN
			`users` u ON (u.`id`=tc.`user_id`)
	";
	 
	public function getComments($task_id) {
		$query = self::COMMENT_QUERY_BASE . " WHERE tc.`removed_fl`='NO' AND tc.`task_id`=? ORDER BY tc.`creation_date` ASC";
		$comments = $this->Database->getObject($query, [$task_id]);
		
		return $comments;		
	}
	
	public function getComment($comment_id) {
		$query = self::COMMENT_QUERY_BASE . " WHERE tc.`removed_fl`='NO' AND tc.`id`=?";
		$comment = $this->Database->getRow($query, [$comment_id]);
		
		return (object)$comment;
	}
	
	public function markCommentAsRemoved($comment_id) {
		$query = "UPDATE `tasks_comments` SET `removed_fl`='YES' WHERE `id`=?";
		$result = $this->Database->executeQuery($query, [$comment_id]);
			
		return $result;		
	}
	
	public function getDefaultComment($task_id, $user_id) {
		$this->loadModels(["Users"]);		
		$user = $this->Users->getUser($user_id);

		$comment = [
			'id' => null,
			'task_id' => $task_id,
			'user_id' => $user_id,
			'text' => "",
			'creation_date' => date("Y-m-d H:i:s"),
			'modification_date' => null,
			'user_name' => $user['name']
		];
		
		return (object)$comment;
	}
}
