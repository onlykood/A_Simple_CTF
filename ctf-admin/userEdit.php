<?php
include 'init.php';
include 'functions.php';
if(!isset($_GET['id'])){
	die("~");
}
$id=intval($_GET['id']);
$link=Database::getConnection();
$sql=$link->query("SELECT * from users where id='$id'");
if(!$sql){
	returnInfo(SQL_ERROR);
}
$row=$sql->fetch_assoc();
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		<title>欢迎页面-X-admin2.0</title>
		<meta name="renderer" content="webkit">
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
		<link rel="shortcut icon" href="/favicon.ico" type="image/x-icon" />
		<link rel="stylesheet" href="./css/font.css">
		<link rel="stylesheet" href="./css/xadmin.css">
		<script type="text/javascript" src="https://cdn.bootcss.com/jquery/3.2.1/jquery.min.js"></script>
		<script type="text/javascript" src="./lib/layui/layui.js" charset="utf-8"></script>
		<script type="text/javascript" src="./js/xadmin.js"></script>
		<!-- 让IE8/9支持媒体查询，从而兼容栅格 -->
		<!--[if lt IE 9]>
			<script src="https://cdn.staticfile.org/html5shiv/r29/html5.min.js"></script>
			<script src="https://cdn.staticfile.org/respond.js/1.4.2/respond.min.js"></script>
		<![endif]-->
	</head>
	<body>
	<div class="x-body">
		<form class="layui-form">
			<input type="hidden" id="userid" name="userid" value="<?php echo $row['id'];?>">
			<input type="hidden" id="userkey" name="usrekey" value="<?php echo $row['user_key'];?>">
			<div class="layui-form-item">
				<label for="L_username" class="layui-form-label">
					<span class="x-red">*</span>用户名
				</label>
				<div class="layui-input-inline">
					<input type="text" id="L_username" name="username" required="" lay-verify="username" autocomplete="off" class="layui-input" value="<?php echo htmlspecialchars($row['name']);?>">
				</div>
			</div>
			<div class="layui-form-item">
				<label for="L_nickname" class="layui-form-label">
					<span class="x-red">*</span>昵称
				</label>
				<div class="layui-input-inline">
					<input type="text" id="L_nickname" name="nickname" required="" lay-verify="nikename" autocomplete="off" class="layui-input" value="<?php echo htmlspecialchars($row['nickname']);?>">
				</div>
			</div>
			<div class="layui-form-item">
				<label for="L_password" class="layui-form-label">
					<span class="x-red">*</span>密码
				</label>
				<div class="layui-input-inline">
					<input type="text" id="L_password" name="password" required="" lay-verify="password" autocomplete="off" class="layui-input" value="">
				</div>
			</div>
			<div class="layui-form-item">
				<label for="L_email" class="layui-form-label">
					<span class="x-red">*</span>邮箱
				</label>
				<div class="layui-input-inline">
					<input type="text" id="L_email" name="email" required="" lay-verify="email" autocomplete="off" class="layui-input" value="<?php echo htmlspecialchars($row['email']);?>">
				</div>
			</div>
			<div class="layui-form-item">
				<label for="L_said" class="layui-form-label">
					<span class="x-red">*</span>Said
				</label>
				<div class="layui-input-inline">
					<input type="text" id="L_said" name="said" required="" lay-verify="said" autocomplete="off" class="layui-input" value="<?php echo htmlspecialchars($row['said']);?>">
				</div>
			</div>
			<div class="layui-form-item">
				<label for="L_password" class="layui-form-label">
				</label>
				<button	class="layui-btn" lay-filter="mod" lay-submit="">
					修改
				</button>
			</div>
		</form>
	</div>
	<script>
		layui.use(['form','layer'], function(){
			$ = layui.jquery;
		var form = layui.form
		,layer = layui.layer;
		
		//自定义验证规则
		form.verify({
			username:function(value){
				if(value.length<2){
					return '用户名至少2个字符';
				}
			},
			nikename: function(value){
				if(value.length < 2){
					return '昵称至少2个字符';
				}
			},
			password:function(value){
				if(value!=''&&value.length<6){
					return '密码必须大于6字符';
				}
			},
			email:[/^([a-zA-Z0-9]+[_|\_|\.]?)*[a-zA-Z0-9]+@([a-zA-Z0-9]+[_|\_|\.]?)*[a-zA-Z0-9]+\.[a-zA-Z]{2,3}$/,'邮箱格式不正确'],
			//ip: [/^(\d+)\.(\d+)\.(\d+)\.(\d+)$/, 'IP格式不正确']
		});

		//监听提交
		form.on('submit(mod)', function(data){
			//发异步，把数据提交给php
			$.ajax({
			url: './ajax.php?m=modUserInfo',
			type: 'POST',
			dataType: 'json',
			data: {
				"userid":$('#userid').val(),
				"userkey":$('#userkey').val(),
				"name":$('#L_username').val(),
				"nickname":$('#L_nickname').val(),
				"email":$('#L_email').val(),
				"said":$('#L_said').val(),
				"password":$('#L_password').val()
			},
			success:function(data){
				if(data[0].code=='0'){
				layer.alert(data[0].text,{icon:2},function(){
					// 获得frame索引
					var index = parent.layer.getFrameIndex(window.name);
					//关闭当前frame
					parent.layer.close(index);
				});
				return false;
				}
				layer.alert("修改成功", {icon: 6},function () {
					// 获得frame索引
					var index = parent.layer.getFrameIndex(window.name);
					//关闭当前frame
					parent.layer.close(index);
				});
			},	
			error:function(data){
				console.log(data);
			}
			});
			return false;
		});
		
		
		});
	</script>
	</body>

</html>