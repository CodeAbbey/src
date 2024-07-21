<div class="row advheader wiki-header">
  <div class="col-md-7">
    <a href="<?= url('wiki', 'param', 'contents') ?>">Contents</a>
    <?php if ($ctx->auth->admin()) : ?>
      <a href="<?= url('wiki_edit', 'url', $model->url) ?>">Edit</a>
      <a href="<?= url('wiki_edit') ?>">Add new article</a>
    <?php endif; ?>
    <br/><br/>
    <h1><?= $model->title ?></h1>
  </div>
  <?= $ctx->util->fragment('adblock') ?>
</div>

<?= $model->text ?>

<div class="lastmod hidden"><?= $model->lastmod ?></div>

