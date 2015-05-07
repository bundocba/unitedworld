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
if (!defined('_JEXEC') && !defined('OSE_ADMINPATH'))
{
	die("Direct Access Not Allowed");
}
$curFolder = dirname(__FILE__);
//require_once($curFolder.OSEDS.'library'.OSEDS.'geoiploc.php');

class oseIpmanager {
	var $total;
	private $db= null;
	private $json= null;
	function __construct() {
		$this->total = 0;
		if (class_exists("athDB"))
		{
			$this->db= athDB :: instance();
		}
		else
		{
			$this->db= oseDB :: instance();
		}
		if (class_exists("athJSON"))
		{
			$this->json= new athJSON();
		}
		else
		{
			$this->json= new oseJSON();
		}
	}
	function checkIDs($ids)
	{
		if (empty($ids))
		{
			self::ajaxResponse("ERROR", "Please select at least one item", false);
		}
	}
	function getList($status) {
		// initialize variables
		$db= $this->db;
		$where= array();
		$where[] = " acl.status = ".(int)$status;
		$where[] = " acl.id = ip.acl_id ";
		$search= JRequest :: getString('search', null);
		if($search) {
			$search= $this->db->Quote('%'.$this->db->getEscaped($search, true).'%', false);
			$where[] = ' acl.name LIKE '.$search. " OR ip.ip LIKE ".$search; 
		}
		$id= JRequest :: getInt('id', null);
		if (!empty($id))
		{
			$where[] = " acl.id = ".(int)$id;
		}
		$where= array_merge($where, $this->generateQueryWhere());
		$where=(count($where) ? ' WHERE ('.implode(') AND (', $where).')' : '');
		$query= " SELECT acl.*, ip.host, ".
 				" (SELECT ip FROM `#__oseipm_iptable` WHERE id = MAX(ip.id)) AS ip_end, ".
				" (SELECT ip FROM `#__oseipm_iptable` WHERE id = MIN(ip.id)) AS ip_start ".
				" FROM `#__oseipm_iptable` AS ip, ".
				" `#__oseipm_acl` AS acl ".
				$where.
				" GROUP BY ip.acl_id";
		$this->total= self::getListTotal($query, "acl.*", "count(acl.id)");
		$start= JRequest :: getInt('start', 0);
		$limit= JRequest :: getInt('limit', 25);
		$db->setQuery($query, $start, $limit);
		$rows= $db->loadAssocList();
		if (!empty($rows))
		{
			foreach($rows as $key => $row) {
				$rows[$key]['name']= empty($row['name']) ? 'acl'.$row['id'] : $row['name'];
				$rows[$key]['score'] = $this->getRickScore($row['id']); 
				if (empty($rows[$key]['country']))
				{
					$rows[$key]['country'] = $this->updateIPCountry($rows[$key]['ip_start'], $rows[$key]['ip_end']);
				}
				if (empty($rows[$key]['host']))
				{
					$rows[$key]['host'] = $this->updateIPHost($rows[$key]['ip_start']);
				}
				$rows[$key]['country'] = "<img src='components/com_ose_antihacker/assets/images/flags/".strtolower($rows[$key]['country']).".png' alt='".$rows[$key]['country']."' />";
			}
		}
		return $rows;
	}
	function iptoCountry($ip)
	{
		$iplong = substr("0000000000" . sprintf('%u',ip2long($ip)), -10);
		$query = " SELECT `country_code` FROM `#__ose_geoip` ".
				" WHERE `ip32_start`<= ".$this->db->Quote($iplong)." and ".$this->db->Quote($iplong)." <= `ip32_end`;";
		$this->db->setQuery($query);
		$country = $this->db->loadResult() ;
		$country  = strtolower($country); 
		return $country;  
	}
	function updateIPCountry($ip_start, $ip_end)
	{
	 	$country = self::iptoCountry($ip_start); 

		$query = " UPDATE `#__oseipm_iptable` SET `country` = ". $this->db->Quote($country, true).
				 " WHERE `ip` >= ". $this->db->Quote($ip_start, true). " AND `ip` <= ". $this->db->Quote($ip_end, true);
		$this->db->setQuery($query);
		$result = $this->db->query() ;
		if ($result == true)
		{
			return $country; 
		}
		else
		{
			return false; 
		}
		
	}
	function updateIPHost($ip_start)
	{
		$host = gethostbyaddr($ip_start);
		$query = " UPDATE `#__oseipm_iptable` SET `host` = ". $this->db->Quote($host, true).
				 " WHERE `ip` = ". $this->db->Quote($ip_start, true);
		$this->db->setQuery($query);
		$result = $this->db->query() ;
		if ($result == true)
		{
			return $host;
		}
		else
		{
			return false;
		}
	}
	function getRickScore($aclid)
	{
		$db= $this->db;
		$query = "SELECT `score` FROM `#__oseath_alerts` WHERE `aclid` = ".(int)$aclid; 
		$db->setQuery($query);
		$result = $db->loadResult() ; 
		if ($result == 0)
		{
			$result = 100; 
		}
		return $result; 
	}
	function transformValue($rows){
		$return = array();
		$i = 0;
		foreach ($rows as $row)
		{
			switch ($row['iptype'])
			{
				case "ip":
				$row['iptype'] = JText::_('IP');
				break;
				case "ips":
				$row['iptype'] = JText::_('IP Range');
				break;
			}
			switch ($row['status'])
			{
				case "0":
				$row['status'] = JText::_('Logged');
				break;
				case "1":
				$row['status'] = JText::_('Blacklisted');
				break;
				case "2":
				$row['status'] = JText::_('Monitored');
				break;
				case "3":
				$row['status'] = JText::_('Whitelisted');
				break;
			}
			$row['view'] = "<a href='#' onClick= 'viewdetail(".urlencode($row['id']).")' ><img src='components/com_ose_antihacker/assets/images/page_white_magnify.png' /></a>";
			$return[$i] = $row;
			$i++;
		}
		return $return;
	}
	function getListTotal($query, $needle, $replace) {
		$db= $this->db;
		$query = str_replace($needle, $replace, $query);
		$db->setQuery($query);
		$list = $db->loadObjectList();
		return count($list);
	}
	function getDupList() {
		// initialize variables
		$db = $this->db;
		$where= array();
		$search= JRequest :: getString('search', null);
		if($search) {
			$where[]= $db->Quote('%'.$db->getEscaped($search, true).'%', false);
		}
		$where[]= "a.ip IN (select b.ip from `#__oseipm_iptable` AS b group by b.ip having count(b.ip)>1)";
		$where= array_merge($where, $this->generateQueryWhere());
		$where=(count($where) ? ' WHERE ('.implode(') AND (', $where).')' : '');
		$query= " SELECT a.* from `#__oseipm_iptable` as a ".
				" INNER JOIN `#__oseipm_acl` AS acl ON acl.id = a.acl_id".
				" GROUP BY a.ip having count(a.ip)>1 ";
		$db->setQuery($query);
		$this->total= self::getListTotal($query, "a.*", "count(a.id)");
		$start= JRequest :: getInt('start', 0);
		$limit= JRequest :: getInt('limit', 1);
		$db->setQuery($query, $start, $limit);
		$rows= $db->loadAssocList();
		foreach($rows as $key =>$row)
		{
			$query = "SELECT acl_id FROM `#__oseipm_iptable` WHERE `ip` = '{$row['ip']}'";
			$db->setQuery($query);
			$acl_ids = $db->loadObjectList();
			$array = array();
			foreach($acl_ids as $acl_id)
			{
				$query = "SELECT status FROM `#__oseipm_acl` WHERE `id` = '{$acl_id->acl_id}'";
				$db->setQuery($query);
				$array[] = $db->loadResult();
			}
			$status = null;
			if(in_array('1',$array))$status.='Blacklist,';
			if(in_array('2',$array))$status.='Monitored,';
			if(in_array('3',$array))$status.='Whitelist,';
			unset($array);
			$rows[$key]['status'] = trim($status,',');
		}
		return $rows;
	}

	function getDupACL() {
		$db = $this->db;
		$where= array();
		$ip= JRequest :: getString('ip', null);
		self::checkIPValidity($ip);
		$where[]= "a.ip = '{$ip}'";
		$where=(count($where) ? ' WHERE ('.implode(') AND (', $where).')' : '');
		$query= " SELECT acl.id AS ACLID, acl.name AS ACLNAME,acl.status from `#__oseipm_iptable` as a ".
				" INNER JOIN `#__oseipm_acl` AS acl ON acl.id = a.acl_id".$where." GROUP BY acl.id ".
				" ORDER BY acl.name";
		$db->setQuery($query);
		$rows= $db->loadAssocList();
		foreach($rows as $key => $row) {
			if($row['status'] == 2)
			{
				$status = 'Monitored';
			}elseif($row['status'] == 1){
				$status = 'Black';
			}elseif($row['status'] == 3){
				$status = 'White';
			}
			$row['ACLNAME']= empty($row['ACLNAME']) ? 'acl'.$row['ACLID'] : $row['ACLNAME'];
			$rows[$key]['ACLNAME']= $row['ACLNAME'].' Status: '.$status;
		}
		return $rows;
	}
	function generateQueryWhere() {
		$filters= JRequest :: getVar('filter', null);
		// GridFilters sends filters as an Array if not json encoded
		if(is_array($filters)) {
			$encoded= false;
		} else {
			$encoded= true;
			$filters= json_decode($filters);
		}
		$where= array();
		// loop through filters sent by client
		if(is_array($filters)) {
			for($i= 0; $i < count($filters); $i++) {
				$filter= $filters[$i];
				// assign filter data (location depends if encoded or not)
				if($encoded) {
					$field= $filter->field;
					$value= $filter->value;
					$compare= isset($filter->comparison) ? $filter->comparison : null;
					$filterType= $filter->type;
				} else {
					$field= $filter['field'];
					$value= $filter['data']['value'];
					$compare= isset($filter['data']['comparison']) ? $filter['data']['comparison'] : null;
					$filterType= $filter['data']['type'];
				}
			}
			switch($filterType) {
				case 'string' :
					$value= $this->_db->Quote('%'.$this->_db->getEscaped($value, true).'%', false);
					$where[]= "{$field} LIKE {$value}";
					break;
				case 'list' :
					if(strstr($value, ',')) {
						$fi= explode(',', $value);
						for($q= 0; $q < count($fi); $q++) {
							$fi[$q]= "'".$fi[$q]."'";
						}
						$value= implode(',', $fi);
						$where[]= $field." IN (".$value.")";
					} else {
						$where[]= "{$field} = '{$value}'";
					}
					break;
			}
		}
		return $where;
	}
	function ajaxResponse($status, $message, $result=false)
	{
		$return['success'] = $result; 
		$return['status'] = $status;
		$return['result'] = $message;
		echo $this->json->encode($return);
		exit;
	}
	function checkIPValidity($ipAddress)
	{
		JArrayHelper :: toInteger($ipAddress, array(1, 1, 1, 1));
		foreach($ipAddress as $key => $ip) {
			if(!isset($ip)) {
				self:: ajaxResponse('ERROR', JText::_("IP is empty."), false);
			}
			elseif($ip > 255) {
				self:: ajaxResponse('ERROR', JText::_("The IP is invalid, please check if your any of your octets is greater than 255."), false);
			}
		}
		return true;
	}
	function addIPs() {
		$id= JRequest :: getInt('id', 0);
		$post= JRequest :: get('post');
		// Start IP value;
		if($post['ip_start']) {
			$ip_start= explode('.', $post['ip_start']);
			self::checkIPValidity($ip_start);
		} else {
			self:: ajaxResponse('ERROR', JText::_("Start IP value is empty."), false);
		}
		$post['ip_start']= implode('.', $ip_start);
		// Now End IP value;
		if($post['iptype'] == 'ips') {
			if($post['ip_end']) {
				$ip_end= explode('.', $post['ip_end']);
				self::checkIPValidity($ip_end);
				$post['ip_end']= implode('.', $ip_end);
			}
		}
		// Add the IP Rules;
		$acl_id= self::insertACL($post);
		if(!self::store($acl_id, $post)) {
			self:: ajaxResponse('ERROR', JText::_("Failed inserting the IP rules."), false);
		}
		else
		{
			if (isset($post['insertype'])&& $post['insertype']=='man')
			{
				return $acl_id;
			}
			else
			{
				self:: ajaxResponse('Done', JText::_("IP rules are added successfully."), true);
			}
		}

	}
	function updateACLIPs($data) {
		if (!in_array($data['iptype'],array('ip', 'ips')))
		{
			return false;
		}
		$query= " UPDATE `#__oseipm_ips` SET `status` = ".(int)$data['status'].", `name` = ".$this->db->Quote($data['title'], true).", `iptype` = ".$this->db->Quote($data['iptype']).
				" WHERE id = ".(int)$data['id'];
		$this->db->setQuery($query);
		$this->db->query();
		return $data['id'];
	}
	function insertACL($data) {
		if (!in_array($data['iptype'],array('ip', 'ips')))
		{
			return false;
		}
		/*
		$query = "SELECT count(id) FROM `#__oseipm_acl` WHERE `name` = ".$this->db->Quote($data['title'], true);
		$this->db->setQuery($query);
		$result = $this->db->loadResult();
		if ($result>0)
		{
			self::ajaxResponse('ERROR', JText::_("The Access Rule with the same title has been registered, please choose another title for this rule."), false);
			return false;
		}
		else
		{
		*/
			$query= " INSERT INTO `#__oseipm_acl` (`status`,`iptype`,`name`,`extension`) ".
					" VALUES ( ".(int)$data['status'].", ".$this->db->Quote($data['iptype'], true).", ".$this->db->Quote($data['title'], true).", 'sec')";
			$this->db->setQuery($query);
			$this->db->query();
			$acl_id= $this->db->insertid();
			return $acl_id;
		//}
	}
	function updateACL($aclids, $data, $ajax = true){
		$aclids=$this->json->decode($aclids);
		$data['title'] = (empty($data['title']))?'System Created':$data['title'];
		self::checkIDs($aclids);
		$result = true;
		if (!is_array($aclids))
		{
			$aclids = array($aclids);
		}
		foreach ($aclids as $aclid)
		{
			$query = " UPDATE `#__oseipm_acl` SET `status` = ".(int)$data['status'].", `name` = ".$this->db->Quote($data['title'], true).
					 " WHERE `id` =".(int)$aclid;
			$this->db->setQuery($query);
			if (!$this->db->query())
			{
				self::ajaxResponse("ERROR", $this->db->getErrorMsg(), false);
				$result = false;
			}
		}
		if ($result == true)
		{
			if ($ajax==true)
			{
				self::ajaxResponse("Done", JText::_("The ACL Rule is successfully updated."), true);
			}
			else
			{
				return true;
			}
		}
	}
	function deleteACL($aclids) {
		$aclids=$this->json->decode($aclids);
		self::checkIDs($aclids);
		$db = $this->db;
		$result = true;
		foreach ($aclids as $aclid)
		{
			$this->deletel1l2records($aclid);
			$query= " DELETE FROM `#__oseipm_acl` WHERE `id` = ".(int)$aclid;
			$db->setQuery($query);
			if(!$db->query()) {
				self::ajaxResponse("ERROR", $db->getErrorMsg(), false);
				$result = false;
			}
			$query= " DELETE FROM `#__oseipm_iptable` WHERE `acl_id` = ".(int)$aclid;
			$db->setQuery($query);
			if(!$db->query()) {
				self::ajaxResponse("ERROR", $db->getErrorMsg(), false);
				$result = false;
			}
			$query= " DELETE FROM `#__oseath_alerts` WHERE `aclid` = ".(int)$aclid;
			$db->setQuery($query);
			if(!$db->query()) {
				self::ajaxResponse("ERROR", $db->getErrorMsg(), false);
				$result = false;
			}
		}
		if ($result == true)
		{
			self::ajaxResponse("Done", JText::_("The ACL Rule is successfully deleted."), true);
		}
	}
	function deletel1l2records($aclid)
	{
		$this->db->setQuery ("SELECT `l1ruleids`, `l2ruleids` FROM `#__oseath_alerts` WHERE aclid = ". (int)$aclid);
		$results = $this->db->loadObject(); 
		if (!empty($results->l1ruleids))
		{
			$where = '('.str_replace(array('[',']'), '', $results->l1ruleids).')'; 
			$this->db->setQuery ("DELETE FROM `#__oseath_l1rules` WHERE `id` IN ". $where);
		}
		if (!empty($results->l2ruleids))
		{
			$where = '('.str_replace(array('[',']'), '', $results->l2ruleids).')';
			$this->db->setQuery ("DELETE FROM `#__oseath_l2rules` WHERE `id` IN ". $where);
		}
		$result = $this->db->query();
		return $result; 
	}
	function reorderingIP($data)
	{
		$start = explode('.',$data['ip_start']);
		$end = explode('.',$data['ip_end']);
		$v1 = ($end[0]<$start[0])?1:0;
		$v2 = ($end[0]==$start[0] && $end[1]<$start[1])?1:0;
		$v3 = ($end[0]==$start[0] && $end[1]==$start[1] && $end[2]<$start[2])?1:0;
		$v4 = ($end[0]==$start[0] && $end[1]==$start[1] && $end[2]==$start[2] && $end[3]<$start[3])?1:0;
		
		if ($v1 || $v2 || $v3 || $v4)
		{
			$tmp = $data['ip_end'];
			$data['ip_end'] = $data['ip_start'];
			$data['ip_start'] = $tmp;
			unset($tmp);
		}
		return $data; 
	}
	function store($acl_id, $data) {
		$data['ip_end'] = (isset($data['ip_end']))?$data['ip_end']:'';
		if (!empty($data['ip_end']))
		{
			$data = self::reorderingIP($data); 
		}	
		$ip_start= explode('.', $data['ip_start']);
		if($data['iptype'] == 'ips') {
			//cycle count
			$t= 0;
			$ip_end= explode('.', $data['ip_end']);
			foreach($ip_start as $key_start => $value_start) {
				$pow= $key_start +1;
				$key_end= $key_start;
				$value_end= $ip_end[$key_end];
				$t +=($value_end - $value_start) * pow(256,(count($ip_start) - $pow));
			}
			for($i= 0; $i <= $t; $i++) {
				$ip= implode('.', $ip_start);
				if(!self::insertIP($acl_id, $ip)) {
					return false;
				}
				for($j=(count($ip_start) - 1); $j >= 0; $j--) {
					$ip_start[$j]++;
					if($ip_start[$j] <= 255) {
						break;
					} else {
						$ip_start[$j]= 0;
					}
				}
			}
		} else {
			$ip= $data['ip_start'];
			if(!self::insertIP($acl_id, $ip)) {
				return false;
			}
		}
		return true;
	}
	function insertIP($acl_id, $ip, $userid = 0) {
		$query = " INSERT INTO `#__oseipm_iptable` (id, acl_id, ip, user_id, host) ".
				 " VALUES(NULL, ".(int)$acl_id.", ".$this->db->Quote($ip).", " . (int)$userid.", '') ";
		$this->db->setQuery($query);
		if(!$this->db->query()) {
			return false;
		}
		return true;
	}
	function updateIP($acl_id, $ip, $userid = 0, $iptable_id) {
		$query = "UPDATE `#__oseipm_iptable` SET `acl_id` = ".(int)$acl_id.", `ip` = ".$this->db->Quote($ip).", `user_id` = " . (int)$userid.", `host` = '' WHERE `id` = ". (int)$iptable_id;
		$this->db->setQuery($query);
		if(!$this->db->query()) {
			return false;
		}
		return true;
	}
	function checkIP($ip) {
		$query = "SELECT id FROM `#__oseipm_iptable` WHERE `ip` = ".$this->db->Quote($ip). " LIMIT 1";
		$this->db->setQuery($query);
		return $this->db->loadResult();
	}
	function remove() {
		$id= JRequest :: getInt('id');
		$db= $this->db;
		$query= " DELETE FROM `#__oseipc_ips` ".		" WHERE `acl_id` = '{$id}'";
		$db->setQuery($query);
		if(!$db->query()) {
			self::ajaxResponse("ERROR", $db->getErrorMsg(), false);
		}
		$query= " DELETE FROM `#__oseipc_acl` ".		" WHERE `id` = '{$id}'";
		$db->setQuery($query);
		if(!$db->query()) {
			return 'e3';
		}
		return 's';
	}
	function removeDup() {
		$db= $this->db;
		$id= JRequest :: getInt('id', 0);
		$acl_id= JRequest :: getInt('acl_id', 0);
		$status= JRequest :: getInt('status', 0);
		$ip= JRequest :: getString('ip', null);

		if(empty($acl_id)) {
			return JText :: _('Please Select the Acl to Delete');
		}
		if(empty($id) || empty($ip)) {
			return JText :: _('Can Not Found Any IP to Delete');
		}
		self::checkIPValidity($ip);
		$query= " SELECT id FROM `#__oseipm_iptable` WHERE `ip` = '{$ip}' AND `acl_id` <> ". (int)$acl_id;
		$db->setQuery($query);
		$results= $db->loadObjectlist();
		if (!empty($results))
		{
			foreach ($results as $result)
			{
				$query= " DELETE FROM `#__oseipm_iptable` WHERE `ip` = '{$ip}' AND `id` = ". (int)$result->id;
				$db->setQuery($query);
				if(!$db->query()) {
					self::ajaxResponse("ERROR", $db->getErrorMsg(), false);
				}
			}
			self::ajaxResponse("Done", JText::_("IP conflicts resolved."), true);
		}
		else
		{
			self::ajaxResponse("ERROR", JText::_("ERROR"), false);
		}
	}
	function updateAclDup($id, $status, $acl_id) {
		$db= $this->db;
		$query= " SELECT count(*) FROM `#__oseipm_iptable` WHERE `id` < '{$id}' and `acl_id` = ". (int)$acl_id;
		$db->setQuery($query);
		$result1= $db->loadResult();
		if($result1 < 1) {
			$query= " DELETE FROM `#__oseipm_acl` WHERE `id` = ".(int)$acl_id;
			$db->setQuery($query);
			if(!$db->query()) {
				self::ajaxResponse("ERROR", $db->getErrorMsg(), false);
			}
		} else
			if($result1 == 1) {
				$query= " UPDATE `#__oseipm_acl` SET `iptype` = 'ip' WHERE `id` = ".(int)$acl_id;
				$db->setQuery($query);
				if(!$db->query()) {
					self::ajaxResponse("ERROR", $db->getErrorMsg(), false);
				}
			}
		$query= " SELECT count(*) FROM `#__oseipm_iptable` WHERE `id` > ". (int)$id." and `acl_id` = ".(int)$acl_id;
		$db->setQuery($query);
		$result2= $db->loadResult();
		if($result2 > 0) {
			if($result2 > 1) {
				$iptype= 'ips';
			} else {
				$iptype= 'ip';
			}
			$query= " INSERT INTO `#__oseipm_acl` (status, iptype) ".
					" VALUES ( '{$status}', '{$iptype}')";
			$db->setQuery($query);
			if(!$db->query()) {
				self::ajaxResponse("ERROR", $db->getErrorMsg(), false);
			}
			$Newid= $db->insertid();
			$query= " UPDATE `#__oseipm_iptable` SET `acl_id` = ". (int)$Newid .
					" WHERE `id` > ". (int)$id. " and `acl_id` = ".(int)$acl_id;
			$db->setQuery($query);
			if(!$db->query()) {
				self::ajaxResponse("ERROR", $db->getErrorMsg(), false);
			}
		}
		self::ajaxResponse("Done", JText::_("IP conflicts resolved."), true);
	}
}
?>