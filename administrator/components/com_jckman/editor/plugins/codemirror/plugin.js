
CKEDITOR.plugins.add( 'codemirror', {
        requires : [ 'sourcearea' ],
        /**
         * This's a command-less plugin, auto loaded as soon as switch to 'source' mode  
         * and 'textarea' plugin is activeated.
         * @param {Object} editor
         */

		 beforeInit : function( editor )
		{
			
			var head = CKEDITOR.document.getHead();

			head.append(
					CKEDITOR.document.createElement( 'script',
						{
							attributes :
								{
									type : 'text/javascript',
									src : this.path + 'js/codemirror.js'
								}
						})
					);
			
		CKEDITOR.document.appendStyleText('.CodeMirror-line-numbers { background-color:#EEEEEE; font-family:monospace;font-size:10pt !important;padding:4px 0.3em 4px 0 !important;width:2.2em !important;}');
			
		CKEDITOR.document.appendStyleText('.CodeMirror-line-numbers div { color:#AAAAAA !important;text-align:right !important;}');
			
		},
 
        init : function( editor ) {
                var thisPath = this.path;
                                editor.on( 'mode', function() {
                                        if ( editor.mode == 'source' ) {
                                                var sourceAreaElement = editor.textarea,
                                                        holderElement = sourceAreaElement.getParent();
                                                var holderHeight = holderElement.$.clientHeight + 'px';
												var holderWidth = holderElement.$.clientWidth + 'px';
                                                /* http://codemirror.net/manual.html */
					
											    var codemirrorInit =
                                                CodeMirror.fromTextArea(
                                                        editor.textarea.$, {
                                                                stylesheet: thisPath + 'css/colors.css',
                                                                path: thisPath + 'js/',
                                                                parserfile: 'parsemixed.js',
                                                                passDelay: 300,
                                                                passTime: 35,
                                                                continuousScanning: 1000, /* Numbers lower than this suck megabytes of memory very quickly out of firefox */
                                                                undoDepth:     editor.config.codemirrorUndoDepth,
                                                                height: editor.config.height || holderHeight, /* Adapt to holder height */
                                                                textWrapping:  editor.config.codemirrorTextWrapping,
																width: holderWidth,
                                                                lineNumbers: editor.config.codemirrorLineNumbers,
                                                                enterMode: 'flat'
                                                        }
                                                );
                                                    // Commit source data back into 'source' mode.
                                                    editor.on( 'beforeCommandExec', function( e ){
                                                      // Listen to this event once.
                                                            e.removeListener();
                                                            editor.textarea.setValue( codemirrorInit.getCode() );
                                                            editor.fire( 'dataReady' );
                                                           
                                                            /*editor._.modes[ editor.mode ].loadData(
                                                                    codemirror.getCode() );*/
                                                    } );
                                                   
                                                    CKEDITOR.plugins.mirrorSnapshotCmd = {
                                                            exec : function( editor ) {
                                                                    if ( editor.mode == 'source' ) {
                                                                            editor.textarea.setValue( codemirrorInit.getCode() );
                                                                            editor.fire( 'dataReady' );
                                                                    }
                                                            }
                                                    };
                                                    editor.addCommand( 'mirrorSnapshot', CKEDITOR.plugins.mirrorSnapshotCmd );
                                                    /* editor.execCommand('mirrorSnapshot'); */
                        }
                } );
                editor.on( 'instanceReady', function(e) {
                  e.removeListener();
                  if ( editor.mode == 'wysiwyg' ) {
                        var thisData = editor.getData().indexOf('<?php');
                        if (thisData !== -1) {
                                editor.execCommand('source');
                        };
                  }
                } );
        }

});	
	
	
	