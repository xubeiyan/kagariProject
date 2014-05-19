<?php
	//获取域名或主机地址 
	echo $_SERVER['HTTP_HOST']."<br>"; #localhost

	//获取网页地址 
	echo $_SERVER['PHP_SELF']."<br>"; #/blog/testurl.php

	//获取网址参数 
	echo $_SERVER['QUERY_STRING'].'<br>'; #id=5

?>