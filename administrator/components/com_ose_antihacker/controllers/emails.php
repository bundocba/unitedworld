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
defined('_JEXEC') or die(';)');
class ose_antihackerControllerEmails extends ose_antihackerController
{
	function __construct()
	{
		parent :: __construct();
	}
	function getEmails()
	{
		parent :: getOSEItem('emails', 'getEmails');
	}
	function getEmail()
	{
		$id= JRequest :: getInt('id', null);
		if(empty($id))
		{
			OSESoftHelper :: returnMessages(false, JText :: _('ID CANNOT BE EMPTY'));
		}
		parent :: getOSEItem('emails', 'getEmail');
	}
	function save()
	{
		parent :: save('emails');
	}
	function remove()
	{
		parent :: remove('emails');
	}
	function getTemplateParams()
	{
		parent :: getOSEItem('emails', 'getTemplateParams');
	}
}
?>