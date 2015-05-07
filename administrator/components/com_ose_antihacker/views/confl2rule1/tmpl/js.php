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
	    labelStyle: 'min-width: 580px;',
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

var l2ruleid_1 = genCombo('l2ruleid_1', Joomla.JText._('L2RULE1')); 
var l2ruleid_2 = genCombo('l2ruleid_2', Joomla.JText._('L2RULE2')); 
var l2ruleid_3 = genCombo('l2ruleid_3', Joomla.JText._('L2RULE3')); 
var l2ruleid_4 = genCombo('l2ruleid_4', Joomla.JText._('L2RULE4')); 
var l2ruleid_5 = genCombo('l2ruleid_5', Joomla.JText._('L2RULE5')); 

var l2ruleid_6 = genCombo('l2ruleid_6', Joomla.JText._('L2RULE6')); 
var l2ruleid_7 = genCombo('l2ruleid_7', Joomla.JText._('L2RULE7')); 
var l2ruleid_8 = genCombo('l2ruleid_8', Joomla.JText._('L2RULE8')); 
var l2ruleid_9 = genCombo('l2ruleid_9', Joomla.JText._('L2RULE9')); 
var l2ruleid_10 = genCombo('l2ruleid_10', Joomla.JText._('L2RULE10')); 

var l2ruleid_11 = genCombo('l2ruleid_11', Joomla.JText._('L2RULE11')); 
var l2ruleid_12 = genCombo('l2ruleid_12', Joomla.JText._('L2RULE12')); 
var l2ruleid_13 = genCombo('l2ruleid_13', Joomla.JText._('L2RULE13')); 
var l2ruleid_14 = genCombo('l2ruleid_14', Joomla.JText._('L2RULE14')); 
var l2ruleid_15 = genCombo('l2ruleid_15', Joomla.JText._('L2RULE15')); 

var l2ruleid_16 = genCombo('l2ruleid_16', Joomla.JText._('L2RULE16')); 
var l2ruleid_17 = genCombo('l2ruleid_17', Joomla.JText._('L2RULE17')); 
var l2ruleid_18 = genCombo('l2ruleid_18', Joomla.JText._('L2RULE18')); 
var l2ruleid_19 = genCombo('l2ruleid_19', Joomla.JText._('L2RULE19')); 
var l2ruleid_20 = genCombo('l2ruleid_20', Joomla.JText._('L2RULE20')); 

var l2ruleid_21 = genCombo('l2ruleid_21', Joomla.JText._('L2RULE21')); 
var l2ruleid_22 = genCombo('l2ruleid_22', Joomla.JText._('L2RULE22')); 
var l2ruleid_23 = genCombo('l2ruleid_23', Joomla.JText._('L2RULE23')); 
var l2ruleid_24 = genCombo('l2ruleid_24', Joomla.JText._('L2RULE24')); 
var l2ruleid_25 = genCombo('l2ruleid_25', Joomla.JText._('L2RULE25')); 

var l2ruleid_26 = genCombo('l2ruleid_26', Joomla.JText._('L2RULE26')); 
var l2ruleid_27 = genCombo('l2ruleid_27', Joomla.JText._('L2RULE27')); 
var l2ruleid_28 = genCombo('l2ruleid_28', Joomla.JText._('L2RULE28')); 
var l2ruleid_29 = genCombo('l2ruleid_29', Joomla.JText._('L2RULE29')); 
var l2ruleid_30 = genCombo('l2ruleid_30', Joomla.JText._('L2RULE30')); 

var l2ruleid_31 = genCombo('l2ruleid_31', Joomla.JText._('L2RULE31')); 
var l2ruleid_32 = genCombo('l2ruleid_32', Joomla.JText._('L2RULE32')); 
var l2ruleid_33 = genCombo('l2ruleid_33', Joomla.JText._('L2RULE33')); 
var l2ruleid_34 = genCombo('l2ruleid_34', Joomla.JText._('L2RULE34')); 
var l2ruleid_35 = genCombo('l2ruleid_35', Joomla.JText._('L2RULE35')); 


Ext.onReady(function(){
    var top = new Ext.FormPanel({
        ref: 'form',
        labelAlign: 'top',
        frame:false,
        bodyStyle:'padding:0px 0px 0',
        autoScroll: true,
        width: 600,
        items: [
				l2ruleid_1,
				l2ruleid_2,
				l2ruleid_3,
				l2ruleid_4,
				l2ruleid_5,
				
				l2ruleid_6,
				l2ruleid_7,
				l2ruleid_8,
				l2ruleid_9,
				l2ruleid_10,

				l2ruleid_11,
				l2ruleid_12,
				l2ruleid_13,
				l2ruleid_14,
				l2ruleid_15,
				
				l2ruleid_16,
				l2ruleid_17,
				l2ruleid_18,
				l2ruleid_19,
				l2ruleid_20,

				l2ruleid_21,
				l2ruleid_22,
				l2ruleid_23,
				l2ruleid_24,
				l2ruleid_25,

				l2ruleid_26,
				l2ruleid_27,
				l2ruleid_28,
				l2ruleid_29,
				l2ruleid_30,

				l2ruleid_31,
				l2ruleid_32,
				l2ruleid_33,
				l2ruleid_34,
				l2ruleid_35	    
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
								type:'l2rule1'
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
											//Ext.getCmp('threshold').setValue(action.result.data.threshold);
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
		            {name: 'l2ruleid_1', type: 'int', mapping: 'l2ruleid_1'},
				    {name: 'l2ruleid_2', type: 'int', mapping: 'l2ruleid_2'},
				    {name: 'l2ruleid_3', type: 'int', mapping: 'l2ruleid_3'},
				    {name: 'l2ruleid_4', type: 'int', mapping: 'l2ruleid_4'},
				    {name: 'l2ruleid_5', type: 'int', mapping: 'l2ruleid_5'},
				    {name: 'l2ruleid_6', type: 'int', mapping: 'l2ruleid_6'},
				    {name: 'l2ruleid_7', type: 'int', mapping: 'l2ruleid_7'},
				    {name: 'l2ruleid_8', type: 'int', mapping: 'l2ruleid_8'},
				    {name: 'l2ruleid_9', type: 'int', mapping: 'l2ruleid_9'},
				    {name: 'l2ruleid_10', type: 'int', mapping: 'l2ruleid_10'},
				    {name: 'l2ruleid_11', type: 'int', mapping: 'l2ruleid_11'},
				    {name: 'l2ruleid_12', type: 'int', mapping: 'l2ruleid_12'},
				    {name: 'l2ruleid_13', type: 'int', mapping: 'l2ruleid_13'},
				    {name: 'l2ruleid_14', type: 'int', mapping: 'l2ruleid_14'},
				    {name: 'l2ruleid_15', type: 'int', mapping: 'l2ruleid_15'},
				    {name: 'l2ruleid_16', type: 'int', mapping: 'l2ruleid_16'},
				    {name: 'l2ruleid_17', type: 'int', mapping: 'l2ruleid_17'},
				    {name: 'l2ruleid_18', type: 'int', mapping: 'l2ruleid_18'},
				    {name: 'l2ruleid_19', type: 'int', mapping: 'l2ruleid_19'},
				    {name: 'l2ruleid_20', type: 'int', mapping: 'l2ruleid_20'},
				    {name: 'l2ruleid_21', type: 'int', mapping: 'l2ruleid_21'},
				    {name: 'l2ruleid_22', type: 'int', mapping: 'l2ruleid_22'},
				    {name: 'l2ruleid_23', type: 'int', mapping: 'l2ruleid_23'},
				    {name: 'l2ruleid_24', type: 'int', mapping: 'l2ruleid_24'},
				    {name: 'l2ruleid_25', type: 'int', mapping: 'l2ruleid_25'},
				    {name: 'l2ruleid_26', type: 'int', mapping: 'l2ruleid_26'},
				    {name: 'l2ruleid_27', type: 'int', mapping: 'l2ruleid_27'},
				    {name: 'l2ruleid_28', type: 'int', mapping: 'l2ruleid_28'},
				    {name: 'l2ruleid_29', type: 'int', mapping: 'l2ruleid_29'},
				    {name: 'l2ruleid_30', type: 'int', mapping: 'l2ruleid_30'},
				    {name: 'l2ruleid_31', type: 'int', mapping: 'l2ruleid_31'},
				    {name: 'l2ruleid_32', type: 'int', mapping: 'l2ruleid_32'},
				    {name: 'l2ruleid_33', type: 'int', mapping: 'l2ruleid_33'},
				    {name: 'l2ruleid_34', type: 'int', mapping: 'l2ruleid_34'},
				    {name: 'l2ruleid_35', type: 'int', mapping: 'l2ruleid_35'}
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
		,height: 1520
		,width: '100%'
		,renderTo: 'l2rule1conf'
	});
})
</script>