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
    <meta name="viewport" content="width=device-width,user-scalable=yes, minimum-scale=0.4, initial-scale=0.8" />
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
          <div class="layui-form-item">
              <label for="L_title" class="layui-form-label">
                  <span class="x-red">*</span>标题
              </label>
              <div class="layui-input-inline">
                  <input type="text" id="L_title" name="title" required="" lay-verify="title"
                  autocomplete="off" class="layui-input">
              </div>
          </div>
          <div class="layui-form-item">
              <label for="L_type" class="layui-form-label">
                  <span class="x-red">*</span>类型
              </label>
              <div class="layui-input-inline">
                <select id="L_type" name="type" lay-verify="type">
                  <option value=""></option>
                  <option value="0">Web</option>
                  <option value="1">Reverse</option>
                  <option value="2">Pwn</option>
                  <option value="3">Misc</option>
                  <option value="4">Crypto</option>
                  <option value="5">Stega</option>
                  <option value="6">Ppc</option>
                </select>
              </div>
          </div>
          <div class="layui-form-item">
              <label for="L_dockerid" class="layui-form-label">
                  Dockerid
              </label>
              <div class="layui-input-inline">
                  <input type="text" id="L_dockerid" name="dockerid" lay-verify="dockerid" class="layui-input" value="0">
              </div>
              <div class="layui-form-mid layui-word-aux">
                  <span class="x-red">*</span>无docker id，则不填或填0
              </div>
          </div>
          <div class="layui-form-item">
              <label for="L_grade" class="layui-form-label">
                  <span class="x-red">*</span>难度
              </label>
              <div class="layui-input-inline">
                <select id="L_grade" name="grade" lay-verify="grade" lay-filter="grade">
                  <option value="1">很简单</option>
                  <option value="2">简单</option>
                  <option value="3">一般</option>
                  <option value="4">难</option>
                  <option value="5">很难</option>
                </select>
              </div>
          </div>
          <div class="layui-form-item">
              <label for="L_score" class="layui-form-label">
                  <span class="x-red">*</span>分数
              </label>
              <div class="layui-input-inline">
                  <input type="text" id="L_score" name="score" required="" lay-verify="score" autocomplete="off" class="layui-input" value="100">
              </div>
          </div>
          <div class="layui-form-item">
            <label for="L_content" class="layui-form-label">
                  <span class="x-red">*</span>介绍
              </label>
            <textarea name="content" id="L_content" required lay-verify="required" class="layui-textarea" style="outline:none;width: 60%"></textarea>
          </div>
          <div class="layui-form-item">
              <label for="L_flag" class="layui-form-label">
                  <span class="x-red">*</span>Flag
              </label>
              <div class="layui-input-inline">
                  <input type="text" id="L_flag" name="flag" lay-verify="flag"
                   class="layui-input">
              </div>
              <div class="layui-form-mid layui-word-aux">
                  <span class="x-red">*</span>留空则设置为随机flag
              </div>
          </div>
          <div class="layui-form-item">
              <label for="L_repass" class="layui-form-label">
              </label>
              <button  class="layui-btn" lay-filter="add" lay-submit="">
                  增加
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
            /*username: function(value){
              if(value.length < 5){
                return '昵称至少得5个字符啊';
              }
            }
            ,pass: [/(.+){6,12}$/, '密码必须6到12位']
            ,repass: function(value){
                if($('#L_pass').val()!=$('#L_repass').val()){
                    return '两次密码不一致';
                }
            }*/
          });
          form.on('select(grade)', function(data){
            var score=data.value; //得到被选中的值
            $('#L_score').val(score*100);
          }); 
          //监听提交
          form.on('submit(add)', function(data){
            console.log(data);
            //发异步，把数据提交给php
            $.ajax({
              url: './ajax.php?m=quesAdd',
              type: 'POST',
              dataType: 'json',
              data: {
                'title':$('#L_title').val(),
                'dockerid':$('#L_dockerid').val(),
                'type':$("#L_type").val(),
                'score':$("#L_score").val(),
                'content':$('#L_content').val(),
                'flag':$('#L_flag').val()
              },
              success:function(data){
                console.log(data);
                if(errorCheck(data)){
                  return false;
                }
                layer.alert("增加成功", {icon: 6},function () {
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
