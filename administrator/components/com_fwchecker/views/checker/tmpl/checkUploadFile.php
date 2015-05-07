<?php
function reformatFilesArray($name, $type, $tmp_name, $size){
	$name = JFile::makeSafe($name);
	return array(
		'name'		=> $name,
		'type'		=> $type,
		'tmp_name'	=> $tmp_name,
		'size'		=> $size
	);
}
function checkUploadFileMedia(){
	$files 				= $_FILES["files"];
	$files 				= array_map( 'reformatFilesArray',(array) $files['name'], (array) $files['type'], (array) $files['tmp_name'], (array) $files['size']);

	for($i=0;$i<count($files);$i++){
		$filename			= uniqid().".".JFile::getExt($files[$i]["name"]);
		$fileDestination 	= JPATH_SITE.DS."images".DS.$filename;
		$uploaded 			= JFile::upload($files[$i]["tmp_name"], $fileDestination);
		if (!$uploaded) {
			return false;
		}
		JFile::delete(JPATH_SITE.DS."images".DS.$filename);
	}

	return true;
}
if(checkUploadFileMedia()==false){
?>
            <tr class="red">
				<td>
					<?php echo $numb++; ?>
				</td>
				<td>
					Upload File
				</td>
				<td>
					Fail
				</td>
				<td>
					<a href="index.php?option=com_media" target="_blank">Fix now</a>
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
					Upload File
				</td>
				<td>
					Upload File Successfully
				</td>
				<td>
					
				</td>
            </tr>
<?php
	}
}