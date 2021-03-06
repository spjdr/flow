<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Account extends Controller_Website {
  
  	public function before()
  	{
  		$this->secure_actions = array('view'=>array('login'),'info'=>array('login'),'picture'=>array('login'),'password'=>array('login'));
  		parent::before();
  	}
  
	/**
	 * Index
	 */
	public function action_index()
	{
		// Just redirect to login page
		$this->request->redirect('login');
	}
	
	public function action_view()
	{
		if (Auth::instance()->logged_in()==0)
		{
			$this->request->redirect('login');
		}
		
		$user = ORM::factory('user')->where('username','=',Request::instance()->param('username'))->find();
		
		$this->template->title = ucfirst($user->name);
		$this->template->content = View::factory('account/view')->bind('user',$user);
		
		if ($user_name = $this->request->param('user_name'))
		{
			$user = ORM::factory('user')->where('username','=',$user_name)->find();
 	 	}
 	 	
 	 	if ($user_id = $this->request->param('user_id'))
 	 	{
 	 		$user = ORM::factory('user',$user_id);
 	 	}
	}
	
	public function action_info()
	{
		#If user already signed-in
		if(!Auth::instance()->logged_in())
		{
			#redirect to the user account
			$this->request->redirect('login');		
		}

 		$form = array (
 			'fullname'=>$this->user->fullname,
 			'email'=>$this->user->email,
 			'description'=>$this->user->description
 		);
 		
 		$errors = array(
 			'fullname'=>'',
 			'email'=>'',
 			'description'=>''
 		);
 			
		#If there is a post and $_POST is not empty
		if ($_POST)
		{
			#Load the validation rules, filters etc...
			$post = $this->user->validate_edit($_POST);			
 
			#If the post data validates using the rules setup in the user model
			if ($post->check())
			{
				#Affects the sanitized vars to the user object
				$this->user->fullname = $_POST['fullname'];
				$this->user->email = $_POST['email'];
				$this->user->description = $_POST['description'];
 
				#save changes
				$this->user->save();
				
				// set flash message
				$this->session->set('message','Your profile information has been updated');
 
				#redirect to the user account
				Request::instance()->redirect(Request::instance()->uri());
			}
			else
			{
				$this->session->set('error','Something went wrong when updating your profile information. Please correct any mistakes and try again.');
				
				#Get errors for display in view
				$errors = $post->errors('account');
			}			
		}
		
		$this->template->title = __('Edit user info');
		$this->template->breadcrumbs = array(HTML::anchor($this->user->uri,ucfirst($this->user->name))); 
		$this->template->content = View::factory('account/info')->bind('user',$this->user)->bind('form',$form)->bind('errors',$errors);
	}
	
	public function action_password()
	{
		#If user already signed-in
		if(!Auth::instance()->logged_in())
		{
			#redirect to the user account
			$this->request->redirect('login');		
		}

 		$form = array (
 			'password'=>'',
 			'password_confirm'=>''
 		);
 		$errors = $form;
 			
		#If there is a post and $_POST is not empty
		if ($_POST)
		{
			#If the post data validates using the rules setup in the user model
			if ($this->user->change_password($_POST,TRUE))
			{
				Auth::instance()->logout();
				
				$login = array('username'=>$this->user->username,'password'=>$_POST['password']);
			
				$this->user->login($login);
				
				// set flash message
				$this->session->set('message','Your profile picture has been updated');
				
				#redirect to the user account
				Request::instance()->redirect(Request::instance()->uri());
			}
			else
			{
				$this->session->set('error',Kohana::message('account','password_failed'));
			
				#Get errors for display in view
				$errors = $_POST->errors('account');
			}			
		}
		
		$this->template->title = __('Change user password');
		$this->template->breadcrumbs = array(HTML::anchor($this->user->uri,ucfirst($this->user->name))); 
		$this->template->content = View::factory('account/password')->bind('user',$this->user)->bind('form',$form)->bind('errors',$errors);
	}
	
	public function action_picture()
	{
		if (Auth::instance()->logged_in()==0)
		{
			$this->request->redirect('login');
		}
		
		$form = array('file'=>'');
		$errors = $form;
		$user = $this->user;
		
		if ($_POST || $_FILES)
		{
			$post = Validate::factory($_FILES)
				->rules('file',
					array(
						'Upload::valid' => array(),
						'Upload::not_empty' => array(), 
						'Upload::type' =>array('Upload::type' => array('jpg','png','gif')), 
						'Upload::size' => array('1M')
					)
				);
						 	
			if ($post->check())
			{
				$filename = Upload::save($_FILES['file'],NULL,NULL,0777);
		
				if (!file_exists(DOCROOT.$user->directory))
 				{
					mkdir(DOCROOT.$user->directory,0777,TRUE);
				}
				
				$image = Image::factory($filename);
				
				if ($image->width>180)
				{
					$image->resize(200,250, Image::WIDTH);
				}
								
				$image->save(DOCROOT.$user->directory.$user->username.'_profile_picture.jpg');
					
				Image::factory($filename)
					->resize(62,100, Image::HEIGHT) //Golden ratio
					->crop(62,100,TRUE,TRUE)
					->save(DOCROOT.$user->directory.$user->username.'_thumbnail_picture.jpg');
				
				unlink($filename);
				
				$this->session->set('message',Kohana::message('account','profile_picture_success'));
				
				Request::instance()->redirect(Request::instance()->uri());
			}
			else
			{
				$this->session->set('error',Kohana::message('account','profile_picture_failed'));
			
				// repopulate form fields
				$form = arr::overwrite($form, $post->as_array());
				
				// show errors if any
				$errors  = arr::overwrite($errors,$post->errors('upload'));
			}	
		}		
		
		$view = View::factory('account/picture')
			->bind('user',$user)
			->bind('form',$form)
			->bind('errors',$errors);
		
		$this->template->title = __('Change profile picture');
		$this->template->breadcrumbs = array(HTML::anchor($this->user->uri,ucfirst($this->user->name))); 
		$this->template->content = $view;
	}
 
	/**
	 * Login
	 */
	public function action_login()
	{
		#If user already signed-in
		if(Auth::instance()->logged_in()!= 0){
			#redirect to the user account
			Request::instance()->redirect('@'.$this->user->username);		
		}
 
 		$this->template->title = __('Login');
		$content = $this->template->content = View::factory('account/login');	
 
		#If there is a post and $_POST is not empty
		if ($_POST)
		{
			#Instantiate a new user
			$user = ORM::factory('user');
 
			#Check Auth
			$status = $user->login($_POST);
 
			#If the post data validates using the rules setup in the user model
			if ($status)
			{	
				$this->session->set('message',Kohana::message('account','welcome'));
				
				#redirect to the user account
				Request::instance()->redirect('@'.$user->username);
			}else
			{
				#Get errors for display in view
				$content->errors = $_POST->errors('account');
			}
		}
	}

	/**
	 * Logout
	 */
	public function action_logout()
	{
		#Sign out the user
		Session::instance()->delete('access');
		Auth::instance()->logout();
		
		#redirect to the user account and then the signin page if logout worked as expected
		Request::instance()->redirect('login');		
	}
 
	/**
	 * Register
	 */
	function action_register()
	{	
		#If user already signed-in
		if(Auth::instance()->logged_in()!= 0)
		{
			#redirect to the user account
			$this->request->redirect('account/~'.$this->user->username);		
		}
 
		#Load the view
		$this->template->title = __('Register');
		$content = $this->template->content = View::factory('account/register');
 
		#If there is a post and $_POST is not empty
		if ($_POST)
		{
			#Instantiate a new user
			$user = ORM::factory('user');	
 
			#Load the validation rules, filters etc...
			$post = $user->validate_create($_POST);			
 
			#If the post data validates using the rules setup in the user model
			if ($post->check())
			{
				#Affects the sanitized vars to the user object
				$user->values($post);
 
				#create the account
				$user->save();
 
				#Add the login role to the user
				$login_role = new Model_Role(array('name' =>'login'));
				$user->add('roles',$login_role);
 
				#sign the user in
				Auth::instance()->login($post['email'], $post['password']);
 
 				$this->session->set('message',Kohana::message('account','registration_success'));
 
				#redirect to the user account
				Request::instance()->redirect('@'.$user->username);
			}
			else
			{
				$this->session->set('error',Kohana::message('account','registration_failed'));
				
				#Get errors for display in view
				$content->errors = $post->errors('account');
				$content->post = $post;
			}
		}
	}
}