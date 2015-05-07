<?php
/**
 * @package     LOGman
 * @copyright   Copyright (C) 2012 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class ComLogmanControllerBehaviorCommandable extends ComDefaultControllerBehaviorCommandable
{
    protected function _afterBrowse(KCommandContext $context)
    {
        if($this->_toolbar)
        {
        	if ($this->canDelete()) {
                $this->getToolbar()->addDelete();
                $this->getToolbar()->addPurge();
            }

            if ($this->canEdit())
            {
            	$enabled = $this->getMixer()->checkPlugin();
		        $command = $enabled ? 'disable' : 'enable';
		    	$this->getToolbar()->addCommand($command, array(
		            'label' => JText::_('TOOLBAR_'.strtoupper($command)),
		            'attribs' => array(
		                'data-novalidate' => 'novalidate',
		                'data-action' => 'editPlugin'
		            )
		        ));
            }

        	if (version_compare(JVERSION, '1.6', '<') || JFactory::getUser()->authorise('core.admin', 'com_logman'))
        	{
        	    $this->getToolbar()->addOptions();
            }
        }
    }
}