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
defined('_JEXEC') or die(';)');
jimport('joomla.application.component.controller');
jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.path');

class ose_antihackerControllerreport extends ose_antihackerController {
	protected $controller= null, $_c= null;
	function __construct() {
		parent :: __construct();
	}
	function getACLlist($status)
	{
		$ipmanager= oseRegistry :: call('ipmanager');
		$return['results'] = $ipmanager->getList($status);
		$return['results'] = $ipmanager->transformValue($return['results']);
		$return['total'] = $ipmanager->total;
		echo $this->oseJSON->encode($return); exit;
	}
	function getDupList()
	{
		$ipmanager= oseRegistry :: call('ipmanager');
		$return['results'] = $ipmanager->getDupList();
		//$return['results'] = $ipmanager->transformValue($return['results']);
		$return['total'] = $ipmanager->total;
		echo $this->oseJSON->encode($return); exit;
	}
	function getDupACL()
	{
		$ipmanager= oseRegistry :: call('ipmanager');
		$return['results'] = $ipmanager->getDupACL();
		echo $this->oseJSON->encode($return); exit;
	}
	function removeDup()
	{
		$ipmanager= oseRegistry :: call('ipmanager');
		$ipmanager->removeDup();
	}
	function updateACLlist($status)
	{
		$aclids= JRequest :: getVar('ids');
		$ipmanager= oseRegistry :: call('ipmanager');
		$data['status'] = $status; 
		$ipmanager->updateACL($aclids, $data);
	}
	function getIPlist()
	{
		$status = JRequest::getVar('status', '1'); 
		$status = ($status=='')?'1':$status; 
		self::getACLlist($status);
	}
	function removeACL()
	{
		$aclids= JRequest :: getVar('ids');
		$ipmanager= oseRegistry :: call('ipmanager');
		$ipmanager->deleteACL($aclids);
	}
	function whitelistBlacklisted()
	{
		self::updateACLlist(3);
	}
	function whitelistMonitored()
	{
		self::updateACLlist(3);
	}
	function blacklistWhitelisted()
	{
		self::updateACLlist(1);
	}
	function blacklistMonitored()
	{
		self::updateACLlist(1);
	}
	function monitorBlacklisted()
	{
		self::updateACLlist(2);
	}
	function monitorWhitelisted()
	{
		self::updateACLlist(2);
	}
	function viewAttack()
	{
		$antihacker= oseRegistry :: call('antihacker');
		$stat= $antihacker->getStat();
		$stat->viewAttack();
	}
	function viewAttackDetail()
	{
		$antihacker= oseRegistry :: call('antihacker');
		$stat= $antihacker->getStat();
		$stat->viewAttackDetail();
	}
	function getlayer1AttackList()
	{
		$antihacker= oseRegistry :: call('antihacker');
		$stat= $antihacker->getStat();
		$return['results'] = $stat->getAttackList("l1");
		$return['total'] = $stat->total;
		echo $this->oseJSON->encode($return); exit;
	}
	function getlayer2AttackList()
	{
		$antihacker= oseRegistry :: call('antihacker');
		$stat= $antihacker->getStat();
		$return['results'] = $stat->getAttackList("l2");
		$return['total'] = $stat->total;
		echo $this->oseJSON->encode($return); exit;
	}
	function blSignature()
	{
		self::updateSignature("1","l1");
	}
	function wlSignature()
	{
		self::updateSignature("2","l1");
	}
	function blKey()
	{
		self::updateSignature("1","l2");
	}
	function wlKey()
	{
		self::updateSignature("3","l2");
	}
	function ftKey()
	{
		self::updateSignature("2","l2");
	}
	function updateSignature($status, $layer)
	{
		$antihacker= oseRegistry :: call('antihacker');
		$stat= $antihacker->getStat();
		$stat->updateSignature($status, $layer);
	}
	function blTargetlayer1()
	{
		self::updateTarget('1', 'l1');
	}
	function wlTargetlayer1()
	{
		self::updateTarget('3', 'l1');
	}
	function blTargetlayer2()
	{
		self::updateTarget('1', 'l2');
	}
	function wlTargetlayer2()
	{
		self::updateTarget('3', 'l2');
	}
	function ftTargetlayer2()
	{
		self::updateTarget('2', 'l2');
	}
	function ftTargetlayer4()
	{
		self::updateTarget('4', 'l2');
	}
	function updateTarget($status, $layer)
	{
		$antihacker= oseRegistry :: call('antihacker');
		$stat= $antihacker->getStat();
		$stat->updateTargetBackend($status, $layer);
	}
	function getBlacklistedSummary()
	{
		$antihacker= oseRegistry :: call('antihacker');
		$stat= $antihacker->getStat();
		$stat->getBlacklistedSummary();
	}
	function removeL1rules()
	{
		self::removeRules('l1');
	}
	function removeL2rules()
	{
		self::removeRules('l2');
	}
	function removeRules($layer)
	{
		$antihacker= oseRegistry :: call('antihacker');
		$stat= $antihacker->getStat();
		$stat->removeRules($layer);
	}
	function loadjoomlaruleset()
	{
		$model= $this->getModel('loadrulesets');
		if($model->loadJoomlaRuleset())
		{
			OSESoftHelper :: returnMessages(true, JText :: _('ITEM_DELETED_SUCCESS'));
		}
		else
		{
			OSESoftHelper :: returnMessages(false, JText :: _('ITEM_DELETED_FAILED'));
		}
	}
	function loadwpruleset()
	{
		$model= $this->getModel('loadrulesets');
		if($model->loadwpruleset())
		{
			OSESoftHelper :: returnMessages(true, JText :: _('ITEM_DELETED_SUCCESS'));
		}
		else
		{
			OSESoftHelper :: returnMessages(false, JText :: _('ITEM_DELETED_FAILED'));
		}
	}
}
?>