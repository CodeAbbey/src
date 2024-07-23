function nameToUrl(s) {
    return s.toLowerCase().replace(/\s+/g, '-').replace(/[^a-z0-9\-]/g, '');
}

function selectLanguage(s) {
    var sel = $('select[name=lang]');
    sel.find('option[selected]').prop('selected', null);
    sel.find('option[value=' + s + ']').prop('selected', true);
    sel.trigger('change');
}

$(function() {
    var flash = $('.flash-msg');
    if (flash.size() > 0) {
        var adv = $(".advheader .adv");
        adv.hide();
        flash.fadeIn(1000, function() {
            flash.delay(7000).fadeOut(2000, function() {adv.show();});
        });
    }

    $('.message-personal').click(function() {
        var elem = $(this);
        var ur = confirm('Remove this Reminder?');
        if (ur) {
            var url = elem.attr('data-rem');
            $.get(url);
            elem.hide();
        }
    });

    $('a.click-to-see').click(function() {
        $(this).closest('.hider').find('.hidden').removeClass('hidden');
        $(this).parent().hide();
        return false;
    });

    function getSelText() {
        var txt = '';
        if (window.getSelection) {
            txt = window.getSelection();
        } else if (document.getSelection) {
            txt = document.getSelection();
        } else if (document.selection) {
            txt = document.selection.createRange().text;
        }
        return txt;
    }

    function twoDigits(v) {
        var s = '00' + v;
        if (s.length > 2) {
            s = s.substring(s.length - 2, s.length);
        }
        return s;
    }

    function serverRunDisable() {
        $('input[id^=run]').attr('disabled', 'true').css('color', '#999');
        $('#no-run-warn').removeClass('hidden');
    }

    function colorLocaleLinks() {
        var lang = navigator.language;
        if (!lang) {
            return;
        }
        lang = lang.substring(0, 2);
        $('a.locale-' + lang).css('background', 'yellow').css('padding', '2px').css('font-weight', 'bold');
    }

    function loadCustomAd() {
        var div = $('div.custom-ad');
        if (div.size() == 0) {
            return;
        }
        div.load(tools + '_adblock');
    }

    function clockTick(initializing) {
        var $clock = $('.clock');
        if (arguments.length > 0) {
            $clock.attr('data-gen', Math.floor(new Date().getTime() / 1000));
            setInterval(clockTick, 300);
            return;
        }
        var time = parseFloat($clock.attr('data-time'));
        var diff = Math.floor(new Date().getTime() / 1000) - parseFloat($clock.attr('data-gen'));
        time = (time + diff) % (24 * 60 * 60);
        var secs = twoDigits(time % 60);
        time = Math.floor(time / 60);
        var mins = twoDigits(time % 60);
        time = Math.floor(time / 60);
        var hours = twoDigits(time);
        $clock.text(hours + ':' + mins + ':' + secs);
    }

    if (typeof(noServerRun) != 'undefined') {
        serverRunDisable()
    }

    clockTick(true);

    colorLocaleLinks();

    loadCustomAd();

});

function LanguageDetector() {
    function countCharacters(s) {
        var cnt = [];
        for (var i = 0; i < 256; i++) {
            cnt[i] = 0;
        }
        for (var j = 0; j < s.length; j++) {
            var c = s.charCodeAt(j);
            if (c >= 0 && c < 256) {
                cnt[i]++;
            }
        }
        return cnt;
    }

    this.detect = function(s) {
        var cnt = countCharacters(s);
        var len = s.length;
        var lenBf = s.replace(/[^\+\-\.\,\[\]\<\>\:\;\s]/g, '').length;
        if (lenBf / (len + 1) > 0.6 && s.indexOf('#include') < 0) {
            return 'Brainfuck';
        }
        if (s.indexOf('<-') >= 0) {
            return 'R';
        }
        if (s.indexOf('var ') >= 0) {
            return s.indexOf('using ') >= 0 || s.indexOf('Main ') >= 0 || s.indexOf('Console.') >= 0
                ? 'C#'
                : s.indexOf('func ') >= 0 && s.indexOf('package ') >= 0 ? 'Go' : 'JavaScript';
        }
        if (/\bputs\b/.test(s) && /\b\.times|\b.each\b/.test(s)) {
            return 'Ruby';
        }
        if (/with\s+ada/i.test(s)) {
            return "Ada";
        }
        if (/fn\s+main/.test(s)) {
            return "Rust";
        }
        var endLines = s.match(/[\,\;\{][\040\t]*[\n\r]/);
        endLines = endLines !== null ? endLines.length : 0;
        if (endLines / (cnt[10] + 1) > 0.4) {
            if (s.indexOf('#include') >= 0) {
                return 'C/C++';
            }
            if (/main\s*\(\s*String/.test(s)) {
                return 'Java';
            }
            if (cnt['$'.charCodeAt(0)] / len > 0.005) {
                return 'PHP';
            }
            return 'C#';
        } else {
            if (/\([\+]/.test(s)) {
                return 'LISP';
            }
            if (s.indexOf('dim ') >= 0) {
                return 'VB';
            }
            if (s.indexOf('local ') >= 0) {
                return 'Lua';
            }
            return 'Python';
        }
    }
}

var languageDetector = new LanguageDetector();

function toUtf8Array(s) {
    var qCode = "?".charCodeAt(0);
    var res = [];
    for (var i = 0; i < s.length; i++) {
        var c = s.charCodeAt(i);
        if (c < 0x80) {
            res.push(c);
        } else if (c < 0x800) {
            res.push((c >> 6) | 0xC0);
            res.push((c & 0x3F) | 0x80);
        } else if (c < 0xD800 || c > 0xE000) {
            res.push((c >> 12) | 0xE0);
            res.push(((c >> 6) & 0x3F) | 0x80);
            res.push((c & 0x3F) | 0x80);
        } else {
            res.push(qCode);
        }
    }
    return res;
}

function sillyEncode(s) {
    var hiCode = "p".charCodeAt(0);
    var loCode = "k".charCodeAt(0);
    var res = [];
    var a = toUtf8Array(s);
    for (var i = 0; i < a.length; i++) {
        var c = a[i];
        var v = String.fromCharCode(hiCode - (c >> 4)) + String.fromCharCode(loCode + (c & 0xF));
        res.push(v);
    }
    return res.join("");
}
