<?php echo form::open(NULL, array('id' => 'edit_flow')) ?><h1><?php echo __('Edit flow') ?></h1><h4><?php echo form::label('uri',__('URI')) ?></h4><? if (isset($errors['uri'])) echo $errors['uri']; ?><?php echo form::input('uri',$flow->uri)?><h4><?php echo form::label('title',__('Title')) ?></h4><? if (isset($errors['title'])) echo $errors['title']; ?><?php echo form::input('title',$flow->title) ?><h4><?php echo form::label('description',__('Description')) ?></h4><? if (isset($errors['description'])) echo $errors['description']; ?><?php echo form::textarea('description',$flow->description) ?><br/><?php echo form::button(NULL, __('Save'), array('type' => 'submit')) ?><?php echo form::close() ?>