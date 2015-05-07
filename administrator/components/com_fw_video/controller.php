<?php defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.controller');
class fw_videoController extends JController{
	function display($cachable = false){
		JRequest::setVar('view', JRequest::getCmd('view', 'category'));
		parent::display($cachable);
	}
}

?>