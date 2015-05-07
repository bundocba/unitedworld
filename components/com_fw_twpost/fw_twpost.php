<?php defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.controller');
$controller = JController::getInstance('Fw_twpost');
$controller->execute(JRequest::getCmd('task'));
$controller->redirect();