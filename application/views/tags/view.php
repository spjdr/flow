A total of <?php echo $events->count() ?> events has been tagged as <b><?php echo $tag->title ?>.</b><?php if ($access['master']): ?>	<?php echo html::anchor(Route::get('flow-item')->uri(array('flow'=>$flow->uri,'item'=>$tag->id,'action'=>'edit')),__('Edit tag'),array('class'=>'action')) ?>	<?php echo html::anchor(Route::get('flow-item')->uri(array('flow'=>$flow->uri,'item'=>$tag->id,'action'=>'delete')),__('Delete tag'),array('class'=>'action')) ?><? endif; ?><div class="flow-controls"><a class="control event-first flow-event-first" data-stream='{"id": "0"}'></a><a class="control event-prev flow-event-prev" data-stream='{"id": "0"}'></a><a class="control event-next flow-event-next" data-stream='{"id": "0"}'"></a><a class="control event-last flow-event-last" data-stream='{"id": "0"}'"></a></div><div class="clearfix"></div><div id="streams-scrollable" class="streams"><ul class="timeline clearfix" style="width: <?php echo ceil((time::date2timestamp($flow->end_date)-time::date2timestamp($flow->start_date))/(30*60))*144 ?>px">	<?php $time = time::date2timestamp($flow->start_date)+1; ?>	<?php while ($time < time::date2timestamp($flow->end_date)): ?>		<li class="day"><?php echo date('l j \o\f F Y',$time) ?></li>		<?php $time+= 24*3600; ?>	<? endwhile; ?></ul><ul class="timeline clearfix" style="width: <?php echo ceil((time::date2timestamp($flow->end_date)-time::date2timestamp($flow->start_date))/(30*60))*144 ?>px">	<li class="halfhour">&nbsp;</li>	<?php $time = 1; ?>	<?php while ($time*3600 < time::date2timestamp($flow->end_date) - time::date2timestamp($flow->start_date)): ?>		<li class="hour"><?php echo $time%24 ?></li>		<?php $time++; ?>	<? endwhile; ?></ul><?php $c = color::hex2RGB($tag->color); ?><ul class="stream clearfix" data-stream='{"id": "0"}' style="width: <?php echo ceil((time::date2timestamp($flow->end_date)-time::date2timestamp($flow->start_date))/(30*60))*144 ?>px; background-color: rgba(<?php echo $c['r'].','.$c['g'].','.$c['b'].',0.3' ?>)"><?php foreach($events as $event): ?>	<?php $pos = ceil(($event->timestamp-time::date2timestamp($flow->start_date))/(60*15))*72 ?>	<li class="event<?= $event->id ?>" style="left: <?php echo $pos ?>px; width: <?php echo ceil($event->duration/30)*144 ?>px">		<div style="background-color: <?php echo $event->stream->color ?>">			<?php echo html::anchor(Route::get('event')->uri(array('flow'=>$flow->uri,'stream'=>$event->stream->uri,'event'=>$event->id)),$event->title) ?>			<?php foreach($event->tags->find_all() as $tag): ?>				<a href="<?php echo url::site($flow->uri.'/tags/'.$tag->id) ?>" class="tooltip marker tag<?php echo $tag->id ?>" title="<?php echo $tag->title ?>"></a>			<?php endforeach; ?>		</div>	</li><?php endforeach; ?></ul><div class="clearfix" style="height: 20px"></div></div><div style="z-index: 1; position: relative; top: -21px; border-width: 0 1px; border-style: solid; border-color: red; height: 20px; overflow: hidden" class="clearfix"><?php $date_range = range(time::date2timestamp($flow->start_date),time::date2timestamp($flow->end_date)-1,24*3600); ?><?php foreach($date_range as $i=>$day): ?>	<div style="float: left; width: <?php echo number_format(round(100/count($date_range),4),4) ?>%">		<div style="height: 20px; border-width: 0 1px; border-style: solid; border-color: transparent #660000 transparent red; text-align: center"> <?php echo strftime('%A',$day) ?> </div>	</div><?php endforeach; ?></div><script type="text/javascript">$(function(){ 	$('a.tooltip').tipsy({live: true});});$(function(){	events = new Object();events[0] = new Array();		streams = $('#streams-scrollable').jScrollPane(		{			showArrows: false,			horizontalGutter: 0,			animateScroll: true		}	);	api = streams.data('jsp');		$('.stream').each(function(){		id = $(this).data("stream").id; 		events[id] = new Array();		i=0;		$('li',this).each(function(){			events[id][i] = $(this).position().left;			events[0].push(events[id][i]);			i++;		});	});		$('a.event-prev').click(function(){		match = bestmatch(api.getContentPositionX(),events[$(this).data('stream').id]);		api.scrollToX(match[0]);	});		$('a.event-next').click(function(){		match = bestmatch(api.getContentPositionX(),events[$(this).data('stream').id]);		api.scrollToX(match[1]);	});		$('a.event-first').click(function(){		match = bestmatch(api.getContentPositionX(),events[$(this).data('stream').id]);		api.scrollToX(match[2]);	});		$('a.event-last').click(function(){		match = bestmatch(api.getContentPositionX(),events[$(this).data('stream').id]);		api.scrollToX(match[3]);	});});function bestmatch(goal,theList){	var smallest = null;	var biggest = null;	var closeless = null;	var closegreat = null;	$.each(theList, function(){		if (smallest == null || this < smallest) {			smallest = this;		}		if (biggest == null || this > biggest) {			biggest = this;		}	  	if (this < goal && (closeless==null || Math.abs(this - goal) < Math.abs(closeless - goal))) {    		closeless = this;		}		if (this > goal && (closegreat==null || Math.abs(this - goal) < Math.abs(closegreat - goal))) {			closegreat = this;		}	});		if (closegreat == null) {		closegreat = biggest;	}	if (closeless == null) {		closeless = smallest;	}		return Array(closeless,closegreat,smallest,biggest);}</script><style><?php foreach($flow->tags->find_all() as $tag): ?>.tag<?php echo $tag->id ?> {	background-color: <?php echo $tag->color ?>}<?php endforeach; ?></style>