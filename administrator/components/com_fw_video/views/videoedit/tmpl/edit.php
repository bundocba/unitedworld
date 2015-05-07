<?php
// No direct access
defined('_JEXEC') or die('Restricted access');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
?>
<form enctype="multipart/form-data" action="<?php echo JRoute::_('index.php?option=com_fw_video&layout=edit&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="adminForm" class="form-validate">
	<fieldset class="adminform">
		<legend>Details</legend>
		<ul class="adminformlist">
<?php foreach($this->form->getFieldset() as $field): ?>
			<li>
<?php
	echo $field->label;
	if (strtolower($field->type) == 'editor') {
		echo '<div style="float:left;">';
		echo $field->input;
		echo '</div>';
	}else if(strtolower($field->type) == 'file') {
		echo '<div style="float:left;">';
		echo $field->input;
		echo '<a href="'.JURI::root().'images/fw_video/'.$field->value.'" target="_blank">View file '.$field->value.'</a>';
		echo '</div>';
	}else{
		echo $field->input;	
	}
?>
            </li>
<?php endforeach; ?>
		</ul>
	</fieldset>
	<div>
		<input type="hidden" name="task" value="fw_video.edit" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>
<style>
fieldset.adminform label, fieldset.adminform span.faux-label{
	min-width:250px;
}
</style>