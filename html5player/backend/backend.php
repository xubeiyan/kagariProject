<?php
$config = array(
	'barrageFile' => '../barrage/1.xml',
	'barrageFormat' => 'time, type, size, user, color, timestamp, content'
);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	$post = file_get_contents("php://input"); // $post变量为1.mp4||1.5,1,25,test,#FFFFFF,2015-09-01 09:21,咦！！！！这种形式
	$data = explode("||", $post, 2); //将POST数据分割成两部分 文件名与弹幕 如1.mp4和1.5,1,25,test,#FFFFFF,2015-09-01 09:21,咦！！！！
	$barrageElements = explode(",", $data[1]);
	if (count($barrageElements) != 7))) {
		die('Length wrong?');
	}
	$config['barrageFile'] = '../barrage/' . explode(".", $data[0])[0] . '.xml'; //$config['barrageFile'] = "'../barrage/' . '1' . '.xml'"
	$barrageFile = fopen($config['barrageFile'], "r+") or die('Unable to open file...');
	// 移动指针至结尾处
	fseek($barrageFile, -2, SEEK_END);
	$barrage = ",\r\n\t\t'" . $data[1] . "'];"; // 将$data[1]作为弹幕写入文件
	fwrite($barrageFile, $barrage);
	fclose($barrageFile);
} else {
	echo 'It seems that the METHOD is wrong...';
}
?>