<?php defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.controllerform');
class fw_videoControllercategoryEdit extends JControllerForm{
	public function __construct($config = array()){
		parent::__construct($config);
		$this->view_list = 'category';
	}
	public function save(){
		$db = JFactory::getDBO();
		$id = JRequest::getVar('id', 0);
		$post = JRequest::get('post');

		$return = 'index.php?option=com_fw_video&task=categoryedit.edit&id='.$id;

		if(Jfolder::exists(JPATH_ROOT.DS.'images'.DS.'fw_video')==false){
			Jfolder::create(JPATH_ROOT.DS.'images'.DS.'fw_video'.DS);
			Jfile::copy(JPATH_ROOT.DS.'images'.DS.'index.html',JPATH_ROOT.DS.'images'.DS.'fw_video'.DS.'index.html');
		}
	
		$model = $this->getModel();
		$item = $model->getItem($id);
		$item_pa = $model->getItem($_POST['jform']['parent_id']);

		

		if($id==''||$id==0){			
			$_POST['jform']['created'] = date("Y-m-d H:i:s");
			$_POST['jform']['ordering'] = $this->saveCheckParent();
		}else{
			
			
			if($item->parent_id!=$_POST['jform']['parent_id']){	
				$_POST['jform']['ordering'] = $this->saveCheckParent();
			}
		}
	
		
		
		
		return parent::save();
	}
	public function saveCheckParent(){
		$db = JFactory::getDBO();
		
		if($_POST['jform']['parent_id']==0||$_POST['jform']['parent_id']==''){
			$query="select max(ordering) from #__fw_category ";
		}else{
			$query="select max(ordering) from #__fw_category WHERE `parent_id`='".$_POST['jform']['parent_id']."' ";
		}
		$db->setQuery($query);
		$ordering=$db->loadResult();
		$ordering=$ordering+1;
		
		return $ordering;
	}
}