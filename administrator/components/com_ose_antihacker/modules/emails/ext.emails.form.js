Ext.ns('oseATH');
Ext.QuickTips.init();
oseATH.msg = new Ext.App();
oseATH.reader = new Ext.data.JsonReader({
	    root: 'results',
	    totalProperty: 'total',
	    fields:[
		    {name: 'id', type: 'int', mapping: 'id'},
		    {name: 'app', type: 'int', mapping: 'msc_id'},
		    {name: 'subject', type: 'string', mapping: 'subject'},
		    {name: 'body', type: 'string', mapping: 'body'},
		    {name: 'type', type: 'string', mapping: 'type'},
		    {name: 'params', type: 'string', mapping: 'params'}
	  	]
  	});

oseATH.form = new Ext.FormPanel({
		id:'ose-templates-form'
		,formId:'ose-templates-form'
		,region: 'center'
		,border: false
        ,labelWidth: 80
        ,reader: oseATH.reader
 		,height: 550
 		,width: 750
 		,bodyStyle: 'padding: 5px'
        ,items: [
        {
        	ref:'id',
            xtype:'hidden',
            name: 'id',
            value:''
        },{
        	ref:'type',
        	xtype: 'combo',
        	width: 400,
        	fieldLabel: 'Type',
        	hiddenName: 'type',
        	typeAhead: true,
		    triggerAction: 'all',
		    lazyRender:true,
		    mode: 'local',
		    store: new Ext.data.ArrayStore({
		        id: 0,
		        fields: [
		            'myId',
		            'displayText'
		        ],
		        data: [
		        	['blacklisted', "Alert for blacklisted entries"],
		        	['filtered', "Alert for filtered entries"],
                    ['403blocked', 'Alert for 403 blocked entries']
		        ]
		    }),
		    valueField: 'myId',
		    displayField: 'displayText',
        	listeners: {
            	select: function(combo)	{
            		oseATH.Paramsstore.reload({
	            		params:{type: combo.getValue()}
	            	});
            	}
            }
        },{
            xtype:'textfield',
            fieldLabel: 'Subject',
            name: 'subject',
            width: 400
        },{
        	name:'body',
        	id:'body',
            xtype:'tinymce'
	        ,fieldLabel:'Description'
		    ,width: 640
            ,height:380
            ,tinymceSettings: {
		        theme: "advanced",
		        skin: 'o2k7',
		        plugins: "pagebreak,style,layer,table,advhr,advimage,advlink,emotions,iespell,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,noneditable,visualchars,nonbreaking,xhtmlxtras,template",
		        theme_advanced_buttons1: "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,styleselect,formatselect,fontselect,fontsizeselect",
		        theme_advanced_buttons2: "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,|,insertdate,inserttime,preview,|,forecolor,backcolor",
		        theme_advanced_buttons3: "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,print,|,ltr,rtl,|",
		        theme_advanced_buttons4: "insertlayer,moveforward,movebackward,absolute,|,styleprops,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,pagebreak",
		        theme_advanced_toolbar_location: "top",
		        theme_advanced_toolbar_align: "left",
		        theme_advanced_statusbar_location: "bottom",
		        theme_advanced_resizing: false,
		        extended_valid_elements: "a[name|href|target|title|onclick],img[style|class|src|border=0|alt|title|hspace|vspace|width|height|align|onmouseover|onmouseout|name],hr[class|width|size|noshade],font[face|size|color|style],span[class|align|style]"
		    }
        }],
        buttons: [{
            text: 'Save',
            handler: function(){
                oseATH.form.getForm().submit({
                	clientValidation: true,
					url : 'index.php?option=com_ose_antihacker',
					method: 'post',
					params:{controller: 'emails', task:'save', body: oseATH.form.getForm().findField('body').getValue()},
					success: function(form, action){
						var msg = action.result;
						oseATH.msg.setAlert(msg.title,msg.content);
						ose.Emails.grid.getStore().reload();
						ose.Emails.grid.getView().refresh();
					},
					failure: function(form, action){
						var msg = action.result;
						oseATH.msg.setAlert(msg.title,msg.content);
					}
				});
            }
        }]
    });

oseATH.Paramscm = new Ext.grid.ColumnModel({
		defaults: {
            sortable: true
            ,width: 120
        },
        columns: [
		    {id: 'key', header: 'Variable', dataIndex: 'key', sortable: true, width: 80}
		    ,{id: 'value', header: 'Description', dataIndex: 'value', sortable: true, width: 150}
	    ]
	});

oseATH.Paramsstore = new Ext.data.Store({
		proxy: new Ext.data.HttpProxy({
    		url: 'index.php?option=com_ose_antihacker'
    		,method: 'POST'
		})
		,baseParams:{
				controller: "emails",
				task: "getTemplateParams",
				limit: 20
		}
		,reader: new Ext.data.JsonReader({
			root: 'results',
			totalProperty: 'total'
		},[
    		{name: 'key', type: 'string', mapping: 'key'},
    		{name: 'value', type: 'string', mapping: 'value'},
  		])
		,sortInfo:{field: 'key', direction: "ASC"}
		,listeners: {
			beforeload: function(s){
			}
		}
		,autoLoad:{}
});

oseATH.Paramsgrid = new Ext.grid.GridPanel({
		height: 400
		,width: 240
		,region: 'east'
		,autoScroll: true
		,store: oseATH.Paramsstore
		,cm: oseATH.Paramscm
		,sm:new Ext.grid.RowSelectionModel({singleSelect:true})
});

oseATH.panel = new Ext.Panel({
		layout: 'border'
		,border: false
		,height: 550
		,items:[
			oseATH.form,
			oseATH.Paramsgrid
		]
});