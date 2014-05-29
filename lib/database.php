<?php
	/*
	*	数据库参数
	*
	*/
	include 'config.php';
	
	$GLOBALS['sql'] = mysql_connect($database['host'], $database['user'], $database['pass']);
	if (!$GLOBALS['sql']) {
		die('无法连接到MySQL数据库……');
	}
	mysql_query('use ' . $database['dbName'], $GLOBALS['sql']);
?>