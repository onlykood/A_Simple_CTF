<?php
    require_once("init.php");
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		<title>用户基础信息管理</title>
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
		<div class="x-nav">
			<span class="" style="line-height:40px">共有数据：<font id="listNum">0</font> 条</span>
			<a class="layui-btn layui-btn-small" style="line-height:1.6em;margin-top:3px;float:right" href="javascript:location.replace(location.href);" title="刷新"><i class="layui-icon" style="line-height:30px">ဂ</i></a>
		</div>
		<div class="x-body">
			<xblock>
				<button class="layui-btn layui-btn-danger" onclick="allClicked(this,'is_ban')" name="批量禁止/放行">批量禁止/放行</button>
				<button class="layui-btn layui-btn-danger" onclick="allClicked(this,'is_delete')" name="批量删除">批量删除</button>
				<button class="layui-btn" onclick="x_admin_show('添加用户','./userAdd.php',600,400)"><i class="layui-icon"></i>添加</button>
			</xblock>
	  		<table class="layui-table">
				<thead>
				  <tr>
					<th>
					  <div class="layui-unselect header layui-form-checkbox" lay-skin="primary"><i class="layui-icon">&#xe605;</i></div>
					</th>
					<th>ID</th>
					<th>用户名</th>
					<th>邮箱</th>
					<th>注册时间</th>
					<th>最后登陆时间</th>
					<th>最后登陆ip</th>
					<th>状态</th>
					<th>操作</th></tr>
				</thead>
				<tbody id="user-list">
				</tbody>
			</table>
	  		<!-- 暂时不考虑分页 -->
			<!--
			<div class="page">
				<div>
					<a class="prev" href="">&lt;&lt;</a>
					<span class="current" href="">1</span>
					<a class="num">2</a>
					<a class="num" href="">3</a>
					<a class="num" href="">489</a>
					<a class="next" href="">&gt;&gt;</a>
				</div>
			</div>
			-->
		</div>
	</body>
</html>
<script>
var type="users_info";

	/*获取用户基础信息*/
	function getBaseList(){
		$.ajax({
			url: './ajax.php?m=getInfoList',
			type: 'POST',
			dataType: 'json',
			data:{'type':type},
			success:function(data){
				console.log(data);
				if(errorCheck(data)){
					return false;
				}
				$('#listNum').text(data[1].length);
				var tbody = $( '<tbody>' );
				$.each(
					data[1],
					function(num,content){
						var trow = $( '<tr>' );
						$( '<td>' ).html('<div class="layui-unselect layui-form-checkbox" lay-skin="primary" data-id="'+content.id+'"><i class="layui-icon">&#xe605;</i></div>').appendTo( trow );
						$( '<td>' ).text( content.id ).appendTo( trow );
						$( '<td>' ).text( content.name ).appendTo( trow );
						$( '<td>' ).text( content.email ).appendTo( trow );
						$( '<td>' ).text( new Date(content.reg_time*1000).toLocaleDateString() ).appendTo( trow );
						$( '<td>' ).text( new Date(content.logged_time*1000).toLocaleDateString() ).appendTo( trow );
						$( '<td>' ).text( int2ip(content.logged_ip) ).appendTo( trow );
						if(content.is_ban=='0'){
							$('<td class="td-status" style="color:#2196F3">').text('正常').appendTo( trow );
						}
						else{
							$('<td class="td-status" style="color:red">').text('禁用').appendTo( trow );
						}
						$('<td class="td-manage">').html('<a title="编辑"  onclick="x_admin_show(\'编辑\',\'userEdit.php?id='+content.id+'\',600,400)" href="javascript:;"><i class="layui-icon">&#xe642;</i></a>').appendTo(trow);
						trow.appendTo( tbody );
					}
				);
				$( '#user-list' ).html( tbody.html() );
				//ajax动态更新的数据必须使用这个更新，不是form的那个，真TM的坑啊
				tableCheck.init();
			},
			error:function(data){
				console.log(data);
			}
		});  
		return false;
	}

	$(document).ready(function() {
		getBaseList();  
	});
</script>