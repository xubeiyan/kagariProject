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
			'user',
			'area',
			'post'
	),
	// 匿名版设置
	'apiLists' => Array(
			'api/getCookies',
			'api/getAreaLists',
			'api/getAreaPosts',
			'api/getPost',
			'api/sendPost'
	),
	'customUserAgent' => '',
	'scriptFilename' => 'index.php',
	'installerPath' => 'install/install.php',
	'allowedRequest' => 'GET|POST',
	'responseType' => 'json'
);

?>