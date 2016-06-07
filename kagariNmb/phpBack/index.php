<?php
/**
* kagariNMB����ļ�index.php
*/
$userAgentString = $_SERVER['HTTP_USER_AGENT'];	// ������ַ���
$remoteAddr = $_SERVER['REMOTE_ADDR']; 			// �ͻ��˵�ַ

// ��ȡִ���ļ���
$scriptArray = explode("/", $_SERVER['SCRIPT_FILENAME']);
$scriptFilename = array_pop($scriptArray);

require 'conf/conf.php'; 	// ����$conf����
require 'lib/error.php';	// ������Ϣ

// ʹ��json����html��Ϊ���ظ�ʽ
if (!isset($conf['responseType']) || $conf['responseType'] == 'json') {
	header('content-type:application/json;charset=utf-8');
} else if ($conf['responseType'] == 'html') {
	header('content-type:text/html;charset=utf-8');
}

// ���󷽷���ָ����һ�ɾܾ� Ĭ��ΪGET|POST
$allowedRequest = explode('|', $conf['allowedRequest']);
if (!in_array($_SERVER['REQUEST_METHOD'], $allowedRequest)) {
	$paras = Array($_SERVER['REQUEST_METHOD']);
	die(Error::errMsg('notAllowedRequestMethod', $paras));
}

// �ж��Ƿ�ִ�й���װ
// if (file_exists($conf['installerPath'])) {
	// $paras = [$conf['installerPath']];
	// die(Error::errMsg('notInstalled', $paras));
// }

require 'lib/database.php';	// �������ݿ�

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
// ����ύAPI�Ƿ�Ϊ�գ����򷵻ػ�ӭҳ��
if ($_SERVER['QUERY_STRING'] == '') {
	echo file_get_contents('welcome.html');
	exit();
}
// ����ύ��API�Ƿ���ָ����API�б���
$queryString = explode("=", $_SERVER['QUERY_STRING'])[1];
if (!in_array($queryString, $conf['apiLists'])) {
	$paras = Array($queryString);
	die(Error::errMsg('notAllowedAPI', $paras));
}

// ʹ��api.php
require 'lib/api.php';
// ��ȡ�ύ����
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
	//echo 'Request API: ' . $queryString . '<br />';
	header('connection:close'); // close��Ҫkeep-alive
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
	$input = json_decode($inputJSON, true); // true����array
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
			if (isset($input['area_id']) && isset($input['user_id']) && isset($input['reply_post_id']) && isset($input['post_content'])) {
				API::sendPost($input);				
			}
			break;
		default:
			die('>w<');
	}
}
?>