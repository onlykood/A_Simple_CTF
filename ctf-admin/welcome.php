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
    </head>
    <body>
        <div class="x-body">
            <fieldset class="layui-elem-field">
              <legend>信息统计</legend>
              <div class="layui-field-box">
            <table class="layui-table">
                <thead>
                    <tr>
                        <th colspan="2" scope="col">服务器信息</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <th width="30%">服务器环境</th>
                        <td><span id="lbServerName"><?php echo $_SERVER["SERVER_SOFTWARE"]?></span></td>
                    </tr>
                    <tr>
                        <td>服务器IP地址</td>
                        <td><?php echo $_SERVER['SERVER_ADDR'];?></td>
                    </tr>
                    <tr>
                        <td>服务器域名</td>
                        <td><?php echo gethostbyname($_SERVER['SERVER_NAME']).' - '.$_SERVER["HTTP_HOST"];?></td>
                    </tr>
                    <tr>
                        <td>服务器端口 </td>
                        <td><?php echo $_SERVER["SERVER_PORT"];?></td>
                    </tr>
                    <tr>
                        <td>本文件所在文件夹 </td>
                        <td><?php echo getcwd();?></td>
                    </tr>
                    <tr>
                        <td>服务器操作系统 </td>
                        <td><?php echo php_uname();?></td>
                    </tr>
                    <tr>
                        <td>服务器时间</td>
                        <td><?php echo date("Y年n月j日 H:i:s");?>
                    </tr>
                    
                    <tr>
                        <td>北京时间</td>
                        <td><?php echo gmdate("Y年n月j日 H:i:s",time()+8*3600);?>
                    </tr>
                    <tr>
                        <td>剩余空间</td>
                        <td><?php echo round((disk_free_space(".")/(1024*1024)),2).'M';?>
                    </tr>
                    <tr>
                        <td>PHP运行方式</td>
                        <td><?php echo php_sapi_name();function aa(){return "NULL";}?></td>
                    </tr>
                    <tr>
                        <td>PHP版本</td>
                        <td><?php echo PHP_VERSION;?></td>
                    </tr>

                    <tr>
                        <td>服务器解译引擎</td>
                        <td><?php echo $_SERVER['SERVER_SOFTWARE'];?></td>
                    </tr>
                    <tr>
                        <td>服务器的语言种类 </td>
                        <td><?php echo $_SERVER['HTTP_ACCEPT_LANGUAGE'];?></td>
                    </tr>

                    <tr>
                        <td>服务器上次启动到现在已运行 </td>
                        <td><?php echo explode(",", exec('uptime'))[0];?></td>
                    </tr>
                    <tr>
                        <td>当前程序占用内存 </td>
                        <td><?php echo round(memory_get_usage()/1024/1024, 2).'M';?></td>
                    </tr>
                    </tr>
                    <tr>
                        <td>当前Session </td>
            <td><?php print_r($_SESSION);?></td>
                    </tr>
                    <tr>
                        <td>当前系统用户名 </td>
                        <td><?php system('whoami');?></td>
                    </tr>
                </tbody>
            </table>
              </div>
            </fieldset>
        </div>

    </body>
</html>
