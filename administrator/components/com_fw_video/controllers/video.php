<?php defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.controlleradmin');
class fw_videoControllervideo extends JControllerAdmin{
	function display($cachable = false){
		JRequest::setVar('view', 'video');
		parent::display($cachable);
	}
	public function getModel($name = 'videoedit', $prefix = 'fw_videoModel'){
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));
		return $model;
	}
	public function saveorderup(){
		$db = JFactory::getDBO();
		$return = 'index.php?option=com_fw_video&view=video';
		
		$cu_change=$_POST['cid'][0];
		$query="select cateid from #__fw_video WHERE `id`='".$cu_change."' ";
		$db->setQuery($query);
		$cateid=$db->loadResult();
		
		if($cateid==""){
			$cateid=0;
		}

		$query="select id,ordering from #__fw_video WHERE `cateid`='".$cateid."' ORDER BY ordering DESC ";
		$db->setQuery($query);
		$items=$db->loadObjectList();

		for($i=0;$i<count($items);$i++){
			if($items[$i]->id==$cu_change){
				$next_change=$items[$i+1]->id;
				
				$query = "UPDATE #__fw_video SET `ordering`='".($items[$i+1]->ordering)."' WHERE id=".(int)$cu_change;
				$db->setQuery( $query );
				$db->query();
				
				$query = "UPDATE #__fw_video SET `ordering`='".(($items[$i+1]->ordering)+1)."' WHERE id=".(int)$next_change;
				$db->setQuery( $query );
				$db->query();
				break;
			}
		}
		

		$this->setRedirect( $return, "Ordering successfully saved." );
		return true;
	}
	public function saveorderdown(){
		$db = JFactory::getDBO();
		$return = 'index.php?option=com_fw_video&view=video';
		
		$cu_change=$_POST['cid'][0];
		$query="select cateid from #__fw_video WHERE `id`='".$cu_change."' ";
		$db->setQuery($query);
		$cateid=$db->loadResult();
		
		if($cateid==""){
			$cateid=0;
		}
		
		$query="select id,ordering from #__fw_video WHERE `cateid`='".$cateid."' ORDER BY ordering ASC ";
		$db->setQuery($query);
		$items=$db->loadObjectList();

		for($i=0;$i<count($items);$i++){
			if($items[$i]->id==$cu_change){
				$next_change=$items[$i+1]->id;
				
				$query = "UPDATE #__fw_video SET `ordering`='".($items[$i+1]->ordering)."' WHERE id=".(int)$cu_change;
				$db->setQuery( $query );
				$db->query();
				
				$query = "UPDATE #__fw_video SET `ordering`='".(($items[$i+1]->ordering)-1)."' WHERE id=".(int)$next_change;
				$db->setQuery( $query );
				$db->query();
				break;
			}
		}
		

		$this->setRedirect( $return, "Ordering successfully saved." );
		return true;
	}
	public function saveorder(){
		$db = JFactory::getDBO();
		$order 	= JRequest::getVar( 'order', array(), 'post', 'array' );
		$arr_id = $_POST["cid"];
		$return = 'index.php?option=com_fw_video&view=video';
		$i=0;
		if(is_array($arr_id)){
			foreach ($arr_id as $id){
				$query = 'UPDATE #__fw_video SET `ordering`='.$order[$i].' WHERE id='.(int)$id;
				$db->setQuery( $query );
				$db->query();
				$i++;
			}
		}
		
		$this->setRedirect( $return, "Ordering successfully saved." );
		return true;
	}
	public function delete(){
		$db = JFactory::getDBO();
		$arr_id=$_POST["cid"];

		for($i=0;$i<count($arr_id);$i++){
			
			$query="select * from #__fw_video WHERE `id`='".$arr_id[$i]."' ";
			$db->setQuery($query);
			$item=$db->loadObject();
			
			if($item->source!=""){
				JFile::delete(JPATH_SITE.DS."images".DS."fw_video".DS.$item->source);
			}
			
		}
		return parent::delete();
	}
}