<?php
/**
  * @version     3.0 +
  * @package       Open Source Excellence Security Suite
  * @subpackage    Open Source Excellence CPU
  * @author        Open Source Excellence {@link http://www.opensource-excellence.com}
  * @author        Created on 30-Sep-2010
  * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
  *
  *
  *  This program is free software: you can redistribute it and/or modify
  *  it under the terms of the GNU General Public License as published by
  *  the Free Software Foundation, either version 3 of the License, or
  *  (at your option) any later version.
  *
  *  This program is distributed in the hope that it will be useful,
  *  but WITHOUT ANY WARRANTY; without even the implied warranty of
  *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  *  GNU General Public License for more details.
  *
  *  You should have received a copy of the GNU General Public License
  *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
  *  @Copyright Copyright (C) 2008 - 2010- ... Open Source Excellence
*/
defined('_JEXEC') or die("Direct Access Not Allowed");
// no direct access
defined('_JEXEC') or die(';)');
jimport('joomla.application.component.controller');
jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.path');

class ose_antihackerController extends JController {
	protected $controller= null, $_c= null, $com_name = 'com_ose_antihacker', $oseJSON = null;
	function __construct() {
		$this->com_name = OSESoftHelper::getExtensionName();
		$task = JRequest::getVar('task');
		if ($task !='upgrade')
		{
			$this->hasactivated();
		}
		$this->oseJSON = new oseJSON();
		parent :: __construct();
	}
	function initControl() {
		$this->controller= JRequest :: getWord('controller', null);
		$this->_c= $this->getController();
	}
	function display($cachable = false, $urlparams = false) {
		if(!JRequest :: getWord('view', null)) {
			JRequest :: setVar('view', 'dashboard');
		}
		parent :: display();
	}
	/*
	 *  Initialize the config.
	 */
	function init() {
		$user= JFactory :: getUser();
		if($user->get('gid') != 24 && $user->get('gid') != 25) {
			global $mainframe;
			$mainframe->redirect('index.php', 'You Do Have Access!');
		} else {
			//require_once( OSECF_API.DS.'api.php' );
			//require_once( OSECF_API.DS.'kit.php' );
			//JRequest::setVar('_cfConfig',osecfKit::getConfigParams());
		}
	}
	function executeTask($task) {
		$this->_c->execute($task);
	}
	function getController() {
		$controller= $this->controller;
		if($controller) {
			require_once(OSEATH_B_CONTROLLER.DS.$controller.'.php');
			$class= 'oseathController'.$controller;
			return new $class();
		} else {
			return $this;
		}
	}
	function redirectE() {
		$this->_c->redirect();
	}
	function callback() {
		$errors= JError :: getErrors();
		if(count($errors) > 0) {
			$string= array();
			foreach($errors as $error) {
				$string[]= '<div class="ui-widget">
												<div class="ui-state-error ui-corner-all" style="padding: 0 .7em;">
													<p><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span>
													<strong>Alert:</strong> '.$error->message.'</p>
												</div>
											</div>';
			}
			$html= implode('&amp;', $string);
		} else {
			global $mainframe;
			$msgs= $mainframe->getMessageQueue();
			$html= '<div class="ui-widget">
									<div class="ui-state-highlight ui-corner-all" style="margin-top: 20px; padding: 0 .7em;">
										<p><span class="ui-icon ui-icon-info" style="float: left; margin-right: .3em;"></span>
										<strong>OK!</strong> '.$msgs[0]['message'].'</p>
									</div>
								</div>';
			//$html = null;
		}
		return $html;
	}
	function refererCheck()
	{
		// Referer Control -- Anti CSRF;
		$curURL= str_replace("?".$_SERVER['QUERY_STRING'], "", $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
		if(isset($_SERVER['HTTP_REFERER'])) {
			$referer= $_SERVER['HTTP_REFERER'];
		} else {
			$referer= "";
		}
		$mainframe = &JFactory::getApplication();
		if (empty($referer))
		{
			//$mainframe->redirect("index.php", JText::_("Anti-CSRF Control: HTTP Referer not defined"));
		}
		else
		{
			$curURL=explode("administrator", $curURL);
			$referer = explode("administrator", str_replace(array("http://", "https://"), "", $referer));
			if ($curURL[0]!=$referer[0])
			{
				//$mainframe->redirect("index.php", JText::_("Anti-CSRF Control: HTTP Referer host does not match server host"));
			}
		}
		// Referer Control -- Anti CSRF Ends;
	}
	function getMod()
	{
		$result= array();
		$name= JRequest :: getCmd('name', null);
		$type= JRequest :: getCmd('type', null);
		$mod= JRequest :: getCmd('mod', null);
		if (!empty($name) && !empty($type) && !empty($mod))
		{
			echo '<script type="text/javascript">'."\r\n";
			require_once(JPATH_SITE.DS.'administrator'.DS.'components'.DS.$this->com_name.DS.'modules'.DS.$mod.DS.'ext.'.$type.'.'.$name.'.js');
			echo "\r\n".'</script>';
		}
		exit;
	}
	function hasactivated()
	{
		$db= JFactory::getDBO();
		$query = "SELECT * FROM #__ose_activation WHERE ext = 'oseath'";
		$db->setQuery($query);
		$result = $db->loadObject();
		if (empty($result))
		{
			$view= JRequest::getVar('view');
			if ($view!='oseactivation')
			{
				$mainframe = JFactory::getApplication();
				$mainframe ->redirect('index.php?option=com_ose_antihacker&view=oseactivation');
			}
		}
		elseif ($result->id == base64_decode($result->code))
		{
			return true;
		}
		else
		{
			$view= JRequest::getVar('view');
			if ($view!='oseactivation')
			{
				$mainframe = JFactory::getApplication();
				$mainframe ->redirect('index.php?option=com_ose_antihacker&view=oseactivation');
			}
		}
	}
	function remove($modelName)
	{
		OSESoftHelper :: checkToken();
		$model= $this->getModel($modelName);
		$id = JRequest::getVar('id');
		$result= array();
		if($model->delete($id))
		{
			OSESoftHelper :: returnMessages(true, JText :: _('ITEM_DELETED_SUCCESS'));
		}
		else
		{
			OSESoftHelper :: returnMessages(false, JText :: _('ITEM_DELETED_FAILED'));
		}
	}
	function getOSEItem($modelName, $funcName)
	{
		$model= $this->getModel($modelName);
		$items= $model->$funcName();
		$result= array();
		$result['results']= $items;
		$result['total']= count($items);
		$result= oseJSON :: encode($result);
		oseExit($result);
	}
	function save($modelName)
	{
		$model= $this->getModel($modelName);
		$update= $model->save();
		if($update)
		{
			$result= array();
			$result['success']= true;
			$result['title']= JText :: _('Done');
			$result['content']= JText :: _('DATA_UPDATE_SUCCESS');
		}
		else
		{
			$result['success']= false;
			$result['title']= JText :: _('Error');
			$result['content']= JText :: _('DATA_UPDATE_FAILED');
		}
		$result= oseJSON :: encode($result);
		oseExit($result);
	}
	function upgrade()
	{
		$model=$this->getModel('upgrade');
		$result = $model ->upgrade();
		if ($result==true)
		{
			$msg ="System upgraded to version 5 successfully!";
		}
		else
		{
			$msg ="There is an error updating the system.";
		}
		$mainframe = JFactory::getApplication(); 
		$mainframe ->redirect('index.php', $msg); 
	}
} // class