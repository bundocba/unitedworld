<?php
defined('_JEXEC') or die;

jimport('joomla.application.component.controlleradmin');

class Fw_twpostControllerArticles extends JControllerAdmin
{

	public function __construct($config = array())
	{
		parent::__construct($config);
	}

	function post_tweet(){
		$app			 = JFactory::getApplication();
		$cid			 = JRequest::getVar('cid', array(), '', 'array');
		$cid 			 = implode(',',$cid);
		$app->redirect(JURI::root().'index.php?option=com_fw_twpost&view=post&format=raw&id='.$cid);
	}
	
	public function getModel($name = 'Article', $prefix = 'Fw_twpostModel', $config = array('ignore_request' => true))
	{
		$model = parent::getModel($name, $prefix, $config);

		return $model;
	}
}
