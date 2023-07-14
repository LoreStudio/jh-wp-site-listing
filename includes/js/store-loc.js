jQuery(document).ready(function($) {
	
	$( '.store-search-form' ).submit( function( e ) {
		console.log( 'search' )
		e.preventDefault( );
	})
	jQuery(document).on('click', '.store-location-btn', function(event){

		event.preventDefault();

		var box_id = $(this).data('id');
		var lat = $(this).data('lat');
		var lng = $(this).data('lng');							
		recenter_map_hover(lat, lng);
		// only for small devices E.g mobiles
		if (jQuery(window).width() < 1024) {
			 jQuery('html, body').animate({
				scrollTop: jQuery("#store-map").offset().top - 250
			}, 1000);
		}
		jQuery('.icon-img-'+box_id).trigger('click');
		//console.log('SCROLL:');
		//console.log('pid:'+box_id);
	});
	
	$( '#location_search_form' ).submit( function(e){
		e.preventDefault();
		search_location_near_by();
		return false;
	});
	
});
var markers = [];
var map, bounds;
var geocoder;
var reset_zoom = true;
var lat = 19.4326;
var lng = -99.1332;
var icon = '';
function getuser_location() {
    let is_map_exist = document.getElementById("store-map");
	if (navigator.geolocation) {
		navigator.geolocation.getCurrentPosition(
		  (position) => {
			const pos = {
			  lat: position.coords.latitude,
			  lng: position.coords.longitude,
			};
			jQuery('#place_lat').val(pos.lat);
			jQuery('#place_lng').val(pos.lng);
			infoWindow.setPosition(pos);
			/*infoWindow.setOptions({
				maxWidth:320,
				closeBoxMargin: "10px 20px 2px 2px",
				closeBoxURL: "http://www.google.com/intl/en_us/mapfiles/close.gif",
			});*/ 
			codeLatLng(pos.lat, pos.lng);
			search_location_near_by();
			map.setCenter(pos);
		  },
		  () => {
			//handleLocationError(true, infoWindow, map.getCenter());
			jQuery('#place_lat').val(lat);
			jQuery('#place_lng').val(lng);
			//search_location_near_by();
		  });
		
	  } else {
		 alert( "Geolocation is not supported by this browser.");
	  }
}
function set_user_position(position) {
    
	jQuery('#place_lat').val(position.coords.latitude);
	jQuery('#place_lng').val(position.coords.longitude);
}						
function initgMap() {
	console.log('JS LOG::');
	var map_exist = document.getElementById("store-map");
	if( map_exist ) {
		var latlng = new google.maps.LatLng(lat,lng);
		map = new google.maps.Map(document.getElementById("store-map"), {
			center: latlng,
			zoom: 12,
			mapTypeControl: false, // a way to quickly hide Statelite controls
			streetViewControl: false, // a way to quickly hide streetView controls
		});
		map.setOptions({styles: mapStyle});
		geocoder = new google.maps.Geocoder();	
		infoWindow = new google.maps.InfoWindow();
		const input = document.getElementById("search-text");	
		const autocomplete = new google.maps.places.Autocomplete(input);
		search_location_near_by();
		autocomplete.addListener("place_changed", () => {
		
			const place = autocomplete.getPlace();
		
			if (!place.geometry || !place.geometry.location) {
				// alert("No details available for input: '" + place.name + "'");
				return;
			}
			reset_zoom = true;
			if (place.geometry.location) {
				console.log('GEOMETRY:'+place.geometry.location);
				var latitude = place.geometry.location.lat();
				var longitude = place.geometry.location.lng();
				jQuery('#place_lat').val(latitude);
				jQuery('#place_lng').val(longitude);
				search_location_near_by();
			}
		});	
	}else{
		
		// just auto complete search
		const input = document.getElementById("search-text");	
		const autocomplete = new google.maps.places.Autocomplete(input);
		search_location_near_by();
		autocomplete.addListener("place_changed", () => {
			
			const place = autocomplete.getPlace();
		
			if (!place.geometry || !place.geometry.location) {
			  // alert("No details available for input: '" + place.name + "'");
			  return;
			}
			reset_zoom = true;
			if (place.geometry.location) {
				 console.log('GEOMETRY:'+place.geometry.location);
				 var latitude = place.geometry.location.lat();
				 var longitude = place.geometry.location.lng();
				 jQuery('#place_lat').val(latitude);
				 jQuery('#place_lng').val(longitude);
				 search_location_near_by();
			}
		});
		console.log('no map');
		//return false;
	}
}
// Geocode default lag lng to address
function codeLatLng(lat, lng) {
	var latlng = new google.maps.LatLng(lat, lng);
	geocoder.geocode({'latLng': latlng}, function(results, status) {
	  if (status == google.maps.GeocoderStatus.OK) {
	 // console.log(results)
		if (results[1]) {
		 //formatted address
		 jQuery('.pac-target-input').attr('placeholder', results[0].formatted_address);
		 //jQuery('.pac-target-input').val( results[0].formatted_address);
		 console.log(results[0].formatted_address)
		} else {
		  console.log("No results found");
		}
	  } else {
		console.log("Geocoder failed due to: " + status);
	  }
	});
}

function recenter_map_hover(lat,lng){
	initialLocation = new google.maps.LatLng(lat, lng);
	map.setCenter(initialLocation);

}

function search_location_near_by(){						
			
	var pos_lat 		= jQuery( '#place_lat' ).val() || '';
	var pos_lng 		= jQuery( '#place_lng' ).val() || '';
	var show_map 		= jQuery( '#show_map' ).val();
	var language_code 	= jQuery( '#language_code' ).val();

	/*if(pos_lat == '' || pos_lng == ''){
		alert('Please enter a valid address');
		return false;
	}*/
	removeOldMarkers();
	jQuery.ajax({
		url : front_object.ajax_url,
		type: 'POST',
		data: {
			action 		: 'search_location_near', 
			pos_lat		: pos_lat, 
			pos_lng		: pos_lng,
			lang		: language_code,
			show_map	: show_map,
		},
		dataType: 'json',
		beforeSend: function(){
			
	},
	}).done(function(response){ //							
		if(response.status){ 
			console.log('FOUND');
			var s_locations = [];
			if( show_map == 'yes' ) {											
				jQuery.each( response.all_locations, function( k, v ) {
					// only split has map										
					s_locations.push({
						"ID": 				this.ID,
						"name": 			this.name,
						"address": 			this.address,
						"lat": 				this.latitude,
						"long": 			this.longitude,
						"phone": 			this.phone,
						'show_direction': 	this.show_direction,
						'direction': 		this.direction,
						"provider_name": 	this.provider_name
					});				
				});
			}
			jQuery('#found-results').html(response.total_found);
			jQuery('#ajax_results_wrapper').html(response.content);	
			// show map
			if(show_map == 'yes'){
				searched_locations_map(s_locations);
			}
		}else{
			jQuery('#found-results').html('0');
			jQuery('#ajax_results_wrapper').html('Sorry no data found.');
			
		}					
		
	}).fail(function(jqXHR, textStatus) {								
			console.log( "Request failed: " + textStatus );
	});
}

function removeOldMarkers() {
	if(markers.length){				
	//Loop through all the markers and remove
	for (var i = 0; i < markers.length; i++) {
	markers[i].setMap(null);
	}
	markers = [];
	}
};
function searched_locations_map(locations){

	var infowindow =  new google.maps.InfoWindow({
		maxWidth: 328
	});
	bounds = new google.maps.LatLngBounds();
	popup_info_window(locations, bounds, infowindow);
   //map.setZoom(6);
}
	
function popup_info_window(locations, bounds, infowindow){
		var bounds = new google.maps.LatLngBounds();
		if( marker_icon != '' ){
			icon = {
				url: marker_icon, // url
				scaledSize: new google.maps.Size(42, 52), // scaled size
				//origin: new google.maps.Point(0,0), // origin
				//anchor: new google.maps.Point(5, 7) // anchor
			};
		}

		for ( count = 0; count < locations.length; count++ ) {		    
			position = new google.maps.LatLng(parseFloat(locations[count].lat), parseFloat(locations[count].long))
            const marker = new google.maps.Marker({
                position: position,
                map: map,
                icon: icon,
                title:  locations[count].name,               
            });

			markers.push(marker);
            bounds.extend(position);

            google.maps.event.addListener(marker, 'click', (function (marker, count) {
			    return function () {                    
				    var popInfo = '<div class="store-map-info-box">' +				
                        '<h5>'+locations[count].name+'</h5>' +
                        '<span class="store-location-address">'+locations[count].address+'</span>' +
						'<div class="store-location-phone"><a href="tel:'+locations[count].phone+'">'+locations[count].phone+'</a></div>';
						if ( locations[count].provider_name!='' ) {
							popInfo +='<span class="store-location-provider">'+locations[count].provider_name+'</span>';
						}
						if ( locations[count].show_direction ) {
							// popInfo +='<a href="https://www.google.com/maps?q='+parseFloat(locations[count].lat)+','+parseFloat(locations[count].long)+'" class="btn" target="_blank">Get Directions</a>';
							popInfo +='<a href="https://www.google.com/maps?q='+locations[count].address+'" class="get-directions-popup-link" target="_blank">'+locations[count].direction+'</a>';
						}
	                popInfo += '</div>';
	                console.log( locations )	
                    infowindow.setContent( popInfo );
                    infowindow.open(map, marker);

                    marker.setIcon( icon );
                }
            })(marker, count));
			
		google.maps.event.addListener(infowindow, 'closeclick', function(){
				marker.setIcon( icon );
			});
			
		google.maps.event.addListener(marker, 'click', function() {
				// remove all  previous hover image
				for (var i = 0; i < markers.length; i++) {
					markers[i].setIcon( icon);
				}
				
		});

        google.maps.event.addListener(map, 'click', function() {
				if (infowindow) {
					infowindow.close();
				}
			});
			 
        }
		if(!reset_zoom){
			console.log('no fit bounds');
			//map.fitBounds(bounds);
			var mbounds = map.getBounds();
			var update_count = 0;
			for(var i = 0; i < markers.length; i++){ // looping through my Markers Collection        
				if(mbounds.contains(markers[i].position)){
				 	//console.log("Marker"+ i +" - matched");
					var new_index = i+1;
					// record exist
					if(jQuery('#loc-'+new_index).length){
						update_count++;
						jQuery('#loc-'+new_index).addClass('exist-loc');
					}
				}
				jQuery('#found-results').html(update_count);
			}
			// remove not visibile
			jQuery('.loc-row').not('.loc-row.exist-loc').remove();

		}else{
			console.log('fit bounds');
			map.fitBounds(bounds);
			if( markers.length == 1){
				map.setZoom(14);
			}
		}	
		
		setTimeout(function(){
            console.log('add Class');
           jQuery(".gm-style").find('img').each(function( index ){
				//console.log('Index:'+index);
				if(typeof locations[index] !== 'undefined'){
					console.log('INDEX::'+locations[index].ID);
					jQuery("div[title=\"" + locations[index].name+ "\"]").addClass('marker-icon').addClass('icon-img-'+locations[index].ID);

                	//jQuery(this).find( "img" ).addClass('marker-icon').addClass('icon-img-'+locations[index].ID);
                	//jQuery(this).find( "img" ).parent().addClass('marker-icon-parent');
				}
			});
			
		}, 1500);
		
		//reset_zoom = true;
		google.maps.event.addListener(map, 'zoom_changed', function(e) {	
				if(!reset_zoom){
					//reset_zoom = true;
					console.log('no zoom reset');
					
				}else{
					//console.log('human'+e);			
					bounds = map.getBounds();
					//new_bounds = bounds;
					//console.log('zoom move bounds:'+bounds);
					//var NewMapCenter = map.getBounds().getCenter();
					var NewMapCenter = map.getCenter();
					console.log('Zoom map center:'+NewMapCenter);
					//var NewMapCenter = map.getCenter();			
					lat = NewMapCenter.lat();
					lng = NewMapCenter.lng();
					//jQuery('#place_lat').val(lat);
					//jQuery('#place_lng').val(lng);	
					var zoomLevel = map.getZoom();
					//search_location_near_by();			
					//console.log('New Lat and Lng'+ lat+', '+lng);	
				}
			});
		
  		/*
		map.dragInProgress = false;	
		google.maps.event.addListener(map, 'dragend', function() {
			  reset_zoom = false;
			  if(map.dragInProgress == false) {
					map.dragInProgress = true;
					window.setTimeout(function() {
						console.log('enable drag map redo search');			
						//bounds = map.getBounds();
						//new_bounds = bounds;
						//console.log('move bounds:'+bounds);
						var NewMapCenter = map.getCenter(); //map.getBounds().getCenter();
						console.log('view map center:'+NewMapCenter);
						//var NewMapCenter = map.getCenter();
						lat = NewMapCenter.lat();
						lng = NewMapCenter.lng();
						console.log('New Lat and Lng:'+ lat+', '+lng);	
						jQuery('#place_lat').val(lat);
						jQuery('#place_lng').val(lng);
						search_location_near_by();
						//cast your logic here
						map.dragInProgress = false; //reset the flag for next drag
					}, 1000);
				
			  }
		});
		*/
}