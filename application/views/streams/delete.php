<?php echo form::open(NULL, array('id' => 'delete_streams')) ?><h1><?php echo __('Delete stream') ?> <?php echo html::entities($stream->title) ?></h1><?php echo form::button('delete', __('Delete'), array('type' => 'submit')) ?><?php echo form::close() ?>