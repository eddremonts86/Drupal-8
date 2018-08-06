(function ($, Drupal) {
	Drupal.behaviors.myBehavior = {
		attach: function (context, settings) {
		    $(".toTop .col a").click(function() {
		        $("html, body").animate({
		            scrollTop: 0
		        }, 2000);
		        return false
		    });
		    $(".wp2", context).once('myBehavior').waypoint(function() {
		        $(".wp2").addClass("animated fadeInUp");
		    }, {
		        offset: "75%"
		    });
		    
		    $(".headSlider", context).once('myBehavior').lightSlider({
		        gallery: false,
		        item: 1,
		        thumbItem: 9,
		        slideMargin: 0,
		        speed: 500,
		        auto: true,
		        controls: false,
		        loop: true,
		        pager: false
		    });
		    
			$(".HomePreviewsSlider", context).once('myBehavior').lightSlider({
		        gallery: false,
		        item: 1,
		        thumbItem: 9,
		        slideMargin: 0,
		        speed: 600,
		        pause: 7000,
		        auto: true,
		        controls: false,
		        loop: true,
		        pauseOnHover: true,
		    });
		    
		    $('.LeaguesListPlugin .LeagueList', context).once('myBehavior').masonry({
		        itemSelector: '.col',
		        isAnimated: true
		    });
		    
		    $('.TeamsListPlugin .TeamsList', context).once('myBehavior').masonry({
		        itemSelector: '.col',
		        isAnimated: true
		    });
		
		    var nav = $('.SiteHeader');
		    var off = 0;
		    $(window).scroll(function () {
		        if ($(this).scrollTop() > 300) {
			        if($('body').hasClass('toolbar-fixed')){
						off = parseInt($('body').css('padding-top'));
		    		}
		    		nav.css('top', off + 'px');
		            nav.addClass("Fixed animated fadeInDown");
		        } else {
			        nav.css('top', 0);
		            nav.removeClass("Fixed animated fadeInDown");
		        }
		    });
		    
		    leagueTVBlock();
		    
		    function leagueTVBlock(){
				$('.tv-block .tv-block-select select').change(function(){
				    $('.tv-block .tv-block-schedule ul li').css('display', 'none');
				    $('.tv-block .tv-block-schedule ul li' + $(this).val()).css('display', 'block'); 
			    });
		    }
		    
		    homeMatchBlock();
		    
		    function homeMatchBlock(){  
			    $('.ContentHeader .match-select #select-legue').on('change', function(){ 
				    $('.ContentHeader .match-select #select-match option').css('display', 'none');
					$('.ContentHeader .match-select #select-match option#' + $(this).val()).css('display', 'block');
					$('.ContentHeader .match-select #select-match option#' + $(this).val()).first().prop('selected', true);
					$('.ContentHeader .match-select a.btn').attr('href', $('.ContentHeader .match-select #select-match').val());
			    });
			    $('.ContentHeader .match-select #select-match').change(function(){
					$('.ContentHeader .match-select a.btn').attr('href', $(this).val());	
				});
		    }
		    
		    TabsFix();
		    
		    function TabsFix(){
				$tabsContainer = $('.Tabs');
				$tabsContainer.find('.TabsMenu .panel-body ul li a').click(function(){
					if(!$(this).parent().hasClass('active')){
						var $activeElement = $(this);
						var off = 0;
						var menu = $('.SiteHeader').height();
						
						if($('body').hasClass('toolbar-fixed')){
							off = parseInt($('body').css('padding-top'));
						}
						
						$("html, body").animate({
							scrollTop: $('.TabsContent').offset().top - off - menu
						}, 2000);	
						
						$tabsContainer.find('.TabsMenu .panel-body ul li').each(function(){
							if(!$(this).find('a').is($activeElement)){
								if($(this).hasClass('active')){
									$(this).removeClass('active');
									$(this).find('a').attr('aria-expanded', 'false');
								}
							}
						});
					}
				});	
			}
			
			popup();
			
			function popup(){		
				$('a.popUp').click(function(e){
					$($(this).attr('href')).addClass('active');
					e.preventDefault();
				});
				
				$('li.popUp a').click(function(e){
					e.preventDefault();
					$($(this).attr('href')).addClass('Open');	
				});
		
				$('.popupBlock').click(function(){
					if($(this).hasClass('active')){
						$('.popupBlock').removeClass('active');
					}
				});	
			}
		}
	}
})(jQuery, Drupal);
