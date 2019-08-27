<?php
    require_once("init.php");
    include 'functions.php';
    isset($_GET['id']) or die("~");
    $quesid=intval($_GET['id']);
    $link=Database::getConnection();
    $sql=$link->query("SELECT a.`id`,a.`name`,a.`email`,a.`key`,b.`seed`,b.`is_rand` from `users_info` as a,`ctf_challenges` as b where b.`id`='$quesid'");
    $sql or returnInfo(SQL_ERROR);
    $tmp='';
    while($row=$sql->fetch_assoc()){
        if(!$row['is_rand']){
            $tmp='<tr><td>非动态flag</td><td>非动态flag</td><td>非动态flag</td><td>非动态flag</td></tr>';
            break;
        }
        $tmp.='<tr><td>'.$row['id'].'</td><td>'.$row['name'].'</td><td>'.$row['email'].'</td><td>flag{'.md5($row['key'].$row['seed']).'}</td></tr>';
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
    </head>
    <body>
        <div class="x-body">
            <table class="layui-table"><thead><tr><th>ID</th><th>用户名</th><th>邮箱</th><th>动态flag</th></tr></thead>
                <tbody id="user-list"><?=$tmp?></tbody>
            </table>
        </div>
    </body>
</html>