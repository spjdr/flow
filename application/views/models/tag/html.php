<div class="tag">	<a class="marker" style="background-color: <?php echo $tag->color ?>"></a>	<?php echo html::anchor($tag->flow->uri.'/tags/'.$tag->id,ucfirst($tag->title)); ?>	<?php echo isset($append) ? $append : '' ?></div>