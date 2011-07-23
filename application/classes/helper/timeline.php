<?php defined('SYSPATH') or die('No direct script access.');

/**
 * formerror helper class.
 */

class Helper_Timeline {

	public $flow = false;
	public $streams = false;
	public $tags = false;
	public $tags_list = false;
	public $access = array();

	public function __construct()
	{
		
	}
	
	public function factory()
	{
		return new Helper_Timeline();	
	}
	
	public function build($flow)
	{
		$this->flow = $flow;
		$this->streams = $flow->streams->find_all();
		$this->tags = $this->flow->tags->find_all();
		
		foreach($this->tags as $tag)
		{
			$this->tags_list[$tag->id] = $tag->title;
		}
	}
	
	public function access($access)
	{
		$this->access = $access;
	}	

	public static function arrayfy($flow,$streams,$tags) {
	
		$out = array();
		$out['start_date'] = $flow->start_date;
		$out['end_date'] = $flow->end_date;
		$out['range'] = range(time::date2timestamp($flow->start_date),time::date2timestamp($flow->end_date),24*3600);
		$out['streams'] = array();
		$out['width'] = ceil((time::date2timestamp($flow->end_date)-time::date2timestamp($flow->start_date)+24*3600)/(15*60));
		$out['uri'] = $flow->uri;
		
		foreach($streams as $stream)
		{
			$RGB = color::hex2RGB($stream->color);
		
			$out['streams'][$stream->id] = array(
				'title' => $stream->title,
				'color' => $stream->color,
				'alpha' => 'rgba('.$RGB['r'].','.$RGB['g'].','.$RGB['b'].',0.3'.')',
				'uri' => $flow->uri.'/'.$stream->uri,
				'events'=>array()
			);
			
			$max_ongoing = 1;
			$ongoing = array();
			foreach($stream->events->order_by('timestamp','ASC')->find_all() as $event)
			{
				$end_timestamp = $event->timestamp + 60*$event->duration;
				$timestamp = $event->timestamp;
				
				$substream = false;
				foreach($ongoing as $key=>$time)
				{
					if ($time <= $timestamp || !$time)
					{
						if (!$substream)
						{
							$ongoing[$key] = $end_timestamp;
							$substream = $key+1;
						}
						else
						{
							$ongoing[$key] = false;
						}
					}
				}
				
				if (!$substream)
				{
					$ongoing[] = $end_timestamp;
					$substream = count($ongoing);
				}

				$max_ongoing = count($ongoing) > $max_ongoing ? count($ongoing) : $max_ongoing;
				
				$out['streams'][$stream->id]['events'][$event->id] = array(
					'title'=> $event->title,
					'uri' => $flow->uri.'/'.$stream->uri.'/'.$event->id,
					'timestamp' => $event->timestamp,
					'duration' => $event->duration,
					'end_timestamp' => $event->end_timestamp,
					'position' => ceil(($event->timestamp - time::date2timestamp($flow->start_date))/(60*15)),
					'width' => ceil($event->duration/15),
					'body' => $event->tag('html',$out['uri'],$tags).'<p>'.text::limit_chars($event->description,ceil($event->duration/(3))).'</p>',
					'ongoing' => $substream
				);
			}
			
			$out['streams'][$stream->id]['ongoing'] = $max_ongoing;
		}

		return $out;
	}
 
 	public function display($scale)
 	{
 		$flow = Helper_Timeline::arrayfy($this->flow,$this->streams,$this->tags);

 		$timeline = new View('timeline/flow');
 		$timeline->scale = $scale;
 		$timeline->flow = $flow;
 		$timeline->tags = $this->tags;
 		$timeline->access = $this->access;
 		$timeline->width = $flow['width']*$scale/2;
 		
 		return $timeline->render();
 	}
}