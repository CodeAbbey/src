<?php

if (!$ctx->auth->admin()) {
    $ctx->util->changePage('error404');
    return;
}

$tag = $ctx->util->paramPost('tag');
$taskid = $ctx->util->paramPost('task');

if (!is_numeric($taskid)) {
    $ctx->util->redirect('task_tag_edit');
    $ctx->util->flash("No task chosen!");
    return;
}

$returnLink = url('task_tag_edit', 'param', $taskid);

$tag = strtolower(trim($tag));

$tagObject = $ctx->tagsDao->findFirst("title = '$tag'");

if (!is_object($tagObject)) {
    $ctx->util->redirect($returnLink);
    $ctx->util->flash("No such tag!");
    return;
}

$assignment = $ctx->taskTagsDao->findFirst("tagid = {$tagObject->id} and taskid = $taskid");

if (is_object($assignment)) {
    $ctx->taskTagsDao->delete($assignment->id);
    $ctx->util->flash("Unassigned: $tag");
} else {
    $assignment = new \stdClass();
    $assignment->tagid = $tagObject->id;
    $assignment->taskid = $taskid;
    $ctx->taskTagsDao->save($assignment);
    $ctx->util->flash("Assigned: $tag");
}

$ctx->util->redirect($returnLink);

