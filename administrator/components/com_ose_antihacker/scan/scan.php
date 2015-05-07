<?php
define ('OSE_ADMINPATH', dirname(__FILE__));
define ('OSE_FRONTPATH', dirname(dirname(__FILE__)));
define('OSEDS', DIRECTORY_SEPARATOR);
require_once(OSE_FRONTPATH.OSEDS.'configuration.php');
if (class_exists('SConfig'))
{
	require_once (OSE_ADMINPATH.OSEDS.'components'.OSEDS.'com_ose_cpu'.OSEDS.'antihacker'.OSEDS.'antihacker.php');
	require_once (OSE_ADMINPATH.OSEDS.'components'.OSEDS.'com_ose_cpu'.OSEDS.'antihacker'.OSEDS.'library'.OSEDS.'athDB.php');
	require_once (OSE_ADMINPATH.OSEDS.'components'.OSEDS.'com_ose_cpu'.OSEDS.'antihacker'.OSEDS.'library'.OSEDS.'athJSON.php');
	$antihacker = new oseAntihacker();
	$antihacker->hackScan();
}
else
{
/*** Start of Joomla config ***/
define('_JEXEC', 1);
if (strpos($_SERVER['REQUEST_URI'], "administrator")!=false)
{
	define('JPATH_BASE', dirname(__FILE__));
	define('DS', DIRECTORY_SEPARATOR);
	require_once(JPATH_BASE.DS.'includes'.DS.'defines.php');
	require_once(JPATH_BASE.DS.'includes'.DS.'framework.php');
	$mainframe= JFactory :: getApplication('administrator');
}
else
{
	define('JPATH_BASE', dirname(dirname(__FILE__)));
	define('DS', DIRECTORY_SEPARATOR);
	require_once(JPATH_BASE.DS.'includes'.DS.'defines.php');
	require_once(JPATH_BASE.DS.'includes'.DS.'framework.php');
	$mainframe= JFactory :: getApplication('site');
}
/*** END of Joomla config ***/
require_once( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_ose_cpu'.DS.'define.php');
require_once( OSECPU_B_PATH.DS.'oseregistry'.DS.'oseregistry.php');

oseRegistry::register('registry','oseregistry');
oseRegistry::call('registry');
oseRegistry::register('antihacker','antihacker');
$antihacker = oseRegistry::call('antihacker');
$antihacker->hackScan();
}
?>