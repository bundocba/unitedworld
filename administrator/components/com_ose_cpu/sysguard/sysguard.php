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
class oseSysguard {
	function __construct() {
		jimport('joomla.filesystem.file');
	}
	function ajaxResponse($status, $message, $success= false) {
		$return['status']= $status;
		$return['result']= $message;
		if($success == true) {
			$return['success']= $success;
		}
		else
		{
			$return['errors']= true;
		}
		echo oseJSON :: encode($return);
		exit;
	}
	function checkValue($key, $value){
		if (empty($value))
		{
			self :: ajaxResponse('ERROR', 'The value for '.$key.' cannot be empty', false);
		}
	}
	function getBasicInfo() {
		$return['id']= '1';
		$return['frontPath']= realpath($_SERVER['DOCUMENT_ROOT'].DS).DS;
		$return['frontURL']= JURI::root();
		$return['backPath']= JPATH_ADMINISTRATOR.DS;
		//$htpassfile =  realpath(dirname($_SERVER['DOCUMENT_ROOT'].DS)).DS.'osehtpasswd'.DS.'osehtpasswd';
		$htpassfile= JPATH_ADMINISTRATOR.DS.'osehtpasswd'.DS.'.htpasswd';
		if(file_exists($htpassfile)) {
			$content= JFile :: read($htpassfile);
			$content= explode(":", $content);
			$return['authUser']= $content[0];
			$return['authPass']= "***hidden***";
		} else {
			$return['authUser']= "";
			$return['authPass']= "";
		}
		return $return;
	}
	function createEncryptPass($htpassfile) {
		$authPass= JRequest :: getString('authPass');
		$authUser= JRequest :: getString('authUser');
		self::checkValue('.htpassword Username', $authUser);
		self::checkValue('.htpassword Password', $authPass);
		$encryptedPassword= crypt($authPass, base64_encode($authPass));
		$content= $authUser.":".$encryptedPassword;
		if (!is_writable(dirname($htpassfile)))
		{
			self :: ajaxResponse('ERROR', 'htpassword cannot be written to the folder: '.dirname($htpassfile).", please see this <a href='http://wiki.opensource-excellence.com/index.php?title=How_to_setup_a_.htpassword_in_your_control_panel%3F&action=edit&redlink=1' target='_blank'>WIKI</a>on how to setup a .htpassword in your control panel", true);
		}
		elseif(JFile :: write($htpassfile, $content)) {
			return true;
		} else {
			self :: ajaxResponse('ERROR', JText :: _("Failed creating .htpassword file."));
		}
	}
	function createHTPass() {
		$htpassfile= JPATH_ADMINISTRATOR.DS.'.htpasswd';
		if(!file_exists($htpassfile)) {
			self :: createEncryptPass($htpassfile);
		}
		$backPath= JRequest :: getString('backPath');
		self::checkValue('Backend Path', $backPath);
		if(defined("OSESUITE")) {
			$backPath= dirname(JPATH_ADMINISTRATOR).DS.".htaccess";
		} else {
			$backPath= JPath :: clean($backPath).DS.".htaccess";
		}
		$filepath= JPath :: clean($htpassfile, "/");
		$htaccessContent= "AuthUserFile ".'"'.$filepath.'"'." \n".
						  "AuthName \"Administrator only\"\n".
						  "AuthType Basic \n".
						  "require valid-user\n";
		if ((!file_exists($backPath)) || (is_writable($backPath)))
		{
			if(JFile :: write($backPath, $htaccessContent)) {
				$backPath = dirname($backPath);
				self :: ajaxResponse('Done', 'htpassword successfully created', true);
			} else {
				self :: ajaxResponse('ERROR', 'Failed creating htpassword', false);
			}
		}
		else
		{
			self :: ajaxResponse('ERROR', 'htpassword cannot be written to the folder: '.dirname($htpassfile).", please see this <a href='http://wiki.opensource-excellence.com/index.php?title=How_to_setup_a_.htpassword_in_your_control_panel%3F&action=edit&redlink=1' target='_blank'>WIKI</a>on how to setup a .htpassword in your control panel", true);
		}
	}
	function customizePHPsetting($frontend = false){
		$return = array();
		$phpRuntime = self::getPHPEnv();

		if ($frontend==true)
		{
			$directoryName = 'protected directory';
			$autoPrependFile = '"'.JPATH_ADMINISTRATOR.DS.'scan.php'.'"';
			$allow_url_fopen = 'off';
		}
		else
		{
			$directoryName = 'administrator directory';
			if ($phpRuntime=='mod')
			{
				$autoPrependFile = 'none';
			}
			else
			{
				$autoPrependFile = '';
			}	
			$allow_url_fopen = 'on';
		}
		if ($phpRuntime=='mod')
		{
			$return['htaccess']="#File: .htaccess in your {$directoryName}<br/>".
								"#Parameters added by OSE Security™<br/>".
								"php_value auto_prepend_file {$autoPrependFile} <br/>".
							    "php_flag register_globals off <br/>" .
							    "php_flag safe_mode off <br/>" .
							    "php_flag allow_url_fopen ".$allow_url_fopen." <br/>" .
							    "php_flag display_errors off <br/>" .
							    "php_value disable_functions \"exec,passthru,shell_exec,system,proc_open,curl_multi_exec,show_source\" <br/>";
		}
		else
		{
			$return['phpini'] = ";File: php.ini in your {$directoryName}<br/>".
								";Parameters added by OSE Security™ <br/>".
								"auto_prepend_file= {$autoPrependFile} <br/>" .
							 	"register_globals=off <br/>" .
							 	"safe_mode=off <br/>" .
							 	"allow_url_fopen=".$allow_url_fopen." <br/>" .
							 	"display_errors=off <br/>" .
							 	"disable_functions=\"exec,passthru,shell_exec,system,proc_open,curl_multi_exec,show_source\" <br/>";
		}
		return $return;
	}
	function getlocalPHPINIcontent(){
			// try to get path using phpinfo
			ob_start();
			phpinfo(INFO_GENERAL);
			$phpinfo = ob_get_contents();
			ob_end_clean();

			preg_match_all('#<body[^>]*>(.*)</body>#siU', $phpinfo, $output);
			$output = preg_replace('#<table#', '<table class="adminlist" align="center"', $output[1][0]);
			$output = preg_replace('#(\w),(\w)#', '\1, \2', $output);
			$output = preg_replace('#border="0" cellpadding="3" width="600"#', 'border="0" cellspacing="1" cellpadding="4" width="95%"', $output);
			$output = preg_replace('#<hr />#', '', $output);
			$output = str_replace('<div class="center">', '', $output);
			$output = str_replace('</div>', '', $output);

			preg_match('#<tr><td\s+class="e">Loaded\s+Configuration\s+File\s+</td><td\s+class="v">.*</td></tr>#', $output, $match);

			$loaded_php = str_replace('<tr><td class="e">Loaded Configuration File </td><td class="v">', "", $match[0]);
			$loaded_php = str_replace('</td></tr>', "", $loaded_php);

			// Get Content//
			if(file_exists($loaded_php)) {
				$phpini= file_get_contents($loaded_php);
			} else {
				$phpini= "";
			}
		return $phpini;
	}

	function activateAntiHackerTest() {
		$frontPath= JRequest :: getString('frontPath');
		self::checkValue('Frontend Path', $frontPath);
		$osecheckfile = dirname(__FILE__).DS.'library'.DS.'osecheck.php';
		if (file_exists($frontPath.DS.'osecheck.php'))
		{
			self :: ajaxResponse('Done', 'PHP activation testing file exists.', true);
		}
		elseif (JFile::copy($osecheckfile, $frontPath.DS.'osecheck.php'))
		{
			self :: ajaxResponse('Done', 'Successfully copied activation testing file.', true);
		}
		else
		{
			self :: ajaxResponse('ERROR', 'Failed copying PHP checking file. <br /><br /> Please copy the file <br /><br />'. $osecheckfile.' <br /><br /> to this folder manually: <br /><br />'.$frontPath.DS, false);
		}
	}
	function getPHPEnv() {
		ob_start();
		phpinfo(INFO_GENERAL | INFO_CONFIGURATION | INFO_MODULES);
		$phpinfo= ob_get_contents();
		ob_end_clean();
		preg_match_all('#<body[^>]*>(.*)</body>#siU', $phpinfo, $output);
		$output= preg_replace('#<table#', '<table class="adminlist" align="center"', $output[1][0]);
		$output= preg_replace('#(\w),(\w)#', '\1, \2', $output);
		$output= preg_replace('#border="0" cellpadding="3" width="600"#', 'border="0" cellspacing="1" cellpadding="4" width="95%"', $output);
		$output= preg_replace('#<hr />#', '', $output);
		$output= str_replace('<div class="center">', '', $output);
		$output= str_replace('</div>', '', $output);
		preg_match('#<tr><td\s+class="e">Server\s+API\s+</td><td\s+class="v">.*</td></tr>#', $output, $match);
		preg_match("/(?-i:CGI|FastCGI)/ms", $match[0], $match2);
		if (!empty($match2))
		{
			return "cgi";
		}
		else
		{
			return "mod";
		}
	}
}