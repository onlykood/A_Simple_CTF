var quesid;
function getQuestion(obj)
{
	debugLog(obj);
	quesid=obj.id;
	debugLog('click1');
	$.ajax({
		type: 'POST',
		url: 'ajax.php?m=getQuestion',
		data:{'id':quesid,'token':token},
		dataType:'json',
		success: function(data){
			debugLog(data);
			if(errorCheck(data)){
				return false;
			}
			debugLog(quesid);
			$("#ques_solve").html("Solves :"+data[1][2])
			$("#ques_score").html(data[1][3]);
			$("#ques_tit").html(data[1][0]);
			$('#ques_con').html(data[1][1]);
			$('#modal').modal('open');
		}
	});
	return false;
}
/*function getQ(id)
{
	quesid=id;
	debugLog('click');
	$.ajax({
		type: 'POST',
		url: 'ajax.php?m=getQuestion',
		data:{'id':quesid,'token':token},
		dataType:'json',
		success: function(data){
			debugLog(data);
			if(errorCheck(data)){
				return false;
			}
			debugLog(quesid);
			$("#ques_solve").html("Solves :"+data[1][2])
			$("#ques_score").html(data[1][3]);
			$("#ques_tit").html(data[1][0]);
			$('#ques_con').html(data[1][1]);
			$('#modal').modal('open');
		}
	});
	return false;
}*/
function getQuestionSolves()
{
	$.ajax({
		type: 'POST',
		url: 'ajax.php?m=getQuestionSolves',
		data:{'id':quesid,'token':token},
		dataType:'json',
		success: function(data){
			debugLog(data);
			if(errorCheck(data)){
				return false;
			}
			var table = $( '<table>' );
			var thead = $( '<thead>' );
			$( '<th><b>' ).text( 'No.' ).appendTo( thead );
			$( '<th><b>' ).text( 'User' ).appendTo( thead );
			$( '<th><b>' ).text( 'Date' ).appendTo( thead );
			thead.appendTo(table);
			$.each(
				data[1],
				function(num,content){
					var trow = $( '<tr>' );
					$( '<td>' ).text( num+1 ).appendTo( trow );
					$( '<td>' ).text( content[0] ).appendTo( trow );
					$( '<td>' ).text( getMyDate(content[1]) ).appendTo( trow );
					trow.appendTo( table );
				}
			);
			$( '#solves' ).html( table.html() );
		},
		error: function(data)	{
			debugLog(data);
		}
	});
	return false;	
}
function getQuestions()
{
	$.ajax({
		type: 'post',
		url: 'ajax.php?m=getQuestions',
		dataType:'json',
		data:{"token":token},
		success: function(data) {
			debugLog(data);
			if(errorCheck(data)){
				$('.questions').html('<h2 class="center">Where there is a will, there is a way.</h2>');
				return false;
			}
			var ques="",type=-1;
			$.each(
				data[1],
				function(num,content){
					if(type!=content[1]){
						type=content[1];
						if(num){
							ques+='<br><br></div>';
						}
						ques+='<div style="height:auto;margin:0 auto; overflow:hidden" class="row questionsType"><h3 class="header">'+quesType[content[1]]+'</h3><hr>';
					}
					if(content[4]){
						ques+='<button class="green lighten-2 btn-large col s6 m4 l3" id="'+content[0]+'" onclick="getQuestion(this)"><div style="min-height:30%">'+content[2]+'</div><span>'+content[3]+'</span></button>';
					}
					else{
						ques+='<button class="btn-large col s6 m4 l3" id="'+content[0]+'" onclick="getQuestion(this)"><div style="min-height:30%">'+content[2]+'</div><span>'+content[3]+'</span></button>';
					}
				}
			);
			if(ques!=''){
				ques+='<br><br></div>';
			}
			else{
				ques='<h2 class="center">No Questions, Please Contact The Administrator.</h2>';
			}
			$( '.questions' ).html( ques );
		},
		error: function(data)	{
			debugLog(data);
		}
	});
	return false;
}
function closeModal(){
	$('#modal').modal('close');
	return false;
}
$("#ques_con").on("click","#destroyDocker",function(){
	var tmp=this;
	debugLog(this);
	$.ajax({
		type:'POST',
		url:'ajax.php?m=destroyDocker',
		data:{'token':token},
		dataType:'json',
		success:function(data){
			debugLog(data);
			if(errorCheck(data)){
				return false;
			}
			Materialize.toast("销毁成功");
			$('#modal').modal('close');
		},
		error:function(data){
			debugLog(data);
		}
	});
	return false;
});
$("#ques_con").on("click","#dockerButton",function(){
	var tmp=this;
	debugLog(this);
	tmp.text="docker 下发中...";
	$.ajax({
		type:"POST",
		url:"ajax.php?m=getDockerUrl",
		data:{'token':token},
		dataType:'json',
		success:function(data){
			debugLog(data);
			if(errorCheck(data)){
				tmp.text="下发docker";
				return false;
			}
			Materialize.toast("下发成功，请点击按钮进入 Challenge , 有效期：1h",4000);
			tmp.text="点击进入";
			tmp.href=data[0][1];
			tmp.id="dockerUrl";
			tmp.target="_blank";
		},
		error: function(data){
			debugLog(data);
		}

	});
	return false;
});

$(document).ready(function(){
	$('.modal').modal({
		complete: function(){ 
			$("#ques_solve").html("")
			$("#ques_score").html("");
			$("#ques_tit").html("");
			$('#ques_con').html("");
			$('#solves').html("");
			$('[name="flag"]').val("");
			$('ul.tabs').tabs('select_tab', 'mod1');
		}
	});
	getQuestions();
	/*getQ(1);*/
	$( 'form' ).submit(function(){
		$.ajax({
			type: 'POST',
			url: 'ajax.php?m=flagSubmit',
			data: {'flag':$( '[name="flag"]' ).prop( 'value' ),'token':token},
			dataType:'json',
			success: function( data ){
				debugLog(data);
				$('#modal').modal('close');
				if(errorCheck(data)){
					return false;
				}
				Materialize.toast(data[0][1],4000);
				if(data[0][0]=='-1'){
					setTimeout(
						function(){
							window.location.href="./account.html";
						},
						3000
					);
				}
				if(data[0][0]=='1'){
					$('[id="'+quesid+'"]').addClass("green lighten-2");
				}
			},
			error: function(data){
				debugLog(data);
			}
		});
		return false;
	});
});
