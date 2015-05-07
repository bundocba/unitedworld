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
Ext.onReady(function(){
    var top = new Ext.FormPanel({
        ref: 'form',
        labelAlign: 'top',
        frame:false,
        bodyStyle:'padding:0px 0px 0',
        autoScroll: true,
        width: 600,
        labelWidth: 200,
        items: [
				{
				    xtype:'displayfield',
				    value: Joomla.JText._('Anti-Hacking_Scanning_Options'),
				    hideLabel: true,
				    labelWidth: 500,
				},
				{
					hiddenName: 'blockIP'
					,fieldLabel: Joomla.JText._('Frontend_Blocking_Mode')
					,xtype:'combo'
					,typeAhead: true
					,triggerAction: 'all'
					,lazyRender:true
					,mode: 'local'
					,width: 400
					,ListWidth: 500
					,store: new Ext.data.ArrayStore({
					     id: 0
					     ,fields: [
					         'myId'
					         ,'displayText'
					      ]
					 ,data: [
							  	['1', Joomla.JText._('Ban_IP_and_show_ban_page_to_stop_an_attack')]
					        	,['0', Joomla.JText._('Show_a_403_error_page_and_stop_the_attack')]
					        	,['2',Joomla.JText._('Silently_filter_hacking_values_Recommended_for_new_users')]
					        ]
					    })
					 ,valueField: 'myId'
					 ,displayField: 'displayText'
					 ,listeners:{
							render: function(c){
								if (c.value=='')
								{
									c.setValue('2');
								}
							 }
					}
				},
				{
					fieldLabel: Joomla.JText._('Attack_blocking_threshold_value_slider'),
					xtype: 'slider',
				    width: 300,
				    minValue: 0,
				    maxValue: 100,
				    name: 'threshold',
				    id: 'threshold',
				    hiddenName: 'threshold',
				    isFormField: true,
				    plugins: new Ext.slider.Tip()
				},
				{
			           xtype:'textfield',
			           labelWidth: 200,
			           labelAlign: 'left',
			           fieldLabel: Joomla.JText._('SILENT_MODE_BLOCK_MAX_ATTEMPTS'),
			           name: 'slient_max_att',
			           id: 'slient_max_att',
			           anchor:'38%'
			   	},
		   		{
			        xtype:'displayfield',
			        value: Joomla.JText._('File_Upload_Scanning_Options'),
			        hideLabel: true,
			        labelWidth: 500,
			   	},
				{
		           xtype:'textfield',
		           labelWidth: 500,
		           labelAlign: 'left',
		           fieldLabel: Joomla.JText._('Allowed_Upload_File_Extensions_FILEINFO_needs_to_be_installed'),
		           name: 'allowExts',
		           id: 'allowExts',
		           anchor:'98%'
		   		},
		   		{
					hiddenName: 'scanFileVirus'
					,fieldLabel: Joomla.JText._('Scan_uploaded_files_with_OSE_Anti_Virus')
					,xtype:'combo'
					,typeAhead: true
					,triggerAction: 'all'
					,lazyRender:true
					,mode: 'local'
					,width: 400
					,ListWidth: 500
					,store: new Ext.data.ArrayStore({
					     id: 0
					     ,fields: [
					         'myId'
					         ,'displayText'
					      ]
					 ,data: [
							  	['1', Joomla.JText._('Enabled')]
					        	,['0', Joomla.JText._('Disabled')]
					        ]
					    })
					 ,valueField: 'myId'
					 ,displayField: 'displayText'
					 ,listeners:{
							render: function(c){
								if (c.getValue()=='')
								{
									c.setValue(0);
								}
							 }
					}
				}
		    
		],

        buttons: [{
            text: 'Save',
            handler: function (){
            	top.getForm().submit({
							url : 'index.php' ,
							params : {
								option : 'com_ose_antihacker',
								controller:'antihacker',
								task:'saveConfiguration',
								type:'scan',
								threshold: Ext.getCmp('threshold').getValues()
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
											Ext.getCmp('threshold').setValue(action.result.data.threshold);
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
         }],
        reader: new Ext.data.JsonReader({
		    root: 'result',
		    totalProperty: 'total',
		    idProperty: "id",
		    fields:[
				    {name: 'threshold', type: 'string', mapping: 'threshold'},
				    {name: 'blockIP', type: 'int', mapping: 'blockIP'},
				    {name: 'allowExts', type: 'string', mapping: 'allowExts'},
				    {name: 'scanFileVirus', type: 'string', mapping: 'scanFileVirus'},
				    {name: 'slient_max_att', type: 'string', mapping: 'slient_max_att'}
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
					type:'scan'					
					},
					method: 'POST',
					success: function (form, action ) {
						Ext.getCmp('threshold').setValue(action.result.data.threshold);
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
		,height: 450
		,width: '100%'
		,renderTo: 'scanconf'
	});
})
</script>