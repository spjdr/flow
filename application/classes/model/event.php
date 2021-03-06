<?php defined('SYSPATH') or die('No direct script access.');

class Model_Event extends ORM {
 
 	protected $_has_many = array(
		'tags' 		=> array('model' => 'tag', 'through' => 'events_tags'),
	);
	
	protected $_belongs_to = array(
		'stream'	=> array('model'=>'stream')
	);
	
	protected $_rules = array(
		'stream_id' => array(
			'not_empty' 	=> NULL,
			'regex' 		=> array('/[0-9]*/')
		),
		'title' => array(
			'not_empty' => NULL,
			'min_length' => array(1),
			'max_length' => array(255)
		),
		'description' => array(
			'not_empty' => NULL
		),
		'timestamp'	=> array(
			'not_empty' => NULL,
			'regex'		=> array('/[0-9]*/')
		),
		'duration' => array(
			'not_empty'	=> NULL,
			'regex'		=> array('/-?[0-9]{1,3}/')
		),
		'notes' => array(
		),
		'created_timestamp' => array(
			'not_empty' => NULL
		)
	);
	
	protected $_ignored_columns = array(
		'date', 'tags'
	);
 
 		// Validation callbacks
	/*protected $_callbacks = array(
		'timestamp' => array('time_slot_free')
	);*/

 
 	public function __get($key)
 	{
 		switch($key)
 		{
 			case 'durations':
 				return Kohana::config('event')->get('durations');
 				break;
 			default:
 				return parent::__get($key); 		
 		}
 	}
 
	public function validate_create(& $array) 
	{
		// Initialise the validation library and setup some rules		
		$array = Validate::factory($array)
						->rules('stream_id',$this->_rules['stream_id'])
						->rules('title', $this->_rules['title'])
						->rules('description', $this->_rules['description'])
						->rules('timestamp', $this->_rules['timestamp'])
						->rules('duration', $this->_rules['duration'])
						->rules('notes', $this->_rules['notes'])
						->filter('title', 'trim')
						->filter('description', 'trim')
						->filter('notes', 'trim');
 		
		return $array;
	}
	
	public function validate_edit(& $array)
	{	
		// Initialise the validation library and setup some rules		
		$array = Validate::factory($array)
						->rules('title', $this->_rules['title'])
						->rules('description', $this->_rules['description'])
						->rules('timestamp', $this->_rules['timestamp'])
						->rules('duration', $this->_rules['duration'])
						->rules('notes', $this->_rules['notes'])
						->filter('title', 'trim')
						->filter('description', 'trim')
						->filter('notes', 'trim');
 		
		return $array;
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
	
	public function time_slot_free(Validate $array, $field)
	{
		return (bool) DB::query('SELECT COUNT("*") as total_count FROM ' . $this->tablename .
			' WHERE stream_id = ' . $array['stream_id'] .
			' AND ( ( timestamp < ' . $array['timestamp'] . ' AND end_timestamp > ' .  $array['timestamp'] . ' ) ' .
			   ' OR ( timestamp < '. $array['timestamp'] + $array['duration'] . ' AND end_timestamp > ' .  $array['timestamp'] + $array['duration'] . ' ) ' .
			    ' )')
			->execute($this->_db)
			->get('total_count');
	}
	
	public function remove_all_tags()
	{
		return (bool) DB::delete('events_tags')
			->where('event_id', '=', $this->pk())
			->execute($this->_db);
	}
	
	public function save()
	{
		if (!parent::__isset('created_timestamp')) {
			parent::__set('created_timestamp',time());
		}
		
		parent::__set('end_timestamp',parent::__get('timestamp')+parent::__get('duration')*60);
		
		$tags = array();
		foreach(ORM::factory('event',parent::__get('id'))->tags->find_all() as $tag) {
			$tags[] = $tag->id;
		}
		parent::__set('cache',implode(',',$tags));
		
		parent::save();
	}
	
	public function tag($format,$uri,$tags)
	{
		$cache = array_filter(explode(',',parent::__get('cache')));
		
		$output = '';
		switch($format)
		{
			default:
			case 'html':
				foreach($tags as $tag) {
					if (in_array($tag->id,$cache)) {
						$output .= '<a href="'.$uri.' ?>/tags/'.$tag->id.'" class="tooltip marker tag'.$tag->id.' ?>" title="'.$tag->title.'"></a> ';
					}
				}
			break;
			case 'php':
				$output = array();
				foreach($tags as $tag) {
					if (in_array($tag->id,$cache)) {
						$output[] = $tag->title;
					}
				}
			break;
			case 'xml':
				foreach($tags as $tag) {
					if (in_array($tag->id,$cache)) {
						$output .= '<tag>'.$tag->title.'</tag>';
					}
				}
			break;
		}
				
		return $output;
	}
}
