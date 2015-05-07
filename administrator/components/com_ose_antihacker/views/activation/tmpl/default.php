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
//include(JPATH_ADMINISTRATOR.DS.'components/com_ose_antihacker/views/activation/tmpl/js.php');
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
		<div class="grid-title"><?php echo JText::_('FRONTEND_ACTIVATION_CODES_IS_USED_TO_PROTECT_SYSTEM_FRONTEND,_WHILE_BACKEND_ACTIVATION_CODES_ARE_USED_TO_AVOID_FALSE_ALERTS'); ?></div>
		
			<div id="activation-codes">
			<?php
			if (!empty($this->phpsetting))
			{
				foreach ($this->phpsetting as $key => $value)
				{
					if (isset($value['htaccess']))
					{
						$filename = '.htaccess';
						$content = $value['htaccess'];
					}
					elseif (isset($value['phpini']))
					{
						$filename = 'php.ini';
						$content = $value['phpini'];
					}
					else
					{
						$filename = '';
						$content = '';
					}
			
					if ($key =='back')
					{
						echo "<span>".JText::_('BACKEND_PHP_HARDENING_CODES')."</span><br/>".JText::_("THE_FOLLOWING_CODES_ARE_USED_FOR_HARDENING_YOUR_PHP_ENVIRONMENT_IN_THE_ADMINISTRATOR_BACKEND,_PLEASE_ADD_IT_TO_THE_END_OF_THE")." ".$filename." ".JText::_("IN_YOUR_ADMINISTRATOR_FOLDER")."<br />";
						echo "<div class='back'>".$content."</div>";
					}
					else
					{
						echo "<span>".JText::_('FRONTEND_ANTI_HACKER_ACTIVATION_CODES')."</span><br/>".JText::_("THE_FOLLOWING_CODES_ARE_ANTI-HACKER_ACTIVATION_CODES,_PLEASE_ADD_IT_TO_THE_END_OF_THE")." ".$filename." ".JText::_("IN_THE_FOLDER_THAT_YOU_WOULD_LIKE_TO_PROTECT")."<br />";
						echo "<div class='front'>".$content."</div>";
					}
				}
			}
			?>
			<span> <?php echo JText::_("TO_TEST_THE_ACTIVATION"); ?></span>
			<div class='front'>
				<?php
					echo JText::_("AFTER_ADDING_THE_ACTIVATION_CODES,_PLEASE_GO_TO_THE_WEBSITE_YOU_WOULD_TO_PROTECT,_AND_USE_THIS_URL_TO_TEST_IT:_INDEX.PHP?%20UNION").'<br />';
					echo JText::_("YOU_WILL_EITHER_BE_BLOCKED_IF_YOU_TURN_ON_BLOCKING_MODE_OR_THROWN_A_403_ERROR_IF_YOU_TURN_ON_403_ERROR_MODE_OR_REDIRECTED_TO_INDEX.PHP_IF_YOU_TURN_ON_SILENT_MODE");
				?>
			</div>
			</div>


	</div>
  </div>
</div>

<?php
		echo oseSoftHelper::renderOSETM();
?>				