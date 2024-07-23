$(function() {

    var langSelect = $("select[name=lang]");
    var taskid = $('input[name=taskid]').val();

    solutionArea = ace.edit('ace-editor');
    solutionArea.setShowPrintMargin(false);
    solutionArea.setFontSize(16);
    solutionArea.getSession().setUseWorker(false);

    window.autoLangDetectorTimer = setInterval(autoLangDetector, 3000);

    hideAnswerAndInputIfNecessary();

    setTimeout(restoreLastSolution, 300);

    function prepareRun() {
        $("#answer").val('');
        var src = solutionArea.getValue();
        $("#solution").val(src);
        return src;
    }

    function autoLangDetector() {
        var lang = langSelect.val();
        var src = solutionArea.getValue();
        if (src.trim) {
            localStorage['task-' + taskid + '-src'] = JSON.stringify({lang:lang, text:src});
        }
        if (lang || src.length < 26 || src.match(/\n/g).length < 3) {
            return;
        }
        var lang = languageDetector.detect(src);
        selectLanguageForDetected(lang);
    }

    function selectLanguage(sel) {
        langSelect.val(sel);
        switchEditorLanguage();
    }

    function selectLanguageForDetected(lang) {
        var opt = langSelect.find('option').filter(function() {
            return $(this).html() == lang;
        });
        selectLanguage(opt.val());
    }

    function switchEditorLanguage() {
        var lang = langSelect.val();
        switch (lang) {
            case "c/c++": lang = "c_cpp";break;
            case "c#": lang = "csharp";break;
            case "java": lang = "java";break;
            case "javascript": lang = "javascript";break;
            case "python": lang = "python";break;
            case "ruby": lang = "ruby";break;
            case "scala": lang = "scala";break;
            case "lua": lang = "lua";break;
            case "php": lang = "php";break;
            case "go": lang = "golang";break;
            case "rust": lang = "rust";break;
            case "julia": lang = "julia";break;
            case "haskell": lang = "haskell";break;
            case "swift": lang = "swift";break;
            case "sql": lang = "sql";break;
            case "perl": lang = "perl";break;
            default: lang = "plain_text";break;
        }
        solutionArea.getSession().setMode("ace/mode/" + lang);
    }

    langSelect.change(switchEditorLanguage);

    $("input[type=submit]").click(function() {
        var taskid = $('input[name=taskid]').val();
        var answer = $("#answer").val();
        var src = solutionArea.getValue();
        if (langSelect.val() == '') {
            var lang = languageDetector.detect(src);
            var detCorrect = confirm(
                lang + '\n\n' + 'The code is in ' + lang + ' - right?\n\n'
                + 'if no, press "Cancel" and choose the language manually (just above the code area)');
            if (!detCorrect) {
                $("#languageLabel").css('background', 'yellow').css('padding', '5px');
                langSelect.focus();
                return false;
            } else {
                selectLanguageForDetected(lang);
            }
        }
        localStorage['lastEditedSolution'] = JSON.stringify({
            task: taskid,
            lang: langSelect.val(),
            text: src,
            ts: new Date().getTime()
        });
        localStorage.removeItem('task-' + taskid + '-src');
        var sol64;
        try {
            sol64 = btoa(src);
        } catch (e) {
            sol64 = btoa(unescape(encodeURIComponent(src)));
        }
        sol64 = sol64.replace('+', '.').replace('/', '_').replace('=', '-');
        sol64 = '-' + sol64.split('').reverse().join('');
        $("#solution").val(sol64);
        $("#b64enc").val('1');
        if (typeof(answerNotNeeded) != 'undefined') {
            if (src == '') {
                alert('You need to submit source code for this task');
                return false;
            }
            if (answer == '') {
                $("#answer").val(' ');
            }
        } else {
            if (answer == '' || src == '') {
                alert('You are to enter both answer and solution source code');
                return false;
            }
        }
        return true;
    });

    function restoreLastSolution() {
        var obj = null;
        var data = localStorage['task-' + taskid + '-src'];
        if (data) {
            obj = JSON.parse(data);
        } else {
            data = localStorage['lastEditedSolution'];
            if (!data) {
                return;
            }
            obj = JSON.parse(data);
            if (!(obj.task == taskid && obj.ts + 10 * 60 * 1000 > new Date().getTime())) {
                return;
            }
        }
        solutionArea.setValue(obj.text);
        solutionArea.selection.moveCursorFileStart();
        langSelect.val(obj.lang);
        switchEditorLanguage();
    }

    $("a.load-code").click(function() {
        var url = $(this).attr('href');
        var lang = url.replace(/.*lang\=/, '').replace('+', ' ');
        $.get(url + '&plain=1', function(data) {
            $("#solution").val(data);
            solutionArea.setValue(data);
            solutionArea.selection.moveCursorFileStart();
            var value = $('select[name=lang] option:contains(' + lang + ')').attr('value');
            $('select[name=lang]').val(value);
            switchEditorLanguage();
        });
        return false;
    });

    function gears() {
        var ans = $("#answer");
        var s = ans.val();
        s += '.';
        ans.val(s);
    }

    function answerFill(value) {
        var ans = $('#answer');
        if (ans.is('input')) {
            ans.val(value.trim());
        } else {
            ans.val(value);
        }
    }

    function inputSrcData(lang) {
        $("#answer").val('');
        var input = $("#test-data").val();
        var src = solutionArea.getValue();
        $("#solution").val(src);
        return {"code": src, "input": input, "lang": lang};
    }

    function localRun(lang, sel) {
        if (sel) selectLanguage(sel);
        var data = inputSrcData(lang);
        var toolsLocalRun = tools + '_localrun';
        try {
            $("#answer").val('Please wait...').focus();
            $.post(toolsLocalRun, data, function(response) {
                answerFill('' + response);
            });
        } catch (e) {
            alert(e.message);
        }
    }

    function remoteRun(lang, sel) {
        if (sel) selectLanguage(sel);
        var data = inputSrcData(lang);
        var sandboxPath = tools + '_sandbox';
        try {
            $("#answer").val('Please wait (up to 20 seconds)').focus();
            var timer = setInterval(gears, 300);
            $.post(sandboxPath, data, function(response) {
                clearInterval(timer);
                if (!/^[A-Z0-9\+\/\=]*.[A-Z0-9\+\/\=]*$/i.test(response)) {
                    $("#answer").val(response);
                    return;
                }
                response = response.split('.');
                answerFill('' + atob(response[0]));
                if (response[1] != '') {
                    $('#errorsModal .modal-body').text(atob(response[1]));
                    $('#errorsModal').modal();
                }
            });
        } catch (e) {
            alert(e.message);
        }
    }

    $("#run-any").click(function() {
        var opts = {
            'c/c++': 'cpp',
            'python': 'py',
            'java': 'java',
            'c#': 'cs',
            'perl': 'pl',
            'lua': 'lua',
            'scheme': 'scm',
            'forth': 'fth',
            'php': 'php',
            'regex': '!re',
            'brainfuck': '!bf',
            'turing': '!turing',
            'basic': '!basic',
            'asm4004': '!i4004',
            'javascript': '#',
            'sql': '#',
        };
        var lang = langSelect.val();
        var runner = opts[lang];
        if (typeof(runner) == 'undefined') {
            alert('Sorry, no executor yet for chosen language :(');
            return;
        }
        if (runner == '#') {
            $('#run-' + lang).click();
        } else if (runner[0] == '!') {
            localRun(runner.substring(1));
        } else {
            remoteRun(runner);
        }
    });

    $("#run-python").click(function() {
        remoteRun('py', "python");
    });

    $("#run-cpp").click(function() {
        remoteRun('cpp', "c/c++");
    });

    $("#run-c").click(function() {
        remoteRun('c', "c/c++");
    });

    $("#run-java").click(function() {
        remoteRun('java', "java");
    });

    $("#run-cs").click(function() {
        remoteRun('cs', "c#");
    });

    $("#run-perl").click(function() {
        remoteRun('pl', "perl");
    });

    $("#run-lua").click(function() {
        remoteRun('lua', "lua");
    });

    $("#run-scheme").click(function() {
        remoteRun('scm', "scheme");
    });

    $("#run-forth").click(function() {
        remoteRun('fth', "forth");
    });

    $("#run-scala").click(function() {
        remoteRun(15, "scala");
    });

    $("#run-php").click(function() {
        remoteRun(7, "php");
    });

    $("#run-go").click(function() {
        remoteRun(21, "go");
    });

    $("#run-regexp").click(function() {
        localRun('re', "regexp");
    });

    $("#run-brainfuck").click(function() {
        localRun('bf', "brainfuck");
    });

    $("#run-turing").click(function() {
        localRun('turing', "turing");
    });

    $("#run-basic").click(function() {
        localRun('basic', "basic");
    });

    $("#run-i4004").click(function() {
        localRun('i4004', "asm4004");
    });

    $("#run-javascript").click(function() {
        selectLanguage("javascript");
        var src = prepareRun();
        try {
            runJsOutput = '';
            eval(src);
            answerFill(runJsOutput);
        } catch (e) {
            alert(e.message);
        }
        inputHolder = null;
    });

    $("#run-sql").click(function() {
        selectLanguage("sql");
        ans = $('#answer');
        var sqlRun = () => {
            ans.val('');
            var db = new sqlite.Database();
            var text = $('#test-data').val() + ';' + solutionArea.getValue()
            var stmts = text.split(';');
            for (var i = 0; i < stmts.length; i++) {
                var line = stmts[i].replace(/^\s+/, '');
                try {
                    if (line.substr(0, 6).toLowerCase() != 'select') {
                        db.run(line);
                    } else {
                        var st = db.prepare(line);
                        while (st.step()) {
                            var row = st.get();
                            ans.val(ans.val() + row.join(',') + ' ');
                        }
                    }
                } catch (e) {
                    ans.val('Probably syntax error');
                }
            }
        };
        if (typeof(sqlite) == 'undefined') {
            ans.val('initializing...');
            initSqlJs({locateFile: (x) => '/js/_sql/' + x})
                .then((s) => {
                    window.sqlite = s;
                    sqlRun();
                });
        } else {
            sqlRun();
        }
    });
});

var inputHolder = null;

function input() {
    if (inputHolder === null) {
        inputHolder = $("#test-data").val().split("\n");
    }
    return (inputHolder.length > 0) ? inputHolder.shift() : null;
}

function output(s) {
    s = '' + s;
    runJsOutput += s;
}

function hideAnswerAndInputIfNecessary() {
    if (typeof(answerHidden) != 'undefined') {
        var elem = $('#answer');
        elem.val(1).parent('div:first').hide().prev('div:first').hide();
    }
    if (typeof(inputHidden) != 'undefined') {
        var elem = $('#test-data');
        elem.parent('div:first').hide().prev('div:first').hide();
    }

}

function openGraphicalAnswerBox(rows) {
    if (typeof(rows) == 'undefined') {
        rows = 5;
    }
    var ans = $('#answer');
    ans.addClass('hidden');
    var box = $('<textarea name="answer" class="form-control"></textarea>');
    box.attr('rows', rows);
    box.insertAfter(ans);
    ans.remove();
    box.attr('id', 'answer');
    box.css('font-family', 'monospace').css('font-weight', 'bold');
    box.css('color', '#0f0').css('background', '#333');
    $(".hint:contains('paste here an answer')").text('stretch it down if needed');
}
