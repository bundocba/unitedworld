<?php
/**
  * @version       1.0 +
  * @package       Open Source Excellence Marketing Software
  * @subpackage    Open Source Excellence Affiates - com_ose_affiliates
  * @author        Open Source Excellence (R) {@link  http://www.opensource-excellence.com}
  * @author        Created on 01-Oct-2011
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
  *  @Copyright Copyright (C) 2010- Open Source Excellence (R)
*/
// No direct access
defined('_JEXEC') or die;
/**
 * Content component helper.
 *
 * @package		Joomla.Administrator
 * @subpackage	com_content
 * @since		1.6
 */
if (!class_exists('JFolder'))
{
	jimport('joomla.filesystem.folder');
}
if (!class_exists('JFile'))
{
	jimport('joomla.filesystem.file');
}
class OSESoftHelper
{
	public static $extension= '';
	/**
	 * Configure the Linkbar.
	 *
	 * @param	string	$vName	The name of the active view.
	 *
	 * @return	void
	 * @since	1.6
	 */
	public function __construct()
	{
		self::$extension= self::getExtensionName();
		$version= new JVersion();
		$version= substr($version->getShortVersion(), 0, 3);
		if(!defined('JOOMLA16'))
		{
			$value=($version >= '1.6') ? true : false;
			define('JOOMLA16', $value);
		}
	}
	public static function getExtensionName()
	{
		return 'com_ose_antihacker';
	}
	
	static function showmenu()
	{
		$db= JFactory::getDBO();
		$query= "SELECT * FROM `#__menu` WHERE `alias` =  'OSE Anti-Hacker™'";
		$db->setQuery($query);
		$results= $db->loadResult();
		if(empty($results))
		{
			$query= "UPDATE `#__menu` SET `alias` =  'OSE Anti-Hacker™', `path` =  'OSE Anti-Hacker™', `published`=1, `img` = '\"components/com_ose_antihacker/favicon.ico\"'  WHERE `component_id` = ( SELECT extension_id FROM `#__extensions` WHERE `element` ='com_ose_antihacker')  AND `client_id` = 1 ";
			$db->setQuery($query);
			$db->query();
		}
		self::$extension= self::getExtensionName();
		$view= JRequest :: getVar('view');
		echo '<div class="menu-search">';
		echo '<ul>';
		echo '<li ';
		echo($view == 'dashboard') ? 'class="current"' : '';
		echo '><a href="index.php?option='.self::$extension.'&view=dashboard">'.JText :: _('DASHBOARD').'</a></li>';

		echo '<li ';
		echo($view == 'activation') ? 'class="current"' : '';
		echo '><a href="index.php?option='.self::$extension.'&view=activation">'.JText :: _('ACTIVATION').'</a></li>';

		echo '<li ';
		echo($view == 'manageips') ? 'class="current"' : '';
		echo '><a href="index.php?option='.self::$extension.'&view=manageips">'.JText :: _('IP_MANAGEMENT').'</a></li>';

		echo '<li ';
		echo($view == 'duplicated') ? 'class="current"' : '';
		echo '><a href="index.php?option='.self::$extension.'&view=duplicated">'.JText :: _('DUPLICATED_IPS').'</a></li>';

		echo '<li ';
		echo($view == 'layer1attacks') ? 'class="current"' : '';
		echo '><a href="index.php?option='.self::$extension.'&view=layer1attacks">'.JText :: _('LAYER_1_ATTACKS').'</a></li>';

		echo '<li ';
		echo($view == 'layer2attacks') ? 'class="current"' : '';
		echo '><a href="index.php?option='.self::$extension.'&view=layer2attacks">'.JText :: _('LAYER_2_ATTACKS').'</a></li>';

		echo '<li ';
		echo($view == 'configuration') ? 'class="current"' : '';
		echo '><a href="index.php?option='.self::$extension.'&view=configuration">'.JText :: _('CONFIGURATION').'</a></li>';

		echo '<li ';
		echo($view == 'aboutose') ? 'class="current"' : '';
		echo '><a href="index.php?option='.self::$extension.'&view=aboutose">'.JText :: _('ABOUTOSE').'</a></li>';
		
		echo '</ul></div>';
	}
	public static function checkAdminAccess()
	{
		$db = JFactory::getDBO();
		$user = JFactory::getUser();
		$db->setQuery("SELECT id FROM #__usergroups");
		$groups = $db->loadResultArray();

		$admin_groups = array();
		foreach ($groups as $group_id)
		{
			if (JAccess::checkGroup($group_id, 'core.login.admin'))
			{
				$admin_groups[] = $group_id;
			}
			elseif (JAccess::checkGroup($group_id, 'core.admin'))
			{
				$admin_groups[] = $group_id;
			}
		}
		$admin_groups = array_unique($admin_groups);
		$user_groups = JAccess::getGroupsByUser($user->id);
		if (count(array_intersect($user_groups, $admin_groups))>0)
		{
			$access=  true;
		}
		else
		{
			$access=  false;
		}
		return $access;
	}
	function getVersion()
	{
		$folder= JPATH_ADMINISTRATOR.DS.'components'.DS.self::$extension;
		if(JFolder :: exists($folder))
		{
			$xmlFilesInDir= JFolder :: files($folder, '.xml$');
		}
		else
		{
			$folder= JPATH_SITE.DS.'components'.DS.$this->extension;
			if(JFolder :: exists($folder))
			{
				$xmlFilesInDir= JFolder :: files($folder, '.xml$');
			}
			else
			{
				$xmlFilesInDir= null;
			}
		}
		$xml_items= '';
		if(count($xmlFilesInDir))
		{
			foreach($xmlFilesInDir as $xmlfile)
			{
				if($data= JApplicationHelper :: parseXMLInstallFile($folder.DS.$xmlfile))
				{
					foreach($data as $key => $value)
					{
						$xml_items[$key]= $value;
					}
				}
			}
		}
		if(isset($xml_items['version']) && $xml_items['version'] != '')
		{
			return $xml_items['version'];
		}
		else
		{
			return '';
		}
	}
	public static function ajaxResponse($status, $message, $data= null, $url= null)
	{
		$return['title']= $status;
		$return['content']= $message;
		$return['data']= $data;
		$return['url']= $url;
		echo oseJSON :: encode($return);
		exit;
	}
	function returnMessages($status, $messages)
	{
		$result= array();
		if($status == true)
		{
			$result['success']= true;
			$result['status']= 'Done';
			$result['result']= $messages;
		}
		else
		{
			$result['success']= false;
			$result['status']= 'Error';
			$result['result']= $messages;
		}
		$result= oseJSON :: encode($result);
		oseExit($result);
	}
	static function renderOSETM()
	{
		$a = base64_decode('PGRpdiBpZCA9ICJvc2Vmb290ZXIiPjxkaXYgY2xhc3M9ImZvb3Rlci10ZXh0Ij5Qb3dlcmVkIGJ5IDxhIGhyZWY9Imh0dHA6Ly93d3cub3BlbnNvdXJjZS1leGNlbGxlbmNlLmNvbS9zaG9wL29zZS1zZWN1cml0eS1zb2x1dGlvbi5odG1sIiBzdHlsZT0idGV4dC1kZWNvcmF0aW9uOiBub25lOyIgdGFyZ2V0PSJfYmxhbmsiIHRpdGxlPSJPU0UgQW50aS1IYWNrZXIiPk9TRSBBbnRpLUhhY2tlcuKEoiA=');
		$a.= '<small><small>[Version: '.OSEANTIHACKERVER.']</small></small><small><small> [Signature: '.OSEATHV4SIGVER.']</small></small></a></div></div>'; 
		return $a;
	}
	static function getFolderName($class)
	{
		$classname = explode("View", get_class($class));
		$classname = strtolower($classname[1]);
		return $classname;
	}
	public function loadCats($cats = array())
    {
        if(is_array($cats))
        {
            $i = 0;
            $return = array();
            foreach($cats as $JCatNode)
            {
                $return[$i]->title = $JCatNode->title;
                $return[$i]->cat_id = $JCatNode->id;
                if($JCatNode->hasChildren())
                    $return[$i]->children = self::loadCats($JCatNode->getChildren());
                else
                    $return[$i]->children = false;

                $i++;
            }
            return $return;
        }
        return false;
    }
	public function loadCatTree($cats = array(), $return = array() )
    {
        if(is_array($cats))
        {
            $i = 0;
            $curreturn = array();
            foreach($cats as $JCatNode)
            {
               $curreturn[$i]->title = '['.$JCatNode->id.'] '.str_repeat('-', $JCatNode->level-1).' '.$JCatNode->title;
               $curreturn[$i]->cat_id = $JCatNode->id;

                if($JCatNode->hasChildren())
                {
                	$subreturn = self::loadCatTree($JCatNode->getChildren(), $return);
                }
                $i++;
            }
            $return = array_merge($curreturn, $return);
            //$return = array_unique($return);
            return $return;
        }
        return false;
    }
    function loadOrders($table)
    {
		$db= JFactory :: getDBO();
		$query= "SELECT CONCAT (`ordering`, ' - ', `title`) as title, `ordering` FROM `{$table}` ORDER BY `ordering` ASC";
		$db->setQuery($query);
		$results= $db->loadObjectList();
		return (!empty($results))?$results:null;
    }

	public static function checkToken($method = 'post')
	{
		if (JOOMLA30==true)
		{
			$token = JSession::getFormToken() ;
		}	
		else
		{
			$token = JUtility::getToken() ;
		}
		if (!JRequest::getVar($token, '', $method, 'alnum'))
		{
			$session = JFactory::getSession();
			if ($session->isNew()) {
				// Redirect to login screen.
				$app = JFactory::getApplication();
				$return = JRoute::_('index.php');
				self::ajaxResponse ('ERROR', JText::_('JLIB_ENVIRONMENT_SESSION_EXPIRED'));
			} else {
				self::ajaxResponse ('ERROR', JText::_('Token invalid'));
			}
		} else {
			return true;
		}
	}
	public static function getToken()
	{
		$html = '<input type="hidden" value="1" name="'.JUtility::getToken().'">';
		return $html;
	}
	function randStr($length= 32, $chars= 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890') {
		// Length of character list
		$chars_length=(strlen($chars) - 1);
		// Start our string
		$string= $chars {
			rand(0, $chars_length)
			};
		// Generate random string
		for($i= 1; $i < $length; $i= strlen($string)) {
			// Grab a random character from our list
			$r= $chars {
				rand(0, $chars_length)
				};
			// Make sure the same two characters don't appear next to each other
			if($r != $string {
				$i -1 })
			$string .= $r;
		}
		// Return the string
		return $string;
	}
	public function getRealIP() {
		$ip= false;
		if(!empty($_SERVER['HTTP_CLIENT_IP'])) {
			$ip= $_SERVER['HTTP_CLIENT_IP'];
		}
		if(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			$ips= explode(", ", $_SERVER['HTTP_X_FORWARDED_FOR']);
			if($ip) {
				array_unshift($ips, $ip);
				$ip= false;
			}
			for($i= 0; $i < count($ips); $i++) {
				if(!preg_match("/^(10|172\.16|192\.168)\./i", $ips[$i])) {
					if(version_compare(phpversion(), "5.0.0", ">=")) {
						if(ip2long($ips[$i]) != false) {
							$ip= $ips[$i];
							break;
						}
					} else {
						if(ip2long($ips[$i]) != -1) {
							$ip= $ips[$i];
							break;
						}
					}
				}
			}
		}
		return($ip ? $ip : $_SERVER['REMOTE_ADDR']);
	}
	
	public static function getPreviewMenus()
	{
		if (JOOMLA30==true)
		{
			$token = JSession::getFormToken() ;
		}	
		else
		{
			$token = JUtility::getToken() ;
		}
		$logoutLink = JRoute::_('index.php?option=com_login&task=logout&'. $token .'=1');
		$hideLinks	= JRequest::getBool('hidemainmenu');
		$output = '<div id="preview_menus">'; 
		
		if (file_exists(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_ose_antihacker'.DS.'define.php'))
		{
			$output .= '<span class="backtojoomla"><a href="'.JURI::root().'administrator/index.php?option=com_ose_antihacker" >'.JText::_('OSE_ANTI_HACKER').'</a></span>';
		}
		
		if (file_exists(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_ose_antivirus'.DS.'define.php'))
		{
			$output .= '<span class="backtojoomla"><a href="'.JURI::root().'administrator/index.php?option=com_ose_antivirus" >'.JText::_('OSE_ANTI_VIRUS').'</a></span>';
		}
		
		$output .= '<span class="backtojoomla"><a href="'.JURI::root().'administrator/" >'.JText::_('BACK_TO_JOOMLA').'</a></span>';
		// Print the logout link.
		$output .= '<span class="logout">' .($hideLinks ? '' : '<a href="'.$logoutLink.'">').JText::_('JLOGOUT').($hideLinks ? '' : '</a>').'</span>';
		// Output the items.
		$output .= "</div>"; 
		return $output; 
	}
	
	public static function getDBFields($table)
	{
		$db = JFactory::getDBO();
		if (JOOMLA30)
		{
			$fields= $db->getTableColumns($table);
			$fields[$table]=$fields;
		}
		else
		{
			$fields= $db->getTableFields($table);	
		}			
		return $fields;
		
	}
}
?>
