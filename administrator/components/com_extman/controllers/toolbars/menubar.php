<?php
/**
 * @package     EXTman
 * @copyright   Copyright (C) 2012 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class ComExtmanControllerToolbarMenubar extends ComDefaultControllerToolbarMenubar
{
    public function getCommands()
    {
        $name = $this->getController()->getIdentifier()->name;

        $this->addCommand(JText::_('Your Extensions'), array(
        		'href'    => JRoute::_('index.php?option=com_extman&view=extensions'),
        		'active'  => $name === 'extension'
        ));

        $this->addCommand(JText::_('Install More'), array(
        		'href'    => JRoute::_('index.php?option=com_installer'),
        		'active'  => false
        ));

        return parent::getCommands();
    }
}