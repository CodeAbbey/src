<?php

if (!$ctx->auth->admin()) {
    $ctx->util->changePage('error404');
    return;
}

$id = $ctx->util->paramGet('id');
$vol = $ctx->util->paramGet('vol');

if (is_numeric($id)) {
    $model->task = $ctx->tasksDao->read($id);
    $taskdata = $ctx->taskDataDao->makeLookup('type', "taskid = $id");
    $model->task->title = htmlentities($model->task->title);
    $model->task->text = base64_decode($taskdata['text']->data);
    $model->task->checker = base64_decode($taskdata['checker']->data);
} else {
    $model->task = new stdClass();
    $model->task->id = null;
    $model->task->title = '';
    $model->task->url = '';
    $model->task->author = '';
    $model->task->volumeid = '';
    $model->task->shown = 0;
    $model->task->text = "(problem statement in markdown format, please)";
    $model->task->checker = "<?php\n\nfunction checker() {\n    return array( , );\n}\n\n?>";
}


array_push($ctx->elems->styles, 'codemirror');
array_push($ctx->elems->scripts, '_cm/codemirror');
array_push($ctx->elems->scripts, '_cm/clike');
array_push($ctx->elems->scripts, '_cm/php');
array_push($ctx->elems->scripts, '_cm/markdown');
array_push($ctx->elems->scripts, '_cm/xml');
array_push($ctx->elems->scripts, '_cm/matchbrackets');
