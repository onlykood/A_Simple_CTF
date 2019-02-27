function getTop3Rank()	{	
	$.ajax({
		type: 'post',
		url: 'ajax.php?m=getRank',
		data:"token="+token,
		dataType:'json',
		success: function(data) {
			debugLog(data);
			if(errorCheck(data)){
				return false;
			}
			var table = $( '<table>' );
			var thead = $( '<thead>' );
			$( '<th><b>' ).text( 'Rank' ).appendTo( thead );
			$( '<th><b>' ).text( 'Username' ).appendTo( thead );
			$( '<th><b>' ).text( 'Score' ).appendTo( thead );
			thead.appendTo(table);
			var i = 0;
			$.each(
				data[1],
				function(num,content){
					if(num < 3)	{
						var trow = $( '<tr>' );
						$( '<td>' ).text( num+1 ).appendTo( trow );
						$( '<td>' ).text( content[1]).appendTo( trow );
						$( '<td>' ).text( content[0] ).appendTo( trow );
						trow.appendTo( table );
					}
				}
			);
			$( '#rank' ).html( table.html() );
		},
		error: function(data)	{
			debugLog(data);
		}
	});
	return false;
}

function getRecentSloves()	{	
	$.ajax({
		type: 'post',
		url: 'ajax.php?m=getRecentSloves',
		data:"token="+token,
		dataType:'json',
		success: function(data) {
			debugLog(data);
			if(errorCheck(data)){
				return false;
			}
			var table = $( '<table>' );
			$.each(
				data[1],
				function(num,content)
				{
					var trow=$('<tr>');
					$( '<td>' ).text(content[1]).appendTo(trow);
					$( '<td>' ).text(content[2]).appendTo(trow);
					trow.appendTo( table );
				}
			);
			$( '#recent_solves' ).html( table.html() );
		},
		error: function(data)	{
			debugLog(data);
		}
	});
	return false;
}

function getStatus()	{
	$.ajax({
		type:'post',
		url:'ajax.php?m=getStatus',
		data:"token="+token,
		dataType:'json',
		success: function(data) {
			if(errorCheck(data)){
				return false;
			}
			debugLog(data);
			var s = '';
			switch(data[1]['status']){
				case '5':s='比赛还没有开始！';break;
				case '4':s='热身赛开始！';break;
				case '3':s='休息中，稍后进行正式赛！';break;
				case '2':s='正式赛开始！';break;
				case '1':s='比赛已经结束了！';break;
			}
			//$( '#status' ).text( 'Season 1' );
		},
		error: function(data){
			debugLog(data);
		}
	});
	return false;
}

$(document).ready(function(){
	getTop3Rank()
	getRecentSloves();
	//getStatus();
	$.fn.downCount = function (options, callback) {
		var settings = $.extend({date: null,offset: null}, options);
		if (!settings.date||!Date.parse(settings.date)) 
			$.error('Date not found or Incorrect date format');
		var container = this;
		var currentDate = function () {
			var date = new Date();
			var utc = date.getTime() + (date.getTimezoneOffset() * 60000);
			var new_date = new Date(utc + (3600000*settings.offset))
			return new_date;
		};
		function countdown () {
			var target_date = new Date(settings.date), current_date = currentDate();
			var difference = target_date - current_date;
			if (difference < 0) {
				clearInterval(interval);
				if (callback && typeof callback === 'function') 
					callback();
				return;
			}
			var _second = 1000,_minute = _second * 60,_hour = _minute * 60,_day = _hour * 24;
			var days = Math.floor(difference / _day),hours = Math.floor((difference % _day) / _hour),minutes = Math.floor((difference % _hour) / _minute),seconds = Math.floor((difference % _minute) / _second);
			days = (String(days).length >= 2) ? days : '0' + days;
			hours = (String(hours).length >= 2) ? hours : '0' + hours;
			minutes = (String(minutes).length >= 2) ? minutes : '0' + minutes;
			seconds = (String(seconds).length >= 2) ? seconds : '0' + seconds;
			var ref_days = (days === 1) ? 'day' : 'days',ref_hours = (hours === 1) ? 'hour' : 'hours',ref_minutes = (minutes === 1) ? 'minute' : 'minutes',ref_seconds = (seconds === 1) ? 'second' : 'seconds';
			container.find('.days').text(days);
			container.find('.hours').text(hours);
			container.find('.minutes').text(minutes);
			container.find('.seconds').text(seconds);
			container.find('.days_ref').text(ref_days);
			container.find('.hours_ref').text(ref_hours);
			container.find('.minutes_ref').text(ref_minutes);
			container.find('.seconds_ref').text(ref_seconds);
		};
		var interval = setInterval(countdown, 1000);
	};
	$('.countdown').downCount({
		date: '1/1/2019 00:00:00',offset: +10
	}, function () {
		debugLog('S1结束!');
	});
});
