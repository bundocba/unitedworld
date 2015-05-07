<?php defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.modellist');
class fw_videoModelvideo extends JModelList{
	protected function getListQuery(){		
		$db 				= JFactory::getDBO();
		$app 				= JFactory::getApplication();
		$filter_order 		= $app->getUserStateFromRequest('com_fw_video.video.filter_order','filter_order','name','cmd');
		$filter_order_Dir 	= $app->getUserStateFromRequest('com_fw_video.video.filter_order_Dir','filter_order_Dir','ASC','cmd');
		
		
		
		$published			= $app->getUserStateFromRequest('com_fw_video.video.published','published','','cmd');
		$search				= $app->getUserStateFromRequest('com_fw_video.video.search','search','','cmd');
		$cateid				= $app->getUserStateFromRequest('com_fw_video.video.cateid','cateid','','cmd');

		$query = $db->getQuery(true);
		$query->select('*');
		$query->from('`#__fw_video`');
		
		
		
		if($published!=''){
			$query->where('`published`='.$published);
		}
		if($cateid!=''){
			$query->where('`cateid`='.$cateid);
		}
		if($search!=""){
			
			$query->where(" `name` LIKE '%".$search."%'  OR `link` LIKE '%".$search."%'  OR `desctiption` LIKE '%".$search."%' ");
		}

		if($filter_order=='ordering'){
			$query->order('`cateid` '.$filter_order_Dir.', ordering ASC ');
		}else{
			$query->order('`'.$filter_order.'` '.$filter_order_Dir);
		}
		return $query;
	}
}