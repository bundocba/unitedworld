<?php defined('_JEXEC') or die('Restricted Access');
JHtml::_('behavior.modal', 'a.modal');
?>
	<center><a  class="modal" rel="{handler:'iframe', size:{x:(document.documentElement.clientWidth)*0.6, y:(document.documentElement.clientHeight)*0.65}}" href="<?php echo  JURI::root(); ?>/administrator/index.php?option=com_fw_fbpost&view=token&format=raw" title="Generete User Token">
		    <img alt="Generate User Token" src="/media/k2/assets/images/dashboard/joomlareader.png">
		    <span>Generate</span>
	    </a></center>




