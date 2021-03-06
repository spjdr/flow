<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Events extends Controller_Website {

  	public function before()
  	{
  		$this->auth_required = 'login';
  		$this->restricted_actions = array('new'=>array('editor','master'));
  		parent::before();
  		
  		$this->template->breadcrumbs = array();
		$this->template->breadcrumbs[] = html::anchor($this->flow->uri,$this->flow->title);
		$this->template->breadcrumbs[] = html::anchor($this->flow->uri.'/'.$this->stream->uri,$this->stream->title);
  	}
  	
  	public function action_index()
  	{
		$this->template->title = __('Events');
		$content = $this->template->content = new View('events/index');
		$content->flow = $this->flow;
		$content->stream = $this->stream;
		$content->events = $this->stream->events->find_all();
  	}
  	
  	public function action_view()
  	{
  		$event = $this->stream->events->where('id','=',Request::instance()->param('event'))->find();
		
		$content = $this->template->content = new View('events/view');
		
		$this->template->title = __($event->title);
		$content->flow = $this->flow;
		$content->stream = $this->stream;
		$content->event = $event;
		$content->tags = $event->tags->find_all();
  	}
  	
  	public function action_new()
	{
		$this->template->title = __('New event');
		$this->template->scripts = array(url::site('assets/datepicker/jquery.datePicker.js'),url::site('assets/datepicker/date.js'));
		$this->template->styles = array('common'=>array('assets/datepicker/datePicker.css'=> 'screen, projection'));
		$content = $this->template->content = new View('events/new');
		$content->flow = $this->flow;
		$content->stream = $this->stream;
		$content->flow_tags = $this->flow->tags->find_all();	
		$content->durations = Kohana::config('event')->get('durations');

		if ($_POST)
		{	
			$event = ORM::factory('event');
			$tags = isset($_POST['tags']) ? $_POST['tags'] : array();
			
			$post = $_POST;
			$post['stream_id'] = $this->stream->id;
			
			$a = time::date_parse($post['date']);
			$post['timestamp'] = mktime($post['hours'], $post['minutes'], 0, $a['month'], $a['day'], $a['year']);
			
			$post = $event->validate_create($post);
			
			if ($post->check())
			{
				$event->values($post);
				$event->save();
				
				foreach($tags as $id=>$val)
				{
					$event->add('tags',ORM::factory('tag',$id));
				}
				
				$this->flow->save();
				
				#add success message
				$this->session->set('message',Kohana::message('events','new_success'));
 
				#redirect to the new stream
				Request::instance()->redirect(Route::get('event')->uri(array('flow'=>$this->flow->uri,'stream'=>$this->stream->uri,'event'=>$event->id)));
				
			}
			else
			{
				#general fail message
				$this->session->set('error',Kohana::message('events','new_failed'));

				#Get errors for display in view
				$content->errors = $post->errors('events');
				$content->post = array_merge($post->as_array(),array('tags'=>$tags));
			}
		}
	}
	
	public function action_edit()
	{
		$event = $this->stream->events->where('id','=',Request::instance()->param('event'))->find();

		$this->template->breadcrumbs[] = html::anchor($this->flow->uri.'/'.$this->stream->uri.'/'.$event->id,$event->title);
		$this->template->title = __('Edit event');
		$this->template->scripts = array(url::site('assets/datepicker/jquery.datePicker.js'),url::site('assets/datepicker/date.js'));
		$this->template->styles = array('common'=>array('assets/datepicker/datePicker.css'=> 'screen, projection'));
		$content = $this->template->content = new View('events/edit');
		$content->flow = $this->flow;
		$content->stream = $this->stream;
		$content->event = $event;
		$content->durations = $event->durations;
		$content->flow_categorized_tags = $this->flow->categorized_tags;

		$event_tags = array();
		foreach($event->tags->find_all() as $tag)
		{
			$event_tags[$tag->id] = $tag->title;
		}

		$content->event_tags = $event_tags;
		
		if ($_POST)
		{	
			$post = $_POST;
			$tags = isset($_POST['tags']) ? $_POST['tags'] : array();
			$categories = isset($_POST['category']) ? array_filter($_POST['category']) : array();
			
			foreach($categories as $tag)
			{
				$tags[$tag] = TRUE;
			}
			
			$a = time::date_parse($post['date']);
			$post['timestamp'] = mktime($post['hours'], $post['minutes'], 0, $a['month'], $a['day'], $a['year']);
			
			$post = $event->validate_edit($post);
			
			if ($post->check())
			{
				$event->values($post);
				$event->save();
				
				$event->remove_all_tags();
				
				foreach($tags as $id=>$val)
				{
					$event->add('tags',ORM::factory('tag',$id));
				}		
				
				$event->save();
				
				$this->flow->save();
				
				$this->session->set('message',Kohana::message('events','edit_success'));
	 
				#redirect to the stream
				Request::instance()->redirect(Route::get('event')->uri(array('flow'=>$this->flow->uri,'stream'=>$this->stream->uri,'event'=>$event->id)));
			}
			else
			{
				$this->session->set('error',Kohana::message('events','edit_failed'));
				
				#Get errors for display in view
				$content->errors = $post->errors('event');
				$content->post = array_merge($post->as_array(),array('tags'=>$tags));
			}
		}
	}
	
	public function action_delete()
	{
		$event = $this->stream->events->where('id','=',Request::instance()->param('event'))->find();
		
		$this->template->breadcrumbs[] = html::anchor($this->flow->uri.'/'.$this->stream->uri.'/'.$event->id,$event->title);
		$this->template->title = __('Delete event');
		$content = $this->template->content = new View('events/delete');
		$content->flow = $this->flow;
		$content->stream = $this->stream;
		$content->event = $event;
		
		if ($_POST)
		{	
			$event->delete();
			
			$this->flow->save();
			
			$this->session->set('message',Kohana::message('events','delete_success'));
 
			#redirect to the flow
			Request::instance()->redirect($this->flow->uri.'/'.$this->stream->uri);
		}
	}
}