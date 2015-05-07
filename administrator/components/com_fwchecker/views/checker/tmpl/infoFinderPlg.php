<?php
$query="select * from #__extensions where folder='finder' and enabled=1 ";
$db->setQuery($query);
$finders=$db->loadObjectList();
if(count($finders)>0){
?>
            <tr>
				<td valign="top">
					<?php echo $numb++; ?>
				</td>
				<td valign="top">
					Finder Plugins<br/><i>*Do we really need this plugin?</i>
				</td>
				<td valign="top">
<?php
for($i=0;$i<count($finders);$i++){
?>
					<a href="index.php?option=com_plugins&view=plugin&layout=edit&extension_id=<?php echo $finders[$i]->id; ?>" target="_blank"><?php echo $finders[$i]->name; ?></a><br/><br/>
<?php
}
?>
				</td>
            </tr>
<?php
}else{
?>
            <tr>
				<td valign="top">
					<?php echo $numb++; ?>
				</td>
				<td valign="top">
					Finder Plugins
				</td>
				<td valign="top">
					No plugin is enable
				</td>
            </tr>
<?php
}
?>