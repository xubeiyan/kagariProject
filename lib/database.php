<?php
	/*
	*	数据库参数
	*
	*/
	include 'config.php';
	
	$con = mysql_connect($database['host'], $database['user'], $database['pass']);
	if (!$con) {
		die('无法连接到MySQL数据库……');
	}
?>