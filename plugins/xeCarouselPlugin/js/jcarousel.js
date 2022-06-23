(function($) {
    $(function() {
        
        /* Crear un conector entre ambos carousels */
        var connector = function(itemNavigation, carouselStage) {
            return carouselStage.jcarousel('items').eq(itemNavigation.index());
        }
        
        /* Inicializar el carousel */
        var mainCarousel = $('.main-carousel').jcarousel();
        var navigationCarousel = $('.navigation-carousel').jcarousel();
        
        /* Inicializar la navegación */
        navigationCarousel.jcarousel('items').each(function() {
            var item = $(this);
            var target = connector(item, mainCarousel);

            item
                .on('jcarouselcontrol:active', function() {
                    navigationCarousel.jcarousel('scrollIntoView', this);
                    item.addClass('active');
                })
                .on('jcarouselcontrol:inactive', function() {
                    item.removeClass('active');
                })
                .jcarouselControl({
                    target: target,
                    carousel: mainCarousel
                });
        });
        
        /* Flecha "anterior" del carousel principal */
        $('.main-carousel-prev')
            .on('jcarouselcontrol:active', function() {
                $(this).removeClass('inactive');
            })
            .on('jcarouselcontrol:inactive', function() {
                $(this).addClass('inactive');
            })
            .jcarouselControl({
                target: '-=1'
            });

        /* Flecha "siguiente" del carousel principal */
        $('.main-carousel-next')
            .on('jcarouselcontrol:active', function() {
                $(this).removeClass('inactive');
            })
            .on('jcarouselcontrol:inactive', function() {
                $(this).addClass('inactive');
            })
            .jcarouselControl({
                target: '+=1'
            });
            
        /* Flecha "anterior" del carousel de navegación */
        $('.navigation-carousel-prev')
            .on('jcarouselcontrol:active', function() {
                $(this).removeClass('inactive');
            })
            .on('jcarouselcontrol:inactive', function() {
                $(this).addClass('inactive');
            })
            .jcarouselControl({
                target: '-=10'
            });

        /* Flecha "siguiente" del carousel de navegación  */
        $('.navigation-carousel-next')
            .on('jcarouselcontrol:active', function() {
                $(this).removeClass('inactive');
            })
            .on('jcarouselcontrol:inactive', function() {
                $(this).addClass('inactive');
            })
            .jcarouselControl({
                target: '+=10'
            });
            
        
        /* Contador */
        mainCarousel.on('jcarousel:fullyvisiblein', 'li', function(event, carousel) {
            var index = $(this).index() + 1;
            var $counter = $('.main-carousel-counter span');
            var total = parseInt($counter.attr('data-total'));
            $counter.html(index + ' de ' + total);
        });

        
        
    });
})(jQuery);