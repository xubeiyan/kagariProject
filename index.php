<?php
	include_once("lib/database.php");
	include("lib/View.class.php");
	$page = '';
	$page .= View::head('主页');
	$page .= View::display('header');
	$page .= View::display('index');
	$replace = array(
		'{timestamp}' => View::timestamp()
	);
	$page .= View::display('footer',$replace);
	$page .= View::foot();
	echo $page;
?>
