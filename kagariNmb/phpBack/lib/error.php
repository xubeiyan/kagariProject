<?php
/**
* 错误信息输出
* note: 最近看见err就以为是英梨梨的我是不是该找个女朋友了呢（拍死
*/
class Error {
	private static $errMsgList = Array(
		// 请求错误的URI
		'requestInvalidURI' => 'request URI expects to be %1, but %2 was get...',
		// 不是指定的UserAgent
		'notSpecificUserAgent' => 'User Agent String %1 was get...',
		// 不被允许的API
		'notAllowedAPI' => 'request a not allowed API: %1...'
	);
	// 输出错误信息，$name为错误名称，$paras为待输出的信息
	public static function errMsg($name, $paras) {
		$string = self::$errMsgList[$name];
		for ($i = 0; $i < count($paras); ++$i) {
			$string = str_replace('%' . ($i + 1), $paras[$i], $string);
		}
		return $string;
	}
}
?>