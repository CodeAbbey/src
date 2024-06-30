<?php

$ctx->auth->logout();

session_unset();

$ctx->util->flash('Logout successful. See you later!');
$ctx->util->redirect('main');
