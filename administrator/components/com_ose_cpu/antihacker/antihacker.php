<?php
	/**
	 * @version     5.0 +
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

	class oseAntihacker {
		private $ip= null;
		private $url= null;
		private $referer= null;
		private $tags= null;
		private $target= null;
		private $scankey= true;
		private $scancookies= true;
		private $allowExts= null;
		private $threshold= 0;
		private $logtime= null;
		private $db= null;
		private $json= null;
		private $storage= null;
		public $centrifuge= array();
		private $layer1Detected= array();
		private $layer2Detected= array();
		private $l2rulesConf = array();
		private $blockIP= true;
		private $slient_max_att = null;
		private $tvar = null;
		private $aclid = null;
		private $scanYahooBots = true;

		function __construct() {
			self :: getBasicInfo();
		}
		private function getBasicInfo() {
			$this->url= 'http://'.str_replace("?".$_SERVER['QUERY_STRING'], "", $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
			if(isset($_SERVER['HTTP_REFERER'])) {
				$this->referer= $_SERVER['HTTP_REFERER'];
			} else {
				$this->referer= "N/A";
			}
			$this->ip= self :: getRealIP();
			$this->scankey= true;
			$this->blockIP= true;
			$this->scancookies= false;
			$this->allowExts= null;
			$this->scanGoogleBots= false;
			$this->scanMsnBots= false;
			$this->threshold= 35;
			$this->slient_max_att= 10;
			$this->layer1Detected= array();
			$this->layer2Detected= array();
			$this->banpagetype = false;
			$this->aclid = null;
			$this->scanYahooBots = true;

			// Get Configuration;
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
			$this->db->setQuery("SELECT * FROM `#__ose_secConfig` ");
			$results= $this->db->loadAssocList();
			if (!empty($results))
			{
			 foreach($results as $result) {
			 		
			 	if (strstr($result['key'], 'l2ruleid'))
			 	{
			 		if ($result['value']==true || $result['value'] == '1')
			 		{
			 			$this->l2rulesConf[] = str_replace('l2ruleid_', '', $result['key'] );
			 		}
			 	}
			 	elseif (strstr($result['key'], 'convert'))
			 	{
			 		if ($result['value']==true || $result['value'] == '1')
			 		{
			 			$this->converters[] = $result['key'];
			 		}
			 	}
			 	elseif (strstr($result['key'], 'slient_max_att'))
			 	{
			 		$this->slient_max_att= (empty($result['value']))?10:$result['value'];
			 	}
			 	else
			 	{
			 		$this->$result['key']= $result['value'];
			 	}
			 }
			}
			unset($results);
		}
		function hackScan() {
			$continue = self :: checkContinue();
			if ($continue===false)
			{
				return;
			}
			$ipStatus= self :: checkIPStatus();
			if ($ipStatus=='1')
			{
				self :: showBanPage();
			}
			elseif ($ipStatus=='3')
			{
				$this->db->__destruct();
				return;
			}
			else
			{
				self :: detectAttack();
				if (class_exists("athDB"))
				{
					$this->db->__destruct();
				}
			}
			unset ($this->layer2Detected);
			unset ($this->layer1Detected);
			unset ($this->l2rulesConf);
		}
		private function detectAttack() {
			self :: layer1Detect();
			if (isset($this->layer1Detected['score']) && $this->layer1Detected['score']===100)
			{
				self::controlAttack();
			}
			else
			{
				self :: layer2Detect();
				if (!empty($this->layer2Detected))
				{
					self::controlAttack();
				}
			}
			if (!empty($_FILES))
			{
				self::checkFileTypes($_FILES);
				self::scanFileVirus($_FILES);
			}
			if (isset($this->antiflooding) && $this->antiflooding==true)
			{
				self::scanFlooding($this->decduration, $this->maxvisits);
			}
		}
		private function checkContinue()
		{
			$bot=array();
			if ($this -> scanGoogleBots===false)
			{
				$bot[]='Google';
			}
			if ($this -> scanMsnBots===false)
			{
				$bot[]='msnbot';
			}
			if ($this ->scanYahooBots ===false)
			{
				$bot[]='Yahoo';
			}
			if (COUNT($bot) > 0 && $this->checkBot(implode('|', $bot), $_SERVER['HTTP_USER_AGENT']))
			{
				$this->blockIP = 2;
				$this->slient_max_att = 20;
				return true;
			}
			if (isset($_REQUEST['option']))
			{
				if (count($_REQUEST['option'])===1 && ($_REQUEST['option'] ==='com_ose_antihacker' || $_REQUEST['option'] ==='com_ose_antivirus' ))
				{
					return false;
				}
				else
				{
					return true;
				}
			}
			else
			{
				return true;
			}
		}
		private function checkBot($crawlers, $userAgent)
		{
			//$crawlers = 'Google|msnbot|Rambler|Yahoo|AbachoBOT|accoona|AcioRobot|ASPSeek|CocoCrawler|Dumbot|FAST-WebCrawler|GeonaBot|Gigabot|Lycos|MSRBOT|Scooter|AltaVista|IDBot|eStyle|Scrubby';
			$isCrawler = (preg_match("/$crawlers/", $userAgent) > 0);
			return $isCrawler;
		}
		private function checkIPStatus() {
			$this->db->setQuery("SELECT acl.status FROM `#__oseipm_acl` as acl, `#__oseipm_iptable` as iptable WHERE acl.id = iptable.acl_id AND iptable.ip='".$this->ip."' LIMIT 1");
			$result= $this->db->loadResult();
			return (int) $result;
		}
		private function getRealIP() {
			$ip= false;
			if(!empty($_SERVER['HTTP_CLIENT_IP'])) {
				$ip= $_SERVER['HTTP_CLIENT_IP'];
			}
			if(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
				$ips= explode(", ", $_SERVER['HTTP_X_FORWARDED_FOR']);
				if($ip) {
					array_unshift($ips, $ip);
					$ip= false;
				}
				$this->tvar = phpversion();
				for($i= 0, $total = count($ips); $i < $total; $i++) {
					if(!preg_match("/^(10|172\.16|192\.168)\./i", $ips[$i])) {
						if(version_compare($this->tvar, "5.0.0", ">=")) {
							if(ip2long($ips[$i]) != false) {
								$ip= $ips[$i];
								break;
							}
						} else {
							if(ip2long($ips[$i]) != -1) {
								$ip= $ips[$i];
								break;
							}
						}
					}
				}
			}
			return($ip ? $ip : $_SERVER['REMOTE_ADDR']);
		}
		function getLayer1Signatures() {
			include_once(dirname(__FILE__).OSEDS."library".OSEDS."signature.php");
			return getSignatures();
		}
		private function layer1Detect() {
			$switch= '';
			$violation= '';
			$return= array();
			$urlQuery= htmlspecialchars(str_replace(array("%3C", "%3E", "%5C", " ", "\\\\"), array("<", ">", "\\","%20", "\\"), $_SERVER['QUERY_STRING']));
			$whitelistSigkeys= self :: getWhitelistSignatureKeys();
			$layer1Rules= self :: getLayer1Signatures();
			if (!empty($whitelistSigkeys))
			{
				$layer1Rules = array_diff($layer1Rules, $whitelistSigkeys);
			}
			// Loop through the exploit list and compare it to the query string
			foreach($layer1Rules as $key => $value) {
				if(!empty($urlQuery) && stristr($urlQuery, htmlspecialchars($value))) {
					// Check whitelist key patterns;
					$whitelisted = $this->checkwhitelistkeys($urlQuery);
					if ($whitelisted== false)
					{
						// Check if the signature is set to ignored
						$this->layer1Detected['score']= 100;
						$this->layer1Detected['trgRule']= "SIG[".$key."]";
						$this->layer1Detected['trimmed_value']= htmlspecialchars($value);
						$this->target= $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
						break;
					}
				}
				// Check User Agent Attack;
				if ((!empty($value)) && isset($_SERVER['HTTP_USER_AGENT']) && stristr($_SERVER['HTTP_USER_AGENT'], htmlspecialchars($value)))
				{
					$this->layer1Detected['score']= 100;
					$this->layer1Detected['trgRule']= "SIG[".$key."]";
					$this->layer1Detected['trimmed_value']= htmlspecialchars($value);
					$this->target= 'HTTP User Agent';
					break;
				}
			}
			return;
		}
		private function checkwhitelistkeys($urlQuery)
		{
			$query = "SELECT `string` FROM `#__oseath_whitelist` ";
			$this->db->setQuery($query); 
			$result = $this->db->loadResultArray();
			if (empty($result))
			{
				return false; 
			}
			$pattern = "/".implode("|", $result)."/";
			$matches = array();
			preg_match($pattern, $urlQuery, $matches); 
			if (count($matches) >0)
			{
				return true;
			}
			else
			{
				return false; 
			}
			
		}
		private function getRules($type, $ruleaction, $targetAction= null, $array= true, $times= null) {
			$where= '';
			if(!empty($times)) {
				$where .= " AND `times` = ".(int) $times;
			}
			if(!empty($targetAction)) {
				$where .= " AND `targetaction` = ".(int) $targetAction;
			}
			if($type == 'l1') {
				$query= "SELECT `signature` FROM `#__oseath_l1rules` WHERE `signatureaction` = ".(int) $ruleaction.$where.' ORDER BY `targetaction` DESC ';
			} else {
				$query= "SELECT `key` FROM `#__oseath_l2rules` WHERE `keyaction` = ".(int) $ruleaction.$where.' ORDER BY `targetaction` DESC ';
			}
			$this->db->setQuery($query);
			if($array == true) {
				$rules= $this->db->loadAssocList();
			} else {
				$rules= $this->db->loadObjectList();
			}
			return $rules;
		}
		private function layer2Detect() {
			require_once(OSE_ADMINPATH.OSEDS.'components'.OSEDS.'com_ose_cpu'.OSEDS.'antihacker'.OSEDS.'library'.OSEDS.'Converter.php');
			require_once(OSE_ADMINPATH.OSEDS.'components'.OSEDS.'com_ose_cpu'.OSEDS.'antihacker'.OSEDS.'library'.OSEDS.'Storage.php');
			$this->storage= new IDS_Filter_Storage();
			$this->filterSet= $this->storage->getFilterSet();
			if ($this->scancookies==true)
			{
				$request= array('GET' => $_GET, 'POST' => $_POST, 'COOKIE' => $_COOKIE);
			}
			else
			{
				$request= array('GET' => $_GET, 'POST' => $_POST );
			}
			foreach($request as $key => $value) {
				self :: layer2DetectIterate($key, $value, null, null, $this->l2rulesConf);
			}
		}
		// Add IP into blacklisted IP pool
		private function addBlacklistedIP($attackType, $attackAction = null) {
			require_once (OSE_ADMINPATH.OSEDS.'components'.OSEDS.'com_ose_cpu'.OSEDS.'ipmanager'.OSEDS.'ipmanager.php');
			$ipmanager= new oseIpmanager();
			$userid= 0;
			if (class_exists("JConfig"))
			{
				$user= & JFactory :: getUser();
			}
			else
			{
				$user= new stdClass();
				$user->id = 0; 
			}
			$data['iptype']= 'ip';
			$timestamp= date("ymdhis").mt_rand(10, 99);
			$data['title']= implode(" + ", $attackType)." "."Rules"."[{$timestamp}]";
			$data['status']= (empty($attackAction))?'1':$attackAction;
			$query= "SELECT * FROM `#__oseipm_iptable` WHERE `ip`=".$this->db->Quote($this->ip);
			$this->db->setQuery($query);
			$result= $this->db->loadObject();
			if(empty($result)) {
				$acl_id= $ipmanager->insertACL($data);
			} else {
				$acl_id= $this->json->encode($result->acl_id);
				$ipmanager->updateACL($acl_id, $data, false);
			}
			$acl_id= $this->json->decode($acl_id);
			$acl_id= (int) $acl_id;
			$iptable_id= $ipmanager->checkIP($this->ip);
			if(empty($iptable_id)) {
				$ipmanager->insertIP($acl_id, $this->ip, $user->id);
			} else {
				$ipmanager->updateIP($acl_id, $this->ip, $user->id, $iptable_id);
			}
			return $acl_id;
		}
		private function checkTarget($aclid, $type, $rule, $target, $ruleAction= null, $targetAction= null, $array= true, $times= null) {
			$where= '';
			if(!empty($times)) {
				$where .= " AND `times` = ".(int) $times;
			}
			if(!empty($targetAction)) {
				$where .= " AND `targetaction` = ".(int) $targetAction;
			}
			if ($type == 'l1')
			{
				$query= "SELECT * FROM `#__oseath_l1rules` WHERE `signatureaction` = 3 AND `signature` = ". $this->db->Quote($rule, true)." LIMIT 1 ";
				$this->db->setQuery($query);
			}
			else
			{
				$query= "SELECT * FROM `#__oseath_l2rules` WHERE `keyaction` = 3 AND `key` = ". $this->db->Quote($rule, true)." LIMIT 1 ";
				$this->db->setQuery($query);
			}
			if($array == true) {
				$ruleExist= $this->db->loadAssocList();
			} else {
				$ruleExist= $this->db->loadObjectList();
			}

			if (empty($ruleExist))
			{
				if($type == 'l1') {
					if(!empty($ruleAction)) {
						$where .= " AND `signatureaction` = ".(int) $ruleAction.' AND `aclid` = '. (int)$aclid;
					}
				} else {
					if(!empty($ruleAction)) {
						$where .= " AND `keyaction` = ".(int) $ruleAction.' AND `aclid` = '. (int)$aclid;
					}
				}
				$query= "SELECT * FROM `#__oseath_{$type}rules` WHERE `target` = ".$this->db->Quote($target, true).$where." LIMIT 1 ";
				$this->db->setQuery($query);
				if($array == true) {
					$rules= $this->db->loadAssocList();
				} else {
					$rules= $this->db->loadObjectList();
				}
				return $rules;
			}
			else
			{
				return $ruleExist;
			}
		}
		private function insertTarget($aclid, $type, $rule, $target, $trimmed_value = null) {
			if($type == 'l1') {
				$query= "INSERT INTO `#__oseath_l1rules` ".	" (`id`,`aclid`,`signature`, `trimmed_value`, `signatureaction`, `target`, `targetaction`, `times`)"
				." VALUES (NULL,".(int)$aclid.",".$this->db->Quote($rule, true).",".$this->db->Quote($trimmed_value, true).", 1, ".$this->db->Quote($target, true).", 1, 1);";
				$this->db->setQuery($query);
				if($this->db->query()) {
					return $this->db->insertid();
				} else {
					return false;
				}
			} else {
				$filters = array();
				if (!empty($this->layer2Detected))
				{
					foreach ($this->layer2Detected as $l2detected)
					{
						$tmp = explode(",",$l2detected ['filtersID']);
						if (!is_array($tmp))
						{
							$tmp = array($tmp);
						}
						foreach ($tmp as $t)
						{
							$filters[]=(int)trim($t);
						}
					}
					$filters = implode(',', array_unique($filters));
					$query= "INSERT INTO `#__oseath_l2rules` ".	" (`id`,`aclid`,`key`, `trimmed_value`, `keyaction`, `target`, `targetaction`, `times`, `filters`)"
					." VALUES (NULL,".(int)$aclid.",".$this->db->Quote($rule, true).", ".$this->db->Quote($trimmed_value, true).", 1, ".$this->db->Quote($target, true).", 1, 1, ".$this->db->Quote($filters, true).");";
					$this->db->setQuery($query);
					if($this->db->query()) {
						return $this->db->insertid();
					} else {
						return false;
					}
				}
			}
		}
		private function updateTarget($type, $value=array(), $targetid) {
			$value = implode(',',$value);
			if($type == 'l1') {
				$query= "UPDATE `#__oseath_l1rules` ".	"SET  ".$value." WHERE `id` = '{$targetid}'";
			} else {
				$query= "UPDATE `#__oseath_l2rules` ".	"SET  ".$value." WHERE `id` = '{$targetid}'";
			}
			$this->db->setQuery($query);
			if($this->db->query()) {
				return $targetid;
			} else {
				return false;
			}
		}
		/* function layer1Detect()
		 * returns true if attacks are found; false if no attacks are found
		*/
		private function getWhitelistSignatureKeys() {
			$return= array();
			$i= 0;
			$ignoreSignatures= self :: getRules('l1', 2, null, false, null);
			if(!empty($ignoreSignatures)) {
				foreach($ignoreSignatures as $ignoreSignature) {
					$return[$i]= str_replace(array("SIG[", "]"), "", $ignoreSignature->signature);
					$i++;
				}
			}
			return $return;
		}
		private function getWhitelistPatternKeys($status) {
			$return= array();
			$i= 0;
			$ignorePatterns= self :: getRules('l2', $status, null, true, null);

			if(!empty($ignorePatterns)) {
				foreach($ignorePatterns as $ignorePattern) {
					$return[$i]= $ignorePattern['key'];
					$i++;
				}
			}
			return $return;
		}
		private function getKeyFilterStatus($type, $rule) {
			if($type == 'l1') {
				$table= "#__oseath_l1rules";
				$field= "signature";
				$fieldaction= "signatureaction";
			} else {
				$table= "#__oseath_l2rules";
				$field= "key";
				$fieldaction= "keyaction";
			}
			$query= "SELECT MAX(`{$fieldaction}`) FROM `{$table}` WHERE (`{$fieldaction}`= 2 OR `{$fieldaction}`= 4) AND `{$field}` = ".$this->db->Quote($rule, true);
			$this->db->setQuery($query);
			return $this->db->loadResult();
		}
		function logAttack($acl_id, $targetid, $totalImpact) {
			$query= "SELECT * FROM `#__oseath_alerts` WHERE `aclid` = ".(int) $acl_id;
			$this->db->setQuery($query);
			$result= $this->db->loadObject();
				
			if (!empty($result->l1ruleids))
			{
				$this->tvar = (array)$this->json->decode($result->l1ruleids);
				if (isset($targetid['l1']))
				{
					$l1ruleids = $this->json->encode(array_unique(array_merge($targetid['l1'],$this->tvar)));
				}
				else
				{
					$l1ruleids = $result->l1ruleids;
				}
			}
			else
			{
				$l1ruleids=(isset($targetid['l1'])) ? $this->json->encode($targetid['l1']) : '';
			}
			if (!empty($result->l2ruleids))
			{
				$this->tvar = (array)$this->json->decode($result->l2ruleids);
				if (isset($targetid['l2']))
				{
					$l2ruleids = $this->json->encode(array_unique(array_merge($targetid['l2'],$this->tvar)));
				}
				else
				{
					$l2ruleids = $result->l2ruleids;
				}
			}
			else
			{
				$l2ruleids=(isset($targetid['l2'])) ? $this->json->encode($targetid['l2']) : '';
			}
			$this->logtime= date("Y-m-d, h:i:s");
			if(COUNT($result) > 0) {
				$query= " UPDATE `#__oseath_alerts` SET `l1ruleids` = ".$this->db->Quote($l1ruleids, true).	", `l2ruleids` = ".$this->db->Quote($l2ruleids, true).	", `score` = ".$this->db->Quote($totalImpact).	", `datetime` = ".$this->db->Quote($this->logtime).	", `referer` = ".$this->db->Quote($this->referer, true)." WHERE `aclid` =".(int) $acl_id;
			} else {
				$query= " INSERT INTO `#__oseath_alerts`".
								" (`id`,`aclid`,`l1ruleids`,`l2ruleids`,`datetime`,`score`,`referer`,`notified`)".
								" VALUES ".
								" (NULL , '".(int) $acl_id."', ".$this->db->Quote($l1ruleids, true).", ".$this->db->Quote($l2ruleids, true).", ".$this->db->Quote($this->logtime).
								", ".$this->db->Quote($totalImpact).", ".$this->db->Quote($this->referer, true)." , NULL);";
			}
			$this->db->setQuery($query);
			if($this->db->query()) {
				return true;
			} else {
				return false;
			}
		}
		private function layer2DetectIterate($key, $value, $priKey=null, $secKey=null, $l2rulesConf=null) {
			if(!is_array($value)) {
				if(is_string($value)) {
					self :: layer2DetectAttack($key, $value, $priKey, $secKey, $l2rulesConf);
				} else {
					return false;
				}
			} else {
				foreach($value as $subKey => $subValue) {
					self :: layer2DetectIterate($key.'.'.$subKey, $subValue, $key, $subKey, $l2rulesConf);
				}
			}
		}
		private function layer2DetectAttack($key, $value, $priKey=null, $secKey=null, $l2rulesConf=null)
		{
			if (empty($value))
			{
				return;
			}
			// 0 - ignore; 1- scan and block; 2 - scan and filter;
			$WhitelistPatternKeys= self :: getWhitelistPatternKeys(3);
			// isn't alphanumeric
			/*
			if(!$value || !preg_match('/[^\w\s\/@!?,\.]+|(?:\.\/)|(?:@@\w+)/', $value)) {
			return false;
			}
			*/
			// check if this field is part of the exceptions
			$tmptest = explode('.', $key);
			if (count($tmptest)>1)
			{
				$key = $tmptest[0].'.'.$tmptest[1];
			}
			if(is_array($WhitelistPatternKeys) && in_array($key, $WhitelistPatternKeys, true)) {
				return false;
			}
			// check for magic quotes and remove them if necessary
			if(function_exists('get_magic_quotes_gpc') && get_magic_quotes_gpc()) {
				$value= stripslashes($value);
			}
			if (!empty($this ->converters)) {
				$value= IDS_Converter :: runAll($value, $this);
				$value= $this->runCentrifuge?IDS_Converter :: runCentrifuge($value, $this): $value;
			}
			// scan keys if activated via config
			if (!isset($this->scankey))
			{
				$this->scankey = false;
			}
			if (!isset($this->scankeyLength))
			{
				$this->scankeyLength = 100;
			}
			if ($this->scankey==true)
			{
				if (!empty($this ->converters)) {
					$key= $this->scankey ? IDS_Converter :: runAll($key, $this) : $key;
					$key= $this->scankey ? IDS_Converter :: runCentrifuge($key, $this) : $key;
				}
				if (strlen($key) > $this->scankeyLength)
				{
					self::show403('Illegal Key detected.');
				}
			}
			// scan value against Patterns;
			$i= 0;
			if (!empty($l2rulesConf))
			{
				foreach($l2rulesConf as $id) {
					self :: patternMatch($key, $value, $this->filterSet[$id-1]);
				}
			}
			unset($key);
			unset($value);
			unset($WhitelistPatternKeys);
			unset($l2rulesConf); 
		}
		private function patternMatch($key, $value, $filter)
		{
			$match= false;
			$urlQuery= htmlspecialchars(str_replace(array("%3C", "%3E", "%5C"), array("<", ">", "\\"), $_SERVER['QUERY_STRING']));
			if($this->scankey == true) {
				if($filter->match($key)) {
					$this->layer2Detected['MalformedKey']['filtersID']=(empty($this->layer2Detected[$key]['filtersID'])) ? $filter->id : $this->layer2Detected[$key]['filtersID'].", ".$filter->id;
					$this->layer2Detected['MalformedKey']['impact']=(empty($this->layer2Detected[$key]['impact'])) ? $filter->impact : $this->layer2Detected[$key]['impact'] + $filter->impact;
					$this->layer2Detected['MalformedKey']['value']= $value;
					$this->layer2Detected['MalformedKey']['description']= (empty($this->layer2Detected['MalformedKey']['description'])) ? $filter->description : $this->layer2Detected['MalformedKey']['description'].", ".$filter->description;
					$this->target= $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'].$urlQuery;
				}
			}
			if($filter->match($value)) {
				$this->layer2Detected[$key]['filtersID']=(empty($this->layer2Detected[$key]['filtersID'])) ? (int)$filter->id : $this->layer2Detected[$key]['filtersID'].",".(int)$filter->id;
				$this->layer2Detected[$key]['impact']=(empty($this->layer2Detected[$key]['impact'])) ? $filter->impact : $this->layer2Detected[$key]['impact'] + $filter->impact;
				$this->layer2Detected[$key]['value']= $value;
				$this->layer2Detected[$key]['description']= (empty($this->layer2Detected[$key]['description'])) ? $filter->description : $this->layer2Detected[$key]['description'].", ".$filter->description;
				$this->target= $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'].$urlQuery;
			}
			return;
		}
		private function showBanPage() {
			$systemSetting = self::getSysSetting();
			$customInfo = $systemSetting->getConfiguration();
			$adminEmail = (isset($customInfo['adminEmail']))?$customInfo['adminEmail']: "";
			$customBanPage=(!empty($customInfo['customBanpage'])) ? $customInfo['customBanpage'] : "Banned";
			$pageTitle=(!empty($customInfo['pageTitle'])) ? $customInfo['pageTitle'] : "OSE Security Suite";
			$metaKeys=(!empty($customInfo['metaKeywords'])) ? $customInfo['metaKeywords'] : "OSE Security Suite";
			$metaDescription=(!empty($customInfo['metaDescription'])) ? $customInfo['metaDescription'] : "OSE Security Suite";
			$metaGenerator=(!empty($customInfo['metaGenerator'])) ? $customInfo['metaGenerator'] : "OSE Security Suite";
			$banhtml= '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
						<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
						<head>
						  <meta http-equiv="content-type" content="text/html; charset=utf-8" />
						  <meta name="robots" content="index, follow" />
						  <meta name="keywords" content="'.$metaKeys.'" />
						  <meta name="description" content="'.$metaDescription.'" />
						  <meta name="generator" content="'.$metaGenerator.'" />
						  <title>'.$pageTitle.'</title>
						</head>
						<body>
			     		<div style="margin:auto;width:780px;border:0px solid #0082b0;padding:0px 10px 10px 10px;z-index:100;color:#000000;">
						<br/>
						'.$customBanPage.'
						<div style="font-family: arial,helvetica,sans-serif;background-color:#ffffff;padding: 10px 0px 0px 0px" align="center"><font color="#666666" size="1">Your IP address is '.$this->ip.'. If you believe this is an error, please contact the <a href="mailto:'.$adminEmail.'?Subject=Inquiry:%20Banned%20for%20suspicious%20hacking%20behaviour - IP: '.$this->ip.' - Violation"> Webmaster </a>.</font></div>
						</div>
						</body>
					</html>';
			echo $banhtml;
			exit;
		}
		/* return values:
		 * 0 --> no record / normal
		* 1 --> blacklisted
		* 2 --> monitored
		* 3 --> whitelisted
		*/
		private function insertacl($totalImpact, $attackType)
		{
			// Put to blacklist or monitor list
			if($totalImpact > $this->threshold) {
				if ($this->blockIP==1)
				{
					$this->acl_id= self :: addBlacklistedIP($attackType);
				}
				elseif ($this->blockIP==2)
				{
					if (!isset($_SESSION[$this->ip]))
					{
						$_SESSION[$this->ip] = 0;
					}
					if ($_SESSION[$this->ip]<$this->slient_max_att)
					{
						$this->acl_id= self :: addBlacklistedIP($attackType,2);
					}
					else
					{
						$this->acl_id= self :: addBlacklistedIP($attackType,1);
					}
				}

				else
				{
					$this->acl_id= self :: addBlacklistedIP($attackType,2);
				}
			} else {
				// Put to monitor list
				$this->acl_id= self :: addBlacklistedIP($attackType, 2);
			}
		}
		private function controlAttack() {
			$totalImpact= 0;
			$targetid= array();
			$action= array();
			$attackType= array();
			$action['trim']= false;
			$action['filter']= false;
			$pass1 = false;
			$pass2 = false;

			if(!empty($this->layer1Detected)) {
				$attackType[]= 'L1';
				$totalImpact += $this->layer1Detected['score'];
				$this->insertacl($totalImpact, $attackType);

				if($this->target=='HTTP User Agent')
				{
					$targetStatus= false;
				}
				else
				{
					$targetStatus= self :: checkTarget($this->acl_id, 'l1', $this->layer1Detected['trgRule'], $this->target);
				}
				$filterkey= self :: getKeyFilterStatus('l1', $this->layer1Detected['trgRule']);
				if(empty($targetStatus)) {
					$targetid['l1'][]= self :: insertTarget($this->acl_id,'l1', $this->layer1Detected['trgRule'], $this->target, $this->layer1Detected['trimmed_value']);
					if($filterkey > 0 || $this->blockIP==2) {
						self :: trimAttack($this->layer1Detected['trgRule'], $this->target);
					}
				} else {
					if($targetStatus[0]['signatureaction'] == 3 || $targetStatus[0]['targetaction'] == 3) {
						$targetid['l1'][]= $targetStatus[0]['id'];
						$pass1 =true;
					} else {
						if($filterkey > 0 || $this->blockIP==2) {
							$action['trim']= true;
							$where [] = ' `aclid` = '.(int)$this->acl_id;
							$where [] = ' `signature` = '.$this->db->Quote($this->layer1Detected['trgRule'], true);
							$where [] = ' `trimmed_value` = '.$this->db->Quote($this->layer1Detected['trimmed_value'], true);
							$where [] = ' `target` = '.$this->db->Quote($this->target, true);
							$l1id = $targetid['l1'][]= self :: updateTarget('l1', $where, $targetStatus[0]['id']);
							self :: trimAttack($this->layer1Detected['trgRule'], $this->target,$l1id);
						} else {
							$targetid['l1'][]= $targetStatus[0]['id'];
						}
					}
				}

			}
			else
			{
				$pass1 = true;
			}

			if(!empty($this->layer2Detected)) {
				$attackType[]= 'L2';
				foreach ($this->layer2Detected as $value)
				{
					$totalImpact += $value['impact'];
				}
				$this->insertacl($totalImpact, $attackType);
				foreach($this->layer2Detected as $key => $value) {
					$targetStatus= self :: checkTarget($this->acl_id,'l2', $key, $this->target);
					$filterlevel= self :: getKeyFilterStatus('l2', $key);
						
					if(empty($targetStatus)) {
						$targetid['l2'][]= self :: insertTarget($this->acl_id, 'l2', $key, $this->target, $this->layer2Detected[$key]['value']);
						if($filterlevel > 0 || $this->blockIP==2)  	{
							self :: filterAttack($key, $value['value'], $value['filtersID'], $filterlevel);
						}
					} else {
						if($targetStatus[0]['keyaction'] == 3 || $targetStatus[0]['targetaction'] == 3) {
							$targetid['l2'][]= $targetStatus[0]['id'];
							$pass2 =true;
						} else {
							if($filterlevel > 0 || $this->blockIP==2)  	{
								$action['filter']= true;
								$where [] = ' `aclid` = '.(int)$this->acl_id;
								$where [] = ' `filters` = '.$this->db->Quote($this->layer2Detected[$key]['filtersID'], true);
								$where [] = ' `trimmed_value` = '.$this->db->Quote($this->layer2Detected[$key]['value'], true);
								$where [] = ' `target` = '.$this->db->Quote($this->target, true);
								$l2id = $targetid['l2'][]= self::updateTarget('l2', $where, $targetStatus[0]['id']);
								self :: filterAttack($key, $value['value'], $value['filtersID'], $filterlevel,$l2id);
							} else {
								$targetid['l2'][]= $targetStatus[0]['id'];
							}
						}
					}
						
				}
			}
			else
			{
				$pass2 = true;
			}
			if ($pass1 == true && $pass2 == true)
			{
				return;
			}
			if($totalImpact > $this->threshold) {
				if ($this->blockIP==1)
				{
					if(!empty($this->acl_id)) {
						if(self :: logAttack($this->acl_id, $targetid, $totalImpact)) {
							$ipStatus= self :: checkIPStatus();
							if($ipStatus == '1') {
								self :: send_email($ipStatus, $attackType, $this->acl_id, $targetid, $totalImpact);
								self :: showBanPage();
							}
						}
					}
				}
				elseif ($this->blockIP==2)
				{
					if(!empty($this->acl_id)) {
						if(self :: logAttack($this->acl_id, $targetid, $totalImpact)) {
							$ipStatus= self :: checkIPStatus();
							$_SESSION[$this->ip] ++;
							self :: send_email($ipStatus, $attackType, $this->acl_id, $targetid, $totalImpact);
							if($ipStatus == '1') {
								self :: showBanPage();
							}
							else
							{
								return;
							}
						}
					}
				}
				else
				{
					if(!empty($this->acl_id)) {
						if(self :: logAttack($this->acl_id, $targetid, $totalImpact)) {
							self :: send_email(3, $attackType, $this->acl_id, $targetid, $totalImpact);
							self :: show403("Permission Denied");
						}
					}
				}
			} else {
				if(!empty($this->acl_id)) {
					self :: logAttack($this->acl_id, $targetid, $totalImpact);
				}
				return true;
			}
		}
		private function trimAttack($signature, $target, $targetid=null) {
			$layer1Rules= self :: getLayer1Signatures();
			$signature= $layer1Rules[str_replace(array("SIG[", "]"), "", $signature)];
			if($this->target=='HTTP User Agent')
			{
				$_SERVER['HTTP_USER_AGENT'] = str_replace($signature, "", $_SERVER['HTTP_USER_AGENT']);
			}
			else
			{
				$_SERVER['REQUEST_URI'] = str_replace($signature, "", $_SERVER['REQUEST_URI']);
				$redirect=((!empty($_SERVER['HTTPS'])) ? "https://" : "http://").str_replace($signature, "", $target);
				header('Location: '.$redirect);
			}
		}
		private function filterAttack($key, $value, $filtersID, $filterlevel,$targetid=null) {
			if (file_exists(dirname(__FILE__).OSEDS.'library'.OSEDS.'htmlpurifier'.OSEDS.'HTMLPurifier.includes.php'))
			{
				require_once(dirname(__FILE__).OSEDS.'library'.OSEDS.'htmlpurifier'.OSEDS.'HTMLPurifier.includes.php');
			}
			else
			{
				require_once(dirname(__FILE__).OSEDS.'library'.OSEDS.'hp'.OSEDS.'HTMLPurifier.includes.php');
			}
			$htmlPurifier= new HTMLPurifier();
			if($filterlevel >= 2) {
				$value= $htmlPurifier->purify($value);
			}
			if($filterlevel == 4) {
				$filtersIDs= explode(",", $filtersID);
				$filterSet= $this->storage->getFilterSet();
				foreach($filtersIDs as $filtersID) {
					$value= preg_replace('/'.$filterSet[$filtersID -1]->rule.'/ms', '', $value);
				}
			}
			$key= explode(".", $key);
			switch($key[0]) {
				case "GET" :
					$_GET[$key[1]]= $value;
					break;
				case "POST" :
					$_POST[$key[1]]= $value;
					break;
				case "COOKIE" :
					$_COOKIE[$key[1]]= $value;
					break;
			}
		}
		private function send_email($ipStatus, $attackType, $acl_id, $targetid, $totalImpact) {
			if (class_exists("JConfig"))
			{
				$config_var = new JConfig();
			}
			elseif (class_exists("SConfig"))
			{
				$config_var = new SConfig();
			}
			
			switch($ipStatus) {
				case('1') :
					$type='blacklisted';
					break;
				case('2') :
				case('4') :
					$type='filtered';
					break;
				case('3') :
					$type='403blocked';
					break;
			}
			
			$email = $this->getemailTemplate($type);
			$attackType = implode(",", $attackType);
			$violation = '<ul>';
			if (!empty($this->layer2Detected))
			{
				foreach ($this->layer2Detected as $l2)
				{
					$l2['description'] = explode(',', $l2['description']);
					$violation .= '<li>'.implode('</li><li>', $l2['description']).'</li>';
				}
			}
			if (!empty($this->layer1Detected['trimmed_value']))
			{
				$violation .= '<li>'.$this->layer1Detected['trimmed_value'].'</li>';
			}
			$violation .= '</ul>';
			$ipURL = "<a href='".$config_var->live_site."/administrator/index.php?option=com_ose_antihacker&view=manageips&id=".$acl_id."'>".$acl_id."</a>";
			$email->subject = $email->subject." for [".$_SERVER['HTTP_HOST']."]";
			$email->body = str_replace('[attackType]', $attackType, $email->body);
			$email->body = str_replace('[violation]', $violation, $email->body);
			$email->body = str_replace('[logtime]', $this->logtime, $email->body);
			$email->body = str_replace('[ip]', $this->ip, $email->body);
			$email->body = str_replace('[target]', $this->target, $email->body);
			$email->body = str_replace(array('[referrer]', '[referer]'), $this->referer, $email->body);
			$email->body = str_replace('[aclid]', $ipURL, $email->body);
			$email->body = str_replace('[score]', $totalImpact, $email->body);
				
			$query = " SELECT u.name, u.email FROM `#__users` AS u "
			." INNER JOIN `#__user_usergroup_map` AS g ON g.user_id = u.id"
			." WHERE g.group_id IN ( 8 )  AND sendEmail =1"
			;
			$this->db->setQuery($query);
			$results= $this->db->loadObjectList();

			if (class_exists("JFactory"))
			{
				$mail = JFactory::getMailer();
				if (!empty($results))
				{
					foreach($results as $result) {
//						$email->body = str_replace('[user]', $result->name, $email->body);
//						$mail->addRecipient($result->email);
//						$mail->setSubject($email->subject);
//						$mail->setBody($email->body);
//						$mail->IsHTML(true);
//						$mail->Send();
					}
				}
			}
			else
			{
				require_once(OSE_FRONTPATH.'/libraries/joomla/mail/mail.php');
				require_once(OSE_FRONTPATH.'/libraries/joomla/mail/helper.php');
				$mail = new JMail();
				$mail->From = $config_var->mailfrom;
				$mail->FromName = $config_var->fromname;
				if ($config_var->mailer=='smtp')
				{	
					$mail->useSMTP($config_var->smtpauth, $config_var->smtphost, $config_var->smtpuser, $config_var->smtppass, $config_var->smtpsecure, $config_var->smtpport);
				}
				if (!empty($results))
				{
					foreach($results as $result) {
//						$email->body = str_replace('[user]', $result->name, $email->body);
//						$mail->addRecipient($result->email);
//						$mail->setSubject($email->subject);
//						$mail->setBody($email->body);
//						$mail->IsHTML(true);
//						$mail->Send();
					}
				}
				/*
				$MailFrom = $config_var->mailfrom;
				$FromName = $config_var->fromname;
				$headers = "From: " . $FromName . " <" . $MailFrom . ">\n";
				$headers .= "Reply-To: <" . $MailFrom . ">\n";
				$headers .= "Return-Path: <" . $MailFrom . ">\n";
				$headers .= "Envelope-from: <" . $FromName . ">\n";
				$headers .= "Content-Type: text/html; charset=\"utf-8\"";
				$headers .= "MIME-Version: 1.0\n";
				if (!empty($results))
				{
					foreach ($results as $result)
					{
						$email->body = str_replace('[user]', $result->name, $email->body);
						mail($result->email, $email->subject, $email->body, $headers);
					}
				}
				*/
			}
		}
		function getemailTemplate($type)
		{
			$query = "SELECT `subject`, `body` FROM `#__ose_app_email` WHERE `app` = 'antihack' AND `type` = ".$this->db->Quote($type, true);
			$this->db->setQuery($query);
			$result = $this->db->loadObject();
			return  $result;
		}
		function getStat() {
			$path= OSEREGISTER_DEFAULT_PATH.OSEDS.'antihacker'.OSEDS.'stat.php';
			if(JFile :: exists($path)) {
				require_once($path);
				static $instance;
				if(!$instance instanceof oseAntihackerStat) {
					$instance= new oseAntihackerStat();
				}
				return $instance;
			} else {
				oseExit('Cannot get the Class of oseAntihackerStat');
			}
		}
		function getSysSetting() {
			$path= dirname(__FILE__).OSEDS.'setting.php';
			if(file_exists($path)) {
				require_once($path);
				static $instance;
				if(!$instance instanceof oseAntihackerSysSetting) {
					$instance= new oseAntihackerSysSetting();
				}
				return $instance;
			} else {
				oseExit('Cannot get the Class of oseAntihackerConfig');
			}
		}
		function show403($message)
		{
			header('HTTP/1.1 403 Forbidden');
			echo("<html>
					<head>
						<title>403 Forbidden</title>
					</head>
					<body>
					<p>{$message}</p>
					</body>
					</html>");
			exit;
		}
		function show503($message)
		{
			header('HTTP/1.1 503 Service Unavailable');
			echo("<html>
						<head>
							<title>503 Service Unavailable</title>
						</head>
						<body>
						<p>{$message}</p>
						</body>
						</html>");
			exit;
		}
		private function getMimeType($filename)
		{
			if (!defined('FILEINFO_MIME_TYPE'))
			{
				define('FILEINFO_MIME_TYPE', 1);
			}
			$defined_functions = get_defined_functions();
			if ((in_array('finfo_open', $defined_functions['internal'])) || function_exists('finfo_open'))
			{
				$finfo = finfo_open(FILEINFO_MIME_TYPE); // return mime type ala mimetype extension
				$content_type = finfo_file($finfo, $filename);
				finfo_close($finfo);
				return $content_type;
			}
			elseif (function_exists('mime_content_type'))
			{
				$content_type = mime_content_type($filename); 
				return $content_type; 
			}	 
			else
			{
				return false;
				//self::show403('Finfo is not compiled in your PHP, file type validation function cannot work on your server. Please go to OSE Security backend and remove the file types.');
			}
		}
		private function checkisPHPfile($file)
		{
			if (filesize($file) > '2048000')
			{
				return false; 
			}	
			$data= file($file);
			$data= implode("\r\n", $data);
			$pattern = "/(\<\?)|(\<\?php)/";
			if (preg_match ($pattern, $data))
			{
				return 'application/x-httpd-php';
			}
			else
			{
				return false;
			}	
		}
		private function checkFileTypes($files)
		{
			if(!empty($this->allowExts))
			{
				foreach($files as $file)
				{
					if(!empty($file['tmp_name']))
					{
						if (is_array($file['tmp_name']))
						{
							foreach ($file['tmp_name'] as $filetmp)
							{	
								$file['tmp_name'] = $filetmp;
								break;
							}
						}
						if (is_array($file['type']))
						{
							foreach ($file['type'] as $filetmp)
							{
								$file['type'] = $filetmp;
								break;
							}
						}
						$mimeType= self :: getMimeType($file['tmp_name']);
						if (empty($mimeType))
						{
							$mimeType = self::checkisPHPfile($file['tmp_name']);
						}	
						if (!empty($mimeType))
						{	
							$mimeType= explode("/", $mimeType);
						}
						else
						{
							$mimeType = explode("/", $file['type']);; 
						}
						$ext= explode('/', $file['type']);
						$allowExts= explode(",", trim($this->allowExts));
						$allowExts = array_map('trim', $allowExts);

						if ($ext[1]=='vnd.openxmlformats-officedocument.wordprocessingml.document' && ($mimeType[1] != $ext[1]))
						{
							$ext[1] ='msword'; 
						}	
						
						if($ext[1] != $mimeType[1])
						{
							$acl_id= self :: addBlacklistedIP(array('L3'));
							$targetid['l1'][]= self :: insertTarget($acl_id, 'l3', 'virus', $this->url);
							$totalImpact= 100;
							unlink($_FILES['tmp_name']);
							unset($_FILES);
							if(!empty($acl_id))
							{
								if(self :: logAttack($acl_id, $targetid, $totalImpact))
								{
									self :: send_email('1', array('L3 - Inconsistent File Type'), $acl_id, '', '100');
									self :: show403('Malicious file type detected. Your action has been reported to the server administrator');
								}
							}
						}
						elseif(in_array($mimeType[1], $allowExts)==false)
						{
							self :: show403('The upload of this file type '.$mimeType[1].' is not allowed this website. If you are the server administrator, please add the extensions in the configuraiton in OSE Security first.');
						}
					}
				}
			}
		}
		private function scanFileVirus($files)
		{
			if(!empty($this->scanFileVirus))
			{
				if(file_exists(OSE_ADMINPATH.OSEDS."components".OSEDS."com_ose_cpu".OSEDS."virusscan".OSEDS."virusscan.php"))
				{
					require_once(OSE_ADMINPATH.OSEDS."components".OSEDS."com_ose_cpu".OSEDS."virusscan".OSEDS."virusscan.php");
					if(file_exists(OSE_ADMINPATH.OSEDS."components".OSEDS."com_ose_cpu".OSEDS."filescan".OSEDS."filescan.php"))
					{
						require_once(OSE_ADMINPATH.OSEDS."components".OSEDS."com_ose_cpu".OSEDS."filescan".OSEDS."filescan.php");
					}
					$virScanClass= new oseVirusscan();
					$virScan= $virScanClass->getInstance();
					foreach($files as $file)
					{
						if(!empty($file['tmp_name']))
						{
							$scanResult= $virScan->virusScan($file['tmp_name']);
							if($scanResult == true)
							{
								$acl_id= self :: addBlacklistedIP(array('L3-Virus'));
								$targetid['l1'][]= self :: insertTarget($acl_id, 'l3', 'virus', $this->url);
								$totalImpact= 100;
								unlink($_FILES['tmp_name']);
								unset($_FILES);
								if(!empty($acl_id))
								{
									if(self :: logAttack($acl_id, $targetid, $totalImpact))
								 {
								 	self :: send_email('1', array('Virus File Upload Attempt'), $acl_id, '', '100');
								 	self :: show403('Malicious file type detected. Your action has been reported to the server administrator');
								 }
								}
							}
						}
					}
				}
			}
		}
		private function scanFlooding($decduration= 10, $maxvisits= 5)
		{
			require_once(dirname(__FILE__).OSEDS.'library'.OSEDS.'addons.php');
			$result= OSEAH_Addons :: floodingCheck($decduration, $maxvisits, $this->ip, $this->db);
			if($result == true)
			{
				if ($this->banpagetype==0)
				{
					$acl_id= self :: addBlacklistedIP(array('L4'));
					$targetid['l1'][]= self :: insertTarget($acl_id, 'l4', 'flooding', $this->url);
					$totalImpact= 100;
					if(!empty($acl_id))
					{
						if(self :: logAttack($acl_id, $targetid, $totalImpact))
						{
							self :: send_email('1', array('L4 - Flooding'), $acl_id, '', '100');
							self :: showBanPage();
						}
					}
				}
				else
				{
					self :: show503('Server is temporarily unavailable. Pleaes check back later.');
				}
			}
		}

		public function getRemoteScan($post){
			$path= dirname(__FILE__).OSEDS.'remote.php';
			if(file_exists($path)) {
				require_once($path);
				static $instance;
				if(!$instance instanceof oseAntihackerRemote) {
					$instance= new oseAntihackerRemote($_POST);
				}
				return $instance;
			} else {
				oseExit('Cannot get the Class of oseAntihackerStat');
			}
		}
	}
	?>