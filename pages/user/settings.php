<h1>Your Settings</h1>

<br/>
<div class="row">
    <div class="col-xs-6">
        <div class="strong col-xs-12">Your name:</div>
        <?php if ($model->nameForChange) : ?>
        <form method="post" action="<?= url('user_settings_chname') ?>">
            <div class="col-xs-9">
            <input type="text" name="newname" value="<?= $model->nameForChange ?>" class="form-control"/>
            </div>
            <div class="col-xs-3">
            <input type="submit" value="Change" class="btn btn-default"/>
            </div>
            <div class="col-xs-12">
                <span class="hint">if you really want to...<br/> 
                    Name should have 8-31 letters in it (space or underscore is allowed).<br/>
                    <span class="strong">Bishops can have shorter names</span> - even 4 letters...</span>
            </div>
        </form>
        <?php else : ?>
        <div class="col-xs-12">It could not be changed for directly registered accounts
            (i.e. using login/password authentication)
            until <?= $ctx->elems->conf->nameChangeLevel ?> tasks are solved. Sorry. :(</div>
        <?php endif; ?>
    </div>

    <div class="col-xs-6">
        <div class="strong col-xs-12">Your Country:</div>
        <form method="post" action="<?= url('user_settings_country') ?>">
            <div class="col-xs-9">
            <select name="code" class="form-control">
                <?php $countryCode = ($model->country ? $model->country : 'US'); ?>
                <?php foreach ($model->countries as $land) : ?>
                    <?php
                    $default = $land->code === $countryCode ? 'selected="true"' : '';
                    echo "<option value=\"{$land->code}\" $default>{$land->title}</option>";
                    ?>
                <?php endforeach; ?>
            </select>
            </div>
            <div class="col-xs-3">
            <input type="submit" value="Set country" class="btn btn-default"/>
            </div>
        </form>
    </div>
</div>
<br/>

<div class="row">
    <div class="col-xs-9">
        <div class="col-xs-12 strong">Your picture:</div>
        <form method="post" action="<?= url('user_settings_avatar') ?>">
            <div class="col-xs-10">
            <input type="text" name="url" value="<?= $model->avatar ?>" class="form-control"
                size="50" maxlength="200" placeholder="http://.../image.jpg"/>
            </div>
            <div class="col-xs-2">
            <input type="submit" value="Update" class="btn btn-default"/>
            </div>
            <div class="hint col-xs-12">Please provide a link to square JPG image
                between 100*100 and 300*300 pixels</div>
        </form>
    </div>
    <div class="col-xs-3 text-right">
        <?php if (!empty($model->avatar)) : ?>
        <img style="height:100px;" src="<?= $model->avatar ?>"/>
        <?php endif; ?>
    </div>
</div>
<br/>

<div class="row">
    <?php if ($ctx->userService->personalInfoAllowed()) : ?>
    <div class="col-xs-8">
        <div class="strong col-xs-12">Your personal info:</div>
        <form method="post" action="<?= url('user_settings_info') ?>">
            <div class="col-xs-12">
            <textarea name="info" style="width:100%;height:100px;"><?= $model->personalInfo ?></textarea><br/>
            <input type="submit" value="Update" class="btn btn-default"/>
            </div>
        </form>
    </div>
    <div class="hint col-xs-4">
        <div class="col-xs-12">&nbsp;</div>
        Use markdown syntax here. Read the short
        <a href="<?= url('wiki/personal-info') ?>">help on formatting</a>
        if you are not acquainted with it.
        <br/><br/>
        It is a good place to put link to your blog or site as the profile is
        indexed by search engines.
    </div>
    <?php else : ?>
    <div class="col-xs-12">
        <div class="col-xs-12 strong">Your Personal Info:</div>
        <div class="col-xs-12">
        You will be able to update personal info upon solving <?= $ctx->elems->conf->personalInfoLevel ?> problems
        - that is the place to tell few words about your background, interests, occupation, hobbies
        - and put a link to your blog or site.
        </div>
    </div>
    <?php endif; ?>
</div>
<br/>
<hr/>
<div class="row">
    <div class="col-xs-12">
        <div class="strong">Change password</div>
        <?php if ($model->usesPwd) : ?>
        <div class="hint">at least 8 symbols, having letters and digits, please</div>
        <form method="post" action="<?= url('user_settings_password') ?>">
            <div class="col-xs-6">
                <input type="password" name="newpwd" value="" placeholder="New Password"
                    class="form-control" autocomplete="off"/>
            </div>
            <div class="col-xs-6">
                <input type="password" name="reppwd" value="" placeholder="Repeat Password"
                    class="form-control" autocomplete="off"/>
            </div>
            <div class="col-xs-6">
                <input type="password" name="oldpwd" value="" placeholder="Old Password"
                    class="form-control" autocomplete="off"/>
            </div>
            <div class="col-xs-6">
                <input type="submit" value="Change Password" class="btn btn-default"/>
            </div>
        </form>
        <?php else: ?>
        <div class="hint">Your login type doesn't use password - very good :)</div>
        <?php endif; ?>
    </div>
</div>
<br/>
<hr/>
<div class="row">
    <div class="col-xs-12">
        <div class="strong">Change e-mail</div>
        <?php if (!is_null($model->email)) : ?>
        <div class="hint">Please, retype your password for security</div>
        <form method="post" action="<?= url('user_settings_email') ?>">
            <div class="col-xs-5">
                <input type="text" name="newemail" value="<?= $model->email ?>" placeholder="New E-mail"
                    class="form-control"/>
            </div>
            <div class="col-xs-5">
                <input type="password" name="chkpwd" value="" placeholder="Your Password"
                    class="form-control" autocomplete="off"/>
            </div>
            <div class="col-xs-2">
                <input type="submit" value="Change E-mail" class="btn btn-default"/>
            </div>
        </form>
        <?php else: ?>
        <div class="hint">All right, Your login type doesn't use e-mail :)</div>
        <?php endif; ?>
    </div>
</div>
<br/>

<?= $ctx->util->fragment('user_extraconfig') ?>

