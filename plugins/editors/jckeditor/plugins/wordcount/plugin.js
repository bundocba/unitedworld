
(function() {
  CKEDITOR.plugins.wordcount = {
    dirty: false,
    spaceIdPrefix: 'cke_wordcount_'
  };
  var plugin = CKEDITOR.plugins.wordcount;
  
  var emptyHtml = '<span class="cke_empty">&nbsp;</span>';
  
  plugin.getSpaceId = function( editor ) {
    return plugin.spaceIdPrefix + editor.name;
  }
  
  plugin.getSpaceElement = function( editor ) {
    return CKEDITOR.document.getById( plugin.getSpaceId( editor ) );
  }
  
  plugin.showWordCount = function( editor ) {
    if (this.dirty) {
      this.dirty = false;
      var wordCount = this.countWords(editor.getData());
      var wordCountStr = wordCount + ' word';
      if (wordCount != 1) {
        wordCountStr = wordCount + ' words';
      }
      this.getSpaceElement(editor).setHtml( '<span style="display: inline-block; float: left; padding: 1px 4px 0;">' + wordCountStr + '</span>' + emptyHtml );
    }
    window.setTimeout( function(){ plugin.showWordCount(editor); }, 100 );
  }
  
  plugin.countWords = function( txt ) {
    var cleanTxt = txt.replace(/<(?:.|\s)*?>/g, '');
    if (cleanTxt.length) {
      return cleanTxt.split(' ').length;
    }
    
    return cleanTxt.length;
  }
  
  CKEDITOR.plugins.add('wordcount', {
    //requires : [ 'selection' ],
    init : function( editor ) {
      var spaceId = plugin.getSpaceId( editor );
      
      editor.on( 'themeSpace', function( event ){
        if ( event.data.space == 'bottom' ) {
          event.data.html =
            '<span id="' + spaceId + '_label" class="cke_voice_label">' + editor.lang.elementsPath.eleLabel + '</span>' +
            '<div id="' + spaceId + '" class="cke_path" role="group" aria-labelledby="' + spaceId + '_label">' + emptyHtml + '</div>' + event.data.html;
        }
      });
      
      editor.on( 'contentDom', function( event ) {
        var editor = event.editor;
        plugin.dirty = true;
        window.setTimeout( function(){ plugin.showWordCount( editor ); }, 100 );
      });
      
      editor.on( 'afterSetData', function( event ) {
        plugin.dirty = true;
      });
      
      editor.on( 'key', function( event ) {
        plugin.dirty = true;
      });
      
      editor.on( 'contentDomUnload', function( event ) {
        var editor = event.editor;
        plugin.getSpaceElement( editor ).setHtml( emptyHtml );
      });
    }
  });
})();
