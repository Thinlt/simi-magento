
define([], function($){
    var MAGE_UTIL = {};
    window.MAGE_UTIL = MAGE_UTIL;
    MAGE_UTIL.str_pad = function (input, pad_length, pad_string, pad_type) {
        var half = '',
            pad_to_go;

        var str_pad_repeater = function(s, len) {
            var collect = '',
                i;

            while (collect.length < len) {
                collect += s;
            }
            collect = collect.substr(0, len);

            return collect;
        };

        input += '';
        pad_string = pad_string !== undefined ? pad_string : ' ';

        if (pad_type !== 'STR_PAD_LEFT' && pad_type !== 'STR_PAD_RIGHT' && pad_type !== 'STR_PAD_BOTH') {
            pad_type = 'STR_PAD_RIGHT';
        }
        if ((pad_to_go = pad_length - input.length) > 0) {
            if (pad_type === 'STR_PAD_LEFT') {
                input = str_pad_repeater(pad_string, pad_to_go) + input;
            } else if (pad_type === 'STR_PAD_RIGHT') {
                input = input + str_pad_repeater(pad_string, pad_to_go);
            } else if (pad_type === 'STR_PAD_BOTH') {
                half = str_pad_repeater(pad_string, Math.ceil(pad_to_go / 2));
                input = half + input + half;
                input = input.substr(0, pad_length);
            }
        }

        return input;
    }

    MAGE_UTIL.arrayHours = [];
    MAGE_UTIL.arrayMinutes = [];

    for(var h = 0; h < 24; ++h) {
        MAGE_UTIL.arrayHours.push(MAGE_UTIL.str_pad(h,2,0,'STR_PAD_LEFT'));
    }

    for(var m = 0; m < 60; ++m) {
        MAGE_UTIL.arrayMinutes.push(MAGE_UTIL.str_pad(m,2,0,'STR_PAD_LEFT'));
    }

    MAGE_UTIL.weekdays = [
        'monday',
        'tuesday',
        'wednesday',
        'thursday',
        'friday',
        'saturday',
        'sunday'
    ];


    return MAGE_UTIL;
});
