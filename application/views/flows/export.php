<p><?php echo __('Please choose which format to export:') ?></p>

<ul>
<?php foreach($formats as $format): ?>
	<li><?php echo html::anchor($flow->uri.'.'.$format,$format); ?></li>
<?php endforeach; ?>
</ul>