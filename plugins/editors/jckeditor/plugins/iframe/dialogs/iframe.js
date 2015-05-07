/*
Copyright (c) 2003-2010, CKSource - Frederico Knabben. All rights reserved.
For licensing, see LICENSE.html or http://ckeditor.com/license
*/
(function()
{
	
	var checkboxValues = {
		scrolling: { 1: 'yes', 0: 'no' },
		frameborder: { 1: '1', 0: '0' }
	}
	

	
	function loadValue( iframeNode )
	{
		var isCheckbox = ( this instanceof CKEDITOR.ui.dialog.checkbox );
		if ( iframeNode.getAttribute( this.id ))
		{
			value = iframeNode.getAttribute( this.id );
			if ( isCheckbox ) {
				this.setValue( checkboxValues[this.id][1] == value.toLowerCase() );
			} else
				this.setValue( value );
			return;
		}
	}

	function commitValue( iframeNode )
	{
		var isRemove = ( this.getValue() === '' ),
		isCheckbox = ( this instanceof CKEDITOR.ui.dialog.checkbox );
		value = this.getValue();
		if ( isRemove)
			iframeNode.removeAttribute( this.id );
		else if(isCheckbox)
			iframeNode.setAttribute( this.id, checkboxValues[this.id][Number(value)] );
		else
			iframeNode.setAttribute( this.id, value );
	}

	CKEDITOR.dialog.add( 'iframe', function( editor )
	{
		return {
			title : editor.lang.iframe.title,
			minWidth : 300,
			minHeight : 260,
			onShow : function()
			{
				// Clear previously saved elements.
				this.fakeImage = this.iframeNode = null;
				previewPreloader = new CKEDITOR.dom.element( 'embeded', editor.document );

				// Try to detect any embed or object tag that has Flash parameters.
				var fakeImage = this.getSelectedElement();
				if ( fakeImage && fakeImage.getAttribute( '_cke_real_element_type' ) && fakeImage.getAttribute( '_cke_real_element_type' ) == 'iframe' )
				{
					this.fakeImage = fakeImage;

					var realElement = editor.restoreRealElement( fakeImage ),
						iframeNode = null;
						iframeNode = realElement;

					this.iframeNode = iframeNode;

					this.setupContent( iframeNode, fakeImage );
				}
			},
			onOk : function()
			{
				// If there's no selected object or embed, create one. Otherwise, reuse the
				// selected object and embed nodes.
				var iframeNode = null;
				if ( !this.fakeImage )
				{
					iframeNode = CKEDITOR.dom.element.createFromHtml( '<cke:iframe></cke:iframe>', editor.document );
				}
				else
				{
					iframeNode = this.iframeNode;
				}

				// A subset of the specified attributes/styles
				// should also be applied on the fake element to
				// have better visual effect. (#5240)
				var extraStyles = {}, extraAttributes = {};
				this.commitContent( iframeNode, extraStyles, extraAttributes );
        
                // Refresh the fake image.
				var newFakeImage = editor.createFakeElement( iframeNode, 'cke_iframe', 'iframe', true );
				newFakeImage.setAttributes( extraAttributes );
				newFakeImage.setStyles( extraStyles );
				if ( this.fakeImage )
				{
					newFakeImage.replace( this.fakeImage );
					editor.getSelection().selectElement( newFakeImage );
				}
				else
					editor.insertElement( newFakeImage );
			},

			onHide : function()
			{
				if ( this.preview )
					this.preview.setHtml('');
			},

			contents : [
				{
					id : 'info',
					label : editor.lang.common.generalTab,
					accessKey : 'I',
					elements :
					[
						{
							type : 'vbox',
							padding : 0,
							children :
							[
								{
									id : 'src',
									type : 'text',
									label : editor.lang.common.url,
									required : true,
									validate : CKEDITOR.dialog.validate.notEmpty( editor.lang.iframe.validateSrc ),
									setup : loadValue,
									commit : commitValue,
									onLoad : function()
									{
										//Consider a preview or 404 verification here?
									}
								}
							]
						},
						{
							type : 'hbox',
							widths : [ '25%', '25%' ],
							children :
							[
								{
									type : 'text',
									id : 'width',
									style : 'width:80px',
									labelLayout : 'vertical',
									label : editor.lang.iframe.width,
									validate : CKEDITOR.dialog.validate.regex(/(^\d*%?$)/ ,editor.lang.iframe.validateWidth ),
									setup : function( iframeNode, fakeImage )
									{
										loadValue.apply( this, arguments );
										if ( fakeImage )
										{
											/*
											var fakeImageWidth = parseInt( fakeImage.$.style.width, 10 );
											
											if ( !isNaN( fakeImageWidth ) )
											{
												
												this.setValue( fakeImageWidth );
												
											}
											*/
											this.setValue( fakeImage.$.style.width.replace('px','') );
										}
									},
									commit : function( iframeNode, extraStyles )
									{
										commitValue.apply( this, arguments );
		                                if ( this.getValue() )
										    extraStyles.width =  CKEDITOR.tools.cssLength(this.getValue());    
									}
								},
								{
									type : 'text',
									id : 'height',
									style : 'width:80px',
									label : editor.lang.iframe.height,
									labelLayout : 'vertical',
									validate : CKEDITOR.dialog.validate.regex(/(^\d*%?$)/ , editor.lang.iframe.validateHeight ),
									setup : function( iframeNode, fakeImage )
									{
										loadValue.apply( this, arguments );
										if ( fakeImage )
										{
											/*var fakeImageHeight = parseInt( fakeImage.$.style.height, 10 );
											if ( !isNaN( fakeImageHeight ) )
												this.setValue( fakeImageHeight );
											*/
											this.setValue( fakeImage.$.style.height.replace('px','') );
										}
									},
									commit : function( iframeNode, extraStyles )
									{
										commitValue.apply( this, arguments );
			                            if ( this.getValue() )
											extraStyles.height =  CKEDITOR.tools.cssLength(this.getValue());
									}
								}
							]
						},
						{
							type : 'hbox',
							widths : [ '50%', '50%' ],
							children :
							[
								{
									type : 'checkbox',
									id : 'scrolling',
									label : editor.lang.iframe.scrolling,
									setup : loadValue,
									commit : commitValue
								},
								{
									type : 'checkbox',
									id : 'frameborder',
									label : editor.lang.iframe.border,
									setup : loadValue,
									commit : commitValue
								}
							]
						},
						{
							type : 'hbox',
							widths : [ '45%', '55%' ],
							children :
							[
								{
									type : 'text',
									id : 'id',
									label : editor.lang.common.id,
									setup : loadValue,
									commit : commitValue
								},
								{
									type : 'text',
									id : 'title',
									label : editor.lang.common.advisoryTitle,
									setup : loadValue,
									commit : commitValue
								}
							]
						},
						{
							type : 'hbox',
							widths : [ '45%', '55%' ],
							children :
							[
								{
									type : 'text',
									id : 'class',
									label : editor.lang.common.cssClass,
									setup : loadValue,
									commit : commitValue
								}
							]
						},
						{
							type : 'text',
							id : 'style',
							label : editor.lang.common.cssStyle,
							setup : loadValue,
							commit : commitValue
						}
					]
				}
			]
		};
	} );
})();