<?php

$model->logged = ($ctx->auth->loggedUser() !== null);
$model->lastForum = [];
$model->lastTasks = [];