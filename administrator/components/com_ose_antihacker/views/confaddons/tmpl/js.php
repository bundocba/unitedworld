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
		fieldLabel: fieldlabel,
	    typeAhead: true,
	    triggerAction: 'all',
	    labelStyle: 'min-width: 300px;',
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

var antiflooding = genCombo('antiflooding', Joomla.JText._('ENABLE_ANTI_FLOOD')); 

var banpagetype = new Ext.form.ComboBox({
	hiddenName: 'banpagetype',
	fieldLabel: 'Ban Page Type',
    typeAhead: true,
    triggerAction: 'all',
    labelStyle: 'min-width: 300px;',
    lazyRender:true,
    mode: 'local',
    store: new Ext.data.ArrayStore({
        id: 0,
        fields: [
            'myId',
            'displayText'
        ],
        data: [[1, Joomla.JText._('SHOW_A_503_ERROR')], [0, Joomla.JText._('BAN_THE_IP_DIRECTLY')]]
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

Ext.onReady(function(){
    var top = new Ext.FormPanel({
        ref: 'form',
        labelAlign: 'top',
        frame:false,
        bodyStyle:'padding:0px 0px 0',
        autoScroll: true,
        width: 600,
        items: [
				antiflooding,
				{
	                xtype:'textfield',
	                labelStyle: 'min-width: 300px;',
	                fieldLabel: Joomla.JText._('DETECT_DURATION'),
	                name: 'decduration',
	                id: 'decduration',
	                size: 100,
	                emptyText: 10
		       	},
				{
	            	xtype:'textfield',
	            	labelStyle: 'min-width: 300px;',
		            fieldLabel: Joomla.JText._('MAX_VISITS_PER_DETECT'),
		            name: 'maxvisits',
		            id: 'maxvisits',
		            size: 100,
		            emptyText: 5
				},
				banpagetype
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
								type:'addons'
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
		            {name: 'antiflooding', type: 'int', mapping: 'antiflooding'},
				    {name: 'decduration', type: 'int', mapping: 'decduration'},
				    {name: 'maxvisits', type: 'int', mapping: 'maxvisits'},
				    {name: 'banpagetype', type: 'int', mapping: 'banpagetype'}
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
					type:'addons'					
					},
					method: 'POST',
					success: function (form, action ) {
						
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
		,height: 220
		,width: '100%'
		,renderTo: 'confaddons'
	});
})
</script>