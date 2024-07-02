<div class="row advheader wiki-header">
<div class="col-md-7">
<h1><?= $model->task->title ?></h1>
<?php if ($ctx->auth->admin()) : ?>
    <p>
        <a href="<?= url('task_edit', 'id', $model->task->id) ?>">Edit</a>
        &nbsp;
        <a href="<?= url('task_tag_edit', 'param', $model->task->id) ?>">Tags</a>
        &nbsp;
        <a href="<?= url('task_notes', 'id', $model->task->id) ?>">Notes</a>
        &nbsp;
        <a href="<?= url('task_chlng_edit', 'taskid', $model->task->id) ?>">Challenge</a>
        <br/>
        <form action="<?= url('task_locales') ?>" method="post">
            <input name="id" type="hidden" value="<?= $model->task->id ?>"/>
            <input name="values" type="text" placeholder=" Locales"
                value="<?= implode(' ', array_keys($model->task->locales)) ?>"/>
            <input type="submit" value="Update"/>
        </form>
    </p>
<?php endif; ?>

<p class="strong">Problem #<?= $model->task->id ?>
    <br/><br/>Tags:
    <?php foreach ($model->tags as $tag) : ?>
        <a class="tag" href="<?= url('task_list', 'param', $tag) ?>"><?= $tag ?></a>
    <?php endforeach; ?>
</p>

<?php if ($model->challenge) : ?>
    <p class="attention">
        This task has a <span class="strong">Challenge</span> attached.<br/>
        You may
        <a href="<?= url('task_chlng_stats', 'param', $model->task->url) ?>">View Stats</a>
        or read a <a href="<?= url('wiki', 'param', 'challenges') ?>">Help on Challenges</a>.
        <?php if ($model->arena) : ?>
            <br/>Wow! there is also <a class="strong"
                href="<?= url('task_chlng_arena', 'param', $model->task->url) ?>">Arena</a>
                for this challenge!
        <?php endif; ?>
    </p>
<?php else : ?>
    <p><a href="<?= url('task_solvers', 'param', $model->task->url) ?>">Who solved this?</a></p>
<?php endif; ?>
</div>
<?= $ctx->util->fragment('adblock') ?>
</div>

<div>
    <?php if (!empty($model->task->locales)) : ?>
        <?php if (!isset($model->task->locales['en'])) : ?>
            Also available in: 
            <?php foreach ($model->task->locales as $locale => $locName) : ?>
                <a class="locale-<?= $locale ?>" href="<?= url('task_view', 'param', $model->task->url . '--' . $locale) ?>"><?= $locName ?></a>
            <?php endforeach; ?>
        <?php else : ?>
            Back to <a href="<?= url('task_view', 'param', $model->task->url) ?>">English</a> version
        <?php endif; ?>
    <?php else : ?>
        <span class="hint">No translations... yet</span>
    <?php endif; ?>
    <?php if ($ctx->auth->user()) : ?>
        <a class="float-right" href="/index/forum_topic/f4b4fae44cecfb57440c2d227a4cdd84">report typos</a><br/>
    <?php endif; ?>
    <br/>
</div>

<?php if (isset($model->checkerCode)) : ?>
    <div>
        <button class="btn btn-warning" data-toggle="collapse"
            href="#checker-code">Looks like you invented this problem! 
            Click to review the "checker" code...</button>
        <a class="btn btn-success" href="<?= url('task_notes', 'id', $model->task->id) ?>">Notes</a>
    <pre id="checker-code" class="collapse"><code><?= $model->checkerCode ?></code></pre></div>
    <br/>
<?php endif; ?>

<div dir="<?= $model->textDirection ?>"><?= $model->task->statement ?></div>

<?php if (!$ctx->auth->user()) : ?>
    <div class="red">You need to <a href="<?= url('main') ?>">login</a> to get test data and submit solution.</div>
<?php elseif (isset($model->solveUnsolved)) : ?>
    <br/><hr/><br/>
    <div class="centered">
        <span class="red">You have too many unsolved tasks!</span><br/>
        According to rule 177.b you can't solve new problems until you solve
        some of tasks submitted unsuccessfully. Please visit your
        <a href="<?= url('user_unsolved', 'param', $model->solveUnsolved) ?>">
            unsolved task list</a>
        and try solving any of the tasks <span class="red">marked red</span>!
    </div>
<?php else : ?>
<br/><hr/><br/>

<form action="<?= url('task_attempt') ?>" method="post" id="task-attempt">

<input type="hidden" name="taskid" value="<?= $model->task->id ?>"/>

<div><span class="strong">Test data:</span> <span class="hint">copy and paste them as an input for your program</span></div>
<div><textarea id="test-data" class="form-control" style="height:170px"><?= $model->testData ?></textarea></div><br/>

<div><span class="strong">Your answer:</span> <span class="hint">paste here an answer from your program</span></div>
<div><input id="answer" name="answer" type="text" value="" class="form-control"/></div><br/>

<div class="row">
    <div class="col-xs-9">
        <div class="row">
        <span class="strong col-xs-4">Your solution:</span>
        <span class="col-xs-8"><span class="red">Please <b>do not</b></span> post it to github (and other public sites)
            <a target="_blank" href="<?= url('wiki', 'param', 'storing-solutions') ?>">WHY???</a></span>
            <span class="col-xs-4"></span><span class="col-xs-8">
                <span class="red">Also <b>do not</b></span> copy other's code, please! 
                Your progress could be reset for cheating :(</span>
        </div>
        <?php if (!empty($model->codes)) : ?>
            You already have sources (click to load, or right-click and copy url):
            <?php foreach ($model->codes as $lang => $sourceUrl) : ?>
                &nbsp;&nbsp;<a class="load-code" href="<?= $sourceUrl ?>"><?= $lang ?></a>
            <?php endforeach; ?>
            <br/>
        <?php endif; ?>
        <div class="hint">Note: it is not checked automatically, it is saved so you
            can later reuse your code in other problems.<br/>
            You can write <strong>in any language you want</strong>.
            For some of them we have built-in tools (see buttons below on right).</div>
    </div>
    <div id="languageLabel" class="col-xs-3 centered">
        <div>Select Language:</div>
        <select name="lang" class="form-control centered">
            <option value="">Autodetect</option>
            <?php foreach ($model->languages as $langKey => $langVal) : ?>
            <option value="<?= $langKey ?>"><?= $langVal ?></option>
            <?php endforeach; ?>
        </select>
        <input type="button" id="run-any" class="btn btn-success form-control"
            value="Run it!" title="button suggested by Alena S"/>
    </div>
</div>

<input type="hidden" name="b64enc" id="b64enc" value="0"/>
<textarea name="solution" id="solution" class="hidden"></textarea>
<div id="ace-editor" style="height:400px;border:1px solid #888;"></div><br/>

<div class="row">

    <div class="col-xs-7">
        <?php if (!isset($model->suspended)) : ?>
        <input type="submit" value="Submit" class="btn btn-default" 
            data-last="<?= implode(' ', $model->lastSolved) ?>"/>
        <?php else : ?>
        Sorry, account is suspended for investigation and submissions are not allowed temporarily.
        <?php endif; ?>
    </div>

    <div class="col-xs-5">
        <table id="code-tools" class="centered full-width" style="border:1px solid #888">
            <tr><td colspan="3">
                    <span class="strong">Code Running Tools</span><br/>
                    <span class="strong red hidden" id="no-run-warn">
                        This problem could not be run on server<br/>
                        please run locally and copy-paste result and code<br/></span>
                    <a href="<?= url('wiki', 'param', 'running') ?>" target="_blank">How to run your solution?</a>
            </td></tr>
            <tr>
                <td>
                    <input type="button" id="run-python" value="Python" title="Pypy3"/><br/>
                    <input type="button" id="run-cpp" value="C++" title="Gnu (g++)"/><br/>
                    <input type="button" id="run-c" value="C (not ++)" title="Gnu GCC"/><br/>
                    <input type="button" id="run-java" value="Java" title="OpenJDK 16"/><br/>
                    <input type="button" id="run-cs" value="C#" title="Mono C# 6.8"/><br/>
                    <input type="button" id="run-perl" value="Perl" title="Perl 5"/><br/>
                    <input type="button" id="run-lua" value="Lua" title="Lua 5.3"/><br/>
                    <!--<input type="button" id="run-scala" value="Scala"/><br/>
                    <input type="button" id="run-php" value="PHP"/><br/>
                    <input type="button" id="run-go" value="Go"/><br/>
                    <input type="button" id="run-perl" value="Perl"/><br/>-->
                    <span class="hint">run in "sandbox"</span>
                    <br/>
                </td>
                <td>
                    <input type="button" id="run-brainfuck" value="Brainf**k"/><br/>
                    <input type="button" id="run-basic" value="Basic"/><br/>
                    <input type="button" id="run-turing" value="Turing"/><br/>
                    <input type="button" id="run-i4004" value="Asm i4004"/><br/>
                    <input type="button" id="run-regexp" value="RegExp"/><br/>
                    <input type="button" id="run-scheme" value="Scheme" title="TinyScheme-R7"/><br/>
                    <input type="button" id="run-forth" value="Forth" title="pForth"/><br/>
                    <span class="hint">interpreted</span>
                </td>
                <td>
                    <input type="button" id="run-javascript" value="JavaScript" title="Read from 'input()', write to 'output(...)'"/><br/>
                    <input type="button" id="run-sql" value="SQLite"/>
                    <br/>
                    <span class="hint">in browser</span>
                    <br/><br/>
                    <span class="strong">Web Compilers</span><br/>
                    <a href="http://ideone.com" target="_blank">IdeOne.com</a><br/>
                    <a href="http://codepad.org" target="_blank">CodePad.org</a>
                </td>
            </tr>
        </table>
    </div>
</div>
</form>

<!--sandbox errors modal-->
<div class="modal fade" id="errorsModal" tabindex="-1" role="dialog"
    aria-labelledby="errorTitle" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="errorTitle">
                    <span class="strong">Errors happened!</span>
                    <a href="#" class="float-right"
                        data-dismiss="modal">(close)</a></h5>
            </div>
            <div class="modal-body"
                style="font-family:courier,monospace;white-space:pre-wrap;">
            </div>
        </div>
    </div>
</div>

<?php endif; ?>
<br/>
<div class="lastmod hidden"><?= $model->task->lastmod ?></div>


