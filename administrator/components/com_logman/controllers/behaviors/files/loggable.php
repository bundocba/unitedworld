<?php
/**
 * @package     LOGman
 * @copyright   Copyright (C) 2012 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class ComLogmanControllerBehaviorFileLoggable extends ComActivitiesControllerBehaviorLoggable
{
    /**
     * This method is called with the current context to determine what identifier generates the event.
     *
     * This is useful in cases where the row is from another package or the actual action happens somewhere else.
     *
     * @param KCommandContext $context
     */
    public function getActivityIdentifier(KCommandContext $context)
    {
        $identifier = clone $context->caller->getIdentifier();
        
        if ($context->result->container instanceof ComFilesDatabaseRowContainer) {
            $container = explode('-', $context->result->container->slug);
            $container = $container[0];
            
            if ($container) {
                $identifier->package = $container;
            }    
        }
        
        return $identifier;
    }
}