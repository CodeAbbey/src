<div class="row advheader wiki-header">
    <div class="col-md-7">
        <h2>Who solved <a href="<?= url('task_view', 'param', $model->task->url) ?>">
                <?= $model->task->title ?></a>?</h2>
    </div>
    <?= $ctx->util->fragment('adblock') ?>
</div>

<div class="row">
    <div class="col-xs-4">
        <p class="strong">View solutions of several random people</p>
        <form>
        <div class="row">
        <span class="col-xs-7">
            <?= $ctx->util->fragment('langselect') ?>
        </span>
        </div>
        </form>
        <br/>
        <?php foreach ($model->users as $k => $u) : ?>
        <a href="<?= url('task_solution', 'task', $model->task->url, 'user', $u->url, 'lang', urlencode($u->language)) ?>" class="btn btn-default">
        <?= $u->username . ' (' . $u->language . ')' ?></a>
        <?php endforeach; ?>
    </div>
    <div class="col-xs-8">
        <p class="strong">Notes on the problem</p>
        <?= !empty($model->notes) ? $model->notes : 'Sorry, no editorial on this task is yet prepared...' ?>
    </div>
</div>
<br/>
<p class="hint">parameters 'withblanks' and 'limit' may be useful</p>
<br/>
