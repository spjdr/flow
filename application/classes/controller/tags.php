<?php defined('SYSPATH') or die('No direct script access.');class Controller_Tags extends Controller_Website {    	public function before()  	{  		$this->auth_required = 'login';  		parent::before();  	}  	  	public function action_index()  	{  		$flow = ORM::factory('flow')->where('uri','=',Request::instance()->param('uri'))->find();					$this->template->title = __('Tagindex');		$content = $this->template->content = new View('tags/index');		$content->flow = $flow;		$content->tags = $flow->tags->find_all();  	}  	  	public function action_add()	{		$flow = ORM::factory('flow')->where('uri','=',Request::instance()->param('uri'))->find();					$this->template->title = __('Add tags');		$content = $this->template->content = new View('tags/add');		$content->flow = $flow;				if ($_POST)		{				$tags = isset($_POST['tags']) ? array_unique($_POST['tags']) : array();						echo Kohana::debug($tags);						foreach($tags as $title)			{				$tag = ORM::factory('tag');				$post = array('flow_id'=>$flow->id,'title'=>$title);				$post = $tag->validate_create($post);								if ($post->check())				{					$tag->values($post);					$tag->save();				}			}						$this->session->set('message',Kohana::message('tags','add_success')); 			#redirect to the flow			Request::instance()->redirect($flow->uri.'/tags');		}	}		public function action_rename()	{		$flow = ORM::factory('flow')->where('uri','=',Request::instance()->param('uri'))->find();		$tag = $flow->tags->find(Request::instance()->param('id'));				$this->template->title = __('Rename tag');		$content = $this->template->content = new View('tags/rename');		$content->flow = $flow;		$content->tag = $tag;				if ($_POST)		{				$post = $_POST;			$post['flow_id'] = $tag->flow_id;			$post = $tag->validate_edit($post);						if ($post->check())			{				$tag->title = $post['title'];				$tag->save();															$this->session->set('message',Kohana::message('tags','edit_success'));	 				#redirect to the flow				Request::instance()->redirect($flow->uri.'/tags');			}			else			{				$this->session->set('error',Kohana::message('tags','edit_failed'));								#Get errors for display in view				$content->errors = $post->errors('flows');				$content->post = $post;			}		}	}		public function action_delete()	{		$flow = ORM::factory('flow')->where('uri','=',Request::instance()->param('uri'))->find();		$tag = $flow->tags->find(Request::instance()->param('id'));				$this->template->title = __('Delete tag');		$content = $this->template->content = new View('tags/delete');		$content->flow = $flow;		$content->tag = $tag;				if ($_POST)		{				$tag->delete();						$this->session->set('message',Kohana::message('tags','delete_success')); 			#redirect to the flow			Request::instance()->redirect($flow->uri.'/tags');		}	}}