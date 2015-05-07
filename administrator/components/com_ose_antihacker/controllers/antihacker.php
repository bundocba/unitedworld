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
jimport('joomla.application.component.controller');
class ose_antihackerControllerantihacker extends ose_antihackerController {
	function __construct() {
		parent :: __construct();
	}
	function addips() {
		$ipmanager= oseRegistry :: call('ipmanager');
		$acl_id = $ipmanager->addIps();
		$antihacker= oseRegistry :: call('antihacker');
		if (!empty($acl_id))
		{
			$result= $antihacker->logAttack($acl_id, '', '100') ;
			if ($result ==true)
			{
				$ipmanager -> ajaxResponse('Done', JText::_("IP rules are added successfully."), true);
			}
			else
			{
				$ipmanager -> ajaxResponse('ERROR', JText::_("Failed inserting the IP rules."), false);
			}
		}
		else
		{
			$ipmanager -> ajaxResponse('ERROR', JText::_("Failed inserting the IP rules."), false);
		}
	}

	function saveConfiguration(){
		$systemSetting = oseRegistry :: call('antihacker')->getSysSetting();
		$systemSetting ->saveConfiguration();
	}

	function getConfiguration(){
		$systemSetting = oseRegistry :: call('antihacker')->getSysSetting();
		$return['result'] = $systemSetting->getConfiguration();
		$return['total'] =1;
		$return['status'] = "Done";
		echo $this->oseJSON->encode($return); exit;
	}
	function getActivationConfig(){

		$sysguard= oseRegistry :: call('sysguard');
		$return['result'] = $sysguard->getBasicInfo();
		$return['total'] =1;
		$return['id'] = "1";
		echo $this->oseJSON->encode($return); exit;
	}
	function createHTPass()
	{
		$sysguard= oseRegistry :: call('sysguard');
		$sysguard->createHTPass();
	}
	function activateAntihacker()
	{
		$sysguard= oseRegistry :: call('sysguard');
		$sysguard->activateAntiHackerTest();
	}
	function addwhitelistkey()
	{
		$result = OSESoftHelper :: checkAdminAccess();
		if (empty($result))
		{
			OSESoftHelper :: ajaxResponse('ERROR', JText :: _('ADMIN_ONLY'));
		}
		$model= $this->getModel('antihacker');
		$key = JRequest::getVar('key');
		if (empty($key))
		{
			OSESoftHelper :: ajaxResponse('ERROR', JText :: _('KEY_CANNOT_BE_EMPTY'));
		}
		$result= array();
		if($model->addwhitelsitkey($key))
		{
			OSESoftHelper :: returnMessages(true, JText :: _('KEY_ADDED_SUCCESS'));
		}
		else
		{
			OSESoftHelper :: returnMessages(false, JText :: _('KEY_ADDED_FAILED'));
		}
	}
	function removeWhitelistKeys()
	{
		$result = OSESoftHelper :: checkAdminAccess();
		if (empty($result))
		{
			OSESoftHelper :: ajaxResponse('ERROR', JText :: _('ADMIN_ONLY'));
		}
		$model= $this->getModel('antihacker');
		$oseJSON = new oseJSON();
		$ids = JRequest::getVar('ids');
		$ids = $oseJSON -> decode($ids);
	 	if (empty($ids))
		{
			OSESoftHelper :: ajaxResponse('ERROR', JText :: _('PLEASE_SELECT_ONEKEY'));
		}
		$result= array();
		if($model->removeWhitelistKeys($ids))
		{
			OSESoftHelper :: returnMessages(true, JText :: _('KEY_ADDED_SUCCESS'));
		}
		else
		{
			OSESoftHelper :: returnMessages(false, JText :: _('KEY_ADDED_FAILED'));
		}
	}
	function getWhitelistKeys()
	{
		$model= $this->getModel('antihacker');
		$items= $model->getWhitelistKeys();
		$result= oseJSON :: encode($items);
		oseExit($result);
	}
}