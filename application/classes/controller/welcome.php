<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Welcome extends Controller {

	public function action_view()
	{
		/*foreach (ORM::factory('event')->find_all() as $event){
			$event->save();
		}
		
	
		$this->request->response = 'hello, world!';*/
		
		foreach(ORM::factory('flow')->find_all() as $flow) {
			$flow->save();
		}
	}

} // End Welcome
