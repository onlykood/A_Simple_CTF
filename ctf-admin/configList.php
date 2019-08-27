<?php
    require_once("init.php");
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>公告列表</title>
    <script src="//cdn.bootcss.com/jquery/3.3.1/jquery.min.js"></script>
</head>
<body>
    <div class="x-nav">
        <a class="layui-btn layui-btn-small" style="line-height:1.6em;margin-top:3px;float:right" href="javascript:location.replace(location.href);" title="刷新"><i class="layui-icon" style="line-height:30px">刷新页面</i></a>
    </div>
    <form method="post" action='./ajax.php?m=updateConfig'>
        <strong>此处Demo版本，未作任何过滤操作，请小心使用，避免出现特殊符号等之类的</strong>
        <br>
        站点开放：<select name="website_open">               <option id="website_open"  type="option" value="0"></option><option value="1">开放</option><option value="1">关闭</option></select>
        那啥开放：<select name="ctf_open">                   <option id="ctf_open"      type="option" value="0"></option><option value="1">开放</option><option value="1">关闭</option></select>
        注册开放：<select name="reg_open">                   <option id="reg_open"      type="option" value="0"></option><option value="1">开放</option><option value="1">关闭</option></select>
        登陆开放：<select name="login_open">                 <option id="login_open"    type="option" value="0"></option><option value="1">开放</option><option value="1">关闭</option></select>
        提交开放：<select name="sub_open">                   <option id="sub_open"      type="option" value="0"></option><option value="1">开放</option><option value="1">关闭</option></select>
        <br><hr>
        动态积分：<select name="dynamic_score_open">         <option id="dynamic_score_open"     type="option" value="0"></option><option value="1">开放</option><option value="1">关闭</option></select>
        一血开放：<select name="one_blood_open">             <option id="one_blood_open"         type="option" value="0"></option><option value="1">开放</option><option value="1">关闭</option></select>
        邮箱验证：<select name="email_verify_open">          <option id="email_verify_open"      type="option" value="0"></option><option value="1">开放</option><option value="1">关闭</option></select>
        依赖隐藏：<select name="challenge_depend_hide">      <option id="challenge_depend_hide"  type="option" value="0"></option><option value="1">开放</option><option value="1">关闭</option></select>
        排行缓存：<select name="cache_open">                 <option id="cache_open"             type="option" value="0"></option><option value="1">开放</option><option value="1">关闭</option></select>
        <br><hr>
        一血配置：<input type="text" id="blood_score"            style="width:30%" name="blood_score" />                    * eg: [10,5,3,1]        <br>
        展示数量：<input type="text" id="recent_solve_show_num"  style="width:30%" name="recent_solve_show_num" />          * 首页最近解答展示数量    <br>
        站点名称：<input type="text" id="ctf_name"               style="width:30%" name="ctf_name" />                       * title 内容             <br>
        菜单名称：<input type="text" id="ctf_organizer"          style="width:30%" name="ctf_organizer" />                  * 菜单栏左侧显示内容      <br>

        <br><hr>
        docker 存活时间：<input type="text" id="docker_exist_time" style="width:30%" name="docker_exist_time" /><br>
        docker 密码令牌：<input type="text" id="get_docker_token"  style="width:30%" name="get_docker_token" /><br>
        docker 服务地址：<input type="text" id="docker_server"     style="width:30%" name="docker_server" /><br>

        <br><hr>
        邮箱姓名：<input type="text"       id="email_username"           style="width:30%" name="email_username" /><br>
        邮箱密码：<input type="text"       id="email_password"           style="width:30%" name="email_password" /><br>
        超级密码：<input type="text"       id="super_password"           style="width:30%" name="super_password" /><br>
        动态flag头格式：<input type="text" id="dynamic_flag_head_fmt"    style="width:30%" name="dynamic_flag_head_fmt" /><br>

        <br><hr>
        <input type="submit" value="提交" onclick="submit()" />
    </form>
</body>
<script type="text/javascript">
function submit(){
    return false;
}

function loadConfigs(){
    $.ajax({
        type:'POST',
        url:'./ajax.php?m=getConfigs',
        dataType:'json',
        success:function(data){
            console.log(data);
            //data=data[1];
            $.each(data[1],function(num,content){
                tmp=$('#'+content['name'])
                tmp.val(content['value']);
                if(tmp.attr('type')=='option'){
                    state=content['value']==1?'开放':'关闭';
                    tmp.html(state);
                }
            });
        },
        error:function(data){
            console.log(data);
        }
    });
    return false;
}

$(document).ready(function(){
    loadConfigs();
});
loadConfigs();
</script>
</html>
