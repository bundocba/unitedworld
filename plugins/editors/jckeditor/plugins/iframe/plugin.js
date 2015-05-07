/*
Iframe plugin for CKEditor
Charlie Croom - 24.06.2010
*/

/**
 * @file IFrame plugin
 */
(function()
{
	var numberRegex = /^\d+(?:\.\d+)?$/;

	function cssifyLength( length )
	{
		if ( numberRegex.test( length ) )
			return length + 'px';
		return length;
	}

	function isIframe( element )
	{
		var attributes = element.attributes;

		return ( attributes.src != '' );
	}

	function createFakeElement( editor, realElement )
	{
		var fakeElement = editor.createFakeParserElement( realElement, 'cke_iframe', 'iframe', true ),
			fakeStyle = fakeElement.attributes.style || '';

		var width = realElement.attributes.width,
			height = realElement.attributes.height;


		if ( typeof width != 'undefined' )
			fakeStyle = fakeElement.attributes.style = fakeStyle + 'width:' + cssifyLength( width ) + ';';



		if ( typeof height != 'undefined' )
			fakeStyle = fakeElement.attributes.style = fakeStyle + 'height:' + cssifyLength( height ) + ';';

		return fakeElement;
		
		
		
	}

	CKEDITOR.plugins.add( 'iframe',
	{
		requires : [ 'dialog' ],
		lang : ['en', 'de'],
		init : function( editor )
		{
			var pluginName = 'iframe';

			// Register the dialog.
			CKEDITOR.dialog.add( pluginName, this.path + 'dialogs/iframe.js' );

			//Add the CSS for our fake element
			editor.addCss(
				'img.cke_iframe' +
				'{' +
					'background-image: url(' + CKEDITOR.getUrl( this.path + 'images/placeholder.png' ) + ');' +
					'background-position: center center;' +
					'background-repeat: no-repeat;' +
					'border: 1px solid #a9a9a9;' +
					'width: 80px;' +
					'height: 80px;' +
				'}'
			);

			// Register the command.
			editor.addCommand( pluginName, new CKEDITOR.dialogCommand( pluginName ) );

			// Register the toolbar button.
			editor.ui.addButton( 'Iframe',
				{
					label : editor.lang.iframe.hover,
					command : pluginName,
					icon: CKEDITOR.plugins.getPath('iframe') + 'images/icon.png'
				});

			editor.on( 'doubleclick', function( evt )
				{
					var element = evt.data.element;

					if ( element.is( 'img' ) && element.getAttribute( '_cke_real_element_type' ) == 'iframe' )
						evt.data.dialog = 'iframe';
				});

			//Define the menu item
			if(editor.addMenuItems)
			{
			    editor.addMenuItems(  //have to add menu item first
			        {
			            iframe:  //name of the menu item
			            {
			                label: editor.lang.iframe.contextLabel,
			                command: 'iframe',
			                group: 'image'  //have to be added in config
			            }
			        });
			}

			// If the "contextmenu" plugin is loaded, register the listeners.
			if ( editor.contextMenu )
			{
				editor.contextMenu.addListener( function( element, selection )
					{
						if ( element && element.is( 'img' ) && element.getAttribute( '_cke_real_element_type' ) == 'iframe' )
							return { iframe : CKEDITOR.TRISTATE_OFF };
					});
			}
		},
		//Add a filter for when we switch from source to HTML mode...this will preserve the "Fake" iframe element
		//Note the 4 at the end, the filter in the flash plugin has a vlue of 5, so we only need a 4 to slip in before
		afterInit : function( editor )
		{
			var dataProcessor = editor.dataProcessor,
				dataFilter = dataProcessor && dataProcessor.dataFilter,
				htmlFilter = dataProcessor && dataProcessor.htmlFilter;

			if ( dataFilter )
			{
				dataFilter.addRules(
					{
						elements :
						{
							iframe : function( element )
							{
								return createFakeElement( editor, element );
							}
						}
					},
					5);
			}
			
			if (htmlFilter)
			{
			
				htmlFilter.addRules( 
				{					
					elements :
					{
						img : function( element )
						{
							var attributes = element.attributes,
								realHtml = attributes && attributes._cke_realelement,
								realFragment = realHtml && new CKEDITOR.htmlParser.fragment.fromHtml( decodeURIComponent( realHtml ) ),
								realElement = realFragment && realFragment.children[ 0 ];
			
							// If we have width/height in the element, we must move it into
							// the real element.
									
							if ( realElement && element.attributes._cke_resizable && element.attributes._cke_real_element_type == 'iframe' )
							{
								var style = element.attributes.style;
								
				
								if ( style )
								{
									// Get the width from the style.
									var match = /(?:^|\s)width\s*:\s*(\d+%?)/i.exec( style ),
										width = match && match[1];
			
									// Get the height from the style.
									match = /(?:^|\s)height\s*:\s*(\d+%?)/i.exec( style );
									var height = match && match[1];
			
									if ( width )
										realElement.attributes.width = width;
			
									if ( height )
										realElement.attributes.height = height;
								}
							}
			
							return realElement;
						}
					}					
					
				});
			
			}
			
			
		},
		requires : [ 'fakeobjects' ]
	});
})();