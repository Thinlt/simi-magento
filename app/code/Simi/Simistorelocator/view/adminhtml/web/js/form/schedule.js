define([
    'jquery',
    'Simi_Simistorelocator/js/utilities',
    'Simi_Simistorelocator/js/weekdaytime'
], function($, MAGE_UTIL, WeekdayTime) {
    var weekdays = MAGE_UTIL.weekdays,
        listWeekdayTime = {};

    for(var i = 0, len = weekdays.length; i< len; ++i) {
        (function(){
            var open = new WeekdayTime(
                    '#schedule_'+weekdays[i]+'_open_hour',
                    '#schedule_'+ weekdays[i] +'_open_minute'
                ),
                openBreak = new WeekdayTime(
                    '#schedule_'+weekdays[i]+'_open_break_hour',
                    '#schedule_'+ weekdays[i] +'_open_break_minute'
                ),
                closeBreak = new WeekdayTime(
                    '#schedule_'+weekdays[i]+'_close_break_hour',
                    '#schedule_'+ weekdays[i] +'_close_break_minute'
                ),
                close = new WeekdayTime(
                    '#schedule_'+weekdays[i]+'_close_hour',
                    '#schedule_'+ weekdays[i] +'_close_minute'
                );

            open.callbackChange = function() {
                if(open.getStringTime() > openBreak.getStringTime()) {
                    openBreak.setWeekdayTime(open);
                }
            }

            openBreak.callbackChange = function() {
                if(openBreak.getStringTime() > closeBreak.getStringTime()) {
                    closeBreak.setWeekdayTime(openBreak);
                }
                if(openBreak.getStringTime() < open.getStringTime()) {
                    open.setWeekdayTime(openBreak);
                }
            }

            closeBreak.callbackChange = function() {
                if(closeBreak.getStringTime() > close.getStringTime()) {
                    close.setWeekdayTime(closeBreak);
                }
                if(closeBreak.getStringTime() < openBreak.getStringTime()) {
                    openBreak.setWeekdayTime(closeBreak);
                }
            }

            close.callbackChange = function() {
                if(close.getStringTime() < closeBreak.getStringTime()) {
                    closeBreak.setWeekdayTime(close);
                }
            }

            var weekdayWrapper = {
                open: open,
                openBreak: openBreak,
                closeBreak: closeBreak,
                close: close,
                status: '#schedule_'+ weekdays[i] +'_status'
            }

            listWeekdayTime[weekdays[i]] = weekdayWrapper;

        })();
    }

    $('#schedule-allpy-to-all').click(function() {
        var weekdaysTmp = weekdays.slice(1),
            monday = listWeekdayTime['monday'],
            modayStatus = $(monday.status).val();

        for(var i = 0, len = weekdaysTmp.length; i< len; ++i) {
            var weekdayWrapper = listWeekdayTime[weekdaysTmp[i]];
            $(weekdayWrapper.status).val(modayStatus);

            for(var time in weekdayWrapper) {
                if(time == 'status') {
                    continue;
                }
                weekdayWrapper[time].setWeekdayTime(monday[time]);
                if(modayStatus == 1) {
                    weekdayWrapper[time].show();
                } else {
                    weekdayWrapper[time].hide();
                }
            }
        }

    });
});
