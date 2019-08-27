<?php
require_once('init.php' );
require_once('functions.php');

if( !isset( $_GET['m'] ) || !is_string( $_GET['m'] ) )
{
    die("Get out! You are not supposed to be here.");
}

if( !defined( 'INITIALIZED' ) ) #定义在init.php，没啥用
{
    exit();
}

//检测数据库连接情况
$link=Database::getConnection();
if (mysqli_connect_errno()) {
    return_info(SQL_CONNECT_FAIL);
}

#检测session是否存在以及是否超时失效
//$_SESSION['reg_time']<time()

switch( $_GET['m'] )    {
    case 'getUserList':
        postCheck('type');
        getUsersList($_POST['type']);
    case 'modUsersStatus':
        postCheck('status','ids');
        modUsersStatus($_POST['status'],$_POST['ids']);
    case 'modUserInfo':
        postCheck('userid','userkey','name','nickname','email','said','password');
        modUserInfo($_POST['userid'],$_POST['userkey'],$_POST['name'],$_POST['nickname'],$_POST['email'],$_POST['said'],$_POST['password']);
    case 'modQuesInfo':
        postCheck('quesid','type','title','score','content','flag',"dockerid",'depends');
        modQuesInfo($_POST['quesid'],$_POST['type'],$_POST['title'],$_POST['score'],$_POST['content'],$_POST['flag'],$_POST['dockerid'],$_POST['depends']);
    case 'userAdd':
        postCheck('username','email','password');
        userAdd($_POST['username'],$_POST['email'],$_POST['password']);
    case 'quesAdd':
        postCheck('title','type','score','content','dockerid');
        quesAdd($_POST['title'],$_POST['type'],$_POST['score'],$_POST['content'],$_POST['flag'],$_POST['dockerid']);
    case 'getSubmitsList':
        getSubmitsList();
    case 'getQuesList':
        getQuesList();
    case 'getInfoList':
        postCheck('type');
        getInfoList($_POST['type']);
    case 'modStatus':
        postCheck('type','operate','ids');
        modStatus($_POST['type'],$_POST['operate'],$_POST['ids']);
    case 'noticeManage':
        postCheck('operate','id','content');
        noticeManage($_POST['operate'],$_POST['id'],$_POST['content']);
    case 'updateConfig':
        postCheck('website_open','ctf_open','reg_open','login_open','sub_open','dynamic_score_open','one_blood_open','email_verify_open','challenge_depend_hide','cache_open','blood_score','recent_solve_show_num','ctf_name','ctf_organizer','docker_exist_time','get_docker_token','docker_server','email_username','email_password','super_password','dynamic_flag_head_fmt');
        updateConfig($_POST['website_open'],$_POST['ctf_open'],$_POST['reg_open'],$_POST['login_open'],$_POST['sub_open'],$_POST['dynamic_score_open'],$_POST['one_blood_open'],$_POST['email_verify_open'],$_POST['challenge_depend_hide'],$_POST['cache_open'],$_POST['blood_score'],$_POST['recent_solve_show_num'],$_POST['ctf_name'],$_POST['ctf_organizer'],$_POST['docker_exist_time'],$_POST['get_docker_token'],$_POST['docker_server'],$_POST['email_username'],$_POST['email_password'],$_POST['super_password'],$_POST['dynamic_flag_head_fmt']);
    case 'getConfigs':
        getConfigs();
    default:returnInfo(DATA_ERROR);
}
?>













