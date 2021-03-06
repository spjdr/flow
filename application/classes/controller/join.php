<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Join extends Controller_Website {
  
  	public function before()
  	{
  		$this->auth_required = 'login';
  		parent::before();
  	}
  	
  	public function action_accept()
  	{
		$invitation = ORM::factory('invitation')->where('secret','=',Request::instance()->param('secret'))->find();
		
		if ($invitation->accepted > 0)
		{
			$this->session->set('message',Kohana::message('join','success').' '.html::chars($flow->title));
			
			Request::instance()->redirect($flow->uri);
		}
		elseif ($invitation->deleted > 0)
		{
			$this->session->set('error',Kohana::message('join','deleted').' '.html::chars($flow->title));
			
			Request::instance()->redirect('@'.$this->user->username);
		}
		elseif ($invitation->loaded())
		{
			$flow = ORM::factory('flow',$invitation->flow_id);
		
			$flow_user = ORM::factory('flow_user')->where('flow_id','=',$flow->id)->where('user_id','=',$this->user->id)->find();
			
			if (!$flow_user->loaded())
			{
				$flow_user->flow_id = $flow->id;
				$flow_user->user_id = $this->user->id;
				$flow_user->save();
			}
			
			$invitation->accepted = 1;
			$invitation->save();
			
			$this->session->delete('access');
			
			$this->session->set('message',Kohana::message('join','success').' '.html::chars($flow->title));
			
			Request::instance()->redirect($flow->uri);
		}
		else
		{
			$this->session->set('error',Kohana::message('join','failure'));
			Request::instance()->redirect();
		}
	}  	
}