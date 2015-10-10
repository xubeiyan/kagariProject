<?php
	include("lib/View.class.php");
	include("lib/Video.class.php");
	
	$vid = isset($_GET['vid']) ? $_GET['vid'] : '';
	$vid = is_numeric($vid) ? $vid : 0;
	if ($vid != 0) {
		$page = '';
		$page .= View::head('视频');
		$page .= View::display('header');
		$video = new Video();
		$video ->getVideoByVid($vid);
		$replace = array(
			'{vid}' => $video ->vid,
			'{title}' => $video ->title,
			'{description}' => $video ->description
		);
		$page .= View::display('video', $replace);
		$replace = array(
			'{timestamp}' => View::timestamp()
		);
		$page .= View::display('footer', $replace);
		
		$page .= View::foot();
	} else {
		echo '找不到视频id为'. $vid . '的视频！';
	}
	echo $page;
?>