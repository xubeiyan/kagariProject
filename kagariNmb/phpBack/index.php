<?php
$requestURI = $_SERVER['REQUEST_URI'];			// �����URI
$userAgentString = $_SERVER['HTTP_USER_AGENT'];	// ������ַ���
$remoteAddr = $_SERVER['REMOTE_ADDR']; 			// �ͻ��˵�ַ
$scriptFilename = $_SERVER['SCRIPT_FILENAME'];

echo $scriptFilename . '<br>';

echo $requestURI . '<br>'; #localhost

echo $userAgentString . '<br>'; #/blog/testurl.php

echo $remoteAddr . '<br>'; #id=5

//if ()
	
?>