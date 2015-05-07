<?php
$ch = curl_init(JURI::root());
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
$content = curl_exec($ch);
curl_close($ch);

$_checkGA=explode("_gaq.push(['_setAccount', '", $content);
if(count($_checkGA)>1){
	$_checkGA=explode("']);",$_checkGA[1]);
	$_GAID=$_checkGA[0];
	$_checkGA=true;
}else{
	$_checkGA=false;
}

if($_checkGA==false){
?>
            <tr class="red">
				<td>
					<?php echo $numb++; ?>
				</td>
				<td>
					Google Analytics
				</td>
				<td>
					Not exist
				</td>
				<td>

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
					Google Analytics
				</td>
				<td>
					Embeded
				</td>
				<td>
					<?php echo $_GAID; ?>
				</td>
            </tr>
<?php
	}
}