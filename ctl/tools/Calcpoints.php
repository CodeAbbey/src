<?php

if (!$ctx->elems->conf->calcPointsSecret
        || $ctx->util->paramGet('param') != $ctx->elems->conf->calcPointsSecret) {
    $ctx->util->changePage('error404');
    return;
}

$timeStart = microtime(true);

$ctx->util->plainOutput("Calculations: \n");

$taskPoints = $ctx->miscService->calcPoints();

$timeEnd = microtime(true);
$deltaTime = number_format(($timeEnd - $timeStart), 3);

echo implode('', $taskPoints);
echo "\nPoints Calculation completed in $deltaTime seconds\n";
