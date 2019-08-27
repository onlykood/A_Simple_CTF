/*判断是否进行了邮件传输情况*/
function mailSendCheck()
{
    debugLog("load_reset run-->");
    if(loggedin){
        Materialize.toast("您已登陆！", 4000);
        window.location.href="account.html";
        return false;
    }
    $.ajax({
        type:'post',
        url:'ajax.php?m=mailSendCheck',
        data:"token="+token,
        dataType:'json',
        success:function(data){
            debugLog(data);
            errorCheck(data);
            if(data[0][0]=="2"){
                $('#resetone').hide();
                $('#resettwo').show();
            }
            else{
                $('#resetone').show();
                $('#resettwo').hide();
            }
        },
        error:function(data){
            debugLog(data);
        }
    });
    return false;
}

$(document).ready(function(){

    mailSendCheck();
    getCaptcha();
    $("#sendmail").click(function(){
        $.ajax({
            type:'post',
            url:'ajax.php?m=sendResetMail',
            data:{'captcha':$('#captcha').val(),'email':$('#email').val(),'token':token},
            dataType:'json',
            success: function(data) {
                debugLog(data);
                if(errorCheck(data)){
                    getCaptcha();
                    return false;
                }
                Materialize.toast(data[0][1], 4000);
                getCaptcha();
                if(data[0][0]=='1'){
                    mailSendCheck();
                }
            },
            error: function(data){
                debugLog(data);
            }
        });
        return false;
    });

    $('#resetaccount').click(function(){
        $('#resetaccount').html('提交中...');
        $.ajax({
            type: 'post',
            url: 'ajax.php?m=resetPassword',
            data: {'password':$('#password').val(),'repeat':$('#repeat').val(),'resetkey':$('#resetkey').val(),'token':token},
            dataType:'json',
            success: function(data){
                debugLog(data);
                if(errorCheck(data)){
                    return false;
                }
                Materialize.toast(data[0][1],4000);
                if(data[0][0]=="1"){
                    setTimeout(function(){
                        window.location.href="./account.html";
                    },2000);
                }
                if(data[0][0]=='2'){
                    setTimeout(function(){
                        window.location.href="./register.html";
                    },2000);
                }
                $('#resetaccount').html('重置密码');
            },
            error: function(data){
                debugLog(data);
            }
        });
        return false;
    })
});
