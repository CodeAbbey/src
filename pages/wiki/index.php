<p><a href="<?= url('wiki', 'param', 'contents') ?>">Contents</a>

<?php if ($ctx->auth->admin()) : ?>
    <a href="<?= url('wiki_edit') ?>">Add new article</a>
<?php endif; ?>
</p>

<?php foreach ($model->pages as $page) : ?>
    <div><a href="<?= url('wiki', 'param', $page->url) ?>"><?= $page->title ?></a></div>
<?php endforeach; ?>

<br/>
