<?php defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.controlleradmin');
class fw_videoControllercategory extends JControllerAdmin{
	function display($cachable = false){
		JRequest::setVar('view', 'category');
		parent::display($cachable);
	}
	public function getModel($name = 'categoryedit', $prefix = 'fw_videoModel'){
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));
		return $model;
	}
	public function saveorderup(){
		$db = JFactory::getDBO();
		$return = 'index.php?option=com_fw_video&view=category';
		
		$cu_change=$_POST['cid'][0];
		$query="select parent_id from #__fw_category WHERE `id`='".$cu_change."' ";
		$db->setQuery($query);
		$parent_id=$db->loadResult();
		
		if($parent_id==""){
			$parent_id=0;
		}
		
		$query="select id,ordering from #__fw_category WHERE `parent_id`='".$parent_id."' ORDER BY ordering DESC ";
		$db->setQuery($query);
		$items=$db->loadObjectList();

		for($i=0;$i<count($items);$i++){
			if($items[$i]->id==$cu_change){
				$next_change=$items[$i+1]->id;
				
				$query = "UPDATE #__fw_category SET `ordering`='".($items[$i+1]->ordering)."' WHERE id=".(int)$cu_change;
				$db->setQuery( $query );
				$db->query();
				
				$query = "UPDATE #__fw_category SET `ordering`='".(($items[$i+1]->ordering)+1)."' WHERE id=".(int)$next_change;
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
		$return = 'index.php?option=com_fw_video&view=category';
		
		$cu_change=$_POST['cid'][0];
		$query="select parent_id from #__fw_category WHERE `id`='".$cu_change."' ";
		$db->setQuery($query);
		$parent_id=$db->loadResult();
		
		if($parent_id==""){
			$parent_id=0;
		}
		
		$query="select id,ordering from #__fw_category WHERE `parent_id`='".$parent_id."' ORDER BY ordering ASC ";
		$db->setQuery($query);
		$items=$db->loadObjectList();

		for($i=0;$i<count($items);$i++){
			if($items[$i]->id==$cu_change){
				$next_change=$items[$i+1]->id;
				
				$query = "UPDATE #__fw_category SET `ordering`='".($items[$i+1]->ordering)."' WHERE id=".(int)$cu_change;
				$db->setQuery( $query );
				$db->query();
				
				$query = "UPDATE #__fw_category SET `ordering`='".(($items[$i+1]->ordering)-1)."' WHERE id=".(int)$next_change;
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
		$return = 'index.php?option=com_fw_video&view=category';
		$i=0;
		if(is_array($arr_id)){
			foreach ($arr_id as $id){
				$query = 'UPDATE #__fw_category SET `ordering`='.$order[$i].' WHERE id='.(int)$id;
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
		$return = 'index.php?option=com_fw_video&view=category';

		for($i=0;$i<count($arr_id);$i++){
			$query="select * from #__fw_category WHERE `id`='".$arr_id[$i]."' ";
			$db->setQuery($query);
			$item=$db->loadObject();
		
			$query="select parent_id from #__fw_category WHERE `parent_id`='".$arr_id[$i]."' LIMIT 0,1 ";
			$db->setQuery($query);
			$item_check=$db->loadResult();
			
			if($item_check!=""||$item_check!=0){
				$this->setRedirect( $return, "Cannot delete category '".$item->name."', because this category contain sub-category","error");
				return false;
			}
			
			$query="select id from compo_item_main_table_name WHERE `cateid`='".$arr_id[$i]."' LIMIT 0,1 ";
			$db->setQuery($query);
			$item_check=$db->loadResult();
			
			if($item_check!=""||$item_check!=0){
				$this->setRedirect( $return, "Cannot delete category '".$item->name."', because this category contain item","error");
				return false;
			}

			
		}
		return parent::delete();
	}
}