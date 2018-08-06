(function ($, Drupal) {
	Drupal.behaviors.myBehavior = {
	  attach: function (context, settings) {
		    preloader();
		    toTop();
		    homeMatchBlock();
			ProgramHome();
			fixedMenu();
			popUp();
			
			function preloader(){
				$('body').removeClass('preloader');
			}
			    
			function toTop(){    
			    $("a.toTop").click(function() {
			        $("html, body").animate({
			            scrollTop: 0
			        }, 2000);
			        return false
			    });
		    }
		    
		    function ProgramHome(){
			    $('.ProgramHome #DaySelect').change(function(){
				    $('.ProgramHome .panel-group').removeClass('active');
				    
				    day = $(this).val();
				    
					$('.ProgramHome .panel-group').each(function(){
						if($(this).attr('day') == day){
							$(this).addClass('active');
						}
					});
			    });
		    }
		    
			function homeMatchBlock(){
			    $('.ContentHeader .match-select #select-legue').change(function(){
				    $('.ContentHeader .match-select #select-match option').css('display', 'none');
					$('.ContentHeader .match-select #select-match option#' + $(this).val()).css('display', 'block');
					$('.ContentHeader .match-select #select-match option#' + $(this).val()).first().prop('selected', true);
					$('.ContentHeader .match-select a').attr('href', $('.ContentHeader .match-select #select-match').val());
			    });
			    $('.ContentHeader .match-select #select-match').change(function(){
					$('.ContentHeader .match-select a').attr('href', $(this).val());	
				});
		    }
		    
		    function fixedMenu(){
			    var nav = $('.SiteHeader .HeaderContainer');
				$('.SiteHeader').height(nav.height());
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
		    }
		    
		    
		    $(".TeamEventsList .holder", context).once('myBehavior').jPages({
			      containerID: "TeamEvents",
			      previous : "←",
			      next : "→",
			      perPage: 10,
			      midRange: 3,
			      direction: "random",
			      animation: "flipInY"
			    });
		    
		    $(".headSlider", context).once('myBehavior').lightSlider({
		        gallery: false,
		        item: 1,
		        controls: true,
		        thumbItem: 9,
		        slideMargin: 0,
		        speed: 1000,
		        auto: true,
		        loop: true,
		        pager: false
		    });
		    
		    $('.TeamsListPlugin .TeamsList', context).once('myBehavior').masonry({
		        itemSelector: '.col',
		        isAnimated: true
		    });
		
			function popUp(){
				$('a.popUp').click(function(e){
					e.preventDefault();
					$($(this).attr('href')).addClass('Open');	
				});
				
				$('li.popUp a').click(function(e){
					e.preventDefault();
					$($(this).attr('href')).addClass('Open');	
				});
	
				$('.PopupPlugin .popupContainer').click(function(){
					$('.PopupPlugin .popupContainer').removeClass('Open');
				});
			}
		}
	}
})(jQuery, Drupal);

