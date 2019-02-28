/*Globals*/
var DEBUG=true;
var loggedin = false;

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

function login(data){
	name = data[2];
	mail = data[4];
	said = data[5];
	nickname=data[3];
	loggedin = true;
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
	$("#capimg").attr('src','ajax.php?m=getCaptcha'); 
	return false;
}

function getMyDate(str){
	//补上3个0 转换为数字
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

//补0操作
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

$(document).ready(function(){
	loadSession();
	$(".button-collapse").sideNav();
	$('.modal').modal();
	console.clear();
	console.log("Welcome to my simple ctf, have a good time.");
});