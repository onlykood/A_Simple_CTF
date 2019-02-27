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
				$('#regone').hide();
				$('#regtwo').show();
			}
			else{
				$('#regone').show();
				$('#regtwo').hide();
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
			url:'ajax.php?m=sendRegMail',
			data:{'captcha':$('#captcha').val(),'email':$('#email').val(),'username':$('#username').val(),'token':token},
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

	$('#regaccount').click(function(){
		 if(!$( '[name="agree"]' ).prop( 'checked' ))	{
			Materialize.toast("请同意注册协议！", 4000);
			return false;
		}	
		else{
			$('#regaccount').html('提交中...');
			$.ajax({
				type: 'post',
				url: 'ajax.php?m=register',
				data: {'password':$('#password').val(),'repeat':$('#repeat').val(),'regkey':$('#regkey').val(),'token':token},
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
							window.location.href="./createaccount.html";
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
