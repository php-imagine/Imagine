/*
Copyright (c) 2009 Vladimir Kolesnikov

Permission is hereby granted, free of charge, to any person obtaining
a copy of this software and associated documentation files (the
"Software"), to deal in the Software without restriction, including
without limitation the rights to use, copy, modify, merge, publish,
distribute, sublicense, and/or sell copies of the Software, and to
permit persons to whom the Software is furnished to do so, subject to
the following conditions:

The above copyright notice and this permission notice shall be
included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE
LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION
OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION
WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
*/

Searchdoc = {};

// navigation.js ------------------------------------------

Searchdoc.Navigation = new function() {
    this.initNavigation = function() {
        var _this = this;
        
        $(document).keydown(function(e) {
            _this.onkeydown(e);
        }).keyup(function(e) {
            _this.onkeyup(e);
        });
        
        this.navigationActive = true;
    }
    
    this.setNavigationActive = function(state) {
        this.navigationActive = state;
        this.clearMoveTimeout();
    }


    this.onkeyup = function(e) {
        if (!this.navigationActive) return;
        switch(e.keyCode) {
            case 37: //Event.KEY_LEFT:
            case 38: //Event.KEY_UP:
            case 39: //Event.KEY_RIGHT:
            case 40: //Event.KEY_DOWN:
            case 73: // i - qwerty
            case 74: // j
            case 75: // k
            case 76: // l
            case 67: // c - dvorak
            case 72: // h 
            case 84: // t
            case 78: // n
                this.clearMoveTimeout();
                break;
            }
    }

    this.onkeydown = function(e) {
        if (!this.navigationActive) return;
        switch(e.keyCode) {
            case 37: //Event.KEY_LEFT:
            case 74: // j (qwerty)
            case 72: // h (dvorak)
                if (this.moveLeft()) e.preventDefault();
                break;
            case 38: //Event.KEY_UP:
            case 73: // i (qwerty)
            case 67: // c (dvorak)
                if (e.keyCode == 38 || e.ctrlKey) {
                    if (this.moveUp()) e.preventDefault();
                    this.startMoveTimeout(false);
                }
                break;
            case 39: //Event.KEY_RIGHT:
            case 76: // l (qwerty)
            case 78: // n (dvorak)
                if (this.moveRight()) e.preventDefault();
                break;
            case 40: //Event.KEY_DOWN:
            case 75: // k (qwerty)
            case 84: // t (dvorak)
                if (e.keyCode == 40 || e.ctrlKey) {
                    if (this.moveDown()) e.preventDefault();
                    this.startMoveTimeout(true);
                }
                break;
            case 9: //Event.KEY_TAB:
            case 13: //Event.KEY_RETURN:
                if (this.$current) this.select(this.$current);
                break;
        }
        if (e.ctrlKey && e.shiftKey) this.select(this.$current);
    }

    this.clearMoveTimeout = function() {
        clearTimeout(this.moveTimeout); 
        this.moveTimeout = null;
    }

    this.startMoveTimeout = function(isDown) {
        if (!$.browser.mozilla && !$.browser.opera) return;
        if (this.moveTimeout) this.clearMoveTimeout();
        var _this = this;
    
        var go = function() {
            if (!_this.moveTimeout) return;
            _this[isDown ? 'moveDown' : 'moveUp']();
            _this.moveTimout = setTimeout(go, 100);
        }
        this.moveTimeout = setTimeout(go, 200);
    }    
    
    this.moveRight = function() {
    }
    
    this.moveLeft = function() {
    }

    this.move = function(isDown) {
    }

    this.moveUp = function() {
        return this.move(false);
    }

    this.moveDown = function() {
        return this.move(true);
    }    
}


// scrollIntoView.js --------------------------------------

function scrollIntoView(element, view) {
    var offset, viewHeight, viewScroll, height;
    offset = element.offsetTop;
    height = element.offsetHeight;
    viewHeight = view.offsetHeight;
    viewScroll = view.scrollTop;
    if (offset - viewScroll + height > viewHeight) {
        view.scrollTop = offset - viewHeight + height;
    }
    if (offset < viewScroll) {
        view.scrollTop = offset;
    }
}


// searcher.js --------------------------------------------

Searchdoc.Searcher = function(data) {
    this.data = data;
    this.handlers = [];
}

Searchdoc.Searcher.prototype = new function() {
    var CHUNK_SIZE = 1000, // search is performed in chunks of 1000 for non-bloking user input
        MAX_RESULTS = 100, // do not try to find more than 100 results
        huid = 1, suid = 1,
        runs = 0;


    this.find = function(query) {
        var queries = splitQuery(query),
            regexps = buildRegexps(queries),
            highlighters = buildHilighters(queries),
            state = { from: 0, pass: 0, limit: MAX_RESULTS, n: suid++},
            _this = this;
        this.currentSuid = state.n;
    
        if (!query) return;
    
        var run = function() {
            // stop current search thread if new search started
            if (state.n != _this.currentSuid) return;
            
            var results = performSearch(_this.data, regexps, queries, highlighters, state),
                hasMore = (state.limit > 0 && state.pass < 3);
                
            triggerResults.call(_this, results, !hasMore);
            if (hasMore) {
                setTimeout(run, 2);
            }
            runs++;
        };
        runs = 0;
        
        // start search thread
        run();
    }

    /*  ----- Events ------  */
    this.ready = function(fn) {
        fn.huid = huid;
        this.handlers.push(fn);
    }

    /*  ----- Utilities ------  */
    function splitQuery(query) {
        return jQuery.grep(query.split(/(\s+|\(\)?)/), function(string) { return string.match(/\S/) });
    }

    function buildRegexps(queries) {
        return jQuery.map(queries, function(query) { return new RegExp(query.replace(/(.)/g, '([$1])([^$1]*?)'), 'i') });
    }

    function buildHilighters(queries) {
        return jQuery.map(queries, function(query) {
            return jQuery.map( query.split(''), function(l, i){ return '\u0001$' + (i*2+1) + '\u0002$' + (i*2+2) } ).join('')
        });
    }

    // function longMatchRegexp(index, longIndex, regexps) {
    //     for (var i = regexps.length - 1; i >= 0; i--){
    //         if (!index.match(regexps[i]) && !longIndex.match(regexps[i])) return false;
    //     };
    //     return true;
    // }
    
    
    /*  ----- Mathchers ------  */
    function matchPass1(index, longIndex, queries, regexps) {
        if (index.indexOf(queries[0]) != 0) return false;
        for (var i=1, l = regexps.length; i < l; i++) {
            if (!index.match(regexps[i]) && !longIndex.match(regexps[i])) return false;
        };
        return true;
    }

    function matchPass2(index, longIndex, queries, regexps) {
        if (index.indexOf(queries[0]) == -1) return false;
        for (var i=1, l = regexps.length; i < l; i++) {
            if (!index.match(regexps[i]) && !longIndex.match(regexps[i])) return false;
        };
        return true;
    }
    
    function matchPassRegexp(index, longIndex, queries, regexps) {
        if (!index.match(regexps[0])) return false;
        for (var i=1, l = regexps.length; i < l; i++) {
            if (!index.match(regexps[i]) && !longIndex.match(regexps[i])) return false;
        };
        return true;
    }


    /*  ----- Highlighters ------  */
    function highlightRegexp(info, queries, regexps, highlighters) {
        var result = createResult(info);
        for (var i=0, l = regexps.length; i < l; i++) {
            result.title = result.title.replace(regexps[i], highlighters[i]);
            if (i > 0)
                result.namespace = result.namespace.replace(regexps[i], highlighters[i]);
        };
        return result;
    }
    
    function hltSubstring(string, pos, length) {
        return string.substring(0, pos) + '\u0001' + string.substring(pos, pos + length) + '\u0002' + string.substring(pos + length);
    }
    
    function highlightQuery(info, queries, regexps, highlighters) {
        var result = createResult(info), pos = 0, lcTitle = result.title.toLowerCase();
        pos = lcTitle.indexOf(queries[0]);
        if (pos != -1) {
            result.title = hltSubstring(result.title, pos, queries[0].length);
        }
        for (var i=1, l = regexps.length; i < l; i++) {
            result.title = result.title.replace(regexps[i], highlighters[i]);
            result.namespace = result.namespace.replace(regexps[i], highlighters[i]);
        };
        return result;
    }

    function createResult(info) {
        var result = {};
        result.title = info[0];
        result.namespace = info[1];
        result.path = info[2];
        result.params = info[3];
        result.snippet = info[4];
        result.badge = info[6];
        return result;
    }

    /*  ----- Searching ------  */
    function performSearch(data, regexps, queries, highlighters, state) {
        var searchIndex = data.searchIndex, // search by title first and then by source
            longSearchIndex = data.longSearchIndex,
            info = data.info,
            result = [],
            i = state.from, 
            l = searchIndex.length,
            togo = CHUNK_SIZE,
            matchFunc, hltFunc;
            
        while (state.pass < 3 && state.limit > 0 && togo > 0) {
            if (state.pass == 0) {
                matchFunc = matchPass1;
                hltFunc = highlightQuery;
            } else if (state.pass == 1) {
                matchFunc = matchPass2;
                hltFunc = highlightQuery;
            } else if (state.pass == 2) {
                matchFunc = matchPassRegexp;
                hltFunc = highlightRegexp;
            }
            
            for (; togo > 0 && i < l && state.limit > 0; i++, togo--) {
                if (info[i].n == state.n) continue;
                if (matchFunc(searchIndex[i], longSearchIndex[i], queries, regexps)) {
                    info[i].n = state.n;
                    result.push(hltFunc(info[i], queries, regexps, highlighters));
                    state.limit--;
                }
            };
            if (searchIndex.length <= i) {
                state.pass++;
                i = state.from = 0;
            } else {
                state.from = i;
            }
        }
        return result;
    }
    
    function triggerResults(results, isLast) {
        jQuery.each(this.handlers, function(i, fn) { fn.call(this, results, isLast) })
    }
}    




// panel.js -----------------------------------------------

Searchdoc.Panel = function(element, data, tree, frame) {
    this.$element = $(element);
    this.$input = $('input', element).eq(0);
    this.$result = $('.result ul', element).eq(0);
    this.frame = frame;
    this.$current = null;
    this.$view = this.$result.parent();
    this.data = data;
    this.searcher = new Searchdoc.Searcher(data.index);
    this.tree = new Searchdoc.Tree($('.tree', element), tree, this);
    this.init();
}

Searchdoc.Panel.prototype = $.extend({}, Searchdoc.Navigation, new function() {
    var suid = 1;

    this.init = function() {
        var _this = this;
        var observer = function() {
            _this.search(_this.$input[0].value);
        };
        this.$input.keyup(observer);
        this.$input.click(observer); // mac's clear field
    
        this.searcher.ready(function(results, isLast) {
            _this.addResults(results, isLast);
        })
    
        this.$result.click(function(e) {
            _this.$current.removeClass('current');
            _this.$current = $(e.target).closest('li').addClass('current');
            _this.select();
            _this.$input.focus();
        });
        
        this.initNavigation();
        this.setNavigationActive(false);
    }

    this.search = function(value, selectFirstMatch) {
        value = jQuery.trim(value).toLowerCase();
        this.selectFirstMatch = selectFirstMatch;
        if (value) {
            this.$element.removeClass('panel_tree').addClass('panel_results');
            this.tree.setNavigationActive(false);
            this.setNavigationActive(true);
        } else {
            this.$element.addClass('panel_tree').removeClass('panel_results');
            this.tree.setNavigationActive(true);
            this.setNavigationActive(false);
        }
        if (value != this.lastQuery) {
            this.lastQuery = value;
            this.firstRun = true;
            this.searcher.find(value);
        }
    }

    this.addResults = function(results, isLast) {
        var target = this.$result.get(0);
        if (this.firstRun && (results.length > 0 || isLast)) {
            this.$current = null;
            this.$result.empty();
        }
        for (var i=0, l = results.length; i < l; i++) {
            target.appendChild(renderItem.call(this, results[i]));
        };
        if (this.firstRun && results.length > 0) {
            this.firstRun = false;
            this.$current = $(target.firstChild);
            this.$current.addClass('current');
            if (this.selectFirstMatch) this.select();
            scrollIntoView(this.$current[0], this.$view[0])
        }
        if (jQuery.browser.msie) this.$element[0].className += '';
    }

    this.open = function(src) {
        this.frame.location.href = src;
        if (this.frame.highlight) this.frame.highlight(src);
    }

    this.select = function() {
        this.open(this.$current.data('path'));
    }

    this.move = function(isDown) {
        if (!this.$current) return;
        var $next = this.$current[isDown ? 'next' : 'prev']();
        if ($next.length) {
            this.$current.removeClass('current');
            $next.addClass('current');
            scrollIntoView($next[0], this.$view[0]);
            this.$current = $next;
        }
        return true;
    }

    function renderItem(result) {
        var li = document.createElement('li'),
            html = '', badge = result.badge;
        html += '<h1>' + hlt(result.title);
        if (result.params) html += '<i>' + result.params + '</i>';
        html += '</h1>';
        html += '<p>';
        if (typeof badge != 'undefined') {
            html += '<span class="badge badge_' + (badge % 6 + 1) + '">' + escapeHTML(this.data.badges[badge] || 'unknown') + '</span>';
        }
        html += hlt(result.namespace) + '</p>';
        if (result.snippet) html += '<p class="snippet">' + escapeHTML(result.snippet) + '</p>';
        li.innerHTML = html;
        jQuery.data(li, 'path', result.path);
        return li;
    }

    function hlt(html) {
        return escapeHTML(html).replace(/\u0001/g, '<b>').replace(/\u0002/g, '</b>')
    }

    function escapeHTML(html) {
        return html.replace(/[&<>]/g, function(c) {
            return '&#' + c.charCodeAt(0) + ';';
        });
    }

}); 

// tree.js ------------------------------------------------

Searchdoc.Tree = function(element, tree, panel) {
    this.$element = $(element);
    this.$list = $('ul', element);
    this.tree = tree;
    this.panel = panel;
    this.init();
}

Searchdoc.Tree.prototype = $.extend({}, Searchdoc.Navigation, new function() {
    this.init = function() {
        var stopper = document.createElement('li');
        stopper.className = 'stopper';
        this.$list[0].appendChild(stopper);
        for (var i=0, l = this.tree.length; i < l; i++) {
            buildAndAppendItem.call(this, this.tree[i], 0, stopper);
        };
        var _this = this;
        this.$list.click(function(e) {
            var $target = $(e.target),
                $li = $target.closest('li');
            if ($target.hasClass('icon')) {
                _this.toggle($li);
            } else {
                _this.select($li);
            }
        })

        this.initNavigation();
        if (jQuery.browser.msie) document.body.className += '';
    }

    this.select = function($li) {
        this.highlight($li);
        var path = $li[0].searchdoc_tree_data.path;
        if (path) this.panel.open(path);
    }
    
    this.highlight = function($li) {
        if (this.$current) this.$current.removeClass('current');
        this.$current = $li.addClass('current');
    }

    this.toggle = function($li) {
        var closed = !$li.hasClass('closed'),
            children = $li[0].searchdoc_tree_data.children;
        $li.toggleClass('closed');
        for (var i=0, l = children.length; i < l; i++) {
            toggleVis.call(this, $(children[i].li), !closed);
        };
    }

    this.moveRight = function() {
        if (!this.$current) {
            this.highlight(this.$list.find('li:first'));
            return;
        }
        if (this.$current.hasClass('closed')) {
            this.toggle(this.$current);
        }
    }
    
    this.moveLeft = function() {
        if (!this.$current) {
            this.highlight(this.$list.find('li:first'));
            return;
        }
        if (!this.$current.hasClass('closed')) {
            this.toggle(this.$current);
        } else {
            var level = this.$current[0].searchdoc_tree_data.level;
            if (level == 0) return;
            var $next = this.$current.prevAll('li.level_' + (level - 1) + ':visible:first');
            this.$current.removeClass('current');
            $next.addClass('current');
            scrollIntoView($next[0], this.$element[0]);
            this.$current = $next;
        }
    }

    this.move = function(isDown) {
        if (!this.$current) {
            this.highlight(this.$list.find('li:first'));
            return true;
        }        
        var next = this.$current[0];
        if (isDown) {
            do {
                next = next.nextSibling;
                if (next && next.style && next.style.display != 'none') break;
            } while(next);
        } else {
            do {
                next = next.previousSibling;
                if (next && next.style && next.style.display != 'none') break;
            } while(next);
        }
        if (next && next.className.indexOf('stopper') == -1) {
            this.$current.removeClass('current');
            $(next).addClass('current');
            scrollIntoView(next, this.$element[0]);
            this.$current = $(next);
        }
        return true;
    }

    function toggleVis($li, show) {
        var closed = $li.hasClass('closed'),
            children = $li[0].searchdoc_tree_data.children;
        $li.css('display', show ? '' : 'none')
        if (!show && this.$current && $li[0] == this.$current[0]) {
            this.$current.removeClass('current');
            this.$current = null;
        }
        for (var i=0, l = children.length; i < l; i++) {
            toggleVis.call(this, $(children[i].li), show && !closed);
        };
    }

    function buildAndAppendItem(item, level, before) {
        var li   = renderItem(item, level),
            list = this.$list[0];
        item.li = li;
        list.insertBefore(li, before);
        for (var i=0, l = item[3].length; i < l; i++) {
            buildAndAppendItem.call(this, item[3][i], level + 1, before);
        };
        return li;
    }

    function renderItem(item, level) {
        var li = document.createElement('li'),
            cnt = document.createElement('div'),
            h1 = document.createElement('h1'),
            p = document.createElement('p'),
            icon, i;
        
        li.appendChild(cnt);
        li.style.paddingLeft = getOffset(level);
        cnt.className = 'content';
        if (!item[1]) li.className  = 'empty ';
        cnt.appendChild(h1);
        // cnt.appendChild(p);
        h1.appendChild(document.createTextNode(item[0]));
        // p.appendChild(document.createTextNode(item[4]));
        if (item[2]) {
            i = document.createElement('i');
            i.appendChild(document.createTextNode(item[2]));
            h1.appendChild(i);
        }
        if (item[3].length > 0) {
            icon = document.createElement('div');
            icon.className = 'icon';
            cnt.appendChild(icon);
        }
        
        // user direct assignement instead of $()
        // it's 8x faster
        // $(li).data('path', item[1])
        //     .data('children', item[3])
        //     .data('level', level)
        //     .css('display', level == 0 ? '' : 'none')
        //     .addClass('level_' + level)
        //     .addClass('closed');
        li.searchdoc_tree_data = {
            path: item[1],
            children: item[3],
            level: level
        }
        li.style.display = level == 0 ? '' : 'none';
        li.className += 'level_' + level + ' closed';
        return li;
    }

    function getOffset(level) {
        return 5 + 18*level + 'px';
    }
});
