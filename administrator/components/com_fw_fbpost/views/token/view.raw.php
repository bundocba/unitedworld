<?php
defined('_JEXEC') or die;
class Fw_fbpostViewToken extends JViewLegacy
{
	public function display($tpl = null)
	{	
		$component		 = JRequest::getVar('option','com_fw_fbpost');
		$result 		 = JComponentHelper::getComponent($option= $component);
		$app_id			 = $result->params->get('app_id');
		$app_secrect 	 = $result->params->get('app_secret');
?>
<style type="text/css">
#button {
    background: none repeat scroll 0 0 #555555;
    border: medium none;
    border-radius: 10px 10px 10px 10px;
    color: #FFFFFF;
    font-family: Tahoma;
    font-size: 25px;
    height: 60px;
    margin: 30px;
    width: 300px;
}
#button:hover{
background:none repeat scroll 0 0 #000;
cursor:pointer;
}
</style>
<form method="POST" name="f2" action="http://sms.futureworkz.com.sg/beta/home.php">
<h2 style="text-align:center;padding-top:50px;">FACEBOOK APP INFOMATION</h2>
<table width="600" border="0" align="center" style="padding-top:50px;">
  <tr>
    <td width="145">APP ID :<span style="color:red">(*)</span></td>
    <td width="445"><input type="text" name="app_id" class="app_id"  value="<?php echo $app_id;?>" size="40" /></td>
  </tr>
  <tr>
    <td>APP SECRET :<span style="color:red">(*)</span></td>
    <td> <input type="text" name="app_secret" class="app_secret"  value="<?php echo $app_secrect;?>" size="50" /></td>
  </tr>
</table>
<center>
<input id="button" name="submit" value="Generate Access Token" type="submit">
</center>
</form>
<?php
		die;
	}
}
