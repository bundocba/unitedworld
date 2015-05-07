<?php
$query="select u.id as userid, u.name as name, u.email as email, u.username as username from #__users as u, #__user_usergroup_map as ug where `u`.`id`=`ug`.`user_id` and `ug`.`group_id`='8' ";
$db->setQuery($query);
$users_admin=$db->loadObjectList();

?>
            <tr>
				<td valign="top">
					<?php echo $numb++; ?>
				</td>
				<td valign="top">
					User Administrator
				</td>
				<td valign="top">
<?php
for($i=0;$i<count($users_admin);$i++){
?>
					Name: <a href="index.php?option=com_users&view=user&layout=edit&id=<?php echo $users_admin[$i]->userid; ?>" target="_blank"><?php echo $users_admin[$i]->name; ?></a> - Email: <?php echo $users_admin[$i]->email; ?> - Username: <?php echo $users_admin[$i]->username; ?><br/><br/>
<?php
}
?>
				</td>
            </tr>
<?php

?>