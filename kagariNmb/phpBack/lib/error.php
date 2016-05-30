<?php
/**
* 错误信息输出
* 闲扯: 最近看见err就以为是英梨梨的我是不是该找个女朋友了呢（拍死
*/
class Error {
	private static $errMsgList = Array(
		// 请求错误的URI
		'requestInvalidURI' => 'request URI expects to be %1, but %2 was get...',
		// 不允许的请求方式
		'notAllowedRequestMethod' => 'request method %1 not allowed...',
		// 不是指定的UserAgent
		'notSpecificUserAgent' => 'User Agent String %1 was get...',
		// 不被允许的API
		'notAllowedAPI' => 'request a not allowed API: %1...',
		// 未安装
		'notInstalled' => 'kagari Nimingban is not installed, please access %1...',
		// JSON数据格式有误
		'badJSON' => 'the JSON cannot be decoded...',
		// 未找到指定的表
		'notSelectedTable' => 'the table %1 not found...'
	);
	// 输出错误信息，$name为错误名称，$paras为待输出的信息
	public static function errMsg($name, $paras, $json = true) {
		$string = self::$errMsgList[$name];
		for ($i = 0; $i < count($paras); ++$i) {
			$string = str_replace('%' . ($i + 1), $paras[$i], $string);
		}
		
		if ($json) {
			$response = Array(
				'error' => $name,
				'message' => $string
			);
			$responseJSON = json_encode($response, JSON_UNESCAPED_UNICODE); // 神奇勿动233
			return $responseJSON;
		} else {
			return $string;
		}
	}
}
?>