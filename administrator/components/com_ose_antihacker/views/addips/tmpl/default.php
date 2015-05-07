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
include(JPATH_ADMINISTRATOR.DS.'components/com_ose_antihacker/views/addips/tmpl/js.php');
?>

<div id="oseheader">
	<div class="container">
		<div class="logo-labels">
			<h1>
				<a href="http://www.opensource-excellence.com" target="_blank"><?php echo JText::_("Open Source Excellence"); ?>
				</a>
			</h1>
				<?php
					echo $this->preview_menus; 
				?>
	</div>
<?php
	oseSoftHelper::showmenu();
?>
	<div class="section">
		<div id="sectionheader"><?php echo $this->title; ?></div>
		<div class="grid-title"><?php echo JText::_('PLEASE_ADD_YOUR_IP__IP_RANGE_AND_CHOOSE_WHETHER_TO_WHITELIST_OR_BLACKLIST_IT'); ?></div>
			<div id ='oseATHAddIps'>
				<div id = 'addIpform'>
					<div class='addIpformitems'>
						<div class='itemlables'><?php echo JText::_('IP_RULE') ?></div>
						<input id ='title' name = 'title' type='input'>
					</div>
				
					<div class='addIpformitems'>
					<div class='itemlables'><?php echo JText::_('TYPE') ?></div>
					<select id ='iptype' name = 'iptype'>
					<option value='ip'><?php echo JText::_('IP');?></option>
					<option value='ips'><?php echo JText::_('IP_RANGE');?></option>
					</select>
					</div>
				
					<div class='addIpformitems'>
					<span class='itemlables'><?php echo JText::_('START_IP') ?></span>
					<input id ='ip_start' name = 'ip_start' type='input'>
					</div>
				
					<div class='addIpformitems'>
					<span class='itemlables'><?php echo JText::_('END_IP'); ?></span>
					<input id ='ip_end' name = 'ip_end' type='input'>
					</div>
				
					<div class='addIpformitems'>
					<span class='itemlables'><?php echo JText::_('STATUS'); ?></span>
					<select id ='status' name = 'status'>
					<option value='1'><?php echo JText::_('BLACKLISTED'); ?></option>
					<option value='2'><?php echo JText::_('MONITORED'); ?></option>
					<option value='3'><?php echo JText::_('WHITELISTED'); ?></option>
					</select>
					</div>
				</div>
				<div class='action_panel'>
				   <div id='loadingindicator'>
				   </div>
				   <div class='action_button'>
				   <button id="addipbutton" class='button'><?php echo JText::_('SUBMIT') ?></button>
				   </div>
				</div>
			</div>
	</div>
  </div>
</div>
<?php
echo oseSoftHelper::renderOSETM();
?>