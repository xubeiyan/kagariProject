<?php
	/* 
	*	弹幕类
	*
	*/	
	class Comment {
		private $id;		// 弹幕ID
		private $vid;		// 视频ID
		private $stime;		// 视频时间
		private $mode;		// 弹幕类型
		private $color;		// 弹幕颜色
		private $size;		// 弹幕字号
		private $time;		// 发送时间
		private $message;	// 弹幕内容
		private $uid;		// 用户ID
		
		function __construnct($vid){
			$this -> $vid = $vid;
		}
		
		/*
		*	建立储存弹幕的表
		*/
		public static function createCommentTable(){
			mysql_select_db($database['dbName'], $con);
			$sql = 'CREATE TABLE comment (	
				ID int NOT NULL AUTO_INCREMENT,
				PRIMARY KEY(ID),	
				vid int,
				uid int
				stime int,
				color char(6),
				mode int(5),
				size int(100),
				time date,
				message text
			)';
			mysql_query($sql, $con);
		}
		
		/*
		*	根据vid从数据库获取弹幕，返回的结果是
		*/
		public static function getCommentsByVid($vid){
			$sqlQuery = sprintf("SELECT * FROM comment WHERE vid=%d", $vid);
			$sqlQuery = mysql_real_escape_string($sqlQuery);
			return mysql_query($sqlQuery, $con); 
		}
		/*
		*	将该弹幕添加到数据库
		*/
		public function insertComment(){
			if($this ->checkFullValue()){
				$sqlQuery = sprintf("INSERT INTO comment (vid, uid, stime, color, mode, size, time, message)
						VALUES (%d, %d, %d, %d, %d, %d, %d, %s)",
						$this -> $vid, $this -> $uid, $this -> $stime, $this -> $color, $this -> $mode, $this -> $size, $this -> $time, $this -> $message);
				$sqlQuery = mysql_real_escape_string($sqlQuery);
				mysql_query($sqlQuery, $con);
			}
		}
		
		/*
		*	根据ID删除指定的弹幕
		*/
		public function deleteCommentById() {
			if (isset($this -> $id)){
				$sqlQuery = sprintf("DELECT FROM comment
									WHERE id=%s",$this -> $id);
				$sqlQuery = mysql_real_escape_string($sqlQuery);
				mysql_query($sqlQuery, $con);
			}
		}
		
		/*
		*	将当前弹幕转化为字符串
		*/
		private function toXMLString() {
			$xmlString = sprintf("<d p='%d,%d,%d,%d,%d,0,%d,%d'>%s</d>\n",
								$this -> $stime, $this -> $mode, $this -> $size, $this -> $color, $this -> $time, 
								$this -> $uid, $this -> $id, $this -> $message);
			return $xmlString;
		}
		/*
		*	用于检查所有的值是否合法
		*/
		private function checkFullValue() {
			if (!is_numeric($this -> $vid))
				return false;
			if (!is_numeric($this -> $stime))
				return false;
			if (!is_numeric($this -> $color))
				return false;
			if (!is_numeric($this -> $mode))
				return false;
			if (!is_numeric($this -> $size))
				return false;
			if (!is_numeric($this -> $uid))
				return false;
			return true;
		}
		
		/*
		*	输出为XML文件
		*/
		public static function XMLByVid($vid) {
			$result = Comment.getCommentsByVid($vid);
			$xml = "<?xml version=\"1.0\" encoding=\"UTF-8\" ?><i>\n"
			while($row = mysql_fetch_array($result)){
				$string = sprintf("<d p='%d,%d,%d,%d,%d,0,%d,%d'>%s</d>\n",
								$row['stime'], $row['mode'], $row['size'], $row['color'], $row['time'], 
								$row['uid'], $row['id'], $row['message']);
				$xml .= $string;
			}
			$xml .= '</i>'
			return $xml;
		}
		
		/*
		*	输出为html表格
		*/
		public static function tableByVid($vid) {
			$result Comment.getCommentsByVid($vid);
			$html = "<table>\n
					<tr><td>发送时间</td><td>模式</td><td>大小</td><td>颜色</td><td>时间戳</td>
					<td>用户ID</td><td>弹幕ID</td><td>弹幕内容</td></tr>"
			while($row = mysql_fetch_array($result)) {
				$string = sprintf("<tr><td>%d</td><td>%d</td><td>%s</td><td>%s</td><td>%s</td>
								<td>%s</td><td>%s</td><td>%s</td></tr>\n",
								$row['stime'], $row['mode'], $row['size'], $row['color'], $row['time'], 
								$row['uid'], $row['id'], $row['message']);
				$html .= $string;
			}
			$html .= "</table>";
			return $html;
		}
	}
?>