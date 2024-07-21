<?php

if (!$ctx->auth->admin()) {
    $ctx->util->changePage('error404');
    return;
}

$tag = $ctx->util->paramPost('tag');
$taskid = $ctx->util->paramPost('task');

$returnLink = url('task_tag_edit', 'param', $taskid);

$tag = strtolower(trim($tag));

if (!preg_match('/^[a-z]+(?:\-[a-z0-9]+)*$/', $tag)) {
    $ctx->util->redirect($returnLink);
    $ctx->util->flash("Bad tag title");
    return;
}

$existing = $ctx->tagsDao->findFirst("title = '$tag'");
if (is_object($existing)) {
    $cnt = $ctx->taskTagsDao->getCount("tagid = {$existing->id}");
    if ($cnt > 0) {
        $ctx->util->flash("Some tasks have this tag");
    } else {
        $ctx->tagsDao->delete($existing->id);
        $ctx->util->flash("Removed: {$existing->title}");
    }
} else {
    $entity = new \stdClass();
    $entity->title = $tag;
    $ctx->tagsDao->save($entity);
    $ctx->util->flash("Added: {$entity->title}");
}

$ctx->util->redirect($returnLink);

