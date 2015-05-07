<?php
/**
  * @version       1.0 +
  * @package       Open Source Excellence Customer Support Software
  * @subpackage    Open Source Excellence Ticket System - com_ose_tickets
  * @author        Open Source Excellence (R) {@link  http://www.opensource-excellence.com}
  * @author        Created on 01-Nov-2011
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
  *  @Copyright Copyright (C) 2010- Open Source Excellence (R)
*/
defined('_JEXEC') or die("Direct Access Not Allowed");
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
		<div class="grid-title"><?php echo JText::_('PLEASE_EDIT_EMAIL_HERE'); ?></div>
		<div id ='ose-emails-list'>
		<?php echo OSESoftHelper::getToken();  ?>
		</div>
	</div>
  </div>
</div>
	<?php
		echo oseSoftHelper::renderOSETM();
	?>