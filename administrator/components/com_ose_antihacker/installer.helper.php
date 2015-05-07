<?php
/**
  * @version     3.0 +
  * @package     Open Source Security Suite
  * @author      Open Source Excellence (R) {@link  http://www.opensource-excellence.com}
  * @author      Created on 17-May-2011
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
  *  @Copyright Copyright (C) 2010- Open Source Excellence (R)
*/
defined('_JEXEC') or die("Direct Access Not Allowed");
class oseInstallerHelper
{
	var $backendPath;
	var $frontendPath;
	var $cpuFile;
	var $successStatus;
	var $failedStatus;
	var $notApplicable;
	var $totalStep;
	var $pageTitle;
	var $verifier;
	var $dbhelper;
	var $template;
	var $component;
	var $frontendCPUPath;
	function __construct()
	{
		jimport('joomla.application.component.controller');
		jimport('joomla.application.component.model');
		jimport('joomla.installer.installer');
		jimport('joomla.installer.helper');
		jimport('joomla.filesystem.file');
		jimport('joomla.filesystem.folder');
		jimport('joomla.filesystem.archive');
		jimport('joomla.filesystem.path');
		$this->component= 'com_ose_antihacker';
		$this->com_title= OSEATHTITLE. '-'. OSEANTIHACKERVER;
		$this->cpuFile = 'cpuATH.zip';
		$this->backendPath= JPATH_ROOT.DS.'administrator'.DS.'components'.DS.$this->component.DS;
		$this->frontendPath= JPATH_ROOT.DS.'components'.DS.$this->component.DS;
		$this->frontendCPUPath= JPATH_ROOT.DS.'components'.DS.'com_ose_cpu'.DS;
		$this->successStatus= '<div style="float:left;">.....&nbsp;</div><div style="color:#009900;">'.JText :: _('Installation completed').'</div><div style="clear:both;"></div>';
		$this->failedStatus= '<div style="float:left;">.....&nbsp;</div><div style="color:red;">'.JText :: _('Installation failed').'</div><div style="clear:both;"></div>';
		$this->notApplicable= '<div style="float:left;">.....&nbsp;</div><div>'.JText :: _('Installation not applicable').'</div><div style="clear:both;"></div>';
		$this->totalStep= 5;
		require_once(dirname(__FILE__).DS.'installer.template.php');
		$this->verifier= new oseInstallerVerifier();
		$this->template= new oseInstallerTemplate();
	}
	function install()
	{
		//check php version
		$installedPhpVersion= floatval(phpversion());
		$supportedPhpVersion= 5.2;
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.$this->component.DS.'installer.template.php');
		$step= JRequest :: getVar('step', '', 'post');
		$helper= new oseInstallerHelper;
		if($installedPhpVersion < $supportedPhpVersion)
		{
			$html= oseInstallerHelper :: getErrorMessage(101, $installedPhpVersion);
			$status= false;
			$nextstep= 0;
			$title= JText :: _('OSE Installer for').' '.$this->com_title;
			$install= 1;
			$substep= 0;
		}
		else
		{
			if(!empty($step))
			{
				$progress= $helper->installSteps($step);
				$html= $progress->message;
				$status= $progress->status;
				$nextstep= $progress->step;
				$title= $progress->title;
				$install= $progress->install;
				$substep= isset($progress->substep) ? $progress->substep : 0;
			}
			else
			{
				$nextstep= 1;
				$verifier= new oseInstallerVerifier();
				$imageTest= $verifier->testImage();
				$template= new oseInstallerTemplate();
				$html= $template->getHTML('welcome', $imageTest);
				$status= true;
				$title= JText :: _('OSE Installer for').' '.$this->com_title;
				$install= 1;
				$substep= 0;
			}
		}
		$this->template->cInstallDraw($html, $nextstep, $title, $status, $install, $substep);
		return;
	}
	function installSteps($step= 1)
	{
		$db= JFactory :: getDBO();
		switch($step)
		{
			case 1 :
				//check requirement
				$status= $this->checkRequirement(2);
				break;
			case 2 :
				//install backend system
				$status= $this->installBackend(3);
				break;
			case 3 :
				//install ajax system
				$status= $this->installCOMCPU(4);
				break;
			case 4 :
				//install frontend system
				$status= $this->installFrontend(5);
				break;
			case 5 :
				//install template
				$status= $this->prepareDatabase(6);
				break;
			case 6 :
			case 'UPDATE_DB' :
				//prepare database
				$status= $this->updateDatabase(7);
				break;
			case 7 :
				$status= $this->installPlugin(8);
				break;
			case 8 :
				$status= $this->installViews(9);
				break;
			case 9 :
				$status= $this->clearInstallation(100);
				break;
			case 100 :
				//show success message
				$status= $this->installationComplete(0);
				break;
			default :
				$status= new stdClass();
				$status->message= $this->getErrorMessage(0, '0a');
				$status->step= '-99';
				$status->title= JText :: _('OSE INSTALLER');
				$status->install= 1;
				break;
		}
		return $status;
	}
	function checkRequirement($step)
	{
		$status= true;
		$this->pageTitle= JText :: _('Checking Requirements');
		$html= '';
		$html .= '<div style="width:300px; float:left;">'.JText :: _('BACKEND ARCHIVE').'</div>';
		if(!$this->verifier->checkFileExist($this->backendPath.'admin.zip'))
		{
			$html .= $this->failedStatus;
			$status= false;
			$errorCode= '1a';
		}
		else
		{
			$html .= $this->successStatus;
		}
		$html .= '<div style="width:300px; float:left;">'.JText :: _('OSE CPU Backend ARCHIVE').'</div>';
		if(!$this->verifier->checkFileExist($this->backendPath.'com_cpu_admin.zip'))
		{
			$html .= $this->failedStatus;
			$status= false;
			$errorCode= '1b';
		}
		else
		{
			$html .= $this->successStatus;
		}
		$html .= '<div style="width:300px; float:left;">'.JText :: _('OSE CPU FRONTEND ARCHIVE').'</div>';
		if(!$this->verifier->checkFileExist($this->backendPath.'com_cpu_site.zip'))
		{
			$html .= $this->failedStatus;
			$status= false;
			$errorCode= '1b';
		}
		else
		{
			$html .= $this->successStatus;
		}
		$html .= '<div style="width:300px; float:left;">'.JText :: _('COMPONENT CPU ARCHIVE for OSE CPU').'</div>';
		if(!$this->verifier->checkFileExist($this->backendPath.$this->cpuFile))
		{
			$html .= $this->failedStatus;
			$status= false;
			$errorCode= '1b';
		}
		else
		{
			$html .= $this->successStatus;
		}

		if($status)
		{
			$autoSubmit= $this->getAutoSubmitFunction();
			//$form = $this->getInstallForm(2);
			$message= $autoSubmit.$html;
		}
		else
		{
			$errorMsg= $this->getErrorMessage(1, $errorCode);
			$message= $html.$errorMsg;
			$step= $step -1;
		}
		$drawdata= new stdClass();
		$drawdata->message= $message;
		$drawdata->status= $status;
		$drawdata->step= $step;
		$drawdata->title= JText :: _('OSE CHECKING REQUIREMENT');
		$drawdata->install= 1;
		return $drawdata;
	}
	function getAutoSubmitFunction()
	{
		ob_start();
?>
		<script type="text/javascript">
		var i=3;

		function countDown()
		{
			if(i >= 0)
			{
				document.getElementById("timer").innerHTML = i;
				i = i-1;
				var c = window.setTimeout("countDown()", 1000);
			}
			else
			{
				document.getElementById("div-button-next").removeAttribute("onclick");
				document.getElementById("input-button-next").setAttribute("disabled","disabled");
				document.forms["installform"].submit();
			}
		}

		window.addEvent('domready', function() {
			countDown();
		});

		</script>
		<?php

		$autoSubmit= ob_get_contents();
		@ ob_end_clean();
		return $autoSubmit;
	}
	function installBackend($step)
	{
		$html= '';
		$html .= '<div style="width:300px; float:left;">'.JText :: _('OSE BACKEND INSTALLATION').'</div>';
		$zip= $this->backendPath.'admin.zip';
		$destination= $this->backendPath;
		if($this->extractArchive($zip, $destination))
		{
			$html .= $this->successStatus;
			$autoSubmit= $this->getAutoSubmitFunction();
			$message= $autoSubmit.$html;
			$status= true;
		}
		else
		{
			$html .= $this->failedStatus;
			$errorMsg= $this->getErrorMessage($step, $step);
			$message= $html.$errorMsg;
			$status= false;
			$step= $step -1;
		}
		$html .= '<div style="width:300px; float:left;">'.JText :: _('English language file installation').'</div>';
		if($this->installLanguage('back'))
		{
			$html .= $this->successStatus;
			$autoSubmit= $this->getAutoSubmitFunction();
			$message= $autoSubmit.$html;
			$status= true;
		}
		else
		{
			$html .= $this->failedStatus;
			$errorMsg= $this->getErrorMessage(4, '4');
			$message= $html.$errorMsg;
			$status= false;
			$step= $step -1;
		}
		$drawdata= new stdClass();
		$drawdata->message= $message;
		$drawdata->status= $status;
		$drawdata->step= $step;
		$drawdata->title= JText :: _('OSE BACKEND INSTALLATION');
		$drawdata->install= 1;
		return $drawdata;
	}
	function installCOMCPU($step)
	{
		$html= '';
		$html .= '<div style="width:300px; float:left;">'.JText :: _('OSE CPU BACKEND INSTALLATION').'</div>';
		$zip= $this->backendPath.'com_cpu_admin.zip';
		$destination= JPATH_ADMINISTRATOR.DS.'components'.DS.'com_ose_cpu'.DS;
		if($this->extractArchive($zip, $destination))
		{
			$html .= $this->successStatus;
			$autoSubmit= $this->getAutoSubmitFunction();
			$message= $autoSubmit.$html;
			$status= true;
		}
		else
		{
			$html .= $this->failedStatus;
			$errorMsg= $this->getErrorMessage(2, '2');
			$message= $html.$errorMsg;
			$status= false;
			$step= $step -1;
		}
		jimport('joomla.filesystem.file');
		jimport('joomla.filesystem.folder');
		if (JFile::exists($this->frontendCPUPath.'extjs'.DS.'init'))
		{
			JFile::delete($this->frontendCPUPath.'extjs'.DS.'init');
		}
		if (JFolder::exists($this->frontendCPUPath.'extjs'.DS.'init'))
		{
			JFolder::delete($this->frontendCPUPath.'extjs'.DS.'init');
		}
		$html .= '<div style="width:300px; float:left;">'.JText :: _('OSE CPU FRONTEND INSTALLATION').'</div>';
		$zip= $this->backendPath.'com_cpu_site.zip';
		$destination= JPATH_SITE.DS.'components'.DS.'com_ose_cpu'.DS;
		if($this->extractArchive($zip, $destination))
		{
			$html .= $this->successStatus;
			$autoSubmit= $this->getAutoSubmitFunction();
			$message= $autoSubmit.$html;
			$status= true;
		}
		else
		{
			$html .= $this->failedStatus;
			$errorMsg= $this->getErrorMessage(2, '2');
			$message= $html.$errorMsg;
			$status= false;
			$step= $step -1;
		}
		$zip= $this->backendPath.$this->cpuFile;
		$destination= JPATH_ADMINISTRATOR.DS.'components'.DS.'com_ose_cpu'.DS;
		$html .= '<div style="width:300px; float:left;">'.JText :: _('OSE CPU Extended INSTALLATION').'</div>';
		if($this->extractArchive($zip, $destination))
		{
			$html .= $this->successStatus;
			$autoSubmit= $this->getAutoSubmitFunction();
			$message= $autoSubmit.$html;
			$status= true;
		}
		else
		{
			$html .= $this->failedStatus;
			$errorMsg= $this->getErrorMessage(2, '2');
			$message= $html.$errorMsg;
			$status= false;
			$step= $step -1;
		}
		$drawdata= new stdClass();
		$drawdata->message= $message;
		$drawdata->status= $status;
		$drawdata->step= $step;
		$drawdata->title= JText :: _('OSE INSTALLING Central Processing Units');
		$drawdata->install= 1;
		return $drawdata;
	}
	function installFrontend($step)
	{
		$html= '';
		$html .= '<div style="width:300px; float:left;">'.JText :: _('OSE Scanning File INSTALLATION').'</div>';
		$src = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_ose_antihacker'.DS.'scan'.DS.'scan.php';
		$dest = JPATH_ADMINISTRATOR.DS.'scan.php';
		if(JFile::copy($src, $dest))
		{
			$html .= $this->successStatus;
			$autoSubmit= $this->getAutoSubmitFunction();
			$message= $autoSubmit.$html;
			$status= true;
		}
		else
		{
			$html .= $this->failedStatus;
			$errorMsg= $this->getErrorMessage(2, '2');
			$message= $html.$errorMsg;
			$status= false;
			$step= $step -1;
		}
		$html .= '<div style="width:300px; float:left;">'.JText :: _('OSE Definition File INSTALLATION').'</div>';
		$src = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_ose_antihacker'.DS.'scan'.DS.'ahsdefines.php';
		$dest = JPATH_ADMINISTRATOR.DS.'ahsdefines.php';
		if(JFile::copy($src, $dest))
		{
			$html .= $this->successStatus;
			$autoSubmit= $this->getAutoSubmitFunction();
			$message= $autoSubmit.$html;
			$status= true;
		}
		else
		{
			$html .= $this->failedStatus;
			$errorMsg= $this->getErrorMessage(2, '2');
			$message= $html.$errorMsg;
			$status= false;
			$step= $step -1;
		}
		$drawdata= new stdClass();
		$drawdata->message= $message;
		$drawdata->status= $status;
		$drawdata->step= $step;
		$drawdata->title= JText :: _('OSE INSTALLING Scanning Files');
		$drawdata->install= 1;
		return $drawdata;
	}
	function installPlugin($step)
	{
		$html= '';
		$html .= '<div style="width:100px; float:left;">'.JText :: _('No Plugins to install for this component').'</div>';
		$result= null;
		$db= JFactory :: getDBO();

		$result= true;
		$viewhtml= '';

		if($result == true)
		{
			$html .= $this->successStatus;
			$autoSubmit= $this->getAutoSubmitFunction();
			//$form = $this->getInstallForm(5);
			$message= $autoSubmit.$html;
			$status= true;
		}
		else
		{
			$html .= $this->failedStatus;
			$html .= $viewhtml;
			$errorMsg= $this->getErrorMessage($step, $step);
			$message= $html.$errorMsg;
			$status= false;
			$step= $step -1;
		}
		$drawdata= new stdClass();
		$drawdata->message= $message;
		$drawdata->status= true;
		$drawdata->step= $step;
		$drawdata->title= JText :: _('CREATING VIEWS');
		$drawdata->install= 1;
		return $drawdata;
	}
	function prepareDatabase($step)
	{
		$html= '';
		$html .= '<div style="width:300px; float:left;">'.JText :: _('Creating Database').'</div>';
		$queryResult= $this->installSQL();
		if($queryResult == true)
		{
			$html .= $this->successStatus;
			$autoSubmit= $this->getAutoSubmitFunction();
			//$form = $this->getInstallForm(7);
			$message= $autoSubmit.$html;
			$status= true;
		}
		else
		{
			$html .= $this->failedStatus;
			$errorMsg= $this->getErrorMessage(6, $queryResult);
			$message= $html.$errorMsg;
			$status= false;
			$step= $step -1;
		}
		$drawdata= new stdClass();
		$drawdata->message= $message;
		$drawdata->status= $status;
		$drawdata->step= $step;
		$drawdata->title= JText :: _('PREPARING DATABASE');
		$drawdata->install= 1;
		return $drawdata;
	}
	function UpdateDatabase($step)
	{
		$html= '';
		$html .= '<div style="width:300px; float:left;">'.JText :: _('Fix Database Integrity').'</div>';
		$queryResult= $this->fixIntegrity();
		if($queryResult == true)
		{
			$html .= $this->successStatus;
			$autoSubmit= $this->getAutoSubmitFunction();
			//$form = $this->getInstallForm(7);
			$message= $autoSubmit.$html;
			$status= true;
		}
		else
		{
			$html .= $this->failedStatus;
			$errorMsg= $this->getErrorMessage(7, $queryResult);
			$message= $html.$errorMsg;
			$status= false;
			$step= $step -1;
		}
		$stage = $this->getGeoIPStage();
		$html .= '<div style="width:300px; float:left;">'.JText :: _('Installing GeoIPLocation Database'). ' Dataset ->'. $stage.'</div>';
		
		if ($stage<=6)
		{	
			$queryResult= $this->installGeoIPDB($stage);
			if($queryResult == true)
			{
				$html .= $this->successStatus;
				$autoSubmit= $this->getAutoSubmitFunction();
				//$form = $this->getInstallForm(7);
				$message= $autoSubmit.$html;
				$status= true;
			}
			else
			{
				$html .= $this->failedStatus;
				$errorMsg= $this->getErrorMessage(7, $queryResult);
				$message= $html.$errorMsg;
				$status= false;
				$step= $step -1;
			}
		}
		if ($stage <= 6)
		{
			$drawdata= new stdClass();
			$drawdata->message= $message;
			$drawdata->status= $status;
			$drawdata->step= $step-1;
			$drawdata->title= JText :: _('Installing GeoIP Data'). 'Dataset ->'. $stage;
			$drawdata->install= 1;
		}
		else
		{		
			$drawdata= new stdClass();
			$drawdata->message= $message;
			$drawdata->status= $status;
			$drawdata->step= $step;
			$drawdata->title= JText :: _('Fixing Database Integrity');
			$drawdata->install= 1;
		}
		return $drawdata;
	}
	function getGeoIPStage()
	{
		$db = JFactory::getDBO();
		$query = "SELECT COUNT(id) FROM #__ose_geoip ";
		$db->setQuery($query);
		$result = $db->loadResult();
		$return = ceil ($result / 25000); 
		return $return; 
	}
	function installGeoIPDB($stage)
	{
		$db = JFactory::getDBO();
		$file = JPATH_COMPONENT.DS.'sql'.DS.'osegeoip'.$stage.'.sql';
		$data = JFile::read($file);
		$queries = self::_splitQueries($data);
		foreach ($queries as $query)
		{	
				$db->setQuery($query);
				if(!$db->query()) {
					echo JText :: _('Unable to insert GeoIP record').'-'.$stage;
					echo $db->getErrorMsg();
					return false;
				}
		}
		unset($queries); 
		unset($data);
		return true; 
	}
	function _splitQueries($sql)
	{
		// Initialise variables.
		$buffer		= array();
		$queries	= array();
		$in_string	= false;
	
		// Trim any whitespace.
		$sql = trim($sql);
	
		// Remove comment lines.
		$sql = preg_replace("/\n\#[^\n]*/", '', "\n".$sql);
	
		// Parse the schema file to break up queries.
		for ($i = 0; $i < strlen($sql) - 1; $i ++)
		{
		if ($sql[$i] == ";" && !$in_string) {
		$queries[] = substr($sql, 0, $i);
		$sql = substr($sql, $i +1);
		$i = 0;
		}
	
		if ($in_string && ($sql[$i] == $in_string) && $buffer[1] != "\\") {
		$in_string = false;
		}
		elseif (!$in_string && ($sql[$i] == '"' || $sql[$i] == "'") && (!isset ($buffer[0]) || $buffer[0] != "\\")) {
		$in_string = $sql[$i];
		}
		if (isset ($buffer[1])) {
		$buffer[0] = $buffer[1];
		}
			$buffer[1] = $sql[$i];
		}
	
		// If the is anything left over, add it to the queries.
		if (!empty($sql)) {
		$queries[] = $sql;
	}
	
	return $queries;
	}
	function extractArchive($source, $destination)
	{
		// Cleanup path
		$destination= JPath :: clean($destination);
		$source= JPath :: clean($source);
		$result= JArchive :: extract($source, $destination);
		if($result === false)
		{
			return false;
		}
		else
		{
			return true;
		}
	}
	function fixIntegrity()
	{
		$db = JFactory::getDBO();
		$fields= OSESofthelper::getDBFields('#__oseath_l1rules');
		if(!isset($fields['#__oseath_l1rules']['trimmed_value'])) {
			$query= "ALTER TABLE `#__oseath_l1rules` ADD  `trimmed_value` longtext DEFAULT NULL;";
			$db->setQuery($query);
			if(!$db->query()) {
				echo $db->getErrorMsg();
				return false;
			}
		}

		if(!isset($fields['#__oseath_l1rules']['aclid'])) {
			$query= "ALTER TABLE `#__oseath_l1rules` ADD `aclid` INT( 11 ) NOT NULL AFTER `id`; ";
			$db->setQuery($query);
			if(!$db->query()) {
				echo $db->getErrorMsg();
				return false;
			}
		}
		$fields= OSESofthelper::getDBFields('#__oseath_l2rules');
		if(!isset($fields['#__oseath_l2rules']['trimmed_value'])) {
			$query= "ALTER TABLE `#__oseath_l2rules` ADD  `trimmed_value` longtext DEFAULT NULL;";
			$db->setQuery($query);
			if(!$db->query()) {
				echo $db->getErrorMsg();
				return false;
			}
		}
		if(!isset($fields['#__oseath_l2rules']['filters'])) {
			$query= "ALTER TABLE `#__oseath_l2rules` ADD `filters` TEXT NULL; ";
			$db->setQuery($query);
			if(!$db->query()) {
				echo $db->getErrorMsg();
				return false;
			}
		}
		if(!isset($fields['#__oseath_l2rules']['aclid'])) {
			$query= "ALTER TABLE `#__oseath_l2rules` ADD `aclid` INT( 11 ) NOT NULL AFTER `id`; ";
			$db->setQuery($query);
			if(!$db->query()) {
				echo $db->getErrorMsg();
				return false;
			}
		}
		
		$fields= OSESofthelper::getDBFields('#__ose_secConfig');		
		if(!isset($fields['#__ose_secConfig']['type'])) {
			$query= "ALTER TABLE `#__ose_secConfig` ADD `type` VARCHAR( 20 ) NULL DEFAULT NULL; ";
			$db->setQuery($query);
			if(!$db->query()) {
				echo $db->getErrorMsg();
				return false;
			}
		}

		$fields= OSESofthelper::getDBFields('#__oseipm_iptable');		
		if(!isset($fields['#__oseipm_iptable']['country'])) {
			$query= "ALTER TABLE `#__oseipm_iptable` ADD `country` VARCHAR( 3 ) NULL DEFAULT NULL; ";
			$db->setQuery($query);
			if(!$db->query()) {
				echo $db->getErrorMsg();
				return false;
			}
		}
		
		return true;
	}
	function installSQL()
	{
		//-- common images
		$img_OK= '<img src="images/publish_g.png" />';
		$img_WARN= '<img src="images/publish_y.png" />';
		$img_ERROR= '<img src="images/publish_r.png" />';
		$BR= '<br />';
		//--install...
		$db = JFactory::getDBO();

		$query = "CREATE TABLE IF NOT EXISTS `#__oseipm_acl` (
				  `id` int(11) NOT NULL AUTO_INCREMENT,
				  `name` varchar(200) DEFAULT NULL,
				  `status` varchar(5) NOT NULL,
				  `iptype` varchar(10) NOT NULL DEFAULT 'ip',
				  `ipv6` tinyint(1) NOT NULL DEFAULT '0',
				  `extension` varchar(4) DEFAULT NULL,
				  `extensionID` int(11) DEFAULT NULL,
				  PRIMARY KEY (`id`)
				) ENGINE=MyISAM  AUTO_INCREMENT=1;";
		$db->setQuery($query);
		if(!$db->query()) {
			echo JText :: _('Unable to insert configuration record');
			echo $db->getErrorMsg();
			return false;
		}

		$query = "CREATE TABLE IF NOT EXISTS `#__oseipm_iptable` (
		  `id` int(20) NOT NULL AUTO_INCREMENT,
		  `acl_id` int(11) NOT NULL,
		  `ip` varchar(40) NOT NULL DEFAULT '',
		  `user_id` int(11) DEFAULT NULL,
		  `host` text,
		  PRIMARY KEY (`id`)
		) ENGINE=MyISAM AUTO_INCREMENT=1;";
		$db->setQuery($query);
		if(!$db->query()) {
			echo JText :: _('Unable to insert configuration record');
			echo $db->getErrorMsg();
			return false;
		}

		$query = "CREATE TABLE IF NOT EXISTS `#__oseath_alerts` (
		  `id` int(11) NOT NULL AUTO_INCREMENT,
		  `aclid` int(11) NOT NULL,
		  `l1ruleids` text,
		  `l2ruleids` text,
		  `datetime` datetime NOT NULL,
		  `score` int(3) DEFAULT NULL,
		  `referer` longtext,
		  `notified` int(1) DEFAULT NULL,
		  PRIMARY KEY (`id`)
		) ENGINE=MyISAM AUTO_INCREMENT=1 ;";
		$db->setQuery($query);
		if(!$db->query()) {
			echo JText :: _('Unable to insert configuration record');
			echo $db->getErrorMsg();
			return false;
		}

		$query = "CREATE TABLE IF NOT EXISTS `#__ose_secConfig` (
		  `id` int(11) NOT NULL AUTO_INCREMENT,
		  `key` text NOT NULL,
		  `value` text NOT NULL,
		  PRIMARY KEY (`id`)
		) ENGINE=MyISAM AUTO_INCREMENT=1 ;";
		$db->setQuery($query);
		if(!$db->query()) {
			echo JText :: _('Unable to insert configuration record');
			echo $db->getErrorMsg();
			return false;
		}

		$query = "CREATE TABLE IF NOT EXISTS `#__oseath_l1rules` (
		  `id` int(11) NOT NULL AUTO_INCREMENT,
		  `signature` longtext NOT NULL,
		  `trimmed_value` longtext DEFAULT NULL,
		  `signatureaction` tinyint(2) DEFAULT NULL,
		  `target` longtext NOT NULL,
		  `targetaction` tinyint(2) DEFAULT NULL,
		  `times` int(11) DEFAULT NULL,
		  PRIMARY KEY (`id`)
		) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
		$db->setQuery($query);
		if(!$db->query()) {
			echo JText :: _('Unable to insert configuration record');
			echo $db->getErrorMsg();
			return false;
		}

		$query = "CREATE TABLE IF NOT EXISTS `#__oseath_l2rules` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `key` longtext NOT NULL,
			  `trimmed_value` longtext DEFAULT NULL,
			  `keyaction` tinyint(2) DEFAULT NULL,
			  `target` text,
			  `targetaction` tinyint(2) DEFAULT NULL,
			  `times` int(11) DEFAULT NULL,
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;";
		$db->setQuery($query);
		if(!$db->query()) {
			echo JText :: _('Unable to insert configuration record');
			echo $db->getErrorMsg();
			return false;
		}

    	$query = "CREATE TABLE IF NOT EXISTS `#__oseipm_iptable_tmp` (
		  `id` int(20) NOT NULL AUTO_INCREMENT,
		  `ip` varchar(40) NOT NULL DEFAULT '',
		  `last_session_request` text DEFAULT NULL,
		  `total_session_request` text  DEFAULT NULL,
		  PRIMARY KEY (`id`)
		) ENGINE=MyISAM AUTO_INCREMENT=1;";

		$db->setQuery($query);
		if(!$db->query()) {
			echo JText :: _('Unable to create temp ip table');
			echo $db->getErrorMsg();
			return false;
		}

		$query = "SELECT `key` FROM `#__ose_secConfig` WHERE `key` = 'threshold' ";
		$db->setQuery($query);
		$result = $db->loadResult();
		if (empty($result))
		{
			$query = " INSERT INTO `#__ose_secConfig` (`id`, `key`, `value`) VALUES
						(6, 'threshold', '40'),
						(7, 'pageTitle', ''),
						(8, 'metaKeywords', ''),
						(9, 'metaDescription', ''),
						(10, 'adminEmail', ''),
						(11, 'customBanpage', ''),
						(12, 'scankey', '0'),
						(13, 'scankeyLength', '0'),
						(14, 'scancookies', '0'),
						(15, 'convertFromSQLKeywords', '0'),
						(16, 'runCentrifuge', '0'),
						(17, 'l2ruleid_36', '1'),
						(18, 'l2ruleid_37', '1'),
						(19, 'l2ruleid_38', '1'),
						(20, 'l2ruleid_39', '1'),
						(21, 'l2ruleid_40', '1'),
						(22, 'l2ruleid_41', '1'),
						(23, 'l2ruleid_42', '1'),
						(24, 'l2ruleid_43', '1'),
						(25, 'l2ruleid_44', '1'),
						(26, 'l2ruleid_45', '1'),
						(27, 'l2ruleid_46', '1'),
						(28, 'l2ruleid_47', '1'),
						(29, 'l2ruleid_48', '1'),
						(30, 'l2ruleid_49', '1'),
						(31, 'l2ruleid_50', '1'),
						(32, 'l2ruleid_51', '1'),
						(33, 'l2ruleid_52', '1'),
						(34, 'l2ruleid_53', '1'),
						(35, 'l2ruleid_54', '1'),
						(36, 'l2ruleid_55', '1'),
						(37, 'l2ruleid_56', '1'),
						(38, 'l2ruleid_57', '1'),
						(39, 'l2ruleid_58', '1'),
						(40, 'l2ruleid_59', '1'),
						(41, 'l2ruleid_60', '1'),
						(42, 'l2ruleid_61', '1'),
						(43, 'l2ruleid_62', '1'),
						(44, 'l2ruleid_63', '1'),
						(45, 'l2ruleid_64', '1'),
						(46, 'l2ruleid_65', '1'),
						(47, 'l2ruleid_66', '1'),
						(48, 'l2ruleid_67', '1'),
						(49, 'l2ruleid_68', '1'),
						(50, 'l2ruleid_69', '1'),
						(51, 'l2ruleid_70', '1'),
						(52, 'l2ruleid_1', '1'),
						(53, 'l2ruleid_2', '1'),
						(54, 'l2ruleid_3', '1'),
						(55, 'l2ruleid_4', '1'),
						(56, 'l2ruleid_5', '1'),
						(57, 'l2ruleid_6', '1'),
						(58, 'l2ruleid_7', '1'),
						(59, 'l2ruleid_8', '1'),
						(60, 'l2ruleid_9', '1'),
						(61, 'l2ruleid_10', '1'),
						(62, 'l2ruleid_11', '1'),
						(63, 'l2ruleid_12', '1'),
						(64, 'l2ruleid_13', '1'),
						(65, 'l2ruleid_14', '1'),
						(66, 'l2ruleid_15', '1'),
						(67, 'l2ruleid_16', '1'),
						(68, 'l2ruleid_17', '1'),
						(69, 'l2ruleid_18', '1'),
						(70, 'l2ruleid_19', '1'),
						(71, 'l2ruleid_20', '1'),
						(72, 'l2ruleid_21', '1'),
						(73, 'l2ruleid_22', '1'),
						(74, 'l2ruleid_23', '1'),
						(75, 'l2ruleid_24', '1'),
						(76, 'l2ruleid_25', '1'),
						(77, 'l2ruleid_26', '1'),
						(78, 'l2ruleid_27', '1'),
						(79, 'l2ruleid_28', '1'),
						(80, 'l2ruleid_29', '1'),
						(81, 'l2ruleid_30', '1'),
						(82, 'l2ruleid_31', '1'),
						(83, 'l2ruleid_32', '1'),
						(84, 'l2ruleid_33', '1'),
						(85, 'l2ruleid_34', '1'),
						(86, 'l2ruleid_35', '1'),
						(87, 'convertFromRepetition', '1'),
						(88, 'convertFromCommented', '1'),
						(89, 'convertFromWhiteSpace', '1'),
						(90, 'convertFromJSCharcode', '1'),
						(91, 'convertJSRegexModifiers', '1'),
						(92, 'convertEntities', '1'),
						(93, 'convertQuotes', '1'),
						(94, 'convertFromSQLHex', '1'),
						(95, 'convertFromControlChars', '1'),
						(96, 'convertFromNestedBase64', '1'),
						(97, 'convertFromOutOfRangeChars', '1'),
						(98, 'convertFromXML', '0'),
						(99, 'convertFromJSUnicode', '1'),
						(100, 'convertFromUTF7', '1'),
						(101, 'convertFromConcatenated', '1'),
						(102, 'convertFromProprietaryEncodings', '1'),
						(103, 'blockIP', '2'),
						(104, 'allowExts', ''),
						(105, 'scanFileVirus', '1'); ";
			$db->setQuery($query);
			if(!$db->query()) {
				echo JText :: _('Unable to insert configuration record');
				echo $db->getErrorMsg();
				return false;
			}
		}

		$query = "CREATE TABLE IF NOT EXISTS `#__ose_app_email` (
		  `id` int(11) NOT NULL AUTO_INCREMENT,
		  `app` varchar(20) NOT NULL,
		  `subject` text,
		  `body` text,
		  `type` varchar(20) DEFAULT NULL,
		  `params` text,
		   PRIMARY KEY (`id`)
		) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;";
		
		$db->setQuery($query);
		if(!$db->query()) {
			echo JText :: _('Unable to insert email table record');
			echo $db->getErrorMsg();
			return false;
		}
		
		$query = "SELECT COUNT(id) FROM `#__ose_app_email`";
		$db->setQuery($query);
		$result = $db->loadResult();
		if ($result==0)
		{
			$query = "INSERT INTO `#__ose_app_email` (`id`, `app`, `subject`, `body`, `type`, `params`) VALUES ".
					 "(NULL, 'antihack', 'OSE Anti-Hacker (TM) alert for a blacklisted entry', '<div style=\"width: 569px; margin: auto; background-color: #dd2d1d; padding: 10px; color: #fff; font-size: 36px; text-shadow: -1px -1px 1px #A10E08; font-family: Georgia,\'Times New Roman\',Times,serif; font-weight: bold;\">Alert: Detected attacks were blocked</div>\n<div style=\"width: 569px; margin: auto; background-color: #e4e4e4; padding: 10px; color: #666666; font-family: Georgia,\'Times New Roman\',Times,serif; font-size: 14px; font-weight: bold; text-shadow: 1px 1px 1px #FFFFFF;\">\n<p>Dear [user],</p>\n<p>An attack attempt was logged on [logtime]</p>\n<p>IP Address: [ip]</p>\n<p>URL: [target]</p>\n<p>Referer (if any): [referer]</p>\n<p>Attack Type: [attackType]</p>\n<p>Violation: [violation]</p>\n<p>Blacklisted IP Rule ID: [aclid];</p>\n<p>Total Risk Score: [score]</p>\n<p>IP information: <a href=\"http://www.infosniper.net/index.php?ip_address=[ip]\">http://www.infosniper.net/index.php?ip_address=[ip]</a></p>\n<p> </p>\n<hr />\n<p>If this blocks your users by mistake, please consult OSE support team for advices.</p>\n<p>OSE Anti-Hacker&trade; Security Anti-Hacking Alert</p>\n</div>', 'blacklisted', '{\"user\":\"Name of the receiptient\",\"host\":\"Hostname of the protected server\",\"logtime\":\"The time the attack was logged.\",\"ip\":\"IP of the attacker\",\"target\":\"The attacked page\",\"referrer\":\"The referrer of the attack\",\"attacktype\":\"The type of attack\",\"violation\":\"The rule violated\",\"aclid\":\"The access rule ID logged in the system\",\"score\":\"The total amount of score the attack has triggered\"}' ),".
					 "(NULL, 'antihack', 'OSE Anti-Hacker (TM) alert for a filtered entry', '<div style=\"width: 569px; margin: auto; background-color: #dd2d1d; padding: 10px; color: #fff; font-size: 36px; text-shadow: -1px -1px 1px #A10E08; font-family: Georgia,\'Times New Roman\',Times,serif; font-weight: bold;\">Alert: Detected attacks were filtered</div>\n<div style=\"width: 569px; margin: auto; background-color: #e4e4e4; padding: 10px; color: #666666; font-family: Georgia,\'Times New Roman\',Times,serif; font-size: 14px; font-weight: bold; text-shadow: 1px 1px 1px #FFFFFF;\">\n<p>Dear [user],</p>\n<p>An attack attempt was filtered on [logtime]</p>\n<p>IP Address: [ip]</p>\n<p>URL: [target]</p>\n<p>Referer (if any): [referer]</p>\n<p>Attack Type: [attackType]</p>\n<p>Violation: [violation]</p>\n<p>Blacklisted IP Rule ID: [aclid];</p>\n<p>Total Risk Score: [score]</p>\n<p>IP information: <a href=\"http://www.infosniper.net/index.php?ip_address=[ip]\">http://www.infosniper.net/index.php?ip_address=[ip]</a></p>\n<p> </p>\n<hr />\n<p>If this blocks your users by mistake, please consult OSE support team for advices.</p>\n<p>OSE Anti-Hacker&trade; Security Anti-Hacking Alert</p>\n</div>', 'filtered', '{\"user\":\"Name of the receiptient\",\"host\":\"Hostname of the protected server\",\"logtime\":\"The time the attack was logged.\",\"ip\":\"IP of the attacker\",\"target\":\"The attacked page\",\"referrer\":\"The referrer of the attack\",\"attacktype\":\"The type of attack\",\"violation\":\"The rule violated\",\"aclid\":\"The access rule ID logged in the system\",\"score\":\"The total amount of score the attack has triggered\"}' ),".
					 "(NULL, 'antihack', 'OSE Anti-Hacker (TM) alert for a 403 blocked entry', '<div style=\"width: 569px; margin: auto; background-color: #dd2d1d; padding: 10px; color: #fff; font-size: 36px; text-shadow: -1px -1px 1px #A10E08; font-family: Georgia,\'Times New Roman\',Times,serif; font-weight: bold;\">Alert: Detected attacks were stopped</div>\n<div style=\"width: 569px; margin: auto; background-color: #e4e4e4; padding: 10px; color: #666666; font-family: Georgia,\'Times New Roman\',Times,serif; font-size: 14px; font-weight: bold; text-shadow: 1px 1px 1px #FFFFFF;\">\n<p>Dear [user],</p>\n<p>An attack attempt was stopped by a 403 error page on [logtime]</p>\n<p>IP Address: [ip]</p>\n<p>URL: [target]</p>\n<p>Referer (if any): [referer]</p>\n<p>Attack Type: [attackType]</p>\n<p>Violation: [violation]</p>\n<p>Blacklisted IP Rule ID: [aclid];</p>\n<p>Total Risk Score: [score]</p>\n<p>IP information: <a href=\"http://www.infosniper.net/index.php?ip_address=[ip]\">http://www.infosniper.net/index.php?ip_address=[ip]</a></p>\n<p> </p>\n<hr />\n<p>If this blocks your users by mistake, please consult OSE support team for advices.</p>\n<p>OSE Anti-Hacker&trade; Security Anti-Hacking Alert</p>\n</div>', '403blocked', '{\"user\":\"Name of the receiptient\",\"host\":\"Hostname of the protected server\",\"logtime\":\"The time the attack was logged.\",\"ip\":\"IP of the attacker\",\"target\":\"The attacked page\",\"referrer\":\"The referrer of the attack\",\"attacktype\":\"The type of attack\",\"violation\":\"The rule violated\",\"aclid\":\"The access rule ID logged in the system\",\"score\":\"The total amount of score the attack has triggered\"}' );";
			$db->setQuery($query);
			if(!$db->query()) {
				echo JText :: _('Unable to insert configuration record');
				echo $db->getErrorMsg();
				return false;
			}
		}
		
		$query = "CREATE TABLE IF NOT EXISTS `#__ose_activation` (
						  `id` int(11) NOT NULL AUTO_INCREMENT,
						  `code` text NOT NULL,
						  `ext` varchar(20) NOT NULL,
						  PRIMARY KEY (`id`)
						) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;";
		$db->setQuery($query);
		if(!$db->query())
		{
			echo $img_ERROR.JText :: _('Unable to create table').$BR;
			echo $db->getErrorMsg();
			return false;
		}
		
		$query = "CREATE TABLE IF NOT EXISTS `#__oseath_whitelist` (
		  `id` int(11) NOT NULL AUTO_INCREMENT,
		  `string` text NOT NULL,
		  `layer` tinyint(1) NOT NULL,
		PRIMARY KEY (`id`)
		) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
		$db->setQuery($query);
		if(!$db->query())
		{
			echo $img_ERROR.JText :: _('Unable to create table').$BR;
			echo $db->getErrorMsg();
			return false;
		}
		
		$query = "CREATE TABLE IF NOT EXISTS `#__ose_geoip` (
				  `id` int(11) NOT NULL AUTO_INCREMENT,
				  `ip32_start` text NOT NULL,
				  `ip32_end` text NOT NULL,
				  `country_code` varchar(2) NOT NULL,
				  PRIMARY KEY (`id`)
				) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
		$db->setQuery($query);
		if(!$db->query())
		{
			echo $img_ERROR.JText :: _('Unable to create table').$BR;
			echo $db->getErrorMsg();
			return false;
		}
		
		return true;
	}
	function oseunpack($src, $dest, $file)
	{
		$extractdir= JPath :: clean($dest);
		$archivename= JPath :: clean($src.DS.$file);
		// do the unpacking of the archive
		$result= JArchive :: extract($archivename, $extractdir);
		if($result === false)
		{
			return false;
		}
		else
		{
			if(JFile :: delete($archivename))
			{
				return true;
			}
		}
	}
	function installLanguage($type)
	{
		if ($type =='back')
		{
			$src= JPATH_ADMINISTRATOR.DS.'components'.DS.$this->component.DS.'language'.DS.'en-GB'.DS.'en-GB.'.$this->component.'.ini';
			$dest= JPATH_ADMINISTRATOR.DS.'language'.DS.'en-GB'.DS.'en-GB.'.$this->component.'.ini';
		}
		else
		{
			$src= JPATH_SITE.DS.'components'.DS.$this->component.DS.'language'.DS.'en-GB'.DS.'en-GB.'.$this->component.'.ini';
			$dest= JPATH_SITE.DS.'language'.DS.'en-GB'.DS.'en-GB.'.$this->component.'.ini';
		}
		if(!JFile :: copy($src, $dest))
		{
			echo JText :: _('Unable to copy language file');
			return false;
		}
		else
		{
			return true;
		}
	}
	function installMenuPatch()
	{
		return true;
	}
	function installModulePatch()
	{

			return true;
	}
	function installViews($step)
	{
		$html= '';
		$html .= '<div style="width:100px; float:left;">'.JText :: _('No Views to install for this component').'</div>';
		$result= null;
		$db= JFactory :: getDBO();

		$result= true;
		$viewhtml= '';

		if($result == true)
		{
			$html .= $this->successStatus;
			$autoSubmit= $this->getAutoSubmitFunction();
			//$form = $this->getInstallForm(5);
			$message= $autoSubmit.$html;
			$status= true;
		}
		else
		{
			$html .= $this->failedStatus;
			$html .= $viewhtml;
			$errorMsg= $this->getErrorMessage($step, $step);
			$message= $html.$errorMsg;
			$status= false;
			$step= $step -1;
		}
		$drawdata= new stdClass();
		$drawdata->message= $message;
		$drawdata->status= true;
		$drawdata->step= $step;
		$drawdata->title= JText :: _('CREATING VIEWS');
		$drawdata->install= 1;
		return $drawdata;
	}
	function clearInstallation($step)
	{
		jimport('joomla.filesystem.file');
		jimport('joomla.filesystem.folder');
		$html= '';
		$zip= array();
		$zip[]= $this->backendPath.'admin.zip';
		$zip[]= $this->backendPath.$this->cpuFile;
		$zip[]= $this->backendPath.'com_cpu_admin.zip';
		$zip[]= $this->backendPath.'com_cpu_site.zip';
		$result= true;
		foreach($zip as $z)
		{
			$html .= '<div style="width:500px; float:left;">'.JText :: _('Clearing file').' '.$z.'</div>';
			$result= JFile :: delete($z);
			if($result == true)
			{
				$html .= $this->successStatus;
				$autoSubmit= $this->getAutoSubmitFunction();
				//$form = $this->getInstallForm(5);
				$message= $autoSubmit.$html;
				$status= true;
			}
			else
			{
				$html .= $this->failedStatus;
				$errorMsg= $this->getErrorMessage($step, $step);
				$message= $html.$errorMsg;
				$status= false;
				$step= $step -1;
			}
		}

		$drawdata= new stdClass();
		$drawdata->message= $message;
		$drawdata->status= true;
		$drawdata->step= $step;
		$drawdata->title= JText :: _('CREATING VIEWS');
		$drawdata->install= 1;
		return $drawdata;
	}
	function installationComplete($step)
	{
		$cache= JFactory :: getCache();
		$cache->clean();
		$version= OSEANTIHACKERVER;
		$file= dirname(__FILE__).DS.'installer.dummy.ini';
		if(JFile :: exists($file) && JFile :: delete($file))
		{
			$html= '';
			$html .= '<div style="margin: 30px 0; padding: 10px; background: #edffb7; border: solid 1px #8ba638; width: 50%; -moz-border-radius: 5px; -webkit-border-radius: 5px;">
									<h3 style="padding: 0; margin: 0 0 5px;">Installation has been completed</h3></div>';
		}
		else
		{
			$html= '<div></div>';
			$html .= '<div style="margin: 30px 0; padding: 10px; background: #edffb7; border: solid 1px #8ba638; width: 50%; -moz-border-radius: 5px; -webkit-border-radius: 5px;">
									<h3 style="padding: 0; margin: 0 0 5px;">Installation has been completed</h3>However we were unable to remove the file <b>installer.dummy.ini</b> located in the '.dirname(__FILE__).' folder. Please remove it manually in order to completed the installation.</div>';
		}
		ob_start();
?>

		<div style="margin: 30px 0; padding: 10px; background: #fbfbfb; border: solid 1px #ccc; width: 50%; -moz-border-radius: 5px; -webkit-border-radius: 5px;">
			<h3 style="color: red;">IMPORTANT!!</h3>
			<div>Before you begin, you might want to take a look at the following documentations first</div>
			<ul style="background: none;padding: 0; margin-left: 15px;">
				<li style="background: none;padding: 0;margin:0;"><a href="http://www.opensource-excellence.com/documentation/tutorials-ose-anti-hacker/item/411.html" target="_blank">Activating OSE Anti Hacker</a></li>
				<li style="background: none;padding: 0;margin:0;"><a href="http://www.opensource-excellence.com/documentation/tutorials-ose-anti-hacker.html" target="_blank">Configuring OSE Anti Hacker</a></li>
			</ul>
			<div>You can read the full documentation at <a href="http://wiki.opensource-excellence.com" target="_blank">OSE Wiki Website</a></div>
		</div>

	<?php

		$content= ob_get_contents();
		ob_end_clean();
		$html .= $content;
		//$form = $this->getInstallForm(0, 0);
		$message= $html;
		$drawdata= new stdClass();
		$drawdata->message= $message;
		$drawdata->status= true;
		$drawdata->step= $step;
		$drawdata->title= JText :: _('INSTALLATION COMPLETED');
		$drawdata->install= 0;
		return $drawdata;
	}
	function getErrorMessage($error= "", $extraInfo= "")
	{
		switch($error)
		{
			case 0 :
				$errorWarning= $error.'-'.$extraInfo.' : '.JText :: _('The operation is invalid');
				break;
			case 1 :
				$errorWarning= $error.'-'.$extraInfo.' : '.JText :: _('The file is missing');
				break;
			case 2 :
				$errorWarning= $error.'-'.$extraInfo.' : '.JText :: _('OSE BACKEND EXTRACT FAILED WARN');
				break;
			case 3 :
				$errorWarning= $error.'-'.$extraInfo.' : '.JText :: _('OSE CPU INSTALL FAILED');
				break;
			case 4 :
				$errorWarning= $error.'-'.$extraInfo.' : '.JText :: _('OSE FRONTEND EXTRACT FAILED WARN');
				break;
			case 5 :
				$errorWarning= $error.'-'.$extraInfo.' : '.JText :: _('Error creating OSE tables');
				break;
			case 6 :
				$errorWarning= $error.'-'.$extraInfo.' : '.JText :: _('Error creating OSE tables');
				break;
			case 7 :
				$errorWarning= $error.'-'.$extraInfo.' : '.JText :: _('Error fixing OSE table integrity');
				break;
			case 8 :
				$errorWarning= $error.'-'.$extraInfo.' : '.JText :: _('Error creating Database Views');
				break;
			case 101 :
				$errorWarning= $error.' : '.JText :: _('PHP version is lower than 5.2, your version is'.' '.$extraInfo);
				break;
			default :
				$error=(!empty($error)) ? $error : '99';
				$errorWarning= $error.'-'.$extraInfo.' : '.JText :: _('UNEXPECTED ERROR WARN');
				break;
		}
		ob_start();
?>
		<div style="font-weight: 700; color: red; padding-top:10px">
			<?php echo $errorWarning; ?>
		</div>
		<div id="communityContainer" style="margin-top:10px">
			<div><?php echo JText::_('OSE INSTALLATION ERROR HELP'); ?></div>
			<div><a href="http://wiki.opensource-excellence.com/index.php?title=Trouble_Shooting_-_OSE_Security_3">http://wiki.opensource-excellence.com/index.php?title=Trouble_Shooting_-_OSE_Security_3</a></div>
		</div>
		<?php

		$errorMsg= ob_get_contents();
		@ ob_end_clean();
		return $errorMsg;
	}
}
class oseInstallerVerifier
{
	var $template;
	var $dbhelper;
	function __construct()
	{
		require_once(dirname(__FILE__).DS.'installer.template.php');
		$this->template= new oseInstallerTemplate();
	}
	function isLatestFriendTable()
	{
		$fields= $this->dbhelper->_isExistTableColumn('#__community_users', 'friendcount');
		return $fields;
	}
	function isLatestGroupMembersTable()
	{
		$fields= $this->dbhelper->_getFields('#__community_groups_members');
		$result= array();
		if(array_key_exists('permissions', $fields))
		{
			if($fields['permissions'] == 'varchar')
			{
				return false;
			}
		}
		return true;
	}
	function isPhotoPrivacyUpdated()
	{
		return $this->dbhelper->checkPhotoPrivacyUpdated();
	}
	function isLatestGroupTable()
	{
		$fields= $this->dbhelper->_getFields();
		if(!array_key_exists('membercount', $fields))
		{
			return false;
		}
		if(!array_key_exists('wallcount', $fields))
		{
			return false;
		}
		if(!array_key_exists('discusscount', $fields))
		{
			return false;
		}
		return true;
	}
	/**
	 * Method to check if the GD library exist
	 *
	 * @returns boolean	return check status
	 **/
	function testImage()
	{
		$msg= '
							<style type="text/css">
							.Yes {
								color:#46882B;
								font-weight:bold;
							}
							.No {
								color:#CC0000;
								font-weight:bold;
							}
							.jomsocial_install tr {

							}
							.jomsocial_install td {
								color: #888;
								padding: 3px;
							}
							.jomsocial_install td.item {
								color: #333;
							}
							</style>
							<div class="install-body" style="background: #fbfbfb; border: solid 1px #ccc; -moz-border-radius: 5px; -webkit-border-radius: 5px; padding: 20px; width: 50%;">
								<p>If any of these items are not supported (marked as <span class="No">No</span>), your system does not meet the requirements for installation. Some features might not be available. Please take appropriate actions to correct the errors.</p>
									<table class="content jomsocial_install" style="width: 100%; background">
										<tbody>';
		// @rule: Test for JPG image extensions
		$type= 'JPEG';
		if(function_exists('imagecreatefromjpeg'))
		{
			$msg .= $this->template->testImageMessage($type, true);
		}
		else
		{
			$msg .= $this->template->testImageMessage($type, false);
		}
		// @rule: Test for png image extensions
		$type= 'PNG';
		if(function_exists('imagecreatefrompng'))
		{
			$msg .= $this->template->testImageMessage($type, true);
		}
		else
		{
			$msg .= $this->template->testImageMessage($type, false);
		}
		// @rule: Test for gif image extensions
		$type= 'GIF';
		if(function_exists('imagecreatefromgif'))
		{
			$msg .= $this->template->testImageMessage($type, true);
		}
		else
		{
			$msg .= $this->template->testImageMessage($type, false);
		}
		$msg .= '
										</tbody>
									</table>

							</div>';
		return $msg;
	}
	function checkFileExist($file)
	{
		return file_exists($file);
	}
}
?>