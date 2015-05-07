function MirrorFrame(a,c){function b(f,g){var d=document.createElement("INPUT");d.type="button";d.value=f;e.home.appendChild(d);d.onclick=function(){e[g].call(e)}}this.home=document.createElement("DIV");a.appendChild?a.appendChild(this.home):a(this.home);var e=this;b("Search","search");b("Replace","replace");b("Current line","line");b("Jump to line","jump");b("Insert constructor","macro");b("Indent all","reindent");this.mirror=new CodeMirror(this.home,c)}
MirrorFrame.prototype={search:function(){var a=prompt("Enter search term:","");if(a){var c=true;do{var b=this.mirror.getSearchCursor(a,c);for(c=false;b.findNext();){b.select();if(!confirm("Search again?"))return}}while(confirm("End of document reached. Start over?"))}},replace:function(){var a=prompt("Enter search string:",""),c;if(a)c=prompt("What should it be replaced with?","");if(c!=null)for(a=this.mirror.getSearchCursor(a,false);a.findNext();)a.replace(c)},jump:function(){var a=prompt("Jump to line:",
"");a&&!isNaN(Number(a))&&this.mirror.jumpToLine(Number(a))},line:function(){alert("The cursor is currently at line "+this.mirror.currentLine());this.mirror.focus()},macro:function(){var a=prompt("Name your constructor:","");a&&this.mirror.replaceSelection("function "+a+"() {\n  \n}\n\n"+a+".prototype = {\n  \n};\n")},reindent:function(){this.mirror.reindent()}};