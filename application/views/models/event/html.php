<?php foreach($event->tags->find_all() as $tag): ?>
		<a href="<?php echo url::site() ?>{{flow}}/tags/<?php echo $tag->id ?>" class="tooltip marker tag<?php echo $tag->id ?>" title="{{tag<?php echo $tag->id ?>}}"></a>
<?php endforeach; ?>

<p><?php echo text::limit_chars($event->description,ceil($event->duration/(3))) ?></p>
