<?php
namespace Components;
 
class Tasks extends Components {
	public $name = "Tasks";
	
	public function prepare() {
		$this->defineUserLevel($this->name);
		$this->loadModels(["Tasks", "Components", "Users"]);
	}
	
	public function load() {
		$action = (isset($this->get->action) ? $this->get->action : "list");
		
		$this->setNecessaryVars($action);
		$this->setRules($action);
		if (!$this->validate()) { return false; }

		switch ($action) { 
			case "list":								
				$this->setListContent($this->get->project_id);
				$this->renderViewContent();
				$this->content['top'] = $this->View->content;
				$this->setUserLastVisitDate($this->name);
				break;
			case "showsetform":
				$id = (isset($this->get->id) ? $this->get->id : null);
				$parent_task_id = (isset($this->get->parent_task_id) ? $this->get->parent_task_id : null);
				
				$this->setTaskformContent($id, null, $parent_task_id);
				$this->renderViewContent();
				$this->content['top'] = $this->View->content;
				
				// Show comments
				if (!empty($id)) { $this->showComments($id); }				
				break;
			case "show":							
				$result = $this->setTaskContent($this->get->id);
				if (!$result) {
					$this->loadhelpers(["ErrorHandler"]);
					$this->content['error'] = $this->ErrorHandler->getHtml([['task' => "Task doesn't exists"]]);
					return false;
				}
				$this->renderViewContent();
				$this->content['top'] = $this->View->content;				
				$this->showComments($this->get->id);
				break;
			case "geteditform":			
				$task = $this->Tasks->getTask($this->get->id);
				
				if (!$task) { return false; }
				if (isset($this->get->return_url)) { $this->current_url = $this->get->return_url; }
				
				$this->setTaskformContent(null, $task);
				$this->renderViewContent();
				echo $this->View->content;
				break;
			case "set":
				// Checking is there cause exist necessity to turn user back to form
				$rules = $this->getSetformRules();
				$this->setRules($rules);
				if ($this->validate()) {				
					$this->Tasks->setTask($this->post);
					$redirection_url = (empty($this->post->id) ? $this->site_url . "index.php?component=tasks&action=list&project_id=" . $this->post->project_id : $this->site_url . "index.php?component=tasks&action=show&id=" . $this->post->id);
					$this->redirect($redirection_url);
				}
				else {
					$id = (isset($this->post->id) ? $this->post->id : null);	
					$task = $this->post;
					
					// Show form
					$this->setSetformContent(null, $task);
					$this->renderViewContent();
					$this->content['top'] = $this->View->content;
					
					// Show comments
					if (!empty($id)) { $this->showComments($id); }
				}
				break;
			case "fastedit":
				$task = $this->Tasks->getTask($this->get->id);
				if (!$task) { return false; }
				
				$property = $this->get->property;
				if ($property == "assigned") {
					$task[$property . "_type"] = $this->post->{$property . "_type"};
					$task[$property . "_id"] = $this->post->{$property . "_id"};
				}
				else {
					$task[$property] = $this->post->{$property};
				}
				$result = $this->Tasks->setTask($task);
				
				$this->redirect($this->get->redirection_url);
				break;
			case "setcomment":
				$comment = $this->Tasks->getComment($this->post->id);
				if (empty($this->post->id) || $this->user_level >= 900 || $this->user_id == $comment->user_id) { 
					$this->Tasks->setComment($this->post);
				}
				
				$this->redirect($this->post->redirection_url);
				break;
			case "getcommentform": 
				$form_html = $this->getTaskCommentsForm($this->get->task_id, $this->get->comment_id);
				echo $form_html;
				break;
			case "removecomment":				
				$comment = $this->Tasks->getComment($this->get->id);				
				if (!$comment) {
					$this->loadhelpers(["ErrorHandler"]);
					$this->content['error'] = $this->ErrorHandler->getHtml([['comment' => "Comment doesn't exists"]]);
					return false;
				}
			
				if ($this->user_level >= 900  || $this->user_id == $comment->user_id) { 
					$this->Tasks->markCommentAsRemoved($this->get->id);
				}
				
				$redirection_url = $this->site_url . "index.php/?component=tasks&action=show&id=" . $comment->task_id;
				$this->redirect($redirection_url);
				break;
			case "getfastcomments":
				if (isset($this->get->return_url)) { $this->current_url = $this->get->return_url; };
				
				$this->showComments($this->get->task_id);
				echo $this->content['bottom'];
				break;
			case "getlistfortie":
					$params = [
						'order'=>[
							['column'=>"creation_date", 'side'=>"DESC"]
						],
						'limit_qty' => 10
					];		
				$tasks = $this->Tasks->getTasks($params);
				break;
			case "tie":								
				$this->Tasks->tieTask($this->post->task_id, $this->post->tied_task_id, $this->post->depended_object);
				break;
			default:
				$this->redirect($this->site_url);
				break;
		}
	}
	
	
	private function showComments($task_id) {
		$comments_form = $this->getTaskCommentsForm($task_id);			
		
		$this->setTaskComments($task_id);
		$this->renderViewContent();
		$this->content['bottom'] = $this->View->content;
		$this->content['bottom'] .= $comments_form;		
	}
	
	public function setTaskComments($task_id) {
		$comments = $this->Tasks->getComments($task_id);
		$vars = [
			'user_id' => $this->user_id,
			'task_id' => $task_id,
			'comments' => $comments			
		];		
		$this->setView("components/tasks/views/comments.php", $vars);
		
		return true;
	}
	
	public function getTaskCommentsForm($task_id, $comment_id=null) {
		$comment = (empty($comment_id) ? $this->Tasks->getDefaultComment($task_id, $this->user_id) : $this->Tasks->getComment($comment_id));

		$this->setView("components/tasks/views/setcommentform.php");
		$this->setViewVars(['comment'=>$comment]);
		$this->renderViewContent();
		
		return $this->View->content;
	}
	
	public function setTaskContent($task_id) {
		$task = $this->Tasks->getTask($task_id);
		if (!$task) { return false; } 
		
		// Attach parents and childrens info
		$task['subtasks'] = $this->Tasks->getSubtasks($task['id']);
		if (!empty($task['parent_task_id'])) { $task['parent_task'] = $this->Tasks->getTask($task['parent_task_id']); }
		$task['tied_tasks'] = $this->Tasks->getTiedTasks($task['id']);
		
		$this->setView("components/tasks/views/task.php");
		$this->setViewVars(['task'=>(object)$task]);
		
		return true;
	}
	
	
	public function setTaskformContent($task_id=null, $task=null, $parent_task_id=null) {
		$this->loadModels(["Projects"]);
		
		// Get task	
		$task = (!empty($task) ? $task : $this->Tasks->getTask($task_id));
		if (!$task) { $task = $this->Tasks->getDefaultTask($this->user_id); }
		$task = (object)$task;
		
		// Attach parents and childrens info
		if (!empty($parent_task_id) && !isset($task->parent_task_id)) { $task->parent_task_id = $parent_task_id; }
		$task->subtasks = $this->Tasks->getSubtasks($task->id);
		
		
		$vars = [
			'users' => $this->Users->getUsers(),
			'groups' => $this->Users->getUserGroups(),
			'projects' => $this->Projects->getProjects(),
			'statuses' => $this->Projects->getStatuses($task->project_id),
			'priorities' => $this->Projects->getPriorities($task->project_id),
			'task' => $task	
		];
		
		$this->setView("components/tasks/views/settaskform.php", $vars);
		
		return true;
	}
	
	public function setListContent($project_id) {
		$params = [
			'order'=>[
				['column'=>"due_date", 'side'=>"DESC"],
				['column'=>"priority_weight", 'side'=>"DESC"],
			]
		];			
		$tasks = $this->Tasks->getTasks($project_id, $params);
		
		if (!empty($this->user_id)) {
			$tasks = $this->Tasks->attachCommentsQty($tasks, $this->user_id, $this->getUserLastVisitDate($this->name));
		}
		
		$this->loadModels(["Projects", "Settings"]);
		$vars = [
			'tasks' => $tasks,
			'statuses' => $this->Projects->getStatuses($project_id),
			'due_critical_period' => $this->Settings->getSetting("due_critical_period", "project", ['id'=>$project_id])
		];
		
		$this->setView("components/tasks/views/list.php", $vars);
		
		return true;
	}	

	public function setRules($data) {
		if (is_array($data)) { 
			$this->rules = $data;
			return true;
		}
		
		switch ($data) {
			case "list":
				$this->rules = [
					'project_id' => [
						'filter' => "int",
						'error_message' => "Incorrect project id"
					]
				];
				break;
			case "showsetform":
				$this->rules = [
					'id' => [
						'filter' => "int",
						'error_message' => "Incorrect task id"
					]
				];
				break;
			case "show":
				$this->rules = [
					'id' => [
						'filter' => "int",
						'error_message' => "Incorrect task id"
					],
				];
				break;
			case "setcomment":
				$this->rules = [
					'id' => [
						'filter' => "int",
						'error_message' => "Incorrect comment id",
						'canbeempty_fl' => "yes"
					],
					'task_id' => [
						'filter' => "int",
						'error_message' => "Incorrect task id"
					],
					'user_id' => [
						'filter' => "int",
						'error_message' => "Incorrect user id"
					],
					'text' => [
						'filter' => "validate_regexp",
						'regexp' => "longstring",
						'error_message' => "Text is too long (max - 4096)"
					],
					'creation_date' => [
						'filter' => "validate_regexp",
						'regexp' => "date",
						'error_message' => "Invalid creation date"
					],
					'modification_date' => [
						'filter' => "validate_regexp",
						'regexp' => "date",
						'error_message' => "Invalid modification date",
						'canbeempty_fl' => "yes"
					],
					'redirection_url' => [
						'filter' => "validate_url",
						'error_message' => "Invalid redirection url"
					]						
				];
				break;
			case "getcommentform":
				$this->rules = [
					'task_id' => [
						'filter' => "int",
						'error_message' => "Incorrect task id"
					],
					'comment_id' => [
						'filter' => "int",
						'error_message' => "Incorrect comment id"
					]
				];
				break;
			case "removecomment":
				$this->rules = [
					'id' => [
						'filter' => "int",
						'error_message' => "Incorrect comment id"
					],
				];
				break;
		}
	}
	
	private function getSetformRules() {
		$rules = [
			'id' => [
				'filter' => "int",
				'error_message' => "Invalid task",
				'canbeempty_fl' => "yes"
			],
			'parent_task_id' => [
				'filter' => "int",
				'error_message' => "Invalid parent task id",
				'canbeempty_fl' => "yes"
			],
			'project_id' => [
				'filter' => "int",
				'error_message' => "Invalid project"
			],
			'status_id' => [
				'filter' => "int",
				'error_message' => "Invalid status"
			],
			'priority_id' => [
				'filter' => "int",
				'error_message' => "Invalid priority"
			],
			'title' => [
				'filter' => "validate_regexp",
				'regexp' => "shortstring",
				'error_message' => "Title is too long (max - 255)"
			],
			'text' => [
				'filter' => "validate_regexp",
				'regexp' => "longstring",
				'error_message' => "Text is too long (max - 4096)"
			],
			'assigned_type' => [
				'filter' => "validate_regexp",
				'options' => ['regexp' => '/(GROUP|USER)/'],
				'error_message' => "Invalid type of assignee"
			],
			'assigned_id' => [
				'filter' => "int",
				'error_message' => "Invalid assignee"
			],
			'creation_date' => [
				'filter' => "validate_regexp",
				'regexp' => "date",
				'error_message' => "Invalid creation date"
			],
			'due_date' => [
				'filter' => "validate_regexp",
				'regexp' => "date",
				'error_message' => "Invalid due date",
				'canbeempty_fl' => "yes"
			],
			'closed_date' => [
				'filter' => "validate_regexp",
				'regexp' => "date",
				'error_message' => "Invalid closed date",
				'canbeempty_fl' => "yes"
			],			
		];
		
		return $rules;	
	}
	
	public function setNecessaryVars($action) {	
		switch ($action) {
			case "list":
				$this->necessary_vars = [
					'get' => ["project_id"]
				];
				break;
			case "show":
				$this->necessary_vars = [
					'get' => ["id"]
				];
				break;			
			case "set":
				$this->necessary_vars  = [
					'post' => [
						"id",
						"parent_task_id",
						"project_id",
						"status_id",
						"priority_id",
						"author_id",
						"assigned_type",
						"assigned_id",
						"creation_date",
						"due_date",
						"closed_date",
						"title",
						"text"
					]
				];
				break;
			case "setcomment":
				$this->necessary_vars = [
					'post' => [
						"id", 
						"task_id", 
						"user_id", 
						"text", 
						"creation_date", 
						"modification_date",
						"redirection_url"
					]
				];
				break;
			case "getcommentform":
				$this->necessary_vars = [
					'get' => [
						"task_id",
						"comment_id" 
					]
				];
				break;
			case "removecomment":
				$this->necessary_vars = [
					'get' => [
						"id"
					]
				];
				break;
		}
	}	
}
