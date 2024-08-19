<div class="row advheader wiki-header">
  <div class="col-md-7">
    <h1 class="forum-header"><?= $model->topic->title ?></h1>
  </div>
  <?= $ctx->util->fragment('adblock') ?>
</div>

<?php if (!empty($model->forTask)) : ?>
<p class="hint">This topic is private for problem
    <a href="<?= url('task_view', 'param', $model->forTask->url) ?>"><?= $model->forTask->title ?></a>
</p>
<?php endif; ?>

<p class="strong">Back to <a href="<?= url('forum_view', 'param', $model->forum->url) ?>"><?= $model->forum->title ?></a> forum</p>

<?php if ($ctx->auth->admin()) : ?>
<div class="right-aligned">
    <a href="<?= url('forum_delete', 'param', $model->topic->url) ?>">Delete this topic</a><br/>
    <form action="<?= url('forum_rename') ?>" method="post">
        <label>Rename or /move: <input type="text" name="newname"/></label>
        <input type="hidden" name="url" value="<?= $model->topic->url ?>"/>
    </form>
</div>
<?php endif; ?>

<?php foreach ($model->posts as $post) : ?>
<div class="forum-post row" <?= isset($post->lastPost) ? 'id="lastPost"' : ''?>>
    <div class="col-xs-11 <?= $post->isauthor ? 'level1' : 'level2 col-xs-offset-1' ?>">
        <div class="comment-head">
            <a href="<?= $post->user->userurl ?>" class="username"><?= $post->user->username ?></a>
            &nbsp;&nbsp;&nbsp;
            <span class="timestamp"><?= $post->created ?></span>
            <?php if (!empty($post->editing)) : ?>
                <a href="<?= $post->editing ?>" title="<?= $post->editingHint ?>">Edit</a>
            <?php endif; ?>
        </div>
        <?php if ($post->user->avatar != '') : ?>
            <img class="avatar-small" alt="User avatar" src="<?= $post->user->avatar ?>"/>
        <?php endif; ?>
        <?= $post->post ?>
    </div>
</div>
<?php endforeach; ?>

<?php if ($model->showForm) : ?>
<?= $ctx->util->fragment('forumform') ?>
<div class="centered">
    <a href="<?= url('forum_summon', 'param', $model->topic->url) ?>"
            class="btn btn-success"
            title="You can invite admin to look into this topic">
        Summon Admin!</a>
</div>
<br/>
<?php else: ?>
<div class="hint centered">Please login and solve 5 problems to be able to post at forum</div><br/>
<?php endif; ?>
