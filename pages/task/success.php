<script>
var taskId = <?= $model->task->id ?>;
var numSolved = <?= $model->numSolved ?>;
var userRnd = <?= $model->userRnd ?>;
</script>

<h3>Your answer is <span class="green">Correct</span>!</h3>

<div class="row">
    <div class="col-xs-7">
        You now are allowed to see
        <a href="<?= url('task_solvers', 'param', $model->task->url) ?>">other's solutions</a>
        of this task!<br/><br/>
        You received <span class="strong"><?= $model->gainedPoints ?></span> points of Blessing!<br/>
        Your <a href="<?= url('wiki', 'param', 'ranks') ?>">Enlightenment</a> is now
        <span class="strong"><?= $model->userPoints ?></span><br/>
        <span class="hint">(these values are approximate - exact ones will be known in few minutes -
            after total recalculation)</span>
        <?php if (count($model->nextTasks) > 0) : ?>
            <br/><span class="strong">Suggested next tasks:</span><br/>
            <?php foreach ($model->nextTasks as $next) :
                $nextUrl = url('task_view', 'param', $next->url);
                echo "<a class=\"btn btn-default\""
                    ." href=\"$nextUrl\">{$next->title}</a> ";
            endforeach;
        endif; ?>
        <span></span>
        <?php if (!empty($model->challengeResult)) : ?>
            <hr/>
            <p><span class="strong">Challenge result:</span><br/>
                &nbsp;&nbsp;&nbsp;
                Score: <?= $model->challengeResult[1] ?><br/>
                &nbsp;&nbsp;&nbsp;
                Notes: <?= $model->challengeResult[2] ?><br/>
                &nbsp;&nbsp;&nbsp;
                <a href="<?= url('task_chlng_stats', 'param', $model->task->url) ?>">view full stats</a>
            </p>
        <?php endif; ?>
        <br/><br/>
        <span id="amusement"></span>
    </div>
    <div class="col-xs-5 photo-frame">
        <span class="hint">this pane is under development</span>
    </div>
</div>

<p>
<a href="<?= url('task_list') ?>">Back to task list</a></p>

<br/>
<p>Visit <a class="strong" href="<?= url('forum_view') ?>">Our Forum</a> if you have any questions or
suggestions about problem statement or suspect the checker is incorrect.</p>

<hr/>

<h3>Author's notes on this problem</h3>

<div><?= !empty($model->notes) ? $model->notes : 'Sorry, no notes were added yet for this task...' ?></div>

<br/>
