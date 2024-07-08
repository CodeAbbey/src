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
- [Step 6 - userrank](https://github.com/CodeAbbey/src/tree/v0.6-userrank) - user profile and ranking pages.

### Step 6 - User profile and ranking pages

Since we are able to submit and solve problems, we want user pages to work - here lists of solved problems are
shown and some info about user (country, registration date, last login etc). In this update we add this page
and also page for user-ranking (it is referenced from main menu).

One of the smaller issues by now is that we haven't yet added country support (it is not complicated but
there is a lot of small data so let's do it separately). So country list shows single entry, unknown country.

There is also separate page for user's tried but not solved problems.

### Excercise

Reinitialize the database, create tasks and users (e.g. tasks from the `taskinit.sql` file) - try solving something
and see the newly added pages.

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
