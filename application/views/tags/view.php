A total of <?php echo $events->count() ?> events has been tagged as <b><?php echo $tag->title ?>.</b>

<?php if ($access['master']): ?>
	<?php echo html::anchor(Route::get('flow-item')->uri(array('flow'=>$flow->uri,'item'=>$tag->id,'action'=>'edit')),__('Edit tag'),array('class'=>'action')) ?>
	<?php echo html::anchor(Route::get('flow-item')->uri(array('flow'=>$flow->uri,'item'=>$tag->id,'action'=>'delete')),__('Delete tag'),array('class'=>'action')) ?>
<? endif; ?>

<table>
<?php foreach($events as $event): ?>
	<tr>
	<td style="padding: 2px 6px; background-color: #e5e5e5"><?php echo $streams[$event->stream_id]->title ?></td><td style="padding: 2px 6px; background-color: #eee"> <?php echo html::anchor($flow->uri.'/'.$streams[$event->stream_id]->uri.'/'.$event->id,$event->title) ?></td>
	</tr>
<?php endforeach; ?>
</table>

<style>
<?php foreach($tags as $tag): ?>
.tag<?php echo $tag->id ?> {
	background-color: <?php echo $tag->color ?>
}
<?php endforeach; ?>
</style>



