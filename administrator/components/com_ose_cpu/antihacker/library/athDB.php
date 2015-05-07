<?php
if (!defined('_JEXEC') && !defined('OSE_ADMINPATH'))
{
	die("Direct Access Not Allowed");
}

class athDB
{
	function __construct()
	{

	}

	public static function instance($type = null)
	{
		static $instance;

		if(empty($instance))
		{

			if (defined('OSE_ADMINPATH'))
			{
				require_once(OSE_ADMINPATH.OSEDS.'components'.OSEDS.'com_ose_cpu'.OSEDS.'oseregistry'.OSEDS.'database'.OSEDS.'osedbMySQL.php');
				$config = new SConfig();
				$options['host']= $config->host;
				$options['user']= $config->user;
				$options['password']= $config->password;
				$options['database']= $config->db;
				$options['prefix']= $config->dbprefix;
				$options['select']= true;
				unset ($config);
				$instance = new OSEDatabaseMySQL($options);
			}
			else
			{
				$instance = JFactory::getDBO();
			}
		}

		return $instance;
	}

	public static function update($table,$keyId,$updateValues)
	{
		$db = oseDB::instance();

		oseDB::lock($table);

		$filterValues = array();
		foreach( $updateValues as $key => $value)
		{
			if($key == $keyId)
			{
				continue;
			}
			$filterValues[$key] = $db->Quote($value);
		}

		$tables = $db->getTableFields($table);

		$temp = array();
		//oseExit($filterValues);
		foreach ( $tables[$table] as $field => $info)
		{
			if(!empty($filterValues[$field]))
			{
				$temp[$field] = $filterValues[$field];
			}
		}

		$filterValues = $temp;

		$sql = array();

		foreach($filterValues as $key => $value)
		{
			$sql[] = "`{$key}` = {$value}";
		}

		if(empty($sql))
		{
			oseDB::unlock();
			return true;
		}
		$query = " UPDATE `{$table}` "
				." SET  ". implode(',',$sql)
				." WHERE `{$keyId}` = ".$db->Quote($updateValues[$keyId])
				;

		$db->setQuery($query);

		$result = oseDB::query();

		oseDB::unlock();

		return $result;
	}

	public static function insert($table,$insertValues)
	{
		$db = oseDB::instance();

		oseDB::lock($table);

		$filterValues = array();
		foreach( $insertValues as $key => $value)
		{
			$filterValues[$key] = $db->Quote($value);
		}
		//oseExit($table);
		$tables = $db->getTableFields($table);

		$temp = array();

		foreach ( $tables[$table] as $field => $info)
		{
			if(!empty($filterValues[$field]))
			{
				$temp[$field] = $filterValues[$field];
			}
		}
		//oseExit($filterValues);
		$filterValues = $temp;

		$sql = array();

		$sql1 = '`'.implode('`,`',array_keys($filterValues)).'`';
		$sql2 = ''.implode(',',$filterValues).'';

		if(empty($filterValues))
		{
			oseDB::unlock();
			return true;
		}
		$query = " INSERT INTO `{$table}` "
				." ({$sql1})  "
				." VALUES"
				." ({$sql2})"
				;

		$db->setQuery($query);

		$result = (oseDB::query())?$db->insertid():false;
		//oseExit($db->_sql);
		oseDB::unlock();

		return $result;
	}

	public static function loadList($type = 'array',$key = null)
	{
		$db = oseDB::instance();

		switch($type)
		{
			case('obj'):
				return $db->loadObjectList($key);
			break;

			default:
				return $db->loadAssocList($key);
			break;
		}
	}

	public static function loadItem($type = 'array',$key = null)
	{
		$db = oseDB::instance();

		switch($type)
		{
			case('obj'):
				return $db->loadObject();
			break;

			default:
				return $db->loadAssoc();
			break;
		}
	}

	public function query($unlock = false)
	{
		$testMode = 1;

		$db = oseDB::instance();

		if(!$db->query())
		{
			if($testMode == 1)
			{
				oseExit($db->getErrorMsg());
			}

			if($unlock)
			{
				oseDB::unlock();
			}

			return false;
		}

		return true;
	}

	public static function lock($query)
	{
		$db = oseDB::instance();
		$db->setQuery("LOCK TABLE ". $query);
		return $db->query();
	}

	public static function unlock()
	{
		$db = oseDB::instance();
		$query = "UNLOCK TABLES";
		$db->setQuery($query);
		return $db->query();
	}

	public static function implodeWhere($where = array())
	{
		$where = ( count( $where ) ? ' WHERE (' . implode( ') AND (', $where ) . ')' : '' );
		return $where;
	}

}