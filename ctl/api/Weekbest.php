<?php

$best = $ctx->userService->topOfWeek();

$res = '';
foreach ($best as $entry) {
    $url = url('user_profile', 'param', $entry->url);
    $res .= "<tr><td><a href=\"$url\">{$entry->username}</a></td>"
        . "<td>{$entry->rank}</td><td>{$entry->cnt}</td></tr>\n";
}

header('Cache-Control: max-age=1200');
$ctx->util->plainOutput($res);
