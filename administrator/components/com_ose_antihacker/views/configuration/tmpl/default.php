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
		<div class="grid-title"><?php echo JText::_('PLEASE_CHOOSE_THE_CONFIG_OPTION'); ?></div>
			<div id = 'configuration'>
				<div class="icon">
				    <a href="index.php?option=com_ose_antihacker&view=confseo">
					    <img alt="<?php echo JText::_('SEO_CONFIGURATION'); ?>" src="components/com_ose_antihacker/assets/images/confseo.png" />
					    <span><?php echo JText::_('SEO_CONFIGURATION'); ?></span>
				    </a>
			    </div>
			
				<div class="icon">
				    <a href="index.php?option=com_ose_antihacker&view=confscan">
					    <img alt="<?php echo JText::_('SCANNING_CONFIGURATION'); ?>" src="components/com_ose_antihacker/assets/images/confscan.png" />
					    <span><?php echo JText::_('SCANNING_CONFIGURATION'); ?></span>
				    </a>
			    </div>
			
				<div class="icon">
				    <a href="index.php?option=com_ose_antihacker&view=confl2var">
					    <img alt="<?php echo JText::_('LAYER_2_VARIABLES_CONVERSTION'); ?>" src="components/com_ose_antihacker/assets/images/confl2vars.png" />
					    <span><?php echo JText::_('LAYER_2_VARIABLES_CONVERSTION'); ?></span>
				    </a>
			    </div>
			
				<div class="icon">
				    <a href="index.php?option=com_ose_antihacker&view=confl2rule1">
					    <img alt="<?php echo JText::_('LAYER_2_FILTER_TWEAKS_1'); ?>" src="components/com_ose_antihacker/assets/images/confl2tweaks.png" />
					    <span><?php echo JText::_('LAYER_2_FILTER_TWEAKS_1'); ?></span>
				    </a>
			    </div>			
			
				<div class="icon">
				    <a href="index.php?option=com_ose_antihacker&view=confl2rule2">
					    <img alt="<?php echo JText::_('LAYER_2_FILTER_TWEAKS_2'); ?>" src="components/com_ose_antihacker/assets/images/confl2tweaks.png" />
					    <span><?php echo JText::_('LAYER_2_FILTER_TWEAKS_2'); ?></span>
				    </a>
			    </div>		
			    			
			    <div class="icon">
				    <a href="index.php?option=com_ose_antihacker&view=confaddons">
					    <img alt="<?php echo JText::_('ADDON_SCANNING_OPTIONS'); ?>" src="components/com_ose_antihacker/assets/images/confaddoncan.png" />
					    <span><?php echo JText::_('ADDON_SCANNING_OPTIONS'); ?></span>
				    </a>
			    </div>	
	
			    <div class="icon">
				    <a href="index.php?option=com_ose_antihacker&view=confadv">
					    <img alt="<?php echo JText::_('ADVANCED_SCANNING_OPTIONS'); ?>" src="components/com_ose_antihacker/assets/images/confadvscan.png" />
					    <span><?php echo JText::_('ADVANCED_SCANNING_OPTIONS'); ?></span>
				    </a>
			    </div>	
	
				<div class="icon">
				    <a href="index.php?option=com_ose_antihacker&view=tools">
					    <img alt="<?php echo JText::_('TOOLS'); ?>" src="components/com_ose_antihacker/assets/images/conftools.png" />
					    <span><?php echo JText::_('TOOLS'); ?></span>
				    </a>
			    </div>	
				
				<div class="icon">
				    <a href="index.php?option=com_ose_antihacker&view=confemail">
					    <img alt="<?php echo JText::_('EMAIL'); ?>" src="components/com_ose_antihacker/assets/images/email.png" />
					    <span><?php echo JText::_('EMAIL'); ?></span>
				    </a>
			    </div>	
			    			    
			</div>
			
			</div>
		</div>
  </div>
</div>
<?php
		echo oseSoftHelper::renderOSETM();
?>