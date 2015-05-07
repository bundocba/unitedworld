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

Ext.onReady(function(){
    var top = new Ext.FormPanel({
        ref: 'form',
        labelAlign: 'top',
        frame:false,
        border: false,
        bodyStyle:'padding:0px 0px 0',
        autoScroll: true,
        width: '100%',
        height: 250,
        items: [
					{   xtype:'textfield',
					    fieldLabel: Joomla.JText._('OSE_htaccess_username'),
					    name: 'authUser',
					    id: 'authUser',
					    boxMaxWidth  : 400,
					    anchor:'98%'
					}
					,{
					    xtype:'textfield',
					    fieldLabel: Joomla.JText._('OSE_htaccess_password'),
					    name: 'authPass',
					    id: 'authPass',
					    boxMaxWidth  : 400,
					    anchor:'98%'
					},{
					    xtype:'textfield',
					    fieldLabel: Joomla.JText._('Please_enter_the_backend_path_of_the_system_that_OSE_Anti_Hacker_htpassword_will_protect'),
					    name: 'backPath',
					    id: 'backPath',
					    anchor:'98%'
					}
		],

        buttons: [{
            text: Joomla.JText._('Create'),
            handler: function (){
            	top.getForm().submit({
	            		url : 'index.php' ,
						params : {
							option : 'com_ose_antihacker',
							controller:'antihacker',
							task:'createHTPass'
						},
						method: 'POST',
						success: function ( form,action ) {
							msg = action.result;
							if (msg.status!='ERROR')
							{
								Ext.Msg.alert(msg.status, msg.result);
							}
							else
							{
								Ext.Msg.alert(Joomla.JText._('Error'), msg.result);
							}
						},
						failure: function ( form,action ) {
							msg = action.result;
							Ext.Msg.alert(Joomla.JText._('Error'), msg.result);
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
				    {name: 'backPath', type: 'string', mapping: 'backPath'},
				    {name: 'authUser', type: 'string', mapping: 'authUser'},
				    {name: 'authPass', type: 'string', mapping: 'authPass'}
		  	]
	  	}),
	  	listeners: {
			render: function(p){
				p.getForm().load({
					url : 'index.php' ,
					params : {
					option : 'com_ose_antihacker',
					task:'getActivationConfig',
					controller:'antihacker'
					},
					method: 'POST',
					success: function (form, action ) {
							if (action.result.data.authUser!='')
							{
								Ext.get('authUser').dom.disabled=true;
								Ext.get('authPass').dom.disabled=true;
							}
						}
					});
			}
		}
    });
    
      new Ext.Panel({
        height:250,
		width: '100%',
        plain:true,
        defaults:{autoScroll: false},
        items:[
			top
        ]
        ,renderTo: 'dashboardcontent'
    });
})
</script>