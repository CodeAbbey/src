<h3>Wiki edit</h3>

<form method="post" action="<?= url('wiki_save') ?>">

    <label>Title:<br/>
        <input type="text" name="title" value="<?= $model->wikipage->title ?>" class="full-width"/>
    </label><br/><br/>

    <label>Url:<br/>
        <input type="text" name="url" value="<?= $model->wikipage->url ?>" class="full-width"/>
    </label><br/><br/>

    <label class="full-width">Text: <span class="hint">or "DELETE"</span><br/>
        <textarea id="text" name="data" class="full-width"><?= $model->wikipage->data ?></textarea>
    </label><br/>

    <input type="submit" value="Save"/>
</form>

