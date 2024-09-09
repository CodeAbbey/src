<div class="row advheader wiki-header">
<div class="col-md-7">
<h1><?= $model->task->title ?></h1>
<?php if ($ctx->auth->admin()) : ?>
    <p>
        <a href="<?= url('task_edit', 'id', $model->task->id) ?>">Edit</a>
        &nbsp;
        <a href="<?= url('task_tag_edit', 'param', $model->task->id) ?>">Tags</a>
        &nbsp;
        <a href="<?= url('task_notes', 'id', $model->task->id) ?>">Notes</a>
        &nbsp;
        <a href="<?= url('task_chlng_edit', 'taskid', $model->task->id) ?>">Challenge</a>
        <br/>
        <form action="<?= url('task_locales') ?>" method="post">
            <input name="id" type="hidden" value="<?= $model->task->id ?>"/>
            <input name="values" type="text" placeholder=" Locales"
                value="<?= implode(' ', array_keys($model->task->locales)) ?>"/>
            <input type="submit" value="Update"/>
        </form>
    </p>
<?php endif; ?>

<p class="strong">Problem #<?= $model->task->id ?>
    <br/><br/>Tags:
    <?php foreach ($model->tags as $tag) : ?>
        <a class="tag" href="<?= url('task_list', 'param', $tag) ?>"><?= $tag ?></a>
    <?php endforeach; ?>
</p>

<?php if ($model->challenge) : ?>
    <p class="attention">
        This task has a <span class="strong">Challenge</span> attached.<br/>
        You may
        <a href="<?= url('task_chlng_stats', 'param', $model->task->url) ?>">View Stats</a>
        or read a <a href="<?= url('wiki', 'param', 'challenges') ?>">Help on Challenges</a>.
        <?php if ($model->arena) : ?>
            <br/>Wow! there is also <a class="strong"
                href="<?= url('task_chlng_arena', 'param', $model->task->url) ?>">Arena</a>
                for this challenge!
        <?php endif; ?>
    </p>
<?php else : ?>
    <p><a href="<?= url('task_solvers', 'param', $model->task->url) ?>">Who solved this?</a></p>
<?php endif; ?>
</div>
<?= $ctx->util->fragment('adblock') ?>
</div>

<div>
    <?php if (!empty($model->task->locales)) : ?>
        <?php if (!isset($model->task->locales['en'])) : ?>
            Also available in: 
            <?php foreach ($model->task->locales as $locale => $locName) : ?>
                <a class="locale-<?= $locale ?>" href="<?= url('task_view', 'param', $model->task->url . '--' . $locale) ?>"><?= $locName ?></a>
            <?php endforeach; ?>
        <?php else : ?>
            Back to <a href="<?= url('task_view', 'param', $model->task->url) ?>">English</a> version
        <?php endif; ?>
    <?php else : ?>
        <span class="hint">No translations... yet</span>
    <?php endif; ?>
    <?php if ($ctx->auth->user()) : ?>
        <a class="float-right" href="/index/forum_topic/f4b4fae44cecfb57440c2d227a4cdd84">report typos</a><br/>
    <?php endif; ?>
    <br/>
</div>

<?php if (isset($model->checkerCode)) : ?>
    <div>
        <button class="btn btn-warning" data-toggle="collapse"
            href="#checker-code">Looks like you invented this problem! 
            Click to review the "checker" code...</button>
        <a class="btn btn-success" href="<?= url('task_notes', 'id', $model->task->id) ?>">Notes</a>
    <pre id="checker-code" class="collapse"><code><?= $model->checkerCode ?></code></pre></div>
    <br/>
<?php endif; ?>

<div dir="<?= $model->textDirection ?>"><?= $model->task->statement ?></div>

<?php if (!$ctx->auth->user()) : ?>
    <div class="red">You need to <a href="<?= url('main') ?>">login</a> to get test data and submit solution.</div>
<?php elseif (isset($model->solveUnsolved)) : ?>
    <br/><hr/><br/>
    <div class="centered">
        <span class="red">You have too many unsolved tasks!</span><br/>
        According to rule 177.b you can't solve new problems until you solve
        some of tasks submitted unsuccessfully. Please visit your
        <a href="<?= url('user_unsolved', 'param', $model->solveUnsolved) ?>">
            unsolved task list</a>
        and try solving any of the tasks <span class="red">marked red</span>!
    </div>
<?php else : ?>
<br/><hr/><br/>

<form action="<?= url('task_attempt') ?>" method="post" id="task-attempt">

<input type="hidden" name="taskid" value="<?= $model->task->id ?>"/>

<div><span class="strong">Test data:</span> <span class="hint">copy and paste them as an input for your program</span></div>
<div><textarea id="test-data" class="form-control" style="height:170px"></textarea></div><br/>

<div><span class="strong">Your answer:</span> <span class="hint">paste here an answer from your program</span></div>
<div><input id="answer" name="answer" type="text" value="" class="form-control"/></div><br/>

<?= $ctx->util->fragment('taskview_solcaption') ?>

<input type="hidden" name="b64enc" id="b64enc" value="0"/>
<textarea name="solution" id="solution" class="hidden"></textarea>
<div id="ace-editor" style="height:400px;border:1px solid #888;"></div><br/>

<div class="row">

    <div class="col-xs-7">
        <?php if (!$model->suspended) : ?>
        <input type="submit" value="Submit" class="btn btn-default"/>
        <?php else : ?>
        Sorry, account is suspended for investigation and submissions are not allowed temporarily.
        <?php endif; ?>
    </div>
    
    <?= $ctx->util->fragment('taskview_runtools') ?>
</div>
</form>

<?= $ctx->util->fragment('taskview_badattempt') ?>

<?= $ctx->util->fragment('taskview_errorsmodal') ?>

<?php endif; ?>
<br/>
<div class="lastmod hidden"><?= $model->task->lastmod ?></div>


