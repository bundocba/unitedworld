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
/* no direct access
This class deals with logging, and generating statistics for OSE tables;
*/
if (!defined('_JEXEC') && !defined('OSE_ADMINPATH'))
{
	die("Direct Access Not Allowed");
}
class oseAntihackerStat extends oseAntihacker{
	var $total = 0;
	private $db= null;
	function __construct() {
		if (class_exists("athDB") && !defined('OSEANTIHACKERVER'))
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
	function ajaxResponse($status, $message)
	{
		$return['status'] = $status;
		$return['result'] = $message;
		echo $this->json->encode($return);
		exit;
	}
	function checkIDs($ids)
	{
		if (empty($ids))
		{
			self::ajaxResponse("ERROR", "Please select the at least one item.");
		}
	}
	function viewAttack()
	{
		$acl_id = JRequest::getInt('id');
		self::checkIDs($acl_id);
		
		require_once(dirname(__FILE__).OSEDS.'library'.OSEDS.'Storage.php');
		$filters= array();
		$this->storage= new IDS_Filter_Storage();
		$filterSet= $this->storage->getFilterSet();
		$html = "<table width='100%' class='stat'>";
		$db = $this->db;
		$query = "SELECT acl.*, alert.l1ruleids, alert.l2ruleids, alert.datetime, alert.score, alert.referer FROM `#__oseipm_acl` AS acl, `#__oseath_alerts` AS alert WHERE acl.id = ".(int)$acl_id." AND alert.aclid = acl.id";
		$db->setQuery($query);
		$results = $db->loadObjectlist();
		if (empty($results))
		{
			$html .= "<tr><td class='label'>".JText::_('Result')."</td><td class='attackcontent'>".JText::_("No attack information found")."</td></tr>";
		}
		else
		{
			$layer1Signatures = self::getLayer1Signatures();
			foreach ($results as $result)
			{
				$html .= "<tr><td><div class='label'>".JText::_('IP Rule ID')."</div></td><td class='attackcontent'>".$result ->id."</td></tr>";
				$html .= "<tr><td><div class='label'>".JText::_('Logged Time')."</div></td><td class='attackcontent'>".$result ->datetime."</td></tr>";
				$html .= "<tr><td><div class='label'>".JText::_('Referer')."</div></td><td class='attackcontent'>".$result ->referer."</td></tr>";
				$l1rulesids= $this->json->decode($result ->l1ruleids);
				$layer1Attack = self::getlayer1Attacks($layer1Signatures, $l1rulesids);

				if (!empty($layer1Attack))
				{
					foreach ($layer1Attack as $layer1AttackInfo)
					{
						if (!isset($layer1AttackInfo['trimmed_value']))
						{
							$layer1AttackInfo['trimmed_value'] = '';
						}
						$html .= "<tr><td width='95px'><div class='label'>".JText::_('Layer 1 Attack')."</div></td><td class='attackcontent'>";
						$html .= "<ul class='parent'><li>".JText::_('Signature').":".$layer1AttackInfo['signature']."</li>";
						$html .= "<li>".JText::_('Signature Action').":".$layer1AttackInfo['signatureaction']."</li>";
						$html .= "<li>".JText::_('Target').":".$layer1AttackInfo['target']."</li>";
						$html .= "<li>".JText::_('Target Action').":".$layer1AttackInfo['targetaction']."</li>";
						$html .= "<li><ul class='child'><li>".JText::_('Detected Singature').": ".$layer1AttackInfo['trimmed_value']."</li></ul></li>";
						$html .= "</td></tr>";
					}
				}

				$l2rulesids= $this->json->decode($result ->l2ruleids);
				$layer2Attack = self::getlayer2Attacks($l2rulesids);

				if (!empty($layer2Attack))
				{
					foreach ($layer2Attack as $layer2AttackInfo)
					{
						if (!isset($layer2AttackInfo['trimmed_value']))
						{
							$layer2AttackInfo['trimmed_value'] = '';
						}
						$html .= "<tr><td width='95px'><div class='label'>".JText::_('Layer 2 Attack')."</div></td><td class='attackcontent'>";
						$html .= "<ul class='parent'><li>".JText::_('Key').": ".$layer2AttackInfo['key']."</li>";
						$html .= "<li>".JText::_('Action').": ".$layer2AttackInfo['keyaction']."</li>";
						$html .= "<li>".JText::_('Target').": ".$layer2AttackInfo['target']."</li>";
						$html .= "<li>".JText::_('Target Action').": ".$layer2AttackInfo['targetaction']."</li>";
						$html .= "<li>".JText::_('Trimmed Value').": ".$layer2AttackInfo['trimmed_value']."</li>";
						
						$filters = explode(',', $layer2AttackInfo['filters']);
						if (!is_array($filters))
						{
							$filters = array($filters);
						}
						$attacks = array();
						foreach ($filters as $filter)
						{
							$filter = trim($filter); 
							if (strlen($filter)>2)
							{
								$filtera = substr($filter, 0, 2); 
								$filterb = substr($filter, 2, strlen($filter));
								$attacks[]= $filterSet[$filtera-1]->description;
								$attacks[]= $filterSet[$filterb-1]->description;
							}
							else 
							{
								$attacks[]= $filterSet[$filter-1]->description;
							}
						}
						$attacks = "<ul class='child'><li>".implode("</li><li>", $attacks)."</li></ul>";
						
						$html .= "<li>".JText::_('Attack Types').$attacks."</li>";
						$html .= "</td></tr>";
					}
				}
			}
		}
		$html .= "</table>";
		$return['result'] = $html;
		echo $this->json->encode($return); exit;
	}
	function viewAttackDetail()
	{
		require_once(dirname(__FILE__).OSEDS.'library'.OSEDS.'Storage.php');
		$filters= array();
		$this->storage= new IDS_Filter_Storage();
		$filterSet= $this->storage->getFilterSet();
		
		$rule_id = JRequest::getInt('id');
		$layer = JRequest::getCmd('layer');
		self::checkIDs($rule_id);
		$html = "<table width='100%' class='stat'>";
		$db= $this->db;
		if ($layer =='l1')
		{
			$table ="`#__oseath_l1rules`";
		}
		else
		{
			$table ="`#__oseath_l2rules`";
		}
		$layer1Signatures = self::getLayer1Signatures();
		$where= array();
		$where[]= " `id` = ".$rule_id;
		$where=(count($where) ? ' WHERE ('.implode(') AND (', $where).')' : '');
		$query= " SELECT * FROM {$table} ". $where;
		$this->total= self::getAttackListTotal($query, "*", "count(*)");
		$start= JRequest :: getInt('start', 0);
		$limit= JRequest :: getInt('limit', 10);
		$db->setQuery($query, $start, $limit);
		$rows= $db->loadAssocList();
		foreach ($rows as $key =>$row)
		{
			if (isset($rows[$key]['signature']))
			{
				$sigID = str_replace(array("SIG[", "]"), "", $rows[$key]['signature']);
				$html .= "<tr><td width='95px'><div class='label'>".JText::_('Layer 1 Signature')."</div></td><td class='attackcontent'>".$rows[$key]['signature']."-".htmlentities ($layer1Signatures[$sigID])."</td></tr>";
				$html .= "<tr><td width='95px'><div class='label'>".JText::_('Action')."</div></td><td class='attackcontent'>".self::transActionValue($rows[$key]['signatureaction'])."</td></tr>";
			}
			if (isset($rows[$key]['key']))
			{
				$html .= "<tr><td width='95px'><div class='label'>".JText::_('Layer 2 Key')."</div></td><td class='attackcontent'>".$rows[$key]['key']."</td></tr>";
				$html .= "<tr><td width='95px'><div class='label'>".JText::_('Action')."</div></td><td class='attackcontent'>".self::transActionValue($rows[$key]['keyaction'])."</td></tr>";
			}
			$html .= "<tr><td width='95px'><div class='label'>".JText::_('Target')."</div></td><td class='attackcontent'>".htmlentities ($rows[$key]['target'])."</td></tr>";
			$html .= "<tr><td width='95px'><div class='label'>".JText::_('Target Action')."</div></td><td class='attackcontent'>".self::transActionValue($rows[$key]['targetaction'], true)."</td></tr>";
			if (isset($rows[$key]['filters']))
			{
				$filters = explode(',', $rows[$key]['filters']);
				if (!is_array($filters))
				{
					$filters = array($filters);
				}
				$attacks = array();
				foreach ($filters as $filter)
				{
					$filter = trim($filter);
					if (strlen($filter)>2)
					{
						$filtera = substr($filter, 0, 2);
						$filterb = substr($filter, 2, strlen($filter));
						$attacks[]= $filterSet[$filtera-1]->description;
						$attacks[]= $filterSet[$filterb-1]->description;
					}
					else
					{
						if (!empty($filter))
						{	
							$attacks[]= $filterSet[$filter-1]->description;
						}
					}
				}
				$attacks = "<ul class='child'><li>".implode("</li><li>", $attacks)."</li></ul>";
				
				$html .= "<tr><td width='95px'><div class='label'>".JText::_('Filters')."</div></td><td class='attackcontent'>".$attacks."</td></tr>";
			}
			if (isset($rows[$key]['trimmed_value']))
			{
				$html .= "<tr><td width='95px'><div class='label'>".JText::_('Trimmed Value')."</div></td><td class='attackcontent'><div class='child'>".htmlentities($rows[$key]['trimmed_value'])."</div></td></tr>";
			}
		}
		$html .= "</table>";
		$return['result'] = $html;
		echo $this->json->encode($return); exit;
	}
	function getlayer1Attacks($layer1Signatures, $l1rulesids)
	{
		$return = array();
		if (!empty($l1rulesids))
		{
			$i = 0;
			foreach ($l1rulesids as $l1rulesid)
			{
				$query = " SELECT * FROM `#__oseath_l1rules` WHERE id = ". (int)$l1rulesid;
				$this->db->setQuery($query);
				$results = $this->db->loadObject();
				$signature = str_replace(array("SIG[", "]"), "", $results->signature);
				$return[$i]['signature'] = htmlentities($layer1Signatures[$signature]);
				$return[$i]['target'] = htmlentities($results->target);
				$return[$i]['signatureaction'] = self::transActionValue($results->signatureaction);
				$return[$i]['targetaction'] = self::transActionValue($results->targetaction);
				$return[$i]['trimmed_value'] = $results->trimmed_value;
				$i++;
			}
			/*
			$return['signatures'] = implode(",", $return['signatures']);
			$return['target'] = implode(",", $return['target']);
			$return['signatureaction'] = implode(",", $return['signatureaction']);
			$return['targetaction'] = implode(",", $return['targetaction']);
			*/
		}
		return $return;
	}

	function getAttackList($layer) {
		// initialize variables
		$db= $this->db;
		if ($layer =='l1')
		{
			$table ="`#__oseath_l1rules`";
		}
		else
		{
			$table ="`#__oseath_l2rules`";
		}
		$layer1Signatures = self::getLayer1Signatures();
		$where= array();
		$search= JRequest :: getString('search', null);
		if($search) {
			$q = $db->Quote('%'.$search.'%', true);
			$where[]= " target LIKE ".$q. " ";
		}
		$where=(count($where) ? ' WHERE ('.implode(') AND (', $where).')' : '');
		$query= " SELECT * FROM {$table} ". $where;
		$this->total= self::getAttackListTotal($query, "*", "count(*)");
		$start= JRequest :: getInt('start', 0);
		$limit= JRequest :: getInt('limit', 10);
		$db->setQuery($query, $start, $limit);
		$rows= $db->loadAssocList();
		foreach ($rows as $key =>$row)
		{
			if (isset($rows[$key]['signature']))
			{
				$sigID = str_replace(array("SIG[", "]"), "", $rows[$key]['signature']);
				$rows[$key]['signature'] = $rows[$key]['signature']."-".htmlentities ($layer1Signatures[$sigID]);
				$rows[$key]['signatureaction'] = self::transActionValue($rows[$key]['signatureaction']);
			}
			if (isset($rows[$key]['keyaction']))
			{
				$rows[$key]['keyaction'] = self::transActionValue($rows[$key]['keyaction']);
			}
			$rows[$key]['key'] = (isset($rows[$key]['key']))?htmlentities($rows[$key]['key']):'';
			$rows[$key]['target'] = htmlentities ($rows[$key]['target']);
			$rows[$key]['targetaction'] = self::transActionValue($rows[$key]['targetaction'], true);
			$rows[$key]['view'] = "<a href='#' onClick= 'viewdetail(".urlencode($row['id']).")' ><img src='components/com_ose_antihacker/assets/images/page_white_magnify.png' /></a>";
		}
		return $rows;
	}

	function getAttackListTotal($query, $needle, $replace)
	{
		$db= $this->db;
		$query = str_replace($needle, $replace, $query);
		$db->setQuery($query);
		return $db->loadResult();
	}

	function getlayer2Attacks($l2rulesids)
	{
		$db = $this->db;
		$return = array();
		if (!empty($l2rulesids))
		{
			$i = 0;
			foreach ($l2rulesids as $l2rulesid)
			{
				$query = " SELECT * FROM `#__oseath_l2rules` WHERE id = ". (int)$l2rulesid;
				$db->setQuery($query);
				$results = $db->loadObject();
				$return[$i]['key'] = htmlentities($results->key);
				$return[$i]['target'] = htmlentities($results->target);
				$return[$i]['keyaction'] = self::transActionValue($results->keyaction);
				$return[$i]['targetaction'] = self::transActionValue($results->targetaction);
				$return[$i]['trimmed_value'] = htmlentities($results->trimmed_value);
				$return[$i]['filters'] = $results->filters;
				
				$i++;
			}
		}
		return $return;
	}
	function transActionValue($value, $target=false)
	{
		switch($value)
		{
			case '0':
			return JText::_("N/A");
			break;
			case '1':
			if ($target==true)
			{
				return JText::_("Blocked");
			}
			else
			{
				return JText::_("Blocked");
			}
			break;
			case '2':
			return JText::_("Filtered");
			break;
			case '3':
			return JText::_("Ignored");
			break;
			case '4':
			return JText::_("Strictly Filtered");
			break;
		}

	}
	function updateSignature($status, $layer)
	{
		$ids = JRequest::getVar('ids');
		$ids = $this->json->decode($ids);
		$db = $this->db;
		self::checkIDs($ids);
		$return = true;
		foreach ($ids as $id )
		{
			if ($layer=='l1')
			{
				$fieldname = "`signature`";
				$fieldactionname = "`signatureaction`";
				$returntitle = JText::_("Signature");
				$table = "`#__oseath_l1rules`";
			}
			if ($layer=='l2')
			{
				$fieldname = "`key`";
				$fieldactionname = "`keyaction`";
				$returntitle = JText::_("Key");
				$table = "`#__oseath_l2rules`";
			}
			if ($status==1)
			{
				$query = " SELECT {$fieldname} FROM {$table} WHERE `id` = ". (int)$id;
				$db ->setQuery($query);
				$result = $db->loadResult();
				$query = " UPDATE {$table} SET $fieldactionname = ". (int)$status. " WHERE `id` = ". (int)$id;
				$db ->setQuery($query);
				if (!$db->query())
				{
					self::ajaxResponse("ERROR", $db->getErrorMsg());
					$return = false;
				}
				else
				{
					$return = true ;
				}
			}
			elseif ($status==2 || $status==3 || $status==4)
			{
				$query = " SELECT * FROM {$table} WHERE `id` = ". (int)$id;
				$db ->setQuery($query);
				$obj = $db->loadObject();
				$keyvalue = ($layer=='l1')?$obj->signature:$obj->key;

				$query = " SELECT * FROM {$table} WHERE {$fieldactionname} = {$status} AND `id` = ". (int)$id;
				$db ->setQuery($query);
				$obj = $db->loadObject();

				if (empty($obj))
				{
					$query = " UPDATE {$table} SET $fieldactionname = ". (int)$status. " WHERE `id` = ". (int)$id;
					$db ->setQuery($query);
					if (!$db->query())
					{
						self::ajaxResponse("ERROR", $db->getErrorMsg());
						$return = false;
					}
					else
					{
						$query = " SELECT id FROM {$table} WHERE {$fieldname} ='{$keyvalue}' and `id` != ". (int)$id;
						$db ->setQuery($query);
						$results2 = $db->loadObjectList();
						if (!empty($results2))
						{
							foreach ($results2 as $result2)
							{
								$query = " DELETE FROM {$table} WHERE `id` = ". (int)$result2->id;
								$db->setQuery($query);
								if (!$db->query())
								{
									self::ajaxResponse("ERROR", $db->getErrorMsg());
									$return = false;
								}
								else
								{
									$return =true;
								}
							}
						}
						else
						{
							$return =true;
						}
					}
				}
				else
				{
					$return =true;
				}



			}

		}
		if ($return ==true)
		{
			self::ajaxResponse("Done", JText::_("The action for the")." ".$returntitle. " ". JText::_("is successfully updated."));
		}
	}
	function updateTargetBackend($status, $layer)
	{
		$ids = JRequest::getVar('ids');
		$ids = $this->json->decode($ids);
		$db = $this->db;
		self::checkIDs($ids);
		$result = true;
		foreach ($ids as $id )
		{
			$fieldname = "`target`";
			$fieldactionname = "`targetaction`";
			$returntitle = JText::_("Target");

			if ($layer=='l1')
			{
				$table = "`#__oseath_l1rules`";
			}
			if ($layer=='l2')
			{
				$table = "`#__oseath_l2rules`";
			}

			$query = " SELECT {$fieldname} FROM {$table} WHERE `id` = ". (int)$id;
			$db ->setQuery($query);
			$result = $db->loadResult();
			if (!empty($result))
			{
				$query = " UPDATE {$table} SET $fieldactionname = ". (int)$status. " WHERE $fieldname = ".$db->Quote($result)." AND id = ". (int)$id;
				$db ->setQuery($query);
				if (!$db->query())
				{
					self::ajaxResponse("ERROR", $db->getErrorMsg());
					$result = false;
				}
			}
			else
			{
				$result = true;  
			}
		}
		if ($result ==true)
		{
			self::ajaxResponse("Done", JText::_("The action for the")." ".$returntitle. " ". JText::_("is successfully updated."));
		}
	}
	function getBlacklistedSummary()
	{
		$db = $this->db;
		$date = oseHTML::getDateTime();
		$query = "SELECT  DATE(alert.datetime) as date, count(acl.id) as attacks, acl.status as status " .
				" FROM `#__oseipm_acl` as acl, `#__oseath_alerts` as alert " .
				" WHERE acl.id =alert.aclid AND (acl.status = 1 OR acl.status = 2) " .
				" AND DATEDIFF( '{$date}', alert.datetime ) <= 10 " .
				" GROUP BY DATE(alert.datetime), acl.status";

		$db->setQuery($query);
		$results = $db->loadObjectlist();
		$return = array();
		foreach ($results as $result)
		{
			if ($result->status ==1)
			{
				$return[$result->date][0]=$result->attacks;
			}
			else
			{
				$return[$result->date][1]=$result->attacks;
			}
		}
		// re-organize
		$results =array();
		$i=0;
		foreach ($return as $key=>$value)
		{
			if (!is_array($value))
			{
				$value = array($value);
			}
			$results[$i]['name']=$key;
			$results[$i]['attacks'] = (isset($value[0]))?$value[0]:0;
			$results[$i]['attacks2'] =(isset($value[1]))?$value[1]:0;
			$i++;
		}	
		echo $this->json->encode($results); exit;
	}
	function removeRules($layer)
	{
		$ids = JRequest::getVar('ids');
		$ids = $this->json->decode($ids);
		$db = $this->db;
		self::checkIDs($ids);
		$result = true;
		foreach ($ids as $id )
		{
			$returntitle = JText::_("Target");

			if ($layer=='l1')
			{
				$table = "`#__oseath_l1rules`";
			}
			if ($layer=='l2')
			{
				$table = "`#__oseath_l2rules`";
			}

			$query = " DELETE FROM {$table} WHERE id = ". (int)$id;
			$db ->setQuery($query);
			if (!$db->query())
			{
				self::ajaxResponse("ERROR", $db->getErrorMsg());
				$result = false;
			}
		}
		if ($result ==true)
		{
			self::ajaxResponse("Done", "Remove Successfully");
		}
	}
}