//*****************************************
$(document).ready ( function(){
});


$('.menu-item').hover(
    function(){
        $(this).children('ul').show(500);
        /*
        $(this).children('ul').each(function () {
            $(this).removeClass('childrenNoActive').addClass('childrenActive');
        });
        */
    },
    function(){
       // $(this).find('ul').hide(500);
        /*
        $(this).find('ul').each(function () {
            $(this).removeClass('childrenActive').addClass('childrenNoActive');
        });
        */


    });

$('.menu-tops').hover(
    function(){
       // $(this).children('ul').show(500);
        /*
        $(this).children('ul').each(function () {
            $(this).removeClass('childrenNoActive').addClass('childrenActive');
        });
        */
    },
    function(){
        $(this).find('ul').hide(500);
        /*
        $(this).find('ul').each(function () {
            $(this).removeClass('childrenActive').addClass('childrenNoActive');
        });
        */


    });

/*
$(".menu-item").on('click', function (event) {
    event.stopPropagation();
    var otherUls = $(".menu-tops" + "[data-id!='" + this.dataset.id + "']");
    if ($(this).hasClass("menu-tops")){
        $(otherUls).find('ul').hide();
        $(otherUls).each(function () {
            this.dataset.mode = 'close';
        })
    }
    switch (this.dataset.mode){
        case 'close':
            $(this).children('ul').show();
            this.dataset.mode = 'open';
            break;
        case 'open':
            $(this).find('ul').hide();
            this.dataset.mode = 'close';
            break;
    }
});
*/