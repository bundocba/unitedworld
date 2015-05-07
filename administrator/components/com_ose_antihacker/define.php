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
defined('_JEXEC') or die(";)");
// Define some properties
define('OSEATH', 'com_ose_antihacker');
define('OSEATHTITLE', 'OSE Anti-Hacker™');
define('OSEANTIHACKERVER', $OSESoftHelper->getVersion());
define('OSEATHV4SIGVER', '5.0.0.12.10.30');
define('OSEATHFOLDER', 'components/'.OSEATH);
define('OSEATH_B_PATH', JPATH_ADMINISTRATOR.DS.'components'.DS.OSEATH);
define('OSEATH_B_CONTROLLER', OSEATH_B_PATH.DS.'controllers');
define('OSEATH_B_MODEL', OSEATH_B_PATH.DS.'models');
define('OSEATH_B_VIEW', OSEATH_B_PATH.DS.'views');
define('OSEATH_B_EXTJS', OSEATH_B_PATH.DS.'js');
define('OSECPU', JPATH_ADMINISTRATOR.DS.'components'.DS.'com_ose_cpu');
define('OSEDS', DIRECTORY_SEPARATOR);
$version = new JVersion();
$version = substr($version->getShortVersion(),0,3);
if(!defined('JOOMLA16'))
{
	$value = ($version >= '1.6')?true:false;
	define('JOOMLA16',$value);
}
if(!defined('JOOMLA30'))
{
	$value = ($version >= '3.0' && $version <='5.0')?true:false;
	define('JOOMLA30',$value);
}
if (function_exists('ini_set'))
{
	ini_set("max_execution_time", 0);
	ini_set("memory_limit", '512M');
}
?>