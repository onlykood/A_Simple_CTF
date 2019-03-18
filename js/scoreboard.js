function getRankList(data)	{	
	var table = $( '<table>' );
	var thead = $( '<thead>' );
	$( '<th><b>' ).text( 'Rank' ).appendTo( thead );
	$( '<th><b>' ).text( 'Avatar' ).appendTo( thead );
	$( '<th><b>' ).text( 'Name' ).appendTo( thead );
	$( '<th><b>' ).text( 'Speak' ).appendTo( thead );
	$( '<th><b>' ).text( 'Score' ).appendTo( thead );
	thead.appendTo(table);
	$.each(
		data,
		function(num,content){
			var trow = $( '<tr>' );
			$( '<td>' ).text( num+1 ).appendTo( trow );
			$('<td><img class="tinyImg" src="'+textToImg(content[3] ,content[1])+'" />').appendTo( trow );
			$( '<td>' ).text( content[1]).appendTo( trow );
			$( '<td>' ).text( content[2]).appendTo( trow );
			$( '<td>' ).text( content[0]).appendTo( trow );
			trow.appendTo( table );
		}
	);
	$( '#ranking' ).html( table.html() );
	return false;
}

function getRankPic(data) {
	var info=[];
	if(data[0][0]=='0'){
		return false;
	}
	$.each(data,function(n,content){
		if(n>14){
			return false;
		}
		info[n]={name:content[1],data:content[4]};
	})
	debugLog("info:");
	debugLog(info);
	$('#rank_pic').highcharts({
		credits:{enabled: false },
		exporting:{enabled:false},
		colors:['#37a2da','#32c5e9','#67e0e3','#9fe6b8','#ffdb5c','#ff9f7f','#fb7293','#e062ae','#e690d1','#e7bcf3','#9d96f5','#8378ea','#96bfff'],
		chart: {type: 'spline'},
		title: {text: null},
		legend:{
			align:'right',
			verticalAlign:'top',
			layout:'vertical',
			floating:true,
		},
		xAxis: {
			type: 'datetime',
			title: {text: null}
			},
		yAxis: {title: {text: null},min: 0},
		tooltip: {
			headerFormat: '<b>{series.name}</b><br>',
			pointFormat: '{point.x:%b-%e}: {point.y:f}'
			},
		plotOptions: {
			spline: {
				marker: {
					enabled: true
					}
				}
			},
		series:info
	});
	return false;
}
function getRanks(){
	$.ajax({
		type: 'post',
		url: 'ajax.php?m=getRank',
		dataType:'json',
		data:{'token':token,'is_img':1},
		success: function(data) {
			debugLog(data);
			if(errorCheck(data)){
				return false;
			}
			getRankList(data[1]);
			getRankPic(data[1]);
		},
		error: function(data)	{
			debugLog(data);
		}
	});
	return false;
}
$(document).ready(function(){
	getRanks();
});
