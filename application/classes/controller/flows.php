<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Flows extends Controller_Website {
  
  	public function before()
  	{
  		$this->auth_required = 'login';
  		$this->restricted_actions = array('edit'=>array('master'),'delete'=>array('master'));
  		parent::before();
  	}
  
	/**
	 * Index
	 */
	public function action_index()
	{
		$this->template->content = new View('flows/index');
		$this->template->content->flows = $this->user->flows->find_all();
	}
	
	public function action_view()
	{
		switch(Request::instance()->param('format'))
		{
			default:
			case 'html':
	
				$this->template->title = $this->flow->title;
				$this->template->scripts = array(
					'assets/qtip2/jquery.qtip.pack.js',
					'assets/tipsy/javascripts/jquery.tipsy.js',
					'assets/scrollpane/jquery.mousewheel.js',
					'assets/scrollpane/jquery.jscrollpane.min.js',
					'assets/drag/jquery.event.drag-2.0.min.js'
				);
				$this->template->styles = array(
					'common'=>
						array(
							'assets/qtip2/jquery.qtip.min.css' => 'screen, projection',
							'assets/tipsy/stylesheets/tipsy.css'=> 'screen, projection',
							'assets/scrollpane/jquery.jscrollpane.css' => 'screen, projection'
						)
				);
				
				$content = $this->template->content = new View('flows/view');
				$content->flow = $this->flow; 
				$content->access = $this->user->access[$this->flow->id]; 
				$content->flow_users = ORM::factory('flow_user')->where('flow_id','=',$this->flow->id)->with('user')->order_by('master')->find_all();
				
				$timeline = new Helper_Timeline();
				$timeline->build($this->flow);
				$timeline->access = $this->user->access[$this->flow->id];
				$content->timeline = $timeline->display(128);
				
				$content->tags = $timeline->tags;
			
			break;
			case 'xml':
				$content = new View('export/xml');
				$content->flow = $this->flow;
				$content->streams = $this->flow->streams->find_all();
				$content->tags = $this->flow->tags->find_all();
				header("Content-Type:text/xml");  
				header('Content-Disposition: attachment; filename="'.$this->flow->uri.'.xml"');
				$this->auto_render = false;
				echo $content->render(); 
				break;
			
			break;
			case 'json':
				$flow = $this->flow; 
				$tags = $this->flow->tags->find_all();
				$streams = $this->flow->streams->find_all();
				
				$export['title'] = $flow->title;
				$export['uri'] = $flow->uri;
				$export['updated'] = strftime('%FT%T%z',$flow->updated_timestamp);
				$export['time']['start'] = strftime('%FT00:00:00%z',$flow->start_timestamp);
				$export['time']['end'] = strftime('%FT00:00:00%z',$flow->end_timestamp);
				$export['description'] = $flow->description;
				$export['tags'] = array();
				foreach ($tags as $tag) {
					$export['tags'][] = $tag->title;
				}
				$export['streams'] = array();
				foreach ($streams as $stream) {
					$s = array();
					$s['title'] = $stream->title;
					$s['id'] = $stream->id;
					$s['color'] = $stream->color;
					$s['description'] = $stream->description;
					foreach ($stream->events->order_by('timestamp','ASC')->find_all() as $event) {
						$e = array();
						$e['title'] = $event->title;
						$e['id'] = $event->id;
						$e['subtitle'] = '';
						$e['time']['start'] = strftime('%FT%T%z',$event->timestamp);
						$e['time']['end'] = strftime('%FT%T%z',$event->end_timestamp);
						$e['description'] = $event->description;
						$e['location'] = array();
						$e['location']['description'] = $event->location;
						$e['location']['lattitude'] = 'N56 10.352';
						$e['location']['longitude'] = 'E010 11.825';
						$e['organiser']['name'] = $flow->title;
						$e['organiser']['phone'] = '+4588888888';
						$e['organiser']['email'] = 'flow@spjdr.dk';
						$e['organiser']['web'] = 'http://www.spjdr.dk/flow';
						$e['photos'] = array();
						if ($event->photo != '') {
							$event['photos'][] = $event->photo;
						}
						$e['tags'] = $event->tag('php',$flow->uri,$tags);
						$s['events'][] = $e;
					}
					$export['streams'][] = $s;
				}
				header("Content-Type:text/json");  
				header('Content-Disposition: attachment; filename="'.$this->flow->uri.'.json"');				
				$this->auto_render = false;
				echo json_encode($export);				
			break;
		}
	}
	
	public function action_new()
	{
		#Load the view
		$this->template->title = __('Create new flow');
		$this->template->scripts = array(
			'assets/datepicker/jquery.datePicker.js',
			'assets/datepicker/date.js'
		);
		$this->template->styles = array('common'=>array('assets/datepicker/datePicker.css'=> 'screen, projection'));
		$content = $this->template->content = View::factory('flows/new');
 
		#If there is a post and $_POST is not empty
		if ($_POST)
		{
			#Instantiate a new flow
			$flow = ORM::factory('flow');	
			
			#Load the validation rules, filters etc...
			$post = $_POST;
			$post = $flow->validate_create($post);			
 
			#If the post data validates using the rules setup in the flow model
			if ($post->check())
			{
				$this->session->delete('access');
	
				#Affects the sanitized vars to the flow object
				$flow->values($post);
 
				#create the flow
				$flow->save();
				
				#Add the user to the flow
				$this->user->add('flows',$flow,array('master'=>1,'created_timestamp'=>time()));
				
 				#Add tags
 				$tags = isset($_POST['tags']) ? array_unique($_POST['tags']) : array();
 				foreach($tags as $title)
 				{
 					$tag = ORM::factory('tag');
 					$insert = array('flow_id'=>$flow->id,'title'=>$title);
 					$insert = $tag->validate_create($insert);
 					if ($insert->check())
 					{
 						$tag->values($insert);
 						$tag->save();
 					}
 				}
 				
 				$this->session->set('message',Kohana::message('flows','new_success'));
 
				#redirect to the flow
				Request::instance()->redirect($flow->uri);
			}
			else
			{
				$this->session->set('error',Kohana::message('flows','new_failed'));
				
				#Get errors for display in view
				$content->errors = $post->errors('flows');
				$content->post = $post;
				$content->tags = isset($_POST['tags']) ? $_POST['tags'] : array();
			}
		}
	}
	
	public function action_edit()
	{
		$flow = ORM::factory('flow')->where('uri','=',Request::instance()->param('flow'))->find();
	
		$this->template->breadcrumbs = array(html::anchor($this->flow->uri,$this->flow->title));
		$this->template->title = __('Options');
		$this->template->scripts = array(url::site('assets/datepicker/jquery.datePicker.js'),url::site('assets/datepicker/date.js'));
		$this->template->styles = array('common'=>array('assets/datepicker/datePicker.css'=> 'screen, projection'));
		$content = $this->template->content = new View('flows/edit');
		$content->flow = $flow;
	
		#If there is a post and $_POST is not empty
		if ($_POST)
		{
			#Load the validation rules, filters etc...
			$post = $_POST;
			$post = $flow->validate_edit($_POST);			
 
			#If the post data validates using the rules setup in the flow model
			if ($post->check())
			{
				#Affects the sanitized vars to the flow object
				$flow->values($post);
 
				#create the flow
				$flow->save();
				
				$this->session->set('message',Kohana::message('flows','edit_success'));
 
				#redirect to the flow
				Request::instance()->redirect($flow->link);
			}
			else
			{
				$this->session->set('error',Kohana::message('flows','edit_failed'));
				
				#Get errors for display in view
				$content->errors = $post->errors('flows');
				$content->post = $post;
			}
		}
	}
	
	public function action_export($format)
	{
		$this->template->breadcrumbs = array(html::anchor($this->flow->uri,$this->flow->title));
		$this->template->title = __('Export flow');
		$content = $this->template->content = new View('flows/export');
		$content->flow = $this->flow;
		$content->formats = array('xml');				
	}
	
	public function action_delete()
	{	
		$this->template->breadcrumbs = array(html::anchor($this->flow->uri,$this->flow->title));
		if ($this->flow->users->count_all() > 1) 
		{
			$this->template->title = __('You cannot delete the flow');
			$content = $this->template->content = new View('flows/nondelete');
			$content->flow = $this->flow;
		}
		else 
		{
			$this->template->title = __('Delete flow');
			$content = $this->template->content = new View('flows/delete');
			$content->flow = $this->flow;
		
			if ($_POST)
			{	
				if (!isset($_POST['delete']))
				{
					Request::instance()->redirect($this->flow->uri);
				}
				
				// Delete flow
				$this->flow->delete();
				
				// Remove cached access panel
				$this->session->delete('access');	
				
				// Message
				$this->session->set('message',Kohana::message('flows','delete_success'));
 
				#redirect to index page
				Request::instance()->redirect('index');
			}
		}
	}
}