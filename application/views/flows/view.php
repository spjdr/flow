<!--<div class="flow_logo">
	<img src="<?php echo $flow->logo ?>" />
</div>-->

<div id="flow-description" class="markdowned">
	<?php echo fust::markdown($flow->description) ?>
</div>

<?php echo $timeline ?>

<div class="vspace">
<span class="h3"><?php echo __('Tags') ?></span> 
<?php foreach($tags as $tag): ?>
	<div class="li"><?php echo $tag->render('html') ?></div>
<? endforeach; ?>
<?php if ($access['master']): ?>
	<?php echo html::anchor($flow->uri.'/tags',__('Manage tags'),array('class'=>'action')) ?>
<?php endif; ?>
</div>

<script>

/*jQuery(function($){
   $('.drag')
      .click(function(){
         $( this ).toggleClass("selected");
      })
      .drag("init",function(){
         if ( $( this ).is('.selected') )
            return $('.selected');                 
      })
      .drag("start",function( ev, dd ){
         dd.attr = $( ev.target ).attr("className");
         dd.width = $( this ).width();
      })
     .drag("end",function( ev, dd ){
      	$( this ).data('id');
      	$( this ).width();
		$( this ).position.left;
      })
      .drag(function( ev, dd ){
         var props = {};
         if ( dd.attr.indexOf("resize") > -1 ){
            props.width = Math.max( 144, Math.round((dd.width + dd.deltaX)/72)*72 );
         }
         if ( dd.attr.indexOf("draghandle") > -1 ){
            props.top = 0;
            props.left = Math.round((api.getContentPositionX() + dd.offsetX -170)/72)*72;
         }
         $( this ).css( props );
      });
});*/

</script>




