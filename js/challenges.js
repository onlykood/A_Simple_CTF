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
function makeques(id,name,solves,type,score){
	//var tmpHtml='<li><div class="collapsible-header">'+name+' - '+solves+' Solves<div class="right chip">'+type+'</div><div class="right chip">'+score+'</div></div><div class="collapsible-body" style="padding: 0;padding-top:2rem">';
	var tmpHtml='<li><div id="qq'+id+'" class="collapsible-header" onclick="getQ('+id+')">'+name+'<div class="right chip">'+type+'</div><div class="right chip">'+score+'</div></div><div class="collapsible-body" style="padding: 0;padding-top:2rem">';
	tmpHtml+='<div id="q'+id+'" class="container"><div class="progress"><div class="indeterminate"></div></div></div>';
	tmpHtml+='<form class="row" style="margin-bottom: 0" onsubmit="subFlag(this);return false;"><div class="col s6 offset-s2 center">';
	tmpHtml+='<input type="text" placeholder="Flag" name="flag"/>';
	tmpHtml+='</div><div class="col s3"><button class="btn waves-effect waves-light" type="submit">Submit</button></div></form></div></li>';
	return tmpHtml;
}
function subFlag(obj){
	debugLog(obj);
	$.ajax({
		type: 'POST',
		url: 'ajax.php?m=flagSubmit',
		data: {'flag':obj.flag.value,'token':token},
		dataType:'json',
		success: function( data ){
			debugLog(data);
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
				debugLog($('#qq'+quesid).html());
				$('#qq'+quesid).addClass("green lighten-2");
			}
		},
		error: function(data){
			debugLog(data);
		}
	});
	return false;
}
function getQ(id)
{
	quesid=id;
	//debugLog($('#q'+id).html());
	if($('#q'+id).html()!=='<div class="progress"><div class="indeterminate"></div></div>'){
		return false;
	}
	$.ajax({
		type: 'POST',
		url: 'ajax.php?m=getQuestion',
		data:{'id':id,'token':token},
		dataType:'json',
		success: function(data){
			debugLog(data);
			if(errorCheck(data)){
				return false;
			}
			debugLog(id);
			$('#q'+id).html(data[1][1]);
		}
	});
	return false;
}
/*导航栏横式*/
function getQuess(type)
{
	$.ajax({
		type:'post',
		url:'ajax.php?m=getQuestions',
		dataType:'json',
		data:{'token':token,'type':type},
		success:function(data){
			debugLog(data);
			if(errorCheck(data)){
				$('.questions').html('<h2 class="center">Where there is a will, there is a way.</h2>');
				return false;
			}
			var ques="";
			$.each(
				data[1],
				function(num,content){
					if(content[4]==='1'){
						ques+=makeques(content[0],content[2],0,quesType[content[1]],content[3],content[5]);
					}
					else if(content[4]==='lock'){
						ques+=makeques(content[0],content[2],0,quesType[content[1]],content[3],content[5]);//ques += '<button class="grey lighten-1 btn-large col s6 m4 l3" id="' + content[0] + '" onclick="getQuestion(this)"><div style="min-height:30%">' + content[2] + '</div><span>' + content[3] + '</span></button>';
					}
					else if (content[4] === null){
						ques+=makeques(content[0],content[2],0,quesType[content[1]],content[3],content[5]);//ques+='<button class="btn-large col s6 m4 l3" id="'+content[0]+'" onclick="getQuestion(this)"><div style="min-height:30%">'+content[2]+'</div><span>'+content[3]+'</span></button>';
					}
					else{
						ques+='';
					}
				}
			);
			if(ques==''){
				ques='<h2 class="center">No Questions, Please Contact The Administrator.</h2>';
			}
			$( '#quesList' ).html( ques );
			$('#mques').show();
		},
		error:function(data){
			debugLog(data);
		}
	});
	return false;
}
/**按钮式*/
function getQuestions()
{
	$.ajax({
		type: 'post',
		url: 'ajax.php?m=getQuestions',
		dataType:'json',
		data:{"token":token,'type':7},
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
					if(content[4]==='1'){
						ques+='<button class="green lighten-2 btn-large col s6 m4 l3" id="'+content[0]+'" onclick="getQuestion(this)"><div style="min-height:30%">'+content[2]+'</div><span>'+content[3]+'</span></button>';
					}
					else if(content[4]==='lock'){
						ques += '<button class="grey lighten-1 btn-large col s6 m4 l3" id="' + content[0] + '" onclick="getQuestion(this)"><div style="min-height:30%">' + content[2] + '</div><span>' + content[3] + '</span></button>';
					}
					else if (content[4] === null){
						ques+='<button class="btn-large col s6 m4 l3" id="'+content[0]+'" onclick="getQuestion(this)"><div style="min-height:30%">'+content[2]+'</div><span>'+content[3]+'</span></button>';
					}
					else{
						ques+='';
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
	$('#bques').show();
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

//var quesType=['Web','Reverse','Pwn','Misc','Crypto','Stega','Ppc'];
/*点击切换*/
$("#o_all").click(function(){getQuess(7);});
$("#o_web").click(function(){getQuess(0);});
$("#o_re").click(function(){getQuess(1);});
$("#o_pwn").click(function(){getQuess(2);});
$("#o_misc").click(function(){getQuess(3);});
$("#o_crypto").click(function(){getQuess(4);});

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
	if(mq)
		getQuess(7);
	else
		getQuestions();
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
