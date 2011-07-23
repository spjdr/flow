<?php defined('SYSPATH') or die('No direct script access.');

class Model_User extends Model_Auth_User {
 	
 	public $cache = array();
 	
 	// Relationships
	protected $_has_many = array(
		'pages'		  => array('model' => 'page'),
		'user_tokens' => array('model' => 'user_token'),
		'roles'       => array('model' => 'role', 'through' => 'roles_users'),
		'flows' 	  => array('model' => 'flow', 'through' => 'flows_users'),
		'streams'	  => array('model' => 'stream', 'through' => 'editors_streams','foreign_key'=>'user_id','far_key'=>'stream_id'),
		'invitations' => array('model' => 'invitation', 'foreign_key' => 'sender_id')
	);
 	
 	public function __get($key)
 	{
 		if ($key == 'name')
 		{
 			return parent::__get('fullname')!='' ? parent::__get('fullname') : parent::__get('username');
 		}
 		
 		if ($key == 'uri')
 		{
 			return '@'.parent::__get('username');
 		}
 	
 		if ($key == 'directory')
 		{
 			return 'files/users/'.parent::__get('id').'/';
 		}
 	
 		if ($key=='picture')
 		{
 			$path = 'files/users/'.parent::__get('id').'/'.parent::__get('username').'_profile_picture.jpg';
 			
 			if (file_exists(DOCROOT.$path))
 			{
 				return $path;
 			}
 			
 			return 'files/default/profile_picture.jpg';
 		}

		if ($key=='thumbnail')
		{
			$path = 'files/users/'.parent::__get('id').'/'.parent::__get('username').'_thumbnail_picture.jpg';
 			
 			if (file_exists(DOCROOT.$path))
 			{
 				return $path;
 			}
 			
 			return 'files/default/thumbnail_picture.jpg';
		}

		if ($key=='access') 
		{	
			if (isset($cache['access'])) {
				$access = $cache['access'];
			} elseif (Session::instance()->get('access')) {
				$access = Session::instance()->get('access');
				$cache['access'] = $access;
			} else {
				$access = $this->query_access();
				$cache['access'] = $access;
				Session::instance()->set('access',$access);
			}

			return $access;
		}

 		return parent::__get($key);
 	}
 
 	public function __contruct($id)
 	{
 		parent::__construct($id);
 	}
 
 	public function query_access()
 	{
 		$flows = ORM::factory('flow_user')->where('user_id','=',parent::__get('id'))->with('flow')->find_all();
 		$editing = ORM::factory('user',parent::__get('id'))->streams->find_all();
 		
 		$streams = array(); 
		foreach($editing as $edit)
		{
			$streams[$edit->flow_id] = array(
				'id'=>$edit->id,
				'uri'=>$edit->uri,
				'title'=>$edit->title
			);
		}
		
 		$access = array();
		foreach($flows as $flow)
		{
			$access[$flow->flow->id] = array (
				'id'=>$flow->flow->id,
				'uri'=>$flow->flow->uri,
				'title'=>$flow->flow->title,
				'master'=>$flow->master,
				'streams'=> isset($streams[$flow->flow->id]) ? $streams[$flow->flow->id] : array()
			);
		}
		
		return $access;
 	}
 
	public function validate_create(& $array) 
	{
		// Initialise the validation library and setup some rules		
		$array = Validate::factory($array)
						->rules('password', $this->_rules['password'])
						->rules('username', $this->_rules['username'])
						->rules('email', $this->_rules['email'])
						->rules('password_confirm', $this->_rules['password_confirm'])
						->filter('username', 'trim')
						->filter('email', 'trim')
						->filter('password', 'trim')
						->filter('password_confirm', 'trim');
 
		#Executes username callbacks defined in parent		
		foreach($this->_callbacks['username'] as $callback){
			$array->callback('username', array($this, $callback));
		}
 
                #Executes email callbacks defined in parent	
		foreach($this->_callbacks['email'] as $callback){
			$array->callback('email', array($this, $callback));
		}		
 
		return $array;
	}
	
	public function validate_edit(& $array)
	{
		// Initialise the validation library and setup some rules		
		$array = Validate::factory($array)
						->rules('email', $this->_rules['email'])
						->filter('fullname','trim')
						->filter('description','trim')
						->filter('email', 'trim');
 
 		#Executes email callbacks defined in parent	
		#foreach($this->_callbacks['email'] as $callback){
		#	$array->callback('email', array($this, $callback));
		#}		
 
		return $array;
	}
	
}