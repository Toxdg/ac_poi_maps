;(function($){
	$(document).ready(function(){	
		// change radio button
		$("#ac_poimaps_category-all > ul > li input").each(function(){
                    //$(this).prop('type', 'radio');
		});
		$("#taxonomy-ac_poimaps_category_region > div > ul > li input").each(function(){
                    //$(this).prop('type', 'radio');
		});
		$(".row-actions > span.inline").each(function(){
                    $(this).remove();
		});
                
		$("#exerpt").addClass("mceEditor");
			if ( typeof( tinyMCE ) == "object" && typeof( tinyMCE.execCommand ) == "function" ) {
				tinyMCE.execCommand("mceAddControl", false, "exerpt");
		}
		
		var map;
		var markersArray = [];
		
		
		var $myDiv = $('#map_canvas');
		if ($myDiv.length){
			initMap();
		}			
	
		function initMap()
		{
			var $latFld_set = $('#latFld').val();
			var $lngFld_set = $('#lngFld').val();

			var adress = "http://maps.googleapis.com/maps/api/geocode/json?address=";
			adress = $.getJSON( adress, {
				tagmode: "any",
				format: "json"
			});
			
			//console.log($latFld_set , $lngFld_set);
			
			if( $latFld_set != '' && $lngFld_set != ''){
				var latlng = new google.maps.LatLng($latFld_set, $lngFld_set);
				var myOptions = {
			        zoom: 10,
			        center: latlng,
			        mapTypeId: google.maps.MapTypeId.ROADMAP
			    };
			}else{
				getLocation();
				$latFld_set = $('#latFld').val();
				$lngFld_set = $('#lngFld').val();
				if( $latFld_set != '' && $lngFld_set != ''){
					var latlng = new google.maps.LatLng($latFld_set, $lngFld_set);
				}else{
					var latlng = new google.maps.LatLng(52.173931692568, 18.8525390625);
				}
				var myOptions = {
			        zoom: 5,
			        center: latlng,
			        mapTypeId: google.maps.MapTypeId.ROADMAP
			    };
			}
				    

		    
		    
		    map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
		    
		    // ustawiam pointer na starcie
			if( $latFld_set != '' && $lngFld_set != ''){
				placeMarker(latlng);
			}		    

		    // add a click event handler to the map object
		    google.maps.event.addListener(map, "click", function(event)
		    {
		        // place a marker
		        placeMarker(event.latLng);
		
		        // display the lat/lng in your form's lat/lng fields
		        document.getElementById("latFld").value = event.latLng.lat();
		        document.getElementById("lngFld").value = event.latLng.lng();
		    });
		}
		function placeMarker(location) {
		    // first remove all markers if there are any
		    deleteOverlays();

		    var marker = new google.maps.Marker({
		        position: location, 
		        map: map
		    });
		
		    // add marker in markers array
		    markersArray.push(marker);
		
		    //map.setCenter(location);
		}
		
		// Deletes all markers in the array by removing references to them
		function deleteOverlays() {
		    if (markersArray) {
		        for (i in markersArray) {
		            markersArray[i].setMap(null);
		        }
		    markersArray.length = 0;
		    }
		}
		
		
		// klikniecie w mapke
		$("#map_canvas").click(function(){	
			google.maps.event.addListener(map, "click", function(event)
		    {
		        // place a marker
		        placeMarker(event.latLng);
		
		        // display the lat/lng in your form's lat/lng fields
		        document.getElementById("latFld").value = event.latLng.lat();
		        document.getElementById("lngFld").value = event.latLng.lng();
		    });
		});
		
		// sprawdzenie adresu
		$( ".set_position" ).click(function() {
				var geocoder = new google.maps.Geocoder();
				var address = $('#adres').val();
				
				geocoder.geocode( { 'address': address}, function(results, status) {
				
				  if (status == google.maps.GeocoderStatus.OK) {
				    var latitude = results[0].geometry.location.lat();
				    var longitude = results[0].geometry.location.lng();
				    //console.log(latitude , longitude);
				    var latlng = new google.maps.LatLng(latitude, longitude);
				    			    
					var myOptions = {
				        zoom: 16,
				        center: latlng,
				        mapTypeId: google.maps.MapTypeId.ROADMAP
				    };
				    		    
				    map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
				    placeMarker(latlng);
					$('#latFld').val(latitude);
					$('#lngFld').val(longitude);
				  } 
				
				}); 
				//location.reload();
		});
		
				
		//add marker
                if ( $( ".marker_input" ).length ) {
                    $('#set-ico-button').click(function() {
                        formfield = $('#set-ico-button').attr('name');
                        tb_show('', 'media-upload.php?type=image&amp;TB_iframe=true');
                        return false;
                    });

                    window.send_to_editor = function(html) {
                     alert(html);
                     imgurl = $(html).children('img').attr('src');
                     alert(imgurl);
                     $('#marker').val(imgurl);
                     tb_remove();

                    }

                    $('#reset_ico').click(function() {
                            $('#marker').val('');
                            //$('#submit').click();
                    });
                }
		//geolocation
		function getLocation() {
			if (navigator.geolocation) {
				navigator.geolocation.getCurrentPosition(showPosition);
			} else {
				alert("Geolocation is not supported by this browser.");
			}
		}

		function showPosition(position) {
			$('#latFld').val(position.coords.latitude );
			$('#lngFld').val(position.coords.longitude);
			map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
		}
	});
})(jQuery);