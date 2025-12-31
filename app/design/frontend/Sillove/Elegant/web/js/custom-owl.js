define([
    'jquery',
    'owlcarousel'
], function($){
    'use strict';

    return function(config, element){
        $(element).owlCarousel(config);
    };
});