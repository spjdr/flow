<?php echo '<?' ?>xml version="1.0" encoding="UTF-8" <?php echo '?>' ?>
<flow xmlns="http://spjdr.dk/flow/flowxml" xml-version="11071901" title="<?php echo $flow->title ?>" id="http://spjdr.dk/flow/<?php echo $flow->uri ?>.xml" data-updated="<?php echo strftime('%FT%T%z',$flow->updated_timestamp) ?>"> <!--Tidszone og sommertid?-->
	<start time="<?php echo strftime('%FT00:00:00%z',$flow->start_timestamp) ?>"/>
	<end time="<?php echo strftime('%FT00:00:00%z',$flow->end_timestamp) ?>"/>
	<description type="html">
<?php echo ($flow->description != '' ? "\t\t\t\t".$flow->description."\n" : '') ?>
	</description>
	<tags>
<?php 	foreach ($tags as $tag): ?>
		<tag><?php echo $tag->title ?></tag>
<?php 	endforeach; ?>
	</tags>
<?php 	foreach ($streams as $stream): ?>
	<stream title="<?php echo $stream->title; ?>" id="<?php echo $stream->id ?>" color="<?php echo $stream->color ?>">
		<description>
<?php 		echo ($stream->description != '' ? "\t\t\t".$stream->description."\n" : '') ?>
		</description>
<?php 	foreach ($stream->events->order_by('timestamp','ASC')->find_all() as $event): ?>
		<event title="<?php echo $event->title ?>" id="<?php echo $event->id ?>">
			<subtitle></subtitle>
			<start time="<?php echo strftime('%FT%T%z',$event->timestamp) ?>"/>
			<end time="<?php echo strftime('%FT%T%z',$event->end_timestamp) ?>"/> 
			<description type="html">
<?php 			echo ($event->description != '' ? "\t\t\t\t".$event->description."\n" : '') ?>
			</description>
			<location>
				<description>
<?php 			echo ($event->location != '' ? "\t\t\t\t\t".$event->location."\n" : '') ?>
				</description>
				<coordinates latitude="N56 10.352" longitude="E010 11.825"/> <!-- Format: HDDD° MM.mmm'. Skal skrives på en standardiseret måde der virker godt i vores programmeringsmiljøer -->
			</location>
			<organiser>
				<name><?php echo $flow->title ?></name>
				<phone>+4588888888</phone>
				<e-mail>flow@spjdr.dk</e-mail>
				<web>http://www.spjdr.dk/flow</web>
			</organiser>
			<photos>
<?php			if ($event->photo != ''): ?>
				<photo><?php echo $event->photo ?></photo>
<?php 			endif; ?>
			</photos>
			<tags><?php echo ($event->cache != '' ? "\n\t\t\t\t".$event->tag('xml',$flow->uri,$tags)."\n\t\t\t\n" : '') ?></tags>
		</event>
<?php 	endforeach; ?>
	</stream>
<?php endforeach; ?>
</flow>