define([
    'Vnecoms_PageBuilder/js/section/default',
    'mageUtils',
    'jquery',
    'Vnecoms_PageBuilder/owlcarousel/owl.carousel'
], function (Element, utils, $) {
    'use strict';

    return Element.extend({
    	initSlider: function(){
    		var self = this;
    		setTimeout(function(){
    			var testimonial = $("#"+self.getSectionId()+' .testimonials-slider');
    			console.log($("#"+self.getSectionId()+' .testimonials-slider'));
    			console.log($("#"+self.getSectionId()+' .testimonials-slider'));
    			testimonial.owlCarousel({
                    nav: false,
                    dots: true,

                    responsive: {
                        0: {
                            items: 1
                        },
                        480: {
                            items: 1
                        },
                        768: {
                            items: 2
                        },
                        991: {
                            items: 3
                        },
                        1200: {
                            items: 3
                        }
                    }

                });
    		}, 1000);
    	}
    });
});
