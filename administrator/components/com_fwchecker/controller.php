<?php defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.controller');
class fwcheckerController extends JController{
	function display($cachable = false){
		JRequest::setVar('view', JRequest::getCmd('view', 'checker'));
		parent::display($cachable);
	}
}

?>