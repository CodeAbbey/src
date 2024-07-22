<div class="row advheader wiki-header">
  <div class="col-md-7">
    <h2>Viewing Solution</h2>

<?php if (is_object($model->solution)) : ?>
    <span class="strong">Task:</span>
    <a href="<?= url('task_view', 'param', $model->solution->taskUrl) ?>"><?= $model->solution->taskTitle ?></a>
    (#<?= $model->solution->taskId ?>)
    <?php if ($model->key) : ?>
    <span class="float-right">(<a title="1-week active"
        href="<?= url('task_solution', 'task', $model->solution->taskUrl, 'user', $model->solution->userUrl, 'lang', $model->solution->language, 'key', $model->key)?>">cute shareable link</a>)</span>
    <?php endif; ?>
    <br/>
    <span class="strong">User:</span>
    <a href="<?= url('user_profile', 'param', $model->solution->userUrl) ?>"><?= $model->solution->userName ?></a>
    <br/>
    <span class="strong">Date:</span>
    <span><?= $model->solution->ts ?></span>
    <br/>
    <span class="strong">Language:</span>
    <?php if (!$model->changeLangAllowed) : ?>
        <span><?= !empty($model->solution->language) ? $model->solution->language : '' ?></span>
    <?php else : ?>
        <form method="post" action="<?= url('task_changelang') ?>" class="inlined">
            <?= $ctx->util->fragment('langselect') ?>
            <input type="hidden" name="task" value="<?= $model->solution->taskUrl ?>"/>
            <input type="hidden" name="user" value="<?= $model->solution->userUrl ?>"/>
            <input type="hidden" name="oldlang" value="<?= $model->solution->language ?>"/>
        </form>
    <?php endif; ?>
<?php endif; ?>

  </div>
<?= $ctx->util->fragment('adblock') ?>
</div>

<?php if (!$model->solution->error) : ?>

    <?php if ($model->highlight != 'other'): ?>
    <script>hljs.initHighlightingOnLoad();</script>
    <?php endif; ?>

    <pre><code style="font-weight:normal" class="<?= $model->highlight ?>"><?= $model->solution->code ?></code></pre>

    <div class="hint">Length: <?= strlen($model->solution->code) ?></div>
    <br/>

<?php if ($ctx->auth->admin()) : ?>
    <form action="<?= url('tools_solsearch') ?>" target="_blank">
        <input type="text" name="taskid" value="<?= $model->solution->taskId ?>" size="5"/>
        <input type="text" name="snippet"/>
        <input type="submit" value="search"/>
    </form>
<?php endif; ?>

<?php else : ?>
    <p class="error">Error: <?= $model->solution->error ?></p>
<?php endif; ?>
