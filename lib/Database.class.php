<?php	
	class Database {
		// 配置
		private static $database = array(
			"host" => "localhost:3306",
			"user" => "root",
			"pass" => "",
			"dbName" => "kagari",
			"videoTable" => "video",
			"userTable" => "user"
		);
		public static $sql;
		
		// 创建数据库
		public static function createDatabase() {
			$database = self::$database;
			$sql = self::$sql;
			$sqlQuery = sprintf("create database if not exists %s character set utf8", $database['dbName']);
			if (mysqli_query($sql, $sqlQuery)) {
				echo "database " . $database['dbName'] . " created<br />";
			} else {
				echo mysqli_error($sql);
				exit();
			}
		}
		// 连接数据库
		public static function connectDatabase() {
			$database = self::$database;
			self::$sql = mysqli_connect($database['host'], $database['user'], $database['pass']);
			if (!self::$sql) {
				die('无法连接到MySQL数据库……');
			}
		}
		// 使用数据库
		public static function useDatabase() {
			$database = self::$database;
			$sql = self::$sql;
			mysqli_query($sql, 'use ' . $database['dbName']);
			mysqli_set_charset($sql, 'utf8');
		}
		
		/*
		*	新建user表
		*/
		public static function createUserTable() {
			$sql = self::$sql;
			/*
			* user表: id, 用户名, 密码, 用户类型, 积分
			*/
			$sqlQuery = sprintf("CREATE TABLE user (
								ID int NOT NULL AUTO_INCREMENT,
								PRIMARY KEY(ID),
								username char(63),
								nickname char(63),
								password char(255),
								type int default 0,
								point int default 0
								)");
			if(mysqli_query($sql, $sqlQuery)) {
				echo "user table created<br />";
			} else {
				echo mysqli_error($sql);
			};
		}
		
		/*
		*	创建视频表
		*/
		public static function createVideoTable() {
			$sql = self::$sql;
			/*
			* video表: id, 上传者id, 播放次数, 视频类型, 评分, 标题, 描述
			*/
			$sqlQuery = sprintf("CREATE TABLE video (
								ID int NOT NULL AUTO_INCREMENT,
								PRIMARY KEY(ID),
								upuid int default 0,
								playtime int default 0,
								type int default 0,
								point int default 0,
								title char(255) character set utf8,
								description text character set utf8
								)");
			if(mysqli_query($sql, $sqlQuery)) {
				echo "video table created<br />";
			} else {
				echo mysqli_error($sql);
			};
		}
		
		// 写入测试数据
		public static function testData() {
			$database = self::$database;
			$sql = self::$sql;
			$sqlQuery = sprintf("INSERT INTO %s (username, nickname, password, type, point) VALUES 
												('test', '伊卡洛斯', 'test', 2, 999)", $database['userTable']);
			if (mysqli_query($sql, $sqlQuery)) {
				echo 'test user data has updated<br />';
			} else {
				echo mysql_error($sql);
			}
			$sqlQuery = sprintf("INSERT INTO %s (upuid, playtime, type, point, title, description) VALUES
												(1, 2, 1, 2, '[Key社]late in autumn--6分钟叙述Key社作中众生的悲欢离合', '修复LB的tag问题 6分钟MAD略长…终于做完了。Key社带给大家的感动在此就不过多赘述了,请诸位静下心来欣赏体会罢——本来是准备参加MAD大赛的坑,暑假忙于各其它事务未能填上,前段时间又因开学忙于他事,为了有始有终这几日狠下心来填完了…特别感谢Z·Dark、LittlePox、肤浅君(汐渚之岸)。十月中旬前后会因3次元之事离开B站,归期不定,但愿这不是我最后1个MAD罢…')",
												$database['videoTable']);
			if (mysqli_query($sql, $sqlQuery)) {
				echo 'test video data has updated<br />';
			} else {
				echo mysql_error($sql);
			}
		}
	}
	
?>