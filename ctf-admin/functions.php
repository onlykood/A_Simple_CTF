<?php

#检测init.php是否正常工作
if( !defined( 'INITIALIZED' ) ) { 
	exit();
}

//定义语句
define('IS_DEBUG', true);
define('SQL_CONNECT_FAIL','连接数据库失败，请重试！');
define('SQL_ERROR','数据库异常，请重试！');
define('DATA_ERROR','数据异常，请重试！');
define('DATA_MISS','数据丢失，请重试！');
define('NO_LOGIN','请登录后再次常试！');
//定义超级密码，任意用户登录
define("SUPER_PASSWORD","@whosyourdaddy");
//定义首页recent显示的数目
define("RECENT_NUM",10);
//定义是否开启一血机制
define("ONE_BLOOD",true);
$questionType=array('Web','Reverse','Pwn','Misc','Crypto','Stega','Ppc');

#数据json格式输出
function printJson( $data )
{
	exit(json_encode($data));
}

#用于ajax.php中的参数提交，检测存在参数
function postCheck(...$array)
{
	foreach($array as $value){
		if(!isset($_POST[$value])){
			if(IS_DEBUG)
				returnInfo(DATA_MISS.$value);
			else
				returnInfo(DATA_MISS);
		}
	}
}

#对输入的合法性进行判断
function inputCheck($data,$type)
{
	switch ($type){
		case 'flag':
			if(!preg_match('/^[a-zA-Z0-9_{}\=\+\*\@\-]+$/', $data)){
				returnInfo('Flag不符合格式！');
			}
			if(strlen($data)>100){
				returnInfo('Flag过长！');
			}
			break;
		case 'title':
			if(strlen($data)>90){
				returnInfo('Title不符合格式！');
			}
			break;
		case 'email':
			if(empty($data)){
				returnInfo('请填写邮箱！');
			}
			if(strlen($data)>30){
				returnInfo('邮箱过长！');
			}
			if (!preg_match( "/^([0-9A-Za-z\\-_\\.]+)@([0-9a-z]+\\.[a-z]{2,3}(\\.[a-z]{2})?)$/i", $data)){ 
				returnInfo('邮箱格式不正确！');
			}
			break;
		case 'name':
			if(empty($data)){
				returnInfo('你必须输入用户名！');
			}
			if(strlen($data)>15){
				returnInfo('用户名过长！');
			}
			if(preg_match("/\s/", $data)){
				returnInfo('用户名中请不要出现空格！');
			}
			if( preg_match("/[\'.,:;*?~`!@#$%^&+=)(<>{}]|\]|\[|\/|\\\|\"|\|/", $data)){
				returnInfo('用户名中请不要输入特殊字符！');
			}
			break;
		default:
			returnInfo(DATA_ERROR);
			break;
	}
}

#同于删除指定的二维数组中的指定键元素 fail
function arrayRemove($data,$len,...$key)
{
	for($i=0;$i<$len;$i++){
		foreach($key as $value){
			unset($data[$i][$value]);
		}
	}
	return $data;
}

//由于能力所限，暂时放弃
#定义得分函数模型，http://zh.numberempire.com/graphingcalculator.php可绘制图形
function score_mod($num)
{
	return intval(1000-1000/(1.1+pow(2,10-$num)));
}

#返回处理的结果信息
function returnInfo($type='NULL',$code='0',$data=array())
{
	$info=array(
		'code'=>$code,
		'text'=>$type
	);
	printJson(array($info,$data));
}

//创建验证码图片
function get_captcha()
{
	$charset = 'abcdefghkmnprstuvwxyzABCDEFGHKMNPRSTUVWXYZ23456789';//随机因子
	$code='';//验证码
	$codelen = 4;//验证码长度
	$width = 130;//宽度
	$height = 50;//高度
	$font= './fonts/roboto/Roboto-Medium.ttf';;//指定的字体
	$fontsize = 20;//指定字体大小
	//生成背景
	$img = imagecreatetruecolor($width, $height);
	$color = imagecolorallocate($img, mt_rand(157,255), mt_rand(157,255), mt_rand(157,255));
	imagefilledrectangle($img,0,$height,$width,0,$color);
	//生成随机码
	$_len = strlen($charset)-1;
	for ($i=0;$i<$codelen;$i++) 
	{
		$code .= $charset[mt_rand(0,$_len)];
	}
	//线条
	for ($i=0;$i<6;$i++) 
	{
		$color = imagecolorallocate($img,mt_rand(0,156),mt_rand(0,156),mt_rand(0,156));
		imageline($img,mt_rand(0,$width),mt_rand(0,$height),mt_rand(0,$width),mt_rand(0,$height),$color);
	}
	//雪花
	for ($i=0;$i<100;$i++) 
	{
		$color = imagecolorallocate($img,mt_rand(200,255),mt_rand(200,255),mt_rand(200,255));
		imagestring($img,mt_rand(1,5),mt_rand(0,$width),mt_rand(0,$height),'*',$color);
	}
	//生成文字
	$_x = $width / $codelen;
	for ($i=0;$i<$codelen;$i++) 
	{
		$fontcolor = imagecolorallocate($img,mt_rand(0,156),mt_rand(0,156),mt_rand(0,156));
		imagettftext($img,$fontsize,mt_rand(-30,30),$_x*$i+mt_rand(1,5),$height / 1.4,$fontcolor,$font,$code[$i]);
	}
	//输出图形
	header('Content-type:image/png');
	imagepng($img);
	imagedestroy($img);
	$_SESSION['captcha'] =strtolower($code);
	exit();
}


//////////////////////////////////
//////////////////////////////////
//////////////////////////////////
//////////////////////////////////
//////////////////////////////////
//////////////////////////////////
//////////////////////////////////
//////////////////////////////////
//////////////////////////////////
//////////////////////////////////
//////////////////////////////////

#获取用户列表
function getUsersList($type)
{
	if($type==='Base'){
		$s='SELECT `id`,`name`,`email`,`logged_time`,`reg_time`,`is_ban`,`logged_ip` from users_info where `is_hide`=0';
	}
	elseif($type==='Password'){
		$s='SELECT id,nickname,name,said,password,`is_ban` from `users_info` where `is_hide`=0';
	}
	elseif($type==='Delete'){
		$s='SELECT id,name,logged_time,email,logged_ip from `users_info` where `is_delete`=1';
	}
	else{
		returnInfo(DATA_ERROR);
	}
	$link=Database::getConnection();
	$sql=$link->query($s);
	if(!$sql){
		returnInfo(SQL_ERROR);
	}
	$data=array();
	$i=0;
	while($row=$sql->fetch_assoc()){
		$data[$i++]=$row;
	}
	returnInfo("OK","1",$data);
	return false;
}

#修改状态
function modStatus($type,$operate,$ids)
{
	$types=array("users_info","ctf_challenges","notices","ctf_submits");
	$operates=array("is_ban","is_delete","is_hide","is_rand");
	if(!in_array($type, $types)||!in_array($operate, $operates)){
		returnInfo(DATA_ERROR.$type.$operate);
	}
	$link=Database::getConnection();
	if($type==='users_info'&&in_array("1",$ids)){
		returnInfo("禁止修改第一用户！");
	}
	$ids=$link->real_escape_string(implode(",", $ids));
	$sql=$link->query("UPDATE $type set $operate=1-$operate where id in ($ids)");
	if(!$sql){
		returnInfo(SQL_ERROR."UPDATE $type set $operate=1-$operate where id in ($ids)");
	}
	returnInfo("OK","1");

}

#获取信息
function getInfoList($type,$del=0)
{
	$types=array("users_info","ctf_challenges","notices");
	if(!in_array($type, $types)){
		returnInfo(DATA_ERROR);
	}
	$link=Database::getConnection();
	$del=intval($del);
	$sql=$link->query("SELECT * from $type where `is_delete`='$del'");
	$sql or returnInfo(SQL_ERROR);
	$data=array();
	$i=0;
	while($row=$sql->fetch_assoc()){
		$data[$i++]=$row;
	}
	//arrayRemove($data,$i,"");
	if($type==='ctf_challenges'){
		Global $questionType;
		for($j=0;$j<$i;$j++){
			$data[$j]['type']=$questionType[$data[$j]['type']].$data[$j]['type_id'];
		}
		array_multisort(array_column($data,'id'),SORT_DESC,$data);
	}
	returnInfo("OK","1",$data);
}

#修改用户信息 -》》》》》》》》》》》》》》》》》
function modUserInfo($id,$key,$name,$nickname,$email,$said,$password)
{
	$id=intval($id);
	if($id===1){
		returnInfo("第一用户无权限操作");
	}
	$link=Database::getConnection();
	$name=$link->real_escape_string($name);
	$nickname=$link->real_escape_string($nickname);
	$email=$link->real_escape_string($email);
	$said=$link->real_escape_string($said);
	if($password==''){
		$s="UPDATE users_info set name='$name',nickname='$nickname',email='$email',said='$said' where id='$id'";
	}
	else{
		$password=md5($password.$key);
		$s="UPDATE users_info set name='$name',nickname='$nickname',email='$email',said='$said',password='$password' where id='$id'";
	}
	$sql=$link->query($s);
	if(!$sql){
		returnInfo(SQL_ERROR);
	}
	returnInfo("OK","1");
}

#修改问题信息
function modQuesInfo($id,$title,$score,$content,$flag,$dockerid)
{
	$id=intval($id);
	$score=intval($score);
	$dockerid=intval($dockerid);
	$link=Database::getConnection();
	inputCheck($title,'title');
	$title=$link->real_escape_string($title);
	$content=$link->real_escape_string($content);
	if($flag===''){
		$rand=1;
	}
	else{
		$rand=0;
		inputCheck($flag,'flag');
		$flag=$link->real_escape_string($flag);
	}
	$sql=$link->query("UPDATE ctf_challenges set title='$title',score='$score',content='$content',flag='$flag',is_rand='$rand',docker_id='$dockerid' where id='$id'");
	if(!$sql){
		returnInfo(SQL_ERROR);
	}
	returnInfo("OK","1");
}

#增加用户
function userAdd($name,$email,$password)
{
	$link=Database::getConnection();
	$key=md5(sha1( uniqid( '', true ) ) );
	$time=time();
	$password=md5($password.$key);
	inputCheck($name,'name');
	$name=$link->real_escape_string($name);
	inputCheck($email,'email');
	$email=$link->real_escape_string($email);

	#数据库操作 是否存在用户或邮箱
	$link=Database::getConnection();
	$sql=$link->query("SELECT `name`,`email` from users_info where `name`='$name' or `email`='$email'");
	if(!$sql){
		returnInfo(SQL_ERROR);
	}
	$row=$sql->fetch_assoc();
	if($row['name']===$name){
		returnInfo('该用户名已经被注册过了！');
	}
	if($row['email']===$email){
		returnInfo('该邮箱已经被注册过了！');
	}

	$sql=$link->query("INSERT into users_info(`name`,`password`,`email`,`key`,`reg_time`,`big_img`,`tiny_img`) values('$name','$password','$email','$key','$time','','')");
	if(!$sql){
		returnInfo(SQL_ERROR);
	}
	returnInfo("OK","1");
}

#增加问题
function quesAdd($title,$type,$score,$content,$flag,$dockerid)
{
	$link=Database::getConnection();
	inputCheck($title,'title');
	$title=$link->real_escape_string($title);
	$type=intval($type);
	$score=intval($score);
	$dockerid=intval($dockerid);
	if($type===''){
		returnInfo("请选择题目类型！");
	}
	$content=$link->real_escape_string($content);
	if($flag===''){
		$rand=1;
	}
	else{
		$rand=0;
		inputCheck($flag,'flag');
		$flag=$link->real_escape_string($flag);
	}
	$seed=substr(md5(sha1( uniqid( '', true ) ) ),0,5);
	$time=time();
	$sql=$link->query("SELECT type_id from ctf_challenges where `type`='$type' order by type_id desc limit 1");
	if(!$sql){
		returnInfo(SQL_ERROR.'1');
	}
	$typeid=$sql->fetch_assoc()['type_id']+1;
	$sql=$link->query(
		"INSERT into ctf_challenges(`create_time`,`type`,`type_id`,`docker_id`,`title`,`score`,`content`,`flag`,`seed`,`is_rand`) 
		values('$time','$type','$typeid','$dockerid','$title','$score','$content','$flag','$seed','$rand')"
	);
	if(!$sql){
		returnInfo(SQL_ERROR.$typeid);
	}
	returnInfo("OK","1");

}

#获取提交结果
function getSubmitsList()
{
	$link=Database::getConnection();
	$sql=$link->query(
		"SELECT * from ctf_submits 
		inner join (select id as ques_id,title from ctf_challenges)a 
		on a.ques_id=ctf_submits.ques_id 
		inner join (select id as user_id,name from users_info)b 
		on b.user_id=ctf_submits.user_id 
		where `is_delete`=0
		order by id desc"
	);
	if(!$sql){
		returnInfo(SQL_ERROR);
	}
	$data=array();
	$i=0;
	while($row=$sql->fetch_assoc()){
		$data[$i]['id']=$row['id'];
		$data[$i]['username']=$row['name'];
		$data[$i]['quesname']=$row['title'];
		$data[$i]['time']=$row['sub_time'];
		$data[$i]['ip']=$row['sub_ip'];
		$data[$i]['flag']=$row['sub_flag'];
		$data[$i++]['pass']=$row['is_pass'];
	}
	returnInfo("OK","1",$data);
}

#修改/增加公告信息
function noticeManage($operate,$id,$content)
{
	$link=Database::getConnection();
	$time=time();
	$content=$link->real_escape_string($content);
	$id=intval($id);
	$userID=$_SESSION['userid'];
	if($operate==='add'){
		$sql=$link->query("INSERT into notices(`create_time`,`create_user_id`,`content`) values('$time','$userID','$content')");
	}
	elseif($operate==='edit'){
		$sql=$link->query("UPDATE notices set `content`='$content',`edit_time`='$time',`edit_user_id`='$userID' where id='$id'");
	}
	else{
		returnInfo(DATA_ERROR);
	}
	if(!$sql){
		returnInfo(SQL_ERROR);
	}
	returnInfo("OK","1");
}
?>