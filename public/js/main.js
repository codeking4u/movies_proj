$(document).ready(function(){
    $(document).on('change', '.filter_checkbox, #length-sort, .curr-page', function(e){
        e.preventDefault();
        ajax_call('get_movies');
    });
    
    ajax_call('get_movies');
});
