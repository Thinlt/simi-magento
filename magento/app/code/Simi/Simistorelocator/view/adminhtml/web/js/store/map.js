define([
    'jquery',
    'Simi_Simistorelocator/js/store/map/map-loader',
    'mage/template',
    'jquery/ui',
], function($, Maploader, mageTemplate) {
    "use strict";
    $.widget('simi.gmap', {
        options: {
            latitude: 0,
            longitude: 0,
            zoom_level: 4,
            country_id: 'US',
            htmlPrefix: '',
            minZoom: 1,
            maxZoom: 20,
            marker_icon: '',
            searchBox: '',
            searchBtn: '',
            resetBtn: '',
            infoCurrentLocation: {},
        },
        _create: function() {
            this._initOption();
            this.infowindowTemplate = mageTemplate($('.map-template').html());

            try{
                Maploader.done($.proxy(this._initMap, this)).fail(function() {
                    console.error("ERROR: Google maps library failed to load");
                });
            } catch(e) {
                console.trace();
            }
        },
        _defaultOption:  function(name, defaultValue) {
            if(this.options[name] === '' || this.options[name] === null || typeof this.options[name] === 'undefined') {
                this.options[name] = defaultValue;
            }
        },
        _initOption: function () {
            this._defaultOption('latitude',0);
            this._defaultOption('longitude',0);
            this._defaultOption('zoom_level',4);
            this._defaultOption('country_id','US');
            this._defaultOption('minZoom',1);
            this._defaultOption('maxZoom',20);
        },
        _initMap: function() {
            var options  = this.options,
                centerPosition = new google.maps.LatLng(options.latitude, options.longitude);
            this.map = new google.maps.Map(this.element[0], {
                zoom: parseFloat(options.zoom_level),
                center: centerPosition,
                minZoom: options.minZoom,
                maxZoom: options.maxZoom
            });



            var markerOption = {
                map: this.map,
                draggable: true,
                position: centerPosition
            };

            if(options.marker_icon) {
                $.extend(markerOption, {
                    icon: {
                        url: options.marker_icon, // url
                        scaledSize: new google.maps.Size(30, 40), // scaled size
                        origin: new google.maps.Point(0,0), // origin
                        anchor: new google.maps.Point(0, 0) // anchor
                    }
                });
            }

            this.marker = new google.maps.Marker(markerOption);

            this.infowindow = new google.maps.InfoWindow();

            if($(options.searchBox).length > 0) {
                this.searchBox = new google.maps.places.SearchBox($(options.searchBox)[0]);
                $(options.searchBox).keyup($.proxy(function(event) {
                    if (event.keyCode === 13) {
                        this._inputsearchHandle();
                    }
                }, this));
                google.maps.event.addListener(this.searchBox, 'places_changed', $.proxy(this._inputsearchHandle, this));
                $(options.searchBox).show();
            }

            $(options.searchBtn).click($.proxy(this._inputsearchHandle, this));

            $(options.resetBtn).click($.proxy(this._resetMap, this));

            google.maps.event.addListener(this.map, 'bounds_changed', $.proxy(function() {
                $(options.input_zoom_level).val(this.map.getZoom());
            }, this));

            google.maps.event.addListener(this.marker, 'dragend', $.proxy(this._callbackGeoLocation, this));
            google.maps.event.addListener(this.map, 'click', $.proxy(this._callbackGeoLocation, this));
        },
        _resetMap: function () {
            var options  = this.options,
                centerPosition = new google.maps.LatLng(options.latitude, options.longitude);
            this.map.setCenter(centerPosition);
            this.map.setZoom(parseFloat(options.zoom_level));
            this.marker.setPosition(centerPosition);
            //this._getInfoLocation(centerPosition);
        },
        _getElement: function (id) {
            return $('#' + this.options.htmlPrefix + id);
        },
        _inputsearchHandle: function () {
            var geocoder = new google.maps.Geocoder();
            geocoder.geocode({
                'address': $(this.options.searchBox).val()
            }, this._handleGeoCoder.bind(this));
        },
        _handleGeoCoder: function(results, status) {
            if (status === google.maps.GeocoderStatus.OK) {
                this.map.setCenter(results[0].geometry.location);
                this.map.fitBounds(results[0].geometry.viewport);
            }
        },
        _callbackGeoLocation: function (event) {
            var latLng = event.latLng;
            this._getInfoLocation(latLng);
        },
        _getInfoLocation: function (latLng) {
            this.marker.setPosition(latLng);
            this.infowindow.open(this.map, this.marker);
            var geocoder = new google.maps.Geocoder();
            geocoder.geocode({
                latLng: latLng
            }, $.proxy(this._handleGeoLocation,this));
        },
        _handleGeoLocation: function(results, status) {
            var infoCurrentLocation = {
                address: "",
                city: "",
                state: "",
                zipcode: "",
                country_id: "",
                countryName: "",
                formattedAddress: "",
                latitude: this.marker.getPosition().lat(),
                longitude: this.marker.getPosition().lng(),
                zoom: this.map.getZoom(),
            }

            if (status === google.maps.GeocoderStatus.OK) {
                for (var i = 0, len = results[0].address_components.length; i < len; i++) {
                    var addr = results[0].address_components[i];
                    // check if this entry in address_components has a type of country
                    if (addr.types[0] == 'country') {
                        infoCurrentLocation.countryName = addr.long_name;
                        infoCurrentLocation.country_id = addr.short_name;
                    } else if (addr.types[0] == 'street_address') // address 1
                        infoCurrentLocation.address = infoCurrentLocation.address + addr.long_name;
                    else if (addr.types[0] == 'establishment')
                        infoCurrentLocation.address = infoCurrentLocation.address + addr.long_name;
                    else if (addr.types[0] == 'route') // address 2
                        infoCurrentLocation.address = infoCurrentLocation.address + addr.long_name;
                    else if (addr.types[0] == 'postal_code') // Zip
                        infoCurrentLocation.zipcode = addr.short_name;
                    else if (addr.types[0] == ['administrative_area_level_1']) // State
                        infoCurrentLocation.state = addr.long_name;
                    else if (addr.types[0] == ['locality']) // City
                        infoCurrentLocation.city = addr.long_name;
                }

                if (results[0].formatted_address != null) {
                    infoCurrentLocation.formattedAddress = results[0].formatted_address;
                }
            }
            this.infoCurrentLocation = infoCurrentLocation;
            this._updateInforWindow(infoCurrentLocation);
            $(this.options.input_latitude).val(infoCurrentLocation.latitude);
            $(this.options.input_longitude).val(infoCurrentLocation.longitude);

            google.maps.event.addListener(this.marker, 'click', $.proxy(function() {
                this.infowindow.open(this.map, this.marker);
            }, this));

        },

        _updateInforWindow: function (infoLocation) {
            this.infowindow.setContent(this.infowindowTemplate({data: infoLocation}));
            $(this.options.applyToFormBtn).click($.proxy(function () {
                this._updateFormInput(infoLocation);
            },this));
        },

        _updateFormInput: function (infoLocation) {
            var options = this.options;

            $(options.input_country_id).val(infoLocation.country_id);
            $(options.input_country_id).trigger('change');

            if(!$('#state_id').prop('disabled')) {
                $('#state_id').find('option').filter(function () {
                    return this.text === infoLocation.state;
                }).prop('selected',true);
            } else {
                $('#state').val(infoLocation.state);
            }

            $(options.input_address).val(infoLocation.formattedAddress);
            $(options.input_city).val(infoLocation.city);
            $(options.input_zipcode).val(infoLocation.zipcode);

            $('html, body').animate({scrollTop: 100}, 'slow');
        },
    });

    return $.simi.gmap;
});
