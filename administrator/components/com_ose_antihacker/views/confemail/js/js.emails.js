Ext.ns('ose.Emails');
function ajaxAction(option, task, controller,selections)
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
						Ext.Msg.alert(msg.status, msg.result);
						ose.Emails.store.reload();
					}
					else
					{
						Ext.Msg.alert('Error', msg.result);
						ose.Emails.store.reload();
					}
				}
	});
  }

Ext.onReady(function(){
	ose.Emails.msg = new Ext.App();
	ose.Emails.cm = new Ext.grid.ColumnModel({
		defaults: {
            sortable: true
            ,width: 200
        },
        columns: [
	        new Ext.grid.RowNumberer({header:'#'})
		    ,{id: 'id', header: 'ID', dataIndex: 'id', hidden: true}
		    ,{id: 'subject', header: 'Subject', dataIndex: 'subject', sortable: true}
	    ]
	});
	ose.Emails.store = new Ext.data.Store({
		proxy: new Ext.data.HttpProxy({
    		url: 'index.php?option=com_ose_antihacker'
    		,method: 'POST'
		})
		,baseParams:{controller: "emails", task: "getEmails",limit: 20}
		,reader: new Ext.data.JsonReader({
			root: 'results',
			totalProperty: 'total'
		},[
			{name: 'id', type: 'int', mapping: 'id'},
    		{name: 'subject', type: 'string', mapping: 'subject'}
  		])

		,sortInfo:{field: 'id', direction: "ASC"}
		,listeners: {
			beforeload: function(s){
			}
		}
		,autoLoad:{}
	});
	ose.Emails.tbar = new Ext.Toolbar({
	    items: ['->',{
        	ref: 'editBtn',
            iconCls: 'icon-user-edit',
            text: 'Edit',
            disabled: true,
            handler: function(){
        		var node =ose.Emails.grid.getSelectionModel().getSelected();
        		var bannerType = node.data.type;
            	ose.Emails.editWin = new Ext.Window({
            			title: 'Edit email template'
            			,modal: true
            			,border: false
            			,autoHeight: true
            			,width: 1024
            			,autoLoad: {
            				url: 'index.php?option=com_ose_antihacker'
	            				,params:{controller: '', task: 'getMod', mod:'emails',type:'emails',name:'form'}
                				,scripts: true
                				,callback: function(el,success,response,opt)	{
                					ose.Emails.editWin.add(eval(oseATH.panel));
                					ose.Emails.editWin.doLayout();
                					oseATH.form.getForm().load({
            				           	url: 'index.php?option=com_ose_antihacker'
	            						,params:{controller: 'emails',task:'getEmail',id:node.id}
            				           	,success: function(form,action)	{
            				           	}
            				        });
                					oseATH.Paramsstore.reload({
	            						params:{id:node.id}
            				        });                					
                				}
            			}
            	});
            	ose.Emails.editWin.show().alignTo(Ext.getBody(),'t-t');
            }
        }]
	});
	ose.Emails.grid = new Ext.grid.GridPanel({
		height: 500
		,autoScroll: true
		,store: ose.Emails.store
		,cm:ose.Emails.cm
		,sm:new Ext.grid.RowSelectionModel({singleSelect:true})
		,viewConfig:{forceFit: true}
		,tbar: ose.Emails.tbar
     	,bbar:new Ext.PagingToolbar({
    		pageSize: 20
    		,store: ose.Emails.store
    		,plugins: new Ext.ux.grid.limit({})
    		,displayInfo: true
		    ,displayMsg: 'Displaying topics {0} - {1} of {2}'
		    ,emptyMsg: "No topics to display"
	    })
	});
	ose.Emails.panel = new Ext.Panel({
		border: false
		,width: Ext.get('ose-emails-list').getWidth()
		,items: [ose.Emails.grid]
		,renderTo: 'ose-emails-list'
	})
	ose.Emails.grid.getSelectionModel().on('selectionchange', function(sm){
		ose.Emails.tbar.editBtn.setDisabled(sm.getCount() != 1);
	});
});