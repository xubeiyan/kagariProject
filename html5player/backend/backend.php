<?php
$config = array(
	'barrageFile' => '../barrage/1.xml',
	'barrageFormat' => 'time, type, size, user, color, timestamp, content'
);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	$barrageFile = fopen($config['barrageFile'], "r+") or die('Unable to open file...');
	// 移动指针至结尾处
	fseek($barrageFile, -2, SEEK_END);
	$barrage = ",\r\n\t\t'" . file_get_contents("php://input") . "'];";
	fwrite($barrageFile, $barrage);
	fclose($barrageFile);
}
?>