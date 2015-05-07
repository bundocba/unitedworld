/*********************************************************************************************************/
/**
 * inserthtml plugin for CKEditor 3.x (Author: Lajox ; Email: lajox@19www.com)
 * version:	 1.0
 * Released: On 2009-12-11
 * Download: http://code.google.com/p/lajox
 * Licence: GNU General Public License
 */
/*********************************************************************************************************/

CKEDITOR.plugins.add('inserthtml',   
  {    
    requires: ['dialog'],
	lang : ['en'],
	beforeInit: function (editor) {
		if (typeof CodeMirror == 'undefined') {
			var head = CKEDITOR.document.getHead();
			head.append(CKEDITOR.document.createElement('script', {
				attributes: {
					type: 'text/javascript',
					src: this.path + 'js/codemirror.js'
				}
			}));
		}
		CKEDITOR.document.appendStyleText('.CodeMirror-line-numbers { background-color:#EEEEEE; font-family:monospace;font-size:10pt !important;padding:4px 0.3em 4px 0 !important;width:2.2em !important;}');
		CKEDITOR.document.appendStyleText('.CodeMirror-line-numbers div { color:#AAAAAA !important;text-align:right !important;}');
		
		
		if(CKEDITOR.env.ie7Compat|| CKEDITOR.env.ie6Compat)
		{
				CKEDITOR.document.appendStyleText('.cke_dialog_ui_input_textarea { position:fixed !important;width:350px !important;1px solid #A0A0A0 !important} ');
		}
		
		CKEDITOR.tools.extend(CKEDITOR.dom.node.prototype,
		{
			getAscendant :  function( name, includeSelf )
			{
				var $ = this.$;

				if ( !includeSelf )
					$ = $.parentNode;

				while ( $ )
				{
									
					if($.nodeName && $.nodeName.toLowerCase() == 'body')
						return null;
					
					if ( $.nodeName && ($.nodeName.toLowerCase() == name || $.nodeName.toLowerCase() == name.replace(/^ck:/,''))  )
						return new CKEDITOR.dom.node( $ );

					$ = $.parentNode;
				}
				return null;
			}
		},true);
	
	},
    init:function(a) { 
	var b="inserthtml";
	var c=a.addCommand(b,new CKEDITOR.dialogCommand(b));
		c.modes={wysiwyg:1,source:0};
		c.canUndo=false;
	this._ = {};
	
	a.ui.addButton("Inserthtml",{
					label:a.lang.inserthtml.title,
					command:b,
					icon:this.path+"html_add.png"
	});
	
	
	if ( a.addMenuItems )
			{
				a.addMenuItems(
				{
					inserthtml :
					{
						label : a.lang.inserthtml.title,
						command : b,
						group : 'div',
						order : 1
					}
				} );
			}
	
	if (a.contextMenu) {
		a.contextMenu.addListener(function (element, selection) {
		
			if ( !element)
				return null;
			
			var selection = a.getSelection();
						
			var range = selection.getRanges( true )[ 0 ];
			range.shrink( CKEDITOR.SHRINK_TEXT );
			var root = range.getCommonAncestor();
			
			var element = a.plugins.inserthtml._.selectedElement = root.getAscendant( 'ck:html', true );
			
			if (a.plugins.inserthtml._.selectedElement)
			{
				return {
						inserthtml : CKEDITOR.TRISTATE_OFF
				};
			}
		
			return null;
		});
		
		a.on( 'contentDom', function()
		{
			
			a.document.on( 'click', function( evt )
			{
				
				var selection = a.getSelection();
				var range = selection.getRanges( true )[ 0 ];
				range.shrink( CKEDITOR.SHRINK_TEXT );
				var root = range.getCommonAncestor();
				
				a.plugins.inserthtml._.selectedElement = root.getAscendant( 'ck:html', true );
			});
		});
		
	}
	
	CKEDITOR.dialog.add(b,this.path+"dialogs/inserthtml.js")
	},
	afterInit: function (editor) {
            
			var dataProcessor = editor.dataProcessor,
                dataFilter = dataProcessor && dataProcessor.dataFilter,
                htmlFilter = dataProcessor && dataProcessor.htmlFilter;
            if (dataFilter) {
                dataFilter.addRules({
                  
                   elements: {
                        'ck:html': function (element) {
				             element.type = CKEDITOR.NODE_ELEMENT;
					        return element;
                        }
                    },
				    comment: function (data) {
                        var content = decodeURIComponent(data);
			            if (content.test(/inserthtml\sbegin/i)) {
						    return new CKEDITOR.htmlParser.cdata('<ck:html><!--{cke_protected}{C}'+encodeURIComponent('<!--'+content+'-->')+'-->');
                        }
						 if (content.test(/inserthtml\send/i)) {
							
                            return new CKEDITOR.htmlParser.cdata( '<!--{cke_protected}{C}'+encodeURIComponent('<!--'+content+'-->')+'--></ck:html>');
                        }
                        return data;
                    }
                });
            }
            if (htmlFilter) {
                htmlFilter.addRules({
                    
					elementNames :
					[
						// Remove the "ck:" namespace prefix.
						[ ( /^ck:/ ), '' ]
					],
					elements: {
                        html : function (element) {
                            delete element.name;
                            return element;
                        }
                    }
			    });
            }
			
        }
});