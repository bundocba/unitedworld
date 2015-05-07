<?php
/**
  * @version     4.0 +
  * @package       Open Source Excellence Security Suite
  * @subpackage    Open Source Excellence File Manager
  * @author        Open Source Excellence {@link http://www.opensource-excellence.com}
  * @author        Created on 23-Apr-2012
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
class ose_antihackerViewAboutOSE extends ose_antihackerView
{
	function display($tpl= null)
	{
		$tmpl = JRequest::getVar('tmpl');
		if (empty($tmpl))
		{
			JRequest::setVar('tmpl', 'component');
		}
		
		$com= OSECPU_PATH_JS.'/com_ose_cpu/extjs';
		$com_name  = OSESoftHelper::getExtensionName();
		$classname = OSESoftHelper::getFolderName($this);
		$this->initScript('jquery');
		oseHTML :: script($com.'/grid/SearchField.js');
		oseHTML :: script($com.'/ose/app.msg.js');
		oseHTML :: script($com.'/grid/limit.js');
		
		$title = JText :: _('About OSE'); 
		$this->assignRef('title', $title);
		$footer = OSESoftHelper::renderOSETM();
		$this->assignRef('footer', $footer);
		$preview_menus= OSESoftHelper :: getPreviewMenus();
		$this->assignRef('preview_menus', $preview_menus);
		parent :: display($tpl);
	}
}
?>