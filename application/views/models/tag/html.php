<a class="tag" href="<?php echo url::site($tag->flow->uri.'/tags/'.$tag->id) ?>">
	<div>
		<span class="marker" style="background-color: <?php echo $tag->color ?>"></span>
		<?php echo ucfirst($tag->title); ?>
		<?php echo isset($append) ? $append : '' ?>
	</div>
</a>