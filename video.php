<?php
	include_once("lib/database.php");
	include("lib/View.class.php");
	$vid = isset($_GET['vid']) ? $_GET['vid'] : '';
	$vid = is_numeric($vid) ? $vid : 0;
	if ($vid != 0) {
		$page = '';
		$page .= View::head('视频');
		$page .= View::display('header');
		$replace = array(
			'{vid}' => $vid
		);
		$page .= View::display('video', $replace);
		$page .= View::display('footer');
		$page .= View::foot();
	} else {
		echo '找不到视频id为'. $vid . '的视频！';
	}
	echo $page;
?>