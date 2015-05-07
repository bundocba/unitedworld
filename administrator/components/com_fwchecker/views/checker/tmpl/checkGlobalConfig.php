<?php
if($config->getValue('editor')!="jckeditor"){
?>
            <tr class="red">
				<td>
					<?php echo $numb++; ?>
				</td>
				<td>
					Default Editor
				</td>
				<td>
					Not JCK
				</td>
				<td>
					<a href="index.php?option=com_config" target="_blank">Fix now</a>
				</td>
            </tr>
<?php
}else{
	if($_POST["showerror"]==0){
?>
            <tr class="blue">
				<td>
					<?php echo $numb++; ?>
				</td>
				<td>
					Default Editor
				</td>
				<td>
					JCK
				</td>
				<td>
					
				</td>
            </tr>
<?php
	}
}

if($config->getValue('sef')==0){
?>
            <tr class="red">
				<td>
					<?php echo $numb++; ?>
				</td>
				<td>
					SEO configuration
				</td>
				<td>
					Search Engine Friendly URLs
				</td>
				<td>
					Disable - <a href="index.php?option=com_config" target="_blank">Fix now</a>
				</td>
            </tr>
<?php
}else{
	if($_POST["showerror"]==0){
?>
            <tr class="blue">
				<td>
					<?php echo $numb++; ?>
				</td>
				<td>
					SEO configuration
				</td>
				<td>
					Search Engine Friendly URLs
				</td>
				<td>
					Enable
				</td>
            </tr>
<?php
	}
}

if($config->getValue('sef_rewrite')==0){
?>
            <tr class="red">
				<td>
					<?php echo $numb++; ?>
				</td>
				<td>
					SEO configuration
				</td>
				<td>
					Use URL rewriting
				</td>
				<td>
					Disable - <a href="index.php?option=com_config" target="_blank">Fix now</a>
				</td>
            </tr>
<?php
}else{
	if($_POST["showerror"]==0){
?>
            <tr class="blue">
				<td>
					<?php echo $numb++; ?>
				</td>
				<td>
					SEO configuration
				</td>
				<td>
					Use URL rewriting
				</td>
				<td>
					Enable
				</td>
            </tr>
<?php
	}
}

if($config->getValue('MetaDesc')==""){
?>
            <tr class="red">
				<td>
					<?php echo $numb++; ?>
				</td>
				<td>
					Meta data configuration
				</td>
				<td>
					MetaDesc
				</td>
				<td>
					Value is empty - <a href="index.php?option=com_config" target="_blank">Fix now</a>
				</td>
            </tr>
<?php
}else{
	if($_POST["showerror"]==0){
?>
            <tr class="blue">
				<td>
					<?php echo $numb++; ?>
				</td>
				<td>
					Meta data configuration
				</td>
				<td>
					MetaDesc
				</td>
				<td>
					<?php echo $config->getValue('MetaDesc'); ?>
				</td>
            </tr>
<?php
	}
}

if($config->getValue('MetaKeys')==""){
?>
            <tr class="red">
				<td>
					<?php echo $numb++; ?>
				</td>
				<td>
					Meta data configuration
				</td>
				<td>
					MetaKeys
				</td>
				<td>
					Value is empty - <a href="index.php?option=com_config" target="_blank">Fix now</a>
				</td>
            </tr>
<?php
}else{
	if($_POST["showerror"]==0){
?>
            <tr class="blue">
				<td>
					<?php echo $numb++; ?>
				</td>
				<td>
					Meta data configuration
				</td>
				<td>
					MetaKeys
				</td>
				<td>
					<?php echo $config->getValue('MetaKeys'); ?>
				</td>
            </tr>
<?php
	}
}

if($config->getValue('robots')!=""){
?>
            <tr class="red">
				<td>
					<?php echo $numb++; ?>
				</td>
				<td>
					Search engine
				</td>
				<td>
					Robot txt
				</td>
				<td>
					Fail, it shoulde be "Index and Follow" - <a href="index.php?option=com_config" target="_blank">Fix now</a>
				</td>
            </tr>
<?php
}else{
	if($_POST["showerror"]==0){
?>
            <tr class="blue">
				<td>
					<?php echo $numb++; ?>
				</td>
				<td>
					Search engine
				</td>
				<td>
					Robot txt
				</td>
				<td>
					Correct
				</td>
            </tr>
<?php
	}
}

