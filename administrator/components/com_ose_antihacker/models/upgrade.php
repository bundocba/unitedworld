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

defined('_JEXEC') or die("Direct Access Not Allowed");

jimport('joomla.application.component.model');

class ose_antihackerModelupgrade extends JModel
{
	function __construct()
	{
		parent::__construct();
	}
	function upgrade()
	{
		$db = JFactory::getDBO(); 
		$curVerion = '5.2.1';
		$query = "SELECT `value` FROM `#__ose_secConfig` WHERE `key` = 'upgradedto'"; 
		$db->setQuery($query); 
		$result = $db->loadResult(); 
		
		// Version 4.0 to version 5.0 upgrade
		if (empty($result))
		{
			$result = $this->upgrade500();
			$result = $this->upgrade510();
			$result = $this->upgrade513();
			$result = $this->upgrade521();
			if ($result==false)
			{
				return false;
			}	
		}	
		elseif ($result=='5.0.0')
		{
			$result = $this->upgrade510();
			$result = $this->upgrade513();
			$result = $this->upgrade521();
			if ($result==false)
			{
				return false;
			}
		}
		elseif ($result=='5.1.0')
		{
			$result = $this->upgrade513();
			$result = $this->upgrade521();
			if ($result==false)
			{
				return false;
			}
		}
		elseif ($result=='5.1.4')
		{
			$result = $this->upgrade521();
			if ($result==false)
			{
				return false;
			}
		}
		
		// Update to latest version
		
		$query = "SELECT `id` FROM `#__ose_secConfig` WHERE `key` = 'upgradedto' ";
		$db->setQuery($query);
		$id = $db->loadResult();
		if (empty($id))
		{
			$query = "INSERT INTO `#__ose_secConfig` (`id`, `key`, `value`, `type`) VALUES
								 (NULL, 'upgradedto', '".$curVerion."', 'update');";
		
		}
		else
		{
			$query = "UPDATE `#__ose_secConfig` SET `value` = ".$db->Quote($curVerion)." WHERE `id` =". (int)$id;
			 
		}
		$db->setQuery($query);
		$result = $db->query();
		return $result;
	}
	function upgrade500()
	{
		$db = JFactory::getDBO();
		// Delete OSE File Man v3 from assets table;
		$query = "DELETE FROM `#__assets` WHERE `name` = 'com_osefileman'";
		$db->setQuery($query);
		$result = $db->query();
			
		$query = "DELETE FROM `#__extensions` WHERE `element` = 'com_osefileman'";
		$db->setQuery($query);
		$result = $db->query();
		
		$query = "DELETE FROM `#__menu` WHERE `link` = 'index.php?option=com_osefileman'";
		$db->setQuery($query);
		$result = $db->query();
			
		$query = "SELECT COUNT(`id`) FROM `#__assets` WHERE `name` = 'com_ose_fileman'";
		$db->setQuery($query);
		$result = $db->query();
		if ($result==0)
		{
			// Insert OSE File Man v4 to assets table;
			$query = "INSERT INTO `#__assets` (`id`, `parent_id`, `lft`, `rgt`, `level`, `name`, `title`, `rules`) VALUES
			(NULL, 1, 41, 42, 1, 'com_ose_fileman', 'ose_fileman', '{}'),
			(NULL, 1, 43, 44, 1, 'com_ose_appcenter', 'ose_appcenter', '{}');";
			$db->setQuery($query);
			$result = $db->query();
		}
		
		$query = "SELECT COUNT(`id`) FROM `#__extensions` WHERE `name` = 'ose_fileman'";
		$db->setQuery($query);
		$result = $db->query();
		if ($result==0)
		{
			$query = "INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES
			(NULL, 'ose_fileman', 'component', 'com_ose_fileman', '', 1, 1, 1, 1, '{\"legacy\":false,\"name\":\"ose_fileman\",\"type\":\"component\",\"creationDate\":\"14-May-2012\",\"author\":\"Open Source Excellence\",\"copyright\":\"Copyright (C) 2008-2012 Open Source Excellence. All rights reserved.\",\"authorEmail\":\"\",\"authorUrl\":\"\",\"version\":\"4.0.0 beta\",\"description\":\"OSE File Manager\",\"group\":\"\"}', '{}', '', '', 0, '0000-00-00 00:00:00', 0, 0);";
			$db->setQuery($query);
			$db->query();
			$id = $db->insertid();
				
			$query = "INSERT INTO `#__menu` (`id`, `menutype`, `title`, `alias`, `note`, `path`, `link`, `type`, `published`, `parent_id`, `level`, `component_id`, `ordering`, `checked_out`, `checked_out_time`, `browserNav`, `access`, `img`, `template_style_id`, `params`, `lft`, `rgt`, `home`, `language`, `client_id`) VALUES
			(NULL, 'main', 'OSE File Manager™', 'OSE File Manager™', '', 'OSE File Manager™', 'index.php?option=com_ose_fileman', 'component', 1, 1, 1, ".(int)$id.", 0, 0, '0000-00-00 00:00:00', 0, 1, '\"components/com_ose_fileman/favicon.ico\"', 0, '', 7, 8, 0, '', 1);";
			$db->setQuery($query);
			$result = $db->query();
		}
			
		$query = "SELECT COUNT(`id`) FROM `#__extensions` WHERE `name` = 'ose_appcenter'";
		$db->setQuery($query);
		$result = $db->query();
		if ($result==0)
		{
			$query = "INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES
			(NULL, 'ose_appcenter', 'component', 'com_ose_appcenter', '', 1, 1, 1, 1, '{\"legacy\":false,\"name\":\"ose_appcenter\",\"type\":\"component\",\"creationDate\":\"09-Oct-2011\",\"author\":\"Open Source Excellence\",\"copyright\":\"Copyright (C) 2008-2011 Open Source Excellence. All rights reserved.\",\"authorEmail\":\"\",\"authorUrl\":\"\",\"version\":\"1.0\",\"description\":\"OSE App Store\",\"group\":\"\"}', '{}', '', '', 0, '0000-00-00 00:00:00', 0, 0);";
			$db->setQuery($query);
			$db->query();
			$id = $db->insertid();
		
			$query = "INSERT INTO `#__menu` (`id`, `menutype`, `title`, `alias`, `note`, `path`, `link`, `type`, `published`, `parent_id`, `level`, `component_id`, `ordering`, `checked_out`, `checked_out_time`, `browserNav`, `access`, `img`, `template_style_id`, `params`, `lft`, `rgt`, `home`, `language`, `client_id`) VALUES
			(NULL, 'main', 'OSE App Center™', 'OSE App Center™', '', 'OSE App Center™', 'index.php?option=com_ose_appcenter', 'component', 1, 1, 1, ".(int)$id.", 0, 0, '0000-00-00 00:00:00', 0, 1, '\"components/com_ose_appcenter/favicon.ico\"', 0, '', 9, 10, 0, '', 1);";
			$db->setQuery($query);
			$result = $db->query();
		}
		$query = "SELECT COUNT(`id`) FROM `#__extensions` WHERE `element` = 'oseupdatecheck'";
		$db->setQuery($query);
		$result = $db->query();
		if ($result==0)
		{
			$query = "INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES
			(NULL, 'System OSE Update Check plugin', 'plugin', 'oseupdatecheck', 'system', 0, 1, 1, 1, '{\"legacy\":false,\"name\":\"System OSE Update Check plugin\",\"type\":\"plugin\",\"creationDate\":\"28-May-2012\",\"author\":\"Open Source Excellence\",\"copyright\":\"Copyright (C) 2009 Open Source Excellence. All rights reserved.\",\"authorEmail\":\"\",\"authorUrl\":\"\",\"version\":\"5.0\",\"description\":\"OSE Update Check plugin - Checks the latest version of OSE software\",\"group\":\"\"}', '', '', '', 0, '0000-00-00 00:00:00', 0, 0);";
			$db->setQuery($query);
			$result = $db->query();
		}
			
		$query = "CREATE TABLE IF NOT EXISTS `#__ose_app_apps` (
		`id` int(11) NOT NULL AUTO_INCREMENT,
		`title` text,
		`cat` varchar(20) DEFAULT NULL,
		`isfree` tinyint(1) DEFAULT NULL,
		`interval` tinyint(1) DEFAULT NULL,
		`cur_version` varchar(11) DEFAULT NULL,
		`new_version` varchar(11) DEFAULT NULL,
		`checked_date` date DEFAULT NULL,
		`remote_id` int(11) DEFAULT NULL,
		`published` tinyint(1) DEFAULT '1',
		`image` text,
		`downloadedpath` text,
		`downloadeddate` date DEFAULT NULL,
		`option` text,
		`type` varchar(50) NOT NULL,
		PRIMARY KEY (`id`)
		) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;";
		$db->setQuery($query);
		$result = $db->query();
			
			
		$query = "SELECT COUNT(`id`) FROM `#__ose_app_apps`";
		$db->setQuery($query);
		$result = $db->query();
		if ($result==0)
		{
			$query = "INSERT INTO `#__ose_app_apps` (`id`, `title`, `cat`, `isfree`, `interval`, `cur_version`, `new_version`, `checked_date`, `remote_id`, `published`, `image`, `downloadedpath`, `downloadeddate`, `option`, `type`) VALUES
			(NULL, 'OSE App Center™', 'utility', 1, 7, '', '', '2012-06-10', 33, 2, 'oseappcenter.png', '', '0000-00-00', 'com_ose_appcenter', 'com'),
			(NULL, 'OSE App Update Check plugin™', 'utility', 0, 7, '', '', '2012-06-10', 37, 1, 'oseappcenter.png', NULL, NULL, 'oseupdatecheck', 'plg.system'),
			(NULL, 'OSE File Manager™', 'utility', 1, 7, '', '', '2012-06-10', 34, 1, 'osefileman.png', NULL, NULL, 'com_ose_fileman', 'com'),
			(NULL, 'OSE Anti-Hacker™', 'security', 0, 3, '', '', '2012-06-10', 35, 1, 'oseantihacker.png', NULL, NULL, 'com_ose_antihacker', 'com'),
			(NULL, 'OSE Anti-Virus™', 'security', 0, 3, '', '', '2012-06-10', 36, 1, 'oseantivirus.png', NULL, NULL, 'com_ose_antivirus', 'com'),
			(NULL, 'OSE Anti-BruteForce™', 'security', 0, 3, '', '', '2012-06-10', 37, 1, 'oseantibruteforce.png', NULL, NULL, 'ose_antibruteforce', 'plg.authentication');";
			$db->setQuery($query);
			$result = $db->query();
		}
			
		$fields= $db->getTableFields('#__oseipm_iptable');
		if(!isset($fields['#__oseipm_iptable']['country'])) {
			$query= "ALTER TABLE `#__oseipm_iptable` ADD `country` VARCHAR( 3 ) NULL DEFAULT NULL; ";
			$db->setQuery($query);
			if(!$db->query()) {
				echo $db->getErrorMsg();
				return false;
			}
		}
		
		$fields= $db->getTableFields('#__oseath_l1rules');
		if(!isset($fields['#__oseath_l1rules']['aclid'])) {
			$query= "ALTER TABLE `#__oseath_l1rules` ADD `aclid` INT( 11 ) NOT NULL AFTER `id`; ";
			$db->setQuery($query);
			if(!$db->query()) {
				echo $db->getErrorMsg();
				return false;
			}
		}
			
		$fields= $db->getTableFields('#__oseath_l2rules');
		if(!isset($fields['#__oseath_l2rules']['aclid'])) {
			$query= "ALTER TABLE `#__oseath_l2rules` ADD `aclid` INT( 11 ) NOT NULL AFTER `id`; ";
			$db->setQuery($query);
			if(!$db->query()) {
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
		return true;
	}
	function upgrade510()
	{
		$db = JFactory::getDBO();
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
		$result = self::installGeoIPDB();
		if ($result==false)
		{
			return false;
		}
		return true;
	}
	function upgrade513()
	{
		$db = JFactory::getDBO();
		
		$fields= $db->getTableFields('#__oseav_detected');
		if(!isset($fields['#__oseav_detected']['type'])) {
			$query = "ALTER TABLE `#__oseav_detected` ADD `type` TEXT NOT NULL;";
			$db->setQuery($query);
			if(!$db->query()) {
				echo $db->getErrorMsg();
				return false;
			}
		}
		$query = "ALTER TABLE `#__oseav_scanitems` CHANGE `ext` `ext` VARCHAR( 10 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;";
		$db->setQuery($query);
		if(!$db->query())
		{
			echo JText :: _('Unable to alter the detected files table');
			echo $db->getErrorMsg();
			return false;
		}
		return true;		
	}
	
	function upgrade521()
	{
		$db = JFactory::getDBO();
		$query = "SELECT * FROM `#__extensions` WHERE `name` = 'com_categories' "; 
		$db->setQuery($query);
		$result = $db->loadResult(); 
		if (empty($result))
		{	
			$query = "INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES ".
					 "(10014, 'com_categories', 'component', 'com_categories', '', 1, 1, 1, 0, '{\"legacy\":false,\"name\":\"com_categories\",\"type\":\"component\",\"creationDate\":\"December 2007\",\"author\":\"Joomla! Project\",\"copyright\":\"(C) 2005 - 2012 Open Source Matters. All rights reserved.\",\"authorEmail\":\"admin@joomla.org\",\"authorUrl\":\"www.joomla.org\",\"version\":\"2.5.0\",\"description\":\"COM_CATEGORIES_XML_DESCRIPTION\",\"group\":\"\"}', '{}', '', '', 0, '0000-00-00 00:00:00', 0, 0),".
					 "(10015, 'plg_editors_none', 'plugin', 'none', 'editors', 0, 1, 1, 0, '{\"legacy\":false,\"name\":\"plg_editors_none\",\"type\":\"plugin\",\"creationDate\":\"August 2004\",\"author\":\"Unknown\",\"copyright\":\"\",\"authorEmail\":\"N\\/A\",\"authorUrl\":\"\",\"version\":\"2.5.0\",\"description\":\"PLG_NONE_XML_DESCRIPTION\",\"group\":\"\"}', '{}', '', '', 0, '0000-00-00 00:00:00', 0, 0),".
					 "(10016, 'plg_editors_tinymce', 'plugin', 'tinymce', 'editors', 0, 1, 1, 0, '{\"legacy\":false,\"name\":\"plg_editors_tinymce\",\"type\":\"plugin\",\"creationDate\":\"2005-2012\",\"author\":\"Moxiecode Systems AB\",\"copyright\":\"Moxiecode Systems AB\",\"authorEmail\":\"N\\/A\",\"authorUrl\":\"tinymce.moxiecode.com\\/\",\"version\":\"3.5.2\",\"description\":\"PLG_TINY_XML_DESCRIPTION\",\"group\":\"\"}', '{\"mode\":\"2\",\"skin\":\"0\",\"entity_encoding\":\"raw\",\"lang_mode\":\"0\",\"lang_code\":\"en\",\"text_direction\":\"ltr\",\"content_css\":\"1\",\"relative_urls\":\"1\",\"newlines\":\"0\",\"invalid_elements\":\"script,applet,iframe\",\"toolbar\":\"top\",\"toolbar_align\":\"left\",\"html_height\":\"550\",\"html_width\":\"750\",\"resizing\":\"true\",\"resize_horizontal\":\"false\",\"element_path\":\"1\",\"fonts\":\"1\",\"paste\":\"1\",\"searchreplace\":\"1\",\"insertdate\":\"1\",\"format_date\":\"%Y-%m-%d\",\"inserttime\":\"1\",\"format_time\":\"%H:%M:%S\",\"colors\":\"1\",\"table\":\"1\",\"smilies\":\"1\",\"media\":\"1\",\"hr\":\"1\",\"directionality\":\"1\",\"fullscreen\":\"1\",\"style\":\"1\",\"layer\":\"1\",\"xhtmlxtras\":\"1\",\"visualchars\":\"1\",\"visualblocks\":\"1\",\"nonbreaking\":\"1\",\"template\":\"1\",\"blockquote\":\"1\",\"wordcount\":\"1\",\"advimage\":\"1\",\"advlink\":\"1\",\"advlist\":\"1\",\"autosave\":\"1\",\"contextmenu\":\"1\",\"inlinepopups\":\"1\"}', '', '', 0, '0000-00-00 00:00:00', 0, 0);";
			$db->setQuery($query);
			if(!$db->query())
			{
				echo JText :: _('Unable to alter the extension table');
				echo $db->getErrorMsg();
				return false;
			}
		}
		jimport('joomla.filesystem.file');
		if (file_exists(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_ose_antihacker'.DS.'admin.ose_antihacker.php'))
		{	
			JFile::delete(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_ose_antihacker'.DS.'admin.ose_antihacker.php');
		}
		if (file_exists(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_ose_antivirus'.DS.'admin.ose_antivirus.php'))
		{
			JFile::delete(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_ose_antivirus'.DS.'admin.ose_antivirus.php');
		}
		if (file_exists(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_ose_fileman'.DS.'admin.ose_fileman.php'))
		{
			JFile::delete(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_ose_fileman'.DS.'admin.ose_fileman.php');
		}	
		return true;
	}
	
	public static function installGeoIPDB()
	{
		$db = JFactory::getDBO();
		$query = "CREATE TABLE IF NOT EXISTS `#__ose_geoip` (
				`id` int(11) NOT NULL AUTO_INCREMENT,
				`ip32_start` text NOT NULL,
				`ip32_end` text NOT NULL,
				`country_code` varchar(2) NOT NULL,
				PRIMARY KEY (`id`)
				) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
		$db->setQuery($query);
		if (!$db->query()) {
			echo $img_ERROR . JText::_('Unable to create table') . $BR;
			echo $db->getErrorMsg();
			return false;
		}
		$db = JFactory::getDBO();
		$query = "SELECT COUNT(id) FROM #__ose_geoip ";
		$db->setQuery($query);
		$result = $db->loadResult();
		
		if ($result == 0) {
			for ($i = 0; $i <= 6; $i++) {
				$file = JPATH_COMPONENT . DS . 'sql' . DS . 'osegeoip' . $i . '.sql';
				$data = JFile::read($file);
				$queries = self::_splitQueries($data);
				foreach ($queries as $query) {
					$db->setQuery($query);
					if (!$db->query()) {
						echo JText::_('Unable to insert GeoIP record') . '-' . $i;
						echo $db->getErrorMsg();
						return false;
					}
				}
				unset($queries);
				unset($data);
			}
		}
		return true;
	}
	public static function _splitQueries($sql)
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
}