<?php

$model->tags = $ctx->tagsDao->find();

if ($ctx->auth->user()) {
    shuffle($model->tags);
}

$ctx->elems->scripts[] = 'cloud';
$ctx->elems->title = 'Tags for Problems';
$ctx->elems->analytics = true;
