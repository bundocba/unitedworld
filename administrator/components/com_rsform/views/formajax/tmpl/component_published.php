<?php
/**
* @version 1.4.0
* @package RSform!Pro 1.4.0
* @copyright (C) 2007-2011 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

echo str_replace('href="#"', 'href="javascript:void(0);"', JHTML::_('grid.published', $this->field, $this->i, 'tick.png', 'publish_x.png', 'components.'));