<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Flowxml extends Controller_Website {
  
  	public function before()
  	{
  		parent::before();
  	}
  
	/**
	 * Index
	 */
	public function action_index()
	{
		$this->template->content = new View('flowxml');
		$this->auto_render = false;
		echo $content->render(); 
	}
	
}