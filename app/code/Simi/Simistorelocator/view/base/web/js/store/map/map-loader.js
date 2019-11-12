define(['jquery'], function($) {
    var google_maps_loaded_def = null;
    if (!google_maps_loaded_def) {

        google_maps_loaded_def = $.Deferred();

        window.google_maps_loaded = function() {
            google_maps_loaded_def.resolve(google.maps);
        }

        require(['https://maps.googleapis.com/maps/api/js?key='+window.googleMapApiKey+'&sensor=false&callback=google_maps_loaded&libraries=places,geometry'], function() {}, function(err) {
            google_maps_loaded_def.reject();
        });

    }

    return google_maps_loaded_def.promise();

});
