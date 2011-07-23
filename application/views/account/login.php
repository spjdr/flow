<?php echo form::open(NULL, array('id' => 'login')) ?>

<h4><?php echo form::label('username',__('Username')) ?></h4>
<? if (isset($errors['username'])) echo $errors['username']; ?>
<?php echo form::input('username') ?>

<h4><?php echo form::label('password',__('Password')) ?></h4>
<? if (isset($errors['password'])) echo $errors['password']; ?>
<?php echo form::password('password') ?>

<br/>
<?php echo form::button(NULL, '<span>'.__('Login').'</span>', array('type' => 'submit','class'=>'press-this')) ?>

<?php echo form::close() ?>