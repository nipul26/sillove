define(['aos', 'jquery'], function (AOS, $) {
    'use strict';

    function applyAOS() {
        $('.aos-fade-up').attr('data-aos', 'fade-up');
        $('.aos-fade-left').attr('data-aos', 'fade-left');
        $('.aos-fade-right').attr('data-aos', 'fade-right');
        $('.aos-zoom-in').attr('data-aos', 'zoom-in');

        $('.delay-100').attr('data-aos-delay', '100');
        $('.delay-200').attr('data-aos-delay', '200');
        $('.delay-300').attr('data-aos-delay', '300');
        $('.delay-500').attr('data-aos-delay', '500');
    }

    $(window).on('load', function () {

        applyAOS();

        AOS.init({
            duration: 1000,
            easing: 'ease-out-cubic',
            once: true,
            offset: 80
        });

        // FIX: force recalculation after load
        setTimeout(function () {
            AOS.refreshHard();
        }, 500);

    });

    // Magento Page Builder dynamic reload fix
    document.addEventListener('contentUpdated', function () {
        applyAOS();
        AOS.refreshHard();
    });

    // EXTRA FIX (for sliders / lazy content)
    $(window).on('resize scroll', function () {
        AOS.refresh();
    });

});