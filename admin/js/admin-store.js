(function ($) {

    $(function () {

        // Trigger geocode
        $(document).on('click', '#geo_code_btn', function () {
            var address = $('#store_address').val() || '';
            let address2 = $('#address_2').val();
            let city = $('#store_city').val() || '';
            let state = $('#store_state').val() || '';
            let zipcode = $('#store_zipcode').val() || '';
            if (address == '') {
                //alert( 'Please enter address');
                //return false;
            }
            if (address2 != '') {
                address += ' ' + address2;
            }
            if (city != '' && !address.toLowerCase().includes(city.toLowerCase())) {
                address += ' ' + city;
            }
            if (state != '' && !address.toLowerCase().includes(' ' + state.toLowerCase())) {
                address += ', ' + state;
            }
            if (zipcode != '' && !address.toLowerCase().includes(' ' + zipcode.toLowerCase())) {
                address += ', ' + zipcode;
            }
            console.log('ADD::' + address);

            var geocoder = new google.maps.Geocoder();
            geocoder.geocode({'address': address}, function (results, status) {
                if (status == google.maps.GeocoderStatus.OK) {
                    // console.log('GEO:'+JSON.stringify(results[0]));
                    var lat = results[0].geometry.location.lat;
                    var lng = results[0].geometry.location.lng;
                    //var latlng = results[0].geometry.location;
                    $('#map_lat').val(lat);
                    $('#map_lng').val(lng);
                } else {
                    alert("Geocode was not successful for the following reason: " + status);
                }
            });
        });
    });

})(jQuery);

/**
 * Import submit
 */
function importSubmit(form) {
    if (!form) {
        return;
    }

    let input = document.querySelector('input[type="file"]');
    let btn = document.querySelector('.import-btn');

    btn.addEventListener('click', e => {
        e.preventDefault();
        input.click();
    });

    input.addEventListener('change', e => {
        e.preventDefault();
        form.submit();
    });
}

importSubmit(document.querySelector('.import-location-form'));
