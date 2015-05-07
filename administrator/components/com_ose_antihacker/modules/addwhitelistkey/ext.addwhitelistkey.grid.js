Ext.ns('oseATH.oseATHViewWLKEY');
Ext.QuickTips.init();

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
						oseATH.oseATHViewWLKEY.store.reload();
					}
					else
					{
						oseATH.msg.setAlert(Joomla.JText._('Error'), msg.result);
						oseATH.oseATHViewWLKEY.store.reload();
					}
				}
			});
}

oseATH.oseATHViewWLKEY.msg = new Ext.App();

oseATH.oseATHViewWLKEY.cm = new Ext.grid.ColumnModel({
        defaults: {
            sortable: false
        },
        columns: [
            new Ext.grid.RowNumberer({header:'#'})
            ,{id: 'id', header: Joomla.JText._('ID'),  hidden:false, dataIndex: 'id', width: 20, sortable:true}
            ,{id: 'string', header: Joomla.JText._('Signature'),  hidden:false, dataIndex: 'string', sortable:true}
            ,{id: 'layer', header: Joomla.JText._('LAYER'),  hidden:false, dataIndex: 'layer', width: 20}
	    ],
	    sortInfo:{field: 'id', direction: "ASC"}
   });

oseATH.oseATHViewWLKEY.store = new Ext.data.GroupingStore({
		  proxy: new Ext.data.HttpProxy({
	            url: 'index.php?option=com_ose_antihacker',
	            method: 'POST'
	      }),
		  baseParams:{controller: "antihacker", task: "getWhitelistKeys",limit: 25},
		  reader: new Ext.data.JsonReader({
		    root: 'results',
		    totalProperty: 'total'
		  },[
		    {name: 'id', type: 'int', mapping: 'id'},
		    {name: 'string', type: 'string', mapping: 'string'},
		    {name: 'layer', type: 'string', mapping: 'layer'}
		  ]),
		  autoLoad:{}
});

oseATH.oseATHViewWLKEY.panel = new Ext.grid.GridPanel({
			id: 'oseATH.oseATHViewWLKEY'
			,cm: oseATH.oseATHViewWLKEY.cm
			,store: oseATH.oseATHViewWLKEY.store
			,viewConfig: {forceFit: true}
			,height: 500
			,border:false
			,region: 'west'
			,margins: {top:5, right:5, bottom:5, left:3}
			,tbar: new Ext.Toolbar({
				items: ['->',
					     {
					        	id: 'removeKey',
					            text: Joomla.JText._('Remove'),
					            xtype: 'button',
					            handler: function(){
					            	Ext.Msg.confirm(Joomla.JText._('Remove_Record_confirmation'), Joomla.JText._('Please_confirm_that_you_would_like_to_remove_the_selected_record'), function(btn, text){
										if (btn == 'yes'){
											var sel = oseATH.oseATHViewWLKEY.panel.getSelectionModel();
											var selections = sel.getSelections();
											athAjax('com_ose_antihacker','removeWhitelistKeys','antihacker', selections);
								      	}
					            	})
					            }
					       }
					    ]
			})
			,bbar:new Ext.PagingToolbar({
	    		pageSize: 25,
	    		store: oseATH.oseATHViewWLKEY.store,
	    		plugins: new Ext.ux.grid.limit({}),
	    		displayInfo: true,
	    		displayMsg: Joomla.JText._('Displaying_topics')+' {0} - {1} '+Joomla.JText._('of')+' {2}',
			    emptyMsg: Joomla.JText._("No_topics_to_display")

		    })
});