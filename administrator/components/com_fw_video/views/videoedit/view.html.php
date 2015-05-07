<?php defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.view');
class fw_videoViewvideoEdit extends JView{
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
		$document->setTitle($isNew ? 'Create Video' : 'Edit Video');
		$document->addScript(JURI::root() . $this->script);
		$document->addScript(JURI::root() . "/administrator/components/com_fw_video/views/videoedit/submitbutton.js");
		JToolBarHelper::title($isNew ? 'Create Video' : 'Edit Video');

		$canDo = fw_videoHelper::getActions( 'com_fw_video.video.' . $this->item->id);		
		if($isNew){
			if ($canDo->get('core.create')){
				JToolBarHelper::apply('videoedit.apply', 'JTOOLBAR_APPLY');
				JToolBarHelper::save('videoedit.save', 'JTOOLBAR_SAVE');
				JToolBarHelper::custom('videoedit.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
			}
			JToolBarHelper::cancel('videoedit.cancel', 'JTOOLBAR_CANCEL');
			
		}else{			
			if ($canDo->get('core.edit')){
				JToolBarHelper::apply('videoedit.apply', 'JTOOLBAR_APPLY');
				JToolBarHelper::save('videoedit.save', 'JTOOLBAR_SAVE');
				if ($canDo->get('core.create')){
					JToolBarHelper::custom('videoedit.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
				}
			}
			JToolBarHelper::cancel('videoedit.cancel', 'JTOOLBAR_CLOSE');
		}
	}
	protected function setDocument(){
		$isNew = ($this->item->id < 1);
	}
}
