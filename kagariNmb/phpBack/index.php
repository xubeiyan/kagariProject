<?php
/**
* kagariNMB入口文件index.php
*/
$userAgentString = $_SERVER['HTTP_USER_AGENT'];	// 浏览器字符串
$remoteAddr = $_SERVER['REMOTE_ADDR']; 			// 客户端地址

// 获取执行文件名
$scriptArray = explode("/", $_SERVER['SCRIPT_FILENAME']);
$scriptFilename = array_pop($scriptArray);

require 'conf/conf.php'; 	// 引入$conf变量
require 'lib/error.php';	// 错误信息

// 使用json还是html作为返回格式
if (!isset($conf['responseType']) || $conf['responseType'] == 'json') {
	header('content-type:application/json;charset=utf-8');
} else if ($conf['responseType'] == 'html') {
	header('content-type:text/html;charset=utf-8');
}

// 请求方法非指定的一律拒绝 默认为GET|POST
$allowedRequest = explode('|', $conf['allowedRequest']);
if (!in_array($_SERVER['REQUEST_METHOD'], $allowedRequest)) {
	$paras = Array($_SERVER['REQUEST_METHOD']);
	die(Error::errMsg('notAllowedRequestMethod', $paras));
}

// 判断是否执行过安装
// if (file_exists($conf['installerPath'])) {
	// $paras = [$conf['installerPath']];
	// die(Error::errMsg('notInstalled', $paras));
// }

require 'lib/database.php';	// 访问数据库

// 检查请求的文件是否是index.php，但是由于rewrite模块的存在这个疑似没啥用
if ($scriptFilename != $conf['scriptFilename']) {
	$paras = Array($conf['scriptFilename'], $scriptFilename);
	die(Error::errMsg('requestInvalidURI', $paras));
}
// 检查User-Agent是否为指定值
if ($conf['customUserAgent'] != '') {
	if ($_SERVER['HTTP_USER_AGENT'] != $conf['customUserAgent']) {
		$paras = Array($_SERVER['HTTP_USER_AGENT']);
		die(Error::errMsg('notSpecificUserAgent', $paras));
	}	
}
// 检查提交API是否为空，是则返回欢迎页面
if ($_SERVER['QUERY_STRING'] == '') {
	echo file_get_contents('welcome.html');
	exit();
}
// 检查提交的API是否在指定的API列表内
$queryString = explode("=", $_SERVER['QUERY_STRING'])[1];
if (!in_array($queryString, $conf['apiLists'])) {
	$paras = Array($queryString);
	die(Error::errMsg('notAllowedAPI', $paras));
}

// 使用api.php
require 'lib/api.php';
// 获取提交内容
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
	//echo 'Request API: ' . $queryString . '<br />';
	header('connection:close'); // close不要keep-alive
	switch ($queryString) {
		case 'api/getCookie':
			API::getCookie();
			break;
		case 'api/getAreaLists':
			API::getAreaLists();
			break;
		default:
			die('>w<');
	}
} else if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	$inputJSON = file_get_contents("php://input");
	$input = json_decode($inputJSON, true); // true返回array
	if ($input == NULL) {
		die(Error::errMsg('badJSON', []));
	}
	header('connection:close');
	switch ($queryString) {
		case 'api/getAreaPosts':
			API::getAreaPosts($input);
			break;
		case 'api/getPost':
			API::getPost($input);
			break;
		case 'api/sendPost':
			API::sendPost($input);
			break;
		default:
			die('>w<');
	}
}
?>