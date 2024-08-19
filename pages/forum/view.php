<div class="row advheader wiki-header">
  <div class="col-md-7">
    <h1><?= !empty($model->forum->title) ? $model->forum->title : 'Forum of CodeAbbey' ?></h1>
  </div>
<?= $ctx->util->fragment('adblock') ?>
</div>

<?php if ($model->otherForums) : ?>
<p class="strong">Switch to forum:
<?php foreach ($model->otherForums as $other) : ?>
&nbsp;&nbsp;<a href="<?= url('forum_view', 'param', $other->url) ?>"><?= $other->title ?></a>
<?php endforeach; ?>
</p>
<?php else : ?>
<p class="centered"><b>Write what's on your mind:</b> tell about yourself, ask about the problem,
request certificate etc!</p>
<?php endif; ?>

<div class="attention"><?= $model->forum->info ?></div>

<?php if ($model->showCreateLink) : ?>
<p class="centered">
    <a class="btn btn-default"
        href="<?= url('forum_newtopic', 'param', $ctx->elems->conf->singleForum ? 'general' : $model->forum->url)?>">
        Create new topic</a></p>
<?php else : ?>
<p class="hint centered">Creating new topics is allowed after you solve at least 5 problems!</p>
<?php endif; ?>

<table class="table table-bordered table-striped full-width middle-aligned">
    <tr>
        <th class="centered" width="65%">Topic</th>
        <th class="centered">Posts</th>
        <th class="centered">Created</th>
        <th class="centered">Last post</th>
    </tr>
    <?php foreach ($model->topics as $topic) : ?>
    <tr>
        <td class="centered">
            <a href="<?= url('forum_topic', 'param', $topic->url) ?>">
                <?= $topic->title ?></a></td>
        <td class="centered"><?= $topic->posts ?></td>
        <td class="centered"><?= $topic->user->username ?><br/>
            <span class="hint"><?= $topic->created ?></span></td>
        <td class="centered"><?= $topic->lastUser->username ?><br/>
            <span class="hint"><?= $topic->lastpost ?></span></td>
    </tr>
    <?php endforeach; ?>
</table>

<br/>
