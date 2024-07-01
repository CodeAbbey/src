# Instructions

Here are two parts - one about how it works and another about how to run it. Consult them in any order according
to your needs.

## How it works

To make studying the project easier it could be done in steps. These steps are represented
by certain points (branches) in repository, as more and more functionality are added.

- [Step 1 - framework](https://github.com/CodeAbbey/src/tree/v0.1-framework) - at this point
    here is bare minimum of files, or "framework" which work for page loading. Refer to README
    in this version to learn more.
- [Step 2 - users](https://github.com/CodeAbbey/src/tree/v0.2-users) - now database is enabled
    and functionality of registering users, login, logout is added.
- [Step 3 - htaccess](https://github.com/CodeAbbey/src/tree/v0.3-htaccess) - about some important
    features which are configured outside PHP code

### Step 3 - `.htaccess` files and Rewrite Rules

Websites in PHP generally are run by external web-server. I.e. separate application (often `Apache
HTTPD`) listens to incoming requests and "serves" files to clients. This is different from situation when application itself listens for connections and generate responses to them (typical
in languages like `Go` and `Java` while `Python` may be often found used in both manners).

So before control gets to `PHP` script, there are some action done by the server. And we may want
some management of these actions.

Most obvious example is this: we have many files already - pages, controllers, fragments, modules -
but we don't want the server to allow showing or processing them alone. I.e. if we have
`fragments/menulogin.html` we still do not want server to respond to `http://localhost:8080/fragments/menulogin.html` by blatantly showing the file's content! As we agreed, only `index.php`
in the root folder is executed, all other files are loaded or included by it.

For such needs, server uses `.htaccess` files. They have rich syntax and our goal could be achieved
by several ways. But the simplest is to put such a file with `Deny from all` statement into
subdirectories which should not be processed. You may see that in this step we acutally add several
such files.

**Another thing** is URL rewriting. As we mentioned beforehand by default our framework loads
specific pages by specifying their names in the dedicated parameter, e.g. `/index.php?page=login`.
This is perfectly working, but aesthetic (and perhaps SEO) reasons we would like server to
accept urls like `/index/login`. For this:

- the server should have its `rewrite` module enabled (recreate docker image if you have earlier 
    version)
- `.htaccess` in the root directory should have specific "rules" configured (have a look into it,
    but don't immediately dive into learning them, as it is an optional feature)
- `conf.php` should have `true` for the setting `modrewrite` - this will make `url(...)` functions
    generate links in new style.

### Excercise

Server still allows loading files from the root. Perhaps, find a way to configure that only
`index.php` (and probably `sqlexec.php`) could be opened there.

## How to run

This is a typical `PHP + MySQL` application, so you need an http-server with PHP interpreter and MySQL (MariaDB).

There are **THREE** ways to launch this zoo relatively easily.

### Docker

Docker is something resembling virtual-machine (but not exactly) widely used in industry. We describe
the content of "containers" we need in a file (or few) and docker prepares everything. You'll need to install
docker itself (there could be subtle steps like setting user group in linux but generally it is not much
complicated) - this is available in linux, windows and OsX. Then run files `docker-build.sh` to build an
image containing our `Apache2 httpd + PHP` server with MariaDB in one. then `docker-run.sh` to launch container
from it. If all is fine, the site should be ready at [http://localhost:8080/](http://localhost:8080)

**To initialize database:** open [http://localhost:8080/sqlexec.php](http://localhost:8080/sqlexec.php) and copy-paste content of the file `dbinit.sql` into the textarea, then click `exec`.

### Free (Shared) Hosting

Pick some free hosting, for example [AwardSpace / AtWebpages](https://www.awardspace.com/) and simply copy files there. If you'll get error because `RewriteRule`s exist in `.htaccess` while
the server has no `rewrite` module enabled, and you can't control it - just comment these lines.

Then you'll get the site running on the web. There could be some limitations about some auxiliary functions
(e.g. 3rd party login etc) but that's not much important for you now (such things anyway require efforts to setup).

**Setup database:** this is done with control-panel of the hosting - generally you are provided
with some database name and connection info and create user here via web-interface. So then you
need to put corresponding settings into `conf.php` file and populate database with tables, for
which use the content of `dbinit.sql` except first few lines (no user or db creation) with
some query running tool in the web-interface.

### LAMPP / XAMPP

With this approach you'll find software package (by the name XAMPP probably) which contains HTTP server, PHP and MySQL
inside and set up on your machine. It is not very difficult also and in some respects more handy than with docker.
Then copy files there and setup database. For managing database there should be `PHPMyAdmin`
web tool in the LAMPP packet - make use of it and generally follow instructions for
shared hosting given above.
