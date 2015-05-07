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
jimport('joomla.application.component.model');
class ose_antihackerModelLoadrulesets extends JModel
{
	protected $lists= array(), $pagination= null;
	function __construct()
	{
		parent :: __construct();
	}
	function loadJoomlaRuleset()
	{
		$db = JFactory::getDBO(); 
		$keys[] = 'COOKIE.CFCLIENT_AVON';
		$keys[] = 'COOKIE.CFCLIENT_LAUSANNE';
		$keys[] = 'COOKIE.CFCLIENT_CFGLOBALS';
		$keys[] = 'COOKIE.omp__super_properties';
		$keys[] = 'COOKIE._okbk';
		$keys[] = 'COOKIE.__utmz';
		$keys[] = 'POST.install_url';
		$keys[] = 'POST.json';
		$keys[] = 'POST.text';
		$keys[] = 'POST.text_mail_new_registration_registrant';
		$keys[] = 'POST.install_directory';
		$keys[] = 'POST.cfg_reg_first_visit_url';
		$keys[] = 'POST.cfg_reg_pend_appr_msg';
		$keys[] = 'POST.cfg_reg_welcome_msg';
		$keys[] = 'POST.filterfieldlist';
		$keys[] = 'POST.params';
		$keys[] = 'POST.sortfields';
		$keys[] = 'POST.title';
		$keys[] = 'POST.url';
		$return = true; 
		foreach ($keys as $key)
		{
			$query = "SELECT COUNT(id) FROM `#__oseath_l2rules` WHERE `key` = ".$db->Quote($key);
			$db->setQuery($query); 
			$result = $db->loadResult();
			if ($result == 0)
			{
				$query = "INSERT INTO `#__oseath_l2rules` (`id`, `aclid`, `key`, `trimmed_value`, `keyaction`, `target`, `targetaction`, `times`, `filters`) VALUES
						 (NULL, 0, ".$db->Quote($key, true).", NULL, 3, '', '', 1, '');";
				$db->setQuery($query);
				$return = $db->query(); 
			}
		}
		return $return; 
	}
	function loadwpRuleset()
	{
		$db = JFactory::getDBO();
		$keys[] = 'POST.siteurl';
		$keys[] = 'POST.redirect_to';
		$keys[] = 'POST.return_to';
		$keys[] = 'POST.home';
		$keys[] = 'POST.current_url';
		$keys[] = 'POST.referredby';
		$keys[] = 'POST._wp_original_http_referer';
		$keys[] = 'POST.link';
		$keys[] = 'POST._jd_wp_twitter';
		$return = true;
		foreach ($keys as $key)
		{
			$query = "SELECT COUNT(id) FROM `#__oseath_l2rules` WHERE `key` = ".$db->Quote($key);
			$db->setQuery($query);
			$result = $db->loadResult();
			if ($result == 0)
			{
				$query = "INSERT INTO `#__oseath_l2rules` (`id`, `aclid`, `key`, `trimmed_value`, `keyaction`, `target`, `targetaction`, `times`, `filters`) VALUES
								 (NULL, 0, ".$db->Quote($key, true).", NULL, 3, '', '', 1, '');";
				$db->setQuery($query);
				$return = $db->query();
			}
		}
		return $return;
	}

}
?>