<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Tags extends Controller_Website {
  
  	public function before()
  	{
  		$this->auth_required = 'login';
  		$this->restricted_actions = array('new'=>array('master'),'edit'=>array('master'),'delete'=>array('master'));

  		parent::before();
  		$this->template->breadcrumbs = array();
  		$this->template->breadcrumbs[] = html::anchor($this->flow->uri,$this->flow->title);
  	}
  	
  	public function action_index()
  	{		
		$this->template->title = __('Tags');
		$content = $this->template->content = new View('tags/index');
		$content->flow = $this->flow;
		$content->tags = $this->flow->tags->find_all();
		$content->access = $this->user->access[$this->flow->id];
  	}
  	
  	public function action_view()
  	{
	  	$tag = $this->flow->tags->find(Request::instance()->param('item'));
	  		
	  	$this->template->breadcrumbs[] = html::anchor($this->flow->uri.'/tags',__('Tags'));
		$this->template->title = ucfirst($tag->category).': '.ucfirst($tag->title);
		
		$this->template->scripts = array(
			'assets/tipsy/javascripts/jquery.tipsy.js',
			'assets/scrollpane/jquery.mousewheel.js',
			'assets/scrollpane/jquery.jscrollpane.min.js'
		);
		$this->template->styles = array(
			'common'=>
				array(
					'assets/tipsy/stylesheets/tipsy.css'=> 'screen, projection',
					'assets/scrollpane/jquery.jscrollpane.css' => 'screen, projection'
				)
		);
		
		$content = $this->template->content = new View('tags/view');
		$content->flow = $this->flow;
		$content->tag = $tag;
		$content->access = $this->user->access[$this->flow->id];
		$content->events = $tag->events->order_by('timestamp','ASC')->find_all();
	
		$streams = array();	
		foreach($this->flow->streams->find_all() as $stream)
		{
			$streams[$stream->id] = $stream;
		}
		$content->streams = $streams;
		
		$tags = $this->flow->tags->find_all();
		$taglist= array('tags'=>array(),'values'=>array());
		foreach($tags as $tag):
			$taglist['tokens'][] = '{{tag'.$tag->id.'}}';
			$taglist['values'][] = $tag->title;
		endforeach;
		
		$content->tags = $tags;
		$content->taglist = $taglist;
  	}
  	
  	public function action_new()
	{
		$this->template->title = __('Create new tag');
		$this->template->breadcrumbs[] = html::anchor($this->flow->uri.'/tags',__('Tags')); 
		$this->template->scripts = array('assets/colorpicker/syronex-colorpicker.js');
		$this->template->styles = array('common'=>array('assets/colorpicker/syronex-colorpicker.css'=> 'screen, projection'));

		$content = $this->template->content = new View('tags/new');
		$content->flow = $this->flow;
		
		if ($_POST)
		{	
			$post = $_POST;
			$post['flow_id'] = $this->flow->id;

			$tag = ORM::factory('tag');
			$post = $tag->validate_create($post);
				
			if ($post->check())
			{
				$tag->values($post);
				$tag->save();
			}
			
			$this->flow->save();
			
			$this->session->set('message',Kohana::message('tags','new_success'));
 
			#redirect to the flow
			Request::instance()->redirect($this->flow->uri.'/tags');
		}
	}
	
	public function action_edit()
	{
		$tag = $this->flow->tags->find(Request::instance()->param('item'));
		
		$this->template->title = __('Edit tag');
		$this->template->breadcrumbs[] = html::anchor($this->flow->uri.'/tags',__('Tags')); 
		$this->template->breadcrumbs[] = html::anchor($this->flow->uri.'/tags/'.$tag->id,ucfirst($tag->title));
		$this->template->scripts = array('assets/colorpicker/syronex-colorpicker.js');
		$this->template->styles = array('common'=>array('assets/colorpicker/syronex-colorpicker.css'=> 'screen, projection'));
		$content = $this->template->content = new View('tags/edit');
		$content->flow = $this->flow;
		$content->tag = $tag;
		
		if ($_POST)
		{	
			$post = $_POST;
			$post = $tag->validate_edit($post);
			
			if ($post->check())
			{
				$tag->values($post);
				$tag->save();
	
				$this->flow->save();
	
				$this->session->set('message',Kohana::message('tags','edit_success'));
	 
				#redirect to the flow
				Request::instance()->redirect($this->flow->uri.'/tags');
			}
			else
			{
				$this->session->set('error',Kohana::message('tags','edit_failed'));
				
				#Get errors for display in view
				$content->errors = $post->errors('flows');
				$content->post = $post;
			}
		}
	}
	
	public function action_delete()
	{
		$tag = $this->flow->tags->find(Request::instance()->param('item'));
		
		$this->template->title = __('Delete tag');
		$this->template->breadcrumbs[] = html::anchor($this->flow->uri.'/tags',__('Tags')); 
		$content = $this->template->content = new View('tags/delete');
		$content->flow = $this->flow;
		$content->tag = $tag;
		
		if ($_POST)
		{	
			$tag->delete();
		
			$this->flow->save();
		
			$this->session->set('message',Kohana::message('tags','delete_success'));
 
			#redirect to the flow
			Request::instance()->redirect($this->flow->uri.'/tags');
		}
	}
}