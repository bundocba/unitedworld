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
jimport( 'joomla.application.component.model' );
class oseipModelIplist extends oseipModel
{
	var $_data;
	var $_total = null;
	var $_pagination = null;
	function __construct()
	{
		parent::__construct();
	} 
	function getList()
	{
		// initialize variables
		$where = array();
		$where = array_merge($where,$this->generateQueryWhere());
	    $where = ( count( $where ) ? ' WHERE (' . implode( ') AND (', $where ) . ')' : '' );
		$query = " SELECT acl.*, "
				." (SELECT ip FROM `#__oseipc_ips` WHERE id = MAX(ip.id)) AS ip_end, " 
				." (SELECT ip FROM `#__oseipc_ips` WHERE id = MIN(ip.id)) AS ip_start "
				." FROM `#__oseipc_ips` AS ip "
				." INNER JOIN `#__oseipc_acl` AS acl ON acl.id = ip.acl_id " 
				. $where
				. ' GROUP BY ip.acl_id'
				;
		$this->_total = $this->_getListCount($query);
	
		$start = JRequest::getInt('start',0);
		$limit = JRequest::getInt('limit',1);
		$this->_db->setQuery($query,$start,$limit);
		//echo $this->_db->_sql;
		$rows = $this->_db->loadAssocList();
		
		foreach($rows as $key => $row)
		{
			$rows[$key]['ip_start'] = '121.12.22.22';
			$rows[$key]['ip_end'] = '121.12.22.255';
		}
		//var_dump($rows);
		$rows = $this->JEncode($rows);
		return $rows;
	}
	function getListTotal()
	{
		if(!$this->_total)
		{
			$this->getList();
		}
		
		return $this->_total;
	}
	
	function JEncode($arr)
	{
	    if (version_compare(PHP_VERSION,"5.2","<"))
	    {    
	        //require_once("./JSON.php");   //if php<5.2 need JSON class
	        $json = new Services_JSON();  //instantiate new json object
	        $data=$json->encode($arr);    //encode the data in json format
	    } else
	    {
	        $data = json_encode($arr);    //encode the data in json format
	    }
	    return $data;
	}
	
	function generateQueryWhere()
	{
		$filters = JRequest::getVar('filter',null);

		// GridFilters sends filters as an Array if not json encoded
		if (is_array($filters)) {
		    $encoded = false;
		} else {
		    $encoded = true;
		    $filters = json_decode($filters);
		}
		
		$where = array();
		
		// loop through filters sent by client
		if (is_array($filters)) {
		    for ($i=0;$i<count($filters);$i++){
		        $filter = $filters[$i];
		
		        // assign filter data (location depends if encoded or not)
		        if ($encoded) {
		            $field = $filter->field;
		            $value = $filter->value;
		            $compare = isset($filter->comparison) ? $filter->comparison : null;
		            $filterType = $filter->type;
		        } else {
		            $field = $filter['field'];
		            $value = $filter['data']['value'];
		            $compare = isset($filter['data']['comparison']) ? $filter['data']['comparison'] : null;
		            $filterType = $filter['data']['type'];
		        }
		    }
		    
		    switch($filterType){
            case 'string' : $qs .= " AND ".$field." LIKE '%".$value."%'"; Break;
            case 'list' :
                if (strstr($value,',')){
                    $fi = explode(',',$value);
                    for ($q=0;$q<count($fi);$q++){
                        $fi[$q] = "'".$fi[$q]."'";
                    }
                    $value = implode(',',$fi);
                    $where[] = $field." IN (".$value.")";
                }else{
                    $where[] = "{$field} = '{$value}'";
                }
            Break;
            
            
        	}
	    }
	    
	    return $where;
	}
	
	function update()
	{
		echo '0';
	}
	
	function store()
	{	
		$data = JRequest::get( 'post' );
			
		// Keep the Key Value in Your Mind
		// $ip => array('ip1','ip2','ip3','ip4')
		// $ips => array('ips1','ips2','ips3','ips4')
		
		foreach($data['ip'] as $key => $ip)
		{
			$data['ip'][$key] = (empty($ip))?0:$data['ip'][$key];
		}
		
		foreach($data['ips'] as $key => $ip)
		{
			$data['ips'][$key] = (empty($ip) and $ip !='0')?255:$data['ips'][$key];
		}
		
	
		//check whether ip is greater that ips
		if($data['iptype']=='ips')
		{
			foreach ($data['ip'] as $key =>$ip)
			{
				if($ip>$data['ips'][$key])
				{
					JError::raiseWarning('500','IP is greater than IPS');
					return false;
				}
			}
		}
		
		
		$db = &JFactory::getDBO();
		if($data['id']=='0')
		{
			$query = "INSERT INTO #__ose_ipcontrol_ip (status, iptype) VALUES ( '{$data['status']}', '{$data['iptype']}')";
			$db->setQuery($query) ;
	        $db->query();
	        $list_id=$db->insertid();
		}else
		{
			$query = 'DELETE FROM #__ose_ipcontrol_ip_list'
			. ' WHERE list_id IN ( '. $data['id'] .' )';
			$db->setQuery( $query );
			if (!$db->query()) 
			{
				$this->setError($db->getErrorMsg());
				return false;
			}
			
			$query = "UPDATE #__ose_ipcontrol_ip SET status = '{$data['status']}' WHERE id = '{$data['id']}'";
			$db->setQuery($query) ;
	        $db->query();
	        $list_id=$data['id'];
	        $data['id']='0';
	       
		}
		
		
		
		if($data['iptype']=='ips')
		{
			//cycle count
			$a=($data["ips"]["ip4"]-$data["ip"]["ip4"]+1+($data["ips"]["ip3"]-$data["ip"]["ip3"])*256+($data["ips"]["ip2"]-$data["ip"]["ip2"])*pow(256,2)+($data["ips"]["ip1"]-$data["ip"]["ip1"])*pow(256,3));
			
			for($i=1;$i<=$a;$i++)
			{
				$ip="ip1={$data["ip"]["ip1"]}&ip2={$data["ip"]["ip2"]}&ip3={$data["ip"]["ip3"]}&ip4={$data["ip"]["ip4"]}";
				$dataip=array("option"=>$data["option"], "id"=>$data["id"],"task"=>$data["task"],"view"=>$data["view"],"controller"=>$data["controller"],"list_id"=>$list_id,"ip"=>$ip);
			
				$this->saveip($dataip);
				$data["ip"]["ip4"]++;
					
				if($data["ip"]["ip4"]>255)
					{
						$data["ip"]["ip4"]=0;
						$data["ip"]["ip3"]++;
						if($data["ip"]["ip3"]>255)
						{
							$data["ip"]["ip3"]=0;
							$data["ip"]["ip2"]++;
							if($data["ip"]["ip2"]>255)
							{
								$data["ip"]["ip2"]=0;
								$data["ip"]["ip1"]++;
							}
						}
					}
				}
		}else
		{
			$ip="ip1={$data["ip"]["ip1"]}&ip2={$data["ip"]["ip2"]}&ip3={$data["ip"]["ip3"]}&ip4={$data["ip"]["ip4"]}";
			$dataip=array("option"=>$data["option"], "id"=>$data["id"],"task"=>$data["task"],"view"=>$data["view"],"controller"=>$data["controller"],"list_id"=>$list_id,"ip"=>$ip);
			$this->saveip($dataip);
		}
		return true;
	}
}