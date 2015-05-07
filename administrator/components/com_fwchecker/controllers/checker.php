<?php defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.controlleradmin');
class fwcheckerControllercompo extends JControllerAdmin{
	function display($cachable = false){
		JRequest::setVar('view', 'checker');
		parent::display($cachable);
	}
}