<?php
// no direct access

defined('_JEXEC') or die;

// Include the syndicate functions only once

$doc  =   JFactory::getDocument();
$doc ->addStylesheet(Juri::root().'modules/mod_our_video/assets/css/css.css');

$moduleclass_sfx = htmlspecialchars($params->get('moduleclass_sfx'));
require JModuleHelper::getLayoutPath('mod_our_video', $params->get('layout', 'default'));

