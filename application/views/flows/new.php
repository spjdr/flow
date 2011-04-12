<?php echo form::open(NULL, array('id' => 'create_flow')) ?><h4><?php echo form::label('title',__('Title')) ?></h4><? if (isset($errors['title'])) echo error::form($errors['title']); ?><?php echo form::input('title',isset($post) ? $post['title'] : '') ?><h4><?php echo form::label('uri',__('URI')) ?></h4><? if (isset($errors['uri'])) echo error::form($errors['uri']); ?><?php echo form::input('uri',isset($post) ? $post['uri'] : '')?><h4><?php echo form::label('tags',__('Tags')) ?></h4><? if (isset($errors['tags'])) echo error::form($errors['tags']); ?><div id="tags"><?php if (isset($tags) && count($tags) > 0): ?>	<?php $i = 1; ?>	<?php foreach($tags as $tag): ?>		<div class="tag">			<?php echo form::input('tags['.$i.']',$tag,array('id'=>'tag'.$i)) ?>			<?php $i++ ?>		</div>	<? endforeach; ?>	<?php echo form::button('add_tag',__('Add tag'),array('type'=>'button','id'=>'add_tag')) ?>	<?php echo form::button('remove_tag',__('Remove tag'),array('type'=>'button','id'=>'remove_tag')) ?><?php else: ?>	<div class="tag">		<?php echo form::input('tags[1]','',array('id'=>'tag1')) ?>	</div>	<?php echo form::button('add_tag','Add tag',array('type'=>'button','id'=>'add_tag')) ?>	<?php echo form::button('remove_tag','Remove tag',array('type'=>'button','id'=>'remove_tag','disabled'=>'disabled')) ?><?php endif; ?></div><h4><?php echo form::label('description',__('Description')) ?></h4><? if (isset($errors['description'])) echo error::form($errors['description']); ?><?php echo form::textarea('description',isset($post) ? $post['description'] : '') ?><div class="clearfix"><h4><?php echo form::label('start_date',__('Start date')) ?></h4><? if (isset($errors['start_date'])) echo error::form($errors['start_date']); ?><?php echo form::input('start_date',isset($post) ? $post['start_date'] : '',array('id'=>'start_date','class'=>'datepicker')) ?></div><div class="clearfix"><h4><?php echo form::label('end_date',__('End date')) ?></h4><? if (isset($errors['end_date'])) echo error::form($errors['end_date']); ?><?php echo form::input('end_date',isset($post) ? $post['end_date'] : '',array('id'=>'end_date','class'=>'datepicker')) ?></div><br/><?php echo form::button(NULL, __('Create'), array('type' => 'submit')) ?><?php echo form::close() ?><!-- Following script modified from http://charlie.griefer.com/blog/index.cfm/2009/9/17/jQuery-Dynamically-Adding-Form-Elements --><script type="text/javascript">$(document).ready(function() {		$('input[name=title]').blur(function(){		$('input[name=uri]').attr('value',cleanURI($(this).attr('value')));	});		$('input[name=uri]').blur(function(){		uri = cleanURI($(this).attr('value'))		$(this).attr('value',uri);	});		$('#remove_tag').click(function(){		var num = new Number($('#tags div').length);		if (num > 1) {			$('#tag' + num).parent('div').remove();			if (num == 2) {				$('#remove_tag').attr('disabled','disabled');			}		} 	});	$('#add_tag').click(function() {		var num		= $('#tags div').length;	// how many "duplicatable" input fields we currently have		var new_num	= new Number(num + 1);		// the numeric ID of the new input field being added		// create the new element via clone(), and manipulate it's ID using newNum value		var elem = $('#tag' + num).parent('div').clone();		// manipulate the name/id values of the input inside the new element		elem.children(':first').attr('id', 'tag' + new_num).attr('name', 'tags[' + new_num + ']').attr('value','');			// insert the new element after the last "duplicatable" input field		$('#tag' + num).parent('div').after(elem);		// enable the "remove" button		$('#remove_tag').attr('disabled','');	});		$('.datepicker').datePicker();		<?php if(isset($post)): ?>		var start_date = new Date('<?php echo $post['start_date'] ?>');	var end_date = new Date('<?php echo $post['end_date'] ?>');		$('#start_date').dpSetEndDate(end_date.addDays(-1).asString());	$('#end_date').dpSetStartDate(start_date.addDays(1).asString());		<? endif; ?>		$('#start_date').bind(		'dpClosed',		function(e, selectedDates)		{			var d = selectedDates[0];			if (d) {				d = new Date(d);				$('#end_date').dpSetStartDate(d.addDays(1).asString());			}		}	);	$('#end_date').bind(		'dpClosed',		function(e, selectedDates)		{			var d = selectedDates[0];			if (d) {				d = new Date(d);				$('#start_date').dpSetEndDate(d.addDays(-1).asString());			}		}	);});</script>