<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Welcome extends Website_Controller {

	public function before()
	{
		parent::
	}

	public function action_index()
	{
		$this->request->response = 'hello, world!';
	}

} // End Welcome
