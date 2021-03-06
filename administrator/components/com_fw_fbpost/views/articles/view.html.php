<?php
defined('_JEXEC') or die;
class Fw_fbpostViewArticles extends JViewLegacy
{
	protected $items;
	protected $pagination;
	protected $state;


	public function display($tpl = null)
	{	
		$db=JFactory::getDbo();
		$query = "SELECT DISTINCT(article_id) FROM #__articles_history_post";
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
		JSubMenuHelper::addEntry(JText::_('Content Post Facebok'), 'index.php?option=com_fw_fbpost&view=articles', 'articles');
		JSubMenuHelper::addEntry(JText::_('K2 Post Facebook'), 'index.php?option=com_fw_fbpost&view=k2articles', 'k2articles');
		JSubMenuHelper::addEntry(JText::_('Generate User Token'), 'index.php?option=com_fw_fbpost&view=apicode', 'k2articles');
		JToolBarHelper::title(JText::_('Articles :: Post Facebook'), 'article.png');
		JToolBarHelper::preferences('com_fw_fbpost', 400, 600, JText::_('Configuration'));
		JToolBarHelper::custom('articles.post_face', 'checkin.png', 'checkin.png', 'POST', true);
	}
}
