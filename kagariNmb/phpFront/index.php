<?php
/**
* ���з��ʵ����
*/
//print_r($_GET);
// δ�ύ�κ�GET��������Ϊ������ҳ
if ($_SERVER['QUERY_STRING'] == '') {
	echo file_get_contents('html/index.html');
	exit();
}

if ($_GET['q']) {
	
}
?>