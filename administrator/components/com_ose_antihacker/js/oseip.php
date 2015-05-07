<?php
/**
  * @version     0.9 
  * @package        Open Source IP Control - com_ose_ipcontrol
  * @author        Open Source Excellence {@link 
http://www.opensource-excellence.co.uk}
  * @author        Created on 19-Apr-2010
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
  *  @Copyright Copyright (C) 2010- ... author-name
*/
defined('_JEXEC') or die("Direct Access Not Allowed");
?>

// --------------------------- Grid Params ----------------------------------
	oseip.store = new Ext.data.Store({
		  id: 'ipList',
		  proxy: new Ext.data.HttpProxy({
		            url: 'index.php?option=com_ose_cpu&controller=oseipc',
		            method: 'POST'
		        }),
		  baseParams:{task: "getList",ajax:true,limit:10}, 
		  reader: new Ext.data.JsonReader({   
		              // we tell the datastore where to get his data from
		    root: 'results',
		    totalProperty: 'total'
		  },[ 
		    {name: 'ID', type: 'int', mapping: 'id'},
		    {name: 'NAME', type: 'string', mapping: 'name'},
		    {name: 'IPStart', type: 'string', mapping: 'ip_start'},
		    {name: 'IPEnd', type: 'string', mapping: 'ip_end'},
		    {name: 'Type', type: 'string', mapping: 'iptype'},
		    {name: 'Status', type: 'int',  mapping: 'status'}
		  ]),
		  sortInfo:{field: 'ID', direction: "ASC"},
		  //autoLoad: {params:{start:0, limit:10}}
    });
    
	oseip.ipacl = Ext.data.Record.create([
					{
				        name: 'ID',
				        type: 'int',
				        mapping: 'id'
				    },{
				        name: 'NAME',
				        type: 'string',
				        mapping: 'name'
				    },{
				        name: 'IPStart',
				        type: 'string',
				        mapping: 'ip_start'
				    }, {
				        name: 'IPEnd',
				        type: 'string',
				        mapping: 'ip_end'
				    }, {
				        name: 'Type',
				        type: 'string',
				        mapping: 'iptype'
				    },{
				        name: 'Status',
				        type: 'int',
				        mapping: 'status'
				    }
				]);
	
	
	oseip.roweditor =  new Ext.ux.grid.RowEditor({
        saveText: 'Update'
    });
    
    oseip.roweditor.on({
		scope: this,
		afteredit: function(roweditor, changes, record, rowIndex) {
			oseip.buttonAction('save',record);
		}
	});
    
    oseip.filters = new Ext.ux.grid.GridFilters({
        // encode and local configuration options defined previously for easier reuse
        encode: true, // json encode the filter query
        local: false,   // defaults to false (remote filtering)
        filters: [ {
            type: 'list',
            dataIndex: 'Status',
            options: [[0,'White'],[1,'Black']],
            phpMode: true
        }]
    }); 
  
	oseip.cm = new Ext.grid.ColumnModel({
	        defaults: {
	            width: 120,
	            sortable: true
	        },
	        columns: [
		        new Ext.grid.RowNumberer(),
		        {
		            id: 'id',
		            header: 'ID',
		            dataIndex: 'ID',
		            sortable: true,
		            hidden : true,
		            editor: {
		                xtype: 'textfield',
		                allowBlank: false
		            }
		        },{
		            id: 'name',
		            header: 'Access Name',
		            dataIndex: 'NAME',
		            sortable: true,
		            editor: {
		                xtype: 'textfield',
		                allowBlank: true,
		                emptyText:'Access Name'
		            },
		            
		        },{
		            id: 'ipstart',
		            header: 'IP Start',
		            dataIndex: 'IPStart',
		            sortable: true,
		            editor: {
		                xtype: 'textfield',
					    allowBlank: false
		            }
		           
		        },{
		            id: 'ipend',
		            header: 'IP End',
		            dataIndex: 'IPEnd',
		            sortable: true,
		            editor: {
		                xtype: 'textfield',
					    allowBlank: true
		            }
		        },{
		            id: 'type',
		            header: 'Type',
		            dataIndex: 'Type',
		            renderer: function(value)	{
				    	return (value=='ip')?'IP':'IP Range';
				    },
		            sortable: true,
		            editor: new Ext.form.ComboBox({
		            	name: 'Type',
					    typeAhead: true,
					    triggerAction: 'all',
					    lazyRender:true,
					    mode: 'local',
					    
					    store: new Ext.data.ArrayStore({
					        fields:['typeValue', 'typeName'],
					        data: [['ip', 'IP'], ['ips', 'IP Range']]
					    }),
					    valueField: 'typeValue',
					    displayField: 'typeName'
					})
		        },{
		        	id: 'status',
	            	header: 'Status', 
	            	renderer: function(value)	{
				    	return (value==0)?'White':'Black';
				    },
				    dataIndex: 'Status',
				    editor: new Ext.form.ComboBox({
				    	name: 'Status',
					    typeAhead: true,
					    triggerAction: 'all',
					    lazyRender:true,
					    mode: 'local',
					  
					    store: new Ext.data.ArrayStore({
					        fields:['statusValue', 'statusName'],
					        data: [[0, 'White'], [1, 'Black']]
					    }),
					    valueField: 'statusValue',
					    displayField: 'statusName'
					})
	            }]
	});
	
	oseip.tbar = new Ext.Toolbar({
				    items: [
				    	{
				            iconCls: 'icon-user-add',
				            text: 'Add',
				            handler: function(){
				                var e = new oseip.ipacl({
				                	ID:0,
				                    IPStart: '',
				                    IPEnd: '',
				                    Type: 'ip',
				                    Status: 0
				                });
				                oseip.roweditor.stopEditing();
				                oseip.store.insert(0, e);
				                oseip.grid.getView().refresh();
				                oseip.grid.getSelectionModel().selectRow(0);
				                oseip.roweditor.startEditing(0);
				            }
				        },{
				        	ref: 'removeBtn',
				            iconCls: 'icon-user-delete',
				            text: 'Remove',
				            disabled: true,
				            handler: function(){
				                oseip.roweditor.stopEditing();
				                var s = oseip.grid.getSelectionModel().getSelections();
				                for(var i = 0, r; r = s[i]; i++){
				                	oseip.buttonAction('remove',r);
				                    oseip.store.remove(r);
				                }
				            }
				        },
				        '->', 
				        new Ext.ux.form.SearchField({
			                store: oseip.store,
			                paramName: 'search',
			                width:150
			            })
				    ]
				});
				
//--------------------------- IP List Grid Panel------------------------------------ 
    oseip.grid = new Ext.grid.GridPanel({
    	id:'ipgrid',
    	title: 'IP Access Control',
        store: oseip.store,
        viewConfig:{forceFit:true},
        //region:'center',
        plugins: [oseip.roweditor,oseip.filters],
     	colModel:oseip.cm,
     	gridlimit:10,
     	listeners: {
     			activate: function(){
     				oseip.store.reload();
     				oseip.grid.getView().refresh();
     			}
     	},
     	sm: new Ext.grid.RowSelectionModel({singleSelect:true}),
     	
     	tbar: oseip.tbar,
            
     	bbar:[
     			new Ext.PagingToolbar({
		    		pageSize: 10,
		    		store: oseip.store,
		    		displayInfo: true,
				    displayMsg: 'Displaying topics {0} - {1} of {2}',
				    emptyMsg: "No topics to display"
			    }
	    	)]
        
    });
    
    oseip.grid.getSelectionModel().on('selectionchange', function(sm){
       oseip.tbar.removeBtn.setDisabled(sm.getCount() < 1); // >
    });
    
    oseip.buttonAction = function(action,record)	{
    	Ext.Ajax.request({   
			    waitMsg: 'Please wait...',
			    url: 'index.php?option=com_ose_cpu&controller=oseipc',
			    params: {
			    	task: action,
			    	ajax: true,
			    	id: record.data.ID,
			    	name: record.data.NAME,
			        ip_start: record.data.IPStart,
			        ip_end: record.data.IPEnd,
			        status: record.data.Status,
			        iptype: record.data.Type
			    }, 
			    success: function(response){							
			        //var result=eval(response.responseText);
			        var result=response.responseText;
			        switch(result){
				        case 's':
				   			oseip.app.setAlert('Successfully','Have '+action+' It!');
				          	oseip.store.commitChanges();   // changes successful, get rid of the red triangles
				           	oseip.store.reload();          // reload our datastore.
				        	break;
				        
				        case 'e1':
				        	oseip.app.setAlert('Initialization Issue','Missing Some Files!');
				        	break;
				        case 'e2':
				        	oseip.app.setAlert('IP Input Error','Please Check Your IP Input Again!');
				           	break;
				        case 'e3':	
				        	oseip.app.setAlert('Save Error','We couldn\'t save him...');
				           	break;
				           			
				        default:
				        	//Ext.get('headerpanel').hide();
				           	oseip.app.setAlert('Uh uh...','We couldn\'t save him...');
				           	break;
			    	}
			    },
			    failure: function(response){
			        var result=response.responseText;
			        oseip.app.setAlert('error','could not connect to the database. retry later');		
			    }									    
		   });   
    };
