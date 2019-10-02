function ajax_call(fn_name){
    var data = $('#filter-form').serialize();
    $.ajax({ 
        type      : 'POST',
        url       : '../app/lib/functions.php?action='+fn_name,
        data      : data,
        success   : function(res) {
            $('.cards-list').html(res);
        }
    });
}