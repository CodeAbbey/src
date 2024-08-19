<?php

$url = $ctx->util->paramPost('url');
$userid = $ctx->auth->loggedUser();

if (empty($userid)) {
    $ctx->util->changePage('error404');
    return;
}

$check = function($url) {
    if (preg_match('/^http[s]?\:\/\/[a-zA-Z0-9\-\_\.\/]+$/', $url) != 1) {
        return "Invalid URL!";
    }
    $image = @getimagesize($url);
    if ($image === false) {
        return "Image loading failed";
    }
    if ($image[0] < 100 || $image[1] < 100) {
        return "Image is too small - min 100px";
    }
    if ($image[0] > 300 || $image[1] > 300) {
        return "Image is too large - max 300px";
    }
    if ($image[2] != IMAGETYPE_JPEG && $image[2] != IMAGETYPE_JPEG2000 && $image[2] != IMAGETYPE_PNG) {
        return "Image should be JPEG or PNG " . $image[2];
    }
    return "";
};

if (!empty($url)) {
    $message = $check($url);
} else {
    $message = "";
    $url = "";
}

if ($message == "") {
    $userdata = $ctx->userDataDao->findFirst("userid = $userid");
    $userdata->avatar = $url;
    $ctx->userDataDao->save($userdata);
    $ctx->util->flash("OK!");
} else {
    $ctx->util->flash($message);
}

$ctx->util->redirect("user_settings");

