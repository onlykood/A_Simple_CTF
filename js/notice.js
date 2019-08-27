function getNotice()
{
    $.ajax({
        type: 'POST',
        url: 'ajax.php?m=getNotice',
        data:{"token":token},
        dataType:'json',
        success: function(data) {
            debugLog(data);
            if(errorCheck(data)){
                return false;
            }
            var table = $( '<table>' );
            $.each
            (
                data[1],
                function(num,content)
                {
                    var trow = $( '<tr>' );
                    /*$( '<td>' ).text().appendTo( trow );*/
                    $( '<td>' ).html('['+ getMyDate(content[0]) +'] '+content[1]).appendTo( trow );
                    trow.appendTo( table );
                }
            );
            $( '#notice' ).html( table.html() );
        },
        error: function(data)    {
            debugLog(data);
        }
    });
    return false;
}
$(document).ready(function(){
    getNotice();
});