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
	$email=$_POST['USER_MAIL'];
	$key=md5(sha1( uniqid( '', true ).mt_rand(1000000000,9999999999) ));
	$time=time();
	$password = md5($password.$key);

	inputCheck('name',$username);
	inputCheck('email',$email);



// 导入数据表
$link->query("DROP TABLE IF EXISTS `users_info`") or die('SQL error');
$link->query("CREATE TABLE IF NOT EXISTS `users_info`(
  `id`          int(11)          NOT NULL AUTO_INCREMENT                             COMMENT '用户id',
  `name`        varchar(50)      NOT NULL DEFAULT 'unknown'                          COMMENT '姓名',
  `password`    char(32)         NOT NULL DEFAULT ''                                 COMMENT '密码',
  `email`       varchar(50)      NOT NULL DEFAULT 'unknow@unknown.com'               COMMENT '邮箱',
  `team_id`     varchar(50)      NOT NULL DEFAULT '0'                                COMMENT '队伍id',
  `key`         char(32)         NOT NULL DEFAULT '00000000000000000000000000000000' COMMENT '随机生成的 key ，用于随机 flag 生成以及密码的盐值',
  `extra_score` int(11)          NOT NULL DEFAULT '0'                                COMMENT '额外得分',
  `hint_score`  int(11)          NOT NULL DEFAULT '0'                                COMMENT '提示积分',
  `nickname`    varchar(50)      NOT NULL DEFAULT 'unknown'                          COMMENT '昵称',
  `said`        varchar(50)      NOT NULL DEFAULT ''                                 COMMENT '言论',
  `reg_time`    int(11) UNSIGNED NOT NULL DEFAULT '0'                                COMMENT '注册时间',
  `reg_ip`      int(11) UNSIGNED NOT NULL DEFAULT '0'                                COMMENT '注册ip',
  `logged_time` int(11) UNSIGNED NOT NULL DEFAULT '0'                                COMMENT '最后登陆时间',
  `logged_ip`   int(11) UNSIGNED NOT NULL DEFAULT '0'                                COMMENT '最后登陆ip',
  `big_img`     mediumtext       NOT NULL                                            COMMENT '大图头像',
  `tiny_img`    text             NOT NULL                                            COMMENT '小图头像',
  `is_hide`     tinyint(1)       NOT NULL DEFAULT '0'                                COMMENT '是否隐藏',
  `is_ban`      tinyint(1)       NOT NULL DEFAULT '0'                                COMMENT '是否禁止',
  `is_admin`    tinyint(1)       NOT NULL DEFAULT '0'                                COMMENT '是否管理',
  PRIMARY KEY(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户信息表'") or die('SQL error1');

$link->query("DROP TABLE IF EXISTS `users_team`") or die('SQL error');
$link->query("CREATE TABLE IF NOT EXISTS `users_team`(
  `id`          int(11)          NOT NULL AUTO_INCREMENT    COMMENT '队伍id',
  `name`        varchar(50)      NOT NULL DEFAULT 'unknown' COMMENT '队伍名称',
  `create_time` int(11) UNSIGNED NOT NULL DEFAULT '0'       COMMENT '创建时间',
  `is_leader`   tinyint(1)       NOT NULL DEFAULT '0'       COMMENT '是否队长',
  `is_hide`     tinyint(1)       NOT NULL DEFAULT '0'       COMMENT '是否隐藏',
  PRIMARY KEY(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='队伍表'") or die('SQL error2');

$link->query("DROP TABLE IF EXISTS `ctf_challenges`") or die('SQL error');
$link->query("CREATE TABLE IF NOT EXISTS `ctf_challenges` (
  `id`          int(11)          NOT NULL AUTO_INCREMENT  COMMENT '题目id',
  `hrad_level`  tinyint(1)       NOT NULL DEFAULT '0'     COMMENT '难度等级',
  `create_time` int(11) UNSIGNED NOT NULL DEFAULT '0'     COMMENT '创建时间',
  `edit_time`   int(11) UNSIGNED NOT NULL DEFAULT '0'     COMMENT '编辑时间',
  `type`        tinyint(1)       NOT NULL DEFAULT '0'     COMMENT '类型',
  `type_id`     int(1)       NOT NULL DEFAULT '0'     COMMENT '该类型中的id',
  `docker_id`   int(11)          NOT NULL DEFAULT '0'     COMMENT '题目对应docker的id',
  `score`       int(11)          NOT NULL DEFAULT '0'     COMMENT '静态分值',
  `title`       varchar(100)     NOT NULL DEFAULT ''      COMMENT '题目名称',
  `content`     text             NOT NULL                 COMMENT '题目描述',
  `flag`        varchar(100)     NOT NULL DEFAULT ''      COMMENT 'flag内容',
  `seed`        char(5)          NOT NULL DEFAULT 'ILWYE' COMMENT '随机种子',
  `is_rand`     tinyint(1)       NOT NULL DEFAULT '0'     COMMENT '是否随机',
  `is_hide`     tinyint(1)       NOT NULL DEFAULT '1'     COMMENT '是否隐藏',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='CTF题表'") or die('SQL error3');

$link->query("DROP TABLE IF EXISTS `ctf_submits`") or die('SQL error');
$link->query("CREATE TABLE IF NOT EXISTS `ctf_submits` (
  `id`       int(11)          NOT NULL AUTO_INCREMENT COMMENT '提交id',
  `user_id`  int(11)          NOT NULL DEFAULT '0'    COMMENT '用户id',
  `ques_id`  int(11)          NOT NULL DEFAULT '0'    COMMENT '题目id',
  `sub_time` int(11) UNSIGNED NOT NULL DEFAULT '0'    COMMENT '提交时间',
  `sub_ip`   int(11) UNSIGNED NOT NULL DEFAULT '0'    COMMENT '提交ip',
  `sub_flag` varchar(100)     NOT NULL DEFAULT ''     COMMENT '提交flag',
  `is_pass`  tinyint(1)       NOT NULL DEFAULT '0'    COMMENT '是否通过',
  `is_hide`  tinyint(1)       NOT NULL DEFAULT '0'    COMMENT '是否隐藏',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='CTF提交表'") or die('SQL error4');

$link->query("DROP TABLE IF EXISTS `notices`") or die('SQL error');
$link->query("CREATE TABLE IF NOT EXISTS `notices` (
  `id`          int(11)          NOT NULL AUTO_INCREMENT COMMENT '公告id',
  `create_time` int(11) UNSIGNED NOT NULL DEFAULT '0'    COMMENT '创建时间',
  `create_user` int(11)          NOT NULL DEFAULT '0'    COMMENT '创建用户id',
  `edit_time`   int(11) UNSIGNED NOT NULL DEFAULT '0'    COMMENT '编辑时间',
  `edit_user`   int(11)          NOT NULL DEFAULT '0'    COMMENT '编辑用户',
  `content`     text             NOT NULL                COMMENT '公告内容',
  `is_hide`     tinyint(1)       NOT NULL DEFAULT '1'    COMMENT '是否隐藏',
  `is_delete`   tinyint(1)       NOT NULL DEFAULT '0'    COMMENT '是否删除',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='公告表'") or die('SQL error5');

$link->query("DROP TABLE IF EXISTS `docker_use_lists`") or die('SQL error');
$link->query("CREATE TABLE IF NOT EXISTS `docker_use_lists` (
  `id`          int(11)      	 NOT NULL AUTO_INCREMENT COMMENT '下发id',
  `user_id`     int(11)      	 NOT NULL DEFAULT '0'    COMMENT '用户id',
  `ques_id`     int(11)      	 NOT NULL DEFAULT '0'    COMMENT '题目id',
  `docker_id`   int(11)      	 NOT NULL DEFAULT '0'    COMMENT '容器id',
  `ret_url`     varchar(100) 	 NOT NULL DEFAULT '#'    COMMENT '返回网址',
  `create_time` int(11) UNSIGNED NOT NULL DEFAULT '0'    COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='docker下发记录'") or die('SQL error6');


$link->query("DROP TABLE IF EXISTS `users_action`") or die('SQL error');
$link->query("CREATE TABLE IF NOT EXISTS `users_action`(
  `id`      int(11)          NOT NULL AUTO_INCREMENT COMMENT '自增主键',
  `user_id` int(11)          NOT NULL DEFAULT '0'    COMMENT '用户id',
  `ip`      int(11) UNSIGNED NOT NULL DEFAULT '0'    COMMENT '操作ip',
  `time`    int(11) UNSIGNED NOT NULL DEFAULT '0'    COMMENT '操作时间',
  `states`  int(11)          NOT NULL DEFAULT '0'    COMMENT '状态',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户行为记录'") or die('SQL error7');

$link->query("DROP TABLE IF EXISTS `configs`") or die('SQL error');
$link->query("CREATE TABLE IF NOT EXISTS `configs`(
  `id`    int(11)     NOT NULL AUTO_INCREMENT COMMENT '自增主键',
  `name`  varchar(50) NOT NULL DEFAULT ''     COMMENT '配置名称',
  `value` varchar(100)  NOT NULL DEFAULT ''     COMMENT '配置值',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8 COMMENT '配置信息'") or die('SQL error8');


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
(14,'random_flag_head_fmt','flag')") or die('SQL error');

$link->query("INSERT INTO `users_info` (`name`,`password`,`email`,`key`,`reg_time`,`reg_ip`,`big_img`,`tiny_img`,`is_hide`,`is_admin`) 
VALUES('$username','$password','$email','$key','$time','$ip','','','1','1')") or die('SQL error');

echo "写入初始数据成功！";

	// 写入 config.php 文件
	if(!file_put_contents('config.php',$content)){
		highlight_string($content);
		die("# 请确认当前目录下是否有文件写权限？或手动将本页面数据保存为 config.php");
	}
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
		<nav class="blue" role="navigation">
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