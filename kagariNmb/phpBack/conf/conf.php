<?php
/**
* 设置
*/
$conf = Array(
	// 数据库部分
	'databaseHost' => 		'localhost',
	'databaseUsername' => 	'root',
	'databasePassword' => 	'',
	'databaseName' =>		'kagari_Nimingban',
	'databasePort' =>		'3306',
	'databaseTableName' => 	Array(
			'user' => 'user',
			'area' => 'area',
			'post' => 'post'
	),
	// 匿名版设置
	'apiLists' => Array(
			'api/getCookie',
			'api/getAreaLists',
			'api/getAreaPosts',
			'api/getPost',
			'api/sendPost'
	),
	'customUserAgent' => '',
	'scriptFilename' => 'index.php',
	'installerPath' => 'install/install.php',
	'allowedRequest' => 'GET|POST',
	'responseType' => 'json',
	'postsPerPage' => 50,
	'lastReplyPosts' => 8 // 最多显示多少条post的回复
);

?>