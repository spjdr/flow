<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Website extends Controller_Template
{
	public $template = 'layouts/default';
	public $auth_required = FALSE;
	public $user = FALSE;
	public $flow = FALSE;
	public $stream = FALSE;
	
	// Controls access for separate actions
	// 'adminpanel' => 'admin' will only allow users with the role admin to access action_adminpanel
	// 'moderatorpanel' => array('login', 'moderator') will only allow users with the roles login and moderator to access action_moderatorpanel
	public $secure_actions = FALSE;
	public $restricted_actions = FALSE;
 
	/**
	 * Initialize properties before running the controller methods (actions),
	 * so they are available to our action.
	 */
	
	public function before()
	{	
		// Run anything that need ot run before this.
		parent::before();
		
		I18n::lang('da-da');
		
		// Open session
		$this->session= Session::instance();
 
		#Check user auth and role
		$action_name = Request::instance()->action;
		if (($this->auth_required !== FALSE && (Auth::instance()->logged_in($this->auth_required) === FALSE))
				|| (is_array($this->secure_actions) && (array_key_exists($action_name, $this->secure_actions)) && 
				Auth::instance()->logged_in($this->secure_actions[$action_name]) === FALSE))
		{
			if (Auth::instance()->logged_in())
			{
				Request::instance()->redirect('noaccess');
			}
			else
			{
				if ($this->secret_access())
				{
					Request::instance()->redirect('login');
				}
			}
		}

		$this->user = ORM::factory('user');
		
		if (Auth::instance()->logged_in())
		{
			$this->user = $this->session->get(Kohana::config('auth.session_key'));
			$this->template->flows = $this->user->access;
		}
		
		if (Request::instance()->param('flow')) 
		{			
			if (!$this->flow)
			{
				$this->flow = $this->template->flow = ORM::factory('flow')->where('uri','=',Request::instance()->param('flow'))->find();
			}
			
			if (!array_key_exists($this->flow->id,$this->user->access) && $this->flow->secret!=Request::instance()->param('secret'))
			{	
				$this->session->set('error','No flow access.');
				Request::instance()->redirect('noaccess');
			}
			
			if ($this->restricted_actions && isset($this->restricted_actions[$action_name]) 
				&& ($this->user->access[$this->flow->id]['master']!=1 && in_array('master',$this->restricted_actions[$action_name]))
			)
			{
				$this->session->set('error','No flow access.');
				Request::instance()->redirect('noaccess');
			}
			
			if (Request::instance()->param('stream'))
			{
				$this->stream = ORM::factory('stream')->where('uri','=',Request::instance()->param('stream'))->find();
				
				if ($this->restricted_actions && isset($this->restricted_actions[$action_name]) 
					&& (
						(in_array('editor',$this->restricted_actions[$action_name]) && !isset($this->user->access[$this->flow->id]['streams'][$this->stream->id]) )
						&&
						($this->user->access[$this->flow->id]['master']!=1 && in_array('master',$this->restricted_actions[$action_name]))
					)
				)
				{
					Request::instance()->redirect('noaccess');
				}
			}
		}
	
		if ($this->auto_render)
		{
		   // Initialize empty values
		   $this->template->title            = 'Flow';
		   $this->template->meta_keywords    = 'picnic,edge,rug,software,open source,program management';
		   $this->template->meta_description = 'Flow is a web platform for planning schedules and programs in development';
		   $this->template->meta_copywrite   = 'Kim Lind Pedersen';
		   $this->template->image_src 		 = '';
		   $this->template->header           = '';
		   $this->template->message 		 = $this->session->get('message',FALSE);
	       $this->template->error			 = $this->session->get('error',FALSE);
		   $this->template->content          = '';
		   $this->template->footer           = View::factory('layouts/footer')->render();
		   $this->template->styles           = array('common'=>array());
		   $this->template->scripts          = array();
		   $this->template->username		 = $this->user->username;
   		   $this->template->name			 = $this->user->name;
   		   
   		   $this->session->delete('message');
   		   $this->session->delete('error');
		}
	}
	
	public function secret_access()
	{
		if (Request::instance()->param('flow') && Request::instance()->param('action')=='view' && Request::instance()->param('secret'))
		{
			$this->flow = $this->template->flow = ORM::factory('flow')->where('uri','=',Request::instance()->param('flow'))->find();
			if ($this->flow->secret == Request::instance()->param('secret'))
			{
				$this->auto_render == false;
				return true;
			}
		}
	}

	public function action_noaccess()
	{
		$this->template->content = View::factory('auth/noaccess');
	}	

	/**
	 * Fill in default values for our properties before rendering the output.
	 */
	public function after()
	{
		if($this->auto_render)
		{
			// Define defaults
			$styles['common']  = array_merge(
				$this->template->styles['common'],
				array(
					'assets/styles/screen.css' => 'screen, projection',
					'assets/styles/print.css' => 'print'
				)
			);
			
			$styles['ie'] = array(
				'assets/styles/stylesheets/ie.css' => 'screen, projection',
			);
			
			$scripts = array(
				//'http://ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js',
				'assets/scripts/jquery.min.js',
				'assets/scripts/utility.js'
			);
 
			// Add defaults to template variables.
			$this->template->styles  = $styles;
			$this->template->scripts = array_reverse(array_merge($this->template->scripts, $scripts));
		 }
 
		// Run anything that needs to run after this.
		parent::after();
	}
}
