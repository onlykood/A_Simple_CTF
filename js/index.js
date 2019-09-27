function getTop3Rank()    {    
    $.ajax({
        type: 'post',
        url: 'ajax.php?m=getRank',
        data:{'token':token,'is_img':0},
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
            debugLog(data[1]);
            $.each(
                data[1],
                function(num,content){
                    if(num < 3)    {
                        var trow = $( '<tr>' );
                        $( '<td>' ).text( num+1 ).appendTo( trow );
                        $( '<td>' ).text( content[1]).appendTo( trow );
                        $( '<td>' ).text( content[0] ).appendTo( trow );
                        trow.appendTo( table );
                    }
                }
            );
            debugLog(table.html());
            $( '#rank' ).html( table.html() );
        },
        error: function(data)    {
            debugLog(data);
        }
    });
    return false;
}
Date.prototype.format = function(fmt) { 
     var o = { 
        "M+" : this.getMonth()+1,                 //月份 
        "d+" : this.getDate(),                    //日 
        "h+" : this.getHours(),                   //小时 
        "m+" : this.getMinutes(),                 //分 
        "s+" : this.getSeconds(),                 //秒 
        "q+" : Math.floor((this.getMonth()+3)/3), //季度 
        "S"  : this.getMilliseconds()             //毫秒 
    }; 
    if(/(y+)/.test(fmt)) {
            fmt=fmt.replace(RegExp.$1, (this.getFullYear()+"").substr(4 - RegExp.$1.length)); 
    }
     for(var k in o) {
        if(new RegExp("("+ k +")").test(fmt)){
             fmt = fmt.replace(RegExp.$1, (RegExp.$1.length==1) ? (o[k]) : (("00"+ o[k]).substr((""+ o[k]).length)));
         }
     }
    return fmt; 
}

function getCTFType(){
    $.ajax({
        type:'post',
        url:'ajax.php?m=getCTFType',
        data:{'token':token},
        dataType:'json',
        success:function(data){
            debugLog(data);
            if(errorCheck(data)){
                return false;
            }
            data=data[1];
            if(data[0]=='1'){
                $('.countdown').hide();
            }
            else{
                startTime=data[1];
                endTime=data[2];
                nowTime=(new Date().getTime()+'').substring(0,10)
                if(nowTime<startTime){
                    $('.countdown').hide();
                    $('#headline').html('比赛尚未开始');
                }
                else if(nowTime>=startTime && nowTime<=endTime){
                    console.log('now -> '+new Date().format('M/d/yyyy hh:mm:ss'));
                    console.log('end -> '+new Date(endTime*1000).format('M/d/yyyy hh:mm:ss'));
                    $('.countdown').downCount({date: new Date(endTime*1000).format('M/d/yyyy hh:mm:ss'),offset: +8}, function () {debugLog('比赛结束!');});
                    $('.countdown').show();
                    $('#headline').html('比赛正在进行中');
                }
                else{
                    $('.countdown').show();
                    $('#headline').html('比赛结束');    
                }
            }
        },
        error:function(data){
            debugLog(data);
        }
    });
    return false;
}

function getRecentSloves()    {    
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
        error: function(data)    {
            debugLog(data);
        }
    });
    return false;
}

$(document).ready(function(){
    getCTFType();
    getTop3Rank()
    getRecentSloves();
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

});
