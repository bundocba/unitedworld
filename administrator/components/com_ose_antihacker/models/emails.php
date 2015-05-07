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
jimport('joomla.application.component.modeladmin');
class ose_antihackerModelEmails extends JModelAdmin
{
	protected $lists= array(), $pagination= null;
	function __construct()
	{
		parent :: __construct();
	}
	function getEmails()
	{
		$db= JFactory :: getDBO();
		$where= array();
		$where[]=" `app` = 'antihack'"; 
		$where= oseDB :: implodeWhere($where);
		$query= " SELECT id, subject " .
				" FROM `#__ose_app_email` " .
				$where." ORDER BY `id`";
		;
		$db->setQuery($query);
		$results= $db->loadObjectList();
		return $results;
	}
	function getEmail()
	{
		$db= JFactory :: getDBO();
		$where= array();
		$id= JRequest :: getInt('id');
		$where[]= " `id` = ".(int)$id;
		$where[]= " `app` = 'antihack'";
		$where= oseDB :: implodeWhere($where);
		$query= " SELECT * " .
				" FROM `#__ose_app_email` " .
				$where." ORDER BY `id`";
		$db->setQuery($query);
		$result= $db->loadObject();
		return $result;
	}
	public function getTable($type= 'OSEEmails', $prefix= 'Tickets', $config= array())
	{
		return JTable :: getInstance($type, $prefix, $config);
	}
	function save()
	{
		$id= JRequest::getInt('id'); 
		$subject = JRequest::getVar('subject');
		$body = JRequest::getString('body','', 'post', JREQUEST_ALLOWHTML);
		$db = JFactory::getDBO(); 
		$query = " UPDATE `#__ose_app_email` SET `subject` = ".$db->Quote($subject, true).", `body` = ". $db->Quote($body, true).
				 " WHERE `id` = ".(int)$id;
		$db->setQuery($query); 
		$result = $db->query();
		return $result;
	}
	public function getForm($data = array(), $loadData = true)
	{
	}
	
	function getTemplateParams()
	{
		$db= JFactory :: getDBO();
		$where= array();
		$type= JRequest :: getVar('type');
		$results = array();

		$where[]= " `app` = 'antihack' ";
		$where= oseDB :: implodeWhere($where);
		$query= " SELECT `params` " .
				" FROM `#__ose_app_email` " .
				$where." LIMIT 1";
		$db->setQuery($query);
		$result= $db->loadResult();
		$results = oseJSON::decode($result);
		
		$return = array();
		$i = 0 ;
		foreach ($results as $key => $value)
		{
			$return[$i]['key'] = $key;
			$return[$i]['value'] = $value;
			$i++;
		}
		return $return;
	}
}
?>