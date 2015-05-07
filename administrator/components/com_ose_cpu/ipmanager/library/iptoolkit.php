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
class oseIPtoolkit
{
	function strtoIP($ip)
    {
		//$ip="ip1=192&ip2=168&ip3=1&ip4=1";
		parse_str($ip,$ip);
		$ip=implode(".",$ip);
		return $ip;
	}

	function IPtostr($ip)
	{
	     //$ip="xxx.xxx.xxx.xxx";
		$ips=explode(".",$ip);
		foreach ($ips as $key => $ip)
		{
		$ips[$key]="ip".($key+1)."=".$ip;
		}
		$ip=implode("&",$ips);
		return $ip;
	}

	function delete()
	{
		$db =& JFactory::getDBO();
		$cid = JRequest::getVar( 'cid' , array() , '' , 'array' );
		JArrayHelper::toInteger($cid);

		foreach ($cid as $id)
		{
			$query = "SELECT id, status FROM `#__oseipc_acl` WHERE id IN"
				."(SELECT acl_id FROM `#__oseipc_ips` WHERE id = '{$id}')";
			$db->setQuery($query);
			$acldata=$db->loadObject();
			$query = "DELETE FROM #__oseipc_ips"
			. " WHERE id = '{$id}'";
			$db->setQuery( $query );
			if (!$db->query())
			{
				$this->setError($db->getErrorMsg());
				return false;
			}

			$this->updateAcl($id,$acldata->status,$acldata->id);

		}
				return true;
	}


	function updateAcl($id,$status,$acl_id)
	{
		$db =& JFactory::getDBO();
		$query = "SELECT count(*) FROM `#__oseipc_ips` WHERE id < '{$id}' and acl_id = '{$acl_id}'";
		$db->setQuery($query);
		$result1=$db->loadResult();

		if($result1<1)
		{
			$query="DELETE FROM #__oseipc_acl"
			. " WHERE id = '{$acl_id}'";
			$db->setQuery( $query );
			if (!$db->query())
			{
				$this->setError($db->getErrorMsg());
				return false;
			}
		}else if($result1==1)
		{
			$query = "UPDATE #__oseipc_acl SET iptype = 'ip' WHERE id = '{$acl_id}'";
			$db->setQuery($query) ;
			if (!$db->query())
				{
					$this->setError($db->getErrorMsg());
					return false;
				}
		}

		$query = "SELECT count(*) FROM `#__oseipc_ips` WHERE id > '{$id}' and acl_id = '{$acl_id}'";
		$db->setQuery($query);
		$result2=$db->loadResult();
		if($result2>0)
		{
			if($result2>1)
			{
				$iptype='ips';
			}else
			{
				$iptype='ip';
			}

			$query = "INSERT INTO #__oseipc_acl (status, iptype) VALUES ( '{$status}', '{$iptype}')";
			$db->setQuery($query) ;
			if (!$db->query())
					{
						$this->setError($db->getErrorMsg());
						return false;
					}
		    $Newid=$db->insertid();

			$query = "SELECT id, acl_id FROM `#__oseipc_ips` WHERE id > '{$id}' and acl_id = '{$acl_id}'";
			$db->setQuery($query);
			$objs=$db->loadObjectList();

			foreach ($objs as $obj)
			{
				$query = "UPDATE #__oseipc_ips SET acl_id = '{$Newid}' WHERE id = '{$obj->id}'";
				$db->setQuery($query) ;
		        $db->query();
			}
		}
	}
}

?>