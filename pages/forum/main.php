<div class="row advheader wiki-header">
  <div class="col-md-7">
    <h1>CodeAbbey Forum</h1>
  </div>
  <?= $ctx->util->fragment('adblock') ?>
</div>
<br/>


<div class="row">
    <div class="col-xs-6">
        <h2>Branches</h2>
        <table class="table">
            <?php foreach ($model->forums as $forum) : ?>
            <tr><td>
            <h3><a href="<?= url('forum_view', 'param', $forum->url) ?>"><?= $forum->title ?></a></h3>
            <div><?= $forum->info ?></div>
            </td></tr>
            <?php endforeach; ?>
        </table>
    </div>

    <div class="col-xs-5 col-xs-offset-1">
        <h2>Recent</h2>
        <table class="table">
        <?php foreach ($model->topics as $topic) : ?>
        <tr><td>
        <div class="row">
            <a href="<?= url('forum_topic', 'param', $topic->url) . '#lastPost' ?>" class="strong"><?= $topic->title ?></a>
        </div>
        <div class="row">
            <span class="col-xs-offset-2">at
                <a href="<?= url('forum_view', 'param', $topic->forum->url) ?>">
                    <?= $topic->forum->title ?>
                </a>
            </span>
        </div>
        <div class="row">
            <span class="col-xs-offset-3 hint">posts: <?= $topic->posts ?>, &nbsp;&nbsp; last at <?= $topic->lastpost ?></span>
        </div>
        <div class="row">
            <span class="col-xs-offset-4">author: <?= $topic->user->username ?></span>
        </div>
        <div class="row">
            <span class="col-xs-offset-5">last: <?= $topic->lastUser->username ?></span>
        </div>
        </td></tr>
        <?php endforeach; ?>
        </table>
    </div>
</div>

