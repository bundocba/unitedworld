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
defined('_JEXEC') or die;
class OseantihackerHelper {
	public static $extension= 'com_osemsc';
	/**
	 * Configure the Linkbar.
	 *
	 * @param	string	$vName	The name of the active view.
	 *
	 * @return	void
	 * @since	1.6
	 */
	public static function addSubmenu($vName) {
		$db= & JFactory :: getDBO();
		jimport('joomla.version');
		$version= new JVersion();
		$version= substr($version->getShortVersion(), 0, 3);
		if($version >= '1.6') {
			$query= "SELECT * FROM `#__menu` WHERE `alias` =  'OSE Anti-Hacker™'";
			$db->setQuery($query);
			$results= $db->loadResult();
			if(empty($results)) {
				$query= "UPDATE `#__menu` SET `alias` =  'OSE Anti-Hacker™', `path` =  'OSE Anti-Hacker™', `published`=1, `img` = '\"components/com_ose_antihacker/favicon.ico\"'  WHERE `component_id` = ( SELECT extension_id FROM `#__extensions` WHERE element ='com_ose_antihacker' ) ";
				$db->setQuery($query);
				$db->query();
			}
		}
		else
		{
			$menus[0]= "OSE Anti Hacker";
			$menus[1]= "OSE Anti Virus";
			$menus[2]= "OSE CPU";
			$menus[3]= "OSE Fileman";

			foreach ($menus as $menu)
			{
				$query= "SELECT `id` FROM `#__components` WHERE `name` =  '{$menu}'";
				$db->setQuery($query);
				$results= $db->loadObjectList();
				$numrecord = count($results);
				if ($numrecord>1)
				{
					$i = 0;
					foreach ($results as $result)
					{
						if ($i==$numrecord-1)
						{
							break;
						}
						else
						{
						$query = "DELETE FROM `#__components` WHERE `id` = ". (int)$result->id;
						$db->setQuery($query);
						$db->query();
						}
						$i ++;
					}
				}
			}
		}

		$fields= $db->getTableFields('#__oseath_l1rules');
		if(!isset($fields['#__oseath_l1rules']['trimmed_value'])) {
			$query= "ALTER TABLE `#__oseath_l1rules` ADD  `trimmed_value` longtext DEFAULT NULL;";
			$db->setQuery($query);
			if(!$db->query()) {
				echo JText::_('Unable to Migrating Anti-Hacker Table') ;
				return false;
			}
		}

		$fields= $db->getTableFields('#__oseath_l2rules');
		if(!isset($fields['#__oseath_l2rules']['trimmed_value'])) {
			$query= "ALTER TABLE `#__oseath_l2rules` ADD  `trimmed_value` longtext DEFAULT NULL;";
			$db->setQuery($query);
			if(!$db->query()) {
				echo JText::_('Unable to Migrating Anti-Hacker Table') ;
				return false;
			}
		}

		$fields= $db->getTableFields('#__oseath_l2rules');
		if(!isset($fields['#__oseath_l2rules']['filters'])) {
			$query= "ALTER TABLE `#__oseath_l2rules` ADD `filters` TEXT NULL; ";
			$db->setQuery($query);
			if(!$db->query()) {
				echo JText::_('Unable to Migrating Anti-Hacker Table') ;
				return false;
			}
		}
		$vName= JRequest :: getCmd('view');
		JSubMenuHelper :: addEntry(JText :: _('Dashboard'), 'index.php?option=com_ose_antihacker', $vName == '');
		JSubMenuHelper :: addEntry(JText :: _('Activation'), 'index.php?option=com_ose_antihacker&view=activation', $vName == 'activation');
		JSubMenuHelper :: addEntry(JText :: _('Blacklisted IPs'), 'index.php?option=com_ose_antihacker&view=blacklisted', $vName == 'blacklisted');
		JSubMenuHelper :: addEntry(JText :: _('Whitelisted IPs'), 'index.php?option=com_ose_antihacker&view=whitelisted', $vName == 'whitelisted');
		JSubMenuHelper :: addEntry(JText :: _('Monitored IPs'), 'index.php?option=com_ose_antihacker&view=monitored', $vName == 'monitored');
		JSubMenuHelper :: addEntry(JText :: _('Add IPs'), 'index.php?option=com_ose_antihacker&view=addips', $vName == 'addips');
		JSubMenuHelper :: addEntry(JText :: _('Duplicated IPs'), 'index.php?option=com_ose_antihacker&view=duplicated', $vName == 'duplicated');
		JSubMenuHelper :: addEntry(JText :: _('Layer 1 Attacks'), 'index.php?option=com_ose_antihacker&view=layer1attacks', $vName == 'layer1attacks');
		JSubMenuHelper :: addEntry(JText :: _('Layer 2 Attacks'), 'index.php?option=com_ose_antihacker&view=layer2attacks', $vName == 'layer2attacks');
		JSubMenuHelper :: addEntry(JText :: _('Configuration'), 'index.php?option=com_ose_antihacker&view=configuration', $vName == 'configuration');
		JSubMenuHelper :: addEntry(JText :: _('Tools'), 'index.php?option=com_ose_antihacker&view=tools', $vName == 'tools');
	}
}