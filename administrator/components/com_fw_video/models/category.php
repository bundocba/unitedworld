<?php defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.modellist');
class fw_videoModelcategory extends JModelList{
	function &getRows(){
		$db 				= JFactory::getDBO();
		$app 				= JFactory::getApplication();
		$filter_order 		= $app->getUserStateFromRequest('com_fw_video.category.filter_order','filter_order','name','cmd');
		$filter_order_Dir 	= $app->getUserStateFromRequest('com_fw_video.category.filter_order_Dir','filter_order_Dir','ASC','cmd');
		
		
		
		$published			= $app->getUserStateFromRequest('com_fw_video.category.published','published','','cmd');
		$search				= $app->getUserStateFromRequest('com_fw_video.category.search','search','','cmd');
		$cateid				= $app->getUserStateFromRequest('com_fw_video.category.cateid','cateid','','cmd');

		$query = $db->getQuery(true);
		$query->select('*');
		$query->from('`#__fw_category`');
		
		
		
		if($published!=''){
			$query->where('`published`='.$published);
		}
		if($search!=""){
			
			$query->where(" `desc` LIKE '%".$search."%' ");
		}
		if($cateid!=""){
			$query->where('`parent_id`='.$cateid);
		}
		$query->order('`'.$filter_order.'` '.$filter_order_Dir);
		$db->setQuery( $query );		
		$rows = $db->loadObjectList();
		
		return $rows;
	}
	function &getItems(){		
		$db 				= JFactory::getDBO();
		$app 				= JFactory::getApplication();
		
		$rows = $this->getRows();
		
		$pageNav = $this->getPagination();

		$children = array();
		if(count($rows)>0){
			foreach ($rows as $v ){
				$pt = $v->parent_id;
				$list = @$children[$pt] ? $children[$pt] : array();
				array_push( $list, $v );
				$children[$pt] = $list;
			}
			$list = fw_videoHelper::treeRecurse(intval($rows[0]->parent_id), '', array(), $children, 9999, 0, 1);
			$list = array_slice( $list, $pageNav->limitstart, $pageNav->limit );
		}
		
		return $list;
	}
	
	function &getPagination(){
		jimport('joomla.html.pagination');
		$db 				= JFactory::getDBO();
		$app 				= JFactory::getApplication();
		$limit				= $app->getUserStateFromRequest('global.list.limit', 'limit', $app->getCfg('list_limit'), 'int');
		$limitstart			= $app->getUserStateFromRequest('com_fw_video.category.limitstart', 'limitstart', 0, 'int');

		$rows = $this->getRows();
		
		$total=count($rows);
		$pageNav = new JPagination( $total, $limitstart, $limit );
		
		return $pageNav;
	}
}