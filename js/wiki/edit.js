$(function() {

    var editor = CodeMirror.fromTextArea($("#text").get(0), {
        lineNumbers: true,
        mode: "markdown",
        indentUnit: 4,
        indentWithTabs: true,
        enterMode: "keep",
        tabMode: "shift",
    });

    $("input[type=submit]").click(function() {
        $("#text").val(editor.getValue());
        return true;
    });

    function nameChanged() {
        var s = $(this).val();
        s = nameToUrl(s);
        $("input[name=url]").val(s);
    }

    $("input[name=title]").change(nameChanged);

});
