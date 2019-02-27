/*判断加载情况，分别执行登陆 未登录两种情况*/
function loadAccount(){
	debugLog(loggedin);
	if(loggedin){
		$('#lin').hide();
		$('#lout').show();
		$('#info').show();
		$("#setMenu").show();
		getUserSolves();
		/*用于显示base info区域的信息*/
		getUserAvator();
		//$('.headImgShow').attr('src',$.cookie('avatar'));
		$('#username').val(name);
		$('#nickname').val(nickname);
		$('#said').val(said);
		$('#email').val(mail);
	}	
	else{
		$('#lin').show();
		$('#lout').hide();
		$('#info').hide();
		$("#setMenu").hide();
		getCaptcha();
	}
	return false;
}

function loadVideo()
{
	$.ajax({
		type: 'POST',
		url: 'ajax.php?m=getVideo',
		dataType:'json',
		data:{"token":token},
		success: function(data) {
			debugLog(data);
			if(errorCheck(data)){
				return false;
			}
			$('#beta').html(data[1]);
		},
		error: function(data)	{
			debugLog(data);
		}
	});
	return false;	
}
function getUserSolveNum(data){
	debugLog('pic1');
	debugLog(data);
	$('#pic1').highcharts({
		credits:{enabled:false},
		exporting:{enabled:false},
		colors:['#FFFFFF','#4caf50', '#f44336'],
		chart: {
			spacing: [0, 0, 0, 0]
		},
		title: {
			text:''
		},
		series:[{
			type:'sunburst',
			data:data,
			allowDrillToNode:true,
			cursor:'pointer',
			dataLabels:{
            	formatter: function () {
            	    var shape = this.point.node.shapeArgs;
            	    var innerArcFraction = (shape.end - shape.start) / (2 * Math.PI);
            	    var perimeter = 2 * Math.PI * shape.innerR;
            	    var innerArcPixels = innerArcFraction * perimeter;
            	    if (innerArcPixels > 16) {
            	        return this.point.name;
            	    }
            	}
			},
			levels:[{
				level:2,
				colorByPoint:true,
				dataLabels:{
					rotationMode:'parallel'
				}
			}]
		}],
		tooltip: {
			useHTML: true,
			padding:10,
			style:{fontSize:'20px'},
			borderWidth: 2,
			formatter:function(){
				debugLog(this.point);
				if(this.point.id[0]=='0'){
					return this.point.name+':'+this.point.value;
				}
				if(this.point.id[0]=='1'){
					return this.point.nick+'('+(this.point.value*100/(data.length-17)).toFixed(0)+'%)';
				}
				if(this.point.id[0]=='2'){
					return this.point.name+'('+this.point.value+')';
				}
				return this.point.name;
			},
		}
	});
}
function getUserSolveType(data){
	debugLog('pic2');
	debugLog(data);
	$('#pic2').highcharts({
		credits:{enabled: false },
		exporting:{enabled:false},
		colors:['#37a2da','#32c5e9','#67e0e3','#9fe6b8','#ffdb5c','#ff9f7f','#fb7293','#e062ae','#e690d1','#e7bcf3','#9d96f5','#8378ea','#96bfff'],
		legend:{
			align:'right',
			verticalAlign:'top',
			layout:'vertical',
			floating:true,
		},
		chart: {
			//到时候调整一下
			spacing: [0, 0, 0, 0],
			style:{fontFamily:"Dosis, sans-serif"},
		},
		title: {
			floating: true,
			style:{fontSize:'40px'},
			text: '题目类型',
		},
		tooltip: {
			useHTML: true,
			padding:10,
			style:{fontSize:'20px'},
			borderWidth: 2,
			headerFormat:'<center>{point.key}</center>',
			pointFormat:'<b>{point.y}</b>({point.percentage:.1f}%)'
		},
		plotOptions: {
			pie: {
				allowPointSelect: true,
				cursor: 'pointer',
				dataLabels: {
					enabled: false
				},
				showInLegend: true,
				point: {
					events: {
						mouseOver: function(e) {
							chart.setTitle({
								text: e.target.name,
								style:{color:e.target.color}
							});
						},
						mouseOut:function(){
							setTimeout(function(){
								chart.setTitle({
									text:'题目类型',
									style:{color:'#333333'}
								});
							},500);
						}
					}
				},
			}
		},
		series: [{
			type: 'pie',
			innerSize: '60%',
			//name: '-',
			data: data
		}]
	},
	//设置title文字位置
	function(c) {
		var centerY = c.series[0].center[1],
		titleHeight = parseInt(c.title.styles.fontSize);
		c.setTitle({
			//莫名其妙要 /3 .。。 官方实例也是要/3才能正中 debug半天..
			y: centerY + titleHeight/3,
		});
		chart = c
	});
}
function getUserSolve(data){
	var table = $( '<table>' );
	var thead = $( '<thead>' );
	$( '<th><b>' ).text( 'No.' ).appendTo( thead );
	$( '<th><b>' ).text( 'Type' ).appendTo( thead );
	$( '<th><b>' ).text( 'Title' ).appendTo( thead );
	//$( '<th><b>' ).text( 'Flag' ).appendTo( thead );
	$( '<th><b>' ).text( 'Date' ).appendTo( thead );
	$( '<th><b>' ).text( 'IP' ).appendTo( thead );
	$( '<th><b>' ).text( 'Kill' ).appendTo( thead );
	thead.appendTo(table);
	debugLog(data[1]);
	/*遍历数组*/
	$.each(
		data[1],
		function(num,content){
			var trow = $( '<tr>' );
			$( '<td>' ).text(num+1).appendTo(trow);
			$( '<td>' ).text(quesType[content[1]]).appendTo( trow );
			$( '<td>' ).text(content[2]).appendTo( trow );
			$( '<td>' ).text(getMyDate(content[3])).appendTo( trow );
			$( '<td>' ).text(content[4]).appendTo( trow );
			if(content[0]=='1'){
				$( '<td style="color:#4caf50">' ).text("√").appendTo( trow );
			}
			else{
				$( '<td style="color:red">' ).text("×").appendTo( trow );
			}
			trow.appendTo( table );
		}
	);
	$( '#subinfo' ).html( table.html() );
}
function getUserSolves(){
	var allData=[{'id':'0.0','parent':'','name':'总提交数'},{'id':'1.1','parent':'0.0','name':'right','nick':'正确率'},{'id':'1.2','parent':'0.0','name':'error','nick':'错误率'},{'id':'2.1','parent':'1.1','name':'Web'},{'id':'2.2','parent':'1.1','name':'Reverse'},{'id':'2.3','parent':'1.1','name':'Pwn'},{'id':'2.4','parent':'1.1','name':'Misc'},{'id':'2.5','parent':'1.1','name':'Crypto'},{'id':'2.6','parent':'1.1','name':'Stega'},{'id':'2.7','parent':'1.1','name':'Ppc'},{'id':'2.8','parent':'1.2','name':'Web'},{'id':'2.9','parent':'1.2','name':'Reverse'},{'id':'2.10','parent':'1.2','name':'Pwn'},{'id':'2.11','parent':'1.2','name':'Misc'},{'id':'2.12','parent':'1.2','name':'Crypto'},{'id':'2.13','parent':'1.2','name':'Stega'},{'id':'2.14','parent':'1.2','name':'Ppc'}];
	var correctType=[['Web',0],['Reverse',0],['Pwn',0],['Misc',0],['Crypto',0],['Stega',0],['Ppc',0]];
	$.ajax({
		type: 'POST',
		url: 'ajax.php?m=getUserSolves',
		dataType: 'json',
		data:{"token":token},
		success:function(data){
			debugLog(data);
			if(errorCheck(data)){
				return false;
			}
			if(data[1].length==0){
				return false;
			}
			$.each(data[1],function(n,data){
				//填充数据 正确率图表
				if(data[0]=='1'){
					allData.push({'id':'3.1','parent':'2.'+(parseInt(data[1])+1),'name':data[2],'value':1});
				}
				else{
					allData.push({'id':'3.2','parent':'2.'+(parseInt(data[1])+8),'name':data[2],'value':1});
				}
				//填充数据 答题类型图表
				correctType[parseInt(data[1])][1]+=data[0]=='1'?1:0;
			});
			getUserSolveNum(allData);
			getUserSolveType(correctType);
			getUserSolve(data);
		},
		error:function(data){
			debugLog(data);
		}
	});	
}
function getUserAvator(){
	debugLog("getUserAvator");
	$.ajax({
		type: 'POST',
		url: 'ajax.php?m=getUserAvator',
		dataType:'json',
		data:{"token":token},
		success: function(data) {
			debugLog(data);
			if(errorCheck(data)){
				return false;
			}
			$('.headImgShow').attr('src',data[1]);
		},
		error: function(data)	{
			debugLog(data);
		}
	});
	return false;
}

function imgCheck(imgInfo){
	if(imgInfo===undefined){
		return 'NULL';
	}

	if(imgInfo.size > 204800){
		Materialize.toast("选择的头像大小过大，请选择小于200k的图片",4000);
		return false;
	}
	imgName=imgInfo.name;
	extStart = imgName.lastIndexOf('.'),
	ext = imgName.substring(extStart,imgName.length).toUpperCase();
	//判断图片格式
	if(ext !== '.PNG' && ext !== '.JPG' && ext !== '.JPEG' && ext !== '.GIF'){
		Materialize.toast("请上传正确格式的图片",4000);
		return false;
	}
	return true;
}

//$(".imgInput").change(function(){
//	debugLog("触发");
//	debugLog(URL.createObjectURL($(this)[0].files[0]));
//	$(".img").attr("src",URL.createObjectURL($(this)[0].files[0]));
//});

$(".headImgShow").click(function(){
	debugLog("触发click");
	return $(".imgInput").click();
});

$(".imgInput").change(function(){
	debugLog("触发change");
	debugLog($(this)[0].files[0]);
	$(".headImgShow").attr("src",URL.createObjectURL($(this)[0].files[0]));
});


$(document).ready(function(){
	loadAccount();
	$('input#said, textarea#textarea1,input#nickname').characterCounter();
	$('#login').submit(function(){
		if(loggedin){
			Materialize.toast("你已经登录过了!", 4000);
			loadAccount();
			return false;
		}	
		var info = new FormData();
		info.append( 'password', $( '[name="password"]' ).prop( 'value' ) );
		info.append( 'username', $( '[name="name"]' ).prop( 'value' ) );
		info.append( 'captcha',  $('[name="captcha"]').prop('value'));
		info.append( 'token', token );
		$.ajax({
			type: 'POST',
			url: 'ajax.php?m=login',
			data: info,
			// async: false,
			dataType: 'json',
			processData: false,
			contentType: false,
			success: function(data){
				if(errorCheck(data)){
					getCaptcha();
					return false;
				}
				debugLog("load function");
				loadSession();
				loadAccount();
				//location.reload();
				Materialize.toast(data[0][1], 4000);
			},
			error:function(data){
				debugLog(data);
			}
		});
		return false;
	});
	
	$('#logout').click(function(){
		if(!loggedin){
			Materialize.toast("你还没有登录!", 4000);
			loadAccount();
			return false;
		}
		$.ajax({
			type: 'POST',
			url: 'ajax.php?m=logout',
			data:"token="+token,
			dataType: 'json',
			success: function(data) {
				Materialize.toast(data[0][1], 4000);
				loadSession();
				loadAccount();
			},
			error:function(data){
				debugLog(data);
			}
		});
		return false;
	});

	$('#share').click(function(){
		alert("请手动复制粘贴本网站url (^ v ^) ");
		return false;
	});

	/*修改base info信息*/
	$('#userbaseinfo').submit(function(){
		debugLog("teaminfo click");
		if(!loggedin){
			Materialize.toast("你还没有登录!", 4000);
			loadAccount();
			return false;
		}
		var imgInfo=$(".imgInput")[0].files[0];
		var imgBase64Data="NULL";
		debugLog(imgInfo);
		if(!imgCheck(imgInfo)){
			return false;
		}
		//Materialize.toast("1111111111111!", 4000);return false;
		var baseAjax=function(imgdata){
			$.ajax({
				type: 'POST',
				url: 'ajax.php?m=modUserBaseInfo',
				data: {'img':imgdata,'said':$('#said').val(),'nickname':$('#nickname').val(),'token':token},
				dataType:'json',
				success: function(data){
					debugLog(data);
					if(errorCheck(data)){
						return false;
					}
					Materialize.toast(data[0][1], 4000);
					if(data[0][0]== '1'){
						loadSession();
						loadAccount();
					}
				},
				error:function(data){
					debugLog(data);
				}
			});
		}

		if(imgInfo===undefined){
			baseAjax('NULL');
			return false;
		}

		var reader = new FileReader();
		//将文件以Data URL形式读入页面
		var imgUrlBase64 = reader.readAsDataURL(imgInfo);
		//debugLog(imgUrlBase64);
		//尼玛的坑壁异步
		reader.onload = function (e) {
			//var ImgFileSize = reader.result.substring(reader.result.indexOf(",") + 1).length;//截取base64码部分（可选可不选，需要与后台沟通）
			imgBase64Data=reader.result;
			debugLog("----->");
			debugLog(imgBase64Data);
			baseAjax(imgBase64Data);
		}

		debugLog("AAAAAAAAAAAAAAAAAAAAAAAA");
		debugLog(imgBase64Data);
		//Materialize.toast("1111111111111!", 4000);return false;
		return false;
	});

	/*修改pass info信息*/
	$('#passinfo').submit(function(){
		debugLog("passinfo click");
		if(!loggedin){
			Materialize.toast("你还没有登录!", 4000);
			loadAccount();
			return false;
		}
		$.ajax({
			type: 'POST',
			url: 'ajax.php?m=modUserPassword',
			data: {
				'old':$('#oldpassword').val(),
				'new':$('#newpassword1').val(),
				'repeat':$('#newpassword2').val(),
				'token':token
			},
			dataType:'json',
			success: function(data){
				if(errorCheck(data)){
					return false;
				}
				Materialize.toast(data[0][1], 4000);
				if(data[0][0]== '1'){
					logout();
					loadSession();
					loadAccount();
				}
			},
			error:function(data){
				debugLog(data);
			}
		});
		return false;
	});	
});

