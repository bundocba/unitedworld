<?php
/**
 * @package     LOGman
 * @copyright   Copyright (C) 2012 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class ComLogmanControllerActivity extends ComActivitiesControllerActivity
{
	public function __construct(KConfig $config)
	{
		parent::__construct($config);

		$this->registerCallback('before.get', array($this, 'setPluginWarning'));
		$this->registerCallback('before.get', array($this, 'purgeOldActivities'));
	}

	public function setPluginWarning()
	{
		if ($this->isDispatched() && !$this->checkPlugin()) {
			JFactory::getApplication()->enqueueMessage(JText::_('LOGMAN_DISABLED_NOTICE'));
		}
	}
	
	/**
	 * Purging is happening on backend GET access because doing it 
	 * when logging stuff would make the request slower for end users
	 */
	public function purgeOldActivities()
	{
	    $params = JComponentHelper::getParams('com_logman');
	    
	    if ($this->canPurge() && $max_age = (int)$params->get('maximum_age')) {
	        // Get a clone without the current request
	        $controller = $this->getService((string)$this->getIdentifier());
	        
	        $end_date   = $this->getService('koowa:date')->addDays(-1*$max_age)->getDate();
	        $controller->end_date($end_date)->purge();
	    }
	}

	protected function _actionEditPlugin(KCommandContext $context)
	{
		$value = $context->data->enabled;
		$id = $this->getPluginId();

		if (version_compare(JVERSION, '1.6', '>')) {
			$query = 'UPDATE #__extensions SET enabled = %d WHERE extension_id = %d';
		}
		else {
			$query = 'UPDATE #__plugins SET published = %d WHERE id = %d';
		}

		$db = JFactory::getDBO();
		$db->setQuery(sprintf($query, $value, $id));

		return $db->query();
	}

	public function getPluginId()
	{
		return ComExtmanInstaller::getExtensionId(array(
			'type' => 'plugin',
			'element' => 'logman',
			'folder' => 'system',
		));
	}

	public function checkPlugin()
	{
		if (version_compare(JVERSION, '1.6', '>')) {
			$query = 'SELECT enabled FROM #__extensions WHERE extension_id = %d';
		} else {
			$query = 'SELECT published FROM #__plugins WHERE id = %d';
		}

		$db = JFactory::getDBO();
		$db->setQuery(sprintf($query, $this->getPluginId()));

		return !!$db->loadResult();
	}
}