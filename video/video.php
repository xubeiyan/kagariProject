<?php
	$vid = isset($_GET['vid']) ? $_GET['vid'] : '';
	$vid = is_numeric($vid) ? $vid : 0;
	echo ' <embed id="MukioPlayer"
		src="mukioplayerplus.swf"
		width="960px"
		height="480px"
		type="application/x-shockwave-flash"
		allowscriptaccess="always"
		quality="high"
		allowfullscreen="true"
		runat="server" 
		flashvars="file=1423991.flv"/>';
?>