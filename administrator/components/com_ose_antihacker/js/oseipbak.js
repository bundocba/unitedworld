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
//--------------------------- Duplicated Panel------------------------------------ 
	  // --------------------------- Grid Params ----------------------------------
	oseip.dup.store = new Ext.data.Store({
		  id: 'ipList',
		  proxy: new Ext.data.HttpProxy({
		            url: 'index.php?option=com_ose_cpu&controller=oseipc',
		            method: 'POST'
		        }),
		  baseParams:{task: "getDupList",ajax:true,limit:10}, 
		  reader: new Ext.data.JsonReader({   
		              // we tell the datastore where to get his data from
		    root: 'results',
		    totalProperty: 'total'
		  },[ 
		    {name: 'ACL_ID', type: 'int', mapping: 'acl_id'},
		    {name: 'ID', type: 'int', mapping: 'id'},
		    {name: 'BT', type: 'string', mapping: 'name'}, // Belonged To...
		    {name: 'IP', type: 'string', mapping: 'ip'},
		    {name: 'Status', type: 'int',  mapping: 'status'}
		  ]),
		  sortInfo:{field: 'ID', direction: "ASC"},
		  //autoLoad: {params:{start:0, limit:10}}
    });
	
    oseip.dup.filters = new Ext.ux.grid.GridFilters({
        // encode and local configuration options defined previously for easier reuse
        encode: true, // json encode the filter query
        local: false,   // defaults to false (remote filtering)
        filters: [ ]
    }); 
    
    oseip.dup.roweditor =  new Ext.ux.grid.RowEditor({
        saveText: 'Update'
    });    
    
    oseip.dup.dupAcl = new Ext.data.JsonStore({
							
					        url: 'index.php?option=com_ose_cpu&controller=oseipc',
							root: 'results',
							    
							baseParams:{task: "getDupAcl",ajax:true},
							
							fields:['', 'ACLNAME'],
					    });
  
	oseip.dup.cm = new Ext.grid.ColumnModel({
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
		            hidden : true
		        },{
		            id: 'ip',
		            header: 'IP',
		            dataIndex: 'IP',
		            sortable: true
		           
		        },{
		        	id: 'status',
	            	header: 'Status', 
	            	renderer: function(value)	{
				    	return (value==0)?'White':'Black';
				    },
				    dataIndex: 'Status'
				    
	            },{
		            id: 'bt',
		            header: 'Belonged To',
		            dataIndex: 'BT',
		            sortable: true,
		            editor: new Ext.form.ComboBox({
				    	name: 'Acl Name',
					    typeAhead: true,
					    triggerAction: 'all',
					    lazyRender:true,
					    mode: 'remote',
					  
					    store: oseip.dup.dupAcl,
					    valueField: 'ACLID',
					    displayField: 'ACLNAME'
		            })
		        }]
	});
	
	
	
	
	oseip.dup.tbar = new Ext.Toolbar({
				    items: [
				    	{
				        	ref: 'removeBtn',
				            iconCls: 'icon-user-delete',
				            text: 'Remove',
				            disabled: true,
				            handler: function(){
				                oseip.dup.roweditor.stopEditing();
				                var s = oseip.dup.grid.getSelectionModel().getSelections();
				               
				                for(var i = 0, r; r = s[i]; i++){
				                	oseip.dup.buttonAction('removeDup',r);
				                    oseip.dup.store.remove(r);
				                }
				            }
				        }
				    ]
				});
	  // --------------------------- Grid -----------------------------------------
    oseip.dup.grid = new Ext.grid.GridPanel({
    	id:'duptable',
    	title: 'Duplicated IP Setting',
    	store: oseip.dup.store,
        viewConfig:{forceFit:true},
        //region:'center',
        plugins: [oseip.dup.roweditor,oseip.dup.filters],
     	colModel:oseip.dup.cm,
     	gridlimit:10,
     	listeners: {
     			activate: function(){
     				oseip.dup.store.reload();
     				oseip.dup.grid.getView().refresh();
     			}
     	},
     	sm: new Ext.grid.RowSelectionModel({singleSelect:false}),
     	
     	tbar: oseip.dup.tbar,
            
     	bbar:[
     			new Ext.PagingToolbar({
		    		pageSize: 10,
		    		store: oseip.dup.store,
		    		displayInfo: true,
				    displayMsg: 'Displaying topics {0} - {1} of {2}',
				    emptyMsg: "No topics to display"
			    }
	    	)]
        
    });
    
    oseip.dup.grid.getSelectionModel().on('selectionchange', function(sm){
       oseip.dup.tbar.removeBtn.setDisabled(sm.getCount() < 1); // >
    });
    
    oseip.dup.buttonAction = function(action,record)	{
    	Ext.Ajax.request({   
			    waitMsg: 'Please wait...',
			    url: 'index.php?option=com_ose_cpu&controller=oseipc',
			    params: {
			    	task: action,
			    	ajax: true,
			    	id: record.data.ID,
			    	//acl_id:record.data.ACLID,
			    	status:record.data.Status
			    }, 
			    success: function(response){							
			        //var result=eval(response.responseText);
			       
			        var result=response.responseText;
			        switch(result){
				        case 's':
				   			oseip.app.setAlert('Successfully','Have Remove It!');
				          	oseip.dup.store.commitChanges();   // changes successful, get rid of the red triangles
				           	oseip.dup.store.reload();          // reload our datastore.
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