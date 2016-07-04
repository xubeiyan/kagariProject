<?php
/**
* 所有访问的入口
*/
//print_r($_GET);
// 未提交任何GET参数则认为访问主页
if ($_SERVER['QUERY_STRING'] == '') {
	echo file_get_contents('html/index.html');
	exit();
}

if ($_GET['q']) {
	
}
?>