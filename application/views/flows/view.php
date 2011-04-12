<!--<div class="flow_logo">
	<img src="<?php echo $flow->logo ?>" />
</div>-->

<div id="flow-description" class="markdowned">
	<?php echo fust::markdown($flow->description) ?>
</div>

<div class="flow-controls">
<a class="control event-first flow-event-first" data-stream='{"id": "0"}'></a>
<a class="control event-prev flow-event-prev" data-stream='{"id": "0"}'></a>
<a class="control event-next flow-event-next" data-stream='{"id": "0"}'"></a>
<a class="control event-last flow-event-last" data-stream='{"id": "0"}'"></a>
</div>

<div class="overview">
<div class="streams_info">
<div class="clearfix">&nbsp;</div>
<div class="clearfix">&nbsp;</div>
<ul>
	<?php foreach($streams as $stream): ?>
		<li style="background-color: <?php echo $stream->color ?>">
			<div>
				<div class="stream-title">
				<?php echo html::anchor($flow->uri.'/'.$stream->uri,$stream->title) ?>
				</div>
				<div class="stream-meta">
				<?php echo $stream->events->count_all() ?> <?php echo __('events in total') ?>
				</div>
				<div class="stream-actions">
					<a class="control event-prev stream-event-prev" data-stream='{"id": "<?php echo $stream->id ?>"}'></a>
					<a class="control event-next stream-event-next" data-stream='{"id": "<?php echo $stream->id ?>"}'"></a>
					<br/>
					<?php if ($access['master'] || isset($access['streams'][$stream->id])): ?>
						<?php echo html::anchor($flow->uri.'/'.$stream->uri.'/new',__('Add event'),array('class'=>'new-event')) ?>
					<? endif; ?>
				</div>
			</div>
		</li>
	<?php endforeach; ?>
</ul>
<div class="new-stream clearfix" style="height: 20px;">
<?php if ($access['master']): ?>
	<?php echo html::anchor(Route::get('stream-new')->uri(array('flow'=>$flow->uri)),'<span class="add-one"></span>'.__('Add stream')) ?>
<?php endif; ?>
</div>
</div>

<div id="streams-scrollable" class="streams">
<ul class="timeline clearfix" style="width: <?php echo ceil((time::date2timestamp($flow->end_date)-time::date2timestamp($flow->start_date)+24*3600)/(30*60))*144 ?>px">
	<?php $time = time::date2timestamp($flow->start_date); ?>
	<?php while ($time < time::date2timestamp($flow->end_date) + 24*3600): ?>
		<li class="day"><?php echo  strftime('%A %e. %B - %Y',$time) ?></li>
		<?php $time+= 24*3600; ?>
	<? endwhile; ?>
</ul>
<ul class="timeline clearfix" style="width: <?php echo ceil((time::date2timestamp($flow->end_date)-time::date2timestamp($flow->start_date)+24*3600)/(30*60))*144 ?>px">
	<li class="halfhour">&nbsp;</li>
	<?php $time = 1; ?>
	<?php while ($time*3600 < time::date2timestamp($flow->end_date) + 24*3600 - time::date2timestamp($flow->start_date)): ?>
		<li class="hour"><?php echo $time%24 ?></li>
		<?php $time++; ?>
	<? endwhile; ?>
</ul>

<?php foreach ($streams as $stream): ?>
<?php $c = color::hex2RGB($stream->color); ?>
<ul class="stream clearfix" data-stream='{"id": "<?php echo $stream->id ?>"}' style="width: <?php echo ceil((time::date2timestamp($flow->end_date)-time::date2timestamp($flow->start_date) + 24*3600)/(30*60))*144 ?>px; background-color: rgba(<?php echo $c['r'].','.$c['g'].','.$c['b'].',0.3' ?>)">
<?php foreach($stream->events->order_by('timestamp','ASC')->find_all() as $event): ?>
	<?php $pos = ceil(($event->timestamp - time::date2timestamp($flow->start_date))/(60*15))*72 ?>
	<li class="event<?= $event->id ?>" style="left: <?php echo $pos ?>px; width: <?php echo ceil($event->duration/30)*144 ?>px">
		<?php echo $event->render($stream->color,$flow->uri,$stream->uri,$taglist); ?>
	</li>
<?php endforeach; ?>
</ul>
<?php endforeach; ?>
<div class="clearfix" style="height: 20px; width: 1024px">
</div>
</div>
<div style="z-index: 1; position: relative; top: -21px; border-width: 0 1px; border-style: solid; border-color: red; height: 20px; margin-left: 150px; overflow: hidden" class="clearfix">
<?php $date_range = range(time::date2timestamp($flow->start_date),time::date2timestamp($flow->end_date),24*3600); ?>
<?php foreach($date_range as $i=>$day): ?>
	<div style="float: left; width: <?php echo number_format(round(100/count($date_range),2),2) ?>%">
		<div style="height: 20px; border-width: 0 1px; border-style: solid; border-color: transparent #660000 transparent red; text-align: center"> <?php echo strftime('%A',$day) ?> </div>
	</div>
<?php endforeach; ?>
</div>


<script type="text/javascript">
$(function(){
	events = new Object();events[0] = new Array();
	
	streams = $('#streams-scrollable').jScrollPane(
		{
			showArrows: false,
			horizontalGutter: 0,
			animateScroll: true
		}
	);
	api = streams.data('jsp');
	
	$('.stream').each(function(){
		id = $(this).data("stream").id; 
		events[id] = new Array();
		i=0;
		$('li',this).each(function(){
			events[id][i] = $(this).position().left;
			events[0].push(events[id][i]);
			i++;
		});
	});
	
	$('a.event-prev').click(function(){
		match = bestmatch(api.getContentPositionX(),events[$(this).data('stream').id]);
		api.scrollToX(match[0]);
	});
	
	$('a.event-next').click(function(){
		match = bestmatch(api.getContentPositionX(),events[$(this).data('stream').id]);
		api.scrollToX(match[1]);
	});
	
	$('a.event-first').click(function(){
		match = bestmatch(api.getContentPositionX(),events[$(this).data('stream').id]);
		api.scrollToX(match[2]);
	});
	
	$('a.event-last').click(function(){
		match = bestmatch(api.getContentPositionX(),events[$(this).data('stream').id]);
		api.scrollToX(match[3]);
	});
});

function bestmatch(goal,theList)
{
	var smallest = null;
	var biggest = null;
	var closeless = null;
	var closegreat = null;

	$.each(theList, function(){
		if (smallest == null || this < smallest) {
			smallest = this;
		}
		if (biggest == null || this > biggest) {
			biggest = this;
		}
	  	if (this < goal && (closeless==null || Math.abs(this - goal) < Math.abs(closeless - goal))) {
    		closeless = this;
		}
		if (this > goal && (closegreat==null || Math.abs(this - goal) < Math.abs(closegreat - goal))) {
			closegreat = this;
		}
	});
	
	if (closegreat == null) {
		closegreat = biggest;
	}
	if (closeless == null) {
		closeless = smallest;
	}
	
	return Array(closeless,closegreat,smallest,biggest);
}

</script>

<div class="vspace">
<span class="h3"><?php echo __('Tags') ?></span> 
<?php foreach($tags as $tag): ?>
	<div class="li"><?php echo $tag->render('html') ?></div>
<? endforeach; ?>
<?php if ($access['master']): ?>
	<?php echo html::anchor($flow->uri.'/tags',__('Manage tags'),array('class'=>'action')) ?>
<?php endif; ?>
</div>

<style>
<?php foreach($tags as $tag): ?>
.tag<?php echo $tag->id ?> {
	background-color: <?php echo $tag->color ?>
}
<?php endforeach; ?>
</style>

<script type="text/javascript">
$(function(){ 
	$('a.tooltip').tipsy({live: true});
});
</script>




