<?php defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.view');
class fw_videoViewcategoryEdit extends JView{
	public function display($tpl = null){	
		$form = $this->get('Form');
		$item = $this->get('Item');
		$script = $this->get('Script');

		if (count($errors = $this->get('Errors'))){
			JError::raiseError(500, implode('<br />', $errors));
			return false;
		}

		$this->form = $form;
		$this->item = $item;
		$this->script = $script;

		$this->addToolBar();
		parent::display($tpl);
	}
	protected function addToolBar(){
		JRequest::setVar('hidemainmenu', true);
		
		$user = JFactory::getUser();
		$userId = $user->id;
		$isNew = ($this->item->id == 0);
		
		$document = JFactory::getDocument();
		$document->setTitle($isNew ? 'Create Category' : 'Edit Category');
		$document->addScript(JURI::root() . $this->script);
		$document->addScript(JURI::root() . "/administrator/components/com_fw_video/views/categoryedit/submitbutton.js");
		JToolBarHelper::title($isNew ? 'Create Category' : 'Edit Category');

		$canDo = fw_videoHelper::getActions( 'com_fw_video.category.' . $this->item->id);		
		if($isNew){
			if ($canDo->get('core.create')){
				JToolBarHelper::apply('categoryedit.apply', 'JTOOLBAR_APPLY');
				JToolBarHelper::save('categoryedit.save', 'JTOOLBAR_SAVE');
				JToolBarHelper::custom('categoryedit.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
			}
			JToolBarHelper::cancel('categoryedit.cancel', 'JTOOLBAR_CANCEL');
			
		}else{			
			if ($canDo->get('core.edit')){
				JToolBarHelper::apply('categoryedit.apply', 'JTOOLBAR_APPLY');
				JToolBarHelper::save('categoryedit.save', 'JTOOLBAR_SAVE');
				if ($canDo->get('core.create')){
					JToolBarHelper::custom('categoryedit.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
				}
			}
			JToolBarHelper::cancel('categoryedit.cancel', 'JTOOLBAR_CLOSE');
		}
	}
	protected function setDocument(){
		$isNew = ($this->item->id < 1);
	}
}
