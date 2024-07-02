$(function() {
    
    checkerEdit = CodeMirror.fromTextArea(document.getElementById("checker"), {
        lineNumbers: true,
        matchBrackets: true,
        mode: "application/x-httpd-php",
        indentUnit: 4,
        indentWithTabs: true,
        enterMode: "keep",
        tabMode: "shift"
    });
    problemEdit = CodeMirror.fromTextArea(document.getElementById("statement"), {
        lineNumbers: true,
        matchBrackets: true,
        mode: "markdown",
        indentUnit: 4,
        indentWithTabs: true,
        enterMode: "keep",
        tabMode: "shift"
    });

    function testChecker() {
        var code = checkerEdit.getValue();
        var testCheckerPath = $("#testCheckerPath").val();
        $("[name=checker]").val(code);
        $.post(testCheckerPath, code, function(response) {
            alert(response);
        });
    }
    
    function nameChanged() {
        var s = $(this).val();
        s = nameToUrl(s);
        $("input[name=url]").val(s);
    }
    
    $("input[name=title]").change(nameChanged);
    
    $("#test-checker").click(testChecker);
    $("#send-data").click(function() {
        // copying from codemirror to textare seems to be unnecessary
        // as it happens automatically on submit
        problemEdit.setValue(sillyEncode(problemEdit.getValue()));
        $("[name=statement]").val(problemEdit.getValue());
        checkerEdit.setValue(sillyEncode(checkerEdit.getValue()));
        $("[name=checker]").val(checkerEdit.getValue());
        $(this).parent('form').get(0).submit();
    });

});
