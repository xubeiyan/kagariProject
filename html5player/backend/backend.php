<?php
$config = Array (
	// 默认弹幕文件路径
	'barragePath'			=> '../barrage/',
	// 默认弹幕文件名称
	'defaultBarrageFile'	=> 'noname.xml',
	// 弹幕格式（时间，类型，文字大小，发送用户，颜色，时间，内容）
	'barrageFormat' 		=> 'time, type, size, user, color, timestamp, content',
);

// 根据是否附带了弹幕文件名字来决定写入哪个弹幕文件
// TODO: 这里文件名的判断可以用正则来解决
$barrageFile = isset($_GET) &&
	isset($_GET['barrage']) ? $_GET['barrage'] : $config['defaultBarrageFile'];
$barrageFileFullPath = $config['barragePath'] . $barrageFile . '.xml';
	 

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
	echo 'only accept POST method...';
	exit();
}

// $post变量为“1.5,1,25,test,#FFFFFF,2015-09-01 09:21,咦！！！！”这种形式
$post = file_get_contents("php://input"); 
// TODO: 暂时用限制切割最大次数解决了如果content里带“,”会导致报错的问题，想个优雅的方法
$length = count(explode(',', $config['barrageFormat']));
$barrageElements = explode(',', $post, $length); 
// 
if (count($barrageElements) != $length) {
	echo 'length wrong?';
	exit();
}
// 给单引号加上转义
$replaceStr = str_replace("'", "\'", $post);

$bfp = fopen($barrageFileFullPath, "r+") or die('Unable to open file...');
// 移动指针至结尾处，TODO：智能判断一下，跳掉后面的\r\n等
fseek($bfp, -2, SEEK_END);
$toWriteContent = ",\r\n'" . $replaceStr . "'];"; // 将$post作为弹幕写入文件
fwrite($bfp, $toWriteContent);
fclose($bfp);

echo 'success';
exit();
?>