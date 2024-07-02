create database ca;

create user 'causer'@'localhost';

grant all privileges on ca.* to 'causer'@'localhost' identified by 'somepwd';

use ca;

drop table if exists users;
create table users (
    id int primary key auto_increment,
    username varchar(120),
    password varchar(120),
    loginid varchar(120),
    url varchar(120),
    index (username),
    index (loginid),
    index (url)
);

drop table if exists roles;
create table roles (
    id int primary key auto_increment,
    userid int,
    role varchar(120),
    index (userid)
);

drop table if exists userdata;
create table userdata (
    id int primary key auto_increment,
    userid int,
    solved int default 0,
    failed int default 0,
    points real default 0,
    cheat int default 0,
    country varchar(32),
    rankpos int default 0,
    language varchar(120) default '',
    avatar varchar(250) default '',
    created timestamp default current_timestamp,
    lastlogin timestamp default '0000-00-00',
    index (userid)
);

drop table if exists tasks;
create table tasks (
    id int primary key auto_increment,
    title varchar(120),
    url varchar(120),
    volumeid int,
    solved int default 0,
    cost real default 1,
    shown int default 0,
    author varchar(120) default null,
    lastmod date default '2001-01-01',
    index (volumeid),
    index (url)
);

drop table if exists taskdata;
create table taskdata (
    id int primary key auto_increment,
    taskid int,
    type varchar(16),
    data text,
    index (taskid)
);

drop table if exists usertasks;
create table usertasks (
    id int primary key auto_increment,
    userid int,
    taskid int,
    ts timestamp default current_timestamp,
    solved int default 0,
    language varchar(120) default '',
    variant int default 0,
    index (userid),
    index (taskid)
);

drop table if exists tags;
create table tags (
    id int primary key auto_increment,
    title varchar(32)
);

drop table if exists tasktags;
create table tasktags (
    id int primary key auto_increment,
    taskid int,
    tagid int,
    index (taskid),
    index (tagid)
);

drop table if exists tagval;
create table tagval (
    id int primary key auto_increment,
    tag varchar(32),
    val text,
    index (tag)
);

drop view if exists tasklist;
create view tasklist as select * from tasks order by solved desc, id asc;

insert into tags (title) values ('unlabeled');
