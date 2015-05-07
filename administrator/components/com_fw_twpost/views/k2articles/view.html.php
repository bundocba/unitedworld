<?php
defined('_JEXEC') or die;
class Fw_twpostViewK2Articles extends JViewLegacy
{
	protected $items;
	protected $pagination;
	protected $state;


	public function display($tpl = null)
	{
		$db		= JFactory::getDbo();
		$query  = "SELECT DISTINCT(article_id) FROM #__tw_k2articles_history_post";
		$db->setQuery($query);
		$article_ids = $db->loadAssocList();
		$article_ida =array();
		foreach($article_ids AS $key=>$val){
			$article_ida[] = $val['article_id'];
		}
		$this->a_ids 		= $article_ida;
		$this->items		= $this->get('Items');
		$this->pagination	= $this->get('Pagination');
		$this->state		= $this->get('State');
		$this->authors		= $this->get('Authors');
		
		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		// We don't need toolbar in the modal window.
		if ($this->getLayout() !== 'modal') {
			$this->addToolbar();
		}
		
		parent::display($tpl);
	}

	protected function addToolbar()
	{
		$user		= JFactory::getUser();
		JSubMenuHelper::addEntry(JText::_('Content Post Twitter'), 'index.php?option=com_fw_twpost&view=articles', 'articles');
		JSubMenuHelper::addEntry(JText::_('K2 Post Twitter'), 'index.php?option=com_fw_twpost&view=k2articles', 'k2articles');
		JToolBarHelper::title(JText::_('Articles :: K2 Post Twitter'), 'article.png');
		JToolBarHelper::preferences('com_fw_twpost', 400, 600, JText::_('Configuration'));
		JToolBarHelper::custom('k2articles.post_tweet', 'checkin.png', 'checkin.png', 'POST', true);
	}
}
