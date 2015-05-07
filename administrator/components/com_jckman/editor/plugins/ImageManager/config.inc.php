<?php
/**
 * Image Manager configuration file.
 * @author Wei Zhuo
 * @modified WebxSolution Ltd 13.02.2010
 * @author Paul Moers <mail@saulmade.nl> - watermarking and replace code + several small enhancements <http://fckplugins.saulmade.nl>
 * @version $Id: config.inc.php,v 1.4 2006/12/17 14:53:50 thierrybo Exp $
 * @package ImageManager
 * @license - GPLv2.0
 */


/* 
 File system path to the directory you want to manage the images
 for multiple user systems, set it dynamically.

 NOTE: This directory requires write access by PHP. That is, 
       PHP must be able to create files in this directory.
	   Able to create directories is nice, but not necessary.
*/

// AW added

 //Cause browser to reload page every time
header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past



//load up Joomla Framework   AW

error_reporting( E_ERROR  );


$IMConfig = array();

define('DS',DIRECTORY_SEPARATOR);

//get root folder
$dir = explode(DS,dirname(__FILE__));

array_splice($dir,-5);

$base_folder = implode(DS,$dir);

$ip_recorded_for_jusr = '';
$ip = md5($_SERVER['REMOTE_ADDR']);
$base_path = '';
$user = '';

define( '_JEXEC', 1 );
define('JPATH_BASE',$base_folder);
//Needed for 1.6
define('JDEBUG',false);
	
require_once ( JPATH_BASE .DS.'includes'.DS.'defines.php' );


/* Load in the configuation file */
require_once( JPATH_CONFIGURATION	.DS.'configuration.php' );

define('JPATH_PLATFORM',JPATH_LIBRARIES); //AW change for J!1.7 15/07/11

/*load loader class */
require_once(JPATH_LIBRARIES .DS.'loader.php' );

defined('_JREQUEST_NO_CLEAN',1);

if(file_exists(JPATH_LIBRARIES.'/import.php'))
	require_once JPATH_LIBRARIES.'/import.php';
elseif(file_exists(JPATH_LIBRARIES.'/joomla/import.php'))
	require_once JPATH_LIBRARIES.'/joomla/import.php';	

if(file_exists(JPATH_LIBRARIES.'/import.legacy.php'))
	require_once JPATH_LIBRARIES.'/import.legacy.php';

// Botstrap the CMS libraries.
if(file_exists(JPATH_LIBRARIES.'/cms.php'))
	require_once JPATH_LIBRARIES.'/cms.php';
	
if(!class_exists('JVersion')) {
	if(file_exists(JPATH_ROOT.'/includes/version.php'))
		require JPATH_ROOT.'/includes/version.php';
}

jimport('joomla.filter.filterinput');
jimport('joomla.language.language');
jimport('joomla.environment.uri');
jimport('joomla.environment.request');
jimport('joomla.environment.response');
jimport('joomla.user.user');
jimport('joomla.event.dispatcher');
jimport('joomla.application.component.model');
jimport('joomla.html.parameter');
//This is required for the User Params
jimport( 'joomla.utilities.arrayhelper' );

$JVersion = new JVersion();

//Load in the right version
if( !version_compare( $JVersion->getShortVersion(), '1.6', 'gt' ) )
{
	define('CKEDITOR_INCLUDES_DIR',JPATH_PLUGINS.DS.'editors'.DS.'jckeditor'.DS.'includes');
	$defaultImgLocation = 'images'.DS.'stories';
	$sql =  "SELECT params FROM #__plugins WHERE element = 'jckeditor' AND folder ='editors'" ;

} else
{
	define('CKEDITOR_INCLUDES_DIR',JPATH_PLUGINS.DS.'editors'.DS.'jckeditor'.DS.'jckeditor'.DS.'includes');
	jimport('joomla.environment.response');
	$defaultImgLocation = 'images';
	$sql =  "SELECT params FROM #__extensions WHERE element = 'jckeditor' AND folder ='editors'" ;

}//end if


/* load JCK loader class*/
require_once (CKEDITOR_INCLUDES_DIR . DS . 'loader.php');

/* load JCK loader class*/
require_once (CKEDITOR_INCLUDES_DIR . '/loader.php');

$config = new JConfig();

// Get the global configuration object
$registry =& JFactory::getConfig();

// Load the configuration values into the registry
$registry->loadObject($config);


if(isset($_GET['client']))
{
	$clientId =  JRequest::getInt('client',0,'get');
	setcookie('client',$clientId);
}

include('session.php'); //use our own version of the JCK session class

//lets set session
jckimport('ckeditor.user.user');
$session =& JCKUser::getSession();

//get image directory

// Get base URL

$url = preg_replace('@/plugins/.*/imagemanager/@i','/',JURI::root());

//get DB intstance
	
$database =  &JFactory::getDBO();   

$base_path = JPATH_BASE;


/* Need to access the database to get the image path */
//The SQL Query for this is on line 87 or 94 depending on the Joomla Version being used.
$database->setQuery( $sql );
$result = $database->loadResult();
$editorParams = new JRegistry( $result );

//import CK plugins
jckimport('ckeditor.plugins.helper');
//import system plugins
JCKPluginsHelper::storePlugins('default');
$dispatcher = JDispatcher::getInstance();

//trigger system plugins
JCKPluginsHelper::importPlugin('default');
$dispatcher->trigger('intialize',array( &$editorParams));

$imagePath = $editorParams->get( 'imagePath', $defaultImgLocation );

$IMConfig['root_dir'] =  $base_path   .DS . $imagePath;
$IMConfig['base_dir'] =  $IMConfig['root_dir'];


jimport('joomla.filesystem.folder'); 
$path = $IMConfig['base_dir'];

if (!JFolder::exists($path)) 
{
	if(!JFolder::create($path))
	{
    	echo "<script>alert('Not able to create upload directory due to server file permissions')</script>";
		exit; 
	}
}

$IMConfig['base_url'] = $url . $imagePath;


$IMConfig['base_root'] = $url . $imagePath;


$IMConfig['server_name'] = $_SERVER['SERVER_NAME'];


//get plugin config values


$params = new JRegistry('_default');  

$sql = "show tables like '".$database->getPrefix()."jckplugins'";
$database->setQuery( $sql );
$result = $database->loadResult();


if($result)
{
	$sql = 'SELECT params FROM #__jckplugins  WHERE name = "ImageManager"';
	$database->setQuery( $sql );
	$dbparam = $database->loadResult();
	if($dbparam)
    	$params = new JRegistry($dbparam);
}

$params->def('demo',false);
$params->def('safe_mdode',false);
$params->def('thumbnail_dir','.thumbs');
$params->def('allow_new_dir',true);
$params->def('allow_upload',true);
$params->def('allow_edit',true);
$params->def('allow_replace',true);
$params->def('allow_delete',false);
$params->def('allow_newFileName',false);
$params->def('allow_overwrite',false);

/*
 demo - when true, no saving is allowed
*/
$IMConfig['demo'] = $params->get('demo');

/*

  Possible values: true, false

  TRUE - If PHP on the web server is in safe mode, set this to true.
         SAFE MODE restrictions: directory creation will not be possible,
		 only the GD library can be used, other libraries require
		 Safe Mode to be off.

  FALSE - Set to false if PHP on the web server is not in safe mode.
*/
$IMConfig['safe_mode'] = $params->get('safe_mode');

/* 
 Possible values: 'GD', 'IM', or 'NetPBM'

 The image manipulation library to use, either GD or ImageMagick or NetPBM.
 If you have safe mode ON, or don't have the binaries to other packages, 
 your choice is 'GD' only. Other packages require Safe Mode to be off.
*/
define('IMAGE_CLASS', 'GD');


/*
 After defining which library to use, if it is NetPBM or IM, you need to
 specify where the binary for the selected library are. And of course
 your server and PHP must be able to execute them (i.e. safe mode is OFF).
 GD does not require the following definition.
*/
define('IMAGE_TRANSFORM_LIB_PATH', '/usr/bin/');


/* ==============  OPTIONAL SETTINGS ============== */


/*
  The prefix for thumbnail files, something like .thumb will do. The
  thumbnails files will be named as "prefix_imagefile.ext", that is,
  prefix + orginal filename.
*/
$IMConfig['thumbnail_prefix'] = '.';

/*
  Thumbnail can also be stored in a directory, this directory
  will be created by PHP. If PHP is in safe mode, this parameter
  is ignored, you can not create directories. 

  If you do not want to store thumbnails in a directory, set this
  to false or empty string '';
*/
$IMConfig['thumbnail_dir'] =  $params->get('thumbnail_dir');

/*
  Possible values: true, false

 TRUE -  Allow the user to create new sub-directories in the
         $IMConfig['base_dir'].

 FALSE - No directory creation.

 NOTE: If $IMConfig['safe_mode'] = true, this parameter
       is ignored, you can not create directories
*/
$IMConfig['allow_new_dir'] = $params->get('allow_new_dir');

/*
  Possible values: true, false

  TRUE - Allow the user to upload files.

  FALSE - No uploading allowed.
*/
$IMConfig['allow_upload'] = $params->get('allow_upload');

/*
  Possible values: true, false

  TRUE - Allow the user to edit images.

  FALSE - No editing allowed.
*/
$IMConfig['allow_edit'] = $params->get('allow_edit');

/*
  Possible values: true, false

  TRUE - Allow the replacement of the image with a newly uploaded image in the editor dialog.

  FALSE - No replacing allowed.
*/
$IMConfig['allow_replace'] =$params->get('allow_replace');

/*
  Possible values: true, false

  TRUE - Allow the deletion of images

  FALSE - No deleting allowed
*/
$IMConfig['allow_delete'] = $params->get('allow_delete');

/*
  Possible values: true, false

  TRUE - Allow the user to enter a new filename for saving the edited image.

  FALSE - Overwrite
*/
$IMConfig['allow_newFileName'] = $params->get('allow_newFileName');
/*
  Possible values: true, false
  Only applies when the the user can enter a new filename (The baove settig = 'allow_newFileName' true)

  TRUE - Overwrite file of entered filename, if file already exist.

  FALSE - Save to variant of entered filename, if file already exist.
*/
$IMConfig['allow_overwrite'] = $params->get('allow_overwrite');

/*
  Specify the paths of the watermarks to use (relative to $IMConfig['base_dir']).
  Specifying none will hide watermarking functionality.
 */
 
$WMbaseFolder =  '/watermark/';

$path = $IMConfig['root_dir'] .$WMbaseFolder;
$IMConfig['watermarks'] = array();	
$exclude = array("Thumbs.db");
//check to see if folder exisrs

if(!is_dir($path))
{
  //create folder
  $origmask =  @umask(0);
   if(!@mkdir($path,0755))
   {
   	 if(!@mkdir($path,0777))
	 	@umask($origmask);
	 	echo '<script>alert("Could not create watermark folder");</script>';
		exit;
   }
   //copy files
   
   $spath = dirname(__FILE__)  . DS . 'img' .DS . 'watermark';  
   
   $handle = opendir($spath);
	while (($file = readdir($handle)) !== false)
	{
		if (($file != '.') && ($file != '..') && (!in_array($file,$exclude))) {
			if(!is_dir($spath . DS . $file))
			{
			
				if(!@copy($spath .DS . $file,$path .DS . $file))
				{
					echo '<script>alert("Could not copy $file to watermark folder");</script>';
					exit;
				}
  				$IMConfig['watermarks'][] = $WMbaseFolder . $file;
			}
					
		}
			
	}
	closedir($handle);
   @umask($origmask);

}
else
{
	//load water file array
	$handle = opendir($path);
	while (($file = readdir($handle)) !== false)
	{
		if (($file != '.') && ($file != '..') && (!in_array($file,$exclude))) {
			if(!is_dir($path . DS . $file))
			{
				$IMConfig['watermarks'][] = $WMbaseFolder . $file;
			}
					
		}
			
	}
	closedir($handle);
}

/*
	To limit the width and height for uploaded files, specify the maximum pixeldimensions.
	Specify more widthxheight sets by copying both lines and increasing the number in the second brackets.
	If only one set is specified, no select list will show and this set will be used by default.
	Setting the single set its values to either zero or empty will allow any size.
*/
//$IMConfig['maxWidth'][0] = 333;
//$IMConfig['maxHeight'][0] = 333;
//$IMConfig['maxWidth'][1] = 100;
//$IMConfig['maxHeight'][1] = 180;
$IMConfig['maxWidth'][0] = 550;  
$IMConfig['maxHeight'][0] = 350;
$IMConfig['maxWidth'][1] = 1000;  
$IMConfig['maxHeight'][1] = 1000;


/*
 Possible values: true, false

 TRUE - If set to true, uploaded files will be validated based on the 
        function getImageSize, if we can get the image dimensions then 
        I guess this should be a valid image. Otherwise the file will be rejected.

 FALSE - All uploaded files will be processed.

 NOTE: If uploading is not allowed, this parameter is ignored.
*/
$IMConfig['validate_images'] = true;

/*
 The default thumbnail if the thumbnails can not be created, either
 due to error or bad image file.
*/
$IMConfig['default_thumbnail'] = 'img/default.gif';

/*
  Thumbnail dimensions.
*/
$IMConfig['thumbnail_width'] = 96;
$IMConfig['thumbnail_height'] = 96;

/*
  Image Editor temporary filename prefix.
*/
$IMConfig['tmp_prefix'] = '.editor_';
?>