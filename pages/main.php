<div class="row">
    <div class="col-md-8 col-sm-12 centered">
        <div class="strong">
            <br/>We believe that three things lead to success:<br/>
                Practice, Practice and Practice!
        </div>
        <h1 class="centered">
            <a href="https://www.codeabbey.com" class="link-image">
                <img class="full-width" src="<?= aurl('img/facade.gif') ?>" alt="CodeAbbey - programming problems"/>
            </a>
        </h1>
        <div class="visible-sm visible-xs centered">
            <?= $ctx->util->fragment('mainlogin') ?>
        </div>
        <div class="centered">
            <em>Everyone can win
            <strong class="red">Free Programming Certificate</strong> here<br/>
            How this works, what is inside?
            <a href="https://www.youtube.com/watch?v=c6WWZe12ves">Watch
                author's demo</a>!</em>
            <br/><br/><strong>Latest problems</strong><br/>
            <?php foreach ($model->lastTasks as $task): ?>
                <a href="<?= url('task_view', 'param', $task->url) ?>">
                    <?= $task->title ?></a>
                    <span class="hint">#<?= $task->id ?></span><br/>
            <?php endforeach; ?>
            <br/>
            <span>
                <a href="https://en.wikipedia.org/wiki/National_University_of_Colombia" target="_blank">
                    <img class="uni-logo" src="https://i.imgur.com/HnXBzHG.png" title="National University of Colombia" alt="National University of Colombia logo"/></a>
                <a href="https://en.wikipedia.org/wiki/State_University_of_Jakarta" target="_blank">
                    <img class="uni-logo" src="https://i.imgur.com/knJssh2.png" title="State University of Jakarta" alt="State University of Jakarta logo"/></a>
                - we proudly collect logos of the Universities which actively use CodeAbbey
            </span>
        </div>
    </div>
    <div class="col-md-4 hidden-sm hidden-xs centered">
        <?= $ctx->util->fragment('mainlogin') ?>
        <!--
        <div class="centered">
            <img src="https://codeabbey.github.io/data/fir-tree.gif"
                alt="fir tree with leds" class="full-width"/>
        </div>
        -->
        <?= $ctx->util->fragment('mainrank') ?>
        <?= $ctx->util->fragment('mainlastforum') ?>
    </div>
</div>
<br/>
