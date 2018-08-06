(function($) {
	$(document).ready(function () {
		window.addEventListener("load", function(){
		    window.cookieconsent.initialise({
		        "palette": {
		            "popup": {
		                "background": "#efefef",
		                "text": "#404040"
		            },
		            "button": {
		                "background": "#8ec760",
		                "text": "#ffffff"
		            }
		        },
		        "theme": "classic",
		        "content": {
		            "message": Drupal.t('cookie_block_text'),
		            "link": Drupal.t('cookie_block_learn_more'),
		            "dismiss": Drupal.t('cookie_block_agree'),
		            "href": "#"
		        }
	    	})
	    });
	});
})(jQuery);