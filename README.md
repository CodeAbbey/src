# CodeAbbey - opensource initiative

It is a website with programming exercises - you can quickly deploy it on your own server (even free hosting) - and
build something like **ProjectEuler** or **Rosalind.Info**. Actually, it is more powerful as problems you create
will include data generator / checker code, so that users are every time provided with newly generated, random set
of data (and of course, another answer is expected every time).

This project started as **[https://www.codeabbey.com](https://www.codeabbey.com)**
in September of 2013 and is now (as of summer 2024) moving to opensource, so that everyone can quickly setup
similar website for personal coding puzzle collection, or for school, enterprise company needs (e.g. for
interviewing, screening candidates).

**Quick video-demo: [deploy CodeAbbey site at free web-hosting](https://www.youtube.com/watch?v=ayjzFg8T1eQ)**

For proof-testing we also started a couple other site based on these sources:

- collection of industrial-oriented problems (from interviews etc): **[Ca4pro.com](https://ca4pro.com)**
- dedicated to exercises in Physics: **[Alferov's Peace](https://alferovs-peace.sourceforge.net)**

## Instructions

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
- [Step 4 - tasks](https://github.com/CodeAbbey/src/tree/v0.4-tasks) - pages for creating, editing,
    listing and viewing tasks.
- [Step 5 - submit](https://github.com/CodeAbbey/src/tree/v0.5-submit) - submission and checking.
- [Step 6 - userrank](https://github.com/CodeAbbey/src/tree/v0.6-userrank) - user profile and ranking pages.
- [Step 7 - sync](https://github.com/CodeAbbey/src/tree/v0.7-sync) - no critical features added,
    mainly some improvements due to syncronizing codebase with existing website.
- [Step 8 - customization](https://github.com/CodeAbbey/src/tree/v0.8-customization) minor tweaks and
    rearrangements to help you customize the site elements (project name, title, author, fragments etc)
- [Step 9 - task tags](https://github.com/CodeAbbey/src/tree/v0.9-tags) task tags and "volumes"
- [Step 10 - wiki](https://github.com/CodeAbbey/src/tree/v0.10-wiki) "wiki" pages support
- [Step 11 - solvers](https://github.com/CodeAbbey/src/tree/v0.11-solvers) about viewing solutions
- [Step 12 - customization-2](https://github.com/CodeAbbey/src/tree/v0.12-cust2) more configuration, and mess-hall working
- [Step 13 - forum](https://github.com/CodeAbbey/src/tree/v0.13-forum) well, forum files and tables are added here
- [Step 14 - profile](https://github.com/CodeAbbey/src/tree/v0.14-profile) files for user profile settings added
- [Step 15 - langs](https://github.com/CodeAbbey/src/tree/v0.15-langs) languages list customization

### Step 15 - Languages list customization

This is a small but somewhat painful piece :) During the lifetime of CodeAbbey the list of languages (used to mark solutions, primarily) have grown - some languages there are pretty specific
to the site (e.g. `Asm4004` and `SQLite`), others may be so rare you won't like to include them
in your site.

Now the list lives also in `conf.php` under the name of `$ctx->elems->conf->languages` - it is
a "key-value" list (or associative array) where values are how the language is named in user 
interface, while keys - how they are called in the database etc. For many languages these match
but sometimes it's preferable that the key doesn't use special characters etc.

There is another use for these languages, almost absent (yet) in opensource version - it is about
running the code in the chosen language in user's interface. For most of languages this requires
sending the code from browser to server and then either executing them here (e.g. it is a situation
with `BASIC` or `RegExp` which are actually executed by `PHP` interpreter), or sending further
to "sandbox" server. Some could be instead executed in-browser. Of such the only one is present
for now - javascript. As a exercise (somewhat advanced and not 5-minutes work) you may try to
attach some python interpreter implemented in javascript (perhaps, brython) - or other more
lightweight language - and create here button for it.

Functionality to run sources in sandbox is absent now mainly for the reason that setting up a
sandbox server is a separate matter (and unlike main site it couldn't run on free hosting) - but
if you inspect code on main CodeAbbey website you'll see how requests are sent to dedicated
controller. You can either implement something here yourself or ask me for further guidance about
creating sandbox. But I recommend not doing this until you feel the most of main functionality is
sturdy enough and your site is steaming well :)

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

### Customization

It is natural that after your deploy succeeds and you see the website running with joy, you may
want to change its name, title and some general fragments. This is what we call "customization".
What makes customization a bit tedious, is that you want to keep compatibility with the original
sources, so that when "upstream" repository updates, you can update your site in as simple way as
possible.

This means that simply starting tweaking all files carelessly may be not exactly what you want -
with the first update you'll inadvertently override half of your changes!

We are trying to simplify this for you and by now this is achieved with the following:

- you need not modify file `conf.php` in the root, instead just drop along it the similar file
    named `cust_conf.php` and put your real secrets and settings here (feel free to remove all
    lines that do not differ, these files are loaded one after another); of course you should
    never commit your file with real secrets to any public repository etc!
- find settings with project name, webpage general title, site author etc in the `conf.php` -
    and update them to your taste and needs (of course, again do it in `cust_conf.php`).
- if you want to replace some `fragments` files, just copy them with some new name (e.g.
    copy `main_bottomnote.html` to `main_bottomextra.html`, then go to configuration file
    and find array `custFrag`, put replacement here, e.g. `'main_bottomnote' => 'main_bottomextra'`,
    then make any changes in this new fragment file instead; also you can use empty name if you
    want some fragment to be removed (by default it happens to `adblock` fragment).

