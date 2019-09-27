<?php
    require_once("init.php");
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>题目信息列表</title>
        <meta name="renderer" content="webkit">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon" />
        <link rel="stylesheet" href="./css/font.css">
        <link rel="stylesheet" href="./css/xadmin.css">
        <script type="text/javascript" src="https://cdn.bootcss.com/jquery/3.2.1/jquery.min.js"></script>
        <script type="text/javascript" src="./lib/layui/layui.js" charset="utf-8"></script>
        <script type="text/javascript" src="./js/xadmin.js"></script>
    </head>
<body>
    <div class="x-nav">
        <a class="layui-btn layui-btn-small" style="line-height:1.6em;margin-top:3px;float:right" href="javascript:location.replace(location.href);" title="刷新"><i class="layui-icon" style="line-height:30px">刷新页面</i></a>
    </div>

            <div class="x-body">
              <table class="layui-table">
                <thead>
                  <tr>
                    <th>配置名</th>
                    <th>描述</th>
                    <th>配置内容</th>
                    <th>操作</th></tr>
                </thead>
            <tbody id='configs-list'>
<tr>
  <td>平台类型</td>
  <td>平台类型，是日常开放，还是比赛开放</td>
  <td id='ctf_type' ops='sbt'> </td>  <td> </td></tr>
<tr>
  <td>比赛开始时间</td>
  <td>比赛开放的比赛开始时间</td>
  <td><input class="layui-input" placeholder="开始时间" name="ctf_start_time" id="ctf_start_time" ops="time"/></td>
  <td><button class="layui-btn" onclick="modConfig('time','ctf_start_time')">修改</button></td>
</tr>
<tr>
  <td>比赛结束时间</td>
  <td>比赛开放的比赛结束时间</td>
  <td><input class="layui-input" placeholder="结束时间" name="ctf_end_time" id="ctf_end_time" ops="time"/></td>
  <td><button class="layui-btn" onclick="modConfig('time','ctf_end_time')">修改</button></td>
</tr>
<tr>  <td>站点开放</td>  <td>是否开放站点</td>       <td id='website_open' ops='bt'></td>  <td> </td></tr>
<tr>  <td>比赛开放</td>  <td>是否开放ctf</td>        <td id='ctf_open' ops='bt'> </td>  <td> </td></tr>
<tr>  <td>注册开放</td>  <td>是否开放注册</td>       <td id='reg_open' ops='bt'> </td>  <td> </td></tr>
<tr>  <td>登陆开放</td>  <td>是否允许登陆</td>       <td id='login_open' ops='bt'> </td>  <td> </td></tr>
<tr>  <td>提交开放</td>  <td>是否允许答题</td>       <td id='sub_open' ops='bt'> </td>  <td> </td></tr>
<tr>  <td>动态积分</td>  <td>是否使用动态积分</td>    <td id='dynamic_score_open' ops='bt'> </td>  <td> </td></tr>
<tr>  <td>一血开放</td>  <td>是否开启前几血加分</td>  <td id='one_blood_open' ops='bt'> </td>  <td> </td></tr>
<tr>
  <td>邮箱验证</td>
  <td>是否开启邮箱验证注册</td>
  <td id='email_verify_open' ops='bt'> </td>  <td> </td></tr>
<tr>
  <td>依赖隐藏</td>
  <td>存在依赖的赛题是否隐藏</td>
  <td id='challenge_depend_hide' ops='bt'> </td>  <td> </td></tr>
<tr>
  <td>排行缓存</td>
  <td>是否启用缓存</td>
  <td id='cache_open' ops='bt'> </td>  <td> </td></tr>
<tr>
<tr>
  <td>一血配置</td>
  <td>前几血加分数值</td>
  <td><input id='blood_score' ops='ipt' type="text" class="layui-input"/> </td>
  <td><button class="layui-btn" onclick="modConfig('ipt','blood_score')">修改</button></td>
</tr>
<tr>
  <td>展示数量</td>
  <td>首页显示最近题数量</td>
  <td><input id='recent_solve_show_num' ops='ipt' type="text" class="layui-input"/> </td>
  <td><button class="layui-btn" onclick="modConfig('ipt','recent_solve_show_num')">修改</button></td>
</tr>
<tr>
  <td>站点名称</td>
  <td>网站标题</td>
  <td><input id='ctf_name' ops='ipt' type="text" class="layui-input"/> </td>
  <td><button class="layui-btn" onclick="modConfig('ipt','ctf_name')">修改</button></td>
</tr>
<tr>
  <td>菜单名称</td>
  <td>菜单栏左侧标识</td>
  <td><input id='ctf_organizer' ops='ipt' type="text" class="layui-input"/> </td>
  <td><button class="layui-btn" onclick="modConfig('ipt','ctf_organizer')">修改</button></td>
</tr>
<tr>
  <td>docker 存活时间</td>
  <td>docker的生存时间</td>
  <td><input id='docker_exist_time' ops='ipt' type="text" class="layui-input"/> </td>
  <td><button class="layui-btn" onclick="modConfig('ipt','docker_exist_time')">修改</button></td>
</tr>
<tr>
  <td>docker 密码令牌</td>
  <td>与docker服务器交互的令牌</td>
  <td><input id='get_docker_token' ops='ipt' type="text" class="layui-input"/> </td>
  <td><button class="layui-btn" onclick="modConfig('ipt','get_docker_token')">修改</button></td>
</tr>
<tr>
  <td>docker 服务地址</td>
  <td>docker服务器url</td>
  <td><input id='docker_server' ops='ipt' type="text" class="layui-input"/> </td>
  <td><button class="layui-btn" onclick="modConfig('ipt','docker_server')">修改</button></td>
</tr>
<tr>
  <td>邮箱姓名</td>
  <td>邮件用户名</td>
  <td><input id='email_username' ops='ipt' type="text" class="layui-input"/> </td>
  <td><button class="layui-btn" onclick="modConfig('ipt','email_username')">修改</button></td>
</tr>
<tr>
  <td>邮箱密码</td>
  <td>邮件密码</td>
  <td><input id='email_password' ops='ipt' type="text" class="layui-input"/> </td>
  <td><button class="layui-btn" onclick="modConfig('ipt','email_password')">修改</button></td>
</tr>
<tr>
  <td>超级密码</td>
  <td>超级密码,一般用于测试使用</td>
  <td><input id='super_password' ops='ipt' type="text" class="layui-input"/> </td>
  <td><button class="layui-btn" onclick="modConfig('ipt','super_password')">修改</button></td>
</tr>
<tr>
  <td>动态flag头格式</td>
  <td>动态flag的flag头格式</td>
  <td><input id='dynamic_flag_head_fmt' ops='ipt' type="text" class="layui-input"/> </td>
  <td><button class="layui-btn" onclick="modConfig('ipt','dynamic_flag_head_fmt')">修改</button></td>
</tr>

            </tbody>
            </table>
        </div>
</body>
<script type="text/javascript">
layui.use('laydate', function(){
  var laydate = layui.laydate;
  
  //执行一个laydate实例
  laydate.render({
    elem: '#ctf_start_time' //指定元素
    ,type:'datetime'
  });

  //执行一个laydate实例
  laydate.render({
    elem: '#ctf_end_time' //指定元素
    ,type:'datetime'
  });
});
function unixtime(strtime=false){ //不传入日期默认今日
    strtime = strtime.replace(/-/g,'/')  //解决低版本解释new Date('yyyy-mm-dd')这个对象出现NaN
    if(strtime){
        var date = new Date(strtime);
    }else{
        var date = new Date();
    }
    time = date.getTime();   //会精确到毫秒---长度为13位
    //time2 = date.valueOf(); //会精确到毫秒---长度为13位
    //time = Date.parse(date); //只能精确到秒，毫秒将用0来代替---长度为10位
    return (time+'').substring(0, 10);
}
/*对所有选中部分进行操作*/
function modConfig(type,name){
  console.log(type+'  '+name);
    //layer.confirm('确认要'+obj.name+'选中的所有行吗?',function(index){
    if(type=='bt' || type=='sbt'){
        value=1-$('#'+name+' >span').attr('val');
        if(type=='bt'){
          if($('#'+name)[0].innerText=='开启'){
            $('#'+name)[0].innerHTML='<span val="0" class="layui-btn layui-btn-danger layui-btn-mini" onclick="modConfig(\'bt\',\''+name+'\')">关闭</span>';
          }
          else{
            $('#'+name)[0].innerHTML='<span val="0" class="layui-btn layui-btn-normal layui-btn-mini" onclick="modConfig(\'bt\',\''+name+'\')">开启</span>';
          }
        }
        else{
          if($('#'+name)[0].innerText=='日常开放'){
            $('#'+name)[0].innerHTML='<span val="0" class="layui-btn layui-btn-danger layui-btn-mini" onclick="modConfig(\'sbt\',\''+name+'\')">竞赛开放</span>';
          }
          else{
            $('#'+name)[0].innerHTML='<span val="0" class="layui-btn layui-btn-normal layui-btn-mini" onclick="modConfig(\'sbt\',\''+name+'\')">日常开放</span>';
          }
        }
    }
    else if (type=='ipt'){
        value=$('#'+name).val();
    }
    else if(type=='time'){
        value=unixtime($('#'+name).val());
    }
    else{
        layer.msg('error',{icon:0});
    }
    $.ajax({
       url:'ajax.php?m=updateConfig',
       type:'POST',
       dataType:'json',
       data:{'name':name,'value':value},
       success:function(data){
           debugLog(data);
           if(errorCheck(data)){
               return false;
           }
           layer.msg('操作成功',{icon:1,time:800});//,function(){location.replace(location.href);});
       }
    })
}

function getMyDate(str){
    var getzf=function(num){
        if(parseInt(num) < 10){  
          num = '0'+num;  
      }
      return num; 
    }
    /*补上3个0 转换为数字*/
    str*=1000;
    var oDate = new Date(str),  
    oYear = oDate.getFullYear(),  
    oMonth = oDate.getMonth()+1,  
    oDay = oDate.getDate(),  
    oHour = oDate.getHours(),  
    oMin = oDate.getMinutes(),  
    oSen = oDate.getSeconds(),  
    oTime = oYear +'-'+ getzf(oMonth) +'-'+ getzf(oDay) +' '+ getzf(oHour) +':'+ getzf(oMin) +':'+getzf(oSen);//最后拼接时间  
    return oTime;  
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
                //console.log(content);
                //console.log(num);
                tmp=$('#'+content['name']);
                //console.log(tmp.html());
                state='';
                if(tmp.attr('ops')=='bt'){
                    state=content['value']==1?'<span val="1" class="layui-btn layui-btn-normal layui-btn-mini" onclick="modConfig(\'bt\',\''+content['name']+'\')">开启</span></td>':'<span val="0" class="layui-btn layui-btn-danger layui-btn-mini" onclick="modConfig(\'bt\',\''+content['name']+'\')">关闭</span></td>';
                    tmp.html(state);
                }
                else if(tmp.attr('ops')=='ipt'){
                    tmp.val(content['value']);
                }
                else if(tmp.attr('ops')=='time'){
                    tmp.val(getMyDate(content['value']));
                }
                else if(tmp.attr('ops')=='sbt'){
                    state=content['value']==1?'<span val="1" class="layui-btn layui-btn-normal layui-btn-mini" onclick="modConfig(\'sbt\',\''+content['name']+'\')">日常开放</span></td>':'<span val="0" class="layui-btn layui-btn-danger layui-btn-mini" onclick="modConfig(\'sbt\',\''+content['name']+'\')">比赛开放</span></td>';
                    tmp.html(state);
                }
                else{
                    console.log('error!');
                }
                //if(num>5)
                //return false;
                //if(tmp.attr('type')=='option'){
                //    state=content['value']==1?'开放':'关闭';
                //    tmp.html(state);
                //}
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
//loadConfigs();
</script>
</html>
