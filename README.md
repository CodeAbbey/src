# Instructions

Here are two parts - one about how it works and another about how to run it. Consult them in any order according
to your needs.

## How it works

Currently repo contains bare minimum files, or "framework" which work for page loading. This provides several features:

- every page may contain two parts - page view or template itself, e.g. mainly HTML code - and "controller" which
    mainly contains PHP code to prepare or load necessary data for given page
- every page may specify "layout" into which it is inserted, so that many pages share common "frame" (currently
    there is a single "default" layout)
- pages and layouts also can insert "fragments" either to reuse them in more than one place - or simply to
    put less important things elsewhere

The main file which processes all requests is `index.php` and it contains the "heart" of framework. Here you may find
brief code which tries to load "controllers" and "pages" upon request. For example if you try to load page by url
[http://localhost:8080/index.php?page=task_list](http://localhost:8080/index.php?page=task_list) then it will look
firstly for file `ctl/task/List.php` for "controller" code, which may inject some data into `$model` object - then
for file `pages/task/list.php` which will have HTML code with inclusions of data from `$model` here and there.

Controller may be absent if it is not needed by some simple page. Page template also could be absent if controller
uses plain output (look at `ctl/api/Weekbest.php`). You can guess that underscores in page parameter in the url
correspond to nested directories below `ctl` and `pages`. Note that controller file names are capitalized to reduce
confusion when opened in a multi-page editor. There is a folder `module` which prepares "context"
object which provides access to various helper "service" classes etc. These are available in pages and controllers
code via `$ctx` variable.

When no page is specified with the request (i.e. we load `index.php` without parameters), then `main` page is loaded.

There is some magic which allows to use urls like `index/task_list` instead of `index.php?page=task_list - this is
achieved by url-rewriting set up in the optional `.htaccess` file and configuration option in the `conf.php` - but
let's return to it later.

**Excercises**

You see that "Problems" link for example leads to non-existing page (as other links too). Try adding such a page with
some example content. Just to make sure you understand where to put files etc.

Check the `ctl/pages/Main.php` controller. There are some empty variables defined which are used to display dynamic
lists on the main page (last problems, last forum topics). Try to put here some static data (arrays, objects)
to get visible results on the main page. This will require you to check corresponding files in "fragments" folder
to figure out which exactly data are needed (of course in reality they are coming from database).

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

### Free Hosting

Pick some free hosting, for example [AwardSpace / AtWebpages](https://www.awardspace.com/) and simply copy
files there. You'll need setup database here also and put its details into configuration, but at the first
step this is not important.

Then you'll get the site running on the web. There could be some limitations about some auxiliary functions
(e.g. 3rd party login etc) but that's not much important for you now (such things anyway require efforts to setup).

### LAMPP / XAMPP

With this approach you'll find software package (by the name XAMPP probably) which contains HTTP server, PHP and MySQL
inside and set up on your machine. It is not very difficult also and in some respects more handy than with docker.
Then copy files there and setup database.
