(function($) {
    $(document).ready(function() {
        function tabSwitcher(){
            $('.HomeProviders .providerTabs .nav-tabs li a').click(function(){
                tab = $(this).attr('aria-controls');
                $('.HomeProviders .StreamProvider').each(function(){
                    if($(this).attr('tab') == tab){
                        $(this).addClass('active');
                    }else{
                        $(this).removeClass('active');
                    }
                });
                
            });
        }
        function popUp(){
            open = false;

            $('html').click(function(){
                if(open){
                    $('.StreamProvider .ProviderLinks > .container > .row > .col-md-4 .popUp').css({'display' : 'none'});
                    open = false;
                }
            });

            $('.StreamProvider .ProviderLinks > .container > .row > .col-md-4 .disclaimer').click(function(){
                if(!open){
                    $(this).parent().find('.popUp').css({'display' : 'block'});
                    setTimeout(function() { open = true }, 50);
                }
            });

        }
        function textExpander(){
            $('.courseCard .details a').click(function(){
                if($(this).parent().hasClass('open')){
                    $(this).parent().removeClass('open');
                }else{
                    $(this).parent().addClass('open');
                }
            })
        }
        function trackOpener(){
            $('.Tracklist .opener a').click(function(){
               $(this).parent().parent().parent().find('.courseCard').css({'display':'block'});
               $(this).parent().parent().css({'display':'none'});
           });
        }
        trackOpener();
        tabSwitcher();
        popUp();
        textExpander();
    });
}
)(jQuery);
