<?php defined('_JEXEC') or die('Restricted access');
jimport('joomla.database.table');
class fw_videoTablecategory extends JTable{
	function __construct(&$db){
		parent::__construct('#__fw_category', 'id', $db);
	}
	public function bind($array, $ignore = ''){
		if (isset($array['params']) && is_array($array['params'])){
			$parameter = new JRegistry;
			$parameter->loadArray($array['params']);
			$array['params'] = (string)$parameter;
		}
		return parent::bind($array, $ignore);
	}
	public function load($pk = null, $reset = true){
		if (parent::load($pk, $reset)){
			$params = new JRegistry;
			$params->loadJSON(@$this->params);
			$this->params = $params;
			return true;
		}else{
			return false;
		}
	}
	protected function _getAssetName(){
		$k = $this->_tbl_key;
		return 'com_fw_video.category.'.(int) $this->$k;
	}
	protected function _getAssetTitle(){
		return $this->greeting;
	}
	protected function _getAssetParentId(){
		$asset = JTable::getInstance('Asset');
		$asset->loadByName('com_fw_video');
		return $asset->id;
	}
}
