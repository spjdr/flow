<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
  <head>
  <meta name="language" content="dan">
  <meta name="date" content="<?php echo date('m-d-Y') ?>">
  <meta name="robots" content="index, follow">
  <meta name="googlebot" content="noarchive"> 
  <meta http-equiv="Content-type" content="text/html; charset=UTF-8" />
  <meta http-equiv="Content-Language" content="en-us" />
  <title><?php echo $title;?></title>
  <meta name="title" content="<?php echo $title;?>">
  <meta name="keywords" content="<?php echo $meta_keywords;?>" />
  <meta name="description" content="<?php echo $meta_description;?>" />
  <meta name="copyright" content="<?php echo $meta_copywrite;?>" />
  <base href="<?php echo url::site() ?>"> 
  <link rel="image_src" href="<?php echo url::site($image_src) ?>" />  
  <?php foreach($styles['common'] as $file => $type) { echo HTML::style($file, array('media' => $type)), "\n"; }?>
	<?php if (isset($styles['ie'])): ?>
	  <!--[if IE]>
		<?php foreach($styles['ie'] as $file => $type) { echo HTML::style($file, array('media' => $type)), "\n"; }?>
  	  <![endif]-->
  	<?php endif; ?>
  <?php foreach($scripts as $file) { echo HTML::script($file), "\n"; }?>
  </head>
  <body>
	<div id="container">
		<div id="message">
		<?php if (isset($message) && $message!=''): ?>
		<div class="success">
			<?php echo $message ?>
		</div>
		<?php endif; ?>
		<?php if (isset($error) && $error!=''): ?>
		<div class="failure">
			<?php echo $error ?>
		</div>
		<?php endif; ?>
		</div>
    	<div id="header" class="clearfix">
    		<!--<a id="logo" class="text3d" href="<?php echo url::site() ?>">flow</a>
			<?php echo $header;?>-->
			<?php if(isset($flows)): ?>
			<ul id="nav" class="menu">
				<?php if (isset($flow)): ?>
				<li><?php echo HTML::anchor($flow->uri,str_replace(' ','&nbsp',$flow->title.'<span>&nbsp;</span>')) ?>
					<ul>
						<?php if ($flows[$flow->id]['master']): ?>
						<li><?php echo HTML::anchor($flow->uri.'/edit',HTML::image('assets/images/wrench_icon&16.png').' '.__('Options')); ?></li>
						<li><?php echo HTML::anchor($flow->uri.'/users',HTML::image('assets/images/users_icon&16.png').' '.__('Users')); ?></li>
						<li><?php echo HTML::anchor($flow->uri.'/invitations',HTML::image('assets/images/mail_icon&16.png').' '.__('Invitations')); ?></li>
						<?php endif; ?>
						<li><?php echo HTML::anchor($flow->uri.'/streams',HTML::image('assets/images/align_just_icon&16.png')." ".__('Streams')); ?></li>
						<li><?php echo HTML::anchor($flow->uri.'/tags',HTML::image('assets/images/tag_icon&16.png').' '.__('Tags')); ?></li>
					</ul>
				</li>
				<?php endif; ?>
				<li><?php echo HTML::anchor('new','Flows<span>&nbsp;</span>') ?>
					<ul>
						<?php foreach($flows as $f): ?>
							<li><?php echo HTML::anchor($f['uri'],str_replace(' ','&nbsp',$f['title'])) ?></li>
						<?php endforeach; ?>
						<li><?php echo HTML::anchor('new',HTML::image('assets/images/add_item.png').' '.__('New flow')) ?></li>
					</ul>
				</li>
				<li><?php echo HTML::anchor('pages/help',__('Help').'&nbsp;<b>?</b>') ?></li>
			</ul>
			<?php endif; ?>
			<ul id="menu" class="menu">
				<?php if (!Auth::instance()->logged_in()): ?>
					<li><?php echo HTML::anchor('',__('Home')) ?></li>
					<li><?php echo HTML::anchor('register',__('Register')) ?></li>
					<li><?php echo HTML::anchor('login',__('Login')) ?></li>
				<?php else: ?>
					<li>
						<?php echo HTML::anchor('@'.$username,ucfirst($name).'<span>&nbsp;</span>') ?>
						<ul>
							<li><?php echo HTML::anchor('@'.$username.'/info',HTML::image('assets/images/contact_card_icon&16.png').' '.__('Info')) ?></li>
							<li><?php echo HTML::anchor('@'.$username.'/picture',HTML::image('assets/images/picture_icon&16.png').' '.__('Picture')) ?></li>
							<li><?php echo HTML::anchor('@'.$username.'/password',HTML::image('assets/images/key_icon&16.png').' '.__('Password')) ?></li>
						</ul>
					</li>
					<li><?php echo HTML::anchor('logout',__('Logout')) ?></li>
				<?php endif; ?>
			</ul>
		</div>
		<!--<div class="header-stream-yellow"></div>
  		<div class="header-stream-magenta"></div>
   		<div class="header-stream-green"></div>
   		<div class="header-stream-blue"></div>-->
		<div id="site">
		<?php if (isset($title)): ?>
		<div id="title">
			<?php if (isset($breadcrumbs)): ?>
				<div id="breadcrumbs"><?php echo implode(' &raquo; ',$breadcrumbs); ?> &raquo;</div>
			<? endif; ?>
			<?php echo $title ?>
		</div>
		<?php endif; ?>
		<div id="content" class="clearfix">
			<?php echo $content;?>
		</div>
		</div>
		<div id="footer">
			<?php echo $footer;?>
		</div>
    </div>
  </body>
  <script type="text/javascript">
  	$(function () {	
		$('.menu li').hover(
			function () {
				//show its submenu
				$('ul', this).css('min-width', Math.max($(this).css('width'),150)).slideDown(50);
			}, 
			function () {
				//hide its submenu
				$('ul', this).slideUp(50);			
			}
		);
	
		$('form').submit(function(){
			$('button[type=submit]', this).attr('disabled', 'disabled');
			$('button[type=submit]', this).attr('disabled', 'disabled');
		});
	});
  </script>
</html>