<?php defined('_JEXEC') or die('Restricted access');
if (!JFactory::getUser()->authorise('core.manage', 'com_fw_video')){
	return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
}
JLoader::register('fw_videoHelper', dirname(__FILE__) . DS . 'helpers' . DS . 'fw_video.php');
jimport('joomla.application.component.controller');
$controller = JController::getInstance('fw_video');
$controller->execute(JRequest::getCmd('task'));
$controller->redirect();
?>