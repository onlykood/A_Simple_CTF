 <?php

if(file_exists("./install.lock")){
	die("file all existed!");
}

?>

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
	<main>
		<div class="no-pad-bot section" id="index-banner">
			<div class="container">
				<h3 class="header center blue-text text-darken-2">Mysql 配置</h3>
			</div>
		</div>
		<div class="container" style="font-size: 20px">
			<div class="section">
				<form class="row">
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
				</form>
			</div>
		</div>
		<div class="no-pad-bot section" id="index-banner">
			<div class="container">
				<h3 class="header center blue-text text-darken-2">管理员配置</h3>
			</div>
		</div>
		<div class="container" style="font-size: 20px">
			<div class="section">
				<form class="row">
					<div class="input-field col s8 m6 offset-s2 offset-m3">
						<input placeholder="admin" value="admin" name="USER_NAME" id="USER_NAME" type="text" class="validate" required/>
						<label for="USER_NAME">用户名</label>
					</div>
					<div class="input-field col s8 m6 offset-s2 offset-m3">
						<input placeholder="留空将随机生成10位的密码" value="" name="USER_PASSWD" id="USER_PASSWD" type="text" class="validate" required/>
						<label for="USER_PASSWD">密码</label>
					</div>
					<div class="input-field col s8 m6 offset-s2 offset-m3">
						<input placeholder="请填写一个您的常用邮箱" value="" name="USER_MAIL" id="USER_MAIL" type="email" class="validate" required/>
						<label for="USER_MAIL" data-error="wrong" data-success="right">邮箱</label>
					</div>
					<div class="col s12 center">
						<button class="btn waves-effect waves-light" type="submit" name="action">提交<i class="material-icons right">send</i></button>
					</div>
				</form>
			</div>
		</div>
	</main>
<script>
</script>
</body>
</html>