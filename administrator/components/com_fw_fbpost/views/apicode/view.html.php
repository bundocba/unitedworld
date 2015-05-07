<?php
defined('_JEXEC') or die;
class Fw_fbpostViewApicode extends JViewLegacy
{
	public function display($tpl = null)
	{	
		$this->addToolbar();
		parent::display($tpl);
	}

	protected function addToolbar()
	{
		JToolBarHelper::title(JText::_('Generate User Token'), 'article.png');
		JSubMenuHelper::addEntry(JText::_('Content Post Facebok'), 'index.php?option=com_fw_fbpost&view=articles', 'articles');
		JSubMenuHelper::addEntry(JText::_('K2 Post Facebook'), 'index.php?option=com_fw_fbpost&view=k2articles', 'k2articles');
		JSubMenuHelper::addEntry(JText::_('Generate User Token'), 'index.php?option=com_fw_fbpost&view=apicode', 'k2articles');
	}
}
