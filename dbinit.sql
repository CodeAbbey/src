-- use these 4 lines if database doesn't exist at all, e.g. in Docker
-- on shared hosting you are given ready database and credentials

create database ca;

create user if not exists 'causer'@'localhost';

grant all privileges on ca.* to 'causer'@'localhost' identified by 'somepwd';

use ca;

-- if all tables should have specific prefix, auto-replace 'pfx_' with it
-- (it may happen on shared hosting)
-- you also may autoremove it (replacing with empty string)
-- if you are sure it is not needed

drop table if exists pfx_users;
create table pfx_users (
    id int primary key auto_increment,
    username varchar(120),
    password varchar(120),
    loginid varchar(120),
    url varchar(120),
    index (username),
    index (loginid),
    index (url)
);

drop table if exists pfx_roles;
create table pfx_roles (
    id int primary key auto_increment,
    userid int,
    role varchar(120),
    index (userid)
);

drop table if exists pfx_userdata;
create table pfx_userdata (
    id int primary key auto_increment,
    userid int,
    solved int default 0,
    failed int default 0,
    points real default 0,
    country varchar(32),
    rankpos int default 0,
    language varchar(120) default '',
    avatar varchar(250) default '',
    created timestamp default current_timestamp,
    lastlogin timestamp default '0000-00-00',
    index (userid)
);

drop table if exists pfx_tasks;
create table pfx_tasks (
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

drop table if exists pfx_taskdata;
create table pfx_taskdata (
    id int primary key auto_increment,
    taskid int,
    type varchar(16),
    data text,
    index (taskid)
);

drop table if exists pfx_usertasks;
create table pfx_usertasks (
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

drop table if exists pfx_tags;
create table pfx_tags (
    id int primary key auto_increment,
    title varchar(32)
);

drop table if exists pfx_tasktags;
create table pfx_tasktags (
    id int primary key auto_increment,
    taskid int,
    tagid int,
    index (taskid),
    index (tagid)
);

drop table if exists pfx_solutions;
create table pfx_solutions (
    id int primary key auto_increment,
    usertaskid int,
    viewkey varchar(16),
    solution text,
    index(usertaskid)
);

drop table if exists pfx_friends;
create table pfx_friends (
    id int primary key auto_increment,
    userid int,
    targetid int,
    visible int default 1,
    index (userid),
    index (targetid)
);

drop table if exists pfx_tagval;
create table pfx_tagval (
    id int primary key auto_increment,
    tag varchar(32),
    val text,
    index (tag)
);

drop table if exists pfx_countries;
create table pfx_countries (
    code varchar(32) primary key,
    title varchar(120)
);

drop table if exists pfx_challenges;
create table pfx_challenges (
    id int primary key auto_increment,
    taskid int,
    userid int,
    score real,
    total real,
    notes varchar(250),
    index (taskid),
    index (userid)
);

drop table if exists pfx_forums;
create table pfx_forums (
    id int primary key auto_increment,
    title varchar(250),
    info text,
    url varchar(250),
    minlevel int default 5,
    index (url)
);

drop table if exists pfx_forumtopics;
create table pfx_forumtopics (
    id int primary key auto_increment,
    forumid int,
    userid int,
    taskid int default 0,
    title varchar(250),
    url varchar(250),
    posts int default 0,
    created timestamp default current_timestamp,
    lastpost timestamp,
    lastpostid int,
    index (forumid),
    index (url)
);

drop table if exists pfx_forumposts;
create table pfx_forumposts (
    id int primary key auto_increment,
    topicid int,
    userid int,
    created timestamp default current_timestamp,
    post text,
    index (topicid)
);

drop table if exists pfx_wiki;
create table pfx_wiki (
    id int primary key auto_increment,
    title varchar(120),
    url varchar(120),
    data text,
    lastmod date default '2001-01-01',
    index (title),
    index (url)
);

drop table if exists pfx_chat;
create table pfx_chat (
    id bigint primary key auto_increment,
    userid int,
    created timestamp default current_timestamp,
    message text,
    index (userid),
    index (created)
);

drop view if exists pfx_userrank;
create view pfx_userrank (id, username, url, solved, failed, points, rankpos, created, country, language, avatar) as
    select u.id, u.username, u.url, d.solved, d.failed, d.points, d.rankpos, d.created, d.country, d.language, d.avatar
    from pfx_users u join pfx_userdata d on u.id = d.userid
    order by d.rankpos desc;

drop view if exists pfx_tasklist;
create view pfx_tasklist as select * from pfx_tasks order by solved desc, id asc;

drop view if exists pfx_userpointstask;
create view pfx_userpointstask as select userid, sum(cost) as sumcost
    from pfx_usertasks ut join pfx_tasks t on ut.taskid = t.id
        where ut.solved > 0 and variant = 0 group by userid;

drop view if exists pfx_userpointschlng;
create view pfx_userpointschlng as select userid, sum(total) as sumcost
    from pfx_challenges where userid <> 0 group by userid;

drop view if exists pfx_userpoints;
create view pfx_userpoints as select pts.userid, pts.sumcost + coalesce(ch.sumcost, 0) as sumcost
    from pfx_userpointstask pts left join pfx_userpointschlng ch using(userid)
    order by sumcost desc;

drop view if exists pfx_chatview;
create view pfx_chatview (id, userid, username, userurl, solved, created, message, avatar) as
    select c.id, c.userid, u.username, u.url, d.solved, c.created, c.message, d.avatar
    from pfx_chat c join pfx_users u on c.userid = u.id left join pfx_userdata d on c.userid = d.userid
    where c.created > (now() - interval 1 day)
    order by c.created desc;

insert into pfx_tags (title) values ('unlabeled');
insert into pfx_countries (code, title) values ('ZZ','Unknown');
insert into pfx_wiki (title, url, data) values ('Help', 'help', 'VGhpcyBpcyBhIG1haW4gaGVscCBwYWdl');
insert into pfx_forums (title, url, minlevel) values ('General discussions', 'general', 5);

