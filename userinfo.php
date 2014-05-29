<?php
	include_once("lib/database.php");
	include("lib/View.class.php");
	$page = '';
	$page .= View::head('个人信息');
	$page .= View::display('header');
	$page .= View::display('userinfo');
	$replace = array(
		'{timestamp}' => View::timestamp()
	);
	$page .= View::display('footer', $replace);
	$page .= View::foot();
	echo $page;
?>