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
class ose_antihackerViewdashboard extends ose_antihackerView {
	function display($tpl= null) {
		$tmpl = JRequest::getVar('tmpl');
		if (empty($tmpl))
		{
			JRequest::setVar('tmpl', 'component');
		}
		$com= OSECPU_PATH_JS.'/com_ose_cpu/extjs';
		$this -> initScript();

		//$this->assignRef('frontPath', $frontPath);
		$error = '';
		$curHost = $_SERVER['HTTP_HOST'];
		if ($curHost == 'localhost')
		{
			$error = "Version does not show on local servers";
			$this->assignRef('version',$error);
		}
		else
		{
			$url= "www.opensource-excellence.com";
			$req= "/version.php?item=ATHV3SIG";
			$fp= fsockopen($url, 80, $errno, $errstr, 30);
			if(!$fp || $errno)
			{
				$error = "Version unknown. Connection ERROR";
				$this->assignRef('version',$error);
			}
			else
			{
				@ fputs($fp, "GET ".$req." HTTP/1.1\r\n");
				@ fputs($fp, "HOST: ".$url."\r\n");
				@ fputs($fp, "Connection: close\r\n\r\n");
				// read the body data
				$res= '';
				$headerdone= false;
				while(!feof($fp)) {
					$line= fgets($fp, 1024);
					if(strcmp($line, "\r\n") == 0) {
						// read the header
						$headerdone= true;
					} else
						if($headerdone) {
							// header has been read. now read the contents
							$res .= $line;
						}
				}
				fclose($fp);
				preg_match('/<(.*)>/i', $res, $ret);

				if(!empty($ret[0]))
				{
					$this->assignRef('version',$ret[0]);
				}
				else
				{
					$ret[0] = "Version unknown. Connection ERROR";
					$this->assignRef('version',$ret[0]);
				}
			}
		}
		
		$footer= OSESoftHelper :: renderOSETM();
		$this->assignRef('footer', $footer);
		$preview_menus= OSESoftHelper :: getPreviewMenus();
		$this->assignRef('preview_menus', $preview_menus);
		$title = JText :: _('OSE Anti-Hacker™ Dashboard'); 
		$this->assignRef('title', $title);
		parent :: display($tpl);
	}
}