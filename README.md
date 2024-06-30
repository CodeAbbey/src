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

When you first open the main page, you'll see "Login" link in the top-right corner. Clicking it
you'll get to the login form which is provided by controller `ctl/Login.php` and corresponding
template `ctl/login.php`. If you see any errors at this step (or any of the pages is loaded
malformed and you find errors when looking at page source) - examine them, quite probably
there is an issue with database, e.g.:

- library for working with database (`mysqli`) is not installed or enabled (this shouldn't happen
    with Docker or shared hosting setups discussed below... so investigate on your own)
- credentials to database (user, password), or database name and connection info are wrong or
    privileges are lacking - check them in `conf.php`, make sure you initialized user if
    it is not shared hosting where user is preconfigured
- database tables are not initialized yet (review setup procedure below).

If everything is fine, you can enter some name and password and try login (which should fail) -
and you can try registering (for which you enter password twice and supply some email, possibly
fake). When login is successful, you are redirected to "Task List" page, for which only stub
is currently added. User profiles and settings pages are absent yet. Try logout.

When you examine `login` page source you'll see the new modules are being used, namely
`UserService.php` and `LoginService.php` while `MiscService.php` and `Auth.php` are updated.
Moreover they now use `UsersDao`, `UserDataDao` and `RolesDao` (via `$ctx` variable) - they are
"Data-Access Objects", with them we perform database operations. Most of them are just instances
of `MysqlDao.php` and there are just typical operations (read, find, save, delete etc) - but
for some (UserDataDao) the class is extended with dedicated file (`modules/dao/UserDataDao.php`)
adding some extra queries (not necessary right now).

**Excercises**

Examine database (e.g. with `sqlexec.php` page) fetching data from `users`, `roles`, `userdata`
tables (e.g. `select * from roles`). Investigate how passwords/emails are hashed in the users
table. Where salts for these hashes come from? You should modify them in "production"!

Try changing some user's role to `admin`. You should see the menu line is slightly different then,
after login.

Try to invent some basic code for `ctl/user/Profile.php` controller and `pages/user/profile.php`
page template so it shows some basic information about user, for example registration and
last login date.

Examine difference of the branches for `Step-1` and `Step-2` in GitHub (click compare branches
and select these two) to see which files were added and which lines are modified. In most cases
you will easily grasp what is happening in those lines.

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

Pick some free hosting, for example [AwardSpace / AtWebpages](https://www.awardspace.com/) and simply copy
files there. You'll need setup database here also and put its details into configuration, but at the first
step this is not important.

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
