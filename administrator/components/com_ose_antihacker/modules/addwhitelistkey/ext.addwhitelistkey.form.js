Ext.ns('oseATH.oseATHAddWLKEY');
Ext.QuickTips.init();

oseATH.oseATHAddWLKEY.msg = new Ext.App();
oseATH.oseATHAddWLKEY.form = new Ext.form.FormPanel({
		bodyStyle: 'padding: 10px; padding-left: 20px'
		,defaults: [{bodyStyle: 'padding: 10px'}]
		,autoScroll: true
		,autoWidth: true
	    ,border: false
	    ,labelAlign: 'left'
	    ,labelWidth: 150
	    ,buttons: [{
			text: 'Save'
			,handler: function(){
				oseATH.oseATHAddWLKEY.form.getForm().submit({
		        	clientValidation: true,
					url : 'index.php?option=com_ose_antihacker',
					method: 'post',
					params:{controller:"antihacker", task:'addwhitelistkey'},
					waitMsg: 'Please wait, this will take a few seconds ...',
					success: function(form, action){
						var msg = action.result;
						oseATH.oseATHAddWLKEY.msg.setAlert(msg.status,msg.result);
						if (oseATH.oseATHAddWLKEY.addWin)
						{
							oseATH.oseATHAddWLKEY.addWin.close();
						}
						oseATH.oseATHReportL1.store.reload();
					},
					failure:function(form, action){
						var msg = action.result;
						oseATH.oseATHAddWLKEY.msg.setAlert(msg.status,msg.result);
						oseATH.oseATHReportL1.store.reload();
					} 
					
				});
			}
		}]
    	,items:[{
		       	itemId:'key'
		       	,xtype:'textfield'
		      	,fieldLabel:'Whitelist Key'
		        ,name: 'key'
		        ,allowBlank:false
		        ,msgTarget: 'side'
		        ,width: 350
	        }]
	});