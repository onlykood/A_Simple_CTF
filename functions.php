<?php
/** 
* 一个包括所有接口的函数包了吧。。。Orz
* 通常返回的是 json 格式
* @author      kood
* @version     Bete 1.0
*/


/**
 * @description 定义动态积分的得分函数模型
 * https://zh.numberempire.com/graphingcalculator.php 可绘制图形，1001-1000/(1.01+2.5^(12-x))
 * @Author      kood
 * @DateTime    2019-02-27
 * @param       int     $num 解题人数
 * @return      int          题目分值
 */
function scoreModel($number){
	return intval(1001-1000/(1.01+pow(2.2,12-$number)));
}

/**
 * @description 根据题目 id 获取题目的解答数目
 * @Author      kood
 * @DateTime    2019-02-27
 * @param       int     $id 题目 id
 * @return      int         解题数量
 */
function getQuestionSolveNum($id){
	$link=Database::getConnection();
	$sql=$link->query(
		"SELECT count(1) as `num`
		from submits
		where pass='1' 
		and del='0'
		and ques_id='$id'"
	);
	$sql or returnInfo(MY_ERROR['SQL_ERROR'],$debugInfo=$link->error);
	return intval($sql->fetch_assoc()['num']);
}

/**
 * @description 给与额外得分（前几血）
 * @Author      kood
 * @DateTime    2019-02-27
 * @param       int     $num 排名
 * @return      int          得分
 */
function oneBlood($num){
	$score=0;
	if(!MY_SWITCH['ONE_BLOOD']){
		return $score;
	}
	$oneBloodNum=count(MY_CONFIG['FIRST_FEW_BLOOD_SCORE']);
	//如果num在一血加成范围之内，加分，否则score为0
	if($num<$oneBloodNum){
		$score=MY_CONFIG['FIRST_FEW_BLOOD_SCORE'][$num];
	}
	return $score;
}


#判断是否登陆(null)
function loginCheck($ret=false){
	if( intval($_SESSION['userid']) > 0 ){
		return true;
	}
	#如果不要求返回值，那么直接退出
	$ret or returnInfo(MY_ERROR['NO_LOGIN']);
	return false;
}

#用于ajax.php中的参数提交，检测存在参数(null)
function postCheck(...$array){
	foreach($array as $value){
		isset($_POST[$value]) or returnInfo(MY_ERROR['DATA_MISS']);
	}
}

#查询平台当前的一些状态(status)
function statusCheck($type='now_status'){
	$types=array('now_status','reg_open','sub_open','login_open');
	if(!in_array($type,$types,true)){
		return 0;
	}
	$link = Database::getConnection();
	$sql=$link->query("SELECT * from status");
	$sql or returnInfo(MY_ERROR['SQL_ERROR'],$debugInfo=$link->error);
	return $sql->fetch_assoc()[$type];
}

#对输入的合法性进行判断(null)
function inputCheck($type,$data,$data1=false){
	switch ($type){
		case 'flag':
			empty($data) and returnInfo('请填写flag！');
			strlen($data)>50 and returnInfo('Flag过长！');
			preg_match("/--/", $data) and returnInfo('Flag不符合格式！');
			preg_match('/^[a-zA-Z0-9_{}\=\+\*\?\@\-]+$/', $data) or returnInfo('Flag不符合格式！');
			break;
		case 'title':
			(strlen($data)>90) and returnInfo('Title不符合格式！');
			break;
		case 'email':
			empty($data) and returnInfo('请填写邮箱！');
			(strlen($data)>30) and returnInfo('邮箱过长！');
			preg_match( "/^([0-9A-Za-z\\-_\\.]+)@([0-9a-z]+\\.[a-z]{2,3}(\\.[a-z]{2})?)$/i", $data) or returnInfo('邮箱格式不正确！');
			break;
		case 'name':
			empty($data) and returnInfo('你必须输入用户名！');
			(strlen($data)>15) and returnInfo('用户名过长！');
			preg_match("/\s/", $data) and returnInfo('用户名中请不要出现空格！');
			preg_match("/[\'.,:;*?~`!@#$%^&+=)(<>{}]|\]|\[|\/|\\\|\"|\|/", $data) and returnInfo('用户名中请不要输入特殊字符！');
			break;
		case 'password':
			($data!==$data1) and returnInfo('密码输入不一致！');
			(strlen($data)<6) and returnInfo('密码长度过短！');
			break;
		case 'said':
			(strlen($data)>50) and returnInfo('你想说的太长了！');
			
			break;
		case 'nickname':
			(strlen($data)>50) and returnInfo('昵称过长！');
			break;
		case 'id':
			is_numeric($data) or returnInfo(MY_ERROR['DATA_ERROR']);
			break;
		default:
			returnInfo(MY_ERROR['DATA_ERROR']);
			break;
	}
	return true;
}

#检测邮件是否发送(null)
function mailSendCheck(){
	if((isset($_SESSION['reg_name']) && isset($_SESSION['reg_mail']))||(isset($_SESSION['reset_name']) && isset($_SESSION['reset_mail']))){
		returnInfo('NULL',2);
	}
	else{
		returnInfo('NULL',1);
	}
	return false;
}

#获取session信息(loggedin token name nickname mail said)
function getSession(){
	$data=array(
		'loggedin'=>0,
		'token'=>$_SESSION['token']
	);
	loginCheck(true) or returnInfo("OK","1",array_values($data));

	//returnInfo(MY_CONFIG['DATA_ERROR'],'-1');
	$link =Database::getConnection();
	$sql=$link->query(
		"SELECT name,nickname,email,said 
		FROM users 
		WHERE id='".$_SESSION['userid']."'"
	);

	$sql or returnInfo(MY_ERROR['SQL_ERROR']);
	$row=$sql->fetch_assoc();
	$data=array(
		'loggedin'=>1,
		'token'=>$_SESSION['token'],
		'name'=>$row['name'],
		'nickname'=>$row['nickname'],
		'mail'=>$row['email'],
		'said'=>$row['said']
	);
	returnInfo("OK","1",array_values($data));
}

#创建用户(null)
function register( $password, $repeat, $regkey){
	#检查是否存在注册的用户名 和 邮箱
	if(!isset($_SESSION['reg_name']) || !isset($_SESSION['reg_mail'])){
		returnInfo(MY_ERROR['DATA_ERROR']);
	}
	#判断是否存在注册码以及检测注册码的有效时间
	if(!isset($_SESSION['reg_key'])||!isset($_SESSION['reg_time'])||$_SESSION['reg_time']<time()){
		if(isset($_SESSION['reg_mail'])){
			unset($_SESSION['reg_mail']);
		}
		returnInfo('注册码已失效！',2);
	}

	#判断注册码输入是否正确
	if((string)$regkey!==(string)$_SESSION['reg_key']){
		#延时3秒，防止重复攻击
		sleep(3);
		returnInfo('注册码不正确,请确认邮件！');
	}

	#密码检测
	inputCheck('password',$password,$repeat);

	#写入数据库流程
	$link = Database::getConnection();

	#之前已经check过了，不需要重复检测
	$name=$_SESSION['reg_name'];
	$mail=$_SESSION['reg_mail'];
	
	$key=md5(sha1( uniqid( '', true ).mt_rand(1000000000,9999999999) ));
	$time=time();
	$password = md5($password.$key);
	$ip=$_SERVER['REMOTE_ADDR'];
	$sql = $link->query(
		"INSERT INTO users(name,password,email,user_key,reg_time,reg_ip)
		VALUES('$name','$password','$mail','$key','$time','$ip')"
	);
	$sql or returnInfo(MY_ERROR['SQL_ERROR'],$debugInfo=$link->error);

	#消除这三个session
	unset($_SESSION['reg_key']);
	unset($_SESSION['reg_mail']);
	unset($_SESSION['reg_name']);
	returnInfo('注册成功！',1);

	return false;
}

#发送邮邮件的函数(null)
function emailToSend($email,$title,$content) {
	require 'sendmail.php';
	$mail = new PHPMailer;
	$mail->isSMTP();
	$mail->SMTPAuth = true;
	$mail->Host = 'smtp.exmail.qq.com';
	$mail->Username = MY_CONFIG['EMAIL_USERNAME'];
	$mail->Password = MY_CONFIG['EMAIL_PASSWORD'];
	$mail->SMTPSecure = 'ssl';
	$mail->Port = 465;
	$mail->Encoding = "base64";
	$mail->CharSet = 'UTF-8';
	$mail->setFrom(MY_CONFIG['EMAIL_USERNAME'], explode("@", MY_CONFIG['EMAIL_USERNAME'])[0]);
	$mail->addAddress($email);
	$mail->isHTML(true);
	$mail->Subject = $title;
	$mail->Body = $content;
	//取消发送验证，几乎没有错误了，加快返回速度，提升4s左右
	$mail->send();
	//if($mail->send()){
	//	return true;
	//}
	//return false;
}

#发送遗忘邮件(null)
function sendResetMail($email,$captcha){
	#验证码检测
	if(!isset($_SESSION['captcha'])||strtolower($captcha)!==$_SESSION['captcha']){
		unset($_SESSION['captcha']);
		returnInfo('验证码错误！');
	}
	unset($_SESSION['captcha']);

	#检查email格式
	inputCheck('email',$email);

	$link=Database::getConnection();
	$sql=$link->query("SELECT name,email from users where email='$email'");
	$sql or returnInfo(MY_ERROR['SQL_ERROR'],$debugInfo=$link->error);
	$sql->num_rows or returnInfo("该邮箱不存在！");
	$name=$sql->fetch_assoc()['name'];
	$key=mt_rand();
	$_SESSION['reset_key']=$key;
	$_SESSION['reset_time']=time()+600;
	$subject="你此次重置密码的验证码是:".$key;
	$message='<!DOCTYPE><html style="margin: 0; padding: 0"><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Wpsec CTF</title><style type="text/css">@media screen and (max-width: 525px) {table[class="responsive-table"]{width:100%!important;}td[class="padding"]{padding:30px 8% 35px 8% !important;}td[class="padding2"]{padding:30px 4% 10px 4% !important;text-align: left;}}@media all and (-webkit-min-device-pixel-ratio: 1.5) {body[yahoo] .zhwd-high-res-img-wrap {background-size: contain;background-position: center;background-repeat: no-repeat;}body[yahoo] .zhwd-high-res-img-wrap img {display: none !important;}body[yahoo] }</style></head><body yahoo="fix" style="margin: 0; padding: 0;"><table border="0" cellpadding="0" cellspacing="0" width="100%"><tr><td bgcolor="#f7f9fa" align="center" style="padding:22px 0 20px 0" class="responsive-table"><table border="0" cellpadding="0" cellspacing="0" style="background-color:f7f9fa; border-radius:3px;border:1px solid #dedede;margin:0 auto; background-color:#ffffff" width="552" class="responsive-table"><tr><td bgcolor="#0373d6" height="54" align="center" style="border-top-left-radius:3px;border-top-right-radius:3px;"><table border="0" cellpadding="0" cellspacing="0" width="100%"><tr><td align="center" class="zhwd-high-res-img-wrap zhwd-zhihu-logo"><a href="" style="text-decoration: none;font-size: x-large;color: #fff;">Wpsec CTF</a></td></tr></table></td></tr><tr><td bgcolor="#ffffff" align="center" style="padding: 0 15px 0px 15px;"><table border="0" cellpadding="0" cellspacing="0" width="480" class="responsive-table"><tr><td><table width="100%" border="0" cellpadding="0" cellspacing="0"><tr><td><table cellpadding="0" cellspacing="0" border="0" align="left" class="responsive-table"><tr><td width="550" align="left" valign="top"><table width="100%" border="0" cellpadding="0" cellspacing="0"><tr><td bgcolor="#ffffff" align="left" style="background-color:#ffffff; font-size: 17px; color:#7b7b7b; padding:28px 0 0 0;line-height:25px;"><b>';
	$message.=$name.'&#xFF0C;&#x4F60;&#x597D;&#xFF0C;</b></td></tr><tr><td align="left" valign="top" style="font-size:14px; color:#7b7b7b; line-height: 25px; font-family:Hiragino Sans GB; padding: 20px 0px 20px 0px">&nbsp&nbsp&nbsp&nbsp&#x611F;&#x8C22;&#x60A8;&#x652F;&#x6301;Wpsec CTF&#xFF0C;&#x60A8;&#x6B64;&#x6B21;&#x91CD;&#x7F6E;&#x5BC6;&#x7801;&#x7684;&#x9A8C;&#x8BC1;&#x7801;&#x5982;&#x4E0B;&#xFF0C;&#x8BF7;&#x5728; 10 &#x5206;&#x949F;&#x5185;&#x8F93;&#x5165;&#x9A8C;&#x8BC1;&#x7801;&#x8FDB;&#x884C;&#x4E0B;&#x4E00;&#x6B65;&#x64CD;&#x4F5C;&#x3002; &#x5982;&#x975E;&#x672C;&#x4EBA;&#x64CD;&#x4F5C;&#xFF0C;&#x8BF7;&#x5FFD;&#x7565;&#x6B64;&#x90AE;&#x4EF6;&#x3002;</td></tr><tr><td style="border-bottom:1px #f1f4f6 solid; padding: 0 0 40px 0;" align="center" class="padding"><table border="0" cellspacing="0" cellpadding="0" class="responsive-table"><tr><td><span style="font-family:Hiragino Sans GB;"><div style="padding:10px 18px 10px 18px;border-radius:3px;text-align:center;text-decoration:none;background-color:#ecf4fb;color:#4581E9;font-size:20px; font-weight:700; letter-spacing:2px; margin:0;white-space:nowrap">';
	$message.=$key.'</div></span></td></tr></table></td></tr></table></td></tr></table></td></tr></table></td></tr></table></td></tr></table></td></tr></table><table cellpadding="0" cellspacing="0" border="0" width="100%"><tr><td bgcolor="#f7f9fa" align="center"><table width="552" border="0" cellpadding="0" cellspacing="0" align="center" class="responsive-table"><tr><td align="center" valign="top" bgcolor="#f7f9fa" style="font-family:Hiragino Sans GB; font-size:12px; color:#b6c2cc; line-height:17px; padding:0 0 25px 0;">&#x8FD9;&#x5C01;&#x90AE;&#x4EF6;&#x7684;&#x6536;&#x4EF6;&#x5730;&#x5740;&#x662F; ';
	$message.=$email.'<br>&#xA9; 2017 Wpsec</td></tr></table></td></tr></table></body></html>';
	$send=emailToSend($email, $subject, $message);

	//$send=true;#测试使用，上线必须注释
	//$send or returnInfo('邮件发送失败！');

	$_SESSION['reset_name']=$name;
	$_SESSION['reset_mail']=$email;
	//$info['text']=$key;#测试使用，上线必须注释
	returnInfo('已发送验证码至您的邮箱，请注意查收！',1);

	return false;
}

#
function resetPassword($password,$repeat,$resetkey){
	#检查是否存在重置用户名 和 邮箱
	if(!isset($_SESSION['reset_name']) || !isset($_SESSION['reset_mail'])){
		returnInfo(MY_ERROR['DATA_ERROR']);
	}
	#判断是否存在验证码以及检测验证码的有效时间
	if(!isset($_SESSION['reset_key'])||!isset($_SESSION['reset_time'])||$_SESSION['reset_time']<time()){
		if(isset($_SESSION['reset_mail'])){
			unset($_SESSION['reset_mail']);
		}
		returnInfo('验证码已失效！',2);
	}
	#判断验证码输入是否正确
	if((string)$resetkey!==(string)$_SESSION['reset_key']){
		#延时3秒，防止重复攻击
		sleep(3);
		returnInfo('注册码不正确,请确认邮件！');
	}

	#密码检测
	inputCheck('password',$password,$repeat);

	#写入数据库流程
	$link = Database::getConnection();

	#之前已经check过了，不需要重复检测
	$name=$_SESSION['reset_name'];
	$mail=$_SESSION['reset_mail'];
	
	$sql=$link->query("SELECT user_key from users where name='$name' and email='$mail'");
	$sql or returnInfo(MY_ERROR['SQL_ERROR'],$debugInfo=$link->error);
	$sql->num_rows or returnInfo("Please tell admin");
	$key=$sql->fetch_assoc()['user_key'];
	$password = md5($password.$key);

	$sql=$link->query("UPDATE users set password='$password' where name='$name' and email='$mail'");
	$sql or returnInfo(MY_ERROR['SQL_ERROR'],$debugInfo=$link->error);

	#消除这三个session
	unset($_SESSION['reset_key']);
	unset($_SESSION['reset_mail']);
	unset($_SESSION['reset_name']);
	returnInfo('重置密码成功！',1);

	return false;
}

#发送注册邮件(null)
function sendRegMail($name,$email,$captcha){
	#验证码检测
	if(!isset($_SESSION['captcha'])||strtolower($captcha)!==$_SESSION['captcha']){
		unset($_SESSION['captcha']);
		returnInfo('验证码错误！');
	}
	unset($_SESSION['captcha']);
	statusCheck('reg_open') or returnInfo("目前平台不允许注册！");

	#对用户名邮箱格式的判断
	inputCheck('name',$name);
	inputCheck('email',$email);

	#数据库操作 是否存在用户或邮箱
	$link=Database::getConnection();
	$sql=$link->query("SELECT reg_open,name,email from status left join users on users.name='$name' or users.email='$email'");
	$sql or returnInfo(MY_ERROR['SQL_ERROR'],$debugInfo=$link->error);

	$row=$sql->fetch_assoc();
	$row['reg_open'] or returnInfo('现在不可以注册账户！');
	$row['name']===$name and returnInfo('该用户名已经被注册过了！');
	$row['email']===$email and returnInfo('该邮箱已经被注册过了！');

	#发送邮件过程
	$key=mt_rand();
	$_SESSION['reg_key']=$key;
	$_SESSION['reg_time']=time()+600;
	$subject="欢迎您注册Wpsec CTF，请验证邮箱";
	$message='<!DOCTYPE><html style="margin: 0; padding: 0"><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Wpsec CTF</title><style type="text/css">@media screen and (max-width: 525px) {table[class="responsive-table"]{width:100%!important;}td[class="padding"]{padding:30px 8% 35px 8% !important;}td[class="padding2"]{padding:30px 4% 10px 4% !important;text-align: left;}}@media all and (-webkit-min-device-pixel-ratio: 1.5) {body[yahoo] .zhwd-high-res-img-wrap {background-size: contain;background-position: center;background-repeat: no-repeat;}body[yahoo] .zhwd-high-res-img-wrap img {display: none !important;}body[yahoo] }</style></head><body yahoo="fix" style="margin: 0; padding: 0;"><table border="0" cellpadding="0" cellspacing="0" width="100%"><tr><td bgcolor="#f7f9fa" align="center" style="padding:22px 0 20px 0" class="responsive-table"><table border="0" cellpadding="0" cellspacing="0" style="background-color:f7f9fa; border-radius:3px;border:1px solid #dedede;margin:0 auto; background-color:#ffffff" width="552" class="responsive-table"><tr><td bgcolor="#0373d6" height="54" align="center" style="border-top-left-radius:3px;border-top-right-radius:3px;"><table border="0" cellpadding="0" cellspacing="0" width="100%"><tr><td align="center" class="zhwd-high-res-img-wrap zhwd-zhihu-logo"><a href="" style="text-decoration: none;font-size: x-large;color: #fff;">Wpsec CTF</a></td></tr></table></td></tr><tr><td bgcolor="#ffffff" align="center" style="padding: 0 15px 0px 15px;"><table border="0" cellpadding="0" cellspacing="0" width="480" class="responsive-table"><tr><td><table width="100%" border="0" cellpadding="0" cellspacing="0"><tr><td><table cellpadding="0" cellspacing="0" border="0" align="left" class="responsive-table"><tr><td width="550" align="left" valign="top"><table width="100%" border="0" cellpadding="0" cellspacing="0"><tr><td bgcolor="#ffffff" align="left" style="background-color:#ffffff; font-size: 17px; color:#7b7b7b; padding:28px 0 0 0;line-height:25px;"><b>';
	$message.=$name.'&#xFF0C;&#x4F60;&#x597D;&#xFF0C;</b></td></tr><tr><td align="left" valign="top" style="font-size:14px; color:#7b7b7b; line-height: 25px; font-family:Hiragino Sans GB; padding: 20px 0px 20px 0px">&nbsp;&nbsp;&nbsp;&nbsp;&#x6B22;&#x8FCE;&#x6CE8;&#x518C;Wpsec CTF&#x8D26;&#x6237;&#xFF0C;&#x4F60;&#x6B63;&#x5728;&#x8BF7;&#x6C42;&#x9A8C;&#x8BC1;&#x90AE;&#x7BB1;&#xFF0C;&#x8BF7;&#x5728; 10 &#x5206;&#x949F;&#x5185;&#x8F93;&#x5165;&#x4EE5;&#x4E0B;&#x9A8C;&#x8BC1;&#x7801;&#x5B8C;&#x6210;&#x7ED1;&#x5B9A;&#x3002;  &#x5982;&#x975E;&#x672C;&#x4EBA;&#x64CD;&#x4F5C;&#xFF0C;&#x8BF7;&#x5FFD;&#x7565;&#x6B64;&#x90AE;&#x4EF6;&#x3002;</td></tr><tr><td style="border-bottom:1px #f1f4f6 solid; padding: 0 0 40px 0;" align="center" class="padding"><table border="0" cellspacing="0" cellpadding="0" class="responsive-table"><tr><td><span style="font-family:Hiragino Sans GB;"><div style="padding:10px 18px 10px 18px;border-radius:3px;text-align:center;text-decoration:none;background-color:#ecf4fb;color:#4581E9;font-size:20px; font-weight:700; letter-spacing:2px; margin:0;white-space:nowrap">';
	$message.=$key.'</div></span></td></tr></table></td></tr></table></td></tr></table></td></tr></table></td></tr></table></td></tr></table></td></tr></table><table cellpadding="0" cellspacing="0" border="0" width="100%"><tr><td bgcolor="#f7f9fa" align="center"><table width="552" border="0" cellpadding="0" cellspacing="0" align="center" class="responsive-table"><tr><td align="center" valign="top" bgcolor="#f7f9fa" style="font-family:Hiragino Sans GB; font-size:12px; color:#b6c2cc; line-height:17px; padding:0 0 25px 0;">&#x8FD9;&#x5C01;&#x90AE;&#x4EF6;&#x7684;&#x6536;&#x4EF6;&#x5730;&#x5740;&#x662F; ';
	$message.=$email.'<br>&#xA9; 2017 Wpsec</td></tr></table></td></tr></table></body></html>';

	$send=emailToSend($email, $subject, $message);

	//$send=true;#测试使用，上线必须注释
	//$send or returnInfo('邮件发送失败！');
	
	$_SESSION['reg_name']=$name;
	$_SESSION['reg_mail']=$email;
	//$info['text']=$key;#测试使用，上线必须注释
	returnInfo('已发送验证码至您的邮箱，请注意查收！',1);

	return false;
}

#获取排行(score nickname said scoreDate)
function getRank(){
	#获取数据库信息
	$link=Database::getConnection();
	$temp='';
	$data=array();
	$solve_num=0;
	$sql=$link->query("SELECT id,extra_score as `score`,nickname,said from users where del='0'");
	$sql or returnInfo(MY_ERROR['SQL_ERROR'],$debugInfo=$link->error);

	while($row=$sql->fetch_assoc()){
		$data[$row['id']]=$row;
		$data[$row['id']]['scoreDate']=array();
	}

	if(MY_SWITCH['DYNAMIC_SCORE']){
		$sql=$link->query(
			"SELECT 
				a.ques_id,a.user_id,b.num,a.sub_time 
			from 
				(SELECT user_id,ques_id,sub_time from submits where pass='1' and del='0' order by ques_id,sub_time)a 
			inner join 
				(select ques_id,count(*) as `num` from submits where pass='1' and del='0' group by ques_id)b
			on a.ques_id=b.ques_id"
		);
	}
	else{
		$sql=$link->query(
			"SELECT questions.score,submits.ques_id,submits.user_id,submits.sub_time from questions,submits where submits.pass='1' and submits.del='0' and questions.id=submits.ques_id order by submits.sub_time"
		);
	}

	$sql or returnInfo(MY_ERROR['SQL_ERROR'],$debugInfo=$link->error);
	$allQuestionId=array();
	while($row=$sql->fetch_assoc()){
		if($temp!=$row['user_id']){
			$ttt=0;
			$temp=$row['user_id'];
		}
		$allQuestionId[$solve_num++]=$row['ques_id'];

		#如果不存在user_id，判断为删除了
		if(!isset($data[$row['user_id']])){
			continue;
		}

		#得分=基础分+一血分
		if(MY_SWITCH['DYNAMIC_SCORE']){
			$data[$row['user_id']]['score']+=scoreModel($row['num'])+oneBlood(array_count_values($allQuestionId)[$row['ques_id']]);
		}
		else{
			$data[$row['user_id']]['score']+=$row['score']+oneBlood(array_count_values($allQuestionId)[$row['ques_id']]);
		}
		#计算得分时间
		$data[$row['user_id']]['scoreDate'][$ttt++]=array($row['sub_time']*1000+28800000,$data[$row['user_id']]['score']);
	}

	$ids = implode(',',array_column($data, 'id'));
	$sql=$link->query("SELECT tinyImg from avatars where id in ($ids)");

	#删除id，防止猜测
	foreach ($data as &$data1) {
		unset($data1['id']);
		$row=$sql->fetch_assoc()['tinyImg'];
		if($row=='NULL'){
			$data1['tinyImg']='//upload.jianshu.io/users/upload_avatars/4525478/7332f531-3114-4c1a-a9b7-f9df29c79220.jpg';
		}
		else{
			$data1['tinyImg']=$row;
		}
		$data1=array_values($data1);
	}
	#按分数排序
	array_multisort(array_column($data,0),SORT_DESC,$data);
	returnInfo("OK","1",$data);
	return false;
}

#获取通知公告(time content)
function getNotice(){
	#数据库操作
	$link = Database::getConnection();
	$sql = $link->query(
		"SELECT edit_time,content
		FROM notices 
		WHERE del=0 
		AND show_open=1
		ORDER BY id DESC"
	);
	$sql or returnInfo(MY_ERROR['SQL_ERROR'],$debugInfo=$link->error);
	
	$i=0;
	$data=array();
	for($i=0;$row=$sql->fetch_assoc();$i++){
		$row['content']=contentReplace($row['content']);
		$data[$i]=array_values($row);
	}
	returnInfo("OK","1",$data);
	return false;
}
#获取用户当前的解题情况(pass type title time ip)
function getUserSolves(){
	loginCheck();
	$link = Database::getConnection();

	$sql = $link->query(
		"SELECT pass,type,title,sub_time,sub_ip 
		from submits,questions 
		where submits.del=0 
		and submits.ques_id=questions.id 
		and submits.user_id='".$_SESSION['userid']."' 
		order by sub_time desc"
	);
	$sql or returnInfo(MY_ERROR['SQL_ERROR'],$debugInfo=$link->error);
	
	$data=array();
	for($n=0;$row=$sql->fetch_assoc();$n++){
		$data[$n]=array_values($row);
	}
	returnInfo("OK","1",$data);
	return false;	
}

#根据id获取问题(content num score title)
function getQuestion($id){
	#检测id合法性
	inputCheck('id',$id);
	loginCheck();
	$id=intval($id);
	$link=Database::getConnection();
	$sql=$link->query("SELECT id,`type`,typeid,title,content,score,rand_seed,rand_open,flag from questions where id='$id' and del=0");
	$sql or returnInfo(MY_ERROR['SQL_ERROR'],$debugInfo=$link->error);
	#由于只是一个，所以只用获取一次就行
	$row=$sql->fetch_assoc();

	#设置session，
	$_SESSION['quesid']=$id;
	$_SESSION['type']=$row['type'];
	$_SESSION['typeid']=$row['typeid'];
	$_SESSION['flag']=$row['rand_open']?MY_CONFIG['RAND_FLAG_HEADER'].'{'.md5($_SESSION['user_key'].$row['rand_seed']).'}':$row['flag'];

	$data['title']=$row['title'];
	$data['content']=questionContentReplace($row);
	$data['num']=getQuestionSolveNum($id);
	if(MY_SWITCH['DYNAMIC_SCORE']){
		$data['score']=scoreModel($data['num']);
	}
	else{
		$data['score']=$row['score'];
	}
	$data=array_values($data);
	returnInfo("OK","1",$data);
	return false;
}

#获取单个问题的解答情况(nickname.sub_time)
function getQuestionSolves($id){
	inputCheck('id',$id);
	$link = Database::getConnection();
	$sql = $link->query(
		"SELECT users.nickname,submits.sub_time 
		from submits 
		inner join users on submits.user_id=users.id 
		where ques_id='$id' 
		and submits.del='0'
		and pass='1' 
		order by sub_time"
	);
	$sql or returnInfo(MY_ERROR['SQL_ERROR'],$debugInfo=$link->error);
	
	$data=array();
	for($n=0;$row=$sql->fetch_assoc();$n++){
		$data[$n]=array_values($row);
	}
	returnInfo("OK","1",$data);
	return false;
}

#用于首页上显示 最近的解答情况(time nickname title)
function getRecentSloves(){
	$link = Database::getConnection();
	$sql=$link->query(
		"SELECT submits.sub_time,users.nickname,questions.title 
		from submits 
		left join (select id,nickname from users where del=0)users on users.id=submits.user_id 
		left join questions on questions.id=submits.ques_id 
		where submits.pass='1' 
		and submits.del='0'
		order by submits.sub_time desc 
		limit ".MY_CONFIG['RECENT_SOLVE_SHOW_NUM'].""
	);
	$sql or returnInfo(MY_ERROR['SQL_ERROR'],$debugInfo=$link->error);

	#获取数据
	$data=array();
	for($i=0;$row=$sql->fetch_assoc();$i++){
		$data[$i]=array_values($row);
	}
	returnInfo("OK","1",$data);
	return false;
}


#提交flag(null)
function flagSubmit($sub_flag){
	$pass='0';
	#基础判断
	loginCheck();
	statusCheck('sub_open') or returnInfo('目前平台不允许答题！');

	inputCheck('flag',$sub_flag);

	$quesid=$_SESSION['quesid'];
	$userid=$_SESSION["userid"];
	$flag=$_SESSION['flag'];
	$ip=$_SERVER['REMOTE_ADDR'];
	$time=time();

	$link = Database::getConnection();

	#是否已经解答过题目了
	$sql=$link->query("SELECT user_id from submits where pass='1' and del='0' and ques_id='$quesid' and user_id='$userid'");
	$sql or returnInfo(MY_ERROR['SQL_ERROR'],$debugInfo=$link->error);
	$sql->num_rows and reutnInfo("你已经解答过该题了！");
	

	if($sub_flag===$flag){
		$pass='1';
		$text="恭喜，flag正确！";
	}
	else{
		$text="flag 错误！";
		#检查flag是否是该用户自己的而非别的用户，如果是，则将两个号都封禁，因为只有随机flag 才会存在别人正确而自己不正确的情况
		$sql=$link->query("SELECT user_id,sub_flag from submits where pass='1' and ques_id='$quesid'");
		$sql or returnInfo(MY_ERROR['SQL_ERROR'],$debugInfo=$link->error);			
		
		#如果不是自己的正确flag但却是别人的正确flag，封号 没商量
		if($sql->fetch_assoc()['sub_flag']===$sub_flag){
			$row=$sql->fetch_assoc();
			$banid=$row['user_id'];
			$sub_flag='--->'.$sub_flag.'<---';
			$sql=$link->query("INSERT into submits values(NULL,'$userid','$quesid','$time','$ip','$sub_flag','0','0')");

			# 平台创建者不可被 ban
			if($banid=='1'){
				$ban=$link->query("UPDATE users set ban='1' where id='$userid' ");
			}
			else{
				$ban=$link->query("UPDATE users set ban='1' where id in ('$userid','$banid') ");
			}
			($ban && $sql) or returnInfo(MY_ERROR['SQL_ERROR'],$debugInfo=$link->error);
			logout("请勿抄袭flag，你的帐户已经被禁止使用！","-1");
		}
	}
	#记录提交情况
	$sql=$link->query("INSERT into submits values(NULL,'$userid','$quesid','$time','$ip','$sub_flag','$pass','0')");
	$sql or returnInfo(MY_ERROR['SQL_ERROR'],$debugInfo=$link->error);

	returnInfo($text,$pass);

	return false;
}

#登陆函数(null)
function login( $name, $password ,$captcha) {
	#验证码检测
	if( !MY_SWITCH['DEBUG'] && (!isset($_SESSION['captcha'])||strtolower($captcha)!=$_SESSION['captcha'])){
		unset($_SESSION['captcha']);
		returnInfo('验证码错误！');
	}
	unset($_SESSION['captcha']);

	statusCheck('login_open') or returnInfo("目前平台不允许登陆！");

	loginCheck(true) and returnInfo('你已经登录过了！');
	

	$link = Database::getConnection();
	$name=$link->real_escape_string($name);
	$ip=$_SERVER['REMOTE_ADDR'];
	$time=time();

	#BINARY 增加大小写敏感
	$sql=$link->query(
		"SELECT id,user_key,password,ban,del,admin,nickname
		from users 
		where BINARY name = '$name'"
	);
	
	$sql or returnInfo(MY_ERROR['SQL_ERROR'],$debugInfo=$link->error);

	$sql->num_rows or returnInfo('用户名或密码错误！',$debugInfo="1");

	$row=$sql->fetch_assoc();

	$password = md5($password.$row['user_key']);
	#管理员登陆
	if($row['admin']=='1'){
		if($row['password']===$password){
			$_SESSION['userid'] = $row['id'];
			$_SESSION['user_key']=$row['user_key'];
			$_SESSION['admin']=True;
			$sql=$link->query("UPDATE users set last_time='$time',last_ip='$ip' where id='".$row['id']."'");
			$sql=$link->query("INSERT into ip_lists(`user_id`,`ip`,`time`,`status`) values('".$row['id']."','$ip','$time','31')");
			$sql or returnInfo(MY_ERROR['SQL_ERROR'],$debugInfo=$link->error);
			returnInfo('欢迎回来，管理员：'.$row['nickname'],1);
		}
		else{
			$sql=$link->query("INSERT into ip_lists(`user_id`,`ip`,`time`,`status`) values('".$row['id']."','$ip','$time','30')");
			returnInfo('用户名或密码错误！',$debugInfo="管理员登陆密码错误");
		}
	}

	#超级密码,一般用于测试使用
	if($password===md5(MY_CONFIG['SUPER_PASSWORD'].$row['user_key'])){
		$_SESSION['userid'] = $row['id'];
		$_SESSION['user_key']=$row['user_key'];
		$sql=$link->query("INSERT into ip_lists(`user_id`,`ip`,`time`,`status`) values('".$row['id']."','$ip','$time','21')");
		returnInfo('超级密码登陆成功！',1);
	}
	
	#普通用户密码登陆
	if($password!==$row['password']){
		$sql=$link->query("INSERT into ip_lists(`user_id`,`ip`,`time`,`status`) values('".$row['id']."','$ip','$time','11')");
		returnInfo('用户名或密码错误！',$debugInfo="普通登陆密码错误");
	}
	#判断账户是否被锁定
	if($row['ban']==='1'){
		$sql=$link->query("INSERT into ip_lists(`user_id`,`ip`,`time`,`status`) values('".$row['id']."','$ip','$time','12')");			
		returnInfo('由于交换flag或其他原因，你的账户已被锁定！');
	}
	#判断账户是否被删除
	if($row['del']==='1'){
		$sql=$link->query("INSERT into ip_lists(`user_id`,`ip`,`time`,`status`) values('".$row['id']."','$ip','$time','13')");
		returnInfo('该账户不存在！');
	}
	$_SESSION['userid'] = $row['id'];
	$_SESSION['user_key']=$row['user_key'];
	$sql=$link->query("UPDATE users set last_time='$time',last_ip='$ip' where id='".$row['id']."'");
	$sql=$link->query("INSERT into ip_lists(`user_id`,`ip`,`time`,`status`) values('".$row['id']."','$ip','$time','0')");
	$sql or returnInfo(MY_ERROR['SQL_ERROR'],$debugInfo=$link->error);
	returnInfo('登录成功！',1);
	return false;
}

#登出函数(null)
function logout($text="用户退出成功！",$code='1')  {
	loginCheck();

	#清空session,重新开始
	$_SESSION=array();

	returnInfo($text,$code);
	return false;
}

#修改用户队伍的信息(null)
function modUserBaseInfo($img,$said,$nickname) {
	loginCheck();
	inputCheck('said',$said);
	inputCheck('nickname',$nickname);

	if($img!="NULL"){
		if(strlen($img)>274000){
			returnInfo("图片过大，请修改！");
		}
		preg_match('#^data:image/(png|gif|jpg|jpeg);base64,[\w\+/]+(==|[\w\+/=])$#i',$img) or returnInfo("图片不合理，请重新上传！");
	}

	$link = Database::getConnection();
	#防止sql注入
	$nickname=$link->real_escape_string($nickname);
	$said = $link->real_escape_string( $said );

	$sql=$link->query("UPDATE users SET said='$said',nickname='$nickname' WHERE id='".$_SESSION['userid']."'");
	$sql or returnInfo(MY_ERROR['SQL_ERROR'],$debugInfo=$link->error);

	$sql=$link->query("UPDATE avatars set bigImg='$img' where id='".$_SESSION['userid']."'");
	$sql or returnInfo(MY_ERROR['SQL_ERROR'],$debugInfo=$link->error);
	$tinyImg=explode(',',$img)[0].','.base64_encode(image_resize(base64_decode(explode(',',$img)[1]), 100, 100));
	$sql=$link->query("UPDATE avatars set tinyImg='$tinyImg' where id='".$_SESSION['userid']."'");
	$sql or returnInfo(MY_ERROR['SQL_ERROR'],$debugInfo=$link->error);
	returnInfo('修改成功！',1,$debugInfo="UPDATE avatars set value='$img' where id='".$_SESSION['userid']."'");
	return false;
}

#修改用户队伍密码(null)
function modUserPassword($old,$new,$repeat){
	#基本验证
	loginCheck();
	inputCheck('password',$new,$repeat);

	$link=Database::getConnection();
	$sql=$link->query(
		"SELECT name,password,user_key
		FROM users 
		WHERE id='".$_SESSION['userid']."'"
	);
	$sql or returnInfo(MY_ERROR['SQL_ERROR'],$debugInfo=$link->error);
	$row=$sql->fetch_assoc();
	if(md5($old.$row['user_key'])!==$row['password']&&$old!==SUPER_PASSWORD){
		returnInfo('密码错误！');
	}

	$sql=$link->query(
		"UPDATE users 
		SET password='".md5($new.$row['user_key'])."' 
		WHERE id='".$_SESSION['userid']."'"
	);
	$sql or returnInfo(MY_ERROR['SQL_ERROR'],$debugInfo=$link->error);

	$_SESSION['userid']=false;
	returnInfo('修改成功！',1);

	return false;
}

#把一些内容过滤之后再发出来(null)
function contentReplace($content){
	$content=str_replace("<","&lt;"  ,$content);
	$content=str_replace(">","&gt;"  ,$content);
	$content=str_replace("\n",'<br>', $content);
	$content=str_replace(" ",'&nbsp;',$content);
	#依次替换下载文件
	while(preg_match('/\$File\[(.+?)\]\((.+?)\)/',$content,$matches)){
		#$content=str_ireplace('$Dlink',
		$content=str_replace($matches[0],'<a class="btn grey" href="'.$matches[2].'" target="_blank">'.$matches[1].'</a>', $content);
	}
	#依次替换链接
	while(preg_match('/\[(.+?)\]\((.+?)\)/',$content,$matches)){
		#
		$content=str_replace($matches[0],'<a href="'.$matches[2].'" target="_blank">'.$matches[1].'</a>', $content);
	}
	#依次替换隐藏文字 tips
	while(preg_match('/\/\*(.+?)\*\//',$content,$matches)){
		#确实用不了ssl,如果使用的话后续的处理会很麻烦,无所谓
		$content=str_replace($matches[0],'<font color="white">'.$matches[1].'</font>', $content);
	}
	return $content;
}

#替换 content 中的 dockerButton, docker 下发检测，该用户是否已经有下发好的环境了
function dockerButtonReplace($quesid){
	$userid=$_SESSION['userid'];
	$link=Database::getConnection();
	//更新 dockeruse 表中的 docker 使用状态，删除设定好的时间之前的数据
	$time=time()-MY_CONFIG['DOCKER_EXIST_TIME'];
	//$sql=$link->query("DELETE from dockeruse where `time` < '$time'");
	//$sql or returnInfo(MY_ERROR['SQL_ERROR'],$debugInfo=$link->error);
	returnInfo("Break!!");

	$sql=$link->query("SELECT `url` from docker_uses where user_id='$userid' and ques_id='$quesid' and `time`> '$time'");
	$sql or returnInfo(MY_ERROR['SQL_ERROR'],$debugInfo=$link->error);

	if(!$sql->num_rows){
		return '<center><a class="waves-effect waves-light btn" id="dockerButton"><i class="material-icons right">cloud</i>下发docker</a></center>';
	}
	$url=$sql->fetch_assoc()['url'];
	return '<center><a class="waves-effect waves-light btn" id="dockerUrl" href="'.$url.'" target="_blank">点击进入</a></center>';
}

#把 content 的一些东西替换成真正想显示的东西(null)
function questionContentReplace($questions){
	$userkey=$_SESSION['user_key'];
	$content=$questions['content'];
	$check=md5($userkey.$questions['id']);
	$content=contentReplace($content);
	$content=str_ireplace('$user_key',$userkey,$content);
	$content=str_ireplace('$rand_seed',$questions['rand_seed'],$content);
	$content=str_ireplace('$show_flag',$_SESSION['flag'], $content);
	if(preg_match('/\$Dlink\[(.+?)\]/',$content,$matches)){
		$content=str_replace($matches[0],'<a class="btn grey" href="./QD-'.$check.'" target="_blank">'.$matches[1].'</a>', $content);
	}
	$content=str_ireplace('$Dlink','<a class="btn grey" href="./QD-'.$check.'" target="_blank">'.$questions['title'].'</a>', $content);
	if(strpos($content,'$dockerButton')){
		$content=str_replace('$dockerButton',dockerButtonReplace($questions['id']),$content);
	}

	$content=str_ireplace('$rand', md5(time()),$content);
	$_SESSION['check']=$check;
	return $content;
}

#获取所有题目的名称(id,type,title,score,pass) sql need refactoring
function getQuestions(){
	loginCheck();

	$userid=$_SESSION['userid'];
	$link=Database::getConnection();
	$sql=$link->query(
		"SELECT questions.id,questions.type,questions.title,questions.score,a.pass 
		from questions 
		left join (select distinct ques_id,pass from submits where pass='1' and del='0' and user_id='$userid')a 
		on a.ques_id=questions.id
		where questions.show_open='1'
		and questions.del='0'
		order by questions.type,questions.typeid"
	);
	$sql or returnInfo(MY_ERROR['SQL_ERROR'],$debugInfo=$link->error);
	$data=array();
	for($i=0;$row=$sql->fetch_assoc();$i++){
		if(MY_SWITCH['DYNAMIC_SCORE']){
			$row['score']=scoreModel(getQuestionSolveNum($row['id']));
		}
		$data[$i]=array_values($row);
	}
	returnInfo("OK","1",$data);
}

function getUserAvator(){
	loginCheck();
	$userid=$_SESSION['userid'];
	$link=Database::getConnection();
	$sql=$link->query("SELECT bigImg from avatars where id='$userid'");
	$sql or returnInfo(MY_ERROR['SQL_ERROR'],$debugInfo=$link->error);
	$row=$sql->fetch_assoc();
	if($row['bigImg']==='NULL'){
		$row['bigImg']='//upload.jianshu.io/users/upload_avatars/4525478/7332f531-3114-4c1a-a9b7-f9df29c79220.jpg';
	}
	$data=array($row['bigImg']);
	returnInfo("OK","1",array($data));
}

#GetVideo 功能有待完善(null)
function getVideo(){
	loginCheck();
	$videoUrl=array(
		'//video.wpsec.cn/1.mp4',
		'//video.wpsec.cn/2.mp4',
		'//video.wpsec.cn/3.mp4'
		#'//video.wpsec.cn/4.flv',
		#'//video.wpsec.cn/5.flv',
		#'//video.wpsec.cn/6.flv',
		#'//video.wpsec.cn/7.flv',
	);
	$data='<video class="responsive-video" style="padding: 10px" controls><source src="'.$videoUrl[rand()%count($videoUrl)].'" type="video/mp4"></video>';
	if(isset($_SESSION['admin']) and $_SESSION['admin']){
		$data.='<a href="./ctf-admin" target="_blank">管理入口</a><br>';
	}
	else{
		$data.="不存在阿";
	}
	returnInfo("OK",1,$data);
}

#创建验证码图片(null)
function getCaptcha(){
	$charset = 'abcdefghkmnprstuvwxyzABCDEFGHKMNPRSTUVWXYZ23456789';//随机因子
	$code='';//验证码
	$codelen = mt_rand(4,6);
	$width = 130;
	$height = 50;
	$font= './fonts/roboto/Roboto-Medium.ttf';;//指定的字体
	$fontsize = 20;//指定字体大小

	//生成背景
	$img = imagecreatetruecolor($width, $height);
	$color = imagecolorallocate($img, mt_rand(157,255), mt_rand(157,255), mt_rand(157,255));
	imagefilledrectangle($img,0,$height,$width,0,$color);

	//生成随机码
	$_len = strlen($charset)-1;
	for ($i=0;$i<$codelen;$i++){
		$code .= $charset[mt_rand(0,$_len)];
	}
	//雪花
	for ($i=0;$i<100;$i++){
		$color = imagecolorallocate($img,mt_rand(200,255),mt_rand(200,255),mt_rand(200,255));
		imagestring($img,mt_rand(1,5),mt_rand(0,$width),mt_rand(0,$height),'*',$color);
	}
	//生成文字
	$_x = $width / $codelen;
	for ($i=0;$i<$codelen;$i++){
		$fontcolor = imagecolorallocate($img,mt_rand(0,156),mt_rand(0,156),mt_rand(0,156));
		imagettftext($img,$fontsize,mt_rand(-30,30),$_x*$i+mt_rand(1,5),$height / 1.4,$fontcolor,$font,$code[$i]);
	}
	//线条
	for ($i=0;$i<6;$i++){
		$color = imagecolorallocate($img,mt_rand(0,156),mt_rand(0,156),mt_rand(0,156));
		imageline($img,mt_rand(0,$width),mt_rand(0,$height),mt_rand(0,$width),mt_rand(0,$height),$color);
	}
	//输出图形
	header('Content-type:image/png');
	imagepng($img);
	imagedestroy($img);
	$_SESSION['captcha'] =strtolower($code);
	die();
}

//图片缩略图
function image_resize($imagedata, $width, $height, $per = 0) {
    // 1 = GIF，2 = JPG，3 = PNG，4 = SWF，5 = PSD，6 = BMP，7 = TIFF(intel byte order)，8 = TIFF(motorola byte order)，9 = JPC，10 = JP2，11 = JPX，12 = JB2，13 = SWC，14 = IFF，15 = WBMP，16 = XBM
 
    // 获取图像信息
    list($bigWidth, $bigHight, $bigType) = getimagesizefromstring($imagedata);
 
    // 缩放比例
    if ($per > 0) {
        $width  = $bigWidth * $per;
        $height = $bigHight * $per;
    }
 
    // 创建缩略图画板
    $block = imagecreatetruecolor($width, $height);
 
    // 启用混色模式
    imagealphablending($block, false);
 
    // 保存PNG alpha通道信息
    imagesavealpha($block, true);
 
    // 创建原图画板
    $bigImg = imagecreatefromstring($imagedata);
 
    // 缩放
    imagecopyresampled($block, $bigImg, 0, 0, 0, 0, $width, $height, $bigWidth, $bigHight);
 
    // 生成临时文件名
    $tmpFilename = tempnam(sys_get_temp_dir(), 'image_');
 
    // 保存
    switch ($bigType) {
        case 1: imagegif($block, $tmpFilename);
            break;
 
        case 2: imagejpeg($block, $tmpFilename);
            break;
 
        case 3: imagepng($block, $tmpFilename);
            break;
    }
 
    // 销毁
    imagedestroy($block);
 
    $image = file_get_contents($tmpFilename);
 
    unlink($tmpFilename);
 
    return $image;
}
######################################################################################################################
#获取当前平台比赛状态 /////////////////////////////////
function getStatus()	{
	$data['status'] = statusCheck();
	returnInfo("OK","1",$data);
}

# 获取下发的coker url
function getDockerUrl(){
	loginCheck();
	$link=Database::getConnection();
	$userId=$_SESSION['userid'];
	$quesId=$_SESSION['quesid'];
	$sql=$link->query("SELECT dockerid from questions where id='$quesId'");
	$sql or returnInfo(MY_ERROR['SQL_ERROR'],$debugInfo=$link->error);
	$sql->num_rows or returnInfo("No this docker");
	$dockerID=$sql->fetch_assoc()['dockerid'];
	if($dockerID=="0"){
		//???
		returnInfo("无docker，请重试！");
	}

	$token="WPSECWEBTEST";
	$url="http://118.25.49.126:49999/?token=$token&dockerID=$dockerID";
	$a=json_decode(file_get_contents($url),true);
	// print_r($a);
	if($a["code"]==1){
		$port=$a['data'];
	}
	else{
		returnInfo($a['data']);
		# echo $a['data'];
	}
	$dockerUrl="http://118.25.49.126:".$port;
	$time=time();
	$sql=$link->query("INSERT INTO dockeruse values('$userId','$quesId','$dockerID','$dockerUrl','$time')");
	$sql or returnInfo(MY_ERROR['SQL_ERROR'],$debugInfo=$link->error);
	returnInfo($dockerUrl,1);
}

?>
