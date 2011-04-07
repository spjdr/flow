<?php echo html::anchor(Route::get('stream')->uri(array('flow'=>$flow->uri,'stream'=>$stream->uri,'action'=>'edit')),__('Edit stream')) ?><?php echo html::anchor(Route::get('stream')->uri(array('flow'=>$flow->uri,'stream'=>$stream->uri,'action'=>'delete')),__('Delete stream')) ?><h2><?php echo $stream->title ?></h2><div class="">	<?php echo $stream->description ?></div><?php echo html::anchor(Route::get('event-new')->uri(array('flow'=>$flow->uri,'stream'=>$stream->uri)),__('New event')) ?><div style="margin: 0" class="streams"><ul class="timeline clearfix" style="width: <?php echo ceil((time::date2timestamp($flow->end_date)-time::date2timestamp($flow->start_date))/(30*60))*144 ?>px">	<?php $time = time::date2timestamp($flow->start_date)+1; ?>	<?php while ($time < time::date2timestamp($flow->end_date)): ?>		<li class="day"><?php echo date('l j \o\f F Y',$time) ?></li>		<?php $time+= 24*3600; ?>	<? endwhile; ?></ul><ul class="timeline clearfix" style="width: <?php echo ceil((time::date2timestamp($flow->end_date)-time::date2timestamp($flow->start_date))/(30*60))*144 ?>px">	<li class="halfhour">&nbsp;</li>	<?php $time = 1; ?>	<?php while ($time*3600 < time::date2timestamp($flow->end_date) - time::date2timestamp($flow->start_date)): ?>		<li class="hour"><?php echo $time%24 ?></li>		<?php $time++; ?>	<? endwhile; ?></ul><?php $c = color::hex2RGB($stream->color); ?><ul class="stream" style="width: <?php echo ceil((time::date2timestamp($flow->end_date)-time::date2timestamp($flow->start_date))/(30*60))*144 ?>px; background-color: rgba(<?php echo $c['r'].','.$c['g'].','.$c['b'].',0.2' ?>)"><?php foreach($stream->events->find_all() as $event): ?>	<li style="left: <?php echo ceil(($event->timestamp-time::date2timestamp($flow->start_date))/(60*15))*72 ?>px; width: <?php echo ceil($event->duration/30)*144 ?>px">		<div style="background-color: <?php echo $stream->color ?>">			<?php echo html::anchor(Route::get('event')->uri(array('flow'=>$flow->uri,'stream'=>$stream->uri,'event'=>$event->id)),$event->title) ?>		</div>	</li><?php endforeach; ?></ul></div><div><ul><?php foreach($events as $event): ?>	<li>		<?php echo html::anchor(Route::get('event')->uri(array('flow'=>$flow->uri,'stream'=>$stream->uri,'event'=>$event->id)),$event->title) ?>	</li><?php endforeach; ?></ul></div><?php echo html::anchor($flow->uri.'/'.$stream->uri.'/invitations',__('Invite users')) ?>