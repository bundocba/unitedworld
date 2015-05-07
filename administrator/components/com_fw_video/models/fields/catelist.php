<?php
defined('JPATH_BASE') or die;
JFormHelper::loadFieldClass('list');
class JFormFieldCatelist extends JFormFieldList{
	public $type = 'Catelist';
	
	protected function getOptions(){
		$db = JFactory::getDBO();
		$_cu_id=JRequest::getVar("id",0);
		$_cu_view=JRequest::getVar("view","");
		if($_cu_id!=0&&$_cu_id!=""&&$_cu_view=="categoryedit"){
			$_ig_id=fw_videoHelper::createIgnoreId($_cu_id,'#__fw_category');
			$_ig_id=explode("-", $_ig_id);
			
			$sql=" WHERE ( `id`!='".$_cu_id."' ";
			for($i=0;$i<(count($_ig_id)-1);$i++){
				if($_ig_id[$i]!=""){
					$sql.=" AND `id`!='".$_ig_id[$i]."' ";
				}
			}
			$sql.=" ) ";
		}
			
		$query="select * from #__fw_category ".$sql." order by ordering ASC ";
		$db->setQuery($query);
		$rows=$db->loadObjectList();

		$children = array();
		if(count($rows)>0){
			foreach ($rows as $v ){
				$pt = $v->parent_id;
				$list = @$children[$pt] ? $children[$pt] : array();
				array_push( $list, $v );
				$children[$pt] = $list;
			}
			$rows = fw_videoHelper::treeRecurse(intval($rows[0]->parent_id), '', array(), $children, 9999, 0, 0);
		}
		
		$options = array();
		if($_cu_view!="edit"){
			$options[] = JHtml::_('select.option', 0, 'Root category');
		}else{
			$options[] = JHtml::_('select.option', '', 'Select category');
		}
		if($rows){
			foreach($rows as $row){
				$options[] = JHtml::_('select.option', $row->id, $row->name);
			}
		}
		$options = array_merge(parent::getOptions(), $options);
		return $options;
	}
}
