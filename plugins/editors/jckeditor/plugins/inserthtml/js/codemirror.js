var CodeMirrorConfig = window.CodeMirrorConfig || {},
    CodeMirror = function () {
        function D(a, b) {
            for (var c in b) a.hasOwnProperty(c) || (a[c] = b[c])
        }
        function E(a, b) {
            for (var c = 0; c < a.length; c++) b(a[c])
        }
        function F(a) {
            var b = document.createElement("DIV"),
                c = document.createElement("DIV");
            b.style.position = "absolute";
            b.style.height = "100%";
            if (b.style.setExpression) try {
                b.style.setExpression("height", "this.previousSibling.offsetHeight + 'px'")
            } catch (f) {}
            b.style.top = "0px";
            b.style.left = "0px";
            b.style.overflow = "hidden";
            a.appendChild(b);
            c.className = "CodeMirror-line-numbers";
            b.appendChild(c);
            c.innerHTML = "<div>1</div>";
            return b
        }
        function G(a) {
            if (typeof a.parserfile == "string") a.parserfile = [a.parserfile];
            if (typeof a.basefiles == "string") a.basefiles = [a.basefiles];
            if (typeof a.stylesheet == "string") a.stylesheet = [a.stylesheet];
            var b = ['<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd"><html><head>'];
            b.push('<meta http-equiv="X-UA-Compatible" content="IE=EmulateIE7"/>');
            E(a.stylesheet, function (c) {
                b.push('<link rel="stylesheet" type="text/css" href="' + c + '"/>')
            });
            E(a.basefiles.concat(a.parserfile), function (c) {
                /^https?:/.test(c) || (c = a.path + c);
                b.push('<script type="text/javascript" src="' + c + '"><\/script>')
            });
            b.push('</head><body style="border-width: 0;" class="editbox" spellcheck="' + (a.disableSpellcheck ? "false" : "true") + '"></body></html>');
            return b.join("")
        }
        function r(a, b) {
            this.options = b = b || {};
            D(b, CodeMirrorConfig);
            if (b.dumbTabs) b.tabMode = "spaces";
            else if (b.normalTab) b.tabMode = "default";
            var c = this.frame = document.createElement("IFRAME");
            if (b.iframeClass) c.className = b.iframeClass;
            c.frameBorder = 0;
            c.style.border = "0";
            c.style.width = "100%";
            c.style.height = "100%";
            c.style.display = "block";
            var f = this.wrapping = document.createElement("DIV");
            f.style.position = "relative";
            f.className = "CodeMirror-wrapping";
            f.style.width = b.width;
            f.style.height = '100%';
            var g = this.textareaHack = document.createElement("TEXTAREA");
            f.appendChild(g);
            g.style.position = "absolute";
            g.style.left = "-10000px";
            g.style.width = "10px";
            c.CodeMirror = this;
            if (b.domain && x) {
                this.html =
                G(b);
                c.src = "javascript:(function(){document.open();" + (b.domain ? 'document.domain="' + b.domain + '";' : "") + "document.write(window.frameElement.CodeMirror.html);document.close();})()"
            } else c.src = "javascript:;";
            a.appendChild ? a.appendChild(f) : a(f);
            f.appendChild(c);
            if (b.lineNumbers) this.lineNumbers = F(f);
            this.win = c.contentWindow;
            if (!b.domain || !x) {
                this.win.document.open();
                this.win.document.write(G(b));
                this.win.document.close()
            }
        }
        D(CodeMirrorConfig, {
            stylesheet: [],
            path: "",
            parserfile: [],
            basefiles: ["util.js", "stringstream.js", "select.js", "undo.js", "editor.js", "tokenize.js"],
            iframeClass: null,
            passDelay: 200,
            passTime: 50,
            lineNumberDelay: 200,
            lineNumberTime: 50,
            continuousScanning: false,
            saveFunction: null,
            onChange: null,
            undoDepth: 50,
            undoDelay: 800,
            disableSpellcheck: true,
            textWrapping: true,
            readOnly: false,
            width: "",
            height: "300px",
            minHeight: 100,
            autoMatchParens: false,
            parserConfig: null,
            tabMode: "indent",
            enterMode: "indent",
            electricChars: true,
            reindentOnLoad: false,
            activeTokens: null,
            cursorActivity: null,
            lineNumbers: false,
            indentUnit: 2,
            domain: null
        });
        var x = document.selection && window.ActiveXObject && /MSIE/.test(navigator.userAgent);
        r.prototype = {
            init: function () {
                this.options.initCallback && this.options.initCallback(this);
                this.options.lineNumbers && this.activateLineNumbers();
                this.options.reindentOnLoad && this.reindent();
                this.options.height == "dynamic" && this.setDynamicHeight()
            },
            getCode: function () {
                return this.editor.getCode()
            },
            setCode: function (a) {
                this.editor.importCode(a)
            },
            selection: function () {
                this.focusIfIE();
                return this.editor.selectedText()
            },
            reindent: function () {
                this.editor.reindent()
            },
            reindentSelection: function () {
                this.focusIfIE();
                this.editor.reindentSelection(null)
            },
            focusIfIE: function () {
                this.win.select.ie_selection && this.focus()
            },
            focus: function () {
                this.win.focus();
                this.editor.selectionSnapshot && this.win.select.setBookmark(this.win.document.body, this.editor.selectionSnapshot)
            },
            replaceSelection: function (a) {
                this.focus();
                this.editor.replaceSelection(a);
                return true
            },
            replaceChars: function (a, b, c) {
                this.editor.replaceChars(a, b, c)
            },
            getSearchCursor: function (a, b, c) {
                return this.editor.getSearchCursor(a, b, c)
            },
            undo: function () {
                this.editor.history.undo()
            },
            redo: function () {
                this.editor.history.redo()
            },
            historySize: function () {
                return this.editor.history.historySize()
            },
            clearHistory: function () {
                this.editor.history.clear()
            },
            grabKeys: function (a, b) {
                this.editor.grabKeys(a, b)
            },
            ungrabKeys: function () {
                this.editor.ungrabKeys()
            },
            setParser: function (a, b) {
                this.editor.setParser(a, b)
            },
            setSpellcheck: function (a) {
                this.win.document.body.spellcheck = a
            },
            setStylesheet: function (a) {
                if (typeof a === "string") a = [a];
                for (var b = {}, c = {}, f = this.win.document.getElementsByTagName("link"), g = 0, d; d = f[g]; g++) if (d.rel.indexOf("stylesheet") !== -1) for (var e = 0; e < a.length; e++) {
                    var j = a[e];
                    if (d.href.substring(d.href.length - j.length) === j) {
                        b[d.href] = true;
                        c[j] = true
                    }
                }
                for (g = 0; d = f[g]; g++) if (d.rel.indexOf("stylesheet") !== -1) d.disabled = !(d.href in b);
                for (e = 0; e < a.length; e++) {
                    j = a[e];
                    if (!(j in c)) {
                        d = this.win.document.createElement("link");
                        d.rel = "stylesheet";
                        d.type = "text/css";
                        d.href = j;
                        this.win.document.getElementsByTagName("head")[0].appendChild(d)
                    }
                }
            },
            setTextWrapping: function (a) {
                if (a != this.options.textWrapping) {
                    this.win.document.body.style.whiteSpace =
                    a ? "" : "nowrap";
                    this.options.textWrapping = a;
                    if (this.lineNumbers) {
                        this.setLineNumbers(false);
                        this.setLineNumbers(true)
                    }
                }
            },
            setIndentUnit: function (a) {
                this.win.indentUnit = a
            },
            setUndoDepth: function (a) {
                this.editor.history.maxDepth = a
            },
            setTabMode: function (a) {
                this.options.tabMode = a
            },
            setEnterMode: function (a) {
                this.options.enterMode = a
            },
            setLineNumbers: function (a) {
                if (a && !this.lineNumbers) {
                    this.lineNumbers = F(this.wrapping);
                    this.activateLineNumbers()
                } else if (!a && this.lineNumbers) {
                    this.wrapping.removeChild(this.lineNumbers);
                    this.wrapping.style.marginLeft = "";
                    this.lineNumbers = null
                }
            },
            cursorPosition: function (a) {
                this.focusIfIE();
                return this.editor.cursorPosition(a)
            },
            firstLine: function () {
                return this.editor.firstLine()
            },
            lastLine: function () {
                return this.editor.lastLine()
            },
            nextLine: function (a) {
                return this.editor.nextLine(a)
            },
            prevLine: function (a) {
                return this.editor.prevLine(a)
            },
            lineContent: function (a) {
                return this.editor.lineContent(a)
            },
            setLineContent: function (a, b) {
                this.editor.setLineContent(a, b)
            },
            removeLine: function (a) {
                this.editor.removeLine(a)
            },
            insertIntoLine: function (a, b, c) {
                this.editor.insertIntoLine(a, b, c)
            },
            selectLines: function (a, b, c, f) {
                this.win.focus();
                this.editor.selectLines(a, b, c, f)
            },
            nthLine: function (a) {
                for (var b = this.firstLine(); a > 1 && b !== false; a--) b = this.nextLine(b);
                return b
            },
            lineNumber: function (a) {
                for (var b = 0; a !== false;) {
                    b++;
                    a = this.prevLine(a)
                }
                return b
            },
            jumpToLine: function (a) {
                if (typeof a == "number") a = this.nthLine(a);
                this.selectLines(a, 0);
                this.win.focus()
            },
            currentLine: function () {
                return this.lineNumber(this.cursorLine())
            },
            cursorLine: function () {
                return this.cursorPosition().line
            },
            cursorCoords: function (a) {
                return this.editor.cursorCoords(a)
            },
            activateLineNumbers: function () {
                function a() {
                    if (d.offsetWidth != 0) {
                        for (var h = d; h.parentNode; h = h.parentNode);
                        if (!u.parentNode || h != document || !e.Editor) {
                            try {
                                y()
                            } catch (n) {}
                            clearInterval(I)
                        } else if (u.offsetWidth != z) {
                            z = u.offsetWidth;
                            d.parentNode.style.paddingLeft = z + "px"
                        }
                    }
                }
                function b() {
                    u.scrollTop = i.scrollTop || j.documentElement.scrollTop || 0
                }
                function c(h) {
                    var n = p.firstChild.offsetHeight;
                    if (n != 0) {
                        n = Math.ceil((50 + Math.max(i.offsetHeight, Math.max(d.offsetHeight, i.scrollHeight || 0))) / n);
                        for (var q = p.childNodes.length; q <= n; q++) {
                            var v = document.createElement("DIV");
                            v.appendChild(document.createTextNode(h ? String(q + 1) : "\u00a0"));
                            p.appendChild(v)
                        }
                    }
                }
                function f() {
                    function h() {
                        c(true);
                        b()
                    }
                    l.updateNumbers = h;
                    var n = e.addEventHandler(e, "scroll", b, true),
                        q = e.addEventHandler(e, "resize", h, true);
                    y = function () {
                        n();
                        q();
                        if (l.updateNumbers == h) l.updateNumbers = null
                    };
                    h()
                }
                function g() {
                    function h(m, A) {
                        o || (o = p.appendChild(document.createElement("DIV")));
                        H && H(o, A, m);
                        s.push(o);
                        s.push(m);
                        B = o.offsetHeight + o.offsetTop;
                        o = o.nextSibling
                    }
                    function n() {
                        for (var m = 0; m < s.length; m += 2) s[m].innerHTML = s[m + 1];
                        s = []
                    }
                    function q() {
                        if (!(!p.parentNode || p.parentNode != l.lineNumbers)) {
                            for (var m = (new Date).getTime() + l.options.lineNumberTime; k;) {
                                for (h(C++, k.previousSibling); k && !e.isBR(k); k = k.nextSibling) for (var A = k.offsetTop + k.offsetHeight; p.offsetHeight && A - 3 > B;) h("&nbsp;");
                                if (k) k = k.nextSibling;
                                if ((new Date).getTime() > m) {
                                    n();
                                    t = setTimeout(q, l.options.lineNumberDelay);
                                    return
                                }
                            }
                            for (; o;) h(C++);
                            n();
                            b()
                        }
                    }
                    function v(m) {
                        b();
                        c(m);
                        k = i.firstChild;
                        o = p.firstChild;
                        B = 0;
                        C = 1;
                        q()
                    }
                    function w() {
                        t && clearTimeout(t);
                        if (l.editor.allClean()) v();
                        else t = setTimeout(w, 200)
                    }
                    var k, o, C, B, s = [],
                        H = l.options.styleNumbers;
                    v(true);
                    var t = null;
                    l.updateNumbers = w;
                    var J = e.addEventHandler(e, "scroll", b, true),
                        K = e.addEventHandler(e, "resize", w, true);
                    y = function () {
                        t && clearTimeout(t);
                        if (l.updateNumbers == w) l.updateNumbers = null;
                        J();
                        K()
                    }
                }
                var d = this.frame,
                    e = d.contentWindow,
                    j = e.document,
                    i = j.body,
                    u = this.lineNumbers,
                    p = u.firstChild,
                    l = this,
                    z = null,
                    y = function () {};
                a();
                var I =
                setInterval(a, 500);
                (this.options.textWrapping || this.options.styleNumbers ? g : f)()
            },
            setDynamicHeight: function () {
                function a() {
                    for (var i = g.firstChild; i; i = i.nextSibling) if (f.isSpan(i) && i.offsetHeight) {
                        d = i.offsetHeight;
                        j = 2 * (b.frame.offsetTop + i.offsetTop + g.offsetTop + (x ? 10 : 0));
                        break
                    }
                    if (d) b.wrapping.style.height = Math.max(j + d * (g.getElementsByTagName("BR").length + 1), b.options.minHeight) + "px"
                }
                var b = this,
                    c = b.options.cursorActivity,
                    f = b.win,
                    g = f.document.body,
                    d = null,
                    e = null,
                    j = 2 * b.frame.offsetTop;
                g.style.overflowY = "hidden";
                f.document.documentElement.style.overflowY = "hidden";
                this.frame.scrolling = "no";
                setTimeout(a, 100);
                b.options.cursorActivity = function (i) {
                    c && c(i);
                    clearTimeout(e);
                    e = setTimeout(a, 200)
                }
            }
        };
        r.InvalidLineHandle = {
            toString: function () {
                return "CodeMirror.InvalidLineHandle"
            }
        };
        r.replace = function (a) {
            if (typeof a == "string") a = document.getElementById(a);
            return function (b) {
                a.parentNode.replaceChild(b, a)
            }
        };
        r.fromTextArea = function (a, b) {
            if (typeof a == "string") a = document.getElementById(a);
            b = b || {};
            if (a.style.width && b.width == null) b.width = a.style.width;
            if (a.style.height && b.height == null) b.height = a.style.height;
            if (b.content == null) b.content = a.value;
            if (a.form) {
                var c = function () {
                    a.value = d.getCode()
                };
                typeof a.form.addEventListener == "function" ? a.form.addEventListener("submit", c, false) : a.form.attachEvent("onsubmit", c);
               
            }
            a.style.display = "none";
            var d = new r(function (e) {
                a.nextSibling ? a.parentNode.insertBefore(e, a.nextSibling) : a.parentNode.appendChild(e)
            }, b);
            d.toTextArea = function () {
                a.parentNode.removeChild(d.wrapping);
                a.style.display = "";
                if (a.form) {
                    a.form.submit = f;
                    typeof a.form.removeEventListener == "function" ? a.form.removeEventListener("submit", c, false) : a.form.detachEvent("onsubmit", c)
                }
            };
            return d
        };
        r.isProbablySupported = function () {
            var a;
            return window.opera ? Number(window.opera.version()) >= 9.52 : /Apple Computers, Inc/.test(navigator.vendor) && (a = navigator.userAgent.match(/Version\/(\d+(?:\.\d+)?)\./)) ? Number(a[1]) >= 3 : document.selection && window.ActiveXObject && (a = navigator.userAgent.match(/MSIE (\d+(?:\.\d*)?)\b/)) ? Number(a[1]) >= 6 : (a = navigator.userAgent.match(/gecko\/(\d{8})/i)) ? Number(a[1]) >= 20050901 : (a = navigator.userAgent.match(/AppleWebKit\/(\d+)/)) ? Number(a[1]) >= 525 : null
        };
        return r
    }();