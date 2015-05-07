<?php defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.view');
class fwcheckerViewchecker extends JView{
	function display($tpl = null){
		$this->addToolBar();
		$this->setDocument();
		parent::display($tpl);
	}	
	protected function addToolBar(){
		JToolBarHelper::title('Checker', 'fwchecker');
		$document = JFactory::getDocument();
		$document->addStyleDeclaration('.icon-48-fwchecker {background-image: url(../administrator/components/com_fwchecker/images/icon_item.png);}');
	}	
	protected function setDocument(){
		$document = JFactory::getDocument();
		$document->setTitle(JText::_('Component Checker'));
	}
}