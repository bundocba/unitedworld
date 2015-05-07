<?php defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.modeladmin');
class fw_videoModelvideoEdit extends JModelAdmin{
	public function getItem($pk = null)
	{
		$pk = (!empty($pk)) ? $pk : (int) $this->getState($this->getName() . '.id');
		$table = $this->getTable();

		if ($pk > 0)
		{
			$return = $table->load($pk);
			if ($return === false && $table->getError())
			{
				$this->setError($table->getError());
				return false;
			}
		}

		$properties = $table->getProperties(1);
		$item = JArrayHelper::toObject($properties, 'JObject');

		if (property_exists($item, 'params'))
		{
			$registry = new JRegistry;
			$registry->loadString($item->params);
			$item->params = $registry->toArray();
		}
		
		
				
		return $item;
	}
	protected function allowEdit($data = array(), $key = 'id'){
		return JFactory::getUser()->authorise('core.edit',
			'com_fw_video.video.'.((int) isset($data[$key]) ? $data[$key] : 0)) or parent::allowEdit($data, $key);
	}
	public function getTable($type = 'video', $prefix = 'fw_videoTable', $config = array()){
		return JTable::getInstance($type, $prefix, $config);
	}
	public function getForm($data = array(), $loadData = true){
		$form = $this->loadForm('com_fw_video.video',
								'videoedit',
								array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form)){
			return false;
		}
		return $form;
	}
	protected function loadFormData(){
		$data = JFactory::getApplication()->getUserState('com_fw_video.edit.video.data', array());

		if (empty($data)){
			$data = $this->getItem();
		}
		
		return $data;
	}
	public function getScript(){
	}
}
