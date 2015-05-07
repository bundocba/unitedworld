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
defined('_JEXEC') or die(';)');
class ose_antihackerModelAntihacker extends ose_antihackerModel
{
	protected $lists= array(), $pagination= null;
	function __construct()
	{
		parent :: __construct();
	}
	function addwhitelsitkey($key)
	{
		$db=JFactory::getDBO(); 
		$query = "SELECT COUNT(id) FROM `#__oseath_whitelist` WHERE `string` = ".$db->Quote($key, true);
		$db->setQuery($query);
		$result = $db->loadResult();
		if ($result == 0)
		{
			$query = "INSERT INTO `#__oseath_whitelist`  (`id`, `string`, `layer`) VALUES
								 (NULL, ".$db->Quote($key, true).", 1);";
			$db->setQuery($query);
			return  $db->query();
		}
		else {
			return true; 
		}
	}
	function removeWhitelistKeys($ids)
	{
		$db=JFactory::getDBO();
		$where = "`id` IN (".implode(",", $ids).")"; 
		$query = "DELETE FROM `#__oseath_whitelist` WHERE ".$where;
		$db->setQuery($query);
		$result = $db->query();
		return $result; 
	}
	function getWhitelistKeys()
	{
		$start= JRequest :: getInt('start', 0);
		$limit= JRequest :: getInt('limit', 0);
		$return = array(); 
		
		$db=JFactory::getDBO();
		$query = "SELECT COUNT(id) FROM `#__oseath_whitelist` ";
		$db->setQuery($query);
		$return['total'] = $db->loadResult();
		
		$query = "SELECT * FROM `#__oseath_whitelist` ";
		$db->setQuery($query, $start, $limit);
		$results = $db->loadObjectlist();
		foreach ($results as $result)
		{
			$result -> layer = JText::_('Layer')." ". $result -> layer;  
		}
		$return['results'] = $results; 
		return $return; 
	}
}	