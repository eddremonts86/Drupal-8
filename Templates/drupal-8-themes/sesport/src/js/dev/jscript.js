
jQuery(document).ready(function(){

    jQuery(".panel-kamp").click(function () {
        jQuery('.panel-heading').removeClass( "collabseIN" );
        var data = jQuery(".in").attr('aria-labelledby');
        jQuery('#'+data).addClass( "collabseIN" );
        console.log(data);
    })



});