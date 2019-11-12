define([
    'jquery',
    'Simi_Simistorelocator/js/utilities',
    'Simi_Simistorelocator/js/weekdaytime'
], function($, MAGE_UTIL, WeekdayTime) {
    var openTime = new WeekdayTime(
            '#specialday_time_open_hour',
            '#specialday_time_open_minute'
        ),
        closeTime = new WeekdayTime(
            '#specialday_time_close_hour',
            '#specialday_time_close_minute'
        );

    openTime.callbackChange = function() {
        if(openTime.getStringTime() > closeTime.getStringTime()) {
            closeTime.setWeekdayTime(openTime);
        }
    }

    closeTime.callbackChange = function() {
        if(closeTime.getStringTime() < openTime.getStringTime()) {
            openTime.setWeekdayTime(closeTime);
        }
    }
});
