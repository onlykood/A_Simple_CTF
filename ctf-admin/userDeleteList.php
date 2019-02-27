<?php
    require_once("init.php");
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
    <div class="x-nav">
      <span class="" style="line-height:40px">共有数据：<font id="listNum">0</font> 条</span>
      <a class="layui-btn layui-btn-small" style="line-height:1.6em;margin-top:3px;float:right" href="javascript:location.replace(location.href);" title="刷新"><i class="layui-icon" style="line-height:30px">ဂ</i></a>
    </div>
    <div class="x-body">
      <xblock>
        <button class="layui-btn layui-btn-danger" onclick="allClicked(this,'del')" name="批量恢复">批量恢复</button>
      </xblock>
      <table class="layui-table">
        <thead>
          <tr>
            <th>
              <div class="layui-unselect header layui-form-checkbox" lay-skin="primary"><i class="layui-icon">&#xe605;</i></div>
            </th>
            <th>ID</th>
            <th>用户名</th>
            <th>注册时间</th>
            <th>邮箱</th>
            <th>ip</th></tr>
        </thead>
        <tbody id="user-list">

        </tbody>
      </table>

    </div>
<script>
var type="user";
function getDeleteList(){
  $.ajax({
    url: './ajax.php?m=getUserList',
    type: 'POST',
    dataType: 'json',
    data:{"type":"Delete"},
    success:function(data){
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
          $( '<td>' ).text( new Date(content.last_time*1000).toLocaleDateString() ).appendTo( trow );
          $( '<td>' ).text( content.email ).appendTo( trow );
          $( '<td>' ).text( content.last_ip ).appendTo( trow );
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
  getDeleteList();  
});
    </script>
  </body>

</html>