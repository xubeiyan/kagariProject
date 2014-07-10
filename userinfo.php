<?php
	include_once("lib/database.php");
	include("lib/View.class.php");
	include("lib/User.class.php");
	$user = new User();
	$user -> getById(2);
	$page = '';
	$page .= View::head('个人信息');
	$page .= View::display('header');
	$replace = array(
		'{uid}' => $user -> uid,
		'{username}' => $user -> username,
		'{nickname}' => $user -> nickname,
		'{type}' => $user -> type,
		'{point}' => $user -> point
	);
	$page .= View::display('userinfo', $replace);
	$replace = array(
		'{timestamp}' => View::timestamp()
	);
	$page .= View::display('footer', $replace);
	$page .= View::foot();
	echo $page;
?>