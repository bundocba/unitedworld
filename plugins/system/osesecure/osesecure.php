<?php
/**
  * @version     3.0 +
  * @package     Open Source PHP Anti-Hacker Suite
  * @subpackage  Open Source PHP Anti-Hacker for Joomla - com_scanner
  * @author      Open Source Excellence {@link http://www.opensource-excellence.co.uk}
  * @author      SSRRN {@link http://www.ssrrn.com}
  * @author      Created on 15-Sep-2008
  * @author      Updated on 16-Apr-2011
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
  *  @Copyright Copyright (C) 2010- ... author-name
*/
defined('_JEXEC') or die("Direct Access Not Allowed");
jimport('joomla.plugin.plugin');
class plgSystemOsesecure extends JPlugin
{
	var $_db= null;
	var $whitelistvars= null;
	function plgSystemOsesecure(& $subject, $config)
	{
		parent :: __construct($subject, $config);
	}
	function onAfterInitialise()
	{
		$mainframe= JFactory :: getApplication('SITE');
		$pluginParams= $this->params;
		// Backend Protection
		if($pluginParams->get('enableSecureKey') == true)
		{
			self :: checkSecureKey($pluginParams);
		}
		// Get Whitelisted Variable;
		$whitelistvars= $pluginParams->get('whitelistvars');
		$whitelistvars= explode("\n", $whitelistvars);
		$this->whitelistvars= $whitelistvars;
		$user= JFactory :: getUser();
		if($mainframe->isAdmin())
		{
			return; // Dont run in admin
		}
		if($pluginParams->get('enableAntihacker') == true)
		{
			self :: scanWithAntihacker();
		}
		if($pluginParams->get('checkMUA') == true)
		{
			self :: checkMUA();
		}
		if($pluginParams->get('checkDFI') == true)
		{
			self :: checkDFI();
		}
		if($pluginParams->get('checkJSInjection') == true)
		{
			self :: checkJSInjection();
		}
		if($pluginParams->get('checkSQLInjection') == true)
		{
			self :: checkSQLInjection();
		}
		return true;
	}
	private function checkSecureKey($pluginParams)
	{
		$user= JFactory :: getUser();
		$session= JFactory :: getSession();
		$secureKey= $session->get('oseSecureAuthentication');
		if(empty($secureKey))
		{
			if((preg_match("/administrator\/*index.?\.php$/", $_SERVER['SCRIPT_NAME'])))
			{
				if(!$user->id && $pluginParams->get('secureKey') != $_SERVER['QUERY_STRING'])
				{
					self :: redirect();
				}
				else
				{
					$session->set('oseSecureAuthentication', 1);
				}
			}
		}
	}
	function onAfterDispatch()
	{}
	private function scanWithAntihacker()
	{
		if(file_exists(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_ose_cpu'.DS.'antihacker'.DS.'antihacker.php') && file_exists(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_ose_cpu'.DS.'define.php') && !file_exists(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_ose_antihacker'.DS.'installer.dummy.ini'))
		{
			define ('OSE_ADMINPATH', JPATH_ADMINISTRATOR);
			define ('OSE_FRONTPATH', JPATH_SITE);
			define('OSEDS', DIRECTORY_SEPARATOR);
			require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_ose_cpu'.DS.'define.php');
			require_once(OSECPU_B_PATH.DS.'oseregistry'.DS.'oseregistry.php');
			oseRegistry :: register('registry', 'oseregistry');
			oseRegistry :: call('registry');
			oseRegistry :: register('antihacker', 'antihacker');
			$antihacker= oseRegistry :: call('antihacker');
			$antihacker->hackScan();
		}
	}
	// Basic function - Checks Malicious User Agent
	private function checkMUA()
	{
		// Some PHP binaries don't set the $_SERVER array under all platforms
		if(!isset($_SERVER))
		{
			return;
		}
		if(!is_array($_SERVER))
		{
			return;
		}
		// Some user agents don't set a UA string at all
		if(!array_key_exists('HTTP_USER_AGENT', $_SERVER))
		{
			return;
		}
		$mua= $_SERVER['HTTP_USER_AGENT'];
		$detected= false;
		if(strstr($mua, '<?'))
		{
			$detected= true;
		}
		$patterns= array('#c0li\.m0de\.0n#', '#libwww-perl#', '#<\?(.*)\?>#', '#curl#', '#^Mozilla\/5\.0$#', '#^Mozilla$#', '#^Java#');
		foreach($patterns as $i => $pattern)
		{
			// libwww-perl fix for w3c
			if($i == 1)
			{
				if(preg_match($pattern, $mua) && !preg_match('#^W3C-checklink#', $mua))
				{
					$detected= true;
				}
				continue;
			}
			if(preg_match($pattern, $mua))
			{
				$detected= true;
			}
		}
		unset($patterns);
		if($detected == true)
		{
			self :: redirect();
		}
	}
	// Basic function - Checks Direct Files Inclusion attack
	private function checkDFI()
	{
		$request= array('get', 'post');
		foreach($request as $scanVar)
		{
			$allVars= JRequest :: get($scanVar, 2);
			if(empty($allVars))
				continue;
			if(self :: DFImathched($allVars))
			{
				self :: redirect();
			}
		}
	}
	private function DFImathched($array)
	{
		$result= false;
		if(is_array($array))
		{
			foreach($array as $key => $value)
			{
				if(!in_array($key, $this->whitelistvars))
				{
					continue;
				}
				// If there's a null byte in the key, break
				if(strstr($key, "\u0000"))
				{
					$result= true;
					break;
				}
				// If there's no value, treat the key as a value
				if(empty($value))
				{
					$value= $key;
				}
				// Scan the value
				if(is_array($value))
				{
					$result= self::DFImathched($value);
				}
				else
				{
					// If there's a null byte, break
					if(strstr($value, "\u0000"))
					{
						$result= true;
						break;
					}
					// If the value starts with a /, ../ or [a-z]{1,2}:, block
					if(preg_match('#^(/|\.\.|[a-z]{1,2}:\\\)#i', $value))
					{
						// Fix 2.0.1: Check that the file exists
						$result= @ file_exists($value);
						break;
					}
					if($result)
					{
						break;
					}
				}
			}
		}
		return $result;
	}
	// Basic function - Checks Remote Files Inclusion attack
	private function checkRFI()
	{
		$request= array('get', 'post');
		$regex= '#(http|ftp){1,1}(s){0,1}://.*#i';
		foreach($request as $scanVar)
		{
			$allVars= JRequest :: get($scanVar, 2);
			if(empty($allVars))
				continue;
			if(self :: RFImathched($regex, $allVars))
			{
				self :: redirect();
			}
		}
	}
	private function RFImathched($regex, $array)
	{
		$result= false;
		if(is_array($array))
		{
			foreach($array as $key => $value)
			{
				if(in_array($key, $this->whitelistvars))
				{
					continue;
				}
				if(is_array($value))
				{
					$result= self :: RFImathched($regex, $value);
				}
				else
				{
					$result= preg_match($regex, $value);
				}
				if($result)
				{
					// Can we fetch the file directly?
					$fContents= @ file_get_contents($value);
					if(!empty($fContents))
					{
						$result=(strstr($fContents, '<?php') !== false);
						if($result)
							break;
					}
					else
					{
						$result= false;
					}
				}
			}
		}
		elseif(is_string($array))
		{
			$result= preg_match($regex, $array);
			if($result)
			{
				// Can we fetch the file directly?
				$fContents= @ file_get_contents($array);
				if(!empty($fContents))
				{
					$result=(strstr($fContents, '<?php') !== false);
					if($result)
					{
						break;
					}
				}
				else
				{
					$result= false;
				}
			}
		}
		return $result;
	}
	private function checkDoS()
	{
		// Check if it comes from PayPal
		if(!empty($_POST['txn_type']) || !empty($_POST['txn_id']))
		{
			return;
		}
		if(empty($_SERVER['HTTP_USER_AGENT']) || $_SERVER['HTTP_USER_AGENT'] == '-' || !isset($_SERVER['HTTP_USER_AGENT']))
		{
			self :: redirect();
		}
	}
	private function checkJSInjection()
	{
		$request= array('get', 'post');
		foreach($request as $scanVar)
		{
			$allVars= JRequest :: get($scanVar, 2);
			foreach($allVars as $element => $value)
			{
				if(empty($value))
				{
					continue;
				}
				if(!is_string($value))
				{
					continue;
				}
				if(preg_match("#<script[^>]*\w*\"?[^>]*>#is", $value))
				{
					self :: redirect();
				}
			}
		}
		return false;
	}
	private function checkSQLInjection()
	{
		$request= array('get', 'post');
		$JConfig= new JConfig();
		$dbprefix= $JConfig->dbprefix;
		$option= JRequest :: getCmd('option');
		foreach($request as $scanVar)
		{
			$allVars= JRequest :: get($scanVar, 2);
			foreach($allVars as $element => $value)
			{
				$commonSQLInjWords= array('union', 'union select', 'insert', 'from', 'where', 'concat', 'into', 'cast', 'truncate', 'select', 'delete', 'having');
				if(empty($value))
				{
					continue;
				}
				if(!is_string($value))
				{
					continue;
				}
				// First scanning
				if(preg_match('#[\d\W](union select|union join|union distinct)[\d\W]#is', $value))
				{
					self :: redirect();
				}
				// Check for the database name and an SQL command in the value
				if(preg_match('#[\d\W]('.implode('|', $commonSQLInjWords).')[\d\W]#is', $value) && preg_match('#'.$dbprefix.'(\w+)#s', $value) && $option != 'com_search')
				{
					self :: redirect();
				}
			}
		}
		return false;
	}
	function redirect()
	{
		$mainframe= JFactory :: getApplication('SITE');
		$pluginParams= $this->params;
		$sefroutemethod= $pluginParams->get('sefroutemethod');
		$redmenuid= $this->params->def('redmenuid', '0');
		$redmessage= $this->params->def('redmessage', 'Bad Behaviour Found!');
		$redmessage=(!empty($message)) ? $message : $redmessage;
		if(!empty($redmenuid))
		{
			$db= JFactory :: getDBO();
			$query= "SELECT * FROM `#__menu` WHERE `id` = ".(int) $redmenuid;
			$db->setQuery($query);
			$menu= $db->loadObject();
			switch($sefroutemethod)
			{
				default :
				case 0 :
					$redURL= JURI :: root().$menu->link."&Itemid=".$menu->id;
					break;
				case 1 :
					$redURL= JRoute :: _(JURI :: root().$menu->link."&Itemid=".$menu->id);
					break;
				case 2 :
					$redURL= JRoute :: _(JURI :: root().$menu->alias);
					break;
			}
		}
		else
		{
			$redURL= JURI :: root().'index.php';
		}
		$redirect= str_replace("&amp;", "&", JRoute :: _($redURL));
		$mainframe->redirect($redirect, $redmessage);
	}
}
?>
