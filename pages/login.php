<h3>Greetings</h3>

<?php if ($model->logged) : ?>
<p>You logged in successfully, thank you!</p>
<?php else : ?>

<p>Please enter your credentials:</p>

<form method="post" action="<?=url('login')?>">
    <div class="row">
        <span class="col-xs-3 form-group">
            <input name="username" type="text" class="form-control"
                placeholder="your username" oninput="unameVerify()"/>
        </span>
        <span class="hint col-xs-9">
            *
            <span id="unamelat">Only Latin letters</span>
            <span id="unameund">and probably one underscore</span>,
            <span id="unamelen">from 8 to 15 characters</span> (i.e. "john_smirnoff")
            <br/>
            * Visible name can be changed on reaching level "Bishop"
        </span>
    </div>
    <div class="row">
        <span class="col-xs-3 form-group">
            <input name="password" type="password" class="form-control"
                placeholder="password" oninput="pwdVerify()" autocomplete="off"/>
        </span>
        <span class="hint col-xs-9">
            *
            <span id="pwdltr">Latin letters and digits</span>
            <span id="pwddig">(at least one of both)</span>
            <span id="pwdlen">from 8 to 31 characters</span> (i.e. "bzfra3jop")
        </span>
    </div>
    <div class="row">
        <span class="col-xs-3"><input type="submit" class="btn btn-default" value="Log in"/></span>
        <span class="hint col-xs-9">Password forgotten? If your e-mail is still accessible,
            then <a href="<?= url('user_recover') ?>">recovery page</a> may help...</span>
    </div>
    <hr/>
    
    <div class="row">
        <div class="col-xs-3 form-group">
            <span>To register please fill also these fields:</span><br/><br/>
            <input name="password2" type="password" class="form-control"
                placeholder="password again" autocomplete="off" oninput="regVerify()"/><br/>
            <input name="email" type="text" class="form-control"
                placeholder="your e-mail" oninput="regVerify()"/><br/>
            <input type="submit" class="btn btn-default" value="Register"/>
            <span id="regerror" class="red"></span>
        </div>
        <div class="col-xs-8 col-xs-offset-1">
        <b><span class="red">Reminder:</span> We don't store passwords and emails!</b><br/>
        We keep only their <code>hashes</code>, so that evil doers could not steal passwords or emails from database
        even if it is hacked.<br/>
        Read more in our <a class="strong" href="/index/wiki/privacy-policy">Privacy Policy</a>.
        <br/><br/>
        But anyway it is good to avoid using the
        same password for your important email and bank accounts. Choose some different.
        <br/><br/>
        <span class="strong">We do not send you any emails!</span> and don't require email confirmation.<br/>
        You will need it, however, if you forget your password - you'll be asked to type the same
        address to receive recovery letter.
        <br/><br/>
        You may prefer login via <code>GitHub</code> / <code>Facebook</code> / <code>Gmail</code> - in this case we
        don't need to store password or email at all.<br/>
        Check login buttons at <a href="<?= url('main') ?>">main page</a>.<br/>
        </div>
    </div>
</form>
<br/>
<div class="centered hidden">
    <?= $ctx->util->fragment('loginbuttons') ?>
</div>
<br/>
<script>
function hilightTri(elem, val) {
    if (val !== null) {
        elem.css('color', val ? '#0e0' : '#f44').css('font-weight', 'bold');
    } else {
        elem.css('color', '').css('font-weight', '');
    }
}

function unameVerify() {
    var inp = $('input[name=username]');
    var val = inp.val();
    var len = val.length;
    hilightTri($('#unamelen'), len > 0 ? (len > 7 && len < 16) : null);
    hilightTri($('#unamelat'), len > 0 ? /^[A-Z\_]+$/i.test(val) : null);
    var unds = val.replace(/[^\_]/g, '').length;
    hilightTri($('#unameund'), unds > 0 ? unds == 1 : null);
}

function pwdVerify() {
    var inp = $('input[name=password]');
    var val = inp.val();
    var len = val.length;
    hilightTri($('#pwdlen'), len > 0 ? (len > 7 && len < 32) : null);
    hilightTri($('#pwdltr'), len > 0 ? /^[A-Z0-9]+$/i.test(val) : null);
    hilightTri($('#pwddig'), len > 0 ? /[A-Z]/i.test(val) && /\d/.test(val) : null);
}

function regVerify() {
    var err = $('#regerror');
    var pwd2 = $('input[name=password2]').val();
    if (pwd2.length > 0 && pwd2 != $('input[name=password]').val()) {
        err.text("Passwords don't match");
        return;
    }
    var email = $('input[name=email]').val();
    if (email.length > 0) {
        if (email.length < 5 || email.length > 100) {
            err.text('Queer e-mail length');
            return;
        }
        if (!/^[^\@]+\@[^\@]+\.[^\@]+$/.test(email)) {
            err.text('Wrong e-mail format');
            return;
        }
        if (/[\s\;\(\)\,\'\"\*]/.test(email)) {
            err.text('Weird symbols in email');
            return;
        }
    }
    err.text('');
}
</script>
<?php endif; ?>

