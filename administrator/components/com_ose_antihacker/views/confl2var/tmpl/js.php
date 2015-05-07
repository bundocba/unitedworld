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
	    labelStyle: 'min-width: 320px;',
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

var convertFromRepetition = genCombo('convertFromRepetition', 'Remove_Repetition_Codes' ); 
var convertFromCommented = genCombo('convertFromCommented', 'Remove_Comments' ); 
var convertFromWhiteSpace = genCombo('convertFromWhiteSpace', 'Remove_White_Space_and_New_Lines' ); 
var convertFromJSCharcode = genCombo('convertFromJSCharcode', 'Convert_common_JS_charcode_patterns' ); 
var convertJSRegexModifiers = genCombo('convertJSRegexModifiers', 'Remove_JS_regular_expression_modifiers' ); 

var convertEntities = genCombo('convertEntities', 'Convert_Hex_Dec_entities' ); 
var convertQuotes = genCombo('convertQuotes', 'Formalize_All_Quotes_to_Double_Quotes' ); 
var convertFromSQLHex = genCombo('convertFromSQLHex', 'Convert_and_Remove_Harmful_HEX_SQL_Query' ); 

var convertFromControlChars = genCombo('convertFromControlChars', 'Converts_malicious_unicode_characters' ); 
var convertFromNestedBase64 = genCombo('convertFromNestedBase64', 'Translates_base64_strings_and_fragments' ); 
var convertFromOutOfRangeChars = genCombo('convertFromOutOfRangeChars', 'Converts_Out_of_Range_Characters' ); 

var convertFromXML = genCombo('convertFromXML', 'Strips_HTML_tags' ); 
var convertFromJSUnicode = genCombo('convertFromJSUnicode', 'Converts_JS_Unicode_Characters_to_its_Original' ); 
var convertFromUTF7 = genCombo('convertFromUTF7', 'Converts_UTF7_Chars_to_UTF8_Chars' );
var convertFromConcatenated = genCombo('convertFromConcatenated', 'convertFromConcatenated' ); 
var convertFromProprietaryEncodings = genCombo('convertFromProprietaryEncodings', 'Converts_proprietary_encoding_types' );


Ext.onReady(function(){
    var top = new Ext.FormPanel({
        ref: 'form',
        labelAlign: 'top',
        frame:false,
        bodyStyle:'padding:0px 0px 0',
        autoScroll: true,
        width: 600,
        items: [
				convertFromRepetition,
				convertFromCommented,
				convertFromWhiteSpace,
				convertFromJSCharcode,
				convertJSRegexModifiers,
				
				convertEntities,
				convertQuotes,
				convertFromSQLHex,
				
				convertFromControlChars,
				convertFromNestedBase64,
				convertFromOutOfRangeChars,
				
				convertFromXML,
				convertFromJSUnicode,
				convertFromUTF7,
				convertFromConcatenated,
				convertFromProprietaryEncodings		    
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
								type:'l2var'
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
		        {name: 'id', type: 'int', mapping: 'id'},
			    {name: 'convertFromRepetition', type: 'int', mapping: 'convertFromRepetition'},
			    {name: 'convertFromCommented', type: 'int', mapping: 'convertFromCommented'},
			    {name: 'convertFromWhiteSpace', type: 'int', mapping: 'convertFromWhiteSpace'},
			    {name: 'convertFromJSCharcode', type: 'int', mapping: 'convertFromJSCharcode'},
			    {name: 'convertJSRegexModifiers', type: 'int', mapping: 'convertJSRegexModifiers'},

			    {name: 'convertEntities', type: 'int', mapping: 'convertEntities'},
			    {name: 'convertQuotes', type: 'int', mapping: 'convertQuotes'},
			    {name: 'convertFromSQLHex', type: 'int', mapping: 'convertFromSQLHex'},

			    {name: 'convertFromControlChars', type: 'int', mapping: 'convertFromControlChars'},
			    {name: 'convertFromNestedBase64', type: 'int', mapping: 'convertFromNestedBase64'},
			    {name: 'convertFromOutOfRangeChars', type: 'int', mapping: 'convertFromOutOfRangeChars'},

			    {name: 'convertFromXML', type: 'int', mapping: 'convertFromXML'},
			    {name: 'convertFromJSUnicode', type: 'int', mapping: 'convertFromJSUnicode'},
			    {name: 'convertFromUTF7', type: 'int', mapping: 'convertFromUTF7'},
			    {name: 'convertFromConcatenated', type: 'int', mapping: 'convertFromConcatenated'},
			    {name: 'convertFromProprietaryEncodings', type: 'int', mapping: 'convertFromProprietaryEncodings'}
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
		,height: 720
		,width: '100%'
		,renderTo: 'l2varconf'
	});
})
</script>