# CodeAbbey - opensource initiative

It is a website with programming exercises - you can quickly deploy it on your own server (even free hosting) - and
build something like **ProjectEuler** or **Rosalind.Info**. Actually, it is more powerful as problems you create
will include data generator / checker code, so that users are every time provided with newly generated, random set
of data (and of course, another answer is expected every time).

This project started as **[https://www.codeabbey.com](https://www.codeabbey.com)**
in September of 2013 and is now (as of summer 2024) moving to opensource, so that everyone can quickly setup
similar website for personal coding puzzle collection, or for school, enterprise company needs (e.g. for
interviewing, screening candidates).

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

### Step 10 - Wiki

In many cases you may want to put some information on the site in the form of simple article -
either some kind of technical help, Q&A, or extended explanations for some sort of tasks etc - for
this we have functionality called "Wiki".

It is not exactly like "Wikipedia" because it is only editable by admins (however you can easily modify this
restriction if you want) - but let the name remain as it is pre-historic.

These articles are written using markdown format (so you may put some extended HTML and even JS here if you want).
Admin has the small "Add new article" link above the text of any wiki page. If there are no pages, still `index/wiki`
(without the trailing slash) leads to the list of all articles, which, even empty, would contain such a link.

When putting links to wiki pages or from them to other parts of the site, it's advisable to use "semi-absolute" path,
i.e. starting with root, like `/index/wiki/help`.

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

