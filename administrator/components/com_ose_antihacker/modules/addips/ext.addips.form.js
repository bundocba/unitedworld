Ext.ns('oseATH.oseATHReportBL');
Ext.QuickTips.init();

function fnValidateIPAddress(ipaddr) {
    ipaddr = ipaddr.replace( /\s/g, "")
    var re = /^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}$/;
    if (re.test(ipaddr)) {
        //split into units with dots "."
        var parts = ipaddr.split(".");
        //if the first unit/quadrant of the IP is zero
        if (parseInt(parseFloat(parts[0])) == 0) {
            return false;
        }
        //if the fourth unit/quadrant of the IP is zero
        if (parseInt(parseFloat(parts[3])) == 0) {
            return false;
        }
        //if any part is greater than 255
        for (var i=0; i<parts.length; i++) {
            if (parseInt(parseFloat(parts[i])) > 255){
                return false;
            }
        }
        return true;
    } else {
        alert ('The IP '+ ipaddr + ' is not a valid IP address');
    	return false;
    }
}

function validateIPs(ip_start, ip_end)
{
    	if (fnValidateIPAddress(ip_start)==true && fnValidateIPAddress(ip_end)==true)
    	{
    		return true;
    	}
    	else
    	{
    		return false; 
    	}
}

oseATH.oseATHReportBL.msg = new Ext.App();
oseATH.oseATHReportBL.form = new Ext.form.FormPanel({
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
				
				var ip_startCmp = Ext.getCmp('ip_start');
				var ip_endCmp = Ext.getCmp('ip_end');
				var iptype = Ext.getCmp('iptype');
				
				if (iptype.getValue() == 'ips')
				{
					if (fnValidateIPAddress(ip_startCmp.getValue()) == false && fnValidateIPAddress(ip_endCmp.getValue()) == false)
					{
						return false;
					}
				}
				else
				{
					if (fnValidateIPAddress(ip_startCmp.getValue()) == false)
					{
						return false;
					}
				}
				
				oseATH.oseATHReportBL.form.getForm().submit({
		        	clientValidation: true,
					url : 'index.php?option=com_ose_antihacker',
					method: 'post',
					params:{controller:"antihacker", task:'addips'},
					waitMsg: 'Please wait, this will take a few seconds ...',
					success: function(form, action){
						var msg = action.result;
						oseATH.oseATHReportBL.msg.setAlert(msg.status,msg.result);
						if (oseATH.oseATHReportBL.addWin)
						{
							oseATH.oseATHReportBL.addWin.close();
						}
						oseATHReportBL.store.reload();
					},
					failure:function(form, action){
						var msg = action.result;
						oseATH.oseATHReportBL.msg.setAlert(msg.status,msg.result);
						oseATHReportBL.store.reload();
					} 
					
				});
			}
		}]
	    ,reader: new Ext.data.JsonReader(
			{root: 'results',totalProperty: 'total'},
			[
				{name: 'id', type: 'int', mapping: 'id'}
			    ,{name: 'title', type: 'string', mapping: 'title'}
			    ,{name: 'iptype', type: 'string', mapping: 'iptype'}
			    ,{name: 'ip_start', type: 'string', mapping: 'ip_start'}
			    ,{name: 'ip_end', type: 'string', mapping: 'ip_end'}
			    ,{name: 'status', type: 'int', mapping: 'status'}
			]
		)
    	,items:[{
	      		id:'id'
		       	,xtype:'hidden'
		        ,name: 'id'
	        },
	        {
	        	id:'insertype'
			    ,xtype:'hidden'
			    ,name: 'insertype'
			    ,value: 'man'
	        },{
		       	itemId:'title'
		       	,xtype:'textfield'
		      	,fieldLabel:'IP Rule'
		        ,name: 'title'
		        ,allowBlank:false
		        ,msgTarget: 'side'
		        ,width: 350
	        },{
			   	xtype:'combo'
				,fieldLabel:'Add IP'
				,hiddenName: 'iptype'
				,id: 'iptype'
				,typeAhead: true
				,triggerAction: 'all'
				,lazyRender:false
				,width: 300
				,mode: 'local'
				,store: new Ext.data.ArrayStore({
					    fields: [
					       'value',
					       'text'
					    ],
					    data: [
					      	['ip', 'IP'],
					      	['ips', "IP Range"]
						      ]
					})
					,valueField: 'value'
					,displayField: 'text'
					,listeners:{
						select:{
							fn:function(combo, value) {
								var ip_endCmp = Ext.getCmp('ip_end');
								var comValue = combo.getValue(); 
								if (comValue == 'ip')
								{
									ip_endCmp.setDisabled(true);
								}
								else
								{
									ip_endCmp.setDisabled(false);
								}	
						    }
						}
					}						

			},{
		       	itemId:'ip_start'
		       	,xtype:'textfield'
		      	,fieldLabel:'Start IP'
		        ,name: 'ip_start'
		        ,id: 'ip_start'
		        ,allowBlank: true
		        ,msgTarget: 'side'
		        ,width: 350
		        ,listeners:{
					change:{
						fn:function(field, value) {
							var fieldValue = field.getValue();
							if (fnValidateIPAddress(fieldValue)==false)
							{
								field.setValue(''); 
							}
					    }
					}
				}
	        },{
		       	itemId:'ip_end'
		       	,xtype:'textfield'
		      	,fieldLabel:'End IP'
		        ,name: 'ip_end'
		        ,id: 'ip_end'
		        ,allowBlank:true
		        ,msgTarget: 'side'
		        ,width: 350
		        ,listeners:{
					change:{
						fn:function(field, value) {
							var fieldValue = field.getValue();
							if (fnValidateIPAddress(fieldValue)==false)
							{
								field.setValue(''); 
							}
					    }
					}
				}
	        },
	        {
		       	xtype:'combo'
			   	,fieldLabel:'Status'
			    ,hiddenName: 'status'
			    ,typeAhead: true
			    ,triggerAction: 'all'
				,lazyRender:false
				,width: 300
				,mode: 'local'
				,store: new Ext.data.ArrayStore({
				    fields: [
				       'value',
				       'text'
				    ],
				    data: [
					      	['1', 'Blacklisted'],
					      	['2', "Monitored"],
					      	['3', "Whitelisted"]
					      ]
				})
				,valueField: 'value'
				,displayField: 'text'
		}]
	});