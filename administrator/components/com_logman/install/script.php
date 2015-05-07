<?php
/**
 * @package     LOGman
 * @copyright   Copyright (C) 2012 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */
defined('_JEXEC') or die;

// Need to do this as Joomla 2.5 "protects" parent and manifest properties of the installer
global $installer_manifest, $installer_source;
$installer_manifest = simplexml_load_file($this->parent->getPath('manifest'));
$installer_source = $this->parent->getPath('source');

class com_logmanInstallerScript
{
	/**
	 * Name of the component
	 */
	public $component;

	public function __construct($installer)
	{
		$class = get_class($this);
		preg_match('#^com_([a-z0-9_]+)#', get_class($this), $matches);
		$this->component = $matches[1];
	}

	public function preflight($type, $installer)
	{
		global $installer_manifest, $installer_source;
		
	    $return = true;
	    
		if (!class_exists('Koowa') || !class_exists('ComExtmanDatabaseRowExtension'))
		{
			if (file_exists(JPATH_ADMINISTRATOR.'/components/com_extman/extman.php') && !JPluginHelper::isEnabled('system', 'koowa'))
			{
				$link = version_compare(JVERSION, '1.6.0', '>=') ? '&view=plugins&filter_folder=system' : '&filter_type=system';
				$error = sprintf(JText::_('This component requires System - EXTman plugin to be installed and enabled. Please go to <a href=%s>Plugin Manager</a>, enable <strong>System - EXTman</strong> and try again'), JRoute::_('index.php?option=com_plugins'.$link));
			}
			else $error = JText::_('This component requires EXTman to be installed on your site. Please download this component from <a href=http://joomlatools.eu target=_blank>joomlatools.eu</a> and install it');

			JError::raiseWarning(null, $error);
			
			$return = false;
		}
		
		// J1.5 does not remove menu items on unsuccessful installs
		if ($return === false && $type !== 'update' && version_compare(JVERSION, '1.6', '<'))
		{
		    $db = JFactory::getDBO();
		    $db->setQuery(sprintf("DELETE FROM #__components WHERE `option` = 'com_%s'", $this->component));
		    $db->query();
		}
		
		return $return;
	}

	public function postflight($type, $installer)
	{
		global $installer_manifest, $installer_source;

		// Need to do this as Joomla 2.5 "protects" parent and manifest properties of the installer
		$source       = $installer_source;
		$manifest     = $installer_manifest;
		$extension_id = ComExtmanInstaller::getExtensionId(array(
			'type' 	  => 'component',
			'element' => 'com_'.$this->component
		));

		$extension = KService::get('com://admin/extman.controller.extension')->add(array(
			'source' 				=> $source,
			'manifest' 				=> $manifest,
			'joomla_extension_id' 	=> $extension_id,
			'event' 				=> $type === 'update' ? 'update' : 'install'
		));

		echo KService::get('com://admin/extman.controller.extension')
			->id($extension->id)
			->event($type === 'update' ? 'update' : 'install')
			->layout('success')
			->display();
	}
}