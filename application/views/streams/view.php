<?php if ($access['master'] || isset($access['streams'][$stream->id])): ?>
<div class="clearfix">
<?php echo html::anchor(Route::get('stream')->uri(array('flow'=>$flow->uri,'stream'=>$stream->uri,'action'=>'edit')),__('Edit stream'),array('class'=>'action')) ?>
<?php echo html::anchor(Route::get('stream')->uri(array('flow'=>$flow->uri,'stream'=>$stream->uri,'action'=>'delete')),__('Delete stream'),array('class'=>'action')) ?>
</div>
<?php endif; ?>

<div id="flow-description" class="markdowned">
	<?php echo $stream->description ?>
</div>

<div class="clearfix vspace"></div>

<div>
<span class="h3 vspace"><?php echo __('Event list'); ?></span>
<?php if ($access['master'] || isset($access['streams'][$stream->id])): ?>
<?php echo html::anchor(Route::get('event-new')->uri(array('flow'=>$flow->uri,'stream'=>$stream->uri)),__('New event'),array('class'=>'action')) ?>
<?php endif; ?>
</div>
<table class="vspace">
<tr>
	<th style="padding: 2px 6px; background-color: #e5e5e5">Event</th>
	<th style="padding: 2px 6px; background-color: #eee"><?php echo __('Time') ?></th>
	<th style="padding: 2px 6px; background-color: #eee"><?php echo __('Duration') ?></th>
</tr>
<?php foreach($events as $event): ?>
	<tr>
		<td style="padding: 2px 6px; background-color: #e5e5e5">
			<?php echo html::anchor(Route::get('event')->uri(array('flow'=>$flow->uri,'stream'=>$stream->uri,'event'=>$event->id)),$event->title) ?>
		</td>
		<td style="padding: 2px 6px; background-color: #eee">
			<?php echo strftime('%A d. %e. %B kl. %H:%M',$event->timestamp) ?>
		</td>
		<td style="padding: 2px 6px; background-color: #eee">
			<?php echo $event->durations[$event->duration] ?>
		</td>
	</tr>
<?php endforeach; ?>
</table>

<style>
<?php foreach($flow->tags->find_all() as $tag): ?>
.tag<?php echo $tag->id ?> {
	background-color: <?php echo $tag->color ?>
}
<?php endforeach; ?>
</style>