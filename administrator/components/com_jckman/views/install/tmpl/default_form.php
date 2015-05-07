<script language="javascript" type="text/javascript">
<!--

	Joomla.submitbutton2 = function(pressbutton)
	{
		var form = document.adminForm;
		
		// do field validation
		if (form.install_package.value == ""){
			alert( "<?php echo JText::_( 'COM_INSTALLER_MSG_INSTALL_PLEASE_SELECT_A_PACKAGE', true ); ?>" );
		} else {
			form.installtype.value  = 'upload';
			for (i=0;i< form.toolbars.length;i++){
					if (form.toolbars[i].checked==true){
						selectedType = form.toolbars[i].value;
						break 
					}
				}
		}	
		if(selectedType == 'all') enableselections();
		submitform(pressbutton);	
	}
	Joomla.submitbutton3 = function(pressbutton) {
		var form = document.adminForm;

		// do field validation
		if (form.install_directory.value == ""){
			alert( "<?php echo JText::_( 'COM_INSTALLER_MSG_INSTALL_PLEASE_SELECT_A_DIRECTORY', true ); ?>" );
		} else {
			form.installtype.value = 'folder';
			for (i=0;i< form.toolbars.length;i++){
				if (form.toolbars[i].checked==true){
					selectedType = form.toolbars[i].value;
					break 
				}
			}
			if(selectedType == 'all') enableselections();
			form.submit();
		}
	}

	Joomla.submitbutton4 = function(pressbutton) {
		var form = document.adminForm;

		// do field validation
		if (form.install_url.value == "" || form.install_url.value == "http://"){
			alert( "<?php echo JText::_( 'COM_INSTALLER_MSG_INSTALL_ENTER_A_URL', true ); ?>" );
		} else {
			form.installtype.value = 'url';
			for (i=0;i< form.toolbars.length;i++){
				if (form.toolbars[i].checked==true){
					selectedType = form.toolbars[i].value;
					break 
				}
			}
		if(selectedType == 'all') enableselections();
			form.submit();
		}
	}
//-->
</script>

<form enctype="multipart/form-data" action="index.php" method="post" name="adminForm" id="adminForm">

	<?php if ($this->ftp) : ?>
		<?php echo $this->loadTemplate('ftp'); ?>
	<?php endif; ?>
	
	
	<div style="float: left; width: 50%; margin-right: 1%;">
	<table class="adminform">
	<tr>
		<th colspan="2"><?php echo JText::_( 'COM_INSTALLER_UPLOAD_PACKAGE_FILE' ); ?></th>
	</tr>
	<tr>
		<td width="120">
			<label for="install_package"><?php echo JText::_( 'COM_INSTALLER_PACKAGE_FILE' ); ?>:</label>
		</td>
		<td>
			<input class="input_box" id="install_package" name="install_package" type="file" size="57" />
			<input class="button" type="button" value="<?php echo JText::_('COM_INSTALLER_UPLOAD_AND_INSTALL'); ?>" onclick="Joomla.submitbutton2()" />
		</td>
	</tr>
	</table>
	<table class="adminform">
	<tr>
		<th colspan="2"><?php echo JText::_( 'COM_INSTALLER_INSTALL_FROM_DIRECTORY' ); ?></th>
	</tr>
	<tr>
		<td width="120">
			<label for="install_directory"><?php echo JText::_( 'COM_INSTALLER_INSTALL_DIRECTORY' ); ?>:</label>
		</td>
		<td>
			<input type="text" id="install_directory" name="install_directory" class="input_box" size="70" value="<?php echo $this->state->get('install.directory'); ?>" />
			<input type="button" class="button" value="<?php echo JText::_( 'COM_INSTALLER_INSTALL_BUTTON' ); ?>" onclick="Joomla.submitbutton3()" />
		</td>
	</tr>
	</table>

	<table class="adminform">
	<tr>
		<th colspan="2"><?php echo JText::_( 'COM_INSTALLER_INSTALL_FROM_URL' ); ?></th>
	</tr>
	<tr>
		<td width="120">
			<label for="install_url"><?php echo JText::_( 'COM_INSTALLER_INSTALL_URL' ); ?>:</label>
		</td>
		<td>
			<input type="text" id="install_url" name="install_url" class="input_box" size="70" value="http://" />
			<input type="button" class="button" value="<?php echo JText::_( 'COM_INSTALLER_INSTALL_BUTTON' ); ?>" onclick="Joomla.submitbutton4()" />
		</td>
	</tr>
	</table>
	</div>
	<div style="float: left; width: 49%;">
		<script type="text/javascript">
					function allselections() {
						var e = document.getElementById('selections');
							e.disabled = true;
						var i = 0;
						var n = e.options.length;
						for (i = 0; i < n; i++) {
							e.options[i].disabled = true;
							e.options[i].selected = true;
						}
					}
					function disableselections() {
						var e = document.getElementById('selections');
							e.disabled = true;
						var i = 0;
						var n = e.options.length;
						for (i = 0; i < n; i++) {
							e.options[i].disabled = true;
							e.options[i].selected = false;
						}
					}
					function enableselections() {
						var e = document.getElementById('selections');
							e.disabled = false;
						var i = 0;
						var n = e.options.length;
						for (i = 0; i < n; i++) {
							e.options[i].disabled = false;
						}
					}
		</script>
	<table class="adminform">
	<tr>
		<th colspan="2"><?php echo JText::_( 'COM_JCKMAN_INSTALLER_SELECT_TOOLBAR_TO_INSTALL_PLUGIN' ); ?></th>
	</tr>
	<tr>
		<td width="120">
			<?php echo JText::_( 'Toolbars(s)' ); ?>
		</td>
		<td>
			<label for="toolbars-all"><input id="toolbars-all" type="radio" name="toolbars" value="all" onclick="allselections();" checked="checked" /><?php echo JText::_( 'All' ); ?></label>
			<label for="toolbars-none"><input id="toolbars-none" type="radio" name="toolbars" value="none" onclick="disableselections();" /><?php echo JText::_( 'None' ); ?></label>
			<label for="toolbars-select"><input id="toolbars-select" type="radio" name="toolbars" value="select" onclick="enableselections();" /><?php echo JText::_( 'Select From List' ); ?></label>
		</td>
	</tr>
	<tr>
		<td valign="top" class="key">
			<?php echo JText::_( 'Toolbar Selection' ); ?>:
		</td>
		<td>
			<?php echo $this->lists['selections']; ?>
		</td>
	</tr>
	</table>
</div>	
	<script type="text/javascript">allselections();</script>
	<input type="hidden" name="view" value="install" />
	<input type="hidden" name="type" value="" />
	<input type="hidden" name="installtype" value="upload" />
	<input type="hidden" name="task" value="install.install" />
	<input type="hidden" name="option" value="com_jckman" />
	<input type="hidden" name="controller" value="Install" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>