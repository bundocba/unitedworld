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
Ext.ns('oseATH','oseATHReportBL');
oseATH.msg = new Ext.App();
function viewdetail(id)
{
	var win = new Ext.Window({
			id:'attackdetail',
			title: Joomla.JText._('Attack_information'),
            layout:'fit',
            width:1024,
            height:500,
            closeAction:'close',
            closable:'true',
            collapsible:'true',
            autoScroll:'true'
	})

	Ext.Ajax.request({
				url : 'index.php' ,
				params : {
					option : 'com_ose_antihacker',
					task:'viewAttack',
					controller:'report',
					id: id
				},
				method: 'POST',
				success: function ( result, request ) {
					msg = Ext.decode(result.responseText);
					if (msg.status!='ERROR')
					{
						win.show();
						win.update(msg.result);
					}
					else
					{
						Ext.Msg.alert('ERROR');
					}
				}
		});
}

Ext.onReady(function(){
		oseATHReportBL.cm = new Ext.grid.ColumnModel({
        defaults: {
            sortable: false
        },
        columns: [
            {id: 'country', header: '',  hidden:false, dataIndex: 'country', width: 20, sortable: true}
            ,{id: 'id', header: Joomla.JText._('ID'),  hidden:false, dataIndex: 'id', width: 20, sortable: true}
            ,{id: 'score', header: Joomla.JText._('Risk_Score'),  hidden:false, dataIndex: 'score', sortable: true}
            ,{id: 'name', header: Joomla.JText._('IP_Rule_Title'),  hidden:false, dataIndex: 'name', sortable: true}
            ,{id: 'ip_start', header: Joomla.JText._('Start_IP'),  hidden:false, dataIndex: 'ip_start', sortable: true}
            ,{id: 'ip_end', header: Joomla.JText._('End_IP'),  hidden:false, dataIndex: 'ip_end', sortable: true}
            ,{id: 'iptype', header: Joomla.JText._('IP_Type'),  hidden:false, dataIndex: 'iptype', sortable: true}
            ,{id: 'status', header: Joomla.JText._('Status'),  hidden:false, dataIndex: 'status', sortable: true}
            ,{id: 'host', header: Joomla.JText._('Host'),  hidden:false, dataIndex: 'host', sortable: true}
            ,{id: 'view', header: Joomla.JText._('View_Detail'),  hidden:false, dataIndex: 'view', width: 20}
	    ],
	    sortInfo:{field: 'id', direction: "ASC"}
    });

		oseATHReportBL.store = new Ext.data.Store({
		  proxy: new Ext.data.HttpProxy({
	            url: 'index.php?option=com_ose_antihacker',
	            method: 'POST'
	      }),
		  baseParams:{controller: "report", task: "getIPlist",limit: 25, id: <?php $id= JRequest::getInt('id'); echo (empty($id))?"''":$id; ?>},
		  reader: new Ext.data.JsonReader({
		    root: 'results',
		    totalProperty: 'total'
		  },[
		    {name: 'id', type: 'int', mapping: 'id'},
		    {name: 'country', type: 'string', mapping: 'country'},
		    {name: 'score', type: 'int', mapping: 'score'},
		    {name: 'name', type: 'string', mapping: 'name'},
		    {name: 'ip_start', type: 'string', mapping: 'ip_start'},
		    {name: 'ip_end', type: 'string', mapping: 'ip_end'},
		    {name: 'iptype', type: 'string', mapping: 'iptype'},
		    {name: 'status', type: 'string', mapping: 'status'},
		    {name: 'host', type: 'string', mapping: 'host'},
		    {name: 'view', type: 'string', mapping: 'view'}
		  ]),
		  autoLoad:{}
		  ,listeners: {
		    	beforeload: function(s)	{
		    			var status = Ext.getCmp('status');
		        		s.setBaseParam('status',status.getValue());
		    	}
		    }
	});

	oseATH.oseATHReportBL = new Ext.grid.GridPanel({
		id: 'oseATHReportBL'
		,cm: oseATHReportBL.cm
		,store: oseATHReportBL.store
		,viewConfig: {forceFit: true}
		,height: '500'
		,region: 'west'
		,margins: {top:5, right:0, bottom:5, left:0}
		,tbar: new Ext.Toolbar({
			defaults: {bodyStyle:'border:0px solid transparent;'},
			items: [
				    	{
				            id: 'addIPbutton',
				            text: Joomla.JText._('Add_an_IP'),
				            handler: function(){
				            	oseATH.oseATHReportBL.addWin = new Ext.Window({
			            			title: Joomla.JText._('Add_IP')
			            			,modal: true
			            			,width: 600
			            			,border: false
			            			,autoHeight: true
			            			,autoLoad: {
			            				url: 'index.php?option=com_ose_antihacker'
			            				,params:{controller: 'antihacker', task: 'getMod', mod:'addips',type:'addips',name:'form'}
			            				,scripts: true
			            				,callback: function(el,success,response,opt)	{
			            					oseATH.oseATHReportBL.addWin.add(oseATH.oseATHReportBL.form);
			            					oseATH.oseATHReportBL.addWin.doLayout();
			            				}
			            			}
					           	});
				            	oseATH.oseATHReportBL.addWin.show().alignTo(Ext.getBody(),'t-t');
				            }
				        },{
				        	id: 'delSelected',
				            text: Joomla.JText._('Delete_Items'),
				            handler: function(){
				            	Ext.Msg.confirm(Joomla.JText._('Delete_confirmation'), Joomla.JText._('Please_confirm_that_you_would_like_to_delete_the_selected_items'), function(btn, text){
									if (btn == 'yes'){
										var sel = oseATH.oseATHReportBL.getSelectionModel();
										var selections = sel.getSelections();
										athAjax('com_ose_antihacker','removeACL','report', selections);
							      	}
				            	})
				            }
				        },{
				        	id: 'blkSelected',
				            text: Joomla.JText._('Blacklist_IPs'),
				            handler: function(){
				            	Ext.Msg.confirm(Joomla.JText._('Blacklist_IPs_confirmation'), Joomla.JText._('Please_confirm_that_you_would_like_to_blacklist_the_selected_items'), function(btn, text){
									if (btn == 'yes'){
										var sel = oseATH.oseATHReportBL.getSelectionModel();
										var selections = sel.getSelections();
										athAjax('com_ose_antihacker','blacklistWhitelisted','report', selections);
							      	}
				            	})
				            }
				        },{
				        	id: 'whtSelected',
				            text: Joomla.JText._('Whitelist_IPs'),
				            handler: function(){
				            	Ext.Msg.confirm(Joomla.JText._('Whitelist_IPs_confirmation'), Joomla.JText._('Please_confirm_that_you_would_like_to_whitelist_the_selected_items'), function(btn, text){
									if (btn == 'yes'){
										var sel = oseATH.oseATHReportBL.getSelectionModel();
										var selections = sel.getSelections();
										athAjax('com_ose_antihacker','whitelistBlacklisted','report', selections);
							      	}
				            	})
				            }
				        },{
				        	id: 'monSelected',
				            text: Joomla.JText._('Monitored_IPs'),
				            handler: function(){
				            	Ext.Msg.confirm(Joomla.JText._('Monitored_IPs_confirmation'), Joomla.JText._('Please_confirm_that_you_would_like_to_monitor_the_selected_items'), function(btn, text){
									if (btn == 'yes'){
										var sel = oseATH.oseATHReportBL.getSelectionModel();
										var selections = sel.getSelections();
										athAjax('com_ose_antihacker','monitorBlacklisted','report', selections);
							      	}
				            	})
				            }
				        },
				        '->',
				        {
				           	xtype:'combo',
				            hiddenName: 'status',
				            id: 'status',
				            width:150,
						    typeAhead: true,
						    triggerAction: 'all',
						    lazyRender:false,
						    emptyText:'Status',
						    mode: 'local',
						    store: new Ext.data.ArrayStore({
						        id: 0,
						        fields: [
						            'value',
						            'text'
						        ],
						        data: [
						        	[1,Joomla.JText._('Blacklisted')],[2,Joomla.JText._('Monitored')], [3,Joomla.JText._('Whitelisted')]
						        ]
						    }),
						    valueField: 'value',
						    displayField: 'text',

						    listeners: {
						        beforequery: function(qe){
						        	delete qe.combo.lastQuery;
						        },
						        select: function(c,r,i)	{
						        	oseATHReportBL.store.reload({
					    				params:{status:r.data.value}
					    			});
					    		}
					        }
				        },'-',
				        new Ext.ux.form.SearchField({
			                store: oseATHReportBL.store,
			                paramName: 'search',
			                emptyText: Joomla.JText._('Search')
			            })
				    ]
		})
		,bbar:new Ext.PagingToolbar({
    		pageSize: 25,
    		store: oseATHReportBL.store,
    		plugins: new Ext.ux.grid.limit({}),
    		displayInfo: true,
		    displayMsg: Joomla.JText._('Displaying_topics')+' {0} - {1} '+Joomla.JText._('of')+' {2}',
		    emptyMsg: Joomla.JText._("No_topics_to_display")

	    })
       });

	    oseATH.oseATHReportBL.panel = new Ext.Panel({
		id: 'oseATHReportBL-panel'
		,border: false
		,layout: 'fit'
		,width: '100%'	
		,items:[
			oseATH.oseATHReportBL
		]
		,height: 'auto'
		,renderTo: 'oseantihackerReport'
	});

function athAjax(option, task, controller,selections)
  {
	var i=0;
    ids=new Array();
	for (i=0; i < selections.length; i++)
	{
        ids [i] = selections[i].id;
	}
	ids = Ext.encode(ids);
	// Ajax post scanning request;
	Ext.Ajax.request({
				url : 'index.php' ,
				params : {
					option : option,
					task:task,
					controller:controller,
					ids: ids
				},
				method: 'POST',
				success: function ( result, request ) {
					msg = Ext.decode(result.responseText);
					if (msg.status!='ERROR')
					{
						oseATH.msg.setAlert(msg.status, Joomla.JText._('The_action')+task+Joomla.JText._('was_executed_successfully'));
						oseATHReportBL.store.reload();
					}
					else
					{
						oseATH.msg.setAlert(Joomla.JText._('Error'), msg.result);
						oseATHReportBL.store.reload();
					}
				}
	});
  }
})
</script>