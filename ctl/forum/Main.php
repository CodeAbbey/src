<?php

$model->forums = $ctx->forumsDao->makeLookup();

foreach ($model->forums as $forum) {
    $forum->info = $ctx->markdown->parse(base64_decode($forum->info));
}

$model->topics = $ctx->forumService->recentList($model->forums);

$ctx->elems->title = "Forums";
$ctx->elems->description = 'Dicussion forum about solving programming problems and learning to code';
$ctx->elems->analytics = true;

