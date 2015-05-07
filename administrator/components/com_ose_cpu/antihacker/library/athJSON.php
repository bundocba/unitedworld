<?php
if (!defined('_JEXEC') && !defined('OSE_ADMINPATH'))
{
	die("Direct Access Not Allowed");
}

class athJSON
{
	function generateQueryWhere()
	{
		$db = oseDB::instance();
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
	            case 'string' :
	            	$where[] =  $field." LIKE '%".$db->Quote($value)."%'";
	            break;

	            case 'list' :
	                if (strstr($value,',')){
	                    $fi = explode(',',$value);
	                    for ($q=0;$q<count($fi);$q++){
	                        $fi[$q] = $db->Quote($fi[$q]);
	                    }
	                    $value = implode(',',$fi);
	                    $where[] = $field." IN (".$value.")";
	                }else{
	                    $where[] = "{$field} = ".$db->Quote($value);
	                }
	            break;


        	}
	    }

	    return $where;
	}

	function encode($arr)
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

	function decode($json,$assoc = false)
	{
	    if (version_compare(PHP_VERSION,"5.2","<"))
	    {
	        //require_once("./JSON.php");   //if php<5.2 need JSON class
	        $json = new Services_JSON();  //instantiate new json object
	        $data=$json->decode($json,$assoc);    //encode the data in json format
	    } else
	    {
	        $data = json_decode($json,$assoc);    //encode the data in json format
	    }
	    return $data;
	}

}
?>
