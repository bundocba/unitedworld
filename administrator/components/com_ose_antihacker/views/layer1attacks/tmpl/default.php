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
include(JPATH_ADMINISTRATOR.DS.'components/com_ose_antihacker/views/layer1attacks/tmpl/js.php');
?>
<div id = "oseheader">
<div class="container">
<div class="logo-labels">
<h1><a href="http://www.opensource-excellence.com" target= "_blank"><?php echo JText::_("Open Source Excellence"); ?></a></h1>
		<?php
			echo $this->preview_menus; 
		?>
	</div>
	
<?php
	oseSoftHelper::showmenu();
?>
	<div class="section">
		<div id="sectionheader"><?php echo $this->title; ?></div>
		<div class="grid-title"><?php echo JText::_('PLEASE_CHOOSE_WHEHTER_TO_WHITELIST_OR_BLACKLIST_THE_ATTACKS_THE_RULES_WORK_IN_THE_FOLLOWING_WAY'); ?></div>
			<?php echo '<div class = "ose-notice">'.JText::_('LAYER1NOTICE').'</div>'; ?>
				<div id ='oseantihackerReport'></div>
	</div>
  </div>
</div>
<?php
		echo oseSoftHelper::renderOSETM();
?>				