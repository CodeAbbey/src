<div class="centered">

<h3>Available Tags</h3>

<p style="width:65%;margin:0px auto;line-height:2.1;">
<?php foreach ($model->tags as $tag) : ?>
<span class="tag"><?= $tag->title ?></span>
<?php endforeach; ?>
</p>
<br/>

<form action="<?= url('task_tag_update')?>" method="post">
    <input type="text" name="tag"/>
    <input type="hidden" name="task" value="<?= empty($model->task) ? '' : $model->task->id ?>"/>
</form>
<span class="hint">new name to add or existing to remove<br/>(type in and press enter)</span>
<br/>

<hr/>

<?php if (!empty($model->task)) : ?>
    <a class="strong" href="<?= url('task_view', 'param', $model->task->url) ?>"><?= $model->task->title ?></a>

    <p>
    <?php foreach ($model->taskTags as $tag) : ?>
    <span class="tag"><?= $tag ?></span>
    <?php endforeach; ?>
    </p>
    
    <form action="<?= url('task_tag_assign')?>" method="post">
        <input type="text" name="tag" autofocus="true"/>
        <input type="hidden" name="task" value="<?= $model->task->id ?>"/>
    </form>
    <span class="hint">new tag to add or existing to remove<br/>(type in and press enter)</span>
    <br/><br/>
<?php else : ?>
    <h3>No task chosen :(</h3>
<?php endif; ?>

</div>
