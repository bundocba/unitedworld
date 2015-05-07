<?php
if($_POST["RSformID"]!=""){
	$query = $db->getQuery(true);
	$query->select("*");
	$query->from("#__rsform_components");
	$query->where("FormId=".$_POST["RSformID"]);
	$query->where("Published=1");
	$query->order("`Order` ASC");
	$db->setQuery($query);
	$rows_rows = $db->loadObjectList();
	
	$_arr_action=array();
	for($i=0;$i<count($rows_rows);$i++){
		$row_rows=$rows_rows[$i];
		
		$_arr_action_in=array();
		
		$query = $db->getQuery(true);
		$query->select("ComponentTypeName");
		$query->from("#__rsform_component_types");
		$query->where("ComponentTypeId=".$row_rows->ComponentTypeId);
		$db->setQuery($query);
		$row_type = $db->loadResult();
		
		$query = $db->getQuery(true);
		$query->select("PropertyValue");
		$query->from("#__rsform_properties");
		$query->where("ComponentId=".$row_rows->ComponentId);
		$query->where("PropertyName='NAME'");
		$db->setQuery($query);
		$row_name = $db->loadResult();
		
		$query = $db->getQuery(true);
		$query->select("PropertyValue");
		$query->from("#__rsform_properties");
		$query->where("ComponentId=".$row_rows->ComponentId);
		$query->where("PropertyName='VALIDATIONRULE'");
		$db->setQuery($query);
		$row_rule = $db->loadResult();
		
		if(
			$row_type=="textBox"||
			$row_type=="textArea"
		){
			$_arr_action_in[]="type";
			$_arr_action_in[]="id=".$row_name;
			if($row_rule=="email"){
				$_arr_action_in[]=$_POST["emailRSQA"];
			}else{
				$_arr_action_in[]="Demo by QA";
			}
			
			$_arr_action[]=$_arr_action_in;
		}else if(
			$row_type=="selectList"
		){
			$_arr_action_in[]="select";
			$_arr_action_in[]="id=".$row_name;
			$_arr_action_in[]="index=1";
			
			$_arr_action[]=$_arr_action_in;			
		}else if(
			$row_type=="checkboxGroup"||
			$row_type=="radioGroup"
		){
			$_arr_action_in[]="click";
			$_arr_action_in[]="id=".$row_name."0";
			$_arr_action_in[]="";
			
			$_arr_action[]=$_arr_action_in;			
		}
		
	}
	
	print_r("
<?xml version=\"1.0\" encoding=\"UTF-8\"?>
<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Strict//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd\">
<html xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"en\" lang=\"en\">
<head profile=\"http://selenium-ide.openqa.org/profiles/test-case\">
<meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\" />
<link rel=\"selenium.base\" href=\"".JURI::root()."\" />
<title>Test Form</title>
</head>
<body>
<table cellpadding=\"1\" cellspacing=\"1\" border=\"1\">
<thead>
<tr><td rowspan=\"1\" colspan=\"3\">Test Form</td></tr>
</thead><tbody>
<tr>
	<td>open</td>
	<td>".JURI::root()."index.php?option=com_rsform&amp;view=rsform&amp;formId=".$_POST["RSformID"]."</td>
	<td></td>
</tr>
");
	for($i=0;$i<count($_arr_action);$i++){
		$_arr_action_in=$_arr_action[$i];
	print_r("
<tr>
	<td>".$_arr_action_in[0]."</td>
	<td>".$_arr_action_in[1]."</td>
	<td>".$_arr_action_in[2]."</td>
</tr>
");
	}
	print_r("
</tbody></table>
</body>
</html>
	");
?>
	<a href="javascript:window.location='index.php?option=com_fwchecker';">Back</a>
<?php
	die;
}
?>