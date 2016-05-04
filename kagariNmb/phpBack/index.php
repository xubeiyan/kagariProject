<?php
$requestURI = $_SERVER['REQUEST_URI'];			// ÇëÇóµÄURI
$userAgentString = $_SERVER['HTTP_USER_AGENT'];	// ä¯ÀÀÆ÷×Ö·û´®
$remoteAddr = $_SERVER['REMOTE_ADDR']; 			// ¿Í»§¶ËµØÖ·
$scriptFilename = $_SERVER['SCRIPT_FILENAME'];

echo $scriptFilename . '<br>';

echo $requestURI . '<br>'; #localhost

echo $userAgentString . '<br>'; #/blog/testurl.php

echo $remoteAddr . '<br>'; #id=5

//if ()
	
?>