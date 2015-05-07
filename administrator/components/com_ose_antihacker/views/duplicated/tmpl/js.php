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
Ext.ns('oseATH','oseATH.dup');
Ext.onReady(function(){
	oseATH.app = new Ext.App();
	oseATH.dup.action = function(record)	{
		var ip = record.get('IP');
		if(!addonOrderViewWin)	{
			var addonOrderViewWin = new Ext.Window({
				//title: 'Action'
				width: 500
				,autoHeight: true
				,modal: true
				,items: [{
					xtype: 'form'
					,items:[{
			            	fieldLabel: Joomla.JText._('ACL_Rule_to_store_this_ip')
				            ,xtype: 'combo'
						    ,typeAhead: true
						    ,triggerAction: 'all'
						    ,lazyInit: false
						    ,mode: 'remote'
						    ,Width: 300
						    ,listWidth: 300
						    ,forceSelection: true
						    ,store: new Ext.data.Store({
						  		proxy: new Ext.data.HttpProxy({
						            url: 'index.php?option=com_ose_antihacker&controller=report',
						            method: 'POST'
					      		})
							  	,baseParams:{task: "getDupACL",ip:ip}
							  	,reader: new Ext.data.JsonReader({
							    	root: 'results'
							    	,totalProperty: 'total'
							    	,idProperty: 'ACLID'
							  	},[
							    {name: 'ACLID', type: 'string', mapping: 'ACLID'},
							    {name: 'ACLNAME', type: 'string', mapping: 'ACLNAME'}
							  	])
							})
							,valueField: 'ACLID'
						    ,displayField: 'ACLNAME'
						    ,listeners: {
						        // delete the previous query in the beforequery event or set
						        // combo.lastQuery = null (this will reload the store the next time it expands)
						        beforequery: function(qe){
						        	delete qe.combo.lastQuery;
						        },

						        select: function(c,r,i)	{

						        	record.data.ACLID = r.get('ACLID');
						        }
					        }
				        }]

					,buttons: [{
						text: Joomla.JText._('Resolve_IP_Conflicts')
						,handler: function(b){
							oseATH.dup.buttonAction('removeDup',record);
		                    oseATH.dup.store.remove(record);
		                    addonOrderViewWin.close();
							//oseATH.dup.buttonAction('removeDup',record);
		                   // oseATH.dup.store.remove(r);
						}
					}]
				}]


			})
		}

		addonOrderViewWin.show().alignTo(Ext.getBody(),'t-t',[0,10]);
	}
  // --------------------------- Grid Params ----------------------------------
	oseATH.dup.store = new Ext.data.Store({
		  id: 'ipList',
		  proxy: new Ext.data.HttpProxy({
		            url: 'index.php?option=com_ose_antihacker&controller=report',
		            method: 'POST'
		        }),
		  baseParams:{task: "getDupList",ajax:true,limit:25},
		  reader: new Ext.data.JsonReader({
		              // we tell the datastore where to get his data from
		    root: 'results',
		    totalProperty: 'total'
		  },[
		    {name: 'ID', type: 'int', mapping: 'id'},
		    {name: 'ACLID', type: 'int', mapping: 'ACLID'}, // Belonged To...
		    {name: 'IP', type: 'string', mapping: 'ip'},
		    {name: 'Status', type: 'string',  mapping: 'status'}
		  ]),
		  sortInfo:{field: 'ID', direction: "ASC"},
		  autoLoad: {}
    });

	oseATH.dup.cm = new Ext.grid.ColumnModel({
	        defaults: {
	            width: 120,
	            sortable: true
	        },
	        columns: [
		        new Ext.grid.RowNumberer(),
		        {
		            id: 'id',
		            header: Joomla.JText._('ID'),
		            dataIndex: 'ID',
		            sortable: true,
		            hidden : true
		        },{
		            id: 'ip',
		            header: Joomla.JText._('IP'),
		            dataIndex: 'IP',
		            sortable: true

		        },{
		        	id: 'status',
	            	header: Joomla.JText._('Status'),
	            	hidden: false,
				    dataIndex: 'Status'
	            },{
		            id: 'bt',
		            header: Joomla.JText._('ACL_Rule_to_store_this_ip'),
		            xtype: 'actioncolumn'
				    	,items: [{

				    		getClass: function(v, meta, rec)	{
	                		return 'view-col';
	                		}
		                    ,tooltip: 'Click_to_view'
		                    ,handler: function(grid, rowIndex, colIndex) {
				    			var record = grid.getStore().getAt(rowIndex);
				    			oseATH.dup.action(record);
		                    }
		                }]
		        }]
	});

	  // --------------------------- Grid -----------------------------------------
    oseATH.dup.grid = new Ext.grid.GridPanel({
    	id:'duptable',
    	store: oseATH.dup.store,
        viewConfig:{forceFit:true},
        //plugins: [oseATH.dup.roweditor,oseATH.dup.filters],
     	cm:oseATH.dup.cm,
     	height: 500,
     	sm: new Ext.grid.RowSelectionModel({
     		singleSelect:true
     	}),

     	tbar: oseATH.dup.tbar,

     	bbar:new Ext.PagingToolbar({
		    		pageSize: 25,
		    		store: oseATH.dup.store,
		    		plugins: new Ext.ux.grid.limit({}),
		    		displayInfo: true,
		    		displayMsg: Joomla.JText._('Displaying_topics')+' {0} - {1} '+Joomla.JText._('of')+' {2}',
				    emptyMsg: Joomla.JText._("No_topics_to_display")
			    }
	    	)

    });

    oseATH.dup.buttonAction = function(action,record)	{
    	Ext.Ajax.request({
			    waitMsg: 'Please wait...',
			    url: 'index.php?option=com_ose_antihacker&controller=report',
			    params: {
			    	task: action,
			    	ajax: true,
			    	id: record.get('ID'),
			    	ip: record.get('IP'),
			    	acl_id:record.get('ACLID'),
			    	status:record.get('Status')
			    },
			    success: function(response){
			    	var result= Ext.decode(response.responseText);
			    	oseATH.app.setAlert(result.status,result.result);
			    	oseATH.dup.store.reload();
			    },
			    failure: function(response){
			        var result=response.responseText;
			        oseATH.app.setAlert('error','could not connect to the database. retry later');
			    }
		   });
    };

	oseATH.dup.panel = new Ext.Panel({
		id: 'oseATH-panel'
		,autoExpandColumn: 'ip'
		,border: false
		,layout: 'fit'
		,items:[
			oseATH.dup.grid
		]
		,height: 'auto'
		,width: '100%'
		,renderTo: 'oseATHDuplicated'
	});

})
</script>