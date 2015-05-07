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

$setting = array();
$setting['activated'] = (strpos(ini_get("auto_prepend_file"), "scan.php")>0)?"Passed":"Failed";
$setting['display_errors'] = ((ini_get("display_errors")=='off')||(ini_get("display_errors")==false))?"Passed":"Failed";
$setting['safe_mode'] = ((ini_get("safe_mode")=='off')||(ini_get("safe_mode")==false))?"Passed":"Failed";
$setting['register_globals'] = ((ini_get("register_globals")=='off')||(ini_get("register_globals")==false))?"Passed":"Failed";
$setting['allow_url_fopen'] = ((ini_get("allow_url_fopen")=='off')||(ini_get("allow_url_fopen")==false))?"Passed":"Failed";

echo "<div class='setting-msg'>Your server has the following PHP setting at the moment: <br />" ;
echo	$setting['activated']." -- Activation"."<br/>".
		$setting['display_errors']." -- Display Errors <br/>".
		$setting['safe_mode']." -- Safe Mode <br/>".
		$setting['allow_url_fopen']." -- Allow Url_fopen <br/>".
		$setting['register_globals']." -- Register Globals <br/>".
		"</div>";
exit;
?>