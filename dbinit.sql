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

