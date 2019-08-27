/*Globals*/
var DEBUG=false;
var loggedin = false;
var mq=false;

var token;
var name = '';
var said = '';
var mail = '';
var nickname='';
var quesType=['Web','Reverse','Pwn','Misc','Crypto','Stega','Ppc'];

function debugLog(data)
{
    if(DEBUG){
        console.log(data);
    }
    return false;
}
function loadSession()
{
    debugLog("load Session");
    $.ajax({
        url: 'ajax.php?m=getSession',
        dataType: 'json',
        async:false,/*这里必须要设置同步，不然会造成不必要的麻烦，例如本来登陆的，但是访问页面却出现登陆界面，jq有警告也没办法了╮(╯▽╰)╭*/
        success:function(data){
            debugLog('========');
            debugLog(data);
            if(errorCheck(data)){
                debugLog("----");
                logout();
                return false;
            }
            if(data[1][0]){
                login(data[1]);
            }
            else{
                logout();
            }
            token=data[1][1];
        },
        error:function(data){
            debugLog(data);
        }
    });
}

function loadThemeColor(){
    var color=sessionStorage.getItem('themeColor');
    if(color==null){
        sessionStorage.setItem('themeColor', 'grey darken-4');
        color='grey darken-4';
    }
    $('nav').attr('class',color);
    return false;
}

function setThemeColor(color){
    sessionStorage.setItem('themeColor',color);
    loadThemeColor();
    return false;
}
function login(data){
    name = data[2];
    mail = data[4];
    said = data[5];
    nickname=data[3];
    loggedin = true;
    $('.head-menu').append('<li><a href="#" id="logout">退出</a></li>')
}

function logout(){
    name = 'unknow';
    mail = 'unknow';
    said = 'unknow';
    nickname='unknow';
    loggedin = false;
}

function getCaptcha()
{
    //.attr('src','ajax.php?m=getCaptcha'+String(Math.random()).slice(2));
    $("#capimg").attr('src','ajax.php?m=getCaptcha&'+String(Math.random()).slice(2));
    return false;
}

function getMyDate(str){
    /*补上3个0 转换为数字*/
    str+='000';
    str-=0;
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

/*补0操作*/
function getzf(num){  
    if(parseInt(num) < 10){  
        num = '0'+num;  
    }  
    return num;  
}

function errorCheck(data){
    if(data[0][0]=='0'){
        Materialize.toast(data[0][1], 4000);
        return 1;
    }
    if(data[0][0]=='-1'){
        window.location.href='./easyInstall.php';
        return 1;
    }
    return 0;
}

function loadTitle(){
    title=sessionStorage.getItem('title');
    pageTitle=sessionStorage.getItem('pageTitle');
    debugLog(title+' '+pageTitle);
    if(title==null && pageTitle==null){
        debugLog("触发loadTitle");
        $.ajax({
            type:'post',
            url: 'ajax.php?m=getTitle',
            dataType: 'json',
            success:function(data){
                debugLog(data);
                if(errorCheck(data)){
                    return false;
                }
                document.title = data[1][0];
                $('.page-title').text(data[1][1]);
                sessionStorage.setItem('title',data[1][0]);
                sessionStorage.setItem('pageTitle',data[1][1]);
            },
            error:function(data){
                debugLog(data);
            }
        });
    }
    else{
        document.title = title;
        $('.page-title').text(pageTitle);
    }
    return false;
}

function textToImg(img,uname) {
    if(img!=''){
        return img;
    }
    var name = uname.charAt(0);
    var fontSize = 60;
    var fontWeight = 'bold';
    var canvas = document.getElementById('headImg');
    var img1 = document.getElementById('headImg');
    canvas.width = 120;
    canvas.height = 120;
    var context = canvas.getContext('2d');
    context.fillStyle = '#F7F7F9';
    context.fillRect(0, 0, canvas.width, canvas.height);
    context.fillStyle = '#605CA8';
    context.font = fontWeight + ' ' + fontSize + 'px sans-serif';
    context.textAlign = 'center';
    context.textBaseline = "middle";
    context.fillText(name, fontSize, fontSize);
    return canvas.toDataURL('image/png');
};

$(document).ready(function(){
    loadTitle();
    loadThemeColor();
    loadSession();
    $(".button-collapse").sideNav();
    $('.modal').modal();
    DEBUG ? null : console.clear();
    console.log("Welcome to my simple ctf, have a good time.");
    $('#logout').click(function(){
        if(!loggedin){
            Materialize.toast("你还没有登录!", 4000);
            loadAccount();
            return false;
        }
        $.ajax({
            type: 'POST',
            url: 'ajax.php?m=logout',
            data:{'token':token},
            dataType: 'json',
            success: function(data) {
                Materialize.toast(data[0][1], 1000,'',function(){location.reload();});
            },
            error:function(data){
                debugLog(data);
            }
        });
        return false;
    });
});