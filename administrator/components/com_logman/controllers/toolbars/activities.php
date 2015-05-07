<?php
/**
 * @package     LOGman
 * @copyright   Copyright (C) 2012 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class ComLogmanControllerToolbarActivities extends ComDefaultControllerToolbarDefault
{
    protected function _initialize(KConfig $config)
    {
        $config->append(array(
            'title'  => JText::_('LOGman')
        ));

        parent::_initialize($config);
    }
    
    protected function _commandPurge(KControllerToolbarCommand $command)
    {
        $option = $this->getIdentifier()->package;
        $command->attribs->href = JRoute::_('index.php?option=com_logman&view=activities&layout=purge&tmpl=component', false);
        $command->width = 280;
        $command->height = 200;
        
        return $this->_commandModal($command);
    }
}