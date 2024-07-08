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
- [Step 4 - tasks]() - pages for creating, editing, listing and viewing tasks.
- [Step 5 - submit](https://github.com/CodeAbbey/src/tree/v0.5-submit) - submission and checking.

### Step 5 - Submission and Checking

Now site gets its main functionality: user can submit solution for the task - and system will check
it (answer), with eventually updating user and overall stats if solution is correct.

This all is governed by `./ctl/Attempt` controller mainly. However it depends on a number of functions
from the `TaskService` (probably largest part of this service is for checking answers and updating stats).
You may investigate all this logic but again this is not crucial to know in details.

Some things not implemented yet at this point are:

- user profile page where solved problems are visible
- user ranking page
- executors which run the code when various programming language buttons are clicked around solution area

The first two are yet to come in the next update.

As about code executors - they are run in a separate sandbox, which in turn requires a dedicated server -
at the moment it is out of the scope of the given project. You may prefer to hid all the buttons (except for
javascript - on the other hand it is possible to add runners for some languages implemented in JS, i.e.
working in-browser - anyway let's not dive into it at the moment).

**Points Recalculation** is yet another subtle thing. Website calculates user score based on the cost of the
tasks solved - but at the same time cost of the tasks changes by and by according to number of solvers.

This process is governed by `MiscService::calcPoints()` which by default is invoked either immediately after each
successful solution. However if your installation hosts many thousands of users, recalculation takes some
time (e.g. it may be several seconds and more as database grow). In this case it is better to invoke recalculation
by external cron job. For this you setup `calcPointsSecret` to some string `XXXX` in the configuration and
point your cron job to request `/index/tools_calcpoints/XXXX`, e.g. passing secret in the url.

### Excercise

Reinitialize the database and create three users (admin and two normal ones). Then execute `taskinit.sql` to populate
the database with a couple of simple problems. Try solving them not necessarily at the first attempt - and after
each submission check, how scores for the tasks are updated. Then change `calcPointSecret` to some string and
make sure automatic recalculation is turned off, you'll need to request the mentioned url manually or by cron
to see the changes.

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
