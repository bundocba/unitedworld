<?php
/**
 * Main Plugin File
 *
 * @package         Tabs
 * @version         3.6.1
 *
 * @author          Peter van Westen <peter@nonumber.nl>
 * @link            http://www.nonumber.nl
 * @copyright       Copyright © 2014 NoNumber All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die;

/**
 * Button Plugin that places Editor Buttons
 */
class plgButtonTabs extends JPlugin
{
	private $_init = false;
	private $_helper = null;

	/**
	 * Display the button
	 *
	 * @return array  A two element array of ( imageName, textToInsert )
	 */
	function onDisplay($name)
	{
		if (!$this->getHelper())
		{
			return;
		}

		return $this->_helper->render($name);
	}

	/*
	 * Below methods are general functions used in most of the NoNumber extensions
	 * The reason these are not placed in the NoNumber Framework files is that they also
	 * need to be used when the NoNumber Framework is not installed
	 */

	/**
	 * Create the helper object
	 *
	 * @return object The plugins helper object
	 */
	private function getHelper()
	{
		// Already initialized, so return
		if ($this->_init)
		{
			return $this->_helper;
		}

		$this->_init = true;

		if (!$this->isFrameworkEnabled())
		{
			return false;
		}

		require_once JPATH_PLUGINS . '/system/nnframework/helpers/protect.php';

		if (NNProtect::isJoomla3())
		{
			return false;
		}

		if (!$this->isSystemPluginInstalled())
		{
			return false;
		}

		// Load plugin parameters
		require_once JPATH_PLUGINS . '/system/nnframework/helpers/parameters.php';
		$parameters = NNParameters::getInstance();
		$params = $parameters->getPluginParams($this->_name);


		require_once JPATH_PLUGINS . '/system/nnframework/helpers/protect.php';

		// allow in frontend?
		if (!$params->enable_frontend && JFactory::getApplication()->isSite())
		{
			return false;
		}

		require_once JPATH_PLUGINS . '/system/nnframework/helpers/helper.php';
		$this->_helper = NNFrameworkHelper::getPluginHelper($this, $params);

		return $this->_helper;
	}

	/**
	 * Check if the NoNumber Framework is enabled
	 *
	 * @return bool
	 */
	private function isFrameworkEnabled()
	{
		// Return false if NoNumber Framework is not installed
		if (!$this->isFrameworkInstalled())
		{
			return false;
		}

		$nnframework = JPluginHelper::getPlugin('system', 'nnframework');

		return isset($nnframework->name);
	}

	/**
	 * Check if the NoNumber Framework is installed
	 *
	 * @return bool
	 */
	private function isFrameworkInstalled()
	{
		jimport('joomla.filesystem.file');

		return JFile::exists(JPATH_PLUGINS . '/system/nnframework/nnframework.php');
	}

	/**
	 * Check if the sytem plugin is installed
	 *
	 * @return bool
	 */
	private function isSystemPluginInstalled()
	{
		jimport('joomla.filesystem.file');

		return JFile::exists(JPATH_PLUGINS . '/system/' . $this->_name . '/' . $this->_name . '.php');
	}
}