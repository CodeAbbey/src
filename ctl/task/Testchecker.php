<?php

if (!$ctx->auth->admin()) {
    $ctx->util->changePage('error404');
    return;
}

$code = file_get_contents("php://input");

echo $ctx->taskService->testChecker($code);

$ctx->util->changePage(null);
