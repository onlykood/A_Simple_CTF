/*判断是否进行了邮件传输情况*/
function mailSendCheck()
{
	debugLog("load_reg run-->");
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
				$('#verify_regone').hide();
				$('#verify_regtwo').show();
			}
			else{
				$('#verify_regone').show();
				$('#verify_regtwo').hide();
			}
		},
		error:function(data){
			debugLog(data);
		}
	});
	return false;
}

function regVerifyCheck(){
	if(loggedin){
		Materialize.toast("您已登陆！",4000);
		window.location.href="account.html";
		return false;
	}
	$.ajax({
		type:'post',
		url:'ajax.php?m=getEmailVerify',
		data:{'token':token},
		dataType:'json',
		success:function(data){
			debugLog(data);
			errorCheck(data);
			if(data[1][0]=='0'){
				$('.no-verify').show();
				$('#no-captcha').attr('src','/ajax.php?m=getCaptcha&1');
				$('.on-verify').hide();
			}
			else{
				$('.on-verify').show();
				$('#on-captcha').attr('src','/ajax.php?m=getCaptcha&2');
				$('.no-verify').hide();
			}
		},
		error:function(data){
			debugLog(data);
		}
	});
	return false;
}

$('.no-verify').submit(function(){
	debugLog($("form").serializeArray());
	$.ajax({
		type:'post',
		url:'ajax.php?m=noVerifyRegister',
		data:$('form').serialize()+'&token='+token,
		dataType:'json',
		success:function(data){
			debugLog(data);
			if(errorCheck(data)){
				$("#no-captcha").click();
				return false;
			}
			Materialize.toast(data[0][1],2000,"",function(){window.location.href="./account.html"});
		},
		error:function(data){
			debugLog(data);
		}
	});
	$("#no-captcha").click();
	return false;
})

$(document).ready(function(){
	regVerifyCheck();
	mailSendCheck();
	$("#sendmail").click(function(){
		$.ajax({
			type:'post',
			url:'ajax.php?m=sendRegMail',
			data:{
				'captcha':$('#verify_captcha').val(),
				'email':$('#verify_email').val(),
				'username':$('#verify_username').val(),
				'token':token
			},
			dataType:'json',
			success: function(data) {
				debugLog(data);
				if(errorCheck(data)){
					$("#on-captcha").click();
					return false;
				}
				Materialize.toast(data[0][1], 4000);
				if(data[0][0]=='1'){
					mailSendCheck();
				}
			},
			error: function(data){
				debugLog(data);
			}
		});
		$("#on-captcha").click();
		return false;
	});

	$('#regaccount').click(function(){
		 if(!$( '[name="verify_agree"]' ).prop( 'checked' )){
			Materialize.toast("请同意注册协议！", 4000);
			return false;
		}	
		else{
			$('#regaccount').html('提交中...');
			$.ajax({
				type: 'post',
				url: 'ajax.php?m=register',
				data: {'password':$('#verify_password').val(),'repeat':$('#verify_repeat').val(),'regkey':$('#verify_regkey').val(),'token':token},
				dataType:'json',
				success: function(data){
					debugLog(data);
					if(errorCheck(data)){
						$('#regaccount').html('注册');
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
					$('#regaccount').html('注册');
				},
				error: function(data){
					debugLog(data);
				}
			});
			return false;
		}
	});
});
