<?php
/**
 * @package     LOGman
 * @copyright   Copyright (C) 2012 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class ComLogmanDispatcher extends ComDefaultDispatcher
{
	protected function _initialize(KConfig $config)
	{
		$config->append(array(
			'controller' => 'activity',
		));

		parent::_initialize($config);
	}
}