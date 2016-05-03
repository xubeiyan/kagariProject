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
* area_id(所属区名称), 
* user_id(发布用户名称), 
* reply_post_id(跟串id，就是是在哪个串下面的ID，没有则是主串), 
* send_time(发布时间), 
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
* 获取板块列表  
  `/api/getAreaLists`   
提交内容：(暂无)  

* 获取板块串   
  `/api/getAreaPosts`  
提交内容：  
  `area_id`    
  `area_page`   

* 获取串内容   
  `/api/getPost`   
提交内容：  
  `post_id`   
  `post_page`    

* 发表新串   
  `/api/sendPost`     
提交内容：   
  `user_id`(用户id，必需)      
  `author_name`   
  `author_email`   
  `post_title`   
  `post_content`(串内容，必需)    
  `post_image`
