<?php
#error_reporting(0);

header("Content-type: text/html; charset=utf-8"); 
if(file_exists("./config.php")){
	die("配置文件已存在，如果您想重新安装，请删除 config.php，如果您已安装完毕，建议删除本文件。");
}

$showHtml=true;

if(	isset($_POST['DB_HOST']) && isset($_POST['DB_USER']) && isset($_POST['DB_PASSWD']) && isset($_POST['DB_NAME']) && isset($_POST['USER_NAME']) && isset($_POST['USER_PASSWD']) && isset($_POST['USER_MAIL']) ){
	$showHtml=false;
	$link=new mysqli($_POST['DB_HOST'],$_POST['DB_USER'],$_POST['DB_PASSWD'],$_POST['DB_NAME']);
	if(mysqli_connect_errno()){
		die(mysqli_connect_error());
	}

	$dbName=$_POST['DB_NAME'];
	$dbHost=$_POST['DB_HOST'];
	$dbUser=$_POST['DB_USER'];
	$dbPassword=$_POST['DB_PASSWD'];

	$content="<?php\ndefine('SQL_CONFIG',[\n\t'DB_HOST'	=> '$dbHost',\n\t'DB_USER'	=> '$dbUser',\n\t'DB_PASS'	=> '$dbPassword',\n\t'DB_NAME'	=> '$dbName',\n]);";

	$ip=ip2long($_SERVER['REMOTE_ADDR']);
	$username=$_POST['USER_NAME'];
	$password=$_POST['USER_PASSWD'];
	if($password==''){
		$a='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!"#$%&\'()*+,-./:;<=>?@[\\]^_`{|}~';
		$password=substr(str_shuffle($a),mt_rand(0,strlen($a)-11),10);
		echo '您的密码为:',$password,'<br/>';
	}
	$email=$_POST['USER_MAIL'];
	$key=md5(sha1( uniqid( '', true ).mt_rand(1000000000,9999999999) ));
	$time=time();
	$password = md5($password.$key);

	inputCheck('name',$username);
	inputCheck('email',$email);



// 导入数据表
$link->query("DROP TABLE IF EXISTS `users_info`") or die('SQL error');
$link->query("CREATE TABLE IF NOT EXISTS `users_info`(
  `id`          int(11)          NOT NULL AUTO_INCREMENT                             ,
  `name`        varchar(50)      NOT NULL DEFAULT 'unknown'                          ,
  `password`    char(32)         NOT NULL DEFAULT ''                                 ,
  `email`       varchar(50)      NOT NULL DEFAULT 'unknow@unknown.com'               ,
  `team_id`     varchar(50)      NOT NULL DEFAULT '0'                                ,
  `key`         char(32)         NOT NULL DEFAULT '00000000000000000000000000000000' ,
  `extra_score` int(11)          NOT NULL DEFAULT '0'                                ,
  `hint_score`  int(11)          NOT NULL DEFAULT '0'                                ,
  `nickname`    varchar(50)      NOT NULL DEFAULT 'anonymous'                             ,
  `said`        varchar(50)      NOT NULL DEFAULT ''                                 ,
  `reg_time`    int(11) UNSIGNED NOT NULL DEFAULT '0'                                ,
  `reg_ip`      int(11) UNSIGNED NOT NULL DEFAULT '0'                                ,
  `logged_time` int(11) UNSIGNED NOT NULL DEFAULT '0'                                ,
  `logged_ip`   int(11) UNSIGNED NOT NULL DEFAULT '0'                                ,
  `big_img`     mediumtext       NOT NULL                                            ,
  `tiny_img`    text             NOT NULL                                            ,
  `is_verify`	tinyint(1)		 NOT NULL DEFAULT '0'                                ,
  `is_hide`     tinyint(1)       NOT NULL DEFAULT '0'                                ,
  `is_ban`      tinyint(1)       NOT NULL DEFAULT '0'                                ,
  `is_admin`    tinyint(1)       NOT NULL DEFAULT '0'                                ,
  `is_delete`   tinyint(1)       NOT NULL DEFAULT '0'                                ,
  PRIMARY KEY(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8") or die('SQL error1');

$link->query("DROP TABLE IF EXISTS `users_team`") or die('SQL error');
$link->query("CREATE TABLE IF NOT EXISTS `users_team`(
  `id`          int(11)          NOT NULL AUTO_INCREMENT    ,
  `name`        varchar(50)      NOT NULL DEFAULT 'unknown' ,
  `create_time` int(11) UNSIGNED NOT NULL DEFAULT '0'       ,
  `is_leader`   tinyint(1)       NOT NULL DEFAULT '0'       ,
  `is_hide`     tinyint(1)       NOT NULL DEFAULT '0'       ,
  `is_delete`     tinyint(1)       NOT NULL DEFAULT '0'       ,
  PRIMARY KEY(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8") or die('SQL error2');

$link->query("DROP TABLE IF EXISTS `ctf_challenges`") or die('SQL error');
$link->query("CREATE TABLE IF NOT EXISTS `ctf_challenges` (
  `id`          int(11)          NOT NULL AUTO_INCREMENT  ,
  `hard_level`  tinyint(1)       NOT NULL DEFAULT '0'     ,
  `create_time` int(11) UNSIGNED NOT NULL DEFAULT '0'     ,
  `edit_time`   int(11) UNSIGNED NOT NULL DEFAULT '0'     ,
  `type`        tinyint(1)       NOT NULL DEFAULT '0'     ,
  `type_id`     int(1)       NOT NULL DEFAULT '0'     ,
  `docker_id`   int(11)          NOT NULL DEFAULT '0'     ,
  `score`       int(11)          NOT NULL DEFAULT '0'     ,
  `title`       varchar(100)     NOT NULL DEFAULT ''      ,
  `content`     text             NOT NULL                 ,
  `flag`        varchar(100)     NOT NULL DEFAULT ''      ,
  `depends`		varchar(100)	 NOT NULL DEFAULT ''	  ,
  `seed`        char(5)          NOT NULL DEFAULT 'ILWYE' ,
  `is_rand`     tinyint(1)       NOT NULL DEFAULT '0'     ,
  `is_hide`     tinyint(1)       NOT NULL DEFAULT '1'     ,
  `is_delete`   tinyint(1)       NOT NULL DEFAULT '0'     ,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8") or die('SQL error3');

$link->query("DROP TABLE IF EXISTS `ctf_submits`") or die('SQL error');
$link->query("CREATE TABLE IF NOT EXISTS `ctf_submits` (
  `id`       int(11)          NOT NULL AUTO_INCREMENT ,
  `user_id`  int(11)          NOT NULL DEFAULT '0'    ,
  `ques_id`  int(11)          NOT NULL DEFAULT '0'    ,
  `sub_time` int(11) UNSIGNED NOT NULL DEFAULT '0'    ,
  `sub_ip`   int(11) UNSIGNED NOT NULL DEFAULT '0'    ,
  `sub_flag` varchar(100)     NOT NULL DEFAULT ''     ,
  `is_pass`  tinyint(1)       NOT NULL DEFAULT '0'    ,
  `is_hide`  tinyint(1)       NOT NULL DEFAULT '0'    ,
  `is_delete` tinyint(1)       NOT NULL DEFAULT '0'    ,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8") or die('SQL error4');

$link->query("DROP TABLE IF EXISTS `notices`") or die('SQL error');
$link->query("CREATE TABLE IF NOT EXISTS `notices` (
  `id`          	int(11)          NOT NULL AUTO_INCREMENT ,
  `create_time` 	int(11) UNSIGNED NOT NULL DEFAULT '0'    ,
  `create_user_id`  int(11)       	 NOT NULL DEFAULT '0'    ,
  `edit_time`   	int(11) UNSIGNED NOT NULL DEFAULT '0'    ,
  `edit_user_id`    int(11)       	NOT NULL DEFAULT '0'    ,
  `content`     	text             NOT NULL                ,
  `is_hide`     	tinyint(1)       NOT NULL DEFAULT '1'    ,
  `is_delete`   	tinyint(1)       NOT NULL DEFAULT '0'    ,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8") or die('SQL error5');

$link->query("DROP TABLE IF EXISTS `docker_use_lists`") or die('SQL error');
$link->query("CREATE TABLE IF NOT EXISTS `docker_use_lists` (
  `id`          int(11)      	 NOT NULL AUTO_INCREMENT ,
  `user_id`     int(11)      	 NOT NULL DEFAULT '0'    ,
  `ques_id`     int(11)      	 NOT NULL DEFAULT '0'    ,
  `docker_id`   int(11)      	 NOT NULL DEFAULT '0'    ,
  `docker_name` varchar(32)		 NOT NULL DEFAULT ''	 ,
  `ret_url`     varchar(100) 	 NOT NULL DEFAULT '#'    ,
  `create_time` int(11) UNSIGNED NOT NULL DEFAULT '0'    ,
  `user_delete` tinyint(1)		 NOT NULL DEFAULT '0'    ,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8") or die('SQL error6');


$link->query("DROP TABLE IF EXISTS `users_action`") or die('SQL error');
$link->query("CREATE TABLE IF NOT EXISTS `users_action`(
  `id`      int(11)          NOT NULL AUTO_INCREMENT ,
  `user_id` int(11)          NOT NULL DEFAULT '0'    ,
  `ip`      int(11) UNSIGNED NOT NULL DEFAULT '0'    ,
  `time`    int(11) UNSIGNED NOT NULL DEFAULT '0'    ,
  `states`  int(11)          NOT NULL DEFAULT '0'    ,
  `is_hide`   tinyint(1)       NOT NULL DEFAULT '0'    ,
  `is_delete` tinyint(1)       NOT NULL DEFAULT '0'    ,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8") or die('SQL error7');

$link->query("DROP TABLE IF EXISTS `configs`") or die('SQL error');
$link->query("CREATE TABLE IF NOT EXISTS `configs`(
  `id`    int(11)     NOT NULL AUTO_INCREMENT ,
  `name`  varchar(50) NOT NULL DEFAULT ''     ,
  `value` varchar(100)  NOT NULL DEFAULT ''   ,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8") or die('SQL error8');


// 写入初始数据

$link->query("INSERT INTO `configs`(`id`,`name`,`value`) VALUES
(1,'reg_open','1'),
(2,'sub_open','1'),
(3,'login_open','1'),
(4,'ctf_open','1'),
(5,'website_open','1'),
(6,'dynamic_score_open','1'),
(7,'one_blood_open','1'),
(8,'blood_score','{10,6,3,1}'),
(9,'recent_solve_show_num','10'),
(10,'docker_exist_time','3600'),
(11,'email_username',''),
(12,'email_password',''),
(13,'super_password','!@#RTRGFEW'),
(14,'random_flag_head_fmt','flag'),
(15,'ctf_name','Simple CTF'),
(16,'ctf_organizer','Simple'),
(17,'email_verify_open','0')") or die('SQL error');

$link->query("INSERT INTO `users_info` (`name`,`password`,`email`,`key`,`reg_time`,`reg_ip`,`big_img`,`tiny_img`,`is_verify`,`is_hide`,`is_admin`) 
VALUES('$username','$password','$email','$key','$time','$ip','','','1','1','1')") or die('SQL error');

	// 写入 config.php 文件
	if(!file_put_contents('config.php',$content)){
		highlight_string($content);
		die("<br/># 请确认当前目录下是否有文件写权限？或手动将本页面数据保存为 config.php");
	}
	echo "写入初始数据成功！<a href='./index.html'>手动跳转</a><br/>";
}

$html=<<<'HTML'
<!DOCTYPE html>
<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0, user-scalable=no"/>
	<title>简便安装</title>
	<link href="//cdn.bootcss.com/materialize/0.98.2/css/materialize.min.css" type="text/css" rel="stylesheet" media="screen,projection"/>
	<script src="//cdn.bootcss.com/jquery/3.3.1/jquery.min.js"></script>
	<script src="//cdn.bootcss.com/highcharts/6.0.7/highcharts.js"></script>
	<script src="//cdn.bootcss.com/highcharts/6.0.7/modules/sunburst.js"></script>
	<script src="//cdn.bootcss.com/materialize/0.98.2/js/materialize.min.js"></script>
	<link href="./favicon.ico" rel="shortcut icon"/>
	<link href="./css/style.css" rel="stylesheet" type="text/css"/>
</head>
<body>
	<header style="padding-bottom: 3px;">
		<nav class="black" role="navigation">
			<div class="container">
				<div class="nav-wrapper">
					<a href="#" data-activates="slide-out" class="page-title" style="font-size: 30px;">A Simple CTF</a>
				</div>
			</div>
		</nav>
	</header>
	<form method="post" action="#">
		<div class="no-pad-bot section" id="index-banner">
			<div class="container">
				<h3 class="header center blue-text text-darken-2">Mysql 配置</h3>
			</div>
		</div>
		<div class="container" style="font-size: 20px">
			<div class="section row">
				<div class="input-field col s8 m6 offset-s2 offset-m3">
					<input placeholder="localhost" value="localhost" name="DB_HOST" id="DB_HOST" type="text" class="validate" required/>
					<label for="DB_HOST">数据库地址</label>
				</div>
				<div class="input-field col s8 m6 offset-s2 offset-m3">
					<input placeholder="root" value="root" name="DB_USER" id="DB_USER" type="text" class="validate" required/>
					<label for="DB_USER">数据库用户名</label>
				</div>
				<div class="input-field col s8 m6 offset-s2 offset-m3">
					<input placeholder="" value="" name="DB_PASSWD" id="DB_PASSWD" type="text" class="validate">
					<label for="DB_PASSWD">数据库密码</label>
				</div>
				<div class="input-field col s8 m6 offset-s2 offset-m3">
					<input placeholder="ctf" value="ctf" name="DB_NAME" id="DB_NAME" type="text" class="validate" required/>
					<label for="DB_NAME">数据库名</label>
				</div>
			</div>
		</div>
		<div class="no-pad-bot section" id="index-banner">
			<div class="container">
				<h3 class="header center blue-text text-darken-2">管理员配置</h3>
			</div>
		</div>
		<div class="container" style="font-size: 20px">
			<div class="section row">
				<div class="input-field col s8 m6 offset-s2 offset-m3">
					<input placeholder="admin" value="admin" name="USER_NAME" id="USER_NAME" type="text" class="validate" required/>
					<label for="USER_NAME">用户名</label>
				</div>
				<div class="input-field col s8 m6 offset-s2 offset-m3">
					<input placeholder="留空将随机生成10位的密码" value="" name="USER_PASSWD" id="USER_PASSWD" type="text" class="validate"/>
					<label for="USER_PASSWD">密码</label>
				</div>
				<div class="input-field col s8 m6 offset-s2 offset-m3">
					<input placeholder="请填写一个您的常用邮箱" value="" name="USER_MAIL" id="USER_MAIL" type="email" class="validate" required/>
					<label for="USER_MAIL" data-error="wrong" data-success="right">邮箱</label>
				</div>
				<div class="col s12 center">
					<button class="btn waves-effect waves-light" type="submit">提交<i class="material-icons right">send</i></button>
				</div>
			</div>
		</div>
	</form>
</body>
</html>
HTML;

if($showHtml)
	echo $html;


#对输入的合法性进行判断(null)
function inputCheck($type,$data){
	switch ($type){
		case 'email':
			empty($data) and die('请填写邮箱！');
			(strlen($data)>30) and die('邮箱过长！');
			preg_match( "/^([0-9A-Za-z\\-_\\.]+)@([0-9a-z]+\\.[a-z]{2,3}(\\.[a-z]{2})?)$/i", $data) or die('邮箱格式不正确！');
			break;
		case 'name':
			empty($data) and die('你必须输入用户名！');
			(strlen($data)>15) and die('用户名过长！');
			preg_match("/\s/", $data) and die('用户名中请不要出现空格！');
			preg_match("/[\'.,:;*?~`!@#$%^&+=)(<>{}]|\]|\[|\/|\\\|\"|\|/", $data) and die('用户名中请不要输入特殊字符！');
			break;
		case 'password':
			(strlen($data)<6) and die('密码长度过短！');
			break;
		default:
			die('数据错误');
			break;
	}
	return true;
}