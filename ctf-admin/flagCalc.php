<?php
    require_once("init.php");
    include 'functions.php';
    if(!isset($_GET['id'])){
      die("~");
    }
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
        <table class="layui-table">
        <thead>
          <tr>
          <th>ID</th>
          <th>用户名</th>
          <th>邮箱</th>
          <th>随机flag</th>
        </tr>
        </thead>
        <tbody id="user-list">
<?php
  $quesid=intval($_GET['id']);
  $link=Database::getConnection();
  $sql=$link->query("SELECT users.id,name,email,user_key,rand_seed from users,questions where questions.id='$quesid'");
  if(!$sql){
    returnInfo(SQL_ERROR);
  }
  while($row=$sql->fetch_assoc()){
    echo '<tr><td>'.$row['id'].'</td><td>'.$row['name'].'</td><td>'.$row['email'].'</td><td>wpsec{'.md5($row['user_key'].$row['rand_seed']).'}</td></tr>';
  }
?>
        </tbody>
      </table>

    </div>
    
  </body>

</html>