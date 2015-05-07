<?php
$sender = array( 
	$config->getValue( 'config.mailfrom' ),
	$config->getValue( 'config.fromname' ) 
);

if($config->getValue( 'config.fromname' )==""){
?>
            <tr class="red">
				<td>
					<?php echo $numb++; ?>
				</td>
				<td>
					Mail configuration
				</td>
				<td>
					From name
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
					Mail configuration
				</td>
				<td>
					From name
				</td>
				<td>
					<?php echo $config->getValue( 'config.fromname' ); ?>
				</td>
            </tr>
<?php
	}
}

if($config->getValue( 'config.mailfrom' )==""){
?>
            <tr class="red">
				<td>
					<?php echo $numb++; ?>
				</td>
				<td>
					Mail configuration
				</td>
				<td>
					From email
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
					Mail configuration
				</td>
				<td>
					From email
				</td>
				<td>
					<?php echo $config->getValue( 'config.mailfrom' ); ?>
				</td>
            </tr>
<?php
	}
}

$mailer = JFactory::getMailer();
$mailer->setSender($sender);
$mailer->addRecipient($_POST["email"]);
$mailer->setSubject('Test email');
$body   = "This is the testing email for:".JURI::root();
$mailer->isHTML(true);
$mailer->Encoding = 'base64';
$mailer->setBody($body);

if((!$mailer->Send())||$_POST["email"]==""){
?>
            <tr class="red">
				<td>
					<?php echo $numb++; ?>
				</td>
				<td>
					Mail configuration
				</td>
				<td>
					Cannot send mail
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
					Mail configuration
				</td>
				<td>
					Test mail send successfully
				</td>
				<td>
					
				</td>
            </tr>
<?php
	}
}