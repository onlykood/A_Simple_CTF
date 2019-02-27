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
				<button class="layui-btn layui-btn-danger" onclick="delAll()">批量删除</button>
				<button class="layui-btn layui-btn-danger" onclick="banAll()">批量禁用</button>
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
						<th>昵称</th>
						<th>言论</th>
						<th>密码</th>
						<th>状态</th>
						<th>操作</th></tr>
				</thead>
				<tbody id="user-list">
				</tbody>
			</table>
		</div>
	</body>
</html>
<script>
var type="user";
function getPasswordList(){
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
					$('<td>').text(content.nickname).appendTo(trow);
					$( '<td>' ).text( content.said ).appendTo( trow );
					$( '<td>' ).text( content.password ).appendTo( trow );
					//$( '<td>' ).text( content.score ).appendTo( trow );
					if(content.ban=='0'){
						$('<td class="td-status" style="color:#2196F3">').text('正常').appendTo( trow );
					}
					else{
						$('<td class="td-status" style="color:red">').text('禁用').appendTo( trow );
					}
					$('<td class="td-manage">').html('<a title="编辑" onclick="x_admin_show(\'编辑\',\'userEdit.php?id='+content.id+'\',600,400)" href="javascript:;"><i class="layui-icon">&#xe642;</i></a>').appendTo(trow);
					trow.appendTo( tbody );
				}
			);
			$( '#user-list' ).html( tbody.html() );
			tableCheck.init();
		},
		error:function(data){
			console.log(data);
		}
	})
	
}
$(document).ready(function() {
	getPasswordList();
});

layui.use('laydate', function(){
	var laydate = layui.laydate;
	//执行一个laydate实例
	laydate.render({
		elem: '#start' //指定元素
	});

	//执行一个laydate实例
	laydate.render({
		elem: '#end' //指定元素
	});
});

	/*用户-锁定*/
	function userBan(obj,id){
		var said='是否切换该用户禁用状态？';
		layer.confirm(said,function(index){
			$.ajax({
				url: './ajax.php?m=modUsersStatus',
				type: 'POST',
				dataType: 'json',
				data:{"status":'ban',"ids":id},
				success:function(data){
					console.log(data);
					if(errorCheck(data)){
					  return false;
					}
					if($(obj).parents("tr").find(".td-status").find('span').hasClass('layui-btn-danger')){
						$(obj).attr('title','解除禁用');
						$(obj).parents("tr").find(".td-status").find('span').removeClass('layui-btn-danger').html('正常');
					}
					else{
						$(obj).attr('title','禁用');
						$(obj).parents("tr").find(".td-status").find('span').addClass('layui-btn-danger').html('禁用');
					}
					layer.msg('已切换该用户的禁用状态！',{icon: 7,time:1000});
				},
				error:function(data){
					console.log(data);
				}
			});
		});
		return false;
	}

	/*用户-删除*/
	function userDel(obj,id){
		layer.confirm('确认要删除吗？',function(index){
			$.ajax({
				url: 'ajax.php?m=modUsersStatus',
				type: 'POST',
				dataType: 'json',
				data:{"status":"del","ids":id},
				success:function(data){
					if(errorCheck(data)){
						return false;
					}
					$(obj).parents("tr").remove();
					$("#listNum").text($("#listNum").text()-1);
					layer.msg('已删除!',{icon:1,time:1000});
				},
				error:function(data){
					console.log(data);
				}
			});
		});
		return false;
	}
/*禁用选中的所有用户*/
function banAll(argument){
	var data = tableCheck.getData();
	layer.confirm('确认要切换禁用/正常状态吗？'+data,function(index){
		//捉到所有被选中的，发异步进行删除
		$.ajax({
			url: 'ajax.php?m=modUsersStatus',
			type: 'POST',
			dataType: 'json',
			data:{"status":'bans',"ids":data},
			success:function(data){
				if(errorCheck(data)){
					return false;
				}
				layer.msg('切换成功', {icon: 1});
				//$(".layui-form-checked").not('.header').parents('tr').remove();
				$("#listNum").text($("#listNum").text()-data[0].text);
			},
			error:function(data){
				console.log(data);
			}
		}); 
	});
	return false;
}
	/*删除选中的所有用户*/
	function delAll (argument) {
		var data = tableCheck.getData();
		layer.confirm('确认要删除吗？'+data,function(index){
			//捉到所有被选中的，发异步进行删除
			$.ajax({
				url: 'ajax.php?m=modUsersStatus',
				type: 'POST',
				dataType: 'json',
				data:{"status":'dels',"ids":data},
				success:function(data){
					if(errorCheck(data)){
						return false;
					}
					layer.msg('删除成功', {icon: 1});
					$(".layui-form-checked").not('.header').parents('tr').remove();
					$("#listNum").text($("#listNum").text()-data[0].text);
				},
				error:function(data){
					console.log(data);
				}
			}); 
		});
		return false;
	}
</script>