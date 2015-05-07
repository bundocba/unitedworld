<form action="index.php" method="post" name="adminForm" id="adminForm">
	<?php if ($this->ftp) : ?>
		<?php echo $this->loadTemplate('ftp'); ?>
	<?php endif; ?>
	<?php if (count($this->items)) : ?>
	<table class="adminlist" cellspacing="1">
		<thead>
			<tr>
				<th class="title" width="10"><?php echo JText::_( 'JGRID_HEADING_ROW_NUMBER' ); ?></th>
				<th class="title" width="10">&nbsp;</th>
				<th class="title"><?php echo JText::_( 'COM_JCK_UNINSTALLER_LANGUAGE' ); ?></th>
				<th class="title" align="center"><?php echo JText::_( 'COM_JCK_UNINSTALLER_LANGUAGE_TAG' ); ?></th>
				<th class="title" width="25%"><?php echo JText::_( 'COM_JCK_UNINSTALLER_PLUGIN' ); ?></th>
				<th class="title" width="10%" align="center"><?php echo JText::_( 'JVERSION' ); ?></th>
				<th class="title" width="15%"><?php echo JText::_( 'JDATE' ); ?></th>
				<th class="title" width="25%"><?php echo JText::_( 'JAUTHOR' ); ?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="8"><?php echo $this->pagination->getListFooter(); ?></td>
			</tr>
		</tfoot>
		<tbody>
		<?php for ($i=0, $n=count($this->items), $rc=0; $i < $n; $i++, $rc = 1 - $rc) : ?>
			<?php
				$this->loadItem($i);
				echo $this->loadTemplate('item');
			?>
		<?php endfor; ?>
		</tbody>
	</table>
	<?php else : ?>
		<?php echo JText::_( 'COM_JCK_UNINSTALLER_NO_CUSTOM_LANGUAGES' ); ?>
	<?php endif; ?>

	<input type="hidden" name="task" value="manage" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="option" value="com_jckman" />
	<input type="hidden" name="view" value="language" />
	<input type="hidden" name="controller" value="Install" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>