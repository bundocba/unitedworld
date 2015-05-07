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
Ext.ns('oseATH','oseATHDashboard');

/*
var win = new Ext.Window({
				id:'activationresults',
                layout:'fit',
                width:800,
                height:300,
                closeAction:'hide',
                collapsible:'true',
                autoScroll:'false'
				})
oseATHDashboard.form1 = new Ext.FormPanel({
        ref: 'form1',
        labelAlign: 'top',
        border:false,
        frame:false,
        width: '100%',
        items: [
        		{
				xtype:'fieldset',
				fieldLabel: 'OSE Anti Hacker Activation Testing',
				labelWidth: 600,
				id:'activationTesting',
				items:[
		                {
		                    xtype:'textfield',
		                    fieldLabel: 'Please enter the frontend path of the system that Anti-Hacker will protect',
		                    name: 'frontPath',
		                    id: 'frontPath',
		                    boxMaxWidth  : 400,
		                    anchor:'98%'
		                },
		                {
		                    xtype:'textfield',
		                    fieldLabel: 'Please enter the URL of the website the Anti-Hacker will protect (this is for PHP setting checking purpose)',
		                    name: 'frontURL',
		                    id: 'frontURL',
		                    boxMaxWidth  : 400,
		                    anchor:'98%'
		                }
					]
                }],
        buttons: [{
            text: 'Test Activation',
            handler: function (){
				oseATHDashboard.form1.getForm().submit({
							url : 'index.php' ,
							params : {
								option : 'com_ose_antihacker',
								task:'activateAntihacker',
								controller:'antihacker'
							},
							method: 'POST',
							success: function ( form,action ) {
								msg = action.result;
								if (msg.status!='ERROR')
								{
									Ext.Msg.alert(msg.status,
												  msg.result+', a window will be open to check if the setting is effective.',
												  function (){

												  	win.show();
												  	win.load({
												  		url:Ext.get('frontURL').dom.value + '/osecheck.php'
												  	});
												  }
												 );
								}
								else
								{
									Ext.Msg.alert('Error', msg.result);
								}
							},
							failure: function ( form,action ) {
								msg = action.result;
								Ext.Msg.alert('Error', msg.result);
							}

				});
            }
        }],
        reader: new Ext.data.JsonReader({
		    root: 'result',
		    totalProperty: 'total',
		    idProperty: "id",
		    fields:[
		     	{name: 'id', type: 'int', mapping: 'id'},
			    {name: 'frontPath', type: 'string', mapping: 'frontPath'},
			    {name: 'frontURL', type: 'string', mapping: 'frontURL'}
		  	]
	  	}),
	  	listeners: {
			render: function(p){
				p.getForm().load({
					url : 'index.php' ,
					params : {
					option : 'com_ose_antihacker',
					task:'getActivationConfig',
					controller:'antihacker',
					},
					method: 'POST'
					});
			}
		}
    });


Ext.onReady(function(){
      new Ext.Panel({
        width:'100%',
        plain:true,
        defaults:{autoScroll: false},
        items:[
        	oseATHDashboard.form1,
        ]
        ,renderTo: 'dashboardcontent'
    });

})
*/
</script>