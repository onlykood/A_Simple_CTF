<?php
    require_once("init.php");
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		<title>题目信息列表</title>
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
				<button class="layui-btn layui-btn-danger" onclick="allClicked(this,'is_hide')" name="批量隐藏/显示">批量隐藏/显示</button>
				<button class="layui-btn layui-btn-danger" onclick="allClicked(this,'is_delete')" name="批量删除">批量删除</button>
				<button class="layui-btn" onclick="x_admin_show('添加公告','./noticeManage.php?operate=add',600,500)">添加公告</button>
			</xblock>
	  		<table class="layui-table">
				<thead>
				  <tr>
					<th>
					  <div class="layui-unselect header layui-form-checkbox" lay-skin="primary"><i class="layui-icon">&#xe605;</i></div>
					</th>
					<th>ID</th>
					<th>发布时间</th>
					<th>内容</th>
					<th>状态</th>
					<th>操作</th></tr>
				</thead>
				<tbody id="content-list">
				</tbody>
			</table>
		</div>
	</body>
</html>
<script>
var type='notices';

	/*获取用户基础信息*/
	function getNoticeList(){
		$.ajax({
			url: './ajax.php?m=getInfoList',
			type: 'POST',
			data:{'type':type},
			dataType: 'json',
			success:function(data){
				//console.log(data);
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
						$( '<td>' ).text( num+1 ).appendTo( trow );
						$( '<td>' ).text( new Date(content.edit_time*1000).toLocaleDateString() ).appendTo( trow );
						$( '<td>' ).text( content.content ).appendTo( trow );
						if(content.is_hide=='1'){
							$('<td class="td-status" style="color:red">').text('隐藏').appendTo( trow );
						}
						else{
							$('<td class="td-status" style="color:green">').text('显示').appendTo( trow );
						}
						$('<td class="td-manage">').html('<a title="编辑"  onclick="x_admin_show(\'编辑\',\'noticeManage.php?operate=edit&id='+content.id+'\',600,400)" href="javascript:;"><i class="layui-icon">&#xe642;</i></a>').appendTo(trow);
						trow.appendTo( tbody );
					}
				);
				$( '#content-list' ).html( tbody.html() );
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
		getNoticeList();  
	});
</script>