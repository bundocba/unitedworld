<?php
/**
 * @package		Joomla.Site
 * @subpackage	mod_articles_latest
 * @copyright	Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

$cate_id = $params->get('catid');
$cate_id = $cate_id[0];
if($cate_id==9){
	$itemid = 280;
}
if($cate_id==10){
	$itemid = 294;
}
//echo $cate_id;
?>
<style type="text/css">
	#utility div.padding{
		margin-left: 50px;
	}
	#utility div.latest{
		float: left;
		width: 275px;
	}
	
	.latest .latest_wrap{
		float: left;
	    margin: 10px 0 0;
	    width: 100%;
	}
	.latest_wrap li{
		border-bottom: 1px dashed #000000;
	    text-align: justify;
	    float: left;
	}
	.latest_wrap li a{
		color: #000000;
		font-weight: bold;
		opacity:0.8;
		
	}
	.latest_wrap li span.published{
		font-weight: normal;
		font-style: italic;
		color: #717171;
		font-size: 12px;
	}
	.latest_wrap  .intro{
		float: left;
		max-width: 30%;
		margin-right: 10px;
		border: 2px solid #d8d8d8;
		border-radius:2px;
		 box-shadow: 0 0 1px 1px #d8d8d8;
	}
	.latest_wrap .seemore a{
		float: right;
		color: #197cc0 !important;
		margin-top: 15px;
		cursor: pointer;
	}
	.latest_wrap .seemore:hover a{
		color: #313131;
		text-decoration: underline;
	}
</style>
<div class="latest_wrap">
	<ul class="latestnews <?php echo $moduleclass_sfx; ?>">
	<?php foreach ($list as $item) :  
		$image = json_decode($item->images); 

		

		$image_intro = $image->image_intro;

		

		$link = $item->link;

		

		$created = date('_d/m/Y',strtotime($item->created));
		$style = "padding:15px 0";
		if($image_intro){
			$style ="padding: 10px 0;";
		}	
	?>
		<li style="<?php echo $style?>">
			<?php 
					if($image_intro){
						echo '<img class="intro" src="'.$image_intro.'" alt="'.$item->title.'"/>';
					}
			?>
			
			<a href="<?php echo $item->link; ?>">
				<?php echo $item->title.' '.'<span class="published">'.$created.'</span>'; ?>
			</a>
		</li>
	<?php endforeach; ?>
	</ul>
	<span class="seemore"><a href="<?php echo JRoute::_('index.php?option=com_content&view=category&id='.$cate_id.'&Itemid='.$itemid);?>">see more!</a></span>
</div>
<div class="clr10"></div>




