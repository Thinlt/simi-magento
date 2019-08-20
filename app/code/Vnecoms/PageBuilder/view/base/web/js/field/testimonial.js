define([
    'jquery',
    './list-keys',
    'uiLayout',
    'mageUtils',
    'mage/translate',
    'jquery/colorpicker/js/colorpicker',
    'Vnecoms_PageBuilder/owlcarousel/owl.carousel'
], function ($, Element, layout, utils, $t) {
    'use strict';

    return Element.extend({
        defaults: {
        	color: '',
        	colorHover: '',
        	resetSliderFlag: false,
            listens: {
            	elems: 'resetSlider'
            }
        },
        
        initSlider: function(){
    		var self = this;
    		setTimeout(function(){
    			self.resetSliderFlag = true;
    			self.initOwlCarousel();
    		}, 1000);
    	},
    	
    	initOwlCarousel: function(){
    		var testimonial = $("#"+this.getPreviewFieldId()+' .testimonials-slider');
    		var items = $("#"+this.getPreviewFieldId()+'-items');
    		testimonial.trigger('destroy.owl.carousel');
    		testimonial.html(items.html());
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
    	},
    	elementChanged: function(){
    		this._super();
    		this.resetSlider();
    	},
        resetSlider: function(){
        	if(this.resetSliderFlag){
        		var testimonial = $("#"+this.getPreviewFieldId()+' .testimonials-slider');
    			var self = this;
    			setTimeout(function(){
        			self.initOwlCarousel();
        		}, 500);
        	}
        }
    });
});
