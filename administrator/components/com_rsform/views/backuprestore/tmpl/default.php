<?php
/**
* @version 1.4.0
* @package RSform!Pro 1.4.0
* @copyright (C) 2007-2011 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');
?>

<script type="text/javascript">
function submitbutton(task)
{
	if (task == 'backup.download' && document.adminForm.boxchecked.value == 0)
		return alert('<?php echo JText::_('RSFP_BACKUP_SELECT', true); ?>');
	submitform(task);
}
</script>

<form enctype="multipart/form-data" action="index.php?option=com_rsform" method="post" name="adminForm">
	<fieldset>
		<legend><?php echo JText::_('RSFP_BACKUP_RESTORE_INSTRUCTIONS'); ?></legend>
		<?php echo JText::sprintf('RSFP_BACKUP_RESTORE_INSTRUCTIONS_DESC', JText::_('RSFP_BACKUP_GENERATE'), JText::_('RSFP_RESTORE')); ?>
	</fieldset>
	
	<?php echo $this->tabs->startPane('backuprestore-pane'); ?>
	
	<?php echo $this->tabs->startPanel('Backup', 'backup'); ?>
	<table class="adminheading" width="100%">
	<?php if ($this->writable) { ?>
	<tr>
		<td>
			<table class="adminlist">
			<thead>
	        <tr>
	            <th width="1"><input type="checkbox" name="toggle" value="" onClick="checkAll(<?php echo count($this->forms); ?>);" /></th>
	            <th class="title"><?php echo JText::_('RSFP_FORM_TITLE'); ?></th>
	            <th class="title"><?php echo JText::_('RSFP_FORM_NAME'); ?></th>
	            <th class="title" width="15"><?php echo JText::_('RSFP_SUBMISSIONS'); ?></th>
	        </tr>
			</thead>
	        <?php
	        $i = 0;
			$k = 0;
	        foreach ($this->forms as $row) { ?>
	        <tr class="row<?php echo $k; ?>">
				<td><?php echo JHTML::_('grid.id', $i, $row->FormId); ?></td>
	            <td><label for="cb<?php echo $i; ?>"><?php echo !empty($row->FormTitle) ? strip_tags($row->FormTitle) : '<em>no title</em>'; ?></label></td>
	            <td><?php echo $row->FormName; ?></td>
	            <td><?php echo $row->_allSubmissions; ?></td>
	        </tr>
			<?php
	            $i++;
				$k=1-$k;
	        }
			?>
			<tr>
				<td width="1%"><input type="checkbox" name="submissions" id="submissions" value="1" /></td>
				<td colspan="3"><label for="submissions"><strong class="rsform_notok"><?php echo JText::_('RSFP_BACKUP_SUBMISSIONS');?></strong></label></td>
			</tr>
	        </table>
			<button class="rs_button" type="button" onclick="submitbutton('backup.download')"><?php echo JText::_('RSFP_BACKUP_GENERATE'); ?></button>
		</td>
	</tr>
	<?php } else { ?>
	<tr>
		<th class="dbbackup">
			<?php echo JText::_('RSFP_BACKUP_NOT_WRITABLE');?>
		</th>
	</tr>
	<?php }	?>
	</table>
	<?php echo $this->tabs->endPanel(); ?>
	
	<?php echo $this->tabs->startPanel(JText::_('RSFP_RESTORE'), 'restore'); ?>
	<?php if(!$this->zlib) { ?>
		<p>The installer can't continue before zlib is installed</p>
	<?php
	} else { ?>
	<table class="adminheading" width="100%">
	<?php if ($this->writable) { ?>
	<tr>
		<td align="left" width="1%" nowrap="nowrap"><label for="userfile"><?php echo JText::_('RSFP_PACKAGE_FILE');?></label></td>
		<td><input class="text_area" name="userfile" id="userfile" type="file" size="70"/></td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td><input type="checkbox" id="overwrite_checkbox" name="overwrite" value="1"/> <label for="overwrite_checkbox"><?php echo JText::_('RSFP_OVERWRITE_FORMS');?></label></td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td><button class="rs_button" type="button" onclick="submitbutton('restore.process')"><?php echo JText::_('RSFP_RESTORE');?></button></td>
	</tr>
	<?php } else { ?>
	<tr>
		<th class="dbbackup">
			<?php echo JText::_('RSFP_RESTORE_NOT_WRITABLE');?>
		</th>
	</tr>
	<?php } ?>
	</table>
	<?php }	?>
	<?php echo $this->tabs->endPanel(); ?>
	<?php echo $this->tabs->endPane(); ?>
	
	<input type="hidden" name="task" value="restore.process"/>
	<input type="hidden" name="option" value="com_rsform"/>
	<input type="hidden" name="boxchecked" value="0"/>
</form>