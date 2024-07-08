<div class="row advheader wiki-header">
  <div class="col-md-7">
    <h1>User Ranking</h1>
  </div>
  <?= $ctx->util->fragment('adblock') ?>
</div>

<div class="hint">Select <span class="strong">your country</span> in
<?php if ($ctx->auth->loggedUser()) : ?>
<a href="<?= url('user_settings') ?>">your settings page</a>
<?php else : ?>
<span class="strong">your profile</span>
<?php endif; ?>
and raise your flag in this table by solving problems!</div>
<br/>

<div>
<span class="strong">Total Brethren and Sistren (who solved at least one problem): <?= $model->total ?></span>
</div>
<br/>

<?php if ($model->friends === null) : ?>
    <div class="row">
        <form action="<?= url('user_ranking') ?>" method="post" class="inlined col-xs-8">
            <span class="col-xs-5"><?= $ctx->util->fragment('countryselect') ?></span>
            <span class="col-xs-3"><?= $ctx->util->fragment('langselect') ?></span>
            <span class="col-xs-2">
                <a rel="nofollow" href="<?= url('user_ranking', 'clr', '1') ?>" class="btn btn-default">clear</a>
            </span>
            <?php if (is_numeric($model->userid)) : ?>
            <span class="col-xs-2">
                <a rel="nofollow" href="<?= url('user_ranking', 'friends', '1') ?>" class="btn btn-default">View Friends</a>
            </span>
            <?php endif; ?>
        </form>
        
        <div class="col-xs-3 col-xs-offset-1">
            <span class="pull-right">
            <?php if ($model->page > 0) : ?>
                <a title="Prev page" rel="prev" href="<?= url('user_ranking', 'p', $model->page - 1) ?>">&lt;&nbsp;&lt;&nbsp;&lt;</a>
            <?php else : ?>
                &lt;&nbsp;&lt;&nbsp;&lt;
            <?php endif; ?>
            &nbsp;Page #
                <form action="<?= url('user_ranking') ?>" method="post" class="inlined">
                    <input name="pf" type="text" size="3" value="<?= $model->page + 1 ?>" class="centered"/>
                </form>
            &nbsp;
            <?php if ($model->count === count($model->rank)) : ?>
                <a title="Next page" rel="next" href="<?= url('user_ranking', 'p', $model->page + 1) ?>">&gt;&nbsp;&gt;&nbsp;&gt;</a>
            <?php else : ?>
                &gt;&nbsp;&gt;&nbsp;&gt;
            <?php endif; ?>
            </span>
        </div>
    </div>
<?php else : ?>
    <div>
        <a href="<?= url('user_ranking') ?>" class="btn btn-default">Back to full list</a>
    </div>
<?php endif; ?>
<br/>

<table class="table table-striped table-bordered full-width ranking-table">
<tr class="centered">
    <th>#</th>
    <th colspan="2">User</th>
    <th title="preferred language">Language</th>
    <th>Rank <a href="<?= url('wiki', 'param', 'ranks') ?>">?</a></th>
    <th>Enlightenment</th>
    <!-- <th title="Number of recommendations for user's solutions">Authority</th> -->
    <th>Solved</th>
</tr>
<?php if ($model->myRank !== null && $model->myRank->before) :
    $model->curEntry = $model->myRank; ?>
    <?= $ctx->util->fragment('rankline') ?>
    <tr><th>...</th><th>&nbsp;</th><th>...</th><th>&nbsp;</th><th>&nbsp;</th><th>&nbsp;</th><th>&nbsp;</th><th>&nbsp;</th></tr>
<?php endif; ?>
<?php foreach ($model->rank as $rankLine) :
    $model->curEntry = $rankLine; ?>
    <?= $ctx->util->fragment('rankline') ?>
<?php endforeach; ?>
<?php if ($model->myRank !== null && !$model->myRank->before) :
    $model->curEntry = $model->myRank; ?>
    <tr><th>...</th><th>&nbsp;</th><th>...</th><th>&nbsp;</th><th>&nbsp;</th><th>&nbsp;</th><th>&nbsp;</th><th>&nbsp;</th></tr>
    <?= $ctx->util->fragment('rankline') ?>
<?php endif; ?>
</table>
<br/>
