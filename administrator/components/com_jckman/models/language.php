<?php
/**
 * @version		$Id: plugins.php 9872 2008-01-05 11:14:10Z eddieajau $
 * @package		Joomla
 * @subpackage	Menus
 * @copyright	Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant to the
 * GNU General Public License, and as distributed it includes or is derivative
 * of works licensed under the GNU General Public License or other free or open
 * source software licenses. See COPYRIGHT.php for copyright notices and
 * details.
 */
// Import library dependencies
require_once(dirname(__FILE__).DS.'extension.php');

/**
 * Installer Plugins Model
 *
 * @package		Joomla
 * @subpackage	Installer
 * @since		1.5
 */
class InstallerModelLanguage extends InstallerModel
{
	/**
	 * Extension Type
	 * @var	string
	 */
	var $_type = 'language';

	/**
	 * Overridden constructor
	 * @access	protected
	 */
	function __construct()
	{
		$app = JFactory::getApplication();
			
		// Call the parent constructor
		parent::__construct();

		// Set state variables from the request
		$this->setState('filter.string', $app->getUserStateFromRequest( "com_jckman.language.string", 'filter', '', 'string' ));
	}

	function _loadItems()
	{
		// Get a database connector
		$db  = JFactory::getDBO();
		$sql = $db->getQuery( true );
		$sql->select( 'id, tag, filename' )
			->from( '#__jcklanguages' )
			->order( 'id DESC' );

		if($search = $this->state->get('filter.string'))
		{
			$sql->where( 'title LIKE '.$db->Quote( '%'.$db->getEscaped( $search, true ).'%', false ) );
		}

		$rows = $db->setQuery($sql)->loadObjectList();


		// Get the plugin base path
		$baseDir = JPATH_COMPONENT.'/language';

		$numRows = count($rows);

		for ($i = 0; $i < $numRows; $i ++)
		{
			$row = & $rows[$i];

			// Get the plugin xml file
			$xmlfile = $baseDir.'/overrides/'. $row->filename;
			if(!file_exists( $xmlfile))
    			$xmlfile = $baseDir.'/'. $row->tag .'/'. $row->filename;

			if (file_exists($xmlfile)) {
				if ($data = JApplicationHelper::parseXMLInstallFile($xmlfile)) {
					foreach($data as $key => $value)
					{
						if($value)
							$row->$key = $value;
					}
				}
				$xml = simplexml_load_file($xmlfile);
				$row->plugin = ($xml->attributes()->plugin ? (string) $xml->attributes()->plugin : '');
			}
		}
		$this->setState('pagination.total', $numRows);
		if($this->state->get('pagination.limit') > 0) {
			$this->_items = array_slice( $rows, $this->state->get('pagination.offset'), $this->state->get('pagination.limit') );
		} else {
			$this->_items = $rows;
		}
	}
}