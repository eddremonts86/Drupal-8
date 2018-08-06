(function($) {
	
    $(window).on('load', function() {
	    addBlockWarning();
	    
		function addBlockWarning(){
			blockAdBlock.onDetected(function(){
				$('.AdBlockPlugin .AdBlockClose').click(function(){
					$('.AdBlockPlugin').remove();
				});
				$('.AdBlockPlugin').addClass('Open');	
			});
		}	
    });
    
})(jQuery);