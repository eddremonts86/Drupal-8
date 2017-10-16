(function($){

    $.fn.rpCarousel = function(isDestroy){
        if (isDestroy) {
            var timerId = this.data('timerId');
            clearInterval(timerId);

        } else {
            var currentIndex = 0;
            var slides = this.find('.carousel__slides__slide');
            var timerId = setInterval(function(){
                var currentSlide, nextSlide;

                currentSlide = $(slides.get(currentIndex));

                if (currentIndex === slides.length-1)
                    currentIndex = 0;
                else
                    currentIndex++;

                nextSlide = $(slides.get(currentIndex));

                currentSlide.animate({
                    'right': '300px'
                }, 250, function(){
                    currentSlide.css('right', '-300px');
                });

                nextSlide.animate({
                    'right': '0px'
                }, 250);

            }.bind(this), 5000);

            this.data('timerId', timerId);
        }
    };

    $(document).ready(function(){
        $('.carousel').rpCarousel();
    });

    $(window).on('beforeunload', function(){
        $('.carousel').rpCarousel(true);
    });

})(jQuery);
