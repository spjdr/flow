<?php echo form::open(NULL, array('id' => 'edit_event')) ?>

<h4><?php echo form::label('title',__('Title')) ?></h4>
<? if (isset($errors['title'])) echo error::form($errors['title']); ?>
<?php echo form::input('title',isset($post) ? $post['title'] : $event->title) ?>

<h4><?php echo form::label('tags',__('Tags')) ?></h4>

<?php foreach($flow_categorized_tags as $category=>$tags): ?>
	<ul>
	<?php foreach ($tags as $tag): ?>
		<? if ($tag->select == 'multiple'): ?>
			<li><?php echo form::checkbox('tags['.$tag->id.']',TRUE,isset($post['tags'][$tag->id]) ? TRUE : isset($event_tags[$tag->id])) ?> <?php echo $tag->title; ?></li>
		<? else: ?>
			<li><?php echo form::radio('category['.$tag->category.']',$tag->id,isset($post['tags'][$tag->id]) ? TRUE : isset($event_tags[$tag->id])) ?> <?php echo $tag->title; ?></li>
		<? endif; ?>
	<?php endforeach; ?>
	</ul>
<?php endforeach; ?>

<h4><?php echo form::label('description',__('Description')) ?></h4>
<? if (isset($errors['description'])) echo error::form($errors['description']); ?>
<?php echo form::textarea('description',isset($post) ? $post['description'] : $event->description) ?>

<h4><?php echo form::label('notes',__('Private notes')) ?></h4>
<? if (isset($errors['notes'])) echo error::form($errors['notes']); ?>
<?php echo form::textarea('notes',isset($post) ? $post['notes'] : $event->notes) ?>

<div class="clearfix">
<h4><?php echo form::label('time',__('Time')) ?></h4>
<? if (isset($errors['date'])) echo error::form($errors['date']); ?>
<?php echo form::input('date',date('d/m/Y',isset($post) ? $post['timestamp'] : $event->timestamp),array('id'=>'date','class'=>'datepicker')) ?>
<?php echo form::select('hours',array_combine(range(00,23,1),range(00,23,1)),date('H',isset($post) ? $post['timestamp'] : $event->timestamp)) ?> 
:
<?php echo form::select('minutes',array_combine(range(0,45,15),range(0,45,15)),date('i',isset($post) ? $post['timestamp'] : $event->timestamp)) ?>
</div>

<h4><?php echo form::label('duration',__('Duration')) ?></h4>
<? if (isset($errors['duration'])) echo error::form($errors['duration']); ?>
<?php echo form::select('duration',$durations,isset($post) ? $post['duration'] : $event->duration) ?>

<br/><br/>
<?php echo form::button(NULL, __('Save'), array('type' => 'submit')) ?>

<?php echo form::close() ?>

<script type="text/javascript">
$(function(){
	$('.datepicker').datePicker();
	
	var start_date = '<?php echo $flow->start_date ?>';
	var end_date = '<?php echo $flow->end_date ?>';
	
	$('#date').dpSetStartDate(start_date);
	$('#date').dpSetEndDate(end_date);
});
	</script>
