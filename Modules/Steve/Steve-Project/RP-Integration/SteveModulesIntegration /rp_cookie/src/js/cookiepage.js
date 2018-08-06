(function($) {
	$(document).ready(function () {
		
	    function get_additional_domains() {
	        a = document.createElement("a");
	        a.href = window.location.href;
	        return a.hostname.replace(/^www/, '');
	    }
	
	    let dataslidervalue = null;
	    let cookieTypeAgree = 'GDPR_OPTIN';
	    let cookie_value = Cookies.get(cookieTypeAgree);
	    let additional_domain = get_additional_domains();
	    switch(cookie_value) {
	        case 'no':
	            {
	                dataslidervalue = 1;
	                $('#cookies_requiered').css('color','#003172');
	
	                break;
	            }
	
	        default:{
	            dataslidervalue = 2;
	            $('#cookies_optional').css('color','#003172');
	            }
	    }
	    let data_slider_ticks_labels=[Drupal.t('cookie_req_option'), Drupal.t('cookie_opc_option')];
	   // $('#ex19').bootstrapSlider();
	
	    var width =  jQuery( document ).width();
	    //if(width > 1024) {
	
	        $('#ex19').bootstrapSlider({
	            ticks: [1, 2],
	            ticks_labels: data_slider_ticks_labels,
	            orientation: "vertical"
	        });
	   /* }
	    else{
	        $('#ex19').bootstrapSlider({
	            ticks: [1,2],
	            ticks_labels: data_slider_ticks_labels
	        });
	    }*/
	    $('#ex19').bootstrapSlider('setValue', dataslidervalue);
	    $('#ex19').change(function() {
	        let agreeType  = $('#ex19').val();
	        switch(agreeType) {
	            case '1':
	                $('#cookies_optional').css('color','#ccc');
	                $('#cookies_requiered').css('color','#003172');
	                break;
	            default:
	                $('#cookies_requiered').css('color','#ccc');
	                $('#cookies_optional').css('color','#003172');
	        }
	    });
	    $('#aggree').on( "click",function() {
	        let agreeType  = $('#ex19').val();
	        switch(agreeType) {
	            case '1':
	                $.each(Cookies.get(), function(key, value) {
	                    Cookies.remove(key);
	                    Cookies.remove(key, { 'domain': additional_domain });
	                })
	                Cookies.set(cookieTypeAgree, 'no');
	                break;
	
	            default:
	                Cookies.set(cookieTypeAgree, 'yes');
	        }
	
	        let url = "/";
	        $(location).attr("href", url);
	
	    });
	
	
	    var width =  jQuery( document ).width();
	    if(width < 1024) {
	        $('#ex19').bootstrapSlider({
	            ticks: [1, 2],
	            ticks_labels: data_slider_ticks_labels,
	            orientation: "vertical"
	        });
	    }
	});
})(jQuery);
