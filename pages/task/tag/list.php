<h1 class="centered">Tags for Problems</h1>

<div class="col-md-8 col-md-offset-2 centered line-height-2">
    <?php foreach ($model->tags as $tag) : ?>
        <a class="tag" href="<?= url('task_list', 'param', $tag->title) ?>"><?= $tag->title ?></a>
    <?php endforeach; ?>
    <br/>
    <span id="tagcloudref" class="hidden">
        <hr/>
        or Play with
        <a class="strong" href="<?= url('task_tag_cloud')?>">Sphere Tag Cloud</a><br/>
    </span>
    <br/>
</div>

<script>
if (typeof(Clouder) == 'function') $('#tagcloudref').removeClass('hidden');
</script>
