<div class="flow-controls">
<a class="control zoom-in"></a>
<a class="control zoom-out"></a>

<a class="control event-first flow-event-first" data-stream='{"id": "0"}'></a>
<a class="control event-prev flow-event-prev" data-stream='{"id": "0"}'></a>
<a class="control event-next flow-event-next" data-stream='{"id": "0"}'></a>
<a class="control event-last flow-event-last" data-stream='{"id": "0"}'></a>
</div>

<div id="timeline" class="scale<?php echo $scale/2 ?>">

<div style="z-index: 1; position: relative; top: 21px; border-width: 0 1px; border-style: solid; border-color: red; height: 20px; margin-left: 150px; overflow: hidden; font-size: 10.5pt" class="clearfix">
<?php foreach($flow['range'] as $i=>$day): ?>
	<div style="float: left; width: <?php echo number_format(round(100/count($flow['range']),2),2) ?>%">
		<div style="height: 20px; border-width: 0 1px; border-style: solid; border-color: transparent #660000 transparent red; text-align: center"> <?php echo strftime('%A',$day) ?> </div>
	</div>
<?php endforeach; ?>
</div>

<div class="timeline_info">
<div class="clearfix" style="height: 21px;"></div> <!-- height hack -->
<div class="clearfix">&nbsp;</div> <!-- height hack -->
<div class="clearfix">&nbsp;</div> <!-- height hack -->
<ul>
	<?php foreach($flow['streams'] as $sid => $stream): ?>
		<li style="background-color: <?php echo $stream['color'] ?>; height: <?php echo 47*max($stream['ongoing'],2) ?>px;" data-stream='{"height": "<?php echo max($stream['ongoing'],2) ?>"}'>
			<div>
				<div class="stream-title">
				<?php echo html::anchor($stream['uri'],$stream['title']) ?>
				</div>
				<div class="stream-meta">
				<?php echo count($stream['events']) ?> <?php echo __('events in total') ?>
				</div>
				<div class="stream-actions">
					<div class="stream-scroll">
						<a class="control event-prev stream-event-prev" data-stream='{"id": "<?php echo $sid ?>"}'></a>
						<a class="control event-next stream-event-next" data-stream='{"id": "<?php echo $sid ?>"}'></a>
					</div>
					<?php if ($access['master'] || isset($access['streams'][$sid])): ?>
						<?php echo html::anchor($stream['uri'].'/new',__('Add event'),array('class'=>'new-event')) ?>
					<? endif; ?>
				</div>
			</div>
		</li>
	<?php endforeach; ?>
</ul>
<div class="new-stream clearfix" style="height: 20px;">
<?php if ($access['master']): ?>
	<?php echo html::anchor(Route::get('stream-new')->uri(array('flow'=>$flow['uri'])),'<span class="add-one"></span>'.__('Add stream')) ?>
<?php endif; ?>
</div>
</div>

<div id="timeline_body" class="timeline_body">

<div class="days" style="width: <?php echo $width/2 ?>px">
<?php foreach($flow['range'] as $i=>$day): ?>
	<div class="day" style="width: <?php echo ceil($scale*24) ?>px"></div>
<?php endforeach; ?>
</div>

<!-- day -->
<div style="height: 20px" class="clearfix"></div>
<!-- hours -->
<ul class="hourline clearfix" style="width: <?php echo $width/2; ?>px">
	<li class="halfhour" style="width: <?php echo $scale/2; ?>px">&nbsp;</li>
	<?php $time = 1; ?>
	<?php while ($time*3600 < time::date2timestamp($flow['end_date']) + 24*3600 - time::date2timestamp($flow['start_date'])): ?>
		<li class="hour" style="width: <?php echo $scale; ?>px"><?php echo $time%24 ?>:00</li>
		<?php $time++; ?>
	<? endwhile; ?>
</ul>

<!-- -->
<?php foreach ($flow['streams'] as $sid => $stream): ?>
<ul class="stream clearfix" data-stream='{"id": "<?php echo $sid ?>"}' style="width: <?php echo $width/2 ?>px; height: <?php echo 47*max($stream['ongoing'],2) ?>px; background-color: <?php echo $stream['alpha'] ?>"  data-stream='{"height": "<?php echo max($stream['ongoing'],2) ?>"}'>
<?php foreach($stream['events'] as $eid => $event): ?>
	<?php $pos = $event['position']*$scale/4 ?>
	<li class="event event<?php echo $eid ?> drag" style="top: <?php echo 47*($event['ongoing']-1) ?>px; left: <?php echo $pos ?>px; width: <?php echo $event['width']*$scale/4 ?>px" data-event='{"position": "<?php echo $event['position'] ?>", "width" : "<?php echo $event['width'] ?>"}'>
		<div class="event_body" style="background-color: <?php echo $stream['color'] ?>">
			<a class="title" href="<?php echo $event['uri'] ?>" title="<?php echo $event['title'] ?>"><?php echo $event['title'] ?></a>
			<?php echo $event['body']; ?>
			<!--<div class="draghandle"></div>
			<div class="resizehandle"></div>-->
		</div>
	</li>
<?php endforeach; ?>
</ul>
<?php endforeach; ?>
</div>
<div style="height: 20px"></div> <!-- height hack! -->

<script type="text/javascript">

var scale = 6;
$(function(){
	events = new Object(); events[0] = new Array();
	
	streams = $('#timeline_body').jScrollPane(
		{
			showArrows: false,
			horizontalGutter: 0,
			animateScroll: true,
			autoReinitialise: true
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
		factor = Math.pow(2,scale-6);
		match = bestmatch(api.getContentPositionX()/factor,events[$(this).data('stream').id]);
		api.scrollToX(match[0]*factor);
	});
	
	$('a.event-next').click(function(){
		factor = Math.pow(2,scale-6);
		match = bestmatch(api.getContentPositionX()/factor,events[$(this).data('stream').id]);
		api.scrollToX(match[1]*factor);
	});
	
	$('a.event-first').click(function(){
		factor = Math.pow(2,scale-6);
		match = bestmatch(api.getContentPositionX()/factor,events[$(this).data('stream').id]);
		api.scrollToX(match[2]*factor);
	});
	
	$('a.event-last').click(function(){
		factor = Math.pow(2,scale-6);
		match = bestmatch(api.getContentPositionX()/factor,events[$(this).data('stream').id]);
		api.scrollToX(match[3]*factor);
	});
	
	$('a.zoom-in').click(function(){
		scale = Math.round(scale+1);
		
		if (scale == 4)
		{
			$('.event_body').qtip('disable');
		}
		
		if (scale > 6) 
		{
			scale = 6;
		}
		
		rescale(Math.pow(2,scale)/2);
	});
	
	$('a.zoom-out').click(function(){
		
		scale = scale-1;
		halfhour = Math.pow(2,scale);
		
		var timeline_width = $('#timeline_body').width();
		
		if (scale == 4)
		{
			$('.event_body').qtip('enable');
		}
		
		if (scale < 2) 
		{
			halfhour = 2*Math.round(timeline_width/(<?php echo $flow['width'] ?>*2)+1);
			scale = 1;
		}
		
		if(halfhour*<?php echo $flow['width'] ?>*2 < timeline_width)
		{
			scale = scale+1;
		}
		
		rescale(halfhour/2);
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

function rescale(quarter)
{
	width = <?php echo $flow['width'] ?>*quarter;
	$('#timeline').removeClass();
	$('#timeline').addClass('scale' + quarter*2)
	
	$('.days').css({'width' : width + 'px'});
	$('.hourline').css({'width' : width + 'px'});
	
	$('.day').each(function(){ $(this).css({'width' : quarter*48*2 + 'px'});  });
	$('.hour').each(function(){ $(this).css({'width' : quarter*2*2 + 'px'});  });
	$('.halfhour').each(function(){ $(this).css({'width' : quarter*2 + 'px'});  });
	
	$('.stream').each(function(){
		$(this).css({'width' : width + 'px'});
	});
	
	$('.event').each(function(){
		p = $(this).data('event').position;
		w = $(this).data('event').width;
		$(this).css({'left' : p*quarter + 'px', 'width' : w*quarter + 'px'}); 
	});
}

</script>

<style>
<?php foreach($tags as $tag): ?>
.tag<?php echo $tag->id ?> {
	background-color: <?php echo $tag->color ?>
}
<?php endforeach; ?>
</style>

<script type="text/javascript">
$(function(){ 
	$('a.tooltip').tipsy({live: true, gravity: $.fn.tipsy.autoNS});

$('.event_body').qtip({
	id: 'events',
	content: {
		text: function() { return $(this).html() }
	},
	events: {
		render: function(event, api) {
			// Grab the tip element
			var elem = api.elements.tip;
		}
	},
	style : {
		tip: {
			corner: true
		}
	},
	position: {
	  container: $('#timeline_body'),
	  my: 'top center',
	  at: 'bottom center',
	  adjust: {
	  	y: -10
	  }
	},
	show: {
	  delay: 10,
	  effect: false
   },
   hide: {
   	delay: 10,
	event: 'mouseout',
	effect: false,
	distance: 40,
	fixed: true
   }
});

$('.event_body').qtip('disable');


});

/*jQuery(function($){
   $('.drag')
      .click(function(){
         $( this ).toggleClass("selected");
      })
      .drag("init",function(){
         if ( $( this ).is('.selected') )
            return $('.selected');                 
      })
      .drag("start",function( ev, dd ){
         dd.attr = $( ev.target ).attr("className");
         dd.width = $( this ).width();
      })
     .drag("end",function( ev, dd ){
      	$( this ).data('id');
      	$( this ).width();
		$( this ).position.left;
      })
      .drag(function( ev, dd ){
         var props = {};
         if ( dd.attr.indexOf("resize") > -1 ){
            props.width = Math.max( 144, Math.round((dd.width + dd.deltaX)/72)*72 );
         }
         if ( dd.attr.indexOf("draghandle") > -1 ){
            props.top = 0;
            props.left = Math.round((api.getContentPositionX() + dd.offsetX -170)/72)*72;
         }
         $( this ).css( props );
      });
});*/

</script>




