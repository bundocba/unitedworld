<?php
/**
 * @package     LOGman
 * @copyright   Copyright (C) 2011 Timble CVBA and Contributors. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.eu
 */

class ComLogmanAliases extends KObject
{
    protected $_loaded = false;

    public function setLoaded($loaded)
    {
        $this->_loaded = $loaded;

        return $this;
    }
    
    public function getLoaded()
    {
        return $this->_loaded;
    }

    public function setAliases()
    {
        if (!$this->_loaded) {
            $maps = array(
                'com://admin/logman.model.activities'            => 'com://admin/activities.model.activities',
                'com://admin/logman.database.table.activities'   => 'com://admin/activities.database.table.activities',
                'com://admin/users.template.helper.listbox'      => 'com://admin/logman.template.helper.listbox',
                'com://admin/users.template.helper.autocomplete' => 'com://admin/logman.template.helper.autocomplete'
            );

            foreach ($maps as $from => $to) {
                KService::setAlias($from, $to);
            }
            
            $this->setLoaded(true);
        }

        return $this;
    }
}
