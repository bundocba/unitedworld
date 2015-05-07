<?php
/**
 * @package     LOGman
 * @copyright   Copyright (C) 2012 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class ComLogmanControllerBehaviorExecutable extends ComActivitiesControllerBehaviorExecutable
{
	public function canGet()
	{
		$result = false;

		if(version_compare(JVERSION,'1.6.0','ge')) {
			$result = JFactory::getUser()->authorise('core.manage', 'com_logman') === true;
		} else {
			$result = JFactory::getUser()->get('gid') > 22;
		}
		return $result;
	}

	public function canDelete()
	{
		$result = $this->canGet();

		if ($result) {
			if(version_compare(JVERSION,'1.6.0','ge')) {
				$result = JFactory::getUser()->authorise('core.delete', 'com_logman') === true;
			} else {
				$result = JFactory::getUser()->get('gid') > 22;
			}
		}

		return $result;
	}
	
	public function canPurge()
	{
	    return $this->canDelete();
	}
}
