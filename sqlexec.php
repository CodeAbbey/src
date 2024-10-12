<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    header('Content-Type: text/plain');
    if (!($_ENV['CA_TEST'] ?? false)) {
        echo 'Oh, you are clever, but this should work only in test setup :)';
        return;
    }
    $output = [];
    $code = -1;
    $input = base64_decode(file_get_contents('php://input'));
    if ($input === false) return;
    $tmpname = @tempnam('.', 'sqlinput');
    file_put_contents($tmpname, $input);
    exec("mariadb -B < $tmpname 2>&1", $output, $code);
    unlink($tmpname);
    echo 'Result: ' . ($code === 0 ? 'OK' : "Fail ($code)") . "\n";
    $output = trim(implode("\n", $output));
    echo $output ? $output : '(no output)';
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
    var output = document.getElementById('output');
    output.innerText = 'waiting for result...';
    setTimeout(function() {
        req.open("POST", "/sqlexec.php", false);
        var data = btoa(document.getElementById('commands').value);
        req.send(data);
        output.innerText = req.responseText;
    }, 100);
}
</script>


