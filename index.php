<?php
	include_once("lib/database.php");
	include("lib/View.class.php");
	$page = '';
	$page .= View::head('主页');
	$page .= View::display('header');
	$page .= View::display('index');
	$page .= View::display('footer');
	$page .= View::foot();
	echo $page;
?>
