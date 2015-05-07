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
if (!defined('DS'))
{
	define('DS', DIRECTORY_SEPARATOR);
}
// INSTALLATION;
$installedPhpVersion= floatval(phpversion());
$supportedPhpVersion= 5.1;
$install= JRequest :: getVar('install', '', 'REQUEST');
$view= JRequest :: getVar('view', '', 'GET');
$task= JRequest :: getVar('task', '', 'REQUEST');
//install
if(((file_exists(JPATH_COMPONENT.DS.'installer.dummy.ini') || $install)) ||($installedPhpVersion < $supportedPhpVersion))
{
	$app = JFactory::getApplication();
	$app ->JComponentTitle = 'OSE Application Installer'; 
	require_once(dirname(__FILE__).DS.'helpers'.DS.'osesofthelper.php');
	$OSESoftHelper= new OSESoftHelper();
	require_once(JPATH_COMPONENT.DS.'define.php');
	require_once(JPATH_COMPONENT.DS.'installer.helper.php');
	$oseInstaller= new oseInstallerHelper();
	$oseInstaller->install();
	$document = JFactory::getDocument();
	$document->addScript(JURI::root().'media/system/js/mootools-core.js');
}
else
{
require_once(dirname(__FILE__).DS.'helpers'.DS.'osesofthelper.php');
$OSESoftHelper= new OSESoftHelper();
require_once(JPATH_COMPONENT.DS.'define.php');
require_once(JPATH_COMPONENT.DS.'language.php');

JLoader::register('OseantihackerHelper', dirname(__FILE__) . DS . 'helpers' . DS . 'oseantihacker.php');
// Require the base controller
if (JOOMLA30==true)
{	
	require_once( OSEATH_B_CONTROLLER.DS.'controller.php' );
	require_once( OSEATH_B_MODEL.DS.'model.php' );
	require_once( OSEATH_B_VIEW.DS.'view.php' );
}
else
{
	require_once( JPATH_COMPONENT_ADMINISTRATOR.DS.'legacy'.DS.'controller.php' );
	require_once( JPATH_COMPONENT_ADMINISTRATOR.DS.'legacy'.DS.'model.php' );
	require_once( JPATH_COMPONENT_ADMINISTRATOR.DS.'legacy'.DS.'view.php' );
}	
// Create the controller
require_once( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_ose_cpu'.DS.'define.php');
require_once( OSECPU_B_PATH.DS.'oseregistry'.DS.'oseregistry.php');
oseRegistry::register('registry','oseregistry');
oseRegistry::call('registry');
oseRegistry :: register('ipmanager', 'ipmanager');
oseRegistry :: register('antihacker', 'antihacker');
oseRegistry :: register('sysguard','sysguard');

$controller= JRequest :: getVar('controller');
$path= JPATH_COMPONENT.DS.'controllers'.DS.$controller.'.php';
if(file_exists($path)) {
	require_once($path);
} else {
	$controller= '';
}
$classname= 'ose_antihackerController'.$controller;
$controller= new $classname();
// Perform referer check
//$controller->refererCheck();
// Perform the Request task
$controller->execute(JRequest :: getVar('task'));
// Redirect if set by the controller
$controller->redirect();
}