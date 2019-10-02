function ajax_call(fn_name){
    var data = $('#filter-form').serialize();
    $.ajax({ 
        type      : 'POST',
        url       : '../app/lib/functions.php?action='+fn_name,
        data      : data,
        success   : function(res) {
            var result = $.parseJSON(res);	
            $('.cards-list').html(result.data);
            $('.total-page').text(result.total_page);
        }
    });
}