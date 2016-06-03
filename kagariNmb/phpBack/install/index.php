<?php
// 安装完成后会删除install.php，就没有啦
if (!file_exists('install.php')) {
	echo 'Nimingban has already installed...please access index page...';
	exit();
}

require '../conf/conf.php';
require '../lib/database.php'; // 数据库连接，变量为$con

$sql = 'CREATE DATABASE ' . $conf['databaseName'] . ' COLLATE utf8_general_ci';
if(!mysqli_query($con, $sql)) {
	die(mysqli_connect_error());
} else {
	echo "create database " . $conf['databaseName'] . " successfully!";
}

mysqli_select_db($con, $conf['databaseName']);
// user表
$usersql = 'CREATE TABLE ' . $conf['databaseTableName']['user'] . ' (
	user_id int NOT NULL AUTO_INCREMENT,
	ip_address varchar(140), 
	user_name varchar(20), 
	block_time int,
	last_post_id int,
	PRIMARY KEY(user_id)
) COLLATE utf8_general_ci';	
// area表
$areasql = 'CREATE TABLE ' . $conf['databaseTableName']['area'] . ' (
	area_id int NOT NULL AUTO_INCREMENT,
	area_name varchar(20),
	area_sort int,
	block_status varchar(60),
	parent_area int,
	min_post int,
	PRIMARY KEY(area_id)
) COLLATE utf8_general_ci';
// post表
$postsql = 'CREATE TABLE ' . $conf['databaseTableName']['post'] . ' (
	post_id int NOT NULL AUTO_INCREMENT,
	area_id int,
	user_id int,
	reply_post_id int,
	author_name varchar(20),
	author_email varchar(20),
	post_title text(128),
	post_content text,
	post_images varchar(60),
	create_time varchar(20),
	update_time varchar(20),
	PRIMARY KEY(post_id)
) COLLATE utf8_general_ci';

if(!mysqli_query($con, $usersql)) {
	die(mysqli_connect_error());
} else {
	echo "create table " . $conf['databaseTableName']['user'] . " successfully!";
}

if(!mysqli_query($con, $areasql)) {
	die(mysqli_connect_error());
} else {
	echo "create table " . $conf['databaseTableName']['area'] . " successfully!";
}

if(!mysqli_query($con, $postsql)) {
	die(mysqli_connect_error());
} else {
	echo "create table " . $conf['databaseTableName']['post'] . " successfully!";
}

?>