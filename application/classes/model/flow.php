<?php defined('SYSPATH') or die('No direct script access.');

class Model_Flow extends ORM {
 
 	protected $_has_many = array(
		'streams' 	=> array('model' => 'stream','foreign_key'=>'flow_id'),
		'tags'		=> array('model' => 'tag'),
		'users' 	=> array('model' => 'user', 'through' => 'flows_users'),
		'invitations' => array()
	);
	
	protected $_rules = array(
		'uri' => array(
			'not_empty'		=> NULL,
			'min_length' 	=> array(2),
			'max_length' 	=> array(255),
			'regex' 		=> array('/^[-\pL\pN_.]++$/uD')
		),
		'title' => array(
			'not_empty'  	=> NULL,
			'min_length' 	=> array(2),
			'max_length' 	=> array(50)
		),
		'description' => array(
			'not_empty'	=> NULL
		),
		'start_date' =>	array(
			'not_empty'		=> NULL,
			'regex' 		=> array('/[0-9]{1,2}\/[0-9]{1,2}\/[0-9]{4}/')
		),
		'end_date' =>	array(
			'not_empty' 	=> NULL,
			'regex' 	=> array('/[0-9]{1,2}\/[0-9]{1,2}\/[0-9]{4}/')
		),
		'created_timestamp' => array(
			'not_empty' => NULL
		)
	);
 
 	// Validation callbacks
	protected $_callbacks = array(
		'uri' => array('uri_available')
	);

	// Field labels
	protected $_labels = array(
		'uri'         => 'uri',
		'title'       => 'title',
		'description' => 'description'
	);
 
 	public function __get($key)
 	{
 		if ($key == 'logo')
 		{
 			return url::site('files/flows/'.parent::__get('id').'/logo.jpg');
 		}
 		
 		if ($key == 'link')
 		{
 			return url::site(parent::__get('uri'));
 		}
 		
 		if ($key == 'categorized_tags') 
 		{
 			$tags = array();
 			foreach($this->tags->order_by('category')->find_all() as $tag)
 			{
				$tags[$tag->category][] = $tag;
			}
			return $tags;
 		}
 		
 		if ($key == 'start_timestamp')
 		{
			return strtotime(parent::__get('start_date')); 
		}
 		
 		if ($key == 'end_timestamp')
 		{
			return strtotime(parent::__get('end_date'));
 		}
 	
 		return parent::__get($key);
 	}
 
	public function validate_create(& $array) 
	{
		// Initialise the validation library and setup some rules		
		$array = Validate::factory($array)
						->rules('uri', $this->_rules['uri'])
						->rules('title', $this->_rules['title'])
						->rules('description', $this->_rules['description'])
						->rules('start_date', $this->_rules['start_date'])
						->rules('end_date', $this->_rules['end_date'])
						->filter('uri','trim')
						->filter('title', 'trim')
						->filter('description', 'trim')
						->filter('start_date','trim')
						->filter('end_date','trim');
 
 		#Executes username callbacks defined in parent		
		foreach($this->_callbacks['uri'] as $callback){
			$array->callback('uri', array($this, $callback));
		}
 
		return $array;
	}
	
	public function validate_edit(& $array)
	{
		// Initialise the validation library and setup some rules		
		$array = Validate::factory($array)
						->rules('uri', $this->_rules['uri'])
						->rules('title', $this->_rules['title'])
						->rules('description', $this->_rules['description'])
						->rules('start_date', $this->_rules['start_date'])
						->rules('end_date', $this->_rules['end_date'])
						->filter('uri','trim')
						->filter('title', 'trim')
						->filter('description', 'trim')
						->filter('start_date','trim')
						->filter('end_date','trim');

  		#Executes username callbacks defined in parent		
		foreach($this->_callbacks['uri'] as $callback){
			$array->callback('uri', array($this, $callback));
		}
  
		return $array;
	}
	
	/**
	 * Does the reverse of unique_key_exists() by triggering error if email exists.
	 * Validation callback.
	 *
	 * @param   Validate  Validate object
	 * @param   string    field name
	 * @return  void
	 */
	public function uri_available(Validate $array, $field)
	{
		// Load the configuration for this type
		$blacklist = Kohana::config('routing')->get('blacklist');
		
		if (in_array($array[$field],$blacklist))
		{
			$array->error($field, 'uri_available', array($array[$field]));
		}
		elseif ($this->unique_key_exists($array[$field], 'uri'))
		{
			$array->error($field, 'uri_available', array($array[$field]));
		}
	}

	/**
	 * Tests if a unique key value exists in the database.
	 *
	 * @param   mixed    the value to test
	 * @param   string   field name
	 * @return  boolean
	 */
	public function unique_key_exists($value, $field)
	{
		return (bool) DB::select(array('COUNT("*")', 'total_count'))
			->from($this->_table_name)
			->where($field, '=', $value)
			->where($this->_primary_key, '!=', $this->pk())
			->execute($this->_db)
			->get('total_count');
	}
	
	public function unique_key($value)
	{
		return Validate::numeric($value) ? 'id' : 'uri';
	}
	
	public function save()
	{
		if (!parent::__isset('created_timestamp')) {
			parent::__set('created_timestamp',time());
		}
		
		if (parent::__get('secret')=='') {
			parent::__set('secret',uniqid());
		}
		
		parent::__set('updated_timestamp',time()); 
		
		parent::save();
	}
}