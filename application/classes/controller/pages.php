<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Pages extends Controller_Website {

	public function before()
	{
		$this->secure_actions = array('edit'=>array('admin'));
		parent::before();
	}

	public function action_view()
	{
		$uri = url::title(Request::instance()->param('page'),'-',TRUE);
	
		$page = ORM::factory('page')->where('uri','=',$uri)->find(); 
		
		if (!$page->loaded())
		{
			if ($this->user->has('roles',ORM::factory('role',array('name'=>'admin'))))
			{
				Request::instance()->redirect('pages/'.$uri.'/edit');
			}
			else 
			{
				Request::instance()->redirect('noaccess');
			}
		}
	
		$this->template->title = $page->title;
		$content = $this->template->content = new View('pages/view');
		$content->body = Fust::markdown($page->body);
	}
	
	public function action_edit()
	{
		$uri = url::title(Request::instance()->param('page'),'-',TRUE);
	
		$page = ORM::factory('page')->where('uri','=',$uri)->find(); 
		
		$this->template->title = $page->title;
		$content = $this->template->content = new View('pages/edit');
		
		if ($page->loaded()) {
			$content->post = array_filter($page->as_array());
		} else {
			$content->post = array('title'=>ucfirst(str_replace('-',' ',$uri)));
		}
		
		if ($_POST)
		{
			$post = $_POST;
			$post['uri'] = $uri;
			$post['user_id'] = $this->user->id;
			
			$post = $page->validate($post);
		
			if ($post->check())
			{
				$page->values($post);
				$page->save();
			
				$this->session->set('message',Kohana::message('pages','edit_success'));
				
				Request::instance()->redirect('pages/'.$uri);
			}
			else
			{
				$this->session->set('error',Kohana::message('pages','edit_failed'));
				
				#Get errors for display in view
				//$content->errors = $post->errors('pages');
				//$content->post = $post;
				
				var_dump($post->errors('pages'));
			}
		}
	
	}
}