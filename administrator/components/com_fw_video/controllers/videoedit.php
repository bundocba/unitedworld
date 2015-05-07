<?php defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.controllerform');
class fw_videoControllervideoEdit extends JControllerForm{
	public function __construct($config = array()){
		parent::__construct($config);		
		$this->view_list = 'video';
	}
	public function save(){
		$db = JFactory::getDBO();
		$id = JRequest::getVar('id', 0);
		$post = JRequest::get('post');

		$return = 'index.php?option=com_fw_video&task=videoedit.edit&id='.$id;

		if(Jfolder::exists(JPATH_ROOT.DS.'images'.DS.'fw_video')==false){
			Jfolder::create(JPATH_ROOT.DS.'images'.DS.'fw_video'.DS);
			Jfile::copy(JPATH_ROOT.DS.'images'.DS.'index.html',JPATH_ROOT.DS.'images'.DS.'fw_video'.DS.'index.html');
		}
		
		$model = $this->getModel();
		$item = $model->getItem($id);
		
		
		$file 				= JRequest::get( "FILES" );
		$file 				= $file["jform"];
		$filename_0 	= "";
		$filekey 			= "source";
		$uploadFile = array();
		if (!empty($file["tmp_name"][$filekey])) {
			foreach ($file as $key => $value) {
				$uploadFile[$key] = $value[$filekey];	
			}

			$filename_0	= uniqid().".".JFILE::getExt($uploadFile["name"]);
			$fileDestination 	= JPATH_SITE.DS."images".DS."fw_video".DS.$filename_0;
			$uploaded 			= JFile::upload($uploadFile["tmp_name"], $fileDestination);
			if (!$uploaded) {
				$this->setRedirect( $return, "Cannot upload file!" );
				return false;
			}

			if($item->source!=""){
				JFile::delete(JPATH_SITE.DS."images".DS."fw_video".DS.$item->source);
			}
		}
			

		if($id==''||$id==0){
			$_POST['jform']['ordering']=$this->saveCheckCate();				
			$_POST['jform']['created'] = date("Y-m-d H:i:s");
		}else{
			
			if($_POST["jform"]["delete_source"]==1){
				$filename_0="";
				JFile::delete(JPATH_SITE.DS."images".DS."fw_video".DS.$item->source);
			}else if($filename_0==""){
				$filename_0=$item->source;
			}
			
			
			if($item->cateid!=$_POST['jform']['cateid']){
				$_POST['jform']['ordering']=$this->saveCheckCate();
			}
		}
		
		
		
		$_POST["jform"]["source"]			= $filename_0;
			
		
		return parent::save();
	}
	public function saveCheckCate(){
		$db = JFactory::getDBO();
		
		$query="select max(ordering) from #__fw_video WHERE `cateid`='".$_POST['jform']['cateid']."'";
		$db->setQuery($query);
		$ordering=$db->loadResult();
		$ordering=$ordering+1;
		
		return $ordering;
	}	
}