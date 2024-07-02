<h3><?= $model->task->id ? "Edit Task #{$model->task->id}" : "Create New task" ?></h3>

<input type="hidden" id="testCheckerPath" value="<?= url('task_testchecker') ?>"/>

<form action="<?= url('task_save') ?>" method="post">

<input type="hidden" name="id" value="<?= $model->task->id ?>"/>

<label>Task Title:</label><br/>
<input type="text" name="title" value="<?= $model->task->title ?>"/><br/><br/>

<label>Url:</label><br/>
<input type="text" name="url" value="<?= $model->task->url ?>"/><br/><br/>

<label>Author:</label><br/>
<input type="text" name="author" value="<?= $model->task->author ?>"/><br/><br/>

<label><input type="checkbox" name="shown" <?= $model->task->shown ? 'checked="true"' : ''?>/>Is Visible</label><br/><br/>

<label>Problem Statement:</label><br/>
<textarea id="statement" name="statement" class="textarea-wide"><?= $model->task->text ?></textarea><br/><br/>

<label>Checker Code:</label><br/>
<textarea id="checker" name="checker" class="textarea-wide"><?= $model->task->checker ?></textarea><br/><br/>

<input id="send-data" type="button" value="Submit"/>

<input id="test-checker" type="button" value="Test Checker"/>

</form>

