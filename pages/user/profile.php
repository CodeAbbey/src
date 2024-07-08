<div class="row advheader wiki-header">
    <div class="col-md-7">
        <h3>User<span title="<?= $model->user->id ?>">:</span>
            <?= $model->user->username ?></h3>
        <p class="strong">Position: #<?= $model->data->rankpos ?>
            (among top <?
                 $top_perc = $model->data->rankpos * 100 / $model->total;
                 echo $top_perc > 10 ? ceil($top_perc) : number_format($top_perc, 1); ?>%)<br/>
        Rank <a href="<?= url('wiki', 'param', 'ranks') ?>">?</a>: <?= $model->data->rank ?></p>
    </div>
    <?= $ctx->util->fragment('adblock') ?>
</div>


<table class="full-width"><tr>
    <td>
        <?php if ($model->data->avatar != "") : ?>
        <div>
            <img height="200" src="<?= $model->data->avatar ?>" alt="User avatar"/>
        </div>
        <br/>
        <?php endif; ?>

        <p>Tasks solved: <?= $model->data->solved ?>
        <br/>of them for certificate: <?= count($model->c1tagged) ?>
        <?php if (isset($model->data->cheatcnt) && $model->data->cheatcnt >= 10) : ?>
            <br/>
            <span class="red strong">This account is suspended and under investigation for
                suspicious activity / unsporting behavior.</span>
            <a class="strong" href="<?= url('wiki', 'param', 'unsporting-behavior') ?>">What is this, what to do???</a>
        <?php endif; ?>
        </p>

        <?php if ($model->user->socialurl) : ?>
        <p><a href="<?= $model->user->socialurl ?>">See page at <?= $model->user->socialnet ?></a></p>
        <?php endif; ?>

        <?php if ($model->country) : ?>
            <p>Country: <?= $model->country ?></p>
        <?php endif; ?>

        <?php if ($ctx->auth->admin()) : ?>
            <p>Lid: <?= $model->user->loginid ?></p>
            <p>Failed: <?= $model->data->failed ?></p>
            <p>Ccnt: <a href="<?= url('tools_cheatcnt', 'user', $model->user->url, 'cnt', $model->data->cheatcnt) ?>" target="_blank">
                <?= $model->data->cheatcnt ?></a></p>
        <?php endif; ?>

        <p>
        <span class="strong">Relations</span> &nbsp; (<a href="<?= url('user_friends', 'param', $model->user->url) ?>">details</a>)<br/>
        Is befriended by <?= $model->followedByCount ?> users<br/>
        and made friendship with  <?= $model->followingCount ?> users<br/>
        <?php $befriendUrl = url('user_befriend', 'user', $model->user->url); ?>
        <?php if ($model->friendType) : ?>
            Is your <?= $model->friendType < 0 ? 'secret ' : '' ?>friend
            (<a href="<?= $befriendUrl ?>">break</a>)
        <?php elseif ($model->friendType === 0) : ?>
            Add to friends:
            <a href="<?= $befriendUrl ?>">public</a> or
            <a href="<?= url('user_befriend', 'secret', $model->user->url) ?>">secret</a>
        <?php endif; ?> 
        </p>

        <p>Registered: <?= $model->data->created ?><br/>
        Last visit: <?= $model->data->lastlogin ?></p>

        <br/>
        <div>
            <?php if ($model->isCurrentUser) : ?>
            <span class="hint">embed this to your site (<a 
href="/index/wiki/embed-your-banner">how?</a>):</span><br/>
            <?php endif; ?>
            <a href="<?= $model->bannerUrl ?>">
                <img alt="User banner" src="<?= $model->bannerUrl ?>"/>
            </a>
        </div>
        <br/>

        <div style="height:4px">&nbsp;</div>
    </td>
    <td>
        <?php if (!empty($model->personalInfo)) : ?>
            <div style="width:650px;height:250px;overflow:scroll;border:1px solid #ccc;padding:5px;display:inline-block;float:right;">
                <?= $model->personalInfo ?>
            </div>
        <?php else : ?>
            <!--googleoff: index-->
            <span class="hint">
                User have not filled his personal info still.<br/>
                If you are acquainted - please ask him / her to do so!
            </span>
            <!--googleon: index-->
        <?php endif; ?>
    </td>
</tr></table>

<div class="row">
    <div class="col-xs-4 col-xs-offset-4 profile-diplomas centered">
        <span class="strong">Awards, Diplomas etc (<a href="<?= url('wiki', 'param', 'certificates') ?>">how to win them?</a>):</span><br/><br/>
        <?php if (empty($model->awards)) : ?>
        <span class="hint">Currently nothing here :(</span>
        <?php endif; ?>

        <?php foreach ($model->awards as $award) : ?>
            <a href="<?= url('cert_check', 'param', $award->nr) ?>"><?= $award->title ?></a> granted on <?= $award->awarded ?>
            <?php if ($award->revoked) : ?>
                <span class="strong red">revoked</span>
            <?php endif; ?>
            <br/>
        <?php endforeach; ?>
    </div>
</div>

<?php if ($model->authored) : ?>
<hr/>
<div class="row">
<div class="col-xs-6 col-xs-offset-3">
<h3 class="centered">Authored problems</h3>
<table class="table table-striped centered">
  <tr class="centered"><th>#</th><th>Title</th><th>Solved</th></tr>
  <?php foreach ($model->authored as $atask) {
    echo "<tr><td>{$atask->id}</td><td><a href=\"" . url('task_view', 'param', $atask->url)
        . "\">{$atask->title}</a></td><td>{$atask->solved}</td></tr>\n";
  } ?>
</table>
</div>
</div>
<?php endif; ?>

<hr/>

<div class="centered">
<span class="strong">Solutions list</span><br/>
(<a href="<?= url('user_unsolved', 'param', $model->user->url) ?>">view saved unsuccessful solutions</a>)<br/>
<span class="hint">Note: you can not view solutions of tasks which you have not yet solved yourself!</span>
</div>

<div class="row">
<div class="col-xs-8 col-xs-offset-2">
<table class="table table-striped full-width centered">
    <tr class="centered">
        <th>#</th>
        <th><span class="pull-left">Title</span></th>
        <th>Language</th>
        <th>Solution</th>
        <th>Date</th>
    </tr>
<?php foreach ($model->tasks as $task) : ?>
    <tr>
        <td <?= in_array($task->taskid, $model->c1tagged)
                ? 'class="strong"' : ''?>>
            <?= $task->taskid ?>
        </td>
        <td>
            <a href="<?= url('task_view', 'param', $task->url) ?>" class="pull-left">
                <?= $task->title ?>
            </a>
        </td>
        <td>
            <?= $task->language ?>
        </td>
        <td>
            <a href="<?= url('task_solution', 'task', $task->url, 'user', $model->user->url, 'lang', urlencode($task->language)) ?>" rel="nofollow">view</a><br/>
        </td>
        <td title="<?= $task->ts2 ?>">
            <?= $task->ts ?>
        </td>
    </tr>
<?php endforeach; ?>
</table>
</div>
</div>
<br/>

