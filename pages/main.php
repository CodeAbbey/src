<div class="row">
    <div class="col-md-8 col-sm-12 centered">
        <div class="strong"><br/><?= $ctx->elems->conf->motto ?></div>
        <?= $ctx->util->fragment('main_image') ?>
        <div class="visible-sm visible-xs centered">
            <?= $ctx->util->fragment('main_login') ?>
        </div>
        <div class="centered">
            <?= $ctx->util->fragment('main_middlenote') ?>
            <?= $ctx->util->fragment('main_lasttasks') ?>
            <?= $ctx->util->fragment('main_bottomnote') ?>
        </div>
    </div>
    <div class="col-md-4 hidden-sm hidden-xs centered">
        <?= $ctx->util->fragment('main_login') ?>
        <?= $ctx->util->fragment('main_rank') ?>
        <?= $ctx->util->fragment('main_lastforum') ?>
    </div>
</div>
<br/>
