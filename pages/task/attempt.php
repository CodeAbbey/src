<p>Thanks for your attempt to solve task #<?= $model->task->id ?> &dash; <strong><?= $model->task->title?></strong></p>

<h3>Your answer is <span class="<?= $model->solved > 0 ? 'green' : 'red' ?>"><?= $model->solved > 0 ? 'Correct' : 'Wrong' ?></span></h3>

<?php if (!empty($model->answer)) : ?>
<hr/>
<div>Expected answer was:<br/>
    <textarea class="full-width"><?= $model->answer ?></textarea>
</div>
<div class="hider">
    <span><a href="#" class="click-to-see">Click</a> to view answer you submitted...</span><br/>
    <span class="hidden">Your answer was:</span>
    <textarea class="full-width hidden base64-encoded"><?= $model->submittedAnswer ?></textarea>
</div>
<div class="hider">
    <span><a href="#" class="click-to-see">Click</a> to view input data if you haven't saved it...</span><br/>
    <span class="hidden">Your input data:</span>
    <textarea class="full-width hidden"><?= $model->inputData ?></textarea>
</div>
<script>
$('.base64-encoded').each(function(idx, elem) {
    var s = $(elem).val();
    $(elem).val(atob(s));
});
</script>
<hr/>
<?php endif; ?>

<?php if ($model->solved) : ?>
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
                echo "<a class=\"strong red\""
                    ." href=\"$nextUrl\">{$next->title}</a><br/>";
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
<?php endif; ?>

<p>
<?php if (!$model->solved) : ?>
You may <a class="strong" href="<?= url('task_view', 'param', $model->task->url) ?>">TRY AGAIN</a>
  or return 
<?php endif; ?>
<a href="<?= url('task_list') ?>">Back to task list</a></p>

<br/>
<p>Visit <a class="strong" href="<?= url('forum_view') ?>">Our Forum</a> if you have any questions or
suggestions about problem statement or suspect the checker is
incorrect.</p>

<?php if ($model->solved) : ?>
<hr/>

<h3>Author's notes on this problem</h3>

<div><?= !empty($model->notes) ? $model->notes : 'Sorry, no notes were added yet for this task...' ?></div>

<br/>
<?php else : ?>
<div class="hint">
Author's notes on the problem may be shown here when problem is solved :)
</div>
<?php endif; ?>

<script>
var newSolved = <?= $model->solved && $model->gainedPoints > 0 ? 'true' : 'false' ?>;
var taskId = <?= $model->task->id ?>;
var numSolved = <?= $model->numSolved ?>;
var userRnd = <?= $model->userRnd ?>;
</script>

<?php if (!$ctx->auth->admin() && empty($ctx->elems->conf->debug)) : ?>
<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-44791471-1', 'codeabbey.com');
  ga('send', 'event', 'submission', newSolved ? 'newsolved' : 'other', '' + taskId);
</script>
<?php endif ?>

