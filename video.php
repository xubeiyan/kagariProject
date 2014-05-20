<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<?php
	$vid = isset($_GET['vid']) ? $_GET['vid'] : '';
	$vid = is_numeric($vid) ? $vid : 0;
	if ($vid != 0) {
		echo ' <embed id="MukioPlayer"
		src="mukioplayerplus.swf"
		width="960px"
		height="480px"
		type="application/x-shockwave-flash"
		allowscriptaccess="always"
		quality="high"
		allowfullscreen="true"
		runat="server" 
		flashvars="file=video/' . $vid . '.flv&cid='. $vid .'"/>';
	} else {
		echo '找不到视频id为'. $vid . '的视频！';
	}
	
?>