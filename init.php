<?php
# 文件检测，是否已安装数据库
if(!file_exists('config.php')){
    die(json_encode(array(array('-1','No Install!'))));
}
include 'config.php';

# 错误代码
define('MY_ERROR',[
    'SQL_CONNECT_FAIL' => '连接数据库失败，请重试！',
    'SQL_ERROR'        => '数据库错误，请重试！',
    'DATA_ERROR'       => '数据异常，请重试！',
    'DATA_MISS'        => '数据丢失，请重试！',
    'NO_LOGIN'         => '请登录后再次常试！',
]);

# 数据库连接类
class Database{
    private static $database;
    public static function getConnection(){
        if ( !self::$database ){
            self::$database = new mysqli( SQL_CONFIG['DB_HOST'], SQL_CONFIG['DB_USER'], SQL_CONFIG['DB_PASS'], SQL_CONFIG['DB_NAME'] );
            self::$database->set_charset("utf8");
        }
        return self::$database;
    }
}

if(DEBUG){
    # Debug 模式，获取响应开始时间
    $startTime = microtime(true);
    ini_set( 'display_errors', '1' );
}
else{
    # 关闭所有错误报告
    error_reporting(0);
}

/**
 * @description 用 json 格式返回信息
 * @Author      kood
 * @DateTime    2019-02-27
 * @param       string     $text      小提示框的显示文字
 * @param       string     $code      返回的状态码
 * @param       array      $data      返回的数据
 * @param       string     $debugInfo 如果开启 debug 模式，附加此项
 * @return      string                json 字符串
 */
function returnInfo(string $text='NULL',int $code=0,$datas=array()){
    $info=array(
        array($code,$text),
        $datas
    );
    if(DEBUG){
        global $link;
        global $startTime;
        $endTime = microtime(true);
        $info[0][]=$endTime-$startTime;
        if($link->connect_errno)
            $info[0][]=$link->connect_error;
        else
            $info[0][]=$link->error;
    }
    exit(json_encode($info));
}

# 检测数据库连接情况
$link=@Database::getConnection();
if ($link->connect_errno){
    returnInfo(MY_ERROR['SQL_CONNECT_FAIL']);
}

/**
 * @description 从配置数据表中读取配置
 * @Author      kood
 * @DateTime    2019-03-20
 * @param       string       $configName 配置名称
 * @param       bool|boolean $retJson    数据是否以json直接返回
 * @return      [type]                   [description]
 */
function getConfig(string $configName, bool $retJson = false)
{
    global $link;
    if(file_exists(CACHEPATH.'configs')){
        $configs=json_decode(file_get_contents(CACHEPATH.'configs'),True);
        $info='ok-cache';
    }
    else{
        $sql = $link->query("SELECT name,value FROM configs");
        $sql or returnInfo(MY_ERROR['SQL_ERROR']);
        $configs=array();
        while($row=$sql->fetch_assoc()){
            $configs[$row['name']]=$row['value'];
        }
        $configs['cache_open'] and file_put_contents(CACHEPATH.'configs', json_encode($configs), LOCK_EX);
        $info='ok';
    }
    array_key_exists($configName,$configs) or returnInfo("No found config");
    if (!$retJson)
        return $configs[$configName];
    returnInfo($info, 1, $configs[$configName]);
}

#指定允许其他域名访问  
#header('Access-Control-Allow-Origin:*');  

# 开启session
session_start();

# 设置html编码格式
# header("Content-type: text/html; charset=utf-8"); 

#设置php时区
date_default_timezone_set('Asia/Shanghai');

# 加载 token
if( !isset($_SESSION['token']) ){
    $_SESSION['token'] = hash( 'ripemd160', sha1( uniqid( '', true ) ) );
}
