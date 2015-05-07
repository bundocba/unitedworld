<?php defined('_JEXEC') or die('Restricted access');
if (!JFactory::getUser()->authorise('core.manage', 'com_fwchecker')){
	return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
}

JLoader::register('fwcheckerHelper', dirname(__FILE__) . DS . 'helpers' . DS . 'fwchecker.php');
jimport('joomla.application.component.controller');
$controller = JController::getInstance('fwchecker');
$controller->execute(JRequest::getCmd('task'));
$controller->redirect();

?>