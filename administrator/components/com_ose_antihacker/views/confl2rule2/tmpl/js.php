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

var l2ruleid_36 = genCombo('l2ruleid_36', Joomla.JText._('L2RULE36')); 
var l2ruleid_37 = genCombo('l2ruleid_37', Joomla.JText._('L2RULE37')); 
var l2ruleid_38 = genCombo('l2ruleid_38', Joomla.JText._('L2RULE38')); 
var l2ruleid_39 = genCombo('l2ruleid_39', Joomla.JText._('L2RULE39')); 
var l2ruleid_40 = genCombo('l2ruleid_40', Joomla.JText._('L2RULE40')); 

var l2ruleid_41 = genCombo('l2ruleid_41', Joomla.JText._('L2RULE41')); 
var l2ruleid_42 = genCombo('l2ruleid_42', Joomla.JText._('L2RULE42')); 
var l2ruleid_43 = genCombo('l2ruleid_43', Joomla.JText._('L2RULE43')); 
var l2ruleid_44 = genCombo('l2ruleid_44', Joomla.JText._('L2RULE44')); 
var l2ruleid_45 = genCombo('l2ruleid_45', Joomla.JText._('L2RULE45')); 

var l2ruleid_46 = genCombo('l2ruleid_46', Joomla.JText._('L2RULE46')); 
var l2ruleid_47 = genCombo('l2ruleid_47', Joomla.JText._('L2RULE47')); 
var l2ruleid_48 = genCombo('l2ruleid_48', Joomla.JText._('L2RULE48')); 
var l2ruleid_49 = genCombo('l2ruleid_49', Joomla.JText._('L2RULE49')); 
var l2ruleid_50 = genCombo('l2ruleid_50', Joomla.JText._('L2RULE50')); 

var l2ruleid_51 = genCombo('l2ruleid_51', Joomla.JText._('L2RULE51')); 
var l2ruleid_52 = genCombo('l2ruleid_52', Joomla.JText._('L2RULE52')); 
var l2ruleid_53 = genCombo('l2ruleid_53', Joomla.JText._('L2RULE53')); 
var l2ruleid_54 = genCombo('l2ruleid_54', Joomla.JText._('L2RULE54')); 
var l2ruleid_55 = genCombo('l2ruleid_55', Joomla.JText._('L2RULE55')); 

var l2ruleid_56 = genCombo('l2ruleid_56', Joomla.JText._('L2RULE56')); 
var l2ruleid_57 = genCombo('l2ruleid_57', Joomla.JText._('L2RULE57')); 
var l2ruleid_58 = genCombo('l2ruleid_58', Joomla.JText._('L2RULE58')); 
var l2ruleid_59 = genCombo('l2ruleid_59', Joomla.JText._('L2RULE59')); 
var l2ruleid_60 = genCombo('l2ruleid_60', Joomla.JText._('L2RULE60')); 

var l2ruleid_61 = genCombo('l2ruleid_61', Joomla.JText._('L2RULE61')); 
var l2ruleid_62 = genCombo('l2ruleid_62', Joomla.JText._('L2RULE62')); 
var l2ruleid_63 = genCombo('l2ruleid_63', Joomla.JText._('L2RULE63')); 
var l2ruleid_64 = genCombo('l2ruleid_64', Joomla.JText._('L2RULE64')); 
var l2ruleid_65 = genCombo('l2ruleid_65', Joomla.JText._('L2RULE65')); 

var l2ruleid_66 = genCombo('l2ruleid_66', Joomla.JText._('L2RULE66')); 
var l2ruleid_67 = genCombo('l2ruleid_67', Joomla.JText._('L2RULE67')); 
var l2ruleid_68 = genCombo('l2ruleid_68', Joomla.JText._('L2RULE68')); 
var l2ruleid_69 = genCombo('l2ruleid_69', Joomla.JText._('L2RULE69')); 
var l2ruleid_70 = genCombo('l2ruleid_70', Joomla.JText._('L2RULE70')); 



Ext.onReady(function(){
    var top = new Ext.FormPanel({
        ref: 'form',
        labelAlign: 'top',
        frame:false,
        bodyStyle:'padding:0px 0px 0',
        autoScroll: true,
        width: 600,
        items: [
				l2ruleid_36,
				l2ruleid_37,
				l2ruleid_38,
				l2ruleid_39,
				l2ruleid_40,

				l2ruleid_41,
				l2ruleid_42,
				l2ruleid_43,
				l2ruleid_44,
				l2ruleid_45,

				l2ruleid_46,
				l2ruleid_47,
				l2ruleid_48,
				l2ruleid_49,
				l2ruleid_50,

				l2ruleid_51,
				l2ruleid_52,
				l2ruleid_53,
				l2ruleid_54,
				l2ruleid_55,

				l2ruleid_56,
				l2ruleid_57,
				l2ruleid_58,
				l2ruleid_59,
				l2ruleid_60,

				l2ruleid_61,
				l2ruleid_62,
				l2ruleid_63,
				l2ruleid_64,
				l2ruleid_65,

				l2ruleid_66,
				l2ruleid_67,
				l2ruleid_68,
				l2ruleid_69,
				l2ruleid_70
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
								type:'l2rule2'
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
		            {name: 'l2ruleid_36', type: 'int', mapping: 'l2ruleid_36'},
				    {name: 'l2ruleid_37', type: 'int', mapping: 'l2ruleid_37'},
				    {name: 'l2ruleid_38', type: 'int', mapping: 'l2ruleid_38'},
				    {name: 'l2ruleid_39', type: 'int', mapping: 'l2ruleid_39'},
				    {name: 'l2ruleid_40', type: 'int', mapping: 'l2ruleid_40'},
				    {name: 'l2ruleid_41', type: 'int', mapping: 'l2ruleid_41'},
				    {name: 'l2ruleid_42', type: 'int', mapping: 'l2ruleid_42'},
				    {name: 'l2ruleid_43', type: 'int', mapping: 'l2ruleid_43'},
				    {name: 'l2ruleid_44', type: 'int', mapping: 'l2ruleid_44'},
				    {name: 'l2ruleid_45', type: 'int', mapping: 'l2ruleid_45'},
				    {name: 'l2ruleid_46', type: 'int', mapping: 'l2ruleid_46'},
				    {name: 'l2ruleid_47', type: 'int', mapping: 'l2ruleid_47'},
				    {name: 'l2ruleid_48', type: 'int', mapping: 'l2ruleid_48'},
				    {name: 'l2ruleid_49', type: 'int', mapping: 'l2ruleid_49'},
				    {name: 'l2ruleid_50', type: 'int', mapping: 'l2ruleid_50'},
				    {name: 'l2ruleid_51', type: 'int', mapping: 'l2ruleid_51'},
				    {name: 'l2ruleid_52', type: 'int', mapping: 'l2ruleid_52'},
				    {name: 'l2ruleid_53', type: 'int', mapping: 'l2ruleid_53'},
				    {name: 'l2ruleid_54', type: 'int', mapping: 'l2ruleid_54'},
				    {name: 'l2ruleid_55', type: 'int', mapping: 'l2ruleid_55'},
				    {name: 'l2ruleid_56', type: 'int', mapping: 'l2ruleid_56'},
				    {name: 'l2ruleid_57', type: 'int', mapping: 'l2ruleid_57'},
				    {name: 'l2ruleid_58', type: 'int', mapping: 'l2ruleid_58'},
				    {name: 'l2ruleid_59', type: 'int', mapping: 'l2ruleid_59'},
				    {name: 'l2ruleid_60', type: 'int', mapping: 'l2ruleid_60'},
				    {name: 'l2ruleid_61', type: 'int', mapping: 'l2ruleid_61'},
				    {name: 'l2ruleid_62', type: 'int', mapping: 'l2ruleid_62'},
				    {name: 'l2ruleid_63', type: 'int', mapping: 'l2ruleid_63'},
				    {name: 'l2ruleid_64', type: 'int', mapping: 'l2ruleid_64'},
				    {name: 'l2ruleid_65', type: 'int', mapping: 'l2ruleid_65'},
				    {name: 'l2ruleid_66', type: 'int', mapping: 'l2ruleid_66'},
				    {name: 'l2ruleid_67', type: 'int', mapping: 'l2ruleid_67'},
				    {name: 'l2ruleid_68', type: 'int', mapping: 'l2ruleid_68'},
				    {name: 'l2ruleid_69', type: 'int', mapping: 'l2ruleid_69'},
				    {name: 'l2ruleid_70', type: 'int', mapping: 'l2ruleid_70'}
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
					type:'l2rule2'					
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
		,renderTo: 'l2rule2conf'
	});
})
</script>