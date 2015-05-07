CKEDITOR.plugins.add('responsiveimages',   
{    

     init:function(editor) 
	 {
		//Nothing to do	 
	 },
	  
	afterInit: function (editor) 
	{
            
			var dataProcessor = editor.dataProcessor,
                 htmlFilter = dataProcessor && dataProcessor.htmlFilter;
          
            if (htmlFilter)
			{
                htmlFilter.addRules(
				{
                    
					elements: 
					{
                        img : function (element) 
						{
							if(element.attributes.style)
							{
								var tempElement = CKEDITOR.dom.element.createFromHtml( '<div style="'+ element.attributes.style +'"></div>' );
								var cssHeight = tempElement.getStyle('height') ;
								var cssWidth = tempElement.getStyle('width') ;
								if(cssHeight) 
								{
									var heightRegExp = new RegExp('height\\s*?:\\s*?' + cssHeight+';?','i');
									element.attributes.style = element.attributes.style.replace(heightRegExp,'').replace(/(^\s+|\s+$)/g, '');
	
								}
								if(cssWidth) 
								{
									var widthRegExp = new RegExp('width\\s*?:\\s*?' + cssWidth+';?','i');
									element.attributes.style = element.attributes.style.replace(widthRegExp,'').replace(/(^\s+|\s+$)/g, '');	
								}
							}
							return element
                        }
                    }
			    });
            }
			
        }

});