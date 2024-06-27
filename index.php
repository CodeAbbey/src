<?php

function main() {
    global $ctx;

    prepare();

    addFile("ctl/_hookBefore.php");

    $ctlName = preg_replace_callback('/\w+$/', 'capitalizeReplace', $ctx->elems->page);
    $ctlFile = "ctl/$ctlName.php";
    $r = addfile($ctlFile);

    addFile("ctl/_hookPreRender.php");

    if ($ctx->elems->page != null) {
        array_push($ctx->elems->styles, $ctx->elems->page);
        array_push($ctx->elems->scripts, $ctx->elems->page);
        $content = 'pages/' . $ctx->elems->page . '.php';
        ob_start();
        if (!addfile($content)) {
            addfile('pages/error404.php');
        }
        $ctx->elems->contentResult = ob_get_clean();

        ob_start();
        require('layouts/' . $ctx->elems->layout . '.html');
        $rendered = ob_get_clean();
        echo $rendered;
    }

    addFile("ctl/_hookAfter.php");

    destroyModules();
}

function capitalizeReplace($m) {
    return ucfirst($m[0]);
}

function prepare() {
    global $model, $ctx;

    new \module\sys\Elems();
    $model = new \stdClass();

    $ctx = new \module\Context();

    addFile('conf.php');
    addFile('cust_conf.php');

    $ctx->elems->path = preg_replace('/^(.*\/)[^\/]*$/', '$1', $_SERVER['PHP_SELF']);

    $page = isset($_GET['page']) ? $_GET['page'] : 'main';
    $page = preg_replace('/[^a-z0-9\_]/', '', $page);
    $page = str_replace('_', '/', $page);

    $ctx->elems->page = $page;

}


function destroyModules() {
    global $ctx;
    for ($i = sizeof($ctx->elems->moduleOrder) - 1; $i >= 0; $i --) {
        $moduleClass = $ctx->elems->modules[$ctx->elems->moduleOrder[$i]];
        if (method_exists($moduleClass, 'onModuleDestroy')) {
            call_user_func("$moduleClass::onModuleDestroy");
        }
    }
}


function addfile($name) {
    global $model, $ctx;
    if (!file_exists($name)) {
        return false;
    }
    include($name);
    return true;
}


function url($name) {
    global $ctx;
    $path = $ctx->elems->path;
    $rewrite = $ctx->elems->conf->modrewrite;
    $res = $rewrite ? "{$path}index/$name" : "{$path}index.php?page=$name";

    $numArgs = func_num_args();
    if ($numArgs == 3) {
        $args = func_get_args();
        if ($rewrite) {
            $pname = $args[1];
            if ($pname != 'param') {
                $res .= "/{$args[1]}_{$args[2]}";
            } else {
                $res .= "/{$args[2]}";
            }
        } else {
            $res .= "&{$args[1]}={$args[2]}";
        }
    } else if ($numArgs > 3) {
        $i = 1;
        $args = func_get_args();
        $query = array();
        while ($i < $numArgs - 1) {
            if ($args[$i] !== null && $args[$i + 1] !== null) {
                array_push($query, $args[$i] . "=" . $args[$i + 1]);
            }
            $i += 2;
        }
        $res .= ($rewrite ? '?' : '&') . implode('&', $query);
    }
    return $res;
}


function aurl($url) {
    global $ctx;
    return $ctx->elems->path . $url;
}

spl_autoload_register(function($class) {
    $path = str_replace('\\', '/', $class) . '.php';
    if ((@include $path) === false) {
        echo "Loading $class file failed!\n";
    } elseif (!class_exists($class, false) && !interface_exists($class, false)) {
        echo "Loading $class failed!\n";
    }
});

main();
