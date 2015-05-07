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
class oseAntihackerSysSetting extends oseAntihacker{
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
	function ajaxResponse($status, $message, $success=false)
	{
		$return['status'] = $status;
		$return['result'] = $message;
		if ($success==true)
		{$return['success'] = $success;}
		echo $this->json->encode($return);
		exit;
	}
	function getConfiguration()
	{
		$db= $this->db;
		$return = array();
		$return['id'] = 1;
		$query= "SELECT * FROM `#__ose_secConfig`";
		$db->setQuery($query);
		$results=$db->loadObjectlist();
		foreach ($results as $obj)
		{
			$return[$obj->key] = (!empty($obj->value))?$obj->value:'';
		}
		return $return;
	}
	function saveConfiguration()
	{
		//print_r($_POST); exit; 
		$data = JRequest::get('post');
		$type = $data['type']; 
		// Unset a couple of variables first;
		unset($data['option']);
		unset($data['task']);
		unset($data['controller']);
		unset($data['type']);
		// Reset customBanpage variable;
		$data['customBanpage']  = JRequest::getVar('customBanpage', null,'post','string', JREQUEST_ALLOWHTML);
		if (empty($data['customBanpage']))
		{
			unset($data['customBanpage']); 
		}
		$finfoEnabled = $this ->checkFileINFO();
		if (isset($data['allowExts']))
		{
			if (!empty($data['allowExts']))
			{
				if ($finfoEnabled == false)
				{
					//$data['allowExts'] = false; 
				}
			}
			else
			{
				$data['allowExts'] = false; 
			}
		}
		$db = $this->db;
		$result = true;
		foreach ($data as $key =>$value)
		{
			$query = "SELECT `id` FROM `#__ose_secConfig` WHERE `key`  = " . $db->Quote($key, true);
			$db->setQuery ($query);
			$id = $db->loadResult();
			if (empty($id))
			{
				$query =" INSERT INTO `#__ose_secConfig` (`id`, `key`, `value`, `type`) ".
						" VALUES (NULL, ".$db->Quote($key, true).", ".$db->Quote($value, true).", ".$db->Quote($type).");";
			}
			else
			{
				$query =" UPDATE `#__ose_secConfig` SET `value` = ".$db->Quote($value, true).
						" , `type` = ".$db->Quote($type).
						" WHERE `id` = ".(int)$id.";";
			}
			$db->setQuery($query);
			if (!$db->query())
			{
				self::ajaxResponse("ERROR", $db->getErrorMsg());
				$result = false;
			}
		}
		if ($result ==true)
		{
			unset($db);
			unset($query);
			unset($result);
			if ($finfoEnabled==true)
			{
				self::ajaxResponse("SUCCESS", JText::_("CONF_UPDATE_SUCCESS"), true);
			}
			else
			{
				self::ajaxResponse("SUCCESS", JText::_("CONF_UPDATE_SUCCESS_BUT_FINFO_NOT_INSTALLED"), true);
			}
		}
	}
	function checkFileINFO()
	{
		$defined_functions = get_defined_functions();
		if ((in_array('finfo_open', $defined_functions['internal'])) || function_exists('finfo_open'))
		{
			return true; 
		}
		else
		{
			return false; 
		}
	}
}
?>
