<?php defined('_JEXEC') or die;
abstract class fw_videoHelper{
	public static function addSubmenu($submenu){
		
		JSubMenuHelper::addEntry("Category", "index.php?option=com_fw_video&view=category", $submenu == "category");
		JSubMenuHelper::addEntry("Video", "index.php?option=com_fw_video&view=video", $submenu == "video");
	}
	
	public static function treerecurse($id, $indent, $list, &$children, $maxlevel = 9999, $level = 0, $type = 1)
	{
		if (@$children[$id] && $level <= $maxlevel){
			foreach ($children[$id] as $v){
				$id = $v->id;
				if ($type){
					$pre = '<sup>|_</sup>&#160;';
					$spacer = '&#160;&#160;&#160;&#160;&#160;&#160;';
				}else{
					$pre = '- ';
					$spacer = '&#160;&#160;';
				}
				if ($v->parent_id == 0){
					$txt = $v->name;
				}else{
					$txt = $pre . $v->name;
				}
				$pt = $v->parent_id;
				$list[$id] = $v;
				$list[$id]->name = $indent.$txt;
				$list[$id]->children = count(@$children[$id]);
				$list = fw_videoHelper::treeRecurse($id, $indent . $spacer, $list, $children, $maxlevel, $level + 1, $type);
			}
		}
		return $list;
	}
	public static function createIgnoreId($id,$table){
		$db		=& JFactory::getDBO();
		$sql="select * from ".$table." `parent_id`='".$id."' order by `".$table."`.`ordering` ASC";
		$db->setQuery($sql);
		$rows=$db->loadObjectList();
		if(count($rows)<=0){
			return;
		}else{
			for($i=0;$i<count($rows);$i++){
				$row=$rows[$i];
				$list.=$row->id.'-'.fw_videoHelper::createIgnoreId($row->id,$table);
			}
			return $list;
		}
	}
	public static function createAddonChild($id,$table){
		$db		=& JFactory::getDBO();
		$sql="select * from ".$table." where `parent_id`='".$id."' order by `".$table."`.`ordering` ASC";
		$db->setQuery($sql);
		$rows=$db->loadObjectList();
		if(count($rows)<=0){
			return;
		}else{
			for($i=0;$i<count($rows);$i++){
				$row=$rows[$i];
				$list.=$row->id.'-'.fw_videoHelper::createIgnoreId($row->id,$table);
			}
			return $list;
		}
	}
	
	public static function getActions( $assetName = 'com_fw_video' ){
		$user	= JFactory::getUser();
		$result	= new JObject;
		$actions = array(
			'core.admin', 'core.manage', 'core.create', 'core.edit', 'core.delete'
		);
		foreach ($actions as $action) {
			$result->set($action, $user->authorise($action, $assetName));
		}
		return $result;
	}
}