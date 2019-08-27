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
                <button class="layui-btn layui-btn-danger" onclick="allClicked(this,'is_delete')" name="批量删除">批量删除</button>
            </xblock>
              <table class="layui-table">
                <thead>
                  <tr>
                    <th>
                      <div class="layui-unselect header layui-form-checkbox" lay-skin="primary"><i class="layui-icon">&#xe605;</i></div>
                    </th>
                    <th>ID</th>
                    <th>用户名</th>
                    <th>问题</th>
                    <th>提交时间</th>
                    <th>提交ip</th>
                    <th>flag</th>
                    <th>状态</th></tr>
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
var type="ctf_submits";
    /*获取用户基础信息*/
    function getSubmitsList(){
        $.ajax({
            url: './ajax.php?m=getSubmitsList',
            type: 'POST',
            dataType: 'json',
            //data:{"type":"Base"},
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
                        $('<td>').text(content.id).appendTo(trow);
                        $( '<td>' ).text( content.username ).appendTo( trow );
                        $( '<td>' ).text( content.quesname ).appendTo( trow );
                        $( '<td>' ).text( new Date(content.time*1000).toLocaleDateString() ).appendTo( trow );
                        $( '<td>' ).text( int2ip(content.ip) ).appendTo( trow );
                        $( '<td>' ).text( content.flag ).appendTo( trow );
                        if(content.pass=='0'){
                            $('<td class="td-status" style="color:red">').text('错误').appendTo( trow );
                        }
                        else{
                            $('<td class="td-status" style="color:green">').text('正确').appendTo( trow );
                        }
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
        getSubmitsList();  
    });
</script>