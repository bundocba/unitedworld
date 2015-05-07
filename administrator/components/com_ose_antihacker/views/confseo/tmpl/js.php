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
<script type="text/javascript" >
Ext.ns('oseATH','oseATHReport');
function genCombo(name, fieldlabel)
{
	var combo = new Ext.form.ComboBox({
		hiddenName: name,
		fieldLabel: Joomla.JText._(fieldlabel),
	    typeAhead: true,
	    triggerAction: 'all',
	    labelStyle: 'min-width: 225px;',
	    lazyRender:true,
	    mode: 'local',
	    store: new Ext.data.ArrayStore({
	        id: 0,
	        fields: [
	            'myId',
	            'displayText'
	        ],
	        data: [[1, Joomla.JText._('Enable')], [0, Joomla.JText._('Disable')]]
	    }),
	    valueField: 'myId',
	    displayField: 'displayText',
	    listeners:{
			render: function(combo){
				if (combo.getValue()=='')
				{
					combo.setValue(0);
				}
			 }
	}
		    
	});
	
	return combo; 
}

var scanGoogleBots = genCombo('scanGoogleBots', 'SCAN_GOOGLE_BOTS' ); 
var scanMsnBots = genCombo('scanMsnBots', 'SCAN_MSN_BOTS' );

Ext.onReady(function(){
    var top = new Ext.FormPanel({
        ref: 'form',
        labelAlign: 'top',
        frame:false,
        bodyStyle:'padding:0px 0px 0',
        autoScroll: true,
        width: 600,
        items: [
			{   xtype:'textfield',
                fieldLabel: Joomla.JText._('SEO_PAGE_TITLE'),
                name: 'pageTitle',
                id: 'pageTitle',
                anchor:'70%'
            }
            ,{
                xtype:'textfield',
                fieldLabel: Joomla.JText._('SEO_META_KEY'),
                name: 'metaKeywords',
                id: 'metaKeywords',
                anchor:'70%'
            },{
                xtype:'textfield',
                fieldLabel: Joomla.JText._('SEO_META_DESC'),
                name: 'metaDescription',
                id: 'metaDescription',
                anchor:'70%'
            },{
                xtype:'textfield',
                fieldLabel: Joomla.JText._('SEO_META_GENERATOR'),
                name: 'metaGenerator',
                id: 'metaGenerator',
                anchor:'70%'
            },{
                xtype:'textfield',
                fieldLabel:  Joomla.JText._('WEBMASTER_EMAIL'),
                name: 'adminEmail',
                id: 'adminEmail',
                vtype:'email',
                allowBlank:false,
                anchor:'70%'
            },{
	            fieldLabel: Joomla.JText._('CUSTOM_BAN_PAGE'),
	            name:'customBanpage',
	            itemId: 'customBanpage',
	            id:'customBanPage',
	            xtype:'tinymce',
		        width: 720,
		        height:300,
	            tinymceSettings: {
			        theme: "advanced",
			        skin: 'default',
			        plugins: "pagebreak,style,layer,table,advhr,advimage,advlink,emotions,iespell,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,noneditable,visualchars,nonbreaking,xhtmlxtras,template",
			        theme_advanced_buttons1: "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,styleselect,formatselect,fontselect,fontsizeselect",
			        theme_advanced_buttons2: "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,|,insertdate,inserttime,preview,|,forecolor,backcolor",
			        theme_advanced_buttons3: "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,print,|,ltr,rtl,|",
			        theme_advanced_buttons4: "",
			        theme_advanced_toolbar_location: "top",
			        theme_advanced_toolbar_align: "left",
			        theme_advanced_statusbar_location: "bottom",
			        theme_advanced_resizing: false,
			        extended_valid_elements: "a[name|href|target|title|onclick],img[class|src|border=0|alt|title|hspace|vspace|width|height|align|onmouseover|onmouseout|name|style],hr[class|width|size|noshade],font[face|size|color|style],span[class|align|style]",
			    }
    	  	},
    	  	scanGoogleBots,
    	  	scanMsnBots
		],

        buttons: [{
            text: 'Save',
            handler: function (){
            	var adminEmail = Ext.get('adminEmail').dom.value;
            	if (adminEmail=='')
            	{
            		Ext.Msg.alert('ERROR', Joomla.JText._('PLS_ENTER_ADMIN_EMAIL'));
            		return false;
            	}
				top.getForm().submit({
							url : 'index.php' ,
							params : {
								option : 'com_ose_antihacker',
								controller:'antihacker',
								task:'saveConfiguration',
								type:'seo'
							},
							method: 'POST',
							success: function ( form,action ) {
								msg = action.result;
								if (msg.status!='ERROR')
								{
									Ext.Msg.alert(msg.status, msg.result);
									top.load({
										url : 'index.php' ,
										params : {
										option : 'com_ose_antihacker',
										task:'getConfiguration',
										controller:'antihacker'
										},
										method: 'POST',
										success: function (form, action ) {
											//thresholdSlider.setValue(action.result.data.threshold);
											//Ext.fly('curSecLevel').update(action.result.data.threshold);
										}
									});
								}
								else
								{
									Ext.Msg.alert('Error', msg.result);
									top.render();
								}
							}
				});
            }
        },
        { text: 'Back',
            handler: function (){
                window.location='index.php?option=com_ose_antihacker&view=configuration';
            }    
         }
        ],
        reader: new Ext.data.JsonReader({
		    root: 'result',
		    totalProperty: 'total',
		    idProperty: "id",
		    fields:[
		        {name: 'id', type: 'int', mapping: 'id'},
			    {name: 'pageTitle', type: 'string', mapping: 'pageTitle'},
			    {name: 'metaKeywords', type: 'string', mapping: 'metaKeywords'},
			    {name: 'metaDescription', type: 'string', mapping: 'metaDescription'},
			    {name: 'metaGenerator', type: 'string', mapping: 'metaGenerator'},
			    {name: 'customBanpage', type: 'string', mapping: 'customBanpage'},
			    {name: 'adminEmail', type: 'string', mapping: 'adminEmail'},
			    {name: 'scanGoogleBots', type: 'int', mapping: 'scanGoogleBots'},
			    {name: 'scanMsnBots', type: 'int', mapping: 'scanMsnBots'}
		  	]
	  	}),
	  	listeners: {
			render: function(p){
				p.getForm().load({
					url : 'index.php' ,
					params : {
					option : 'com_ose_antihacker',
					controller:'antihacker',
					task:'getConfiguration',
					tyle:'seo'					
					},
					method: 'POST',
					success: function (form, action ) {
						//thresholdSlider.setValue(action.result.data.threshold);
						//Ext.fly('curSecLevel').update(action.result.data.threshold);
					}
				});
			}
		}
    });
		panel = new Ext.Panel({
		id: 'oseATHConfig-panel'
		,border: false
		,layout: 'fit'
		,items:[
			top
		]
		,height: 650
		,width: '100%'
		,renderTo: 'seoconf'
	});
})
</script>