<?php
defined('_JEXEC') or die;
$id = $params->get('cat_id');
$show_title = $module->showtitle;
$db = JFactory::getDbo();
$query = $db->getQuery(true);
$query->select('*');
$query->from("#__fw_video");
$query->where(" `cateid`='".$id."' ");
$query->order('ordering ASC');
$db->setQuery($query);
$rows = $db->loadObjectList();
$num_rows = count($rows);

//print_r('<pre>');print_r($rows);die;

?>
<div class="clr30"></div>
<div id="video_wrap">
	<?php 
	$i=0;
	foreach($rows as $video){
	$i++;
		$video_key = $video->link;
		$array_key = explode('http://youtu.be/', $video_key);
		$key = $array_key[1];
	?>
	<div class="video <?php if($i==$num_rows) {echo 'no_border';}?>" >
			<iframe width="<?php echo trim($params->get('width'))?>" height="<?php echo trim($params->get('height'))?>" src="//www.youtube.com/embed/<?php echo $key;?>" frameborder="0" allowfullscreen></iframe>
			<a class="video_name"><?php echo $video->name;?></a>
	</div>
	<div class="clr10"></div>
	<?php }?>
</div>