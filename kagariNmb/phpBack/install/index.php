<?php
// 安装完成后会删除install.php，就没有啦
if (!file_exists('install.php')) {
	echo 'Nimingban has already installed...please access index page...';
	exit();
}

require '../lib/database.php'; // 数据库连接，变量为$con
require '../conf/conf.php';

$sql = 'CREATE DATABASE ' . $conf['databaseName'] . ' COLLATE utf8_general_ci';
if(!mysqli_query($con, $sql)) {
	die(mysqli_connect_error());
}
// user表
$usersql = 'CREATE TABLE ' . $conf['databaseTableName'][0] . ' (
	user_id int NOT NULL AUTO_INCREMENT,
	ip_address varchar(140), 
	user_name varchar(20), 
	block_time int,
	last_post_id int,
	PRIMARY KEY(user_id)
) COLLATE utf8_general_ci';	
// area表
$areasql = 'CREATE TABLE ' . $conf['dababaseTableName'][1] . ' (
	area_id int NOT NULL AUTO_INCREMENT,
	area_name varchar(20),
	area_sort int,
	block_status varchar(60),
	parent_area int,
	min_post int,
	PRIMARY KEY(area_id)
) COLLATE utf8_general_ci';
// post表
$postsql = 'CREATE TABLE ' . $conf['dababaseTableName'][2] . ' (
	
) COLLATE utf8_general_ci';
mysqli_query($con, $usersql);
mysqli_query($con, $areasql);
mysqli_query($con, $postsql);
?>