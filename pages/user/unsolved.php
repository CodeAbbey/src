<div class="row advheader wiki-header">
  <div class="col-md-7">
    <h3>Unsuccessful Solutions</h3>
    <div class="strong">
        Back to profile: <a href="<?= url('user_profile', 'param', $model->userUrl) ?>"><?= $model->userName ?></a>
    </div>
    </div>
    <?= $ctx->util->fragment('adblock') ?>
</div>

<div class="row">
    <div class="col-xs-8 col-xs-offset-2">
        <table class="table table-striped table-bordered full-width ranking-table text-center">
        <tr class="centered">
            <th>#</th>
            <th>Problem</th>
            <th>Language</th>
            <th>Link</th>
            <th>Date</th>
        </tr>
        <?php foreach ($model->records as $record) : ?>
        <tr>
            <td class="<?= $record->neverSolved ? 'red' : ''?>">
                <?= $record->taskid ?></td>
            <td><a href="<?= $record->taskUrl ?>"><?= $record->title ?></a></td>
            <td><?= $record->language ?></td>
            <td><a href="<?= $record->url ?>">view</a></td>
            <td><?= $record->ts ?></td>
        </tr>
        <?php endforeach; ?>
        </table>
    </div>
</div>
