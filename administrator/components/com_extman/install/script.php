<?php
/**
 * @package     EXTman
 * @copyright   Copyright (C) 2012 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

defined('_JEXEC') or die;

// Need to do this as Joomla 2.5 "protects" parent and manifest properties of the installer
global $installer_manifest, $installer_source, $installer_instance;
$installer_manifest = simplexml_load_file($this->parent->getPath('manifest'));
$installer_source   = $this->parent->getPath('source');
$installer_instance = $this->parent;

require_once dirname(__FILE__).'/helper.php';

class com_extmanInstallerScript
{
	/**
	 * Name of the component
	 */
	public $component;

	public function __construct($installer)
	{
		global $installer_manifest, $installer_source, $installer_instance;
		// Need to do this as Joomla 2.5 "protects" parent and manifest properties of the installer
		$source   = $installer_source;
		$manifest = $installer_manifest;

		$class = get_class($this);
		preg_match('#^com_([a-z0-9_]+)#', get_class($this), $matches);
		$this->component = $matches[1];

		$this->helper = new ComExtmanInstallerHelper();

		$this->helper->installer = $installer_instance;
		$this->helper->manifest = $manifest;
	}
	
	protected function _fixJoomlaInstallBugs()
	{
	    $db = JFactory::getDbo();
	    
	    // Delete leftover entries from #__assets, #__extensions and #__menu
	    $queries = array();
	    $queries[] = "DELETE FROM #__assets WHERE name = '%s'";
	    $queries[] = "DELETE FROM #__extensions WHERE element = '%s'";
	    $queries[] = "DELETE FROM #__menu 
	        WHERE type = 'component' AND menutype = 'main'
	        AND link LIKE 'index.php?option=%s%%'";
	    
	    foreach ($queries as $query) {
	        $db->setQuery(sprintf($query, 'com_'.$this->component))
	           ->query();
	    }
	}
	
	protected function _fixJoomlaUpdateBugs()
	{
	    $db = JFactory::getDbo();
	    $component = 'com_'.$this->component;
	    
	    // Delete excess entries from #__extensions
	    $query = "SELECT extension_id FROM #__extensions WHERE element = '%s' ORDER BY extension_id ASC";
	    $ids = $db->setQuery(sprintf($query, $component))->loadColumn();

	    if (count($ids) > 1) {
	        $query = sprintf("DELETE FROM #__extensions WHERE element = '%s' AND extension_id <> %d", $component, $ids[0]);
	        $db->setQuery($query)->query();
	    }
	    
	    // Delete excess entries from #__assets
	    $query = "SELECT id FROM #__assets WHERE name = '%s' ORDER BY id ASC LIMIT 1";
	    $ids = $db->setQuery(sprintf($query, $component))->loadColumn();
	    
	    if (count($ids) > 1) {
	        $query = sprintf("DELETE FROM #__assets WHERE name = '%s' AND id <> %d", $component, $ids[0]);
	        $db->setQuery($query)->query();
	    }
	    
	    // Delete entries from #__menu to be sure
	    $query = "DELETE FROM #__menu 
	        WHERE type = 'component' AND menutype = 'main'
	        AND link LIKE 'index.php?option=%s%%'";
	    $query = sprintf($query, $component);

	    $db->setQuery($query)->query();
	}

	public function preflight($type, $installer)
	{
	    if (version_compare(JVERSION, '1.6', '>='))
	    {
	        if (in_array($type, array('install', 'discover_install'))) {
	            $this->_fixJoomlaInstallBugs();
	        } else {
	            $this->_fixJoomlaUpdateBugs();
	        }
	    }
		if ($errors = $this->helper->getServerErrors())
		{
			ob_start();
			echo JText::_("The installation can't proceed until you resolve the following: ");
			echo implode(',', $errors);

			$error = ob_get_clean();
			JFactory::getApplication()->enqueueMessage($error, 'error');

			// J1.5 does not remove menu items on unsuccessful installs
			if (version_compare(JVERSION, '1.6', '<'))
			{
				$db = JFactory::getDBO();
				$db->setQuery("DELETE FROM #__components WHERE `option` = 'com_extman'");
				$db->query();
			}

			return false;
		}
	}

	public function postflight($type, $installer)
	{
		$this->helper->installFramework();
		$this->helper->installExtensions();

		// Hide component in the menu manager in Joomla 1.5
		if (version_compare(JVERSION, '1.6', '<'))
		{
			$db = JFactory::getDBO();
			$db->setQuery("UPDATE #__components SET link = '' WHERE link = 'option=com_extman'");
			$db->query();
		}

		if ($this->helper->bootFramework())
		{
			$controller = KService::get('com://admin/extman.controller.extension', array(
				'request' => array('view' => 'extension', 'layout' => 'success')
			));
			$controller->event = $type === 'update' ? 'update' : 'install';

			$extension = $controller->read();
			$extension->name = 'EXTman';
			$extension->version = (string)$this->helper->manifest->version;

			echo $controller->display();
		}
	}

	public function uninstall($installer)
	{
		// Pre-cache uninstall tracking code since we are gonna get rid of Nooku Framework
		$track = '';
		if (class_exists('Koowa'))
		{
			$controller = KService::get('com://admin/extman.controller.extension', array(
				'request' => array('view' => 'extension', 'layout' => 'uninstall')
			));
			$controller->event = 'uninstall';

			$extension = $controller->read();
			$extension->name = 'EXTman';
			$extension->version = (string)$this->helper->manifest->version;

			$track = $controller->display();
		}

		$db = JFactory::getDBO();
		$db->setQuery("SELECT name FROM #__extman_extensions WHERE parent_id = 0 AND identifier <> 'com:extman'");
		$results = $db->loadResultArray();
		if (count($results))
		{
			$extension = count($results)  == 1 ? sprintf('the <strong>%s</strong> extension by Joomlatools installed', $results[0]) : sprintf('%d Joomlatools extensions installed', count($results));
			JFactory::getApplication()->enqueueMessage(sprintf(
				"You have $extension. EXTman is needed for Joomlatools extensions to work properly. These extensions will not work until you re-install EXTman. EXTman database tables are not deleted to make sure your site still works if you install it again.",
				JRoute::_('index.php?option=com_extman')), 'error');
		} 
		else 
		{
			$tables = array('#__extman_extensions', '#__extman_dependencies');
			foreach ($tables as $table)
			{
				$db->setQuery('DROP TABLE IF EXISTS '.$db->replacePrefix($table));
				$db->query();
			}
		}

		$this->helper->uninstallExtensions();
		$this->helper->uninstallFramework();

		echo $track;
	}
}