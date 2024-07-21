<?php

$title = $ctx->util->paramPost('title');
$url = $ctx->util->paramPost('url');
$data = $ctx->util->paramPost('data');

if (!$ctx->auth->admin() || empty($title) || empty($url)) {
    $ctx->util->changePage('error404');
    return;
}

$url = $ctx->strUtils->nameToUrl($url);
$wikipage = $ctx->wikiDao->findFirst("url = '$url'");
if (!is_object($wikipage)) {
    $wikipage = new stdClass();
}

if (trim($data) != 'DELETE') {
    $wikipage->title = $title;
    $wikipage->url = $url;
    $wikipage->data = base64_encode($data);
    $wikipage->lastmod = date('Y-m-d');

    $ctx->wikiDao->save($wikipage);

    $ctx->util->redirect(url('wiki', 'param', $wikipage->url));
} else {
    if ($wikipage->id) {
        $ctx->wikiDao->delete($wikipage->id);
        $ctx->util->flash("Wiki on $url was deleted!");
    } else {
        $ctx->util->flash("Page to delete was not found!");
    }
    $ctx->util->redirect('wiki');
}