/*********************************************************************************************************/
/**
 * inserthtml plugin for CKEditor 3.x (Author: Lajox ; Email: lajox@19www.com)
 * version:	 1.0
 * Released: On 2009-12-11
 * Download: http://code.google.com/p/lajox
 */
/*********************************************************************************************************/

CKEDITOR.dialog.add("inserthtml",function(e){	

	var codemirrorInit;

	return{
		title:e.lang.inserthtml.title,
		resizable : CKEDITOR.DIALOG_RESIZE_BOTH,
		minWidth:380,
		minHeight:220,
		onShow:function(){ 
		
			var element = false;
							
			this.element = e.plugins.inserthtml._.selectedElement;
	
			if (this.element) {
                var data = this.element.getHtml();
				data = data.replace( /<!--\{cke_protected\}\{C\}([\s\S]+?)-->/g, function( match, html )
						{
							return decodeURIComponent( html );
						});	
					
				element = CKEDITOR.dom.element.createFromHtml('<ck:html></ck:html>', e.document);
		        element.setHtml(data.replace(/<!--inserthtml\sbegin-->/i, '').replace(/<!--inserthtml\send-->/i,''));
				
            }
			
			this.setupContent(element);
		    var thisPath = e.plugins.inserthtml.path;
            var textarea = this.getContentElement('info', 'insertcode_area').getInputElement();
            var holderHeight = textarea.$.clientHeight + 'px';
            var holderWidth = textarea.$.clientWidth + 'px';
            codemirrorInit = CodeMirror.fromTextArea(textarea.$, {
                stylesheet: thisPath + 'css/colors.css',
                path: thisPath + 'js/',
                parserfile: 'parsemixed.js',
                passDelay: 300,
                passTime: 35,
                continuousScanning: 1000,
                undoDepth: 50,
                height: holderHeight,
                textWrapping: false,
                width: holderWidth,
                lineNumbers: true,
                enterMode: 'flat'
            });
			
			if(CKEDITOR.env.ie7Compat|| CKEDITOR.env.ie6Compat)
			{
					CKEDITOR.document.appendStyleText('.CodeMirror-wrapping iframe { width: 316px !important}');
			}
		
		},
		onLoad:function(){ 
				dialog = this; 
				this.setupContent();
		},
		onOk:function(){
			this.setValueOf('info', 'insertcode_area', codemirrorInit.getCode());
			var sInsert= this.getValueOf('info','insertcode_area');
			 if (this.element) {
                sInsert =  '<!--{cke_protected}{C}' + encodeURIComponent('<!--inserthtml begin-->') +'-->' +  sInsert + '<!--{cke_protected}{C}'+  encodeURIComponent('<!--inserthtml end-->')+'-->';   
                var newElement = CKEDITOR.dom.element.createFromHtml('<ck:html>'+sInsert+'</ck:html>', e.document);
				newElement.replace(this.element);
                e.getSelection().selectElement(newElement);
            }
            else {
				if ( sInsert.length > 0 ) 
				sInsert =  '<!--inserthtml begin-->' +  sInsert + '<!--inserthtml end-->';  
				e.insertHtml(sInsert); 
			}
			;
		},
		onHide : function()
		{
			codemirrorInit.toTextArea(); // destroy codemirror instance
			delete this.element
		},
		contents:[
			{	id:"info",
				name:'info',
				label:e.lang.inserthtml.commonTab,
				elements:[{
				 type:'vbox',
				 padding:0,
				 children:[
				  {type:'html',
				  html:'<span style="line-height: 20px;">'+e.lang.inserthtml.HelpInfo+'</span>'
				  },
				  { type:'textarea',
				    id:'insertcode_area',
					label:'',
					cols:60,
					rows:11,
					setup: function (element) {
						
						if (element) {
							var data = element.getHtml();
							this.setValue(data);
						}
					
                    }
				  }]
				}]
			}
		]
	};
});