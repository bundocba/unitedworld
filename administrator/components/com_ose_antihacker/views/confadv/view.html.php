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
class ose_antihackerViewconfadv extends ose_antihackerView {
	function display($tpl= null) {
		$tmpl = JRequest::getVar('tmpl');
		if (empty($tmpl))
		{
			JRequest::setVar('tmpl', 'component');
		}
		$com= OSECPU_PATH_JS.'/com_ose_cpu/extjs';
		$this -> initScript('jquery');
		oseHTML :: script($com.'/grid/SearchField.js');
		oseHTML :: script($com.'/grid/expander.js');
		oseHTML :: script($com.'/ose/app.msg.js');
		oseHTML :: script($com.'/grid/limit.js');
		oseHTML :: stylesheet($com.'/fileupload/field.css');
		oseHTML::script($com.'/grid/GridFilters.js',false);
		oseHTML::script($com.'/grid/filter/Filter.js',false);
		oseHTML::script($com.'/grid/filter/BooleanFilter.js',false);
		oseHTML::script($com.'/grid/filter/DateFilter.js',false);
		oseHTML::script($com.'/grid/filter/ListFilter.js',false);
		oseHTML::script($com.'/grid/filter/NumericFilter.js',false);
		oseHTML::script($com.'/grid/filter/StringFilter.js',false);
		oseHTML::script($com.'/grid/SearchField.js',false);
		oseHTML :: script($com."/htmleditor/tiny_mce/tiny_mce.js");
		oseHTML :: script($com."/htmleditor/TinyMCE.js");

		$footer= OSESoftHelper :: renderOSETM();
		$this->assignRef('footer', $footer);
		$preview_menus= OSESoftHelper :: getPreviewMenus();
		$this->assignRef('preview_menus', $preview_menus);
		
		$title = JText :: _('OSE_ANTIHACKER_CONFADV');
		$this->assignRef('title', $title);
		parent :: display($tpl);
	}
}