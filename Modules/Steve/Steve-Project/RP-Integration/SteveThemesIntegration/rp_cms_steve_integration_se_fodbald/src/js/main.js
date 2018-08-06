(function ($, Drupal) {
	Drupal.behaviors.myBehavior = {
	  attach: function (context, settings) {
			toTop();
			popUp();
			selectMatchBlock();
			lazyLoad();
			slider();
			customSelect();
			scheduleBlock();
			articleBlock();
		   
			function toTop(){
				$('a#toTop').click(function(e){
					e.preventDefault();
					$("html,body").animate({
						scrollTop: 0
					}, 150)
				}); 
			}
			
			function chunk(array, groupsize){
				var sets = [], chunks, i = 0;
				chunks = array.length / groupsize;
				
				while(i < chunks){
					sets[i] = array.splice(0, groupsize);
					i++;
				}
				
				return sets;
			}
			
			function slider(){
				$(".bxslider", context).once('myBehavior').bxSlider({
					auto: !0,
	    			autoControls: false,
	    			pager: false,
	    			controls: false
				});
			}
			
			function lazyLoad(){
				$('img').each(function(){
					if (typeof $(this).attr('data-src') !== typeof undefined && $(this).attr('data-src') !== false) {
						$(this).on('error', function(){
							$(this).attr('src', 'data:image/gif;base64,R0lGODlhAQABAIAAAP///wAAACH5BAEAAAAALAAAAAABAAEAAAICRAEAOw==');
						});
						$(this).attr('src', $(this).attr('data-src'));
	
						$(this).removeAttr("data-src");
					}			
				});
			}    
			    
			function popUp(){
				$('a.popUp').click(function(e){
					e.preventDefault();
					$($(this).attr('href')).addClass('Open');	
				});
				
				$('li.popUp a').click(function(e){
					e.preventDefault();
					$($(this).attr('href')).addClass('Open');	
				});
				
				$('.PopUpPlugin .popUpContainer').click(function(){
					$('.PopUpPlugin .popUpContainer').removeClass('Open');
				});
			}
			
			function articleBlock(){
				$(".ArticlesBlock .BlockContent ul.ArticlesList", context).once('myBehavior').paginate({autoScroll: false});
			}      
		    
			function customSelect(){
				$('.CustomSelect').each(function(){
					$(this).find('select').change(function(){
						$(this).parent().find('span').text($(this).find('option:selected').text());
					});
					$(this).parent().find('span').text($(this).find('select option:selected').text());
				});
			}    
	
			function selectMatchBlock(){
				$('.SelectMatch .select-match option').css('display', 'none');
				$('.SelectMatch .select-match option#' + $('.SelectMatch .select-league').val()).css('display', 'block');
				$('.SelectMatch .select-match option#' + $('.SelectMatch .select-league').val()).first().prop('selected', true);
				$('.SelectMatch a.btn').attr('href', $('.SelectMatch .select-match').val());
				$('.SelectMatch .select-match').parent().find('span').text($('.SelectMatch .select-match option:selected').text());
	
				$('.SelectMatch .select-league').change(function(){
					$('.SelectMatch .select-match option').css('display', 'none');
					$('.SelectMatch .select-match option#' + $(this).val()).css('display', 'block');
					$('.SelectMatch .select-match option#' + $(this).val()).first().prop('selected', true);
					$('.SelectMatch a.btn').attr('href', $('.SelectMatch .select-match').val());
					$('.SelectMatch .select-match').parent().find('span').text($('.SelectMatch .select-match option:selected').text());
				});
				
				$('.SelectMatch .select-match').change(function(){
					$('.SelectMatch a.btn').attr('href', $(this).val());	
				});
			}
			
			function scheduleBlock(){
				$('.ScheduleBlock').each(function(){
					position = 0;
					$schedule = $(this);
					$scheduleList = $schedule.find('.ScheduleList');
					pages = chunk($('.ScheduleTabs ul li.normal'), 6);
					
					pages.forEach(function(page, index){
						if(index != position){
							page.forEach(function(li){
								$(li).css('display', 'none');
							});
						}
					});
					
					var switchDay = function(day){
						$schedule.find('.ScheduleTabs ul li').removeClass('active');
						$schedule.find('.ScheduleTabs ul li.normal').each(function(){
							if($(this).attr('day') == day){
								$(this).addClass('active');
							}
						});
						
						if($schedule.find('.ScheduleSelect .CustomSelect select option[value="' + day + '"]')){
							$schedule.find('.ScheduleSelect .CustomSelect select option[value="' + day + '"]').prop('selected', true);
							$schedule.find('.ScheduleSelect .CustomSelect span').text($schedule.find('.ScheduleSelect .CustomSelect select option[value="' + day + '"]').text());
						}
						
						$scheduleList.find('.DayContainer').removeClass('active');
						$scheduleList.find('.DayContainer').each(function(){
							if($(this).attr('day') == day){
								$(this).addClass('active');
							}
						});
					}
					
					$schedule.find('.LeagueName').click(function(){
						if($schedule.hasClass('Widget')){
							console.log('test');
							$(this).parent().toggleClass('Open')
						}					
					});
					
					$schedule.find('select.date-select').change(function(){
						switchDay($(this).val());
					});
					
					
					$('.ScheduleTabs ul li').click(function(){
						if($(this).hasClass('normal')){
							if(!$(this).hasClass('active')){
								switchDay($(this).attr('day'));
							}
						}else if($(this).hasClass('next')){
							if(position + 1 <= pages.length - 1){
								position++;
								pages.forEach(function(page, index){
									if(index == position){
										page.forEach(function(li){
											$(li).css('display', 'inline-flex');
										});
									}else{
										page.forEach(function(li){
											$(li).css('display', 'none');
										});
									}
								});
							}
						}else if($(this).hasClass('prev')){
							if(position - 1 >= 0){
								position--;
								pages.forEach(function(page, index){
									if(index == position){
										page.forEach(function(li){
											$(li).css('display', 'inline-flex');
										});
									}else{
										page.forEach(function(li){
											$(li).css('display', 'none');
										});
									}
								});
							}
						}
					})	
				});		
			}   
		}
	}
})(jQuery, Drupal);