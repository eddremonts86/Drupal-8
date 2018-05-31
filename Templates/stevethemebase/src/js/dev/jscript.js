(function($) {
	$(document).ready(function() { 

		function sheldueList(){
			$('.o-sport-program  .panel-kamp > .panel-collapse .panel-heading a').click(function(){
				if($(this).attr('aria-expanded')){
					$active = $(this).parent().parent().parent().parent();
					$list = $active.parent().parent();

					$list.find('.panel-group').each(function(){
						if($active.get(0) !== $(this).get(0) && $(this).find('.panel-collapse').hasClass('in')){
							$(this).find('.panel-collapse').addClass('collapsing').removeClass('colapse in').attr('aria-expanded', false).css({'height': $(this).find('.panel-collapse').height()});
							$(this).find('.panel-heading a').attr('aria-expanded', false).addClass('colapsed');
							$(this).find('.panel-collapse').delay(350).removeClass('collapsing').addClass('collapse');
							//$(this).find('.panel-collapse').collapse('hide');	???
						}
					});
				}
			});	
		}

		function sportStreamTabsToMobile(){
			$('.o-sport-stream .stream_tabs').addClass('mobile');
			$('.o-sport-stream .stream_tabs .tab-content .tab-pane').each(function(index){
				$tab = $(this);
				if(index){				
					$('.o-sport-stream .stream_tabs .tab-content:last-child').after('<div class="tab-content"></div>');
					$('.o-sport-stream .stream_tabs .tab-content:last-child').append($tab);

					$('.o-sport-stream .stream_tabs .nav-tabs li').each(function(i){
						if(!$(this).hasClass('tabs-head') && $(this).find('a').attr('href') === '#' + $tab.attr('id') && i != 1){
							$('.o-sport-stream .stream_tabs .tab-content:last-child').before('<ul class="nav nav-tabs"></ul>');
							$('.o-sport-stream .stream_tabs .nav-tabs')[$('.o-sport-stream .stream_tabs .nav-tabs').length - 1].append($(this)[0]);
						}
					});
				}

			});
		}

		function sportStreamTabsToDesktop(){
			$('.o-sport-stream .stream_tabs').removeClass('mobile');
			$('.o-sport-stream .stream_tabs .tab-content .tab-pane').each(function(index){
				if(index){
					$('.o-sport-stream .stream_tabs .tab-content:nth-child(2)').append($(this));
				}
			});
			$('.o-sport-stream .stream_tabs .nav-tabs li').each(function(index){
				if(index){
					$('.o-sport-stream .stream_tabs .nav-tabs:nth-child(1)').append($(this));
				}
			});
			$('.o-sport-stream .stream_tabs .tab-content').each(function(index){
				if(index){
					$(this).remove();
				}
			});
			$('.o-sport-stream .stream_tabs .nav-tabs').each(function(index){
				if(index){
					$(this).remove();
				}
			});
		}

		function sportStreamTabs(){
			initialBorder = '#bfbfbf';
			initialGradient = 'linear-gradient(to bottom, #f0f0f0 0, #e0e0e0 14%, #dedede 61%)';
			 $('.o-sport-stream .stream_tabs .nav-tabs li').each(function(){
			 	if(!$(this).hasClass('tabs_header')){
			 		$(this).hover(function(){
			 			gradient = $('.o-sport-stream .stream_tabs .nav-tabs li.active').css('background-image');
						border = $('.o-sport-stream .stream_tabs .nav-tabs li.active').css('border-color'); 
			 			$(this).css({'background': gradient });
			 			$(this).css({'border-color': border });
			 		}, function(){
			 			if(!$(this).hasClass('active')){
			 				$(this).css({'background': initialGradient });
			 				$(this).css({'border-color': initialBorder });
			 			}
			 		})
			 	}
			 });

			if(window.matchMedia('(max-width: 767px)').matches){
				sportStreamTabsToMobile();
			}

			$('.o-sport-stream .stream_tabs .nav-tabs a').click(function(){
				if(!$(this).parent().hasClass('active')){
					$('.o-sport-stream .stream_tabs .nav-tabs li').each(function(){
						if(!$(this).hasClass('tabs_header')){
							$(this).css({'background': initialGradient });
			 				$(this).css({'border-color': initialBorder });
						}
					});
					$(this).parent().css({'background': gradient });
			 		$(this).parent().css({'border-color': border });
				}

				if($('.o-sport-stream .stream_tabs').hasClass('mobile')){
					if(!$(this).parent().hasClass('active')){
						$active = $(this);
						$('.o-sport-stream .stream_tabs .nav-tabs a').each(function(){
							if($(this).parent().hasClass('active') && $active.attr('href') !== $(this).attr('href')){
								$(this).attr('aria-expanded', false);
								$(this).parent().removeClass('active');
							}
						});
						$('.o-sport-stream .stream_tabs .tab-content .tab-pane').each(function(){
							if($(this).hasClass('active') && $active.attr('href') !== '#' + $(this).attr('id')){
								$(this).removeClass('active')
							}
						})
					}
				}
			});

			$(window).resize(function(){
				if(window.matchMedia('(max-width: 767px)').matches && !$('.o-sport-stream .stream_tabs').hasClass('mobile')){
					sportStreamTabsToMobile();
				}else if(window.matchMedia('(min-width: 767px)').matches && $('.o-sport-stream .stream_tabs').hasClass('mobile')){
					sportStreamTabsToDesktop();
				}
			})
		}

		function calendarTable(){

			$('.block-sportlivestreamschedulefooterplugin table tbody tr.active').click(function(){
				if($(this).attr('goto')){
					window.location.href = $(this).attr('goto');
				}else{
					$('html,body').animate({scrollTop:0}, 'slow');
					return false; 
				}
			});

			$('.block-sportlivestreamschedulefooterplugin table tr.tr_btn a').click(function(){
				$('.block-sportlivestreamschedulefooterplugin table tr.tr_btn').css({'display':'none'});
				$('.block-sportlivestreamschedulefooterplugin table tbody tr.hidden').removeClass('hidden');
			});
		}

		function sportLiveStreamToMobile(nOfElements){
			if(window.matchMedia('(max-width: 667px)').matches){
				$('.o-sport-program > .container').addClass('mobile');

				if(nOfElements < $('.o-sport-program .panel').length ){
					$('.o-sport-program .panel').each(function(index){
						if(index + 1 > nOfElements){
							$(this).parent().addClass('hidden');
						}
					});
					$('.o-sport-program > .container .ShowBlocks').addClass('active');
				}
			}
		}

		function sportLiveStreamToDesktop(){
			$('.o-sport-program > .container > .ShowBlocks').removeClass('active');
			$('.o-sport-program > .container').removeClass('mobile');
			$('.o-sport-program > .container > .col-md-12').removeClass('hidden');

		}

		function sportLiveStreamMobile(){
			nOfElements = 3;

			sportLiveStreamToMobile(nOfElements);

			$('.o-sport-program > .container > .ShowBlocks').on('click', function(){
				$('.o-sport-program .container > .col-md-12').removeClass('hidden');
				$(this).removeClass('active');
			});

			$(window).resize(function(){
				if(window.matchMedia('(max-width: 667px)').matches && !$('.o-sport-program > .container').hasClass('mobile')){
					sportLiveStreamToMobile(nOfElements);
				}else if(window.matchMedia('(min-width: 667px)').matches && $('.o-sport-program > .container').hasClass('mobile')){
					sportLiveStreamToDesktop();
				}
			})
		}

		function buttonsHover(){
			$('.btn').hover(function(){
				gradient = $(this).css('background-image');
				if(gradient != 'none'){
					color1 = gradient.split('rgb(')[1].split(')')[0];
					color2 = gradient.split('rgb(')[2].split(')')[0];
					newGradient = 'linear-gradient(rgb(' + color2 + ') 0px, rgb(' + color1 +') 100%)';
					$(this).css({'background': newGradient});
				}
			},function(){
				gradient = $(this).css('background-image');
				if(gradient != 'none'){
					color1 = gradient.split('rgb(')[1].split(')')[0];
					color2 = gradient.split('rgb(')[2].split(')')[0];
					newGradient = 'linear-gradient(rgb(' + color2 + ') 0px, rgb(' + color1 +') 100%)';
					$(this).css({'background': newGradient});
				}
			});
		}

		function mobileMenuHendler(){
			$('.o-header .o-header__main .menu_top .menuwrap .burger').click(function(){
				if($(this).hasClass('open')){
					$(this).removeClass('open');
					$('.o-header__secondary ul').removeClass('open');
				}else{
					$(this).addClass('open');
					$('.o-header__secondary ul').addClass('open');
				}
			});
		}

		function carouselPopularMatch(){
			if(window.matchMedia('(max-width: 767px)').matches){
				$('.o_sport_home_important_events .popularMatchCarousel .carousel-item').addClass('item');
				$('.o_sport_home_important_events .popularMatchCarousel').carousel();
			}
		}

		function carouselTabletPopularMatch(){
			if(window.matchMedia('(min-width: 767px) and (max-width: 992px)').matches){
				$('.o_sport_home_important_events .popularMatchCarousel .carousel-item').addClass('item');
				$('.o_sport_home_important_events .container').addClass("tablet");
				$('.o_sport_home_important_events .popularMatchCarousel .item').each(function(){
					var next = $(this).next();
					if (!next.length) {
						next = $(this).siblings(':first');
					}
					next.children(':first-child').clone().appendTo($(this));
				});
				$('.o_sport_home_important_events .popularMatchCarousel').carousel();
			}
		}

		function showStreamList(){
			$('.GP_stream .ShowStreamList .ShowStreamListButton').click(function(){
				$('.GP_stream .StreamPanel').css({'display':'block'});
				$('.GP_stream .ShowStreamList').css({'display':'none'});
			});
		}

		function eventCountdown(){
			date = $('.o-frontpage-hero_footbal .GP_head .sport_home .countdown #counter').attr('startdate');
			$(".o-frontpage-hero_footbal .GP_head .sport_home .countdown #counter").countdown(date, function(event) {
  				$(this).html(event.strftime('%-d D, %H h : %M m : %S s'));
  			}).on('finish.countdown', function(event) {
  				$(".o-frontpage-hero_footbal .GP_head .sport_home .countdown").css({"display": "none"});
  			});
		}

		function shortcodeCountdown(){
			date = $('.shortcodeCountdown').attr('startdate');
			$(".shortcodeCountdown").countdown(date, function(event) {
  				$(this).html(event.strftime('%-d D, %H h : %M m : %S s'));
  			}).on('finish.countdown', function(event) {
  				$(this).css({"display": "none"});
  			});
		}

		function languageSwitcher(){
			$( "body" ).on( "click", '.langSwitcher > a', function(event){
				if($(this).hasClass('open')){
					$(this).removeClass('open');
					$(this).parent().find('.language-switcher-language-extended-session').removeClass('active');
				}else{
					$(this).addClass('open');
					$(this).parent().find('.language-switcher-language-extended-session').addClass('active');
				}
			});

			$( "body" ).on( "click", ".langSwitcher .links a", function( event ){
				language = $(this).attr('data-drupal-link-query');

				if(language){
					Cookies.set('Land', JSON.parse(language).language, { expires: 365 });
				}
			});
		}

		languageSwitcher();
		shortcodeCountdown();
		eventCountdown();
		showStreamList();
		carouselTabletPopularMatch();
		carouselPopularMatch();
		mobileMenuHendler();
		buttonsHover();
		sportLiveStreamMobile();
		calendarTable();
		sheldueList();
		sportStreamTabs();
		// $(".panel-kamp").click(function () {
		// 	$('.panel-heading').removeClass( "collabseIN" );
		// 	var data = $(".in").attr('aria-labelledby');
		// 	$('#'+data).addClass( "collabseIN" );
		// 	console.log(data);
		// })

	});

})(jQuery);


