<?php
defined('_JEXEC') or die;

jimport('joomla.application.component.controlleradmin');

class Fw_fbpostControllerK2Articles extends JControllerAdmin
{

	public function __construct($config = array())
	{
		parent::__construct($config);
	}

	function post_face(){
		$app			 = JFactory::getApplication();
		$cid			 = JRequest::getVar('cid', array(), '', 'array');
		$cid 			 = implode(',',$cid);
		$app->redirect(JURI::root().'index.php?option=com_fw_fbpost&view=post&format=raw&type=k2&id='.$cid);
	}
	
	public function getModel($name = 'Article', $prefix = 'Fw_fbpostModel', $config = array('ignore_request' => true))
	{
		$model = parent::getModel($name, $prefix, $config);

		return $model;
	}
}
