define([
    'jquery',
    'Simi_Simistorelocator/js/utilities'
], function($, MAGE_UTIL){
    var WeekdayTime = function(hourElement, minuteElemnt, callbackChange) {
        this.hourElement = hourElement;
        this.minuteElemnt = minuteElemnt;
        this.callbackChange = callbackChange;
        this._init();
    };

    WeekdayTime.prototype._init = function() {
        $(this.hourElement).change(this.change.bind(this));
        $(this.minuteElemnt).change(this.change.bind(this));
    }

    WeekdayTime.prototype.change = function() {
        if(typeof this.callbackChange == 'function') {
            this.callbackChange();
        }
    }

    /**
     * set current hour
     */
    WeekdayTime.prototype.setCurrentHour = function(hour) {
        if(MAGE_UTIL.arrayHours.indexOf(hour) !== -1) {
            $(this.hourElement).val(hour);
        }
    }

    /**
     * set current minute
     */
    WeekdayTime.prototype.setCurrentMinute = function(minute) {
        if(MAGE_UTIL.arrayMinutes.indexOf(minute) !== -1) {
            $(this.minuteElemnt).val(minute);
        }
    }

    WeekdayTime.prototype.getCurrentHour = function() {
        return $(this.hourElement).val();
    }

    WeekdayTime.prototype.getCurrentMinute = function() {
        return $(this.minuteElemnt).val();
    }

    WeekdayTime.prototype.setWeekdayTime = function(weekdayTime) {
        this.setCurrentHour(weekdayTime.getCurrentHour());
        this.setCurrentMinute(weekdayTime.getCurrentMinute());
        $(this.hourElement).trigger('change');
        $(this.minuteElemnt).trigger('change');
    }

    WeekdayTime.prototype.getStringTime = function() {
        return this.getCurrentHour() + ':' + this.getCurrentMinute();
    }

    WeekdayTime.prototype.hide = function() {
        $(this.hourElement).parent().parent().hide();

        $(this.hourElement).prop("disabled", true).addClass('ignore-validate');
        $(this.minuteElemnt).prop("disabled", true).addClass('ignore-validate');

    }

    WeekdayTime.prototype.show = function() {
        $(this.hourElement).parent().parent().show();

        $(this.hourElement).prop("disabled", false).removeClass('ignore-validate');
        $(this.minuteElemnt).prop("disabled", false).removeClass('ignore-validate');
    }

    return WeekdayTime;
});
