<?php 

#开启session
session_start();
if(!isset($_SESSION['admin']) and !$_SESSION['admin']){
    include '../404.html';
    die();
}
include '../config.php';


if(DEBUG){
    // Debug 模式，获取响应开始时间
    $startTime = microtime(true);
    ini_set( 'display_errors', '1' );
}
else{
    //关闭所有错误报告
    error_reporting(0);
}

#数据库链接类
class Database
{
    private static $database;
    public static function getConnection()
    {
        if ( !self::$database )
        {
            self::$database = new mysqli( SQL_CONFIG['DB_HOST'], SQL_CONFIG['DB_USER'], SQL_CONFIG['DB_PASS'], SQL_CONFIG['DB_NAME'] );
            self::$database->set_charset("utf8");
        }
        return self::$database;
    }
}


#指定允许其他域名访问  
#header('Access-Control-Allow-Origin:*');  

#设置html编码格式
header("Content-type: text/html; charset=utf-8"); 

#设置php时区
date_default_timezone_set('Asia/Shanghai');

#加载令牌SESSION
if( !isset( $_SESSION['token'] ) )
{
    $_SESSION['token'] = hash( 'ripemd160', sha1( uniqid( '', true ) ) );
}
if( !isset( $_SESSION['userID'] ) )
{
    $_SESSION['userID'] = false;
}
define( 'INITIALIZED', true );
?>
