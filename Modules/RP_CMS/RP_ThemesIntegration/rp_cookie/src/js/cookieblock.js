(function($) {
	
    $(window).on('load', function() {
	    cookieBlock();
	    
	    function cookieBlock(){
			$('.CookiesPlugin .closeWindow').click(function(e){
				e.preventDefault();
				setCookie('cookiepolicy', 'hide', {expires: $('.CookiesPlugin').attr('duration')});		
				$('.CookiesPlugin').remove();
			});
		}
		
		function setCookie(name, value, options) {
			options = options || {};
			
			var expires = options.expires;
			
			if (typeof expires == "number" && expires) {
				var d = new Date();
				d.setTime(d.getTime() + expires * 1000);
					expires = options.expires = d;
			}
				
			if (expires && expires.toUTCString) {
				options.expires = expires.toUTCString();
			}
				
			value = encodeURIComponent(value);
				
			var updatedCookie = name + "=" + value;
				
			for (var propName in options) {
				updatedCookie += "; " + propName;
				var propValue = options[propName];
				if (propValue !== true) {
					updatedCookie += "=" + propValue;
				}
			}
			
			document.cookie = updatedCookie;
		}
		
    });
    
})(jQuery);