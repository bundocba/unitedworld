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
if (!defined('_JEXEC') && !defined('OSEDS'))
{
	die("Direct Access Not Allowed");
}
class OSEAH_Addons
{
    /**
     * Anti-Flooding
     */
	public function floodingCheck($duration, $visits, $ip, $db)
    {
		$query ="SELECT * FROM `#__oseipm_iptable_tmp` WHERE `ip` = ". $db->Quote($ip). " LIMIT 1";
		$db->setQuery($query);
		$results = $db->loadObject();
		if (count($results)==0)
		{
			$last_session_request = time();
			$total_session_request = 1;
			$query = "INSERT INTO `#__oseipm_iptable_tmp` (
					`id` ,
					`ip` ,
					`last_session_request` ,
					`total_session_request`
					)
					VALUES (
					NULL , ".$db->Quote($ip).", ".$db->Quote($last_session_request).", ".$db->Quote($total_session_request).");";
			$db->setQuery($query);
			$db->query();
			return false;
		}

		// anti flood protection
		if($results->last_session_request > (time() - $duration)){
			if ($results->total_session_request > $visits)
			{
				return true;
			}
			else
			{
				$last_session_request = time();
				$total_session_request = $results->total_session_request +1;
				$query =" UPDATE `#__oseipm_iptable_tmp` SET `last_session_request` = " .$db->Quote($last_session_request).
						", `total_session_request` = " .$db->Quote($total_session_request).
						" WHERE `ip` = ". $db->Quote($ip);
				$db->setQuery($query);
				$db->query();
				return false;
			}
		}
		else
		{
			if ($results->total_session_request > $visits)
			{
				// real flooding, return true;
				$query =" DELETE FROM `#__oseipm_iptable_tmp` WHERE `ip` = ". $db->Quote($ip);
				$db->setQuery($query);
				$db->query();
				return false;
			}
			else
			{
				$last_session_request = time();
				$total_session_request = $results->total_session_request +1;
				$query =" UPDATE `#__oseipm_iptable_tmp` SET `last_session_request` = " .$db->Quote($last_session_request).
						", `total_session_request` = 1" .
						" WHERE `ip` = ". $db->Quote($ip);
				$db->setQuery($query);
				$db->query();
				return false;
			}
		}
    }
    public function roundtripdns($ip,$domain)
	{
	        if (@is_ipv6($ip)) return $ip;

	        $host = gethostbyaddr($ip);
	        $host_result = strpos(strrev($host), strrev($domain));
	        if ($host_result === false || $host_result > 0) return false;
	        $addrs = gethostbynamel($host);
	        if (in_array($ip, $addrs)) return true;
	        return false;
	}

}
?>