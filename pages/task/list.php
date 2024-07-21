<div class="row advheader wiki-header">
<div class="col-md-7">
<h1><?= $model->title ?></h1>
<div class="hint">Note that you can solve most task in any order!<br/>
Already solved ones are at the bottom of the table.<br/>

<? $ctx->util->fragment('tasklist_notice') ?>
</div>
</div>
<?= $ctx->util->fragment('adblock') ?>
</div>
<br/>

<div class="row">
    <div class="col-sm-8">
    <?php if ($model->addingAllowed) : ?>
        <a href="<?= url('task_edit') ?>">Add new task</a>
    <?php endif; ?>
    <?php if (empty($model->filterTag)) : ?>
        <a href="/index/wiki/volumes">Volume</a>:
        <div class="btn-group" role="group" aria-label="Task Volumes">
            <?php foreach ($ctx->elems->conf->taskVolumes as $tag => $volume) : ?>
            <a class="btn btn-default"
                href="<?=url('task_list', 'param', $tag)?>"><?= $volume ?></a>
            <?php endforeach; ?>
        </div>
        &nbsp;&nbsp;&nbsp;
        <a class="btn btn-default"
            href="<?= url('task_tag_list') ?>">all tags</a>
    <?php else : ?>
        <?php if ($model->volume !== null) : ?>
            Volume: <span class="strong"><?= $model->volume ?></span>
        <?php else : ?>
            Tagged with: <span class="strong"><?= $model->filterTag ?></span>
        <?php endif; ?>
        - total <?= count($model->tasks) ?>
        &nbsp;&nbsp;
        <a href="<?= url('task_list') ?>">(clear filter)</a>
    <?php endif; ?>
    </div>
    <div class="col-sm-3 col-sm-offset-1 row">
        <select id="task-sorter" class="form-control" data-sort="<?= $model->sort ?>">
            <option value="tre1">Trending</option>
            <option value="num1">Sort by Num of Solvers</option>
            <option value="id1">Sort by Creation Order</option>
            <option value="num0">by Solvers (with solved)</option>
            <option value="id0">by Creation (with solved)</option>
        </select>
        <script>
        $(function() {
            var sorter = $('#task-sorter');
            sorter.val(sorter.attr('data-sort')).change(function() {
                location.href = location.href.replace(/\?.*$/, '') + '?sort=' + $(this).val();
            })
        });
        </script>
    </div>
</div>
<br/>

<table class="table table-striped table-bordered table-condensed table-hover full-width centered">
<tr class="centered">
    <th>Id</th>
    <th>Title</th>
    <th class="col-sm-2">Translations</th>
    <th class="col-sm-2 hidden-xs">Author</th>
    <th class="col-sm-1">Solved</th>
    <th class="col-sm-1 hidden-xs">Blessing</th>
    <?= $model->isUser ? '<th>Status</th>' : '' ?>
</tr>
<?php
foreach ($model->tasks as $task) {
    $state = $task->state;
    $statusCell = $model->isUser ? "<td class=\"$state\">$state</td>" : '';
    $editLink = $model->addingAllowed ? " <a href=\"{$task->editurl}\">edit</a>" : '';
    $authorLink = $task->author
        ? '<a href="' . url('user_profile', 'param', $task->author) . "\">{$task->author}</a>"
        : '';
    $solved = $task->solved;
    if ($state === 'solved' || ($model->addingAllowed && is_numeric($solved))) {
        $solved = '<a href="' . url('task_solvers', 'param', $task->shortUrl) . "\">$solved - view</a>";
    }
    $isc1 = isset($model->c1ids[$task->id]) ? 'class="strong"' : '';
    echo "<tr><td $isc1>{$task->id}</td><td><a href=\"{$task->url}\">{$task->title}</a>$editLink</td>";
    echo "<td>{$task->translations}</td>";
    echo "<td class=\"hidden-xs\">$authorLink</td>";
    echo "<td>$solved</td><td class=\"hidden-xs\">{$task->cost}</td>$statusCell</tr>";
}
?>
</table>
<br/>
