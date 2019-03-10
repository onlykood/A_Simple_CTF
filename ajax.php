<?php
include 'init.php';
include 'functions.php';

if( !isset( $_GET['m'] ) || !is_string( $_GET['m'] ) )
{
	include '404.html';
	die();
}

//检测token令牌是否存在/被改写
if(!isset($_POST['token'])||($_SESSION['token'] !== $_POST['token'])){
	if($_GET['m']!='getSession' && $_GET['m']!='getCaptcha' && $_GET['m']!='getConfig'){
		unset($_SESSION['token']);
		if(!isset($_POST['token'])){
			returnInfo(MY_ERROR['DATA_MISS']);
		}
		returnInfo(MY_ERROR['DATA_ERROR']);
	}
}
switch( $_GET['m'] )	{
	case 'getConfig':
		postCheck('type');
		getConfig($_POST['type']);

	case 'getNotice':
		getNotice();
	case 'getQuestion':
		postCheck('id');
		getQuestion($_POST['id']);
	case 'getRank':
		getRank();

	case 'getRecentSloves':
			getRecentSloves();
	case 'getUserSolves':
			getUserSolves();

	case 'getQuestionSolves':
		postCheck('id');
		getQuestionSolves($_POST['id']);

	case 'register':
		postCheck('password','repeat','regkey');
		register($_POST['password'],$_POST['repeat'],$_POST['regkey']);

	case 'flagSubmit':
		postCheck('flag');
		flagSubmit($_POST['flag']);
	case 'login':
		postCheck('username','password','captcha');
		login($_POST['username'],$_POST['password'],$_POST['captcha']);

	case 'logout':
		logout();

	case 'getSession':
		getSession();

	case 'getStatus':
		getStatus();
	case 'modUserBaseInfo':
		postCheck('img','said','nickname');
		//returnInfo($_POST['img']);
		modUserBaseInfo($_POST['img'],$_POST['said'],$_POST['nickname']);
	case 'modUserPassword':
		postCheck('old','new','repeat');
		modUserPassword($_POST['old'],$_POST['new'],$_POST['repeat']);
	case 'sendRegMail':
		postCheck('username','captcha','email');
		sendRegMail($_POST['username'],$_POST['email'],$_POST['captcha']);
	case 'sendResetMail':
		postCheck('email','captcha');
		sendResetMail($_POST['email'],$_POST['captcha']);
	case 'resetPassword':
		postCheck('password','repeat','resetkey');
		resetPassword($_POST['password'],$_POST['repeat'],$_POST['resetkey']);
	case 'mailSendCheck':
		mailSendCheck();
	case 'getQuestions':
		getQuestions();
	case 'getCaptcha':
		getCaptcha();
	case 'getVideo':
		getVideo();
	case 'getUserAvator':
		getUserAvator();
	case 'getDockerUrl':
		getDockerUrl();
	default:
		returnInfo(MY_ERROR['DATA_ERROR']);
}
?>
