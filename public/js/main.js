$(document).ready(function(){
    //on load
    ajax_call('get_movies');

    $(document).on('change', '.filter_checkbox, #length-sort, .curr-page', function(e){
        e.preventDefault();
        if(!$(this).is('.curr-page')){
            $('.curr-page').val(1);
        }
        ajax_call('get_movies');
    });

    $('.prev').on('click',function(){
        var curr = $('.curr-page').val();
        if(curr !=1){
            $('.curr-page').val(parseInt(curr)-1);
            ajax_call('get_movies');
        }
    });
    $('.next').on('click',function(){
        var curr = parseInt($('.curr-page').val());
        if(curr !=$('.total-page').text()){
            $('.curr-page').val(parseInt(curr)+1);
            ajax_call('get_movies');
        }
    });
    $('.curr-page').on('blur',function(){
        var pattern = /^(0|([1-9]\d*))$/;
        if(!pattern.test($(this).val()) || $.trim($(this).val())==""){
            $('.curr-page').val(parseInt(1))
        }
        if($(this).val()>$('.total-page').text()){
            $(this).val(parseInt($('.total-page').text()));
        }
        ajax_call('get_movies');
    });
});
