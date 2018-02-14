<?php
return array(
	//'配置项'=>'配置值'
	'DB_TYPE'               =>  'mysql',     // 数据库类型
    'DB_HOST'               =>  '112.74.79.222', // 服务器地址
    'DB_NAME'               =>  'jingyi2',    // 数据库名
    'DB_USER'               =>  'xiao',      // 用户名
    'DB_PWD'                =>  '123456',      // 密码
    'DB_PORT'               =>  '3306',        // 端口
    'DB_PREFIX'             =>  '',    // 数据库表前缀
    'DB_FIELDTYPE_CHECK'    =>  false,       // 是否进行字段类型检查
    'DB_FIELDS_CACHE'       =>  true,        // 启用字段缓存
    'DB_CHARSET'            =>  'utf8mb4',      // 数据库编码默认采用utf8
    'SESSION_AUTO_START' => true, //是否开启session
    'URL_MODEL' => 1    ,     // 默认false 表示URL区分大小写 true则表示不区分大小写
    'URL_CASE_INSENSITIVE'  =>  true,
	'imgpathurl'=>"http://112.74.79.222/jingyi2",
	'imgpath'=>"112.74.79.222",//用于编辑器替换地址
	'TELCODE_ACCOUNTA' =>'cf_JEANONE',  	//短信账号
	'TELCODE_ACCOUNTA_OTHER' =>'I05692365',  	//国际短信账号
	
	'TELCODE_PASSWORD'=>'b477e970ff82a2b2e8addf89eac205ec',        	//短信密码
	'TELCODE_PASSWORD_OTHER'=>'3115fd7e0438b740c26aed42e4c01ae2',        	//国际短信密码
	'codetime'=>'120',                  //短信验证码有效时间 （秒）
	'uploadpath' => 'Public/upload',     //上传图片地址
	'membermoney'=>300,     //会员卡单价
	'membercordpfloor'=>10,     //会员卡父级分成层数
	'membercordpmoney'=>10,     //会员卡父级分成钱数
	'LOAD_EXT_FILE' =>  'common,pay,weixin',   //自动加载Common目录下载PHP文件
	'LOAD_EXT_CONFIG'=>'question',              //邀请问题配置信息
	'HOST'=>"http://112.74.79.222/jingyi/index.php/Home/",

	'hx_client_id' => 'YXA6VUxNQCDbEeeqkpuP9WCsvw',     //环信client_id
	'hx_client_secret' => 'YXA6MT-Wry331u2FVsZzkj9TPlgLuw8',     //环信Client Secret:
	'hx_org_name' => '1132161226178269',     //环信org_name
	'hx_app_name' => 'jingyi',     //环信app_name
	'hx_user_password' => '123456',     //环信注册用户默认密码
	
	"planet_lvl1"=>0,
	"planet_lvl2"=>300,
	"planet_lvl3"=>600,
	"planet_lvl4"=>1000,
	'activity_gettime'=>"84600", //报名时间是活动开始前1天前
	'activity_begin'=>"84600", //活动开始时间是当前 1天后
	
	"frist_day_posts_growth"=>3,   //每日首次发帖获得成长值
	"good_praise"=>3,              //好评分值获得成长值
	"commonly_praise"=>1,   	   //中评分值获得成长值
	"bad_praise"=>-3,   			//差评分值获得成长值
	
    "activity_good_comment"=>1,   //活动评价积分--好评
	"activity_bad_comment"=>-1,   //活动评价积分--差评
	
	
	'jpush_app_key' => '65b45b31ae2cafc30049cfaf',     //jpush app_key
	'jpush_master_secret' => '8626c6845f475ff5dd8dc1a7',     //jpush master_secret
	'jpush_apns_production' => True,     //jpush ios环境 True 表示推送生产环境，False 表示要推送开发环境；如果不指定则为推送生产环境。
	
);