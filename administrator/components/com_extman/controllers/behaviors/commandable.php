<?php
/**
 * @package     EXTman
 * @copyright   Copyright (C) 2012 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class ComExtmanControllerBehaviorCommandable extends ComDefaultControllerBehaviorCommandable
{
    protected function _afterBrowse(KCommandContext $context)
    {
        if($this->_toolbar)
        {
        	if ($this->canDelete()) {
                $this->getToolbar()->addDelete();
            }

        	if (version_compare(JVERSION, '1.6.0', '>=') && JFactory::getUser()->authorise('core.admin', 'com_extman'))
            {
            	$this->getToolbar()->addModal(array(
		    		'icon' => 'icon-32-options',
		    		'label' => 'Options',
                    'href' => 'index.php?option=com_config&amp;view=component&amp;component=com_extman&amp;path=&amp;tmpl=component'
		    	));
            }
        }
    }
}