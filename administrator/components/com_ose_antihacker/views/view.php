<?php
/**
  * @version     3.0 +
  * @package       Open Source Excellence Security Suite
  * @subpackage    Open Source Excellence CPU
  * @author        Open Source Excellence {@link http://www.opensource-excellence.com}
  * @author        Created on 30-Sep-2010
  * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
  *
  *
  *  This program is free software: you can redistribute it and/or modify
  *  it under the terms of the GNU General Public License as published by
  *  the Free Software Foundation, either version 3 of the License, or
  *  (at your option) any later version.
  *
  *  This program is distributed in the hope that it will be useful,
  *  but WITHOUT ANY WARRANTY; without even the implied warranty of
  *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  *  GNU General Public License for more details.
  *
  *  You should have received a copy of the GNU General Public License
  *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
  *  @Copyright Copyright (C) 2008 - 2010- ... Open Source Excellence
*/
defined('_JEXEC') or die("Direct Access Not Allowed");
jimport('joomla.application.component.view');
class ose_antihackerView extends JViewLegacy
{
	function display($tpl = null)
	{
		self :: initCss();
		$document = JFactory::getDocument();
		$document->addScript(JURI::root().'media/system/js/core.js');
		parent :: display($tpl);
	}
	public function initCss()
	{
		$this->com_name= OSESoftHelper :: getExtensionName();
		oseHTML :: stylesheet('components/com_ose_cpu/extjs/resources/css/ose-all.css');
		oseHTML :: stylesheet('administrator/components/com_ose_cpu/assets/css/old.css');
		oseHTML :: stylesheet('administrator/components/com_ose_cpu/assets/css/osesoft.css');
		oseHTML :: stylesheet('administrator/components/'.$this->com_name.'/assets/css/style.css');
	}
	public function initScript($type= 'extjs')
	{
		switch($type)
		{
			case('jquery') :
				oseHTML :: script('components/com_ose_cpu/jquery/jquery.min.js');
				oseHTML :: script('components/com_ose_cpu/extjs/adapter/jquery/ext-jquery-adapter.js');
				oseHTML :: script('components/com_ose_cpu/extjs/ext-all.js');
				break;
			case('extjs') :
			default :
				oseHTML :: script('components/com_ose_cpu/extjs/adapter/ext/ext-base.js');
				oseHTML :: script('components/com_ose_cpu/extjs/ext-all.js');
				break;
		}
	}
}
?>