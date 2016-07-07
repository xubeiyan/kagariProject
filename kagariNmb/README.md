kagari Nimingba(匿名版)
======================

>纯属个人练手作品   

考虑后端用php+mysql，以及python+elasticsearch各做一个版本

前后端数据交互使用json（方便开发？）

>匿名版说是匿名版但其实还是将你的信息绑定到了IP上，于是就有了某个字符串代表用户这样的规定（貌似A岛是这样的）。然后有个认证User-Agent的地方，于是乎那些不能修改User-Agent的浏览器也被踢掉了。回某个串会将其在某个区顶起来，但是也可以加上SAGE让其保持下沉状态

###数据库设计
####MySQL数据库：(库名kagari_Nimingban)
#####数据表名：(表名前缀kagari_)
######user:(用户信息)
* user_id(primary key), 
* ip_address(IP地址), 
* user_name(随机生成字符串还是按规律增长的字符串), 
* block_time(被阻止时间，秒数？分钟数？亦或是被永久),
* last_post_id(最后发串id？这个功能疑似没啥用啊)

######area:(分区)
* area_id(primary key), 
* area_name(区名), 
* area_sort(排序，在获取板块列表的时候的顺序),
* block_status(被阻止的状态，禁回复，禁发帖，转向第一个子分区), 
* parent_area(此分区的父分区), 
* min_post(最小发串间隔)

######post:(串)
* post_id(primary key), 
* area_id(所属区id), 
* user_id(发布用户id), 
* reply_post_id(跟串id，就是是在哪个串下面的ID，没有则是主串), 
* author_name(作者名), 
* author_email(作者邮箱名), 
* post_title(串标题), 
* post_content(串内容), 
* post_images(图片，应该是可以支持多张图片的),
* create_time(此串发布时间),
* update_time(此串更新时间，普通回复更新，SAGE则不更新)

###API设计列表
>会同时接受JSON和multipart/form-data(因为会上传图片)

####用户级别:
* 获取饼干  
  `/api/getCookie`    
提交内容：(暂无)    
返回内容：(举例)    
	```javascript
	{
		"request": "getCookie",
		"response": {
			"timestamp": "2016-06-06 10:15:34",
			"ip": "::1",
			"username": "1abCDEF"
		}
	}
	```
* 获取板块列表  
  `/api/getAreaLists`   
提交内容：(暂无)  
返回内容：(举例)    
	```javascript
	{
		"request": "getAreaList", 
		"response": {
			"timestamp": "2016-05-24 13:53:05",
			"areas": [
			{
				"area_id": 1,
				"area_name": "综合",
				"parent_area": ""
			},
			{
				"area_id": 2,
				"area_name": "综合版1",
				"parent_area": 1
			}]
		}
	}
	```

* 获取板块串   
  `/api/getAreaPosts`  
提交内容：    
  `area_id`    
  `area_page`    
返回内容：(举例)(返回结果)  
	```javascript
	{
		"request": "getAreaPosts",
		"response": {
			"timestamp": "2016-05-27 16:26:24",
			"area_id": 2,
			"area_name": "综合版1",
			"area_page": 1,
			"posts_per_page": 50,
			"posts": [{
				"post_id": 10000,
				"post_title": "无标题",
				"post_content": "aaabbbccc",
				"post_images": "1.png",
				"user_id": 1,
				"user_name": "1wuQKIZ",
				"author_name": "无名氏",
				"author_email": "",
				"create_time": "2016-05-27 16:37:45",
				"update_time": "2016-05-27 16:38:56",
				"reply_num": 2,
				"reply_recent_posts": [{
					"post_id": 10001,
					"user_id": 1,
					"user_name": "1wuQKIZ",
					"author_name": "无名氏",
					"author_email": "",
					"post_title": "无标题",
					"post_content": "dddeeefff",
					"post_images": "2.png,3.jpg",
					"create_time": "2016-05-27 16:38:45",
					"update_time": "2016-05-27 16:39:56",
				},
				{
					"post_id": 10002,
					"user_id": 2,
					"user_name": "1mjIUYJ",
					"author_name": "无名氏",
					"author_email": "",
					"post_title": "无标题",
					"post_content": "ggghhhiii",
					"post_images": "",
					"create_time": "2016-05-27 16:40:45",
					"update_time": "2016-05-27 16:41:56",
				}]
			},
			{
				"post_id": 10003,
				"post_title": "无标题",
				"post_content": "aaabbbccc",
				"post_images": "1.png",
				"user_id": 1,
				"user_name": "1mjIUYJ",
				"author_name": "无名氏",
				"author_email": "",
				"create_time": "2016-05-27 16:37:45",
				"update_time": "2016-05-27 16:38:56",
				"reply_num": 0,
				"reply_recent_posts": []
			}]
		}
	}
	```  
(返回未找到板块)
	```javascript
	{
		"request": "getAreaList",
		"response": {
			"timestamp": "2016-06-18 17:19:34",
			"error": "未找到对应的板块"
		}
	}
	```
* 获取串内容   
  `/api/getPost`   
提交内容：  
  `post_id`   
  `post_page`    
返回内容：(举例)(返回结果)     
	```javascript
	{
		"request": "getPost",
		"response": {
			"timestamp": "2016-05-27 17:06:43",
			"post_id": 10000,
			"post_page": 1,
			"posts_per_page": 50,
			"post_title": "无标题",
			"post_content": "aaabbbccc",
			"post_images": "1.png",
			"user_id": 1,
			"user_name": "1wuQKIZ",
			"author_name": "无名氏",
			"author_email": "",
			"create_time": "2016-05-27 16:37:45",
			"update_time": "2016-05-27 16:38:56",
			"reply_num": 2,
			"reply_recent_posts": [{
				"post_id": 10001,
				"user_id": 1,
				"user_name": "1wuQKIZ",
				"author_name": "无名氏",
				"author_email": "",
				"post_title": "无标题",
				"post_content": "dddeeefff",
				"post_images": "2.png,3.jpg",
				"create_time": "2016-05-27 16:38:45",
				"update_time": "2016-05-27 16:39:56",
			},
			{
				"post_id": 10002,
				"user_id": 2,
				"user_name": "1mjIUYJ",
				"author_name": "无名氏",
				"author_email": "",
				"post_title": "无标题",
				"post_content": "ggghhhiii",
				"post_images": "",
				"create_time": "2016-05-27 16:40:45",
				"update_time": "2016-05-27 16:41:56",
			}]
		}
	}
	```
(返回未找到帖子)
	```javascript
	{
		"request": "getPost",
		"response": {
			"timestamp": "2016-06-28 11:05:12",
			"error": "未找到相应帖子"
		}
	}
	```

* 发表新串   
  `/api/sendPost`     
提交内容：   
  `user_id`(用户id，必需)   
  `area_id`(分区id，必需)     
  `reply_post_id`(回复还是新串，新串为0，为空则为0)    
  `author_name`   
  `author_email`   
  `post_title`   
  `post_content`(串内容，必需)    
  `post_image`    
 返回内容：(正常回帖)    
	```javascript
	{
		"request": "sendPost",
		"response": {
			"timestamp": "2016-06-06 10:50:45",
			"status": "OK"
		}
	}
	```
(不存在的帖子)     
	```javascript
	{
		"request": "sendPost",
		"response": {
			"timestamp": "2016-06-29 13:17:09",
			"error": "回复串不存在"
		}
	}
	```
(所在的帖子为回复帖子)     
	```javascript
	{
		"request": "sendPost",
		"response": {
			"timestamp": "2016-06-29 13:21:35",
			"error": "回复的串不是主串"
		}
	}
	```

* 删除某个串    
`/api/deletePost`
提交内容：    
`post_id`(删除的串的id)
返回内容：(删除成功)     
	```javascript
	{
		"request": "deletePost",
		"response": {
			"timestamp": "2016-07-07 11:53:19",
			"status": "OK"
		}
		
	}
	```
(不存在的帖子)
	```javascript
	{
		"request": "deletePost",
		"response": {
			"timestamp": "2016-07-07 11:53:25",
			"error": "删除的串不存在"
		}
	}
	```