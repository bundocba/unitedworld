<?php
/**
 * @package     LOGman
 * @copyright   Copyright (C) 2012 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

defined( '_JEXEC' ) or die;

jimport( 'joomla.plugin.plugin' );

class plgSystemLogman extends JPlugin
{
	/**
	 * A JUser instance to store values in delete events
	 */
	protected $_user = null;
	
	protected $_component_params;
	
	public function __construct(&$subject, $config = array())
	{
	    parent::__construct($subject, $config);
	    
	    $this->_component_params = JComponentHelper::getParams('com_logman');
	}

	/**
	 * Overridden to only run if we have Nooku framework installed
	 */
	public function update(&$args)
	{
		$return = null;

		if (class_exists('Koowa')) {
			$return = parent::update($args);
		}

		return $return;
	}

	/**
	 * Log requests to com_files
	 */
	function curPageURL() {
		$pageURL = 'http';
//		if ($_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
		$pageURL .= "://";
		if ($_SERVER["SERVER_PORT"] != "80") {
			$pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
		} else {
			$pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
		}
		return $pageURL;
	}
	public function onAfterRoute()
	{
		$db=& JFactory::getDBO();
		$config =& JFactory::getConfig();
		
//		$this->saveLogIker('admin','com','URL','','','','','go to '.$this->curPageURL().'');

/*BEGIN-MODULE************************************************************************/
		if($_GET['option']=='com_modules'&&count($_POST['cid'])>0&&$_POST['task']=='modules.delete'){
			//DELETE
			for($i=0;$i<count($_POST['cid']);$i++){
				$query="SELECT `title` FROM #__modules WHERE id = '".$_POST['cid'][$i]."'";
				$db->setQuery($query);
				$title=$db->loadResult();

				$this->saveLogIker('admin','com','modules','module','delete',$_POST['cid'][$i],$title,'deleted');	
			}
		}else if($_GET['option']=='com_modules'&&count($_POST['cid'])>0&&$_POST['task']=='modules.trash'){
			//TRASH
			for($i=0;$i<count($_POST['cid']);$i++){
				$query="SELECT `title` FROM #__modules WHERE id = '".$_POST['cid'][$i]."'";
				$db->setQuery($query);
				$title=$db->loadResult();

				$this->saveLogIker('admin','com','modules','module','edit',$_POST['cid'][$i],$title,'trashed');	
			}
		}else if($_GET['option']=='com_modules'&&count($_POST['cid'])>0&&($_POST['task']=='modules.publish'||$_POST['task']=='modules.unpublish')){
			//OTHER
			for($i=0;$i<count($_POST['cid']);$i++){
				$query="SELECT `title` FROM #__modules WHERE id = '".$_POST['cid'][$i]."'";
				$db->setQuery($query);
				$title=$db->loadResult();

				$this->saveLogIker('admin','com','modules','module','edit',$_POST['cid'][$i],$title,'updated');	
			}
		}else if($_GET['option']=='com_modules'&&$_POST['jform']['id']!=""&&$_POST['jform']['id']!=0&&($_POST['task']=='module.apply'||$_POST['task']=='module.save'||$_POST['task']=='module.save2new')){
			//EDIT
			$this->saveLogIker('admin','com','modules','module','edit',$_POST['jform']['id'],$_POST['jform']['title'],'updated');
		}else if($_GET['option']=='com_modules'&&($_POST['jform']['id']==""||$_POST['jform']['id']==0)&&($_POST['task']=='module.apply'||$_POST['task']=='module.save'||$_POST['task']=='module.save2new')){
			//NEW
			$query="SELECT `auto_increment` FROM `INFORMATION_SCHEMA`.`TABLES` WHERE table_name = '".$config->getValue('config.dbprefix')."modules'";
			$db->setQuery($query);
			$max_id=$db->loadResult();
			
			$this->saveLogIker('admin','com','modules','module','add',$max_id,$_POST['jform']['title'],'created');
		}
/*END-MODULE************************************************************************/

/*BEGIN-PLUGIN************************************************************************/
		if($_GET['option']=='com_plugins'&&count($_POST['cid'])>0&&$_POST['task']=='items.delete'){
			//DELETE
			for($i=0;$i<count($_POST['cid']);$i++){
				$query="SELECT `title` FROM #__menu WHERE id = '".$_POST['cid'][$i]."'";
				$db->setQuery($query);
				$title=$db->loadResult();

				$this->saveLogIker('admin','com','menus','item','delete',$_POST['cid'][$i],$title,'deleted');	
			}
		}else if($_GET['option']=='com_plugins'&&count($_POST['cid'])>0&&$_POST['task']=='items.trash'){
			//TRASH
			for($i=0;$i<count($_POST['cid']);$i++){
				$query="SELECT `title` FROM #__menu WHERE id = '".$_POST['cid'][$i]."'";
				$db->setQuery($query);
				$title=$db->loadResult();

				$this->saveLogIker('admin','com','menus','item','edit',$_POST['cid'][$i],$title,'trashed');	
			}
		}else if($_GET['option']=='com_plugins'&&$_GET['extension_id']!=""&&$_GET['extension_id']!=0&&($_POST['task']=='plugin.apply'||$_POST['task']=='plugin.save')){
			//EDIT
			$this->saveLogIker('admin','com','plugins','plugin','edit',$_GET['extension_id'].'&extension_id='.$_GET['extension_id'],$_POST['jform']['name'],'updated');
		}
/*END-PLUGIN************************************************************************/

/*BEGIN-MENU************************************************************************/
		if($_GET['option']=='com_menus'&&count($_POST['cid'])>0&&$_POST['task']=='items.delete'){
			//DELETE
			for($i=0;$i<count($_POST['cid']);$i++){
				$query="SELECT `title` FROM #__menu WHERE id = '".$_POST['cid'][$i]."'";
				$db->setQuery($query);
				$title=$db->loadResult();

				$this->saveLogIker('admin','com','menus','item','delete',$_POST['cid'][$i],$title,'deleted');	
			}
		}else if($_GET['option']=='com_menus'&&count($_POST['cid'])>0&&$_POST['task']=='items.trash'){
			//TRASH
			for($i=0;$i<count($_POST['cid']);$i++){
				$query="SELECT `title` FROM #__menu WHERE id = '".$_POST['cid'][$i]."'";
				$db->setQuery($query);
				$title=$db->loadResult();

				$this->saveLogIker('admin','com','menus','item','edit',$_POST['cid'][$i],$title,'trashed');	
			}
		}else if($_GET['option']=='com_menus'&&count($_POST['cid'])>0&&($_POST['task']=='items.publish'||$_POST['task']=='items.unpublish')){
			//OTHER
			for($i=0;$i<count($_POST['cid']);$i++){
				$query="SELECT `title` FROM #__menu WHERE id = '".$_POST['cid'][$i]."'";
				$db->setQuery($query);
				$title=$db->loadResult();

				$this->saveLogIker('admin','com','menus','item','edit',$_POST['cid'][$i],$title,'updated');	
			}
		}else if($_GET['option']=='com_menus'&&$_POST['jform']['id']!=""&&$_POST['jform']['id']!=0&&($_POST['task']=='item.apply'||$_POST['task']=='item.save'||$_POST['task']=='item.save2new')){
			//EDIT
			$this->saveLogIker('admin','com','menus','item','edit',$_POST['jform']['id'],$_POST['jform']['title'],'updated');
		}else if($_GET['option']=='com_menus'&&($_POST['jform']['id']==""||$_POST['jform']['id']==0)&&($_POST['task']=='item.apply'||$_POST['task']=='item.save'||$_POST['task']=='item.save2new')){
			//NEW
			$query="SELECT `auto_increment` FROM `INFORMATION_SCHEMA`.`TABLES` WHERE table_name = '".$config->getValue('config.dbprefix')."menu'";
			$db->setQuery($query);
			$max_id=$db->loadResult();
			
			$this->saveLogIker('admin','com','menus','item','add',$max_id,$_POST['jform']['title'],'created');
		}
/*END-MENU************************************************************************/

/*BEGIN-TEMPLATE************************************************************************/
		if($_GET['option']=='com_templates'&&count($_POST['cid'])>0&&$_POST['task']=='styles.delete'){
			//DELETE
			for($i=0;$i<count($_POST['cid']);$i++){
				$query="SELECT `title` FROM #__template_styles WHERE id = '".$_POST['cid'][$i]."'";
				$db->setQuery($query);
				$title=$db->loadResult();

				$this->saveLogIker('admin','com','templates','style','delete',$_POST['cid'][$i],$title,'deleted');	
			}
		}else if($_GET['option']=='com_templates'&&$_GET['id']!=""&&$_GET['id']!=0&&($_POST['task']=='style.apply'||$_POST['task']=='style.save'||$_POST['task']=='style.save2new')){
			//EDIT
			$this->saveLogIker('admin','com','templates','style','edit',$_GET['id'],$_POST['jform']['title'],'updated');
		}
/*END-TEMPLATE************************************************************************/
		
		
	    try {
	        $behavior = KService::get('com://admin/logman.controller.behavior.file.loggable', array(
	            'title_column' => 'name'
	        ));
	        $config = array('behaviors' => array($behavior));
	        
	        KService::setConfig('com://admin/files.controller.file'  , $config);
	        KService::setConfig('com://admin/files.controller.folder', $config);
	    }
	    catch (KServiceException $e) {}	
	}
	
	function saveLogIker($application,$type,$package,$name,$action,$row,$title,$status){
		$db=& JFactory::getDBO();
		$user=&JFactory::getUser();
	
		$query="insert into #__activities_activities (
			`uuid`,`application`,`type`,`package`,`name`,`action`,`row`,
			`title`,`status`,`created_on`,`created_by`
		 )  
		 values (
		 	'".rand(0,1000000000)."','".$application."','".$type."','".$package."','".$name."','".$action."','".$row."',
		 	'".$title."','".$status."','".date("Y-m-d H:i:s")."','".$user->get('id')."' 
		  ) ";
		$db->setQuery($query);
		$db->query();
	}

	/**
	 * For Joomla 1.5
	 */
	public function onAfterContentSave($article, $isNew)
	{
		$context = 'com_content.article';
		return $this->onContentAfterSave($context, $article, $isNew);
	}

	/**
	 * For Joomla 1.7 and 2.5
	 */
	public function onContentAfterSave($context, $content, $isNew)
	{
		$config = new KConfig(array(
			'context' => $context,
			'content' => $content,
			'new'     => $isNew,
			'event'   => ($isNew) ? 'after.add' : 'after.edit',
			'action'  => ($isNew) ? 'add' : 'edit',
			'status'  => ($isNew) ? KDatabase::STATUS_CREATED : KDatabase::STATUS_UPDATED,
		));

		return $this->contentEvent($config);
	}

	/**
	 * For Joomla 1.7 and 2.5
	 */
	public function onContentAfterDelete($context, $content)
	{
		$config = new KConfig(array(
			'context' => $context,
			'content' => $content,
			'new'     => false,
			'event'   => 'after.delete',
			'action'  => 'delete',
			'status'  => KDatabase::STATUS_DELETED,
		));

		return $this->contentEvent($config);
	}

	public function contentEvent(KConfig $config)
	{
		$context  = explode('.', $config->context);
		$option   = str_replace('com_', '', array_shift($context));
		$view     = KInflector::singularize(array_shift($context));
		$app      = JFactory::getApplication()->isAdmin() ? 'admin' : 'site';

		$config->append(array(
			'caller' => $this->getService('com://'.$app.'/'.$option.'.controller.'.$view),
			'result' => KService::get('com://admin/'.$option.'.database.row.'.$view),
		));

		$config->result->setData($config->content);

		return $this->saveLog($config);
	}

	/**
	 * For Joomla 1.5
	 */
	public function onLoginUser($user, $options)
	{
		return $this->userLog($user, $options);
	}

	/**
	 * For Joomla 2.5
	 */
	public function onUserLogin($user, $options)
	{
		return $this->userLog($user, $options);
	}

	/**
	 * For Joomla 1.5
	 */
	public function onLogoutUser($user, $options)
	{
		return $this->userLog($user, $options);
	}

	/**
	 * For Joomla 2.5
	 */
	public function onUserLogout($user, $options)
	{
		return $this->userLog($user, $options);
	}

	/**
	 * For Joomla 1.5
	 */
	public function onBeforeDeleteUser($user)
	{
		return $this->onUserBeforeDelete($user);
	}

	/**
	 * For Joomla 2.5
	 */
	public function onUserBeforeDelete($user)
	{
		// Save the user into a static class property
		$this->_user = clone JFactory::getUser($user['username']);
	}

	/**
	 * For Docman
	 */
	public function onAfterEditDocument($data)
	{
		$isNew = ($data['process'] == 'new document');

		$config = new KConfig(array(
			'status'   => ($isNew) ? KDatabase::STATUS_CREATED : KDatabase::STATUS_UPDATED,
			'event'    => ($isNew) ? 'after.add' : 'after.edit',
			'action'   => ($isNew) ? 'add' 	     : 'edit',
			'document' => $data['document'],
		));

		return $this->docmanEvent($config);
	}

	/**
	 * For Joomla 1.5
	 */
	public function onAfterStoreUser($user, $isNew, $success, $msg)
	{
		return $this->onUserAfterSave($user, $isNew, $success, $msg);
	}

	/**
	 * For Joomla 2.5
	 */
	public function onUserAfterSave($user, $isNew, $success, $msg)
	{
		$config = new KConfig(array(
			'user'   => $user,
			'event'  => ($isNew) ? 'after.add' : 'after.edit',
			'action' => ($isNew) ? 'add' 	   : 'edit',
			'status' => ($isNew) ? KDatabase::STATUS_CREATED : KDatabase::STATUS_UPDATED
		));

		return $this->userEvent($config);
	}

	/**
	 * For Joomla 1.5
	 */
	public function onAfterDeleteUser($user, $success = null, $msg = null)
	{
		return $this->onUserAfterDelete($user, $success, $msg);
	}

	/**
	 * For Joomla 2.5
	 */
	public function onUserAfterDelete($user, $success, $msg)
	{
		$config = new KConfig(array(
			'user'   => $this->_user,
			'event'  => 'after.delete',
			'action' => 'delete',
			'status' => KDatabase::STATUS_DELETED,
		));

		return $this->userEvent($config);
	}

	public function userLog($user, $options)
	{
	    if (!$this->_component_params->get('log_login_events')) {
	        return;    
	    }
	    
		$login = !isset($user['id']);

		$config = new KConfig(array(
			'user'   => $this->_user ? $this->_user : $user,
			'event'  => ($login) ? 'after.login' : 'after.logout',
			'action' => ($login) ? 'login' 		 : 'logout',
			'status' => ($login) ? 'logged in' 	 : 'logged out',
		));

		return $this->userEvent($config);
	}

	public function userEvent($config=array())
	{
		$config = new KConfig($config);
		$app    = JFactory::getApplication()->isAdmin() ? 'admin' : 'site';

		$config->append(array(
			'caller'  => $this->getService('com://'.$app.'/users.controller.user'),
			'result'  => KService::get('com://admin/users.database.row.user'),
		));

	 	if (isset($config->user))
	 	{
	 		$user = ($config->user instanceof JUser) ? $config->user : JUser::getInstance($config->user->username);
	 		$config->result->setData($user);
	 		if ($config->event == 'after.login') {
	 			$config->result->created_by = $user->id;
	 		}
	 	}

		return $this->saveLog($config);
	}

	public function docmanEvent($config=array())
	{
		$config = new KConfig($config);
		$app = JFactory::getApplication()->isAdmin() ? 'admin' : 'site';

		$config->append(array(
			'caller'       => $this->getService('com://'.$app.'/docman.controller.document'),
			'result'       => KService::get('com://admin/docman.database.row.document'),
			'title_column' => 'dmname',
		));

		$config->result->setData($config->document);

		return $this->saveLog($config);
	}

	public function saveLog(KConfig $config)
	{
		$context = new KCommandContext();

		$context->action = $config->action;
		$context->caller = $config->caller;
		$context->result = $config->result->setStatus($config->status);

		$options = array('actions' => array($config->event));

		if (!is_null($config->title_column)) {
			$options['title_column'] = $config->title_column;
		}

		KService::get('com://admin/activities.controller.behavior.loggable', $options)->execute($config->event, $context);
	}

	public function getService($identifier)
	{
		$config = new KConfig();

		//Set the service container and identifier
        $config->service_container  = KService::getInstance();
        $config->service_identifier = KService::getIdentifier($identifier);

        return new KObject($config);
	}
}
