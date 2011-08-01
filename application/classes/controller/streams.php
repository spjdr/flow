<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Streams extends Controller_Website {

  	public function before()
  	{
  		$this->auth_required = 'login';
  		$this->restricted_actions = array('new'=>array('master'),'edit'=>array('master','editor'),'delete'=>array('master'));
  		parent::before();
  		
  		$this->template->breadcrumbs = array(html::anchor($this->flow->uri,$this->flow->title));
  	}
  	
  	public function action_index()
  	{
		$this->template->title = __('Streams');
		$content = $this->template->content = new View('streams/index');
		$content->flow = $this->flow;
		$content->streams = $this->flow->streams->find_all();
  	}
  	
  	public function action_view()
  	{
		$this->template->title = $this->stream->title;
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
		
		$content = $this->template->content = new View('streams/view');
	
		$content->flow = $this->flow;
		$content->stream = $this->stream;
		$content->events = $this->stream->events->order_by('timestamp','ASC')->find_all(); //ORM::factory('event')->where('stream_id','=',$stream->id)->find_all();
		$content->access = $this->user->access[$this->flow->id];
  	}
  	
  	public function action_new()
	{
		//$flow = ORM::factory('flow')->where('uri','=',Request::instance()->param('flow'))->find();
		
		$this->template->title = __('New stream');
		$this->template->scripts = array('assets/colorpicker/syronex-colorpicker.js');
		$this->template->styles = array('common'=>array('assets/colorpicker/syronex-colorpicker.css'=> 'screen, projection'));

		$content = $this->template->content = new View('streams/new');
		$content->flow = $this->flow;

		if ($_POST)
		{	
			$stream = ORM::factory('stream');
			
			$post = $_POST;
			$post['flow_id'] = $this->flow->id;
			$post = $stream->validate_create($post);
			
			if ($post->check())
			{
				$stream->values($post);
				$stream->save();
				
				$this->flow->save();
				
				#add success message
				$this->session->set('message',Kohana::message('streams','new_success'));
 
				#redirect to the new stream
				Request::instance()->redirect($flow->uri.'/'.$stream->uri);
			}
			else
			{
				#general fail message
				$this->session->set('error',Kohana::message('streams','new_failed'));
				
				#Get errors for display in view
				$content->errors = $post->errors('streams');
				$content->post = $post;
			}
		}
	}
	
	public function action_edit()
	{
		$this->template->breadcrumbs[] = html::anchor($this->flow->uri.'/'.$this->stream->uri,$this->stream->title);
		$this->template->title = __('Edit stream');
		$this->template->scripts = array('assets/colorpicker/syronex-colorpicker.js');
		$this->template->styles = array('common'=>array('assets/colorpicker/syronex-colorpicker.css'=> 'screen, projection'));

		$content = $this->template->content = new View('streams/edit');
		$content->flow = $this->flow;
					
		$content->post = $this->stream->as_array();
		
		if ($_POST)
		{	
			$post = $_POST;
			$post['flow_id'] = $this->stream->flow_id;
			$post = $this->stream->validate_edit($post);
			
			if ($post->check())
			{
				$this->stream->values($post);
				$this->stream->save();
		
				$this->flow->save();
			
				$this->session->set('message',Kohana::message('streams','edit_success'));
	 
				#redirect to the stream
				Request::instance()->redirect($this->flow->uri.'/'.$this->stream->uri);
			}
			else
			{
				$this->session->set('error',Kohana::message('streams','edit_failed'));
				
				#Get errors for display in view
				$content->errors = $post->errors('stream');
				$content->post = $post;
			}
		}
	}
	
	public function action_delete()
	{
		$this->template->breadcrumbs[] = html::anchor($this->flow->uri.'/'.$this->stream->uri,$this->stream->title);
		$this->template->title = __('Delete stream');
		$content = $this->template->content = new View('streams/delete');
		$content->flow = $this->flow;
		$content->stream = $this->stream;
		
		if ($_POST)
		{	
			$this->stream->delete();
			
			$this->flow->save();
			
			$this->session->set('message',Kohana::message('streams','delete_success'));
 
			#redirect to the flow
			Request::instance()->redirect($this->flow->uri);
		}
	}
}