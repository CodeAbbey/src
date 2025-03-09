<?php

if (!$ctx->auth->user()) {
    $ctx->util->changePage('error404');
    return;
}

$ctx->util->plainOutput('');

$userid = $ctx->auth->loggedUser();
$taskid = $ctx->util->paramPost('task');
$star = $ctx->util->paramPost('star');

if (!is_numeric($taskid) || !in_array($star, ['0','1'], true)) {
    echo "badparams";
    return;
}

$tag = "star.$userid";
$starred = $ctx->miscService->getTaggedValue($tag);
echo "already starred: $starred\n";
$starred = $starred ? explode(',', $starred) : [];
$exists = array_search($taskid, $starred);
if ($star) {
    if ($exists !== false) return;
    $starred[] = $taskid;
    echo "added star\n";
} else {
    if ($exists === false) return;
    array_splice($starred, $exists, 1);
    echo "removed star\n";
}
echo "remaining starred: " . json_encode($starred) . "\n";
$ctx->miscService->setTaggedValue($tag,
    $starred ? implode(',', $starred) : null);
