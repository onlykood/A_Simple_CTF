<?php
if(!isset($_GET['operate'])){
  die("~");
}
if($_GET['operate']==='add'){
  $operate="增加";
  $row['id']=0;
  $row['content']='';
}
elseif($_GET['operate']==='edit'){
  if(!isset($_GET['operate'])){
    die("~~");
  }
  require_once('init.php');
  $operate="修改";
  $id=intval($_GET['id']);
  $link=Database::getConnection();
  $sql=$link->query("SELECT * from notices where id='$id'");
  if(!$sql){
    returnInfo(SQL_ERROR);
  }
  $row=$sql->fetch_assoc();
}
else{
  die("~~~");
}
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
      <input type="hidden" id="noticeid" name="noticeid" value="<?php echo $row['id'];?>">
      <div class="layui-form-item">
        <label for="L_content" class="layui-form-label">
          <span class="x-red">*</span>内容
        </label>
        <div class="layui-input-inline">
          <textarea name="content" id="L_content" required lay-verify="required" class="layui-textarea" style="outline:none;resize:none;"><?php echo htmlspecialchars($row['content']); ?></textarea>
        </div>
      </div>
      <div class="layui-form-item">
        <label class="layui-form-label">
        </label>
        <button class="layui-btn" lay-filter="mod" lay-submit="">
          <?php echo $operate; ?>
        </button>
      </div>
    </form>
  </div>
  <script>
    layui.use(['form','layer'], function(){
      $ = layui.jquery;
    var form = layui.form,layer = layui.layer;
    
    //监听提交
    form.on('submit(mod)', function(data){
      //发异步，把数据提交给php
      $.ajax({
        url: './ajax.php?m=noticeManage',
        type: 'POST',
        dataType: 'json',
        data: {
          "operate":"<?php echo $_GET['operate'];?>",
          "id":$("#noticeid").val(),
          "content":$('#L_content').val()
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
          layer.alert("<?php echo $operate;?>成功", {icon: 6},function () {
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