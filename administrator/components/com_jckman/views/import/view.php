<?php
/*------------------------------------------------------------------------
# Copyright (C) 2005-2012 WebxSolution Ltd. All Rights Reserved.
# @license - GPLv2.0
# Author: WebxSolution Ltd
# Websites:  http://www.webxsolution.com
# Terms of Use: An extension that is derived from the JoomlaCK editor will only be allowed under the following conditions: http://joomlackeditor.com/terms-of-use
# ------------------------------------------------------------------------*/ 

defined( '_JEXEC' ) or die();

/**
 * Extension Manager Install View
 *
 * @package		Joomla
 * @subpackage	Installer
 * @since		1.5
 */

include_once(dirname(__FILE__).DS.'..'.DS.'default'.DS.'view.php');

class ImportViewImport extends ImportViewDefault
{
	function display($tpl=null)
	{
		/*
		 * Set toolbar items for the page
		 */
		$bar = & JToolBar::getInstance('toolbar');
		// Add a Link button for Control Panel
		$bar->appendButton( 'Link', 'export', JText::_( 'JTOOLBAR_EXPORT' ),'index.php?option=com_jckman&controller=cpanel&task=export');
		$bar->appendButton( 'Link', 'cpanel', JText::_( 'COM_JCKMAN_SUBMENU_CPANEL_NAME' ), 'index.php?option=com_jckman&controller=cpanel');
		JToolBarHelper::help( 'screen.installer' );
			
		$paths = new stdClass();
		$paths->first = '';
			
	
		$this->assignRef('paths', $paths);
		$this->assignRef('state', $this->get('state'));

		parent::display($tpl);
	}

}