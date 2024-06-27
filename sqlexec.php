<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    header('Content-Type: text/plain');
    $output = [];
    $code = -1;
    $input = file_get_contents('php://input');
    exec("echo -n '$input' | base64 --decode | mariadb -B", $output, $code);
    echo implode("\n", $output);
    return;
}

?>

<center>
<h1>MySQL (MariaDB) command executor</h1>

<textarea id="commands" cols="80" rows="10" style="font-family:monospace;font-weight:bold;"></textarea><br/><br/>

<p>Enter some commands, separated by semi-colon. Don't forget start with USE "databasename"
unless you create new database.</p>
<input type="button" value="Execute!" onclick="doExec();"/><br/><br/>

<pre><code id="output"></code></pre>

<script>
function doExec() {
    var req = new XMLHttpRequest();
    req.open("POST", "/sqlexec.php", false);
    var data = btoa(document.getElementById('commands').value);
    req.send(data);
    document.getElementById('output').innerText = req.responseText;
}
</script>


