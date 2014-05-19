<?php
	$welcome_message = 'Kagari Project搭建成功！';
	$title = "欢迎";
	$filepath = "templates/index.html";
	if (file_exists($filepath) == FALSE) {
		echo ('找不到templates/index.html');
		exit();
	}
	$file = file_get_contents($filepath);
	$file = str_replace("{welcome_message}", $welcome_message, $file);
	$file = str_replace("{title}", $title, $file);
	
	echo $file;
?>