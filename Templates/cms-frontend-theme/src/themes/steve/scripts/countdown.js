(function($){

    $.fn.countdown = function(isDestroy){
        if (isDestroy) {
            var timerId = this.data('timerId');
            clearInterval(timerId);

        } else {
            var count = 1;
            var timerId = setInterval(function(){
                // TODO use real stuff once date is set
                //var date = moment(parseInt(this.attr('data-value')));
                var date = moment().endOf('day'); // TODO TEMP
                this.text(date.subtract(count, 'seconds').format('h [h] : mm [m] : ss [s]'));
                count++;
            }.bind(this), 1000);

            this.data('timerId', timerId);
        }
    };

    $(document).ready(function(){
        $('.countdown').countdown();
    });

    $(window).on('beforeunload', function(){
        $('.countdown').countdown(true);
    });

})(jQuery);
