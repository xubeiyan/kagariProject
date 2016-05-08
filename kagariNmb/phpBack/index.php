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
 
// 判断是否执行过安装
if (file_exists($conf['installerPath'])) {
	$paras = [$conf['installerPath']];
	die(Error::errMsg('notInstalled', $paras));
}

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
// 检查提交的API是否在指定的API列表内
$queryString = explode("=", $_SERVER['QUERY_STRING'])[1];
if (!in_array($queryString, $conf['apiLists'])) {
	$paras = Array($queryString);
	die(Error::errMsg('notAllowedAPI', $paras));
}
?>