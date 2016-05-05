<?php
$userAgentString = $_SERVER['HTTP_USER_AGENT'];	// ������ַ���
$remoteAddr = $_SERVER['REMOTE_ADDR']; 			// �ͻ��˵�ַ

// ��ȡִ���ļ���
$scriptArray = explode("/", $_SERVER['SCRIPT_FILENAME']);
$scriptFilename = array_pop($scriptArray);

require 'conf/conf.php'; 	// ����$conf����
require 'lib/error.php';	// ������Ϣ
 
// ���������ļ��Ƿ���index.php����������rewriteģ��Ĵ����������ûɶ��
if ($scriptFilename != $conf['scriptFilename']) {
	$paras = Array($conf['scriptFilename'], $scriptFilename);
	die(Error::errMsg('requestInvalidURI', $paras));
}
// ���User-Agent�Ƿ�Ϊָ��ֵ
if ($conf['customUserAgent'] != '') {
	if ($_SERVER['HTTP_USER_AGENT'] != $conf['customUserAgent']) {
		$paras = Array($_SERVER['HTTP_USER_AGENT']);
		die(Error::errMsg('notSpecificUserAgent', $paras));
	}	
}
// ����ύ��API�Ƿ���ָ����API�б���
$queryString = explode("=", $_SERVER['QUERY_STRING'])[1];
if (!in_array($queryString, $conf['apiLists'])) {
	$paras = Array($queryString);
	die(Error::errMsg('notAllowedAPI', $paras));
}
?>