<p><?php echo __('Do you really wan\'t to delete the flow') ?> <?php echo html::anchor($flow->uri,html::entities($flow->title)) ?>? <?php echo __('This action cannot be undone.'); ?></p><?php echo form::open(NULL, array('id' => 'delete_flow')) ?><?php echo form::submit('delete', __('I understand. I still want to delete the flow.')) ?><?php echo form::submit('cancel', __('No stop! I don\'t wan\'t to delete the flow.')) ?><?php echo form::close() ?>