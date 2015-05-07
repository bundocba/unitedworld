<?php defined('_JEXEC') or die('Restricted Access');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.modal', 'a.modal');
jimport('joomla.filesystem.file');
$config = JFactory::getConfig();
$db 	= JFactory::getDBO();

$query = $db->getQuery(true);
$query->select("*");
$query->from("#__rsform_forms");
$query->order('FormName ASC');
$db->setQuery($query);
$rows_form = $db->loadObjectList();

include("seleUpload.php");

include("seleRS.php");

?>
<style>
#adminForm a{font-size: 8pt !important;}
#adminForm .red td,
#adminForm .red td a,
#adminForm .red td i{
	color:red !important;
}
#adminForm .blue td,
#adminForm .blue td a,
#adminForm .blue td i{
	color:blue !important;
}
</style>
<form action="<?php echo JRoute::_('index.php?option=com_fwchecker'); ?>" method="post" id="adminForm" name="adminForm" enctype="multipart/form-data">
	<table class="adminlist">
		<thead>
            <tr>
				<th colspan="3">
					<h3 style="float:left;">Get Selenium automatic upload file</h3>
				</th>
				<th colspan="3">
					<h3 style="float:left;">Get Selenium for RSFORM</h3>
				</th>
            </tr>
		</thead>
		<tbody>
            <tr>
				<td width="100" valign="top">
					Path to folder
				</td>
				<td width="100" valign="top">
					<input type="text" id="pathFolder" name="pathFolder" value="" size="30" />
					<br/>
					<pre>Eg: /Users/iker/Downloads/test/documents/</pre>
				</td>
				<td valign="top">
					<input type="submit" value="Get Now" />
				</td>
				<td width="100" valign="top">
					Select Form
					<br/>
					<br/>
					Your email
				</td>
				<td width="100" valign="top">
					<select type="text" id="RSformID" name="RSformID">
						<option value="">Select form</option>
<?php for($i=0;$i<count($rows_form);$i++){ ?>
						<option value="<?php echo $rows_form[$i]->FormId; ?>"><?php echo $rows_form[$i]->FormName; ?></option>
<?php } ?>
					</select>
					<br/>
					<br/>
					<input type="text" id="emailRSQA" name="emailRSQA" value="" size="30" />
				</td>
				<td valign="top">
					<input type="submit" value="Get Now" />
				</td>
            </tr>
		</tbody>
	</table>
	<table class="adminlist">
		<thead>
            <tr>
				<th colspan="6">
					<h3 style="float:left;">Check Joomla</h3>
				</th>
            </tr>
		</thead>
		<tfoot>
            <tr>
				<th colspan="6">
					<input type="submit" value="Check Now" />
				</th>
            </tr>
		</tfoot>
		<tbody>
            <tr>
				<td width="100">
					Your email
				</td>
				<td>
					<input type="text" id="email" name="email" value="<?php echo $_POST["email"]; ?>" size="30" />
				</td>
				<td width="100">
					File to upload
				</td>
				<td>
					<input type="file" id="files" name="files[]" multiple>
				</td>
				<td width="100">
					Type
				</td>
				<td>
					<select name="showerror" id="showerror">
						<option value="1">Show error checking only</option>
						<option value="0">Show all checking</option>
					</select>
					<script>
						$("showerror").value="<?php echo $_POST["showerror"]; ?>";
					</script>
				</td>
            </tr>
		</tbody>
	</table>
<?php if($_POST["test"]==1){ ?>

<?php $numb=1; ?>
	<table class="adminlist">
		<thead>
            <tr>
				<th colspan="3">
					<h3>Information</h3>
				</th>
            </tr>
            <tr>
				<th>
					#
				</th>
				<th>
					Type
				</th>
				<th>
					Note
				</th>
            </tr>
		</thead>
		<tbody>
		
<?php include_once("infoUserAdmin.php"); ?>

<?php include_once("infoFinderPlg.php"); ?>

		</tbody>
	</table>
	
<?php $numb=1; ?>
	<table class="adminlist">
		<thead>
            <tr>
				<th colspan="4">
					<h3>Checker</h3>
				</th>
            </tr>
            <tr>
				<th>
					#
				</th>
				<th>
					Type
				</th>
				<th>
					Error
				</th>
				<th>
					Note
				</th>
            </tr>
		</thead>
		<tbody>
		
<?php include_once("checkDirectory.php"); ?>

<?php include_once("checkGlobalConfig.php"); ?>

<?php include_once("checkSendMail.php"); ?>

<?php include_once("checkUploadFile.php"); ?>

<?php include_once("checkGA.php"); ?>

		</tbody>
	</table>
<?php } ?>
<input type="hidden" name="test" value="1" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="view" value="checker" />
<?php echo JHtml::_('form.token'); ?>
</form>