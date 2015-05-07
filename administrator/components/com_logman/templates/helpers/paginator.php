<?php
/**
 * @package     LOGman
 * @copyright   Copyright (C) 2012 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class ComLogmanTemplateHelperPaginator extends ComDefaultTemplateHelperPaginator
{
	/**
	 * Overridden to remove Display NUM text before the limit box
     *
	 * @param array|KConfig $config
	 */
    public function pagination($config = array())
    {
        $config = new KConfig($config);
        $config->append(array(
            'total'      => 0,
            'display'    => 4,
            'offset'     => 0,
            'limit'      => 0,
            'show_limit' => true,
		    'show_count' => true
        ));

        $this->_initialize($config);

        $html   = '<div class="container">';
        $html  .= '<div class="pagination">';

        if($config->show_limit) {
            $html .= '<div class="limit">'.$this->limit($config->append(array('attribs' => array('class' => 'input-small')))).'</div>';
        }

        $html .=  $this->_pages($this->_items($config));
        if($config->show_count) {
            $html .= '<div class="limit"> '.JText::_('Page').' '.$config->current.' '.JText::_('of').' '.$config->count.'</div>';
        }

        $html .= '</div>';
        $html .= '</div>';

        return $html;
    }
}