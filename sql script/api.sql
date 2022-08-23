create database api;

use api;

create table users(
	id bigint unsigned not null AUTO_INCREMENT,
	name varchar(50) not null,
	surname varchar(50) not null,
	patronymic varchar(50) not null,
	company varchar(100) null,
	phone varchar(17) not null,
	email varchar(50) not null,
	birth date null,
	image varchar(250) null,
	PRIMARY KEY (id)
);