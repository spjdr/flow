<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Test extends Controller {

	public function action_index()
	{
		foreach (ORM::factory('event')->find_all() as $event){
			$event->save();
		}
		
		foreach(ORM::factory('flow')->find_all() as $flow) {
			$flow->save();
		}
		$this->request->response = 'hello, world!';
	}

} // End Welcome
