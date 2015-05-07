
/*------------------------------------------------------------------------
# Copyright (C) 2005-2010 WebxSolution Ltd. All Rights Reserved.
# @license - GPLv2.0
# Author: WebxSolution Ltd
# Websites:  http://www.webxsolution.com
# Terms of Use: An extension that is derived from the JoomlaCK editor will only be allowed under the following conditions: http://joomlackeditor.com/terms-of-use
# ------------------------------------------------------------------------*/


/**
 * @file Preview plugin.
 */

(function()
{
	var imageManagerCmd =
	{
		modes : { wysiwyg:1, source:0 },

		exec : function( editor )
		{
			ImageManager_click(editor,null);
		},
		execAdhoc : function (editor,data)
		{
			ImageManager_StartManager(editor,data);	
		}
	};

	var pluginName = 'ImageManager';
	

	// Register a plugin named "ImageManager".
	CKEDITOR.plugins.add( pluginName,
	{
		
		lang : ['en'],
		
		init : function( editor )
		{
			
			editor.addCommand( pluginName, imageManagerCmd );
			editor.ui.addButton( 'ImageManager',
				{
					label : editor.lang.imageManager.toolbar,
					command : pluginName,
					icon : this.path + 'icon.gif'
				});
			
			// If the "menu" plugin is loaded, register the menu items.
			if ( editor.addMenuItems )
			{
				editor.addMenuItems(
					{
						ImageManager :
						{
							label : editor.lang.imageManager.menu,
							command : pluginName,
							icon : this.path + 'icon.gif',
							group : 'image',
							order: -1
						}
					});
			}
			
			// If the "contextmenu" plugin is loaded, register the listeners.
			if ( editor.contextMenu )
			{
				editor.contextMenu.addListener( function( element, selection )
					{
						if ( !element || !element.is( 'img' ) )
							return null;
	
						return { ImageManager : CKEDITOR.TRISTATE_OFF };
					});
			}
					
			
			CKEDITOR.tools.extend( CKEDITOR.config,
			{
				 _editor_lang : editor.langCode
			});
			
			CKEDITOR.tools.extend( CKEDITOR,
			{
				 _imgMan_Dialog : Dialog
			});

	}
	});
	
	
	    // Open the Placeholder dialog on double click.
    var ImageManager_doubleClick = function(img) {

		//if ( img.tagName == 'IMG' ) FCKCommands.GetCommand( 'ImageManager' ).Execute() ;
	}

	//FCK.RegisterDoubleClickHandler( ImageManager_doubleClick, 'IMG' ) ;	

	// starting ImageManager
	var ImageManager_click = function (editor, sender) {
		var wArgs = {};
		
		var sElm = editor.getSelection().getSelectedElement();
		
		if (sElm != null && sElm.getName().toLowerCase() == 'img') var im = sElm;  // is current cell a image ?			
	
		if (im) { // selected object is image 
			wArgs.f_url		= im.getAttribute('src') ? im.getAttribute('src') : '';
			wArgs.f_alt		= im.getAttribute('alt') ? im.getAttribute('alt') : '';
			wArgs.f_title 	= im.getAttribute('title') ? im.getAttribute('title') : '';
			wArgs.f_width 	= im.getStyle('width')  ? im.getStyle('width').replace(/px$/,'')  : im.getAttribute('width');
			wArgs.f_height 	= im.getStyle('height') ? im.getStyle('height').replace(/px$/,'') : im.getAttribute('height');
			wArgs.f_border 	= im.getAttribute('border') ? im.getAttribute('border') : '';
			if( im.getStyle('float'))
					wArgs.f_align = im.getStyle('float');
			else
				wArgs.f_align 	= im.getAttribute('align') ? im.getAttribute('align') : '';
			wArgs.f_className = im.getAttribute('className') ? im.getAttribute('className') : '';
			
			// (-1 when not set under gecko for some reason)
			if(im.getStyle('margin-right'))
				wArgs.f_horiz = im.getStyle('margin-right').replace(/px$/,'');
			else
				wArgs.f_horiz = (im.getAttribute('hspace') >= 0) ? im.getAttribute('hspace'): ''; 
				if(im.getStyle('margin-bottom'))
					wArgs.f_horiz = im.getStyle('margin-bottom').replace(/px$/,'');
			else	
				wArgs.f_vert = (im.getAttribute('vspace') >= 0) ? im.getAttribute('vspace') : '';			
		} else {
			wArgs = null;
		}
		//-------------------------------------------------------------------------
		var manager = new ImageManager();



		manager.insert(editor,wArgs);

	}

	//-------------------------------------------------------------------------
	var setAttrib = function (element, name, value, fixval) { // set element attributes
		if (!fixval && value != null) {
			var re = new RegExp('[^0-9%]', 'g');
			value = value.replace(re, '');
		}
		if (value != null && value != '') {
			element.setAttribute(name, value);
		} else {
			element.removeAttribute(name);
		}
	}
	
	/* IMAGE MANAGER OBJECT - A CROSS BETWEEN THE STANDALONE & HTMLAREA PLUGIN VERSIONS */
	var ImageManager = function() {};
	
	
	// Open up the plugin's dialog with manager or editor.
	ImageManager.prototype.insert = function(editor,outparam)
	{
		var lastSlashPosition,imgFileName,dir;
		
		var pluginPath = editor.plugins[pluginName].path;
		
		var manager = pluginPath + editor.config.imageManager_url;
		
		manager += (manager.indexOf("?") == -1 ? "?client=" : "&client=") +  editor.config.client;
		
		var func = function(param) {
				if (!param) return false; // user must have pressed cancel
				var sElm = editor.getSelection().getSelectedElement();
				
				 // new image// no image - create new image
				var im = editor.document.createElement( 'img' );
				
				var saved_url = param.f_url.replace(editor.config.baseHref,'');	
				// set image attributes	
				setAttrib(im, "_cke_saved_src", saved_url, true);			
				setAttrib(im, 'src', param.f_url, true);				
				setAttrib(im, 'alt', param.f_alt, true);
				setAttrib(im, 'title', param.f_title, true);
				setAttrib(im, 'align', param.f_align, true);
				setAttrib(im, 'border', param.f_border);
				setAttrib(im, 'hspace', param.f_horiz);
				setAttrib(im, 'vspace', param.f_vert);
				setAttrib(im, 'width', param.f_width);
				setAttrib(im, 'height', param.f_height);				
				setAttrib(im, 'className', param.f_className, true); 
				
				if(sElm != null && sElm.getName().toLowerCase() == 'img')
				{
					sElm.insertBeforeMe( im );
					sElm.remove( true );
				}
				else
				{
					editor.insertElement( im);	
				}
				return;
	
			}
		
		// show image editor
		if (editor.config.IM_directEdit)
		{
			// image selected?
			var sElm = editor.getSelection().getSelectedElement();
			if (sElm != null && sElm.getName().toLowerCase() == 'img')
			{
				// opening a dialog with the image editor - editor.php must receive the path to the image relative to your 'base_url' defined in 'config.inc.php'
				// for direct Editing, we assume that there are no subdirectories in 'base_url' so our path is just '/'
				lastSlashPosition = sElm.getAttribute('src').lastIndexOf('/') + 1;
				imgFileName = sElm.getAttribute('src').substring(lastSlashPosition);
				dir = sElm.getAttribute('src').substring(0,sElm.getAttribute('src').lastIndexOf('/'));
				lastSlashPosition = dir.lastIndexOf('/')
				dir = dir.substring(lastSlashPosition) + '/';
				var url = manager +  '&dir='+ dir +'&img=' + imgFileName;
				Dialog(url, func,editor, outparam);
			}
			// no image selected - stop
			else
			{
			
			Dialog(manager,func, editor, outparam);
			}
		}
		// show image manager
		else
		{
			Dialog(manager,func ,editor,outparam);
		}
	};
	
	
	 var ImageManager_StartManager = function(editor,data)
	 {
		 
		 var pluginPath = editor.plugins[pluginName].path;
		 
		 var url = pluginPath + editor.config.imageManager_url;
		 url += '?dir='+ data.dir +'&img=' + data.file + "&client=" + editor.config.client;
		 Dialog(url, function(param) {
	
				if (!param) return false; // user must have pressed cancel
				var sElm = editor.getSelection().getSelectedElement();
		
				
				 // new image// no image - create new image
				var im = editor.document.createElement( 'img' );
			
				var saved_url = param.f_url.replace(editor.config.baseHref,'');	
				// set image attributes	
				setAttrib(im, "_cke_saved_src", saved_url, true);			
				setAttrib(im, 'src', param.f_url, true);				
				setAttrib(im, 'alt', param.f_alt, true);
				setAttrib(im, 'title', param.f_title, true);
				setAttrib(im, 'align', param.f_align, true);
				setAttrib(im, 'border', param.f_border);
				setAttrib(im, 'hspace', param.f_horiz);
				setAttrib(im, 'vspace', param.f_vert);
				setAttrib(im, 'width', param.f_width);
				setAttrib(im, 'height', param.f_height);				
				setAttrib(im, 'className', param.f_className, true); 
				
				if(sElm != null && sElm.getName().toLowerCase() == 'img')
				{
					
					sElm.insertBeforeMe( im );
					sElm.remove( false );
				}
				else
				{
					editor.insertElement( im);	
				}
				return;
	
			},editor,null);
		 
	 }
	
	
	
	// Dialog v3.0 - Copyright (c) 2003-2004 interactivetools.com, inc.
	// This copyright notice MUST stay intact for use (see license.txt).
	//
	// Portions (c) dynarch.com, 2003-2004
	//
	// A free WYSIWYG editor replacement for <textarea> fields.
	// For full source code and docs, visit http://www.interactivetools.com/
	//
	// Version 3.0 developed by Mihai Bazon.
	//   http://dynarch.com/mishoo
	//
	// Id: dialog.js 26 2004-03-31 02:35:21Z Wei Zhuo 
	
	// Though "Dialog" looks like an object, it isn't really an object.  Instead
	// it's just namespace for protecting global symbols.
	var Dialog = function (url, action,editor, init) {
		if (typeof init == "undefined") {
			init = window;	// pass this window object by default
		}
		Dialog._geckoOpenModal(url, action, editor, init);
	};
	
	Dialog._parentEvent = function(ev) {
		setTimeout( function() { if (Dialog._modal && !Dialog._modal.closed) { Dialog._modal.focus() } }, 50);
		if (Dialog._modal && !Dialog._modal.closed) {
			Dialog._stopEvent(ev);
		}
	};
	
	
	// should be a function, the return handler of the currently opened dialog.
	Dialog._return = null;
	
	// constant, the currently opened dialog
	Dialog._modal = null;
	
	// the dialog will read it's args from this variable
	Dialog._arguments = null;
	
	Dialog._geckoOpenModal = function(url, action, editor, init) {
		//var urlLink = "hadialog"+url.toString();
		var myURL = "hadialog"+url;
		var regObj = /\W/g;
		myURL = myURL.replace(regObj,'_');
		var dlg = window.open(url, myURL,
					  "toolbar=no,menubar=no,personalbar=no,width=10,height=10," +
					  "scrollbars=no,resizable=yes,modal=yes,dependable=yes,status=0");
		Dialog._modal = dlg;
	
		
		//fire event to attach onSearch handler
	    Dialog._fieLoadEvents = function () { 
			CKEDITOR.fire('imageMangerLoaded',
			{
		 	 _dialog : Dialog._modal
			},editor);
		}
		
		Dialog._arguments = init;
	
		// capture some window's events
		function capwin(w) {
			Dialog._addEvent(w, "click", Dialog._parentEvent);
			Dialog._addEvent(w, "mousedown", Dialog._parentEvent);
			Dialog._addEvent(w, "focus", Dialog._parentEvent);
		};
		// release the captured events
		function relwin(w) {
			Dialog._removeEvent(w, "click", Dialog._parentEvent);
			Dialog._removeEvent(w, "mousedown", Dialog._parentEvent);
			Dialog._removeEvent(w, "focus", Dialog._parentEvent);
		};
		//capwin(window.document);
		// capture other frames
		//for (var i = 0; i < window.frames.length; capwin(window.frames[i++].document));
		// make up a function to be called when the Dialog ends.
		Dialog._return = function (val) {
			if (val && action) {
				action(val);
			}
			//relwin(window.document);
			// capture other frames
			//for (var i = 0; i < window.frames.length; relwin(window.frames[i++].document));
			Dialog._modal = null;
		};
	};
	
	
	// event handling
	
	Dialog._addEvent = function(el, evname, func) {
		if (Dialog.is_ie) {
			el.attachEvent("on" + evname, func);
			
		} else {		
			el.addEventListener(evname, func, true);
		}
	};
	
	
	Dialog._removeEvent = function(el, evname, func) {
		if (Dialog.is_ie) {
			el.detachEvent("on" + evname, func);
		} else {
			el.removeEventListener(evname, func, true);
		}
	};
	
	
	Dialog._stopEvent = function(ev) {
		if (Dialog.is_ie) {
			ev.cancelBubble = true;
			ev.returnValue = false;
		} else {
			ev.preventDefault();
			ev.stopPropagation();
		}
	};
	
	Dialog.agt = navigator.userAgent.toLowerCase();
	Dialog.is_ie	   = ((Dialog.agt.indexOf("msie") != -1) && (Dialog.agt.indexOf("opera") == -1));
	
		
	CKEDITOR.tools.extend( CKEDITOR.config,
	{
		imageManager_url: 'manager.php',
		IM_directEdit : true
	} );
	
	
	CKEDITOR.on('imageMangerLoaded', function(evt)
	{
		 if(CKEDITOR.env.gecko) // only required for firefox
		 {
			
			//var	dlg = evt.data._dialog;
			//dlg.resizeTo(620, 520);
		 }
	});
	
})();
