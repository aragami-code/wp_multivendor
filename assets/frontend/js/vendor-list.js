/* global wcmb_vendor_list_script_data */
(function ($) {
    
    // wcmb_vendor_list_script_data is required to continue, ensure the object exists
    if ( typeof wcmb_vendor_list_script_data === 'undefined' ) {
        return false;
    }

    var markers = [];
    var infoWindow;
    var map;
    var bounds;
    var init_options;
    var styles
    var styledMap
    
    function initialize() { 
	// Create a map object and specify the DOM element for display.
        var init_options = wcmb_vendor_list_script_data.map_data.map_options;
        if(wcmb_vendor_list_script_data.map_data.map_style == '1'){
            wcmb_vendor_list_script_data.map_data.map_options.mapTypeControlOptions.mapTypeIds.push('wcmb_map_style');
            var styles = wcmb_vendor_list_script_data.map_data.map_style_data;
            var styledMap = new google.maps.StyledMapType(styles, { name: wcmb_vendor_list_script_data.map_data.map_style_title });
        }
        var map = new google.maps.Map(document.getElementById("wcmb-vendor-list-map"), init_options);
        if(wcmb_vendor_list_script_data.map_data.map_style == '1'){
            map.mapTypes.set('wcmb_map_style', styledMap);
            map.setMapTypeId('wcmb_map_style');
        }
        
	infoWindow = new google.maps.InfoWindow();
        bounds = new google.maps.LatLngBounds();
	// Try HTML5 geolocation.
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function(position) {
                var pos = {
                    lat: position.coords.latitude,
                    lng: position.coords.longitude
                };
                // set center position
                $('#wcmb_vlist_center_lat').val(position.coords.latitude);
                $('#wcmb_vlist_center_lng').val(position.coords.longitude);

                map.setCenter(pos);
                map.fitBounds(bounds);
            }, function(error) {
                handleLocationError(true, infoWindow, map.getCenter(), error);
            });
        } else {
            // Browser doesnt support Geolocation
            handleLocationError(false, infoWindow, map.getCenter(), -1);
        }

        function handleLocationError(browserHasGeolocation, infoWindow, pos, error) {
            if( error == -1 ){
                alert( wcmb_vendor_list_script_data.lang.geolocation_doesnt_support );
            }else{
                switch( error.code ) {
                    case error.PERMISSION_DENIED:
                    alert( wcmb_vendor_list_script_data.lang.geolocation_permission_denied );
                    break;
                    case error.POSITION_UNAVAILABLE:
                    alert( wcmb_vendor_list_script_data.lang.geolocation_position_unavailable );
                    break;
                    case error.TIMEOUT:
                    alert( wcmb_vendor_list_script_data.lang.geolocation_timeout );
                    break;
                    case error.UNKNOWN_ERROR:
                    alert( wcmb_vendor_list_script_data.lang.geolocation_service_failed );
                    break;
                }
            }
        
            infoWindow.setPosition(pos);
            infoWindow.setContent(browserHasGeolocation ? wcmb_vendor_list_script_data.lang.geolocation_service_failed : wcmb_vendor_list_script_data.lang.geolocation_doesnt_support);
            infoWindow.open(map);
        }

	function createMarker(storeInfo) { 
            bounds.extend(new google.maps.LatLng(storeInfo.location.lat, storeInfo.location.lng));
            var marker = new google.maps.Marker({
                map: map,
                icon: wcmb_vendor_list_script_data.map_data.marker_icon,
                position: new google.maps.LatLng(storeInfo.location.lat, storeInfo.location.lng),
                title: storeInfo.store_name
            });
            google.maps.event.addListener(marker, "click", function() {
                infoWindow.setContent(storeInfo.info_html);
                infoWindow.open(map, marker);
            });
            markers.push(marker);
        }

	wcmb_vendor_list_script_data.stores.forEach(function(store){
            createMarker(store);
	}); 
        
        if( wcmb_vendor_list_script_data.autocomplete ) {
            var input = document.getElementById("locationText");
            var autocomplete = new google.maps.places.Autocomplete(input);
            autocomplete.addListener("place_changed", function() {
                var place = autocomplete.getPlace();
                if (!place.geometry) {
                    window.alert("Autocomplete returned place contains no geometry");
                    return;
                }
                // If the place has a geometry, then present it on a map.
                if (place.geometry.viewport) {
                    map.fitBounds(place.geometry.viewport);
                } else {
                    map.setCenter(place.geometry.location);
                    map.setZoom(17);
                }
                // set center position
                $('#wcmb_vlist_center_lat').val(place.geometry.location.lat());
                $('#wcmb_vlist_center_lng').val(place.geometry.location.lng());
                //place.geometry.location.lat(),place.geometry.location.lng()
            });
        } else {
            $('#locationText').on('input', function(){
                var value = $(this).val();
                var geocoder = new google.maps.Geocoder();
                geocoder.geocode({address: value}, function(results, status) {
                    if (status == google.maps.GeocoderStatus.OK) {
                        var res_location = results[0].geometry.location;
                        map.setCenter(res_location);
                        // set center position
                        $('#wcmb_vlist_center_lat').val(res_location.lat());
                        $('#wcmb_vlist_center_lng').val(res_location.lng());
                    } else {  
                    }
                });
            });
        }
    }
    
    google.maps.event.addDomListener(window, "load", initialize);
    
})(jQuery);

