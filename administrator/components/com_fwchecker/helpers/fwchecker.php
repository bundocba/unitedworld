<?php defined('_JEXEC') or die;
abstract class fwcheckerHelper{
	public static function addSubmenu($submenu){
	}
	public static function getActions( $assetName = 'com_fwchecker' ){
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