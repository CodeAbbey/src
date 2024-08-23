<div class="row advheader wiki-header">
    <div class="col-md-7">
        <h2>Mess Hall</h2>
    </div>
    <?= $ctx->util->fragment('adblock') ?>
</div>

<div class="row">

<div class="col-xs-9">
    <div class="strong">Here you can see what is going on at <?= $ctx->elems->conf->projectName ?></div>
    <br/>

    <table class="table table-hover table-condensed">
    <?php foreach ($model->records as $rec) : ?>
    <tr>
        <td class="timestamp"><?= $rec->created ?></td>
        <td>
            <?php if ($rec->avatar != '') : ?>
                <img class="avatar-tiny" src="<?= $rec->avatar ?>" alt="User avatar"/>
            <?php else : ?>
                <span class="avatar-tiny">&nbsp;</span>
            <?php endif; ?>
        </td>
        <td><a href="<?= url('user_profile', 'param', $rec->userurl) ?>">
            <span class="username"><?= $rec->rank ?></span></a></td>
        <td class="message col-xs-6"><?= $rec->message ?></td>
    </tr>
    <?php endforeach; ?>
    </table>
    <br/>
</div>

<div class="col-xs-3">
    <div class="strong centered">Weekly stats</div>
    <br/>
    <table class="table table-bordered table-striped full-width centered">
        <tr class="centered">
            <th>Date</th>
            <th>Solved</th>
        </tr>
        <?php foreach ($model->stats as $st) : ?>
        <tr>
            <td><?= $st->thedate ?></td>
            <td><?= $st->cnt ?></td>
        </tr>
        <?php endforeach; ?>
        <?php if (!empty($model->statsAvg)) : ?>
        <tr>
            <td>Avg15</td>
            <td><?= $model->statsAvg ?></td>
        </tr>
        <?php endif; ?>
    </table>
</div>

<script>
setTimeout(function() {
    location.reload(true);
}, 301000);
</script>
