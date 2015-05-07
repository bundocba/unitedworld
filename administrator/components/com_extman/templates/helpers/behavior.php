<?php
/**
 * @package     EXTman
 * @copyright   Copyright (C) 2011 Timble CVBA and Contributors. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.eu
 */

class ComExtmanTemplateHelperBehavior extends ComDefaultTemplateHelperBehavior
{
    public function jquery($config = array())
    {
        $config = new KConfig($config);
        $html ='';

        // Only load once
        if (!isset(self::$_loaded['jquery'])) {
            $html .= '<script src="media://com_extman/js/jquery-1.8.0.min.js" />';
            self::$_loaded['jquery'] = true;
        }

        return $html;
    }
    
    public function bootstrap($config = array())
    {
        $config = new KConfig($config);
        $config->append(array(
            'namespace' => null,
            'javascript' => array(),
            'package' => null,
            'type' => null
        ));

        $html = '';

        if (count($config->javascript) && !isset(self::$_loaded['jquery'])) {
            $html .= $this->jquery();
        }
        
        if (empty($config->package)) {
            $config->package = $this->getTemplate()->getIdentifier()->package;
        }
        
        if (empty($config->type) && $config->type !== false) {
            $config->type = $this->getTemplate()->getIdentifier()->application;
        }

        foreach ($config->javascript as $js) {
            if (!isset(self::$_loaded[$config->package.'-bootsrap-'.$js])) {
                $html .= '<script src="media://com_'.$config->package.'/bootstrap/js/bootstrap-'.$js.'.js" />';
                self::$_loaded['bootsrap-'.$js] = true;
            }
        }
        
        $filename = 'bootstrap'.($config->type ? '-'.$config->type : '');
        if (!isset(self::$_loaded[$config->package.'-'.$filename])) {
            $html .= '<style src="media://com_'.$config->package.'/bootstrap/css/'.$filename.'.css" />';
            self::$_loaded[$config->package.'-'.$filename] = true;
        }

        $this->getTemplate()->addFilter('bootstrap');

        if (!empty($config->namespace) || $config->namespace === false) {
            $this->getTemplate()->getFilter('bootstrap')->setNamespace($config->namespace);
        }

        return $html;
    }
}
