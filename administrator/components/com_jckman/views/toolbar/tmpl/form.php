<?php defined('_JEXEC') or die('Restricted access'); ?>

<?php JHTML::_('behavior.tooltip'); 

define('JCK_COMPONENT_VIEW', JCK_COMPONENT. '/views/toolbar');
 
JHTML::stylesheet('icons.css',JCK_COMPONENT .'/css/');
JHTML::stylesheet('sorttables.css',JCK_COMPONENT_VIEW .'/css/');

jimport('joomla.environment.browser');
$browser = JBrowser::getInstance();
$name = $browser->getBrowser();
$version = $browser->getMajor();

if($name == 'msie' && $version == 7) 
{
	JHTML::stylesheet('sorttables_ie7.css',JCK_COMPONENT_VIEW .'/css/');
}

JFilterOutput::objectHTMLSafe( $this->toolbar, ENT_QUOTES, '' );
	
   
?>


<script language="javascript" type="text/javascript">
	 window.addEvent('domready', function()
	 {
		var sortablelist = new Sortables('.sortableList', {revert: true, clone: true, opacity:0});
														
		var sortablerow = new Sortables('.sortableRow', {revert: true, clone: true, opacity:0,
										onStart : function(element,clone)
										{
											clone.style.zIndex = 999;										
										},
										onComplete : function(element)
										{
											 if(element && element.id  in { icon72:1, icon73:1, icon74:1,icon92:1 } )
											 {
												element.setStyles({
												'position': 'relative',
												'left' : '0px',
												'top': '2px'
												});
											}
										
											if(!Browser.ie)
											 {
												if(element)
												{
													element.setStyle('margin-right','4px');
												} 
											}
											sortablelist.attach();		
										}});
										
		$$('li.sortableItem').getElements('img').each(function(el){
				el.addEvent('mousedown',function(){
					sortablelist.detach();
				})
			});	
			
		$$('li.sortableItem').getElements('img').each(function(el){
			el.addEvent('mouseup',function(){
				sortablelist.attach();
			})
		});								
	 });


	Joomla.submitbutton = function(pressbutton) {
		if (pressbutton == "cancelEdit") {
			submitform(pressbutton);
			return;
		}
		// validation
		var form = document.adminForm, items = [];
		if (form.title.value == "") {
			alert( "<?php echo JText::_( 'COM_JCK_LAYOUT_MANAGER_LAYOUT_TOOLBAR_MUST_HAVE_A_TITLE', true ); ?>" );
		} 
		else if (form.name.value == "") {
			alert( "<?php echo JText::_( 'COM_JCK_LAYOUT_MANAGER_LAYOUT_TOOLBAR_MUST_HAVE_A_NAME', true ); ?>" );
		}else {
			// Serialize group layout
			  if(document.id('groupLayout'))
			  {
				  document.id('groupLayout').getElements('ul.sortableRow').each(function(el){
					items.include(el.getChildren().map(function(o, i){
					  if(o.hasClass('spacer')){
						return ';';
					  }
					  return o.id.replace(/[^A-Za-z0-9]/gi, '');
					}).join(','));  
				  });
			  }
			  form.rows.value = items.join(',/,') || '';
			submitform(pressbutton);
		}
	
	}
	
	
</script>
<?php
  $edit_name_Disabled = $this->toolbar->iscore ? ' readonly="readonly"' :'';
?>
<form action="index.php" method="post" name="adminForm">
<?php if($this->total > 0) : ?>
<div style="float:left;">
<?php endif; ?>

<fieldset style="width:700px;background-color:#fff;display:block;">
	<legend><?php echo JText::_( 'JDETAILS' ); ?></legend>
	<table class="admintable">
		<tr>
			<td width="100" class="key">
				<label for="name">
					<?php echo JText::_( 'COM_JCK_LAYOUT_MANAGER_DESCRIPTION' ); ?>:
				</label>
			</td>
			<td>
				<input class="text_area" type="text" name="title" id="title" size="35" value="<?php echo $this->toolbar->title; ?>" <?php echo $edit_name_Disabled ?>/>
			</td>
		</tr>
		<td valign="top" class="key">
				<label for="file">
					<?php echo JText::_( 'COM_JCK_LAYOUT_MANAGER_TOOLBAR_NAME' ); ?>:
				</label>
			</td>
			<td>
				<input class="text_area" type="text" name="name" id="name" size="35" value="<?php echo $this->toolbar->name; ?>" <?php echo $edit_name_Disabled ?>/>
			</td>
		</tr>
   </table>
</fieldset>

<?php if($this->total > 0) : ?>
  <table>
             <tr>
                <td>
                <fieldset style="display:block">
                	<legend><?php echo JText::_( 'COM_JCK_LAYOUT_MANAGER_LAYOUT_MANAGEMENT' ); ?></legend>
					
					<div class="cke_top" >&nbsp;</div>
					
					
                <div class="sortableList" id="groupLayout" style="position:relative">
               <?php
       
	   			$totalRows = count($this->toolbarplugins);
				
		
				
				
                for( $i=0; $i< $totalRows+1; $i++){?>
                    <div class="sortableListDiv">
                        <span class="sortableListSpan">
                        <ul class="sortableRow">
                <?php
                    				
					
					if($i < $totalRows) {
					
						$toolbarplugins =  $this->toolbarplugins[$i];
						$many = count($toolbarplugins);
																					
						for( $x=0; $x< $many; $x++ ){
						
							$icon = $toolbarplugins[$x];
				
							if( $icon) {
							
								if($icon->title == "spacer" && !$icon->id){?>  
									<li class="sortableItem spacer" id="icon"><img src="<?php echo JCK_COMPONENT;?>/icons/spacer.png" alt="<?php echo JText::_('Spacer');?>" title="<?php echo JText::_('Spacer');?>" /></li>
						<?php	}
								elseif($icon->title != "spacer" && $icon->id)
								{
								
									$extraAttr = "";
									
									if($icon->icon != "")
									{ 
										if(is_numeric($icon->icon))
										{
											$path = JCK_COMPONENT_VIEW .'/images/spacer.gif';
											$extraAttr = ' class="cke_icon"  style="background-position:0px '.$icon->icon.'px;"'; 
										}
										else
										{
											$path = '../plugins/editors/jckeditor/plugins/'.$icon->name.'/'.$icon->icon;
											if(!file_exists($path))
												continue;
										}
									}
									else
									{
										$path = JCK_COMPONENT .'/icons/'.$icon->name.'.png';
									}	
									
									
								 ?>
								  <li class="sortableItem" id="icon<?php echo $icon->id ;?>"><img src="<?php echo $path;?>" alt="<?php echo $icon->title;?>" title="<?php echo $icon->title;?>" <?php echo $extraAttr; ?>/></li>
								<?php
								  }
							}
						}
					}
                   ?>
                        </ul>
                        </span>
                    </div>
         <?php }?>
                </div>
				
				
				<div class="cke_bot" >&nbsp;</div>
				
				
                </fieldset>
                <fieldset style="display:block">
                <legend><?php echo JText::_( 'COM_JCK_LAYOUT_MANAGER_LAYOUT_TOOLBAR_AVAILABLE_BUTTONS' ); ?></legend>
				
				<div class="cke_top" >&nbsp;</div>
				
				
                <div class="sortableList" style="position:relative">
                <?php 

				$max = JCKHelper::getNextAvailablePluginRowId() + 1;
				
                for( $i=1; $i<=$max; $i++ ){
                ?>
                    <div class="sortableListDiv">
                        <span class="sortableListSpan">
                        <ul class="sortableRow">
                   <?php 
						if( $i == $max){
							for( $x = 1; $x<=20; $x++ ){?>
								<li class="sortableItem spacer" id="icon0"><img src="<?php echo JCK_COMPONENT;?>/icons/spacer.png" alt="<?php echo JText::_('Spacer');?>" title="<?php echo JText::_('Spacer');?>" /></li>
                  <?php 	}
                		}
				    if(!empty( $this->plugins)) {
					
						foreach( $this->plugins as $icon ){
												
							$extraAttr = "";
							
							if( $icon->row == $i ){
								if($icon->icon != "")
								{ 
									if(is_numeric($icon->icon))
									{
										$path = JCK_COMPONENT_VIEW .'/images/spacer.gif';
										$extraAttr = ' class="cke_icon"  style="background-position:0px '.$icon->icon.'px;"'; 
									}
									else
									{
										$path = '../plugins/editors/jckeditor/plugins/'.$icon->name.'/'.$icon->icon;
									}
								}
								else
								{
									$path = JCK_COMPONENT .'/icons/'.$icon->name.'.png';
								}	
									
				   ?>
								<li class="sortableItem" id="icon<?php echo $icon->id ;?>"><img src="<?php echo $path;?>" alt="<?php echo $icon->title;?>" title="<?php echo $icon->title;?>" <?php echo $extraAttr; ?> /></li>
					<?php }
							
						}
					}
                    ?>
                        </ul>
                        </span>
                    </div>
            <?php }?>
                </div>     
				
		<div class="cke_bot" >&nbsp;</div>
				       
                </fieldset>
                </td>
            </tr>
        </table>
</div>       

	<div class="col width-40">
		<fieldset class="adminform" style="<?php echo ($this->toolbar->default ? 'visibility:hidden;' : '');?>">
		<legend><?php echo JText::_( 'COM_JCK_LAYOUT_MANAGER_COMPONENT_ASSIGNMENT' ); ?></legend>
			<table class="adminform" style="border:0px;">
			<tr>
				<td valign="top" class="key">
					<?php echo JText::_( 'Toolbar Assignment' ); ?>:
				</td>
				<td>
					<?php echo $this->lists['components']; ?>
				</td>
			</tr>
			</table>
		</fieldset>
	</div>
<?php endif; ?>
<div class="clr"></div>
	<input type="hidden" name="option" value="com_jckman" />
	<input type="hidden" name="id" value="<?php echo $this->toolbar->id; ?>" />
	<input type="hidden" name="cid[]" value="<?php echo $this->toolbar->id; ?>" />
 	<input type="hidden" name="controller" value="toolbars" />
	<input type="hidden" name="rows" value="" />
	<input type="hidden" name="task" value="" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>