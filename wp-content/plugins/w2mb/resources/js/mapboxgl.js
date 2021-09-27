/*if (w2mb_js_objects.is_maps_used) {
	var w2mb_3rd_party_maps_plugin = false;
	var _warn = console.warn,
	    _error = console.error;
	console.error = function() {
	    if (typeof arguments[0].message != "undefined" && arguments[0].message) {
	    	alert(arguments[0].message);
	    }
	    return _error.apply(console, arguments);
	};
}*/

var w2mb_draws = [];
var w2mb_draw_features = [];

//mapboxgl_edit.js -------------------------------------------------------------------------------------------------------------------------------------------
(function($) {
	"use strict";

	window.w2mb_load_maps_api_backend = function() {
		if ($("#w2mb_map_starting_point_metabox").length) {
			$("#w2mb_map_starting_point_metabox .inside .vp-metabox").append("<div id='w2mb-starting-point-metabox-map-canvas' style='width: auto; height: 450px'></div>");
			
			var start_zoom = 1;
			if ($("select[name='w2mb_map_starting_point[start_zoom]']").val() > 0) {
				start_zoom = parseInt($("select[name='w2mb_map_starting_point[start_zoom]']").val());
			}
			
			mapboxgl.accessToken = w2mb_maps_objects.mapbox_api_key;
			var starting_point_map = new mapboxgl.Map({
				container: "w2mb-starting-point-metabox-map-canvas",
				style: w2mb_maps_objects.map_style
			});
			if (w2mb_js_objects.is_rtl) {
				mapboxgl.setRTLTextPlugin('https://api.mapbox.com/mapbox-gl-js/plugins/mapbox-gl-rtl-text/v0.1.0/mapbox-gl-rtl-text.js');
			}
			var options = {};
			if (w2mb_js_objects.lang) {
				options = { defaultLanguage:  w2mb_js_objects.lang}; 
			}
			starting_point_map.addControl(new MapboxLanguage(options))
			var navigationControl = new mapboxgl.NavigationControl({
		        showCompass: false,
		        showZoom: true,
		    });
			starting_point_map.addControl(navigationControl);
			
			var start_latitude = 47.651968;
			var start_longitude = 9.478485;
			if ($("input[name='w2mb_map_starting_point[start_latitude]']").val()) {
				start_latitude = $("input[name='w2mb_map_starting_point[start_latitude]']").val();
			}
			if ($("input[name='w2mb_map_starting_point[start_longitude]']").val()) {
				start_longitude = $("input[name='w2mb_map_starting_point[start_longitude]']").val();
			}
			
			var starting_point_marker = new mapboxgl.Marker({
				draggable: true
			})
    		.setLngLat([start_longitude, start_latitude])
    		.addTo(starting_point_map);
			
			starting_point_marker.on('drag', function() {
				var point = starting_point_marker.getLngLat();
				if (point !== undefined) {
					$("input[name='w2mb_map_starting_point[start_latitude]']").val(point.lat);
					$("input[name='w2mb_map_starting_point[start_longitude]']").val(point.lng);
				}
			});
	
			starting_point_map.on('zoom', function() {
				var start_zoom = starting_point_map.getZoom();
				if (start_zoom >= 1 && start_zoom <= 19) {
					$("select[name='w2mb_map_starting_point[start_zoom]']").val(Math.round(start_zoom)).trigger("change");
				}
			});
	
			starting_point_map.setZoom(start_zoom);
			starting_point_map.setCenter([start_longitude, start_latitude]);
		}
		
		if ($("#w2mb-maps-canvas").length) {
			mapboxgl.accessToken = w2mb_maps_objects.mapbox_api_key;
			w2mb_map_backend = new mapboxgl.Map({
			    container: "w2mb-maps-canvas",
			    style: w2mb_maps_objects.map_style
			});
			if (w2mb_js_objects.is_rtl) {
				mapboxgl.setRTLTextPlugin('https://api.mapbox.com/mapbox-gl-js/plugins/mapbox-gl-rtl-text/v0.1.0/mapbox-gl-rtl-text.js');
			}
			var options = {};
			if (w2mb_js_objects.lang) {
				options = { defaultLanguage:  w2mb_js_objects.lang}; 
			}
			w2mb_map_backend.addControl(new MapboxLanguage(options))
			var navigationControl = new mapboxgl.NavigationControl({
		        showCompass: false,
		        showZoom: true,
		    });
			w2mb_map_backend.addControl(navigationControl);

			if (w2mb_isAnyLocation_backend()) {
				w2mb_generateMap_backend();
			} else {
				w2mb_map_backend.setCenter([w2mb_maps_objects.default_longitude, w2mb_maps_objects.default_latitude]);
			}

			w2mb_map_backend.on('zoom', function() {
				if (w2mb_allow_map_zoom_backend) {
					$(".w2mb-map-zoom").val(Math.round(w2mb_map_backend.getZoom()));
				}
			});
			
		}
		w2mb_setupAutocomplete();
	}

	window.w2mb_setupAutocomplete = function() {
		$(".w2mb-listing-field-autocomplete").listing_address_autocomplete();
	}

	function w2mb_setMapCenter_backend(w2mb_coords_array_1, w2mb_coords_array_2) {
		var count = 0;
		var bounds = new mapboxgl.LngLatBounds();
		for (count == 0; count<w2mb_coords_array_1.length; count++)  {
			bounds.extend([w2mb_coords_array_2[count], w2mb_coords_array_1[count]]);
		}
		if (count == 1) {
			// required workaround: first zoom, then setCenter for initial load when single marker
			if ($(".w2mb-map-zoom").val() == '' || $(".w2mb-map-zoom").val() == 0) {
				var zoom_level = 1;
			} else {
				var zoom_level = parseInt($(".w2mb-map-zoom").val());
			}
			
			// allow/disallow map zoom in listener, this option needs because w2mb_map.setZoom() also calls this listener
			w2mb_allow_map_zoom_backend = false;
			w2mb_map_backend.setZoom(zoom_level);
			w2mb_allow_map_zoom_backend = true;
			
			w2mb_map_backend.setCenter([w2mb_coords_array_2[0], w2mb_coords_array_1[0]]);
		} else {
			w2mb_map_backend.fitBounds(bounds, {padding: 50, duration: 0});
		}
	}
	
	var w2mb_coords_array_1 = new Array();
	var w2mb_coords_array_2 = new Array();
	var w2mb_attempts = 0;
	window.w2mb_generateMap_backend = function() {
		w2mb_ajax_loader_target_show($("#w2mb-maps-canvas"));
		w2mb_coords_array_1 = new Array();
		w2mb_coords_array_2 = new Array();
		w2mb_attempts = 0;
		w2mb_clearOverlays_backend();
		w2mb_geocodeAddress_backend(0);
	}
	
	function w2mb_setFoundPoint(point, location_obj, i) {
		$(".w2mb-map-coords-1:eq("+i+")").val(point.lat);
		$(".w2mb-map-coords-2:eq("+i+")").val(point.lng);
		var map_coords_1 = point.lat;
		var map_coords_2 = point.lng;
		w2mb_coords_array_1.push(map_coords_1);
		w2mb_coords_array_2.push(map_coords_2);
		location_obj.setPoint(point);
		location_obj.w2mb_placeMarker();
		w2mb_geocodeAddress_backend(i+1);

		if ((i+1) == $(".w2mb-location-in-metabox").length) {
			w2mb_setMapCenter_backend(w2mb_coords_array_1, w2mb_coords_array_2);
			w2mb_ajax_loader_target_hide("w2mb-maps-canvas");
		}
	}

	window.w2mb_geocodeAddress_backend = function(i) {
		if ($(".w2mb-location-in-metabox:eq("+i+")").length) {
			var locations_drop_boxes = [];
			$(".w2mb-location-in-metabox:eq("+i+")").find("select").each(function(j, val) {
				if ($(this).val())
					locations_drop_boxes.push($(this).children(":selected").text());
			});
	
			var location_string = locations_drop_boxes.reverse().join(', ');
	
			if ($(".w2mb-manual-coords:eq("+i+")").is(":checked") && $(".w2mb-map-coords-1:eq("+i+")").val()!='' && $(".w2mb-map-coords-2:eq("+i+")").val()!='' && ($(".w2mb-map-coords-1:eq("+i+")").val()!=0 || $(".w2mb-map-coords-2:eq("+i+")").val()!=0)) {
				var map_coords_1 = $(".w2mb-map-coords-1:eq("+i+")").val();
				var map_coords_2 = $(".w2mb-map-coords-2:eq("+i+")").val();
				if ($.isNumeric(map_coords_1) && $.isNumeric(map_coords_2)) {
					var point = new mapboxgl.LngLat(map_coords_2, map_coords_1);
					w2mb_coords_array_1.push(map_coords_1);
					w2mb_coords_array_2.push(map_coords_2);
	
					var location_obj = new w2mb_glocation_backend(i, point, 
						location_string,
						$(".w2mb-address-line-1:eq("+i+")").val(),
						$(".w2mb-address-line-2:eq("+i+")").val(),
						$(".w2mb-zip-or-postal-index:eq("+i+")").val(),
						$(".w2mb-map-icon-file:eq("+i+")").val()
					);
					location_obj.w2mb_placeMarker();
				}
				w2mb_geocodeAddress_backend(i+1);
				if ((i+1) == $(".w2mb-location-in-metabox").length) {
					w2mb_setMapCenter_backend(w2mb_coords_array_1, w2mb_coords_array_2);
					w2mb_ajax_loader_target_hide("w2mb-maps-canvas");
				}
			} else if (location_string || $(".w2mb-address-line-1:eq("+i+")").val() || $(".w2mb-address-line-2:eq("+i+")").val() || $(".w2mb-zip-or-postal-index:eq("+i+")").val()) {
				var location_obj = new w2mb_glocation_backend(i, null, 
					location_string,
					$(".w2mb-address-line-1:eq("+i+")").val(),
					$(".w2mb-address-line-2:eq("+i+")").val(),
					$(".w2mb-zip-or-postal-index:eq("+i+")").val(),
					$(".w2mb-map-icon-file:eq("+i+")").val()
				);

				// Geocode by address
				function _w2mb_geocodeAddress_backend(status, lat, lng) {
					if (status) {
						w2mb_setFoundPoint(new mapboxgl.LngLat(lng, lat), location_obj, i);
					} else {
						alert("Sorry, we are unable to geocode address (address line: "+location_obj.compileAddress()+" #"+(i)+")");
						w2mb_ajax_loader_target_hide("w2mb-maps-canvas");
					}
				}

				w2mb_geocodeAddress(location_obj.compileAddress(), _w2mb_geocodeAddress_backend, w2mb_maps_objects.address_autocomplete_code);
			} else
				w2mb_ajax_loader_target_hide("w2mb-maps-canvas");
		}
	}

	window.w2mb_placeMarker_backend = function(w2mb_glocation) {
		
		// dragging does not work on mobile devices for PNG richtext markers
		var marker_options = {
				anchor: 'bottom',
				draggable: true
		};
		var marker = new mapboxgl.Marker(marker_options)
	    .setLngLat(w2mb_glocation.point)
	    .addTo(w2mb_map_backend);
		
		w2mb_markersArray_backend.push(marker);
		
		marker.on('drag', function() {
			var point = marker.getLngLat();
			if (point !== undefined) {
				var selected_location_num = w2mb_glocation.index;
				$(".w2mb-manual-coords:eq("+w2mb_glocation.index+")").prop("checked", true);
				$(".w2mb-manual-coords:eq("+w2mb_glocation.index+")").parents(".w2mb-manual-coords-wrapper").find(".w2mb-manual-coords-block").show(200);
				
				$(".w2mb-map-coords-1:eq("+w2mb_glocation.index+")").val(point.lat);
				$(".w2mb-map-coords-2:eq("+w2mb_glocation.index+")").val(point.lng);
			}
		});
	}

	function w2mb_clearOverlays_backend() {
		if (w2mb_markersArray_backend) {
			for(var i = 0; i<w2mb_markersArray_backend.length; i++){
				w2mb_markersArray_backend[i].remove();
			}
		}
	}
	
	function w2mb_isAnyLocation_backend() {
		var is_location = false;
		$(".w2mb-location-in-metabox").each(function(i, val) {
			var locations_drop_boxes = [];
			$(this).find("select").each(function(j, val) {
				if ($(this).val()) {
					is_location = true;
					return false;
				}
			});
	
			if ($(".w2mb-manual-coords:eq("+i+")").is(":checked") && $(".w2mb-map-coords-1:eq("+i+")").val()!='' && $(".w2mb-map-coords-2:eq("+i+")").val()!='' && ($(".w2mb-map-coords-1:eq("+i+")").val()!=0 || $(".w2mb-map-coords-2:eq("+i+")").val()!=0)) {
				is_location = true;
				return false;
			}
		});
		if (is_location)
			return true;
	
		if ($(".w2mb-address-line-1[value!='']").length != 0)
			return true;
	
		if ($(".w2mb-address-line-2[value!='']").length != 0)
			return true;
	
		if ($(".w2mb-zip-or-postal-index[value!='']").length != 0)
			return true;
	}
})(jQuery);

(function($) {
	"use strict";
	
	window.w2mb_buildPoint = function(lat, lng) {
		return [lng, lat];
	}
	window.w2mb_getLat = function(point) {
		return point[1];
	}
	window.w2mb_getLng = function(point) {
		return point[0];
	}

	window.w2mb_buildBounds = function() {
		return new mapboxgl.LngLatBounds();
	}

	window.w2mb_extendBounds = function(bounds, point) {
		bounds.extend(point);
	}

	window.w2mb_mapFitBounds = function(map_id, bounds) {
		var left_offset = 50;
		var right_offset = 50;
		if ($("#w2mb-maps-canvas-"+map_id).hasClass("w2mb-sidebar-open") /*&& screen.width >= w2mb_js_objects.mobile_screen_width*/) {
			if (w2mb_js_objects.is_rtl) {
				right_offset = 365;
			} else {
				left_offset = 365;
			}
			//console.log(left_offset);
		}
		w2mb_maps[map_id].fitBounds(bounds, {padding: {top: 50, bottom: 50, left: left_offset, right: right_offset}, duration: 0});
	}

	window.w2mb_getMarkerPosition = function(marker) {
		return marker.getLngLat();
	}

	window.w2mb_closeInfoWindow = function(map_id) {
		if (typeof w2mb_infoWindows[map_id] != 'undefined') {
			w2mb_infoWindows[map_id].remove();
			w2mb_infoWindows[map_id].location = null;
			// Removes hash from URL
			history.pushState("", document.title, window.location.pathname + window.location.search);
		}
	}
	
	class w2mb_point {
		constructor(lng, lat) {
			this.coord_1 = lng;
			this.coord_2 = lat;
		}
		lng() {
			return this.coord_1;
		}
		lat() {
			return this.coord_2;
		}
	}

	window.w2mb_setAjaxMarkersListener = function(map_id) {
		w2mb_setMapAjaxListener(w2mb_maps[map_id], map_id);
	}
	
	window.w2mb_geocodeAddress = function(address, callback, address_autocomplete_code) {
		if (typeof address_autocomplete_code != 'undefined' && address_autocomplete_code != '0')
			var country = '&country='+address_autocomplete_code;
		else
			var country = '';

		$.get("https://api.mapbox.com/geocoding/v5/mapbox.places/"+encodeURIComponent(address)+".json?access_token="+w2mb_maps_objects.mapbox_api_key+country, function(data) {
			if (data.features.length) {
				// data.features[0].geometry.coordinates[0] - longitude
				// data.features[0].geometry.coordinates[1] - latitude
				callback(true, data.features[0].geometry.coordinates[1], data.features[0].geometry.coordinates[0]);
			} else {
				callback(false, 0, 0);
			}
		}).fail(function() {
			callback(false, 0, 0);
		});
	}
	
	window.w2mb_callMapResize = function(map_id) {
		w2mb_maps[map_id].resize();
	}

	window.w2mb_setMapCenter = function(map_id, center) {
		w2mb_maps[map_id].setCenter(center);
	}
	
	window.w2mb_setMapZoom = function(map_id, zoom) {
		w2mb_maps[map_id].setZoom(parseInt(zoom));
	}

	window.w2mb_autocompleteService = function(term, address_autocomplete_code, common_array, response, callback) {
		if (address_autocomplete_code != '0')
			var country = '&country='+address_autocomplete_code;
		else
			var country = '';
		
		if (w2mb_js_objects.lang)
			var language = '&language='+w2mb_js_objects.lang;
		else
			var language = '';

		var output_predictions = [];
		$.get("https://api.mapbox.com/geocoding/v5/mapbox.places/"+encodeURIComponent(term)+".json?access_token="+w2mb_maps_objects.mapbox_api_key+country+language, function(data) {
			$.map(data.features, function (prediction, i) {
				var output_prediction = {
						label: prediction.text,
						value: prediction.place_name,
						name: prediction.place_name,
						sublabel: prediction.place_name.replace(prediction.text + ", ", ""),
				};
				output_predictions.push(output_prediction);
			});
			
			callback(output_predictions, common_array, response);
		}).fail(function() {
			callback(output_predictions, common_array, response);
		});
	}
	
	function w2mb_addPolygon(map_id) {
		var map = w2mb_maps[map_id];
		
		map.addSource('geo-poly-'+map_id, {
			'type': 'geojson',
			'data': w2mb_draw_features[map_id]
		});
		map.addLayer({
			'id': 'geo-poly-'+map_id,
			'type': 'fill',
			'source': 'geo-poly-'+map_id,
			'layout': {},
			'paint': {
				'fill-color': '#0099FF',
				'fill-opacity': 0.3,
				'fill-outline-color': '#AA2143'
			}
		});

		w2mb_polygons[map_id] = true;
	}
	
	function w2mb_drawFreeHandPolygon(map_id) {
		var geojson = {
				"type": "FeatureCollection",
				"features": []
		};

		var linestring = {
				"type": "Feature",
				"geometry": {
					"type": "LineString",
					"coordinates": []
				}
		};
		
		var map = w2mb_maps[map_id];

		map.addSource('geo-lines', {
			"type": "geojson",
			"data": geojson
		});

	    map.addLayer({
	        id: 'geo-lines',
	        type: 'line',
	        source: 'geo-lines',
	        layout: {
	            'line-cap': 'round',
	            'line-join': 'round'
	        },
	        paint: {
	            'line-color': '#AA2143',
	            'line-width': 2
	        },
	        filter: ['in', '$type', 'LineString']
	    });

	    var draw_move_event = function(e) {
	    	var features = map.queryRenderedFeatures(e.point, { layers: ['geo-lines'] });

	        // Remove the linestring from the group
	        // So we can redraw it based on the points collection
	        if (geojson.features.length > 1) geojson.features.pop();

	        var point = {
	        		"type": "Feature",
	                "geometry": {
	                    "type": "Point",
	                    "coordinates": [
	                        e.lngLat.lng,
	                        e.lngLat.lat
	                    ]
	                },
	                "properties": {
	                    "id": String(new Date().getTime())
	                }
	        };

	        geojson.features.push(point);

	        if (geojson.features.length > 1) {
	            linestring.geometry.coordinates = geojson.features.map(function(point) {
	                return point.geometry.coordinates;
	            });

	            geojson.features.push(linestring);
	        }

	        map.getSource('geo-lines').setData(geojson);
	    };
	    map.on('mousemove', draw_move_event);
	    map.on('touchmove', draw_move_event);
	    
	    var draw_up_event = function(e) {
	    	map.off('mousemove', draw_move_event);
	    	map.off('touchmove', draw_move_event);
	    	map.removeLayer('geo-lines');
	    	map.removeSource('geo-lines');

	    	var theArrayofLngLat = [];
	    	linestring.geometry.coordinates.map(function(point_feature) {
	    		theArrayofLngLat.push(new w2mb_point(point_feature[0], point_feature[1]));
	    	});
			var ArrayforPolygontoUse = w2mb_GDouglasPeucker(theArrayofLngLat, 1);
			
			var geo_poly_json = [];
			var geo_poly_ajax = [];
			if (ArrayforPolygontoUse.length) {
				var lat_lng;
				for (lat_lng in ArrayforPolygontoUse) {
					geo_poly_json.push([ArrayforPolygontoUse[lat_lng].lng(), ArrayforPolygontoUse[lat_lng].lat()]);
					geo_poly_ajax.push({ 'lat': ArrayforPolygontoUse[lat_lng].lat(), 'lng': ArrayforPolygontoUse[lat_lng].lng() });
				}
				geo_poly_json.push([ArrayforPolygontoUse[0].lng(), ArrayforPolygontoUse[0].lat()]);
			}

			if (geo_poly_json.length) {
				w2mb_sendGeoPolyAJAX(map_id, geo_poly_ajax);
				
				var geo_poly_feature = {
						'id': 'geo-poly-feature-'+map_id,
						'type': 'Feature',
						'properties': {},
		                'geometry': {
		                    'type': 'Polygon',
		                    'coordinates': [geo_poly_json]
		                }
				};
				
				w2mb_draw_features[map_id] = geo_poly_feature;
				
				w2mb_addPolygon(map_id);
				
				var editButton = $(map.getContainer()).find('.w2mb-map-edit').get(0);
				$(editButton).removeAttr('disabled');
			}
			var drawButton = $(map.getContainer()).find('.w2mb-map-draw').get(0);
			drawButton.drawing_state = 0;
			window.removeEventListener('touchmove', w2mb_stop_touchmove_listener, { passive: false });
			map.getCanvas().style.cursor = '';
			$(drawButton).removeClass('w2mb-btn-active');
			w2mb_disableDrawingMode(map_id);
	    };
		map.once('mouseup', draw_up_event); 
		map.once('touchend', draw_up_event); 
	}
	function w2mb_enableDrawingMode(map_id) {
		$(w2mb_maps[map_id].getContainer()).find('.w2mb-map-custom-controls').hide();
		// if sidebar was not opened - hide search field
		if (!w2mb_isSidebarOpen(map_id) && $('#w2mb-map-search-wrapper-'+map_id).length) {
			$('#w2mb-map-search-wrapper-'+map_id).hide();
		}
		var map = w2mb_maps[map_id];

		map.scrollZoom.disable();
		map.dragRotate.disable();
		map.touchZoomRotate.disable();
		map.dragPan.disable();
	}
	function w2mb_disableDrawingMode(map_id) {
		var map = w2mb_maps[map_id];

		$(map.getContainer()).find('.w2mb-map-custom-controls').show();
		if ($('#w2mb-map-search-wrapper-'+map_id).length) $('#w2mb-map-search-wrapper-'+map_id).show();

		var attrs_array = w2mb_get_map_markers_attrs_array(map_id);
		var enable_wheel_zoom = attrs_array.enable_wheel_zoom;
		var enable_dragging_touchscreens = attrs_array.enable_dragging_touchscreens;
		if (enable_dragging_touchscreens || !('ontouchstart' in document.documentElement)) {
			map.dragRotate.enable();
			map.dragPan.enable();
			map.touchZoomRotate.enable();
		}
		if (enable_wheel_zoom) {
			map.scrollZoom.enable();
		}
	}
	
	window.w2mb_setMapZoomCenter = function(map_id, map_attrs, markers_array) {
		if (typeof map_attrs.start_zoom != 'undefined' && map_attrs.start_zoom > 0)
			var zoom_level = map_attrs.start_zoom;
	    else if (markers_array.length == 1)
			var zoom_level = markers_array[0][6];
		else if (markers_array.length > 1)
			// fitbounds does not need zoom
			var zoom_level = false;
		else
			var zoom_level = 2;
	
	    if (typeof map_attrs.start_latitude != 'undefined' && map_attrs.start_latitude && typeof map_attrs.start_longitude != 'undefined' && map_attrs.start_longitude) {
			var start_latitude = map_attrs.start_latitude;
			var start_longitude = map_attrs.start_longitude;
			if (zoom_level == false) {
				zoom_level = 12;
			}
			// required workaround: first zoom, then setCenter
			w2mb_setMapZoom(map_id, zoom_level);
			w2mb_setMapCenter(map_id, [start_longitude, start_latitude]);
			
			if (typeof map_attrs.ajax_loading != 'undefined' && map_attrs.ajax_loading == 1) {
			    // use closures here
			    w2mb_setMapAjaxListener(w2mb_maps[map_id], map_id);
			}
	    } else if (typeof map_attrs.start_address != 'undefined' && map_attrs.start_address) {
	    	// use closures here
	    	w2mb_geocodeStartAddress(map_attrs, map_id, zoom_level);
	    } else if (markers_array.length == 1) {
	    	w2mb_setMapZoom(map_id, zoom_level);
	    } else if (zoom_level && markers_array.length == 0) {
			// no fitbounds here
			// required workaround: first zoom, then setCenter for initial load when single marker
		    w2mb_setMapZoom(map_id, zoom_level);
		    w2mb_setMapCenter(map_id, [w2mb_maps_objects.default_longitude, w2mb_maps_objects.default_latitude]);
		}
    }

	function w2mb_load_maps() {
		for (var i=0; i<w2mb_map_markers_attrs_array.length; i++) {
			if (typeof w2mb_maps[w2mb_map_markers_attrs_array[i].map_id] == 'undefined') { // workaround for "tricky" themes and plugins to load maps twice
				w2mb_load_map(i);
			}
		}
		
		w2mb_geolocatePosition();
	}

	window.w2mb_load_maps_api = function() {
		$(document).trigger('w2mb_mapbox_api_loaded');

		// are there any markers?
		if (typeof w2mb_map_markers_attrs_array != 'undefined' && w2mb_map_markers_attrs_array.length) {
			_w2mb_map_markers_attrs_array = JSON.parse(JSON.stringify(w2mb_map_markers_attrs_array));

			w2mb_load_maps();
		}
		
		$(".w2mb-listing-field-autocomplete").listing_address_autocomplete();
		
		$(document).on('click', '.w2mb-show-on-map, .w2mb-listing-location', function() {
			var location_id = $(this).data("location-id");
			var map_id = $(this).parents(".w2mb-maps-canvas-wrapper").data("id");

			w2mb_showInfoWindowByLocationId(location_id);
		});
	}
	
	window.w2mb_getDirections = function(origin, destination, map_id) {
		if (origin) {
			function _geocoderOrigin(status, latitude, longitude) {
				if (status) {
					
					var map = w2mb_maps[map_id];
					var destination_array = destination.split(',');
					var language = '';
					if (w2mb_js_objects.lang) {
						language = '&language='+w2mb_js_objects.lang;
					}
					var url = 'https://api.mapbox.com/directions/v5/mapbox/driving-traffic/' + longitude + ',' + latitude + ';' + destination_array[1] + ',' + destination_array[0] + '?steps=true&geometries=geojson&access_token=' + w2mb_maps_objects.mapbox_api_key + language;
					
					$.get(url, function(data) {
						
						var route = data.routes[0].geometry.coordinates;
						var geojson = {
								type: 'Feature',
								properties: {},
								geometry: {
									type: 'LineString',
									coordinates: route
								}
						};
						// if the route already exists on the map, reset it using setData
						if (map.getSource('route')) {
							map.getSource('route').setData(geojson);
						} else { // otherwise, make a new request
							var _map = map.addLayer({
								id: 'route',
								type: 'line',
								source: {
									type: 'geojson',
									data: {
										type: 'Feature',
										properties: {},
										geometry: {
											type: 'LineString',
											coordinates: geojson
										}
									}
								},
								layout: {
									'line-join': 'round',
									'line-cap': 'round'
								},
								paint: {
									'line-color': '#3887be',
									'line-width': 5,
									'line-opacity': 0.75
								}
							});
							map.getSource('route').setData(geojson);
						}
						
						var end = {
								type: 'FeatureCollection',
								features: [{
									type: 'Feature',
									properties: {},
									geometry: {
										type: 'Point',
										coordinates: [longitude, latitude]
									}
								}]
						};
						if (map.getLayer('end')) {
							map.getSource('end').setData(end);
						} else {
							map.addLayer({
								id: 'end',
								type: 'circle',
								source: {
									type: 'geojson',
									data: {
										type: 'FeatureCollection',
										features: [{
											type: 'Feature',
											properties: {},
											geometry: {
												type: 'Point',
												coordinates: [longitude, latitude]
											}
										}]
									}
								},
								paint: {
									'circle-radius': 10,
									'circle-color': '#f30'
								}
							});
						}
						
						var instructions = document.getElementById('w2mb-route-container-'+map_id);
						var steps = data.routes[0].legs[0].steps;

						var tripInstructions = [];
						for (var i = 0; i < steps.length; i++) {
							tripInstructions.push('<li>' + steps[i].maneuver.instruction + (steps[i].distance ? ' ' + Math.floor(steps[i].distance) + ' ' + w2mb_js_objects.directions_meters_label : '')) + '</li>';
							instructions.innerHTML = '<p class="w2mb-route-duration">' + w2mb_js_objects.directions_distance_label + ': ' + Math.floor(data.routes[0].distance/1000) + ' ' + w2mb_js_objects.directions_kilometers_label + '</p>' + tripInstructions;
						}
						
						w2mb_nice_scroll();
					}).fail(function() {
						
					});;
				}
			}
			w2mb_geocodeAddress(origin, _geocoderOrigin);
		}
	}

	window.w2mb_showInfoWindowByLocationId = function(location_id, map_id) {
		var local_w2mb_maps = [];
		if (typeof  map_id != "undefined") {
			local_w2mb_maps[map_id] = w2mb_maps[map_id];
		} else {
			local_w2mb_maps = w2mb_maps;
		}
		
		for (var map_id in w2mb_maps) {
			if (typeof w2mb_global_locations_array[map_id] != 'undefined') {
				for (var i=0; i<w2mb_global_locations_array[map_id].length; i++) {
					if (typeof w2mb_global_locations_array[map_id][i] == 'object') {
						if (location_id == w2mb_global_locations_array[map_id][i].id) {
							var location_obj = w2mb_global_locations_array[map_id][i];
							/*var side_offset = 0;
							if ($("#w2mb-maps-canvas-"+map_id).hasClass("w2mb-sidebar-open")) {
								if (w2mb_js_objects.is_rtl) {
									side_offset = 200;
								} else {
									side_offset = -200;
								}
							}*/
							if (!location_obj.is_ajax_markers) {
								w2mb_applyZoomOnClick(map_id);
								w2mb_maps[map_id].panToWithOffset(location_obj.marker.getLngLat(), 0, -100);
								w2mb_setInfoWindow(location_obj, location_obj.marker, map_id, 'bottom', 'onbuttonclick');
							} else {
								var old_zoom = w2mb_maps[map_id].getZoom();
								var new_zoom = w2mb_applyZoomOnClick(map_id);
								if ((new_zoom && new_zoom != old_zoom) || w2mb_isCenterOnClick(map_id)) {
									w2mb_setMapCenter(map_id, location_obj.marker.position);
								}
								w2mb_showInfoWindowAJAXMarker(location_obj, location_obj.marker, map_id, true);
							}
						
							/*var location_obj = w2mb_global_locations_array[map_id][i];
							if (!location_obj.is_ajax_markers) {
								w2mb_applyZoomOnClick(map_id);
								w2mb_showInfoWindow(location_obj, location_obj.marker, map_id);
								//w2mb_panByInfoWindow(map_id);
								w2mb_setMapCenter(map_id, location_obj.marker.position);
							} else {
								var old_zoom = w2mb_maps[map_id].getZoom();
								var new_zoom = w2mb_applyZoomOnClick(map_id);
								if ((new_zoom && new_zoom != old_zoom) || w2mb_isCenterOnClick(map_id)) {
									w2mb_setMapCenter(map_id, location_obj.marker.position);
								}
								w2mb_showInfoWindowAJAXMarker(location_obj, location_obj.marker, map_id, true);
							}*/
						}
					}
				}
			}
		}
	}

	document.addEventListener("DOMContentLoaded", function() {
		if (w2mb_maps_callback.callback) {
			window[w2mb_maps_callback.callback]();
		}
	});
	
	window.w2mb_countLocations = function(map_id) {
		if (typeof w2mb_locations_counters[map_id] != undefined) {
			var counter = $(w2mb_locations_counters[map_id]).find(".w2mb-map-locations-counter").get(0);
			$(counter).html(w2mb_global_locations_array[map_id].length);
		}
    }

	window.w2mb_load_map = function(i) {
		var map_id = w2mb_map_markers_attrs_array[i].map_id;
		var markers_array = w2mb_map_markers_attrs_array[i].markers_array;
		var radius_circle = w2mb_map_markers_attrs_array[i].radius_circle;
		var clusters = w2mb_map_markers_attrs_array[i].clusters;
		var counter = w2mb_map_markers_attrs_array[i].counter;
		var counter_text = w2mb_map_markers_attrs_array[i].counter_text;
		var show_directions_button = w2mb_map_markers_attrs_array[i].show_directions_button;
		var map_style = w2mb_map_markers_attrs_array[i].map_style;
		var draw_panel = w2mb_map_markers_attrs_array[i].draw_panel;
		var show_readmore_button = w2mb_map_markers_attrs_array[i].show_readmore_button;
		var enable_full_screen = w2mb_map_markers_attrs_array[i].enable_full_screen;
		var enable_full_screen_by_default = w2mb_map_markers_attrs_array[i].enable_full_screen_by_default;
		var enable_wheel_zoom = w2mb_map_markers_attrs_array[i].enable_wheel_zoom;
		var enable_dragging_touchscreens = w2mb_map_markers_attrs_array[i].enable_dragging_touchscreens;
		var directions_panel = w2mb_map_markers_attrs_array[i].directions_panel;
		var map_attrs = w2mb_map_markers_attrs_array[i].map_attrs;
		
		w2mb_sticky_scroll();
		
		if (document.getElementById("w2mb-maps-canvas-"+map_id)) {
			if (typeof w2mb_fullScreens[map_id] == "undefined" || !w2mb_fullScreens[map_id]) {
				
				if (typeof w2mb_maps[map_id] != 'undefined') {
					w2mb_maps[map_id].remove();
				}
				
				mapboxgl.accessToken = w2mb_maps_objects.mapbox_api_key;
				var map = new mapboxgl.Map({
				    container: "w2mb-maps-canvas-"+map_id,
				    style: map_style
				});
				
				if (w2mb_js_objects.is_rtl) {
					if (mapboxgl.getRTLTextPluginStatus() != 'loaded') {
						mapboxgl.setRTLTextPlugin('https://api.mapbox.com/mapbox-gl-js/plugins/mapbox-gl-rtl-text/v0.1.0/mapbox-gl-rtl-text.js');
					}
				}
				var options = {};
				if (w2mb_js_objects.lang) {
					options = { defaultLanguage:  w2mb_js_objects.lang}; 
				}
				map.addControl(new MapboxLanguage(options));
				
				if (!enable_wheel_zoom) {
					map.scrollZoom.disable();
				}
				
				if (!enable_dragging_touchscreens) {
					map.dragRotate.disable();
					map.dragPan.disable();
					map.touchZoomRotate.disable();
				}

				w2mb_maps[map_id] = map;
			    w2mb_maps_attrs[map_id] = map_attrs;
			    
			    if (directions_panel) {
			    	
			    	if (w2mb_maps_objects.dimension_unit == "kilometers") {
			    		var dimension_unit = "metric";
			    	} else {
			    		var dimension_unit = "imperial";
			    	}
			    	
					var directions = new MapboxDirections({
					    accessToken: mapboxgl.accessToken,
					    language: w2mb_js_objects.lang,
					    geocoder: {
							language: w2mb_js_objects.lang
						},
						unit: dimension_unit,
						placeholderOrigin: w2mb_maps_objects.mapbox_directions_placeholder_origin,
						placeholderDestination: w2mb_maps_objects.mapbox_directions_placeholder_destination
					}).on('profile', function() {
						$('label[for="mapbox-directions-profile-driving-traffic"]').text(w2mb_maps_objects.mapbox_directions_profile_driving_traffic);
						$('label[for="mapbox-directions-profile-driving"]').text(w2mb_maps_objects.mapbox_directions_profile_driving);
						$('label[for="mapbox-directions-profile-walking"]').text(w2mb_maps_objects.mapbox_directions_profile_walking);
						$('label[for="mapbox-directions-profile-cycling"]').text(w2mb_maps_objects.mapbox_directions_profile_cycling);
					});
					
					if (w2mb_js_objects.is_rtl) {
						var cposition = 'top-right';
					} else {
						var cposition = 'top-left';
					}
					map.addControl(directions, cposition);
					
					w2mb_geocodeField($("#mapbox-directions-destination-input input"), w2mb_maps_objects.my_location_button_error);
				}

			    class w2mb_custom_controls {
					  onAdd(map){
					    this.map = map;

					    var customControls = document.createElement('div');
					    $(customControls).addClass('mapboxgl-ctrl w2mb-map-custom-controls');
					    $(customControls).html('<div class="w2mb-btn-group"><button class="w2mb-btn w2mb-btn-primary w2mb-map-btn-zoom-in"><span class="w2mb-glyphicon w2mb-glyphicon-plus"></span></button><button class="w2mb-btn w2mb-btn-primary w2mb-map-btn-zoom-out"><span class="w2mb-glyphicon w2mb-glyphicon-minus"></span></button></div> <div class="w2mb-btn-group">'+(enable_full_screen ? '<button class="w2mb-btn w2mb-btn-primary w2mb-map-btn-fullscreen"><span class="w2mb-glyphicon w2mb-glyphicon-fullscreen"></span></button>' : '')+'</div>');
					    
					    this.container = customControls;
					    return this.container;
					  }
					  onRemove(){
					    this.container.parentNode.removeChild(this.container);
					    this.map = undefined;
					  }
				}
				var customControls = new w2mb_custom_controls();
				
				if (w2mb_js_objects.is_rtl) {
					var cposition = 'top-left';
				} else {
					var cposition = 'top-right';
				}
				map.addControl(customControls, cposition);
				
				$(customControls.container).find('.w2mb-map-btn-zoom-in').on("click", function() {
			    	w2mb_maps[map_id].zoomIn();
			    });
			    $(customControls.container).find('.w2mb-map-btn-zoom-out').on("click", function() {
			    	w2mb_maps[map_id].zoomOut();
			    });
			    
			    var interval;
			    var mapDiv = w2mb_maps[map_id].getContainer();
			    var mapDivParent = $(mapDiv).parent().parent();
			    var divStyle = mapDiv.style;
			    if (mapDiv.runtimeStyle)
			        divStyle = mapDiv.runtimeStyle;
			    var originalPos = divStyle.position;
			    var originalWidth = divStyle.width;
			    var originalHeight = divStyle.height;
			    // ie8 hack
			    if (originalWidth === "")
			        originalWidth = mapDiv.style.width;
			    if (originalHeight === "")
			        originalHeight = mapDiv.style.height;
			    var originalTop = divStyle.top;
			    var originalLeft = divStyle.left;
			    var originalZIndex = divStyle.zIndex;
			    var bodyStyle = document.body.style;
			    if (document.body.runtimeStyle)
			        bodyStyle = document.body.runtimeStyle;
			    var originalOverflow = bodyStyle.overflow;
			    var thePanoramaOpened = false;

			    var elements_to_zindex = [
		                                  "#w2mb-map-search-wrapper-"+map_id,
		                                  "#w2mb-map-search-panel-wrapper-"+map_id,
		                                  "#w2mb-map-sidebar-toggle-container-"+map_id,
		                                  "#w2mb-map-directions-panel-wrapper-"+map_id,
		        ];
			    
			    if (enable_full_screen_by_default) {
				    w2mb_fullScreens[map_id] = true;
				    openFullScreen();
			    }

			    function openFullScreen() {
			    	mapDivParent.after("<div id='w2mb-map-placeholder-"+map_id+"'></div>");
			    	mapDivParent.appendTo('body');
			    	
			    	var center = w2mb_maps[map_id].getCenter();
			        mapDiv.style.position = "fixed";
			        mapDiv.style.width = "100%";
			        mapDiv.style.height = "100%";
			        mapDiv.style.top = "0";
			        mapDiv.style.left = "0";
			        mapDiv.style.zIndex = "100000";
			        $(mapDiv).parent(".w2mb-maps-canvas-wrapper").css({ 'overflow': 'initial',  'z-index': 100000});
			        document.body.style.overflow = "hidden";
			        $(customControls.container).find('.w2mb-map-btn-fullscreen span').removeClass('w2mb-glyphicon-fullscreen');
			        $(customControls.container).find('.w2mb-map-btn-fullscreen span').addClass('w2mb-glyphicon-resize-small');
			        
			        w2mb_callMapResize(map_id);
			        w2mb_setMapCenter(map_id, center);
			        
			        $(elements_to_zindex).each(function() {
			        	if ($(this).length) {
			        		var zindex = ($(this).css('z-index')) ? $(this).css('z-index') : 0;
			        		if (zindex !== false) {
			        			if ($(document).width() > 768) {
			        				$(this).css('position', 'fixed');
			        			}
			        			$(this).css('z-index', parseInt(zindex) + 100000);
			        		}
			        	}
			        });
			        
			        $(window).trigger('resize');
			        if ($(".w2mb-map-listings-panel").length) {
			        	$(".w2mb-map-listings-panel").getNiceScroll().resize();
			        }
			    }
			    function closeFullScreen() {
			    	$('#w2mb-map-placeholder-'+map_id).after(mapDivParent);
			    	$('#w2mb-map-placeholder-'+map_id).detach();
			    	
		            if (originalPos === "") {
		                mapDiv.style.position = "relative";
		            } else {
		                mapDiv.style.position = originalPos;
		            }
		            var center = w2mb_maps[map_id].getCenter();
		            mapDiv.style.width = originalWidth;
		            mapDiv.style.height = originalHeight;
		            mapDiv.style.top = originalTop;
		            mapDiv.style.left = originalLeft;
		            mapDiv.style.zIndex = originalZIndex;
		            $(mapDiv).parent(".w2mb-maps-canvas-wrapper").css({ 'overflow': 'hidden',  'z-index': originalZIndex });
		            document.body.style.overflow = originalOverflow;
		            $(customControls.container).find('.w2mb-map-btn-fullscreen span').removeClass('w2mb-glyphicon-resize-small');
			        $(customControls.container).find('.w2mb-map-btn-fullscreen span').addClass('w2mb-glyphicon-fullscreen');

			        w2mb_callMapResize(map_id);
			        w2mb_setMapCenter(map_id, center);
			        
			        $(elements_to_zindex).each(function() {
			        	if ($(this).length) {
			        		var zindex = ($(this).css('z-index')) ? $(this).css('z-index') : 100000;
			        		if (zindex) {
			        			if ($(document).width() > 768) {
			        				$(this).css('position', 'absolute');
			        			}
			        			$(this).css('z-index', parseInt(zindex) - 100000);
			        		}
			        	}
			        });
		            
			        $(window).trigger('resize');
			        if ($(".w2mb-map-listings-panel").length) {
			        	$(".w2mb-map-listings-panel").getNiceScroll().resize();
			        }
			    }
			    if (enable_full_screen) {
			    	$(customControls.container).find('.w2mb-map-btn-fullscreen').on("click", function() {
				    	if (typeof w2mb_fullScreens[map_id] == "undefined" || !w2mb_fullScreens[map_id]) {
				    		$("#w2mb-maps-canvas-wrapper-"+map_id).addClass("w2mb-map-full-screen");
				    		w2mb_fullScreens[map_id] = true;
				    		openFullScreen();
				    	} else {
				    		$("#w2mb-maps-canvas-wrapper-"+map_id).removeClass("w2mb-map-full-screen");
				    		w2mb_fullScreens[map_id] = false;
				    		closeFullScreen();
				    	}
				    });
				    $(document).on("keyup", function(e) {
				    	if (typeof w2mb_fullScreens[map_id] != "undefined" && w2mb_fullScreens[map_id] && e.keyCode == 27) {
				    		$("#w2mb-maps-canvas-wrapper-"+map_id).removeClass("w2mb-map-full-screen");
				    		w2mb_fullScreens[map_id] = false;
				    		closeFullScreen();
				    	}
				    });
			    }

			    if (draw_panel) {
				    var drawPanel = document.createElement('div');
				    $(drawPanel).addClass('w2mb-map-draw-panel');

				    class w2mb_dummy_control {
				    	constructor(drawPanel) {
				    		this.drawPanel = drawPanel;
				    	}
				    	onAdd(map){
						    this.map = map;

						    var customControls = document.createElement('div');
						    $(customControls).addClass('mapboxgl-ctrl w2mb-map-draw-panel');
						    customControls.appendChild(this.drawPanel);
						    
						    this.container = customControls;
						    return this.container;
						  }
						  onRemove(){
						    this.container.parentNode.removeChild(this.container);
						    this.map = undefined;
						  }
					}
					var dummyDiv = new w2mb_dummy_control(drawPanel);
					map.addControl(dummyDiv, cposition);

				    var drawButton = document.createElement('button');
				    $(drawButton)
				    .addClass('w2mb-btn w2mb-btn-primary w2mb-map-draw')
				    .attr("title", w2mb_maps_objects.draw_area_button)
				    .html('<span class="w2mb-glyphicon w2mb-glyphicon-pencil"></span>');
				    
				    drawPanel.appendChild(drawButton);
				    drawButton.map_id = map_id;
					drawButton.drawing_state = 0;
					$(drawButton).on("click", function(e) {
						var map_id = drawButton.map_id;
						if (this.drawing_state == 0) {
							this.drawing_state = 1;
							window.addEventListener('touchmove', w2mb_stop_touchmove_listener, { passive: false });
							w2mb_clearMarkers(map_id);
							w2mb_closeInfoWindow(map_id);
							w2mb_removeShapes(map_id);
		
							w2mb_enableDrawingMode(map_id);
							
							var editButton = $(w2mb_maps[map_id].getContainer()).find('.w2mb-map-edit').get(0);
							$(editButton).removeClass('w2mb-btn-active');
							$(editButton).attr('disabled', 'disabled');
							$(editButton).find('.w2mb-map-edit-label').text(w2mb_maps_objects.edit_area_button);
							editButton.editing_state = 0;
		
							// remove ajax_loading and set drawing_state
							var map_attrs_array;
							if (map_attrs_array = w2mb_get_map_markers_attrs_array(map_id)) {
								map_attrs_array.map_attrs.drawing_state = 1;
								delete map_attrs_array.map_attrs.ajax_loading;
							}
			
							w2mb_maps[map_id].getCanvas().style.cursor = 'crosshair';
							$(this).toggleClass('w2mb-btn-active');

							w2mb_maps[map_id].getContainer().map_id = map_id;
							
							var draw_down_event = function(e) {
								var el = e.target;
			                    do {
			                        if ($(el).hasClass('w2mb-map-draw-panel')) {
			                            return;
			                        }
			                    } while (el = el.parentNode);
								w2mb_drawFreeHandPolygon(map_id);
							};
							
							w2mb_maps[map_id].once('mousedown', draw_down_event);
							w2mb_maps[map_id].once('touchstart', draw_down_event);
						} else if (this.drawing_state == 1) {
							this.drawing_state = 0;
							window.removeEventListener('touchmove', w2mb_stop_touchmove_listener, { passive: false });
							map.getCanvas().style.cursor = '';
							$(drawButton).removeClass('w2mb-btn-active');
							w2mb_disableDrawingMode(map_id);

							// repair ajax_loading and set drawing_state
							var map_attrs_array;
							if (map_attrs_array = w2mb_get_map_markers_attrs_array(map_id)) {
								map_attrs_array.map_attrs.drawing_state = 0;
								if (typeof w2mb_get_original_map_markers_attrs_array(map_id).map_attrs.ajax_loading != 'undefined' && w2mb_get_original_map_markers_attrs_array(map_id).map_attrs.ajax_loading == 1) {
									map_attrs_array.map_attrs.ajax_loading = 1;
								}
							}
						}
					});
				    
				    var editButton = document.createElement('button');
				    $(editButton)
				    .addClass('w2mb-btn w2mb-btn-primary w2mb-map-edit')
				    .attr("title", w2mb_maps_objects.edit_area_button)
				    .html('<span class="w2mb-glyphicon w2mb-glyphicon-edit"></span>')
				    .attr('disabled', 'disabled');
				    
				    drawPanel.appendChild(editButton);
				    editButton.map_id = map_id;
				    editButton.editing_state = 0;
				    $(editButton).on("click", function(e) {
				    	var map_id = editButton.map_id;
						if (this.editing_state == 0) {
							this.editing_state = 1;
							$(this).toggleClass('w2mb-btn-active');
							$(this).attr("title", w2mb_maps_objects.apply_area_button);

							w2mb_removeShapes(map_id);

							var draw = new MapboxDraw({
								displayControlsDefault: false,
								styles: [
										// line stroke
										{
											"id": "gl-draw-line",
											"type": "line",
											"filter": ["all", ["==", "$type", "LineString"], ["!=", "mode", "static"]],
											"layout": {
												"line-cap": "round",
												"line-join": "round"
											},
											"paint": {
												"line-color": "#AA2143",
												"line-dasharray": [0.2, 2],
												"line-width": 1
											}
										},
										// polygon fill
										{
											"id": "gl-draw-polygon-fill",
											"type": "fill",
											"filter": ["all", ["==", "$type", "Polygon"], ["!=", "mode", "static"]],
											"paint": {
												"fill-color": "#0099FF",
												"fill-outline-color": "#AA2143",
												"fill-opacity": 0.3
											}
										},
										// vertex point halos
										{
											"id": "gl-draw-polygon-and-line-vertex-halo-active",
											"type": "circle",
											"filter": ["all", ["==", "meta", "vertex"], ["==", "$type", "Point"], ["!=", "mode", "static"]],
											"paint": {
												"circle-radius": 5,
												"circle-color": "#FFF"
											}
										},
										// vertex points
										{
											"id": "gl-draw-polygon-and-line-vertex-active",
											"type": "circle",
											"filter": ["all", ["==", "meta", "vertex"], ["==", "$type", "Point"], ["!=", "mode", "static"]],
											"paint": {
												"circle-radius": 3,
												"circle-color": "#AA2143",
											}
										}
								]
							});
							map.addControl(draw);
							draw.add(w2mb_draw_features[map_id]);
							draw.changeMode('direct_select', { featureId: w2mb_draw_features[map_id].id });

							w2mb_draws[map_id] = draw;
							
						} else if (this.editing_state == 1) {
							this.editing_state = 0;
							$(this).toggleClass('w2mb-btn-active');
							$(this).attr("title", w2mb_maps_objects.edit_area_button);
							if (typeof w2mb_draws[map_id] != 'undefined' && w2mb_draws[map_id]) {
								var draw = w2mb_draws[map_id];
								draw.changeMode('simple_select', { featureId: w2mb_draw_features[map_id].id });
								w2mb_draw_features[map_id] = draw.get(w2mb_draw_features[map_id].id);
								w2mb_addPolygon(map_id);

								var geo_poly_ajax = [];
								w2mb_draw_features[map_id].geometry.coordinates[0].map(function(point_feature) {
									geo_poly_ajax.push({ 'lat': point_feature[1], 'lng': point_feature[0] });
						    	});

								if (geo_poly_ajax.length) {
									w2mb_sendGeoPolyAJAX(map_id, geo_poly_ajax);
								}
							}
							map.removeControl(w2mb_draws[map_id]);
							w2mb_draws[map_id] = false;
						}
				    });
				    
				    var reloadButton = document.createElement('button');
				    $(reloadButton)
				    .addClass('w2mb-btn w2mb-btn-primary w2mb-map-reload')
				    .attr("title", w2mb_maps_objects.reload_map_button)
				    .html('<span class="w2mb-glyphicon w2mb-glyphicon-refresh"></span>');
				    
				    drawPanel.appendChild(reloadButton);
				    reloadButton.map_id = map_id;
				    $(reloadButton).on("click", function(e) {
						var map_id = reloadButton.map_id;
						for (var i=0; i<w2mb_map_markers_attrs_array.length; i++) {
							if (w2mb_map_markers_attrs_array[i].map_id == map_id) {
								w2mb_map_markers_attrs_array[i] = JSON.parse(JSON.stringify(_w2mb_map_markers_attrs_array[i]));

								window.removeEventListener('touchmove', w2mb_stop_touchmove_listener, { passive: false });
		
								var editButton = $(w2mb_maps[map_id].getContainer()).find('.w2mb-map-edit').get(0);
								$(editButton).removeClass('w2mb-btn-active');
								$(editButton).find('.w2mb-map-edit-label').text(w2mb_maps_objects.edit_area_button);
								$(editButton).attr('disabled', 'disabled');

								w2mb_disableDrawingMode(map_id);
								w2mb_clearMarkers(map_id);
								w2mb_closeInfoWindow(map_id);
								w2mb_removeShapes(map_id);
								w2mb_load_map(i);
								if (w2mb_global_markers_array[map_id].length) {
									var markers_array = [];
									var bounds = w2mb_buildBounds();
									for (var j=0; j<w2mb_global_markers_array[map_id].length; j++) {
										var marker = w2mb_global_markers_array[map_id][j];
									    w2mb_extendBounds(bounds, w2mb_getMarkerPosition(marker));
									    markers_array.push(marker);
						    		}
									w2mb_mapFitBounds(map_id, bounds);
									
									var map_attrs = w2mb_map_markers_attrs_array[i].map_attrs;
									w2mb_setMapZoomCenter(map_id, map_attrs, markers_array);
						    	}
								break;
							}
						}
						
					});

				    if (w2mb_maps_objects.enable_my_location_button) {
				    	var locationButton = document.createElement('button');
						$(locationButton)
						.addClass('w2mb-btn w2mb-btn-primary w2mb-map-location')
						.attr("title", w2mb_maps_objects.my_location_button)
						.html('<span class="w2mb-glyphicon w2mb-glyphicon-screenshot"></span>');

						drawPanel.appendChild(locationButton);
						
						locationButton.map_id = map_id;
					    $(locationButton).on("click", function(e) {
							var map_id = locationButton.map_id;
							if (navigator.geolocation) {
						    	navigator.geolocation.getCurrentPosition(
						    		function(position) {
							    		var start_latitude = position.coords.latitude;
							    		var start_longitude = position.coords.longitude;
									    w2mb_setMapCenter(map_id, w2mb_buildPoint(start_latitude, start_longitude));
							    	},
							    	function(e) {
								   		//alert(e.message);
								    },
								   	{timeout: 10000}
							    );
							}
						});
				    }
			    }
			} // end of (!fullScreen)

		    w2mb_global_markers_array[map_id] = [];
		    w2mb_global_locations_array[map_id] = [];
		    
		    
		    if (markers_array.length) {
		    	var bounds = w2mb_buildBounds();
		    		
			    if (typeof map_attrs.ajax_markers_loading != 'undefined' && map_attrs.ajax_markers_loading == 1) {
					var is_ajax_markers = true;
			    } else {
					var is_ajax_markers = false;
			    }
		
			    var markers = [];
			    for (var j=0; j<markers_array.length; j++) {
		    		var map_coords_1 = markers_array[j][2];
				   	var map_coords_2 = markers_array[j][3];
				   	if ($.isNumeric(map_coords_1) && $.isNumeric(map_coords_2)) {
				    	var point = w2mb_buildPoint(map_coords_1, map_coords_2);
				    	w2mb_extendBounds(bounds, point);

		    			var location_obj = new w2mb_glocation(
		    				markers_array[j][0],  // location ID
		    				markers_array[j][1],  // listing ID
		    				point, 
		    				markers_array[j][4],  // map icon file
		    				markers_array[j][5],  // map icon color
		    				markers_array[j][7],  // listing title
		    				markers_array[j][8],  // logo image
		    				markers_array[j][9],  // content fields output
		    				show_directions_button,
		    				show_readmore_button,
		    				map_id,
		    				is_ajax_markers
			    		);
			    		var marker = location_obj.w2mb_placeMarker(map_id);
			    		markers.push(marker);
		
			    		w2mb_global_locations_array[map_id].push(location_obj);
			    	}
	    		}
			    	
			    w2mb_mapFitBounds(map_id, bounds);

			    w2mb_setClusters(clusters, map_id);
			    
			    if (radius_circle && typeof window['radius_params_'+map_id] != 'undefined') {
		    		var radius_params = window['radius_params_'+map_id];
					var map_radius = parseFloat(radius_params.radius_value);
					w2mb_maps[map_id].on('load', function() {
						w2mb_draw_radius(radius_params, map_radius, map_id);
					});
				}
		    }

		    w2mb_setMapZoomCenter(map_id, map_attrs, markers_array);
		    
		    if (counter && counter_text) {
		    	class w2mb_locations_counter_controls {
					  onAdd(map){
					    this.map = map;

					    w2mb_locations_counters[map_id] = document.createElement('div');
					    var counter_html = counter_text.replace('%d', '<span class="w2mb-map-locations-counter"></span>');
					    $(w2mb_locations_counters[map_id]).addClass('w2mb-map-locations-counter-bar');
					    $(w2mb_locations_counters[map_id]).html(counter_html);
					    
					    w2mb_countLocations(map_id);
					    
					    this.container = w2mb_locations_counters[map_id];
					    return this.container;
					  }
					  onRemove(){
					    this.container.parentNode.removeChild(this.container);
					    this.map = undefined;
					  }
				}
				var locationsCounterControls = new w2mb_locations_counter_controls();
				
				if (w2mb_js_objects.is_rtl) {
					var cposition = 'bottom-left';
				} else {
					var cposition = 'bottom-right';
				}
				map.addControl(locationsCounterControls, cposition);
		    }
		}
	}

	function w2mb_setMapAjaxListener(map, map_id, search_button_obj) {
		var search_button_obj = typeof search_button_obj !== 'undefined' ? search_button_obj : null;

		map.on('load', function() {
			w2mb_setAjaxMarkers(map, map_id, search_button_obj);
		});
		map.on('moveend', function() {
			w2mb_setAjaxMarkers(map, map_id, search_button_obj);
		});
		map.on('zoomend', function() {
			w2mb_setAjaxMarkers(map, map_id, search_button_obj);
		});
	}
	function w2mb_geocodeStartAddress(map_attrs, map_id, zoom_level) {
		var start_address = map_attrs.start_address;
		function _geocodeStartAddress(status, start_latitude, start_longitude) {
			if (status == true) {
				w2mb_setMapZoom(map_id, zoom_level);
			    w2mb_setMapCenter(map_id, [start_longitude, start_latitude]);
			    
			    if (typeof map_attrs.ajax_loading != 'undefined' && map_attrs.ajax_loading == 1) {
				    // use closures here
				    w2mb_setMapAjaxListener(w2mb_maps[map_id], map_id);
			    }
			}
		}
		w2mb_geocodeAddress(start_address, _geocodeStartAddress);
	}
	function w2mb_geolocatePosition() {
		if (navigator.geolocation) {
			var geolocation_maps = [];
	    	for (var map_id in w2mb_maps_attrs) {
	    		if (typeof w2mb_maps_attrs[map_id].geolocation != 'undefined' && w2mb_maps_attrs[map_id].geolocation == 1) {
	    			geolocation_maps.push({ 'map': w2mb_maps[map_id], 'map_id': map_id});
	    		}
	    	}
	    	if (geolocation_maps.length) {
	    		navigator.geolocation.getCurrentPosition(
	    			function(position) {
		    			var start_latitude = position.coords.latitude;
		    			var start_longitude = position.coords.longitude;
				    	for (var i in geolocation_maps) {
				    		var map_id = geolocation_maps[i].map_id;
				    		
				    		w2mb_setMapCenter(geolocation_maps[i].map_id, [start_longitude, start_latitude]);
				    		
				    		if (typeof w2mb_maps_attrs[map_id].start_zoom != 'undefined' && w2mb_maps_attrs[map_id].start_zoom > 0) {
				    			w2mb_setMapZoom(map_id, w2mb_maps_attrs[map_id].start_zoom);
				    		}
				    		
				    		for (var j=0; j<w2mb_map_markers_attrs_array.length; j++) {
								if (w2mb_map_markers_attrs_array[j].map_id == map_id) {
									w2mb_map_markers_attrs_array[j].map_attrs.start_latitude = start_latitude;
									w2mb_map_markers_attrs_array[j].map_attrs.start_longitude = start_longitude;
								}
				    		}
				    	}
		    		}, 
		    		function(e) {
		    			//alert(e.message);
			    	},
			    	{timeout: 10000}
		    	);
	    	}
		}
	}

	window.w2mb_setAjaxMarkers = function(map, map_id, search_button_obj) {
		var attrs_array = w2mb_get_map_markers_attrs_array(map_id);
		var map_attrs = attrs_array.map_attrs;
		var radius_circle = attrs_array.radius_circle;
		var clusters = attrs_array.clusters;
		var show_directions_button = attrs_array.show_directions_button;
		var show_readmore_button = attrs_array.show_readmore_button;
		var search_button_obj = typeof search_button_obj !== 'undefined' ? search_button_obj : null;

		var address_string = '';
		if (typeof map_attrs.address != 'undefined' && map_attrs.address) {
			var address_string = map_attrs.address;
		} else if (typeof map_attrs.location_id_text != 'undefined' && map_attrs.location_id_text) {
			var address_string = map_attrs.location_id_text;
		}
		if (address_string) {
			if (typeof w2mb_searchAddresses[map_id] == "undefined" || w2mb_searchAddresses[map_id] != address_string) {
				function _geocodeSearchAddress(status, latitude, longitude) {
					if (status == true) {
						map.panTo([longitude, latitude]);
	
						if (search_button_obj) {
							w2mb_delete_iloader_from_element(search_button_obj);
						}
						w2mb_setAjaxMarkers(map, map_id);
					}
				}
				w2mb_geocodeAddress(address_string, _geocodeSearchAddress);
				
				w2mb_searchAddresses[map_id] = address_string;
			}
		}
	
		var bounds_new = map.getBounds();
		if (bounds_new) {
			var south_west = bounds_new.getSouthWest();
			var north_east = bounds_new.getNorthEast();
		} else
			return false;
		
		function inBoundingBox(bl/*bottom left*/, tr/*top right*/, p) {
			// in case longitude 180 is inside the box
			function isLongInRange(bl, tr, p) {
				if (tr.lng < bl.lng) {
					if (p.lng >= bl.lng || p.lng <= tr.lng) {
						return true;
					}
				} else
					if (p.lng >= bl.lng && p.lng <= tr.lng) {
						return true;
					}
			}

			if (p.lat >= bl.lat  &&  p.lat <= tr.lat  &&  isLongInRange(bl, tr, p)) {
				return true;
			} else {
				return false;
			}
		}
	
		if (typeof map_attrs.swLat != 'undefined' && typeof map_attrs.swLng != 'undefined' && typeof map_attrs.neLat != 'undefined' && typeof map_attrs.neLng != 'undefined') {
			var sw_point = new mapboxgl.LngLat(map_attrs.swLng, map_attrs.swLat);
		    var ne_point = new mapboxgl.LngLat(map_attrs.neLng, map_attrs.neLat);

		    var worldCoordinate_new = map.project(sw_point);
		    var worldCoordinate_old = map.project(south_west);
		    if (
		    	(inBoundingBox(sw_point, ne_point, south_west) && inBoundingBox(sw_point, ne_point, north_east))
		    	||
			    	(140 > Math.abs(Math.floor(worldCoordinate_new.x) - Math.floor(worldCoordinate_old.x))
			    	&&
			    	140 > Math.abs(Math.floor(worldCoordinate_new.y) - Math.floor(worldCoordinate_old.y)))
		    )
		    	return false;
		}
		map_attrs.swLat = south_west.lat;
		map_attrs.swLng = south_west.lng;
		map_attrs.neLat = north_east.lat;
		map_attrs.neLng = north_east.lng;
		
		if (attrs_array.use_ajax_loader) {
			w2mb_ajax_loader_target_show($('#w2mb-maps-canvas-'+map_id));
		}
	
		var ajax_params = {};
		for (var attrname in map_attrs) {
			if (attrname != 'start_latitude' && attrname != 'start_longitude') {
				ajax_params[attrname] = map_attrs[attrname];
			}
		}
		ajax_params.action = 'w2mb_get_map_markers';
		ajax_params.hash = map_id;

		var listings_args_array;
		if (listings_args_array = w2mb_get_controller_args_array(map_id)) {
			ajax_params.hide_order = listings_args_array.hide_order;
			ajax_params.hide_count = listings_args_array.hide_count;
			ajax_params.hide_paginator = listings_args_array.hide_paginator;
			ajax_params.show_views_switcher = listings_args_array.show_views_switcher;
			ajax_params.listings_view_type = listings_args_array.listings_view_type;
			ajax_params.listings_view_grid_columns = listings_args_array.listings_view_grid_columns;
			ajax_params.listing_thumb_width = listings_args_array.listing_thumb_width;
			ajax_params.wrap_logo_list_view = listings_args_array.wrap_logo_list_view;
			ajax_params.logo_animation_effect = listings_args_array.logo_animation_effect;
			ajax_params.grid_view_logo_ratio = listings_args_array.grid_view_logo_ratio;
			ajax_params.scrolling_paginator = listings_args_array.scrolling_paginator;
			ajax_params.paged = listings_args_array.paged;
			ajax_params.perpage = listings_args_array.perpage;
			ajax_params.onepage = listings_args_array.onepage;
			ajax_params.order = listings_args_array.order;
			ajax_params.order_by = listings_args_array.order_by;
			ajax_params.base_url = listings_args_array.base_url;
	
			w2mb_ajax_loader_target_show($('#w2mb-controller-'+map_id));
		} else
			ajax_params.without_listings = 1;
		
		if ($("#w2mb-map-listings-panel-"+map_id).length) {
			ajax_params.map_listings = 1;
			if (attrs_array.use_ajax_loader) {
				w2mb_ajax_loader_target_show($("#w2mb-map-search-panel-wrapper-"+map_id));
			}
		}
	
		$.ajax({
			type: "POST",
			url: w2mb_js_objects.ajaxurl,
			data: ajax_params,
			dataType: 'json',
			success: function(response_from_the_action_function) {
				if (response_from_the_action_function) {
					var response_hash = response_from_the_action_function.hash;
	
					if (response_from_the_action_function.html) {
						var listings_block = $('#w2mb-controller-'+response_hash);
						listings_block.replaceWith(response_from_the_action_function.html);
						w2mb_ajax_loader_target_hide('w2mb-controller-'+response_hash);
					}
					
					var map_listings_block = $('#w2mb-map-listings-panel-'+response_hash);
			    	if (map_listings_block.length) {
			    		map_listings_block.find(".w2mb-no-listings-found").remove();
						if (!response_from_the_action_function.map_listings) {
			    			var empty_listings = $("<p />").addClass("w2mb-no-listings-found").html(w2mb_js_objects.no_listings);
			    			map_listings_block.html(empty_listings[0].outerHTML);
			    		} else {
			    			map_listings_block.html(response_from_the_action_function.map_listings);
			    		}
			    		w2mb_ajax_loader_target_hide('w2mb-map-search-panel-wrapper-'+response_hash);
			    	}
	
			    	var active_location;
			    	if (typeof w2mb_infoWindows[map_id] != 'undefined' && w2mb_infoWindows[map_id].location) {
			    		active_location = w2mb_infoWindows[map_id].location;
			    	}
			    	
					w2mb_clearMarkers(map_id);
					w2mb_removeShapes(map_id);

					if (typeof map_attrs.ajax_markers_loading != 'undefined' && map_attrs.ajax_markers_loading == 1)
						var is_ajax_markers = true;
					else
						var is_ajax_markers = false;
		
					var markers_array = response_from_the_action_function.map_markers;
					w2mb_global_locations_array[map_id] = [];
			    	for (var j=0; j<markers_array.length; j++) {
		    			var map_coords_1 = markers_array[j][2];
				    	var map_coords_2 = markers_array[j][3];
				    	if ($.isNumeric(map_coords_1) && $.isNumeric(map_coords_2)) {
			    			var point = w2mb_buildPoint(map_coords_1, map_coords_2);
	
			    			var location_obj = new w2mb_glocation(
			    				markers_array[j][0],
			    				markers_array[j][1],
			    				point, 
			    				markers_array[j][4],
			    				markers_array[j][5],
			    				markers_array[j][7],
			    				markers_array[j][8],
			    				markers_array[j][9],
			    				show_directions_button,
			    				show_readmore_button,
			    				map_id,
			    				is_ajax_markers
				    		);
				    		var marker = location_obj.w2mb_placeMarker(map_id);
	
				    		w2mb_global_locations_array[map_id].push(location_obj);
				    		
				    		// Re-open active infoWindow
				    		if (active_location && active_location.id == location_obj.id) {
				    			if (!location.is_ajax_markers) {
				    				w2mb_setInfoWindow(location_obj, marker, map_id, 'bottom', 'onbuttonclick');
				    				//w2mb_showInfoWindowAJAXMarker(location_obj, marker, map_id, false);
				    			} else {
				    				w2mb_showInfoWindowAJAXMarker(location_obj, marker, map_id, false);
				    				//w2mb_showInfoWindow(location_obj, marker, map_id);
				    			}
						    	w2mb_scrollToListingLocation(map_id, location_obj.id, 0)
								w2mb_highlightListingLocation(location_obj.id);
				    		}
				    	}
		    		}
			    	w2mb_countLocations(map_id);
			    	w2mb_setClusters(clusters, map_id, w2mb_global_markers_array[map_id]);

			    	if (radius_circle && typeof response_from_the_action_function.radius_params != 'undefined') {
			    		var radius_params = response_from_the_action_function.radius_params;
						var map_radius = parseFloat(radius_params.radius_value);
						w2mb_draw_radius(radius_params, map_radius, response_hash);
					}
				}
			},
			complete: w2mb_completeAJAXSearchOnMap(map_id, search_button_obj)
		});
	}
	var w2mb_completeAJAXSearchOnMap = function(map_id, search_button_obj) {
		return function() {
			w2mb_ajax_loader_target_hide("w2mb-controller-"+map_id);
			w2mb_ajax_loader_target_hide("w2mb-maps-canvas-"+map_id);
			if (search_button_obj) {
				w2mb_delete_iloader_from_element(search_button_obj);
			}
		}
	}
	window.w2mb_draw_radius = function(radius_params, map_radius, map_id) {
		if (radius_params.dimension == 'miles')
			map_radius *= 1.609344;
		var map_coords_1 = radius_params.map_coords_1;
		var map_coords_2 = radius_params.map_coords_2;

		if ($.isNumeric(map_coords_1) && $.isNumeric(map_coords_2)) {
			var map = w2mb_maps[map_id];
			map.addSource("source-circle-"+map_id, {
				"type": "geojson",
				"data": {
					"type": "FeatureCollection",
					"features": [{
						"type": "Feature",
						"geometry": {
							"type": "Point",
							"coordinates": [map_coords_2, map_coords_1]
						}
					}]
				}
			});
	
			const metersToPixelsAtMaxZoom = (meters, latitude) =>
			meters / 0.075 / Math.cos(latitude * Math.PI / 180)

			map.addLayer({
				"id": "radius-circle-"+map_id,
				"type": "circle",
				"source": "source-circle-"+map_id,
				"paint": {
					"circle-radius": {
						stops: [
						[0, 0],
						[20, metersToPixelsAtMaxZoom(map_radius*1000, map_coords_1)]
						],
						base: 2
					},
					"circle-color": "#FF0000",
					"circle-opacity": 0.1,
					"circle-stroke-width": 1,
					"circle-stroke-color": "#FF0000",
					"circle-stroke-opacity": 0.25
				}
			});

			w2mb_drawCircles[map_id] = true;
		}
	}
	mapboxgl.Map.prototype.panToWithOffset = function(lnglat, offsetX, offsetY) {
		var map = this;
		var aPoint = map.project(lnglat);
		aPoint.x = aPoint.x+offsetX;
		aPoint.y = aPoint.y+offsetY;
		map.panTo(map.unproject(aPoint));
	};
	var w2mb_bouncing_marker = null;
	window.w2mb_placeMarker = function(location, map_id) {
		if (w2mb_maps_objects.map_markers_type != 'icons') {
			if (w2mb_maps_objects.global_map_icons_path != '') {
				var re = /(?:\.([^.]+))?$/;
				if (location.map_icon_file && typeof re.exec(w2mb_maps_objects.global_map_icons_path+'icons/'+location.map_icon_file)[1] != "undefined")
					var icon_file = w2mb_maps_objects.global_map_icons_path+'icons/'+location.map_icon_file;
				else
					var icon_file = w2mb_maps_objects.global_map_icons_path+"blank.png";

				var el = $("<div>", {
					id: 'marker-id-'+location.id,
					style: 'background-image: url('+icon_file+'); width: '+parseInt(w2mb_maps_objects.marker_image_width)+'px; height: '+parseInt(w2mb_maps_objects.marker_image_height)+'px',
					class: 'w2mb-mapbox-marker'
				});
				var marker_div = el[0];
				
				var marker_options = {
						anchor: 'bottom',
						element: marker_div
				};
				
				var marker = new mapboxgl.Marker(marker_options)
	    		.setLngLat(location.point)
	    		.addTo(w2mb_maps[map_id]);
			} else {
				var marker = new mapboxgl.Marker()
	    		.setLngLat(location.point)
	    		.addTo(w2mb_maps[map_id]);
			}
		} else {
			if (location.map_icon_color)
				var map_marker_color = location.map_icon_color;
			else
				var map_marker_color = w2mb_maps_objects.default_marker_color;

			if (typeof location.map_icon_file == 'string' && location.map_icon_file.indexOf("w2mb-fa-") != -1) {
				var map_marker_icon = '<span class="w2mb-map-marker-icon w2mb-fa '+location.map_icon_file+'" style="color: '+map_marker_color+';"></span>';
				var map_marker_class = 'w2mb-map-marker';
			} else {
				if (w2mb_maps_objects.default_marker_icon) {
					var map_marker_icon = '<span class="w2mb-map-marker-icon w2mb-fa '+w2mb_maps_objects.default_marker_icon+'" style="color: '+map_marker_color+';"></span>';
					var map_marker_class = 'w2mb-map-marker';
				} else {
					var map_marker_icon = '';
					var map_marker_class = 'w2mb-map-marker-empty';
				}
			}

			var el = $("<div>", {
				id: 'marker-id-'+location.id,
				class: 'w2mb-mapbox-marker',
				html: '<div class="'+map_marker_class+'" style="background: '+map_marker_color+' none repeat scroll 0 0;">'+map_marker_icon+'</div>'
			});
			var marker_div = el[0];
			
			var marker_options = {
				anchor: 'bottom',
				offset: [0, -20],
				element: marker_div
			};

			var marker = new mapboxgl.Marker(marker_options)
    		.setLngLat(location.point)
    		.addTo(w2mb_maps[map_id]);
			
			var speed = 15;
			var down = true;
			var timer;
			function doBounce() {
				if (w2mb_bouncing_marker) {
					var offset = w2mb_bouncing_marker.getOffset();
					var offset_y = offset.y;
					
					if (down == true && offset_y > -20) {
						down = false;
					}
					if (down == false) {
						offset_y = offset_y - 1;
					}
					
					if (down == false && offset_y < -40) {
						down = true;
					}
					if (down == true) {
						offset_y = offset_y + 1;
					}
					
					//console.log(offset_y);
					//console.log(w2mb_bouncing_marker);
					
					w2mb_bouncing_marker.setOffset([0, offset_y]);
					
					timer = window.setTimeout(function() {
						doBounce();
					}, speed);
				}
			}
			
			$(document).on("mouseenter", ".w2mb-listing-has-location-"+location.id, function(event) {
				if (!w2mb_bouncing_marker) {
					w2mb_bouncing_marker = marker;
					
					doBounce();
				}
			});
			$(document).on("mouseleave", ".w2mb-listing-has-location-"+location.id, function(event) {
				if (w2mb_bouncing_marker) {
					w2mb_bouncing_marker.setOffset([0, -20]);
					window.clearTimeout(timer);
					timer = null;
					w2mb_bouncing_marker = null;
				}
			});
		}
		
		w2mb_global_markers_array[map_id].push(marker);

		marker_div.addEventListener('click', function() {
			w2mb_scrollToListingLocation(map_id, location.id, 'fast');
			
			var old_zoom = w2mb_maps[map_id].getZoom();
			var new_zoom = w2mb_applyZoomOnClick(map_id);
			if ((new_zoom && new_zoom != old_zoom) || w2mb_isCenterOnClick(map_id)) {
				w2mb_setMapCenter(map_id, location.marker.position);
			}
			
			if (!location.is_ajax_markers) {
				w2mb_setInfoWindow(location, marker, map_id, 'bottom', 'onmarkerclick');
			} else {
				w2mb_showInfoWindowAJAXMarker(location, marker, map_id, true);
			}
			
			w2mb_placeDestination(location, map_id);
		});

		return marker;
	}
	
	window.w2mb_showInfoWindowAJAXMarker = function(location, marker, map_id, do_panby) {
		var attrs_array = w2mb_get_map_markers_attrs_array(map_id);
		
		if (attrs_array.use_ajax_loader) {
			w2mb_ajax_loader_target_show($('#w2mb-maps-canvas-'+map_id));
		}

		var post_data = {
				'location_id': location.id,
				'action': 'w2mb_get_map_marker_info',
				'map_id': map_id,
				'show_directions_button': location.show_directions_button,
				'show_readmore_button': location.show_readmore_button
		};
		$.ajax({
    		type: "POST",
    		url: w2mb_js_objects.ajaxurl,
    		data: eval(post_data),
    		map_id: map_id,
    		location: location,
    		marker: marker,
    		dataType: 'json',
    		success: function(response_from_the_action_function) {
    			var marker_array = response_from_the_action_function;
    			var map_coords_1 = marker_array[2];
		    	var map_coords_2 = marker_array[3];
		    	if ($.isNumeric(map_coords_1) && $.isNumeric(map_coords_2)) {
	    			var point = w2mb_buildPoint(map_coords_1, map_coords_2);

	    			var new_location_obj = new w2mb_glocation(
	    				marker_array[0],
	    				marker_array[1],
	    				point, 
	    				marker_array[4],
	    				marker_array[5],
	    				marker_array[7],
	    				marker_array[8],
	    				marker_array[9],
	    				marker_array[10],
	    				marker_array[11],
	    				marker_array[12],
	    				location.show_directions_button,
	    				location.show_readmore_button,
	    				map_id,
	    				true
		    		);
	    			w2mb_setInfoWindow(new_location_obj, marker, map_id, '', 'onbuttonclick');
	    			
	    			if (do_panby) {
	    				w2mb_maps[map_id].panToWithOffset(marker.getLngLat(), 0, -100);
	    			}
		    	}
    		},
    		complete: function() {
    			var map_id = this.map_id
				w2mb_ajax_loader_target_hide("w2mb-maps-canvas-"+map_id);
			}
		});
	}
	window.w2mb_setInfoWindow = function(location, marker, map_id, anchor, event) {
		if (!location.is_ajax_markers) {
			w2mb_showInfoWindow(location, marker, map_id, anchor, event);
		} else {
			w2mb_ajax_loader_target_show($('#w2mb-maps-canvas-'+map_id));

			var post_data = {
					'location_id': location.id,
					'action': 'w2mb_get_map_marker_info',
					'map_id': map_id,
					'show_directions_button': location.show_directions_button,
					'show_readmore_button': location.show_readmore_button
			};
			$.ajax({
	    		type: "POST",
	    		url: w2mb_js_objects.ajaxurl,
	    		data: eval(post_data),
	    		dataType: 'json',
	    		success: function(response_from_the_action_function) {
	    			var marker_array = response_from_the_action_function;
	    			var map_coords_1 = marker_array[2];
			    	var map_coords_2 = marker_array[3];
			    	if ($.isNumeric(map_coords_1) && $.isNumeric(map_coords_2)) {
		    			var point = w2mb_buildPoint(map_coords_1, map_coords_2);

		    			var new_location_obj = new w2mb_glocation(
		    				marker_array[0],
		    				marker_array[1],
		    				point, 
		    				marker_array[4],
		    				marker_array[5],
		    				marker_array[7],
		    				marker_array[8],
		    				marker_array[9],
		    				marker_array[10],
		    				marker_array[11],
		    				marker_array[12],
		    				location.show_directions_button,
		    				location.show_readmore_button,
		    				map_id,
		    				true
			    		);
		    			w2mb_showInfoWindow(new_location_obj, marker, map_id, anchor, 'onbuttonclick');
			    	}
	    		},
	    		complete: function() {
					w2mb_ajax_loader_target_hide("w2mb-maps-canvas-"+map_id);
				}
			});
		}
	}
	function w2mb_scrollToListingLocation(map_id, location_id, speed) {
		if ($('#w2mb-map-listings-panel-'+map_id).length) {
			if ($('#w2mb-map-listings-panel-'+map_id+' #post-'+location_id).length) {
				$('#w2mb-map-listings-panel-'+map_id).animate({scrollTop: $('#w2mb-map-listings-panel-'+map_id).scrollTop() + $('#w2mb-map-listings-panel-'+map_id+' #post-'+location_id).position().top}, speed);
			}
		}
	}
	function w2mb_highlightListingLocation(location_id) {
		$(".w2mb-listing-location-selected").removeClass("w2mb-listing-location-selected");
		$(".w2mb-listing-location[data-location-id='" + location_id + "']").addClass("w2mb-listing-location-selected");
	}
	// This function builds info Window and shows it hiding another
	function w2mb_showInfoWindow(w2mb_glocation, marker, map_id, anchor, event) {
		
		w2mb_closeInfoWindow(map_id);
		
		w2mb_highlightListingLocation(w2mb_glocation.id);
	    
	    var windowHtml = w2mb_glocation.content_fields;
	    
	    // remove summary button when no listing excerpt veisible
	    windowHtml = $($.parseHTML(windowHtml));
		var listing_id = windowHtml.find(".w2mb-info-window-summary-button").data("listing-id");
		if (!$("#post-"+listing_id).length) {
			var summary_btn = windowHtml.find(".w2mb-info-window-summary-button");
			
			summary_btn.parent()
			.removeClass("w2mb-map-info-window-buttons")
			.addClass("w2mb-map-info-window-buttons-single");
			
			summary_btn.remove();
		}
		windowHtml = $(windowHtml)[0].outerHTML;

		var options = {
				offset: {'bottom': [0,-30]},
				closeOnClick: false,
				anchor: anchor,
				maxWidth: w2mb_maps_objects.infowindow_width
		};
		var popup = new mapboxgl.Popup(options)
		.setHTML(windowHtml)
		.addTo(w2mb_maps[map_id]);

		marker.setPopup(popup);
		// This is needed workaround, otherwise it will not open infoWindow on "On map" button click due to popup.addTo(w2mb_maps[map_id])
		if (event == 'onmarkerclick') {
			marker.addTo(w2mb_maps[map_id]);
		}
		
		w2mb_infoWindows[map_id] = popup;
		w2mb_infoWindows[map_id].marker = marker;
		w2mb_infoWindows[map_id].location = w2mb_glocation;
	}
	
	window.w2mb_isCenterOnClick = function(map_id) {
		var attrs_array = w2mb_get_map_markers_attrs_array(map_id);
		return attrs_array.center_map_onclick;
	}
	
	window.w2mb_applyZoomOnClick = function(map_id) {
		var attrs_array = w2mb_get_map_markers_attrs_array(map_id);
		if (attrs_array.zoom_map_onclick) {
			w2mb_maps[map_id].setZoom(parseInt(attrs_array.zoom_map_onclick));
			return attrs_array.zoom_map_onclick;
		} {
			return false;
		}
	}

	window.w2mb_scrollToListing = function(anchor, map_id) {
		var scroll_to_anchor = $("#"+anchor);
		var sticky_scroll_toppadding = 0;
		if (typeof window["w2mb_sticky_scroll_toppadding_"+map_id] != 'undefined') {
			sticky_scroll_toppadding = window["w2mb_sticky_scroll_toppadding_"+map_id];
		}

		if (scroll_to_anchor.length) {
			$('html,body').animate({scrollTop: scroll_to_anchor.position().top - sticky_scroll_toppadding}, 'fast');
		}
	}

	// global object to set and remove updateClusters event
	var w2mb_updateClusters;
	window.w2mb_setClusters = function(enable_clusters, map_id) {
		if (enable_clusters) {
			var map = w2mb_maps[map_id],
			clusters = {},
			markers = [],
			clustersGeojson = {};

			var displayFeatures = function (features) {
				if (w2mb_global_locations_array[map_id].length) {
		            $.each(w2mb_global_locations_array[map_id], function (i, marker) {
		            	// Do not remove markers, only hide. Otherwise on each move it will remove opened opoup as well.
		            	$("#marker-id-"+w2mb_global_locations_array[map_id][i].id).hide();
		            });
				}

				$.each(features, function (i, feature) {
					var isCluster = (!!feature.properties.cluster) ? true : false,
						$feature;

					if (isCluster) {
						var count = feature.properties.point_count,
							className;
						if (count > 50) {
							className = 'w2mb-mapbox-cluster-extralarge';
						} else if (count > 25) {
							className = 'w2mb-mapbox-cluster-large';
						} else if (count > 15) {
							className = 'w2mb-mapbox-cluster-medium';
						} else if (count > 10) {
							className = 'w2mb-mapbox-cluster-small';
						} else {
							className = 'w2mb-mapbox-cluster-extrasmall';
						}

						$feature = $('<div class="w2mb-mapbox-cluster ' + className + '" tabindex="0">' + feature.properties.point_count_abbreviated + '</div>');
						clusters[feature.properties.cluster_id] = new mapboxgl.Marker($feature[0]).setLngLat(feature.geometry.coordinates).addTo(map);
						
						$feature.on("click", function() {
							var cluster_coords = feature.geometry.coordinates;
							var cluster_zoom = clusterIndex.getClusterExpansionZoom(feature.properties.cluster_id);
							map.flyTo({ 
								center: cluster_coords,
								zoom: cluster_zoom
							});
						});
					} else {
						$("#marker-id-"+feature.location_id).show();
					}
				});
			};

			w2mb_updateClusters = function () {
				var bounds = map.getBounds(),
					zoom = map.getZoom();

				clustersGeojson = clusterIndex.getClusters([
					bounds.getWest(),
					bounds.getSouth(),
					bounds.getEast(),
					bounds.getNorth()
				], Math.floor(zoom));

				if (Object.keys(clusters).length) {
					$.each(clusters, function (i, cluster) {
						cluster.remove();
					});
				}

				displayFeatures(clustersGeojson);
			};

			var feature_collection = [];
			for (var j=0; j<w2mb_global_locations_array[map_id].length; j++) {
				feature_collection.push({
					"type": "Feature",
					"properties": {},
					"geometry": {
						"type": "Point",
						"coordinates": w2mb_global_locations_array[map_id][j].point
					},
					"location_id": w2mb_global_locations_array[map_id][j].id
				});
			}

			var clusterIndex = supercluster({
				maxZoom: 20
			});
			clusterIndex.load(feature_collection);
			map.on('moveend', w2mb_updateClusters);
			w2mb_updateClusters();

			w2mb_markerClusters[map_id] = clusters;
		}
	}
	window.w2mb_clearMarkers = function(map_id) {
		if (typeof w2mb_markerClusters[map_id] != 'undefined') {
			$.each(w2mb_markerClusters[map_id], function (i, clusters) {
				w2mb_markerClusters[map_id][i].remove();
			});
			// remove updateClusters event
			var map = w2mb_maps[map_id];
			map.off('moveend', w2mb_updateClusters);
			
			w2mb_markerClusters[map_id] = [];
		}
	
		if (w2mb_global_markers_array[map_id]) {
			$.each(w2mb_global_markers_array[map_id], function (i, marker) {
				marker.remove();
			})
		}
		w2mb_global_markers_array[map_id] = [];
		w2mb_global_locations_array[map_id] = [];
	}
	window.w2mb_removeShapes = function(map_id) {
		if (typeof w2mb_drawCircles[map_id] != 'undefined' && w2mb_drawCircles[map_id]) {
			w2mb_maps[map_id].removeLayer('radius-circle-'+map_id);
			w2mb_maps[map_id].removeSource('source-circle-'+map_id);
			w2mb_drawCircles[map_id] = false;
		}

		if (typeof w2mb_polygons[map_id] != 'undefined' && w2mb_polygons[map_id]) {
			w2mb_maps[map_id].removeLayer('geo-poly-'+map_id);
			w2mb_maps[map_id].removeSource('geo-poly-'+map_id);
			w2mb_polygons[map_id] = false;
		}
		
		if (typeof w2mb_draws[map_id] != 'undefined' && w2mb_draws[map_id]) {
			w2mb_maps[map_id].removeControl(w2mb_draws[map_id]);
			w2mb_draws[map_id] = false;
		}
	}
	window.w2mb_setZoomCenter = function(map) {
		var zoom = map.getZoom();
		var center = map.getCenter();
		map.resize();
		map.setZoom(zoom);
		map.setCenter(center);
	}

	window.w2mb_geocodeField = function(field, error_message) {
		if (navigator.geolocation) {
			navigator.geolocation.getCurrentPosition(
				function(position) {
					$.get("https://api.mapbox.com/geocoding/v5/mapbox.places/"+position.coords.longitude + ',' + position.coords.latitude+".json?access_token="+w2mb_maps_objects.mapbox_api_key, function(data) {
						if (data.features.length) {
							field.val(data.features[0].place_name);
							field.trigger('change');
						}
					});
			    },
			    function(e) {
			    	//alert(e.message);
		    	},
			    {enableHighAccuracy: true, timeout: 10000, maximumAge: 0}
		    );
		} else
			alert(error_message);
	}
})(jQuery);

// supercluster.js
//https://unpkg.com/supercluster@3.0.2/dist/supercluster.min.js
!function(t){if("object"==typeof exports&&"undefined"!=typeof module)module.exports=t();else if("function"==typeof define&&define.amd)define([],t);else{("undefined"!=typeof window?window:"undefined"!=typeof global?global:"undefined"!=typeof self?self:this).supercluster=t()}}(function(){return function t(n,o,e){function r(s,u){if(!o[s]){if(!n[s]){var a="function"==typeof require&&require;if(!u&&a)return a(s,!0);if(i)return i(s,!0);var h=new Error("Cannot find module '"+s+"'");throw h.code="MODULE_NOT_FOUND",h}var p=o[s]={exports:{}};n[s][0].call(p.exports,function(t){var o=n[s][1][t];return r(o||t)},p,p.exports,t,n,o,e)}return o[s].exports}for(var i="function"==typeof require&&require,s=0;s<e.length;s++)r(e[s]);return r}({1:[function(t,n,o){"use strict";function e(t){return new r(t)}function r(t){this.options=h(Object.create(this.options),t),this.trees=new Array(this.options.maxZoom+1)}function i(t){return{type:"Feature",properties:s(t),geometry:{type:"Point",coordinates:[function(t){return 360*(t-.5)}(t.x),function(t){var n=(180-360*t)*Math.PI/180;return 360*Math.atan(Math.exp(n))/Math.PI-90}(t.y)]}}}function s(t){var n=t.numPoints,o=n>=1e4?Math.round(n/1e3)+"k":n>=1e3?Math.round(n/100)/10+"k":n;return h(h({},t.properties),{cluster:!0,cluster_id:t.id,point_count:n,point_count_abbreviated:o})}function u(t){return t/360+.5}function a(t){var n=Math.sin(t*Math.PI/180),o=.5-.25*Math.log((1+n)/(1-n))/Math.PI;return o<0?0:o>1?1:o}function h(t,n){for(var o in n)t[o]=n[o];return t}function p(t){return t.x}function f(t){return t.y}var c=t("kdbush");n.exports=e,n.exports.default=e,r.prototype={options:{minZoom:0,maxZoom:16,radius:40,extent:512,nodeSize:64,log:!1,reduce:null,initial:function(){return{}},map:function(t){return t}},load:function(t){var n=this.options.log;n&&console.time("total time");var o="prepare "+t.length+" points";n&&console.time(o),this.points=t;for(var e=[],r=0;r<t.length;r++)t[r].geometry&&e.push(function(t,n){var o=t.geometry.coordinates;return{x:u(o[0]),y:a(o[1]),zoom:1/0,id:n,parentId:-1}}(t[r],r));this.trees[this.options.maxZoom+1]=c(e,p,f,this.options.nodeSize,Float32Array),n&&console.timeEnd(o);for(var i=this.options.maxZoom;i>=this.options.minZoom;i--){var s=+Date.now();e=this._cluster(e,i),this.trees[i]=c(e,p,f,this.options.nodeSize,Float32Array),n&&console.log("z%d: %d clusters in %dms",i,e.length,+Date.now()-s)}return n&&console.timeEnd("total time"),this},getClusters:function(t,n){if(t[0]>t[2]){var o=this.getClusters([t[0],t[1],180,t[3]],n),e=this.getClusters([-180,t[1],t[2],t[3]],n);return o.concat(e)}for(var r=this.trees[this._limitZoom(n)],s=r.range(u(t[0]),a(t[3]),u(t[2]),a(t[1])),h=[],p=0;p<s.length;p++){var f=r.points[s[p]];h.push(f.numPoints?i(f):this.points[f.id])}return h},getChildren:function(t){var n=t>>5,o=t%32,e="No cluster with the specified id.",r=this.trees[o];if(!r)throw new Error(e);var s=r.points[n];if(!s)throw new Error(e);for(var u=this.options.radius/(this.options.extent*Math.pow(2,o-1)),a=r.within(s.x,s.y,u),h=[],p=0;p<a.length;p++){var f=r.points[a[p]];f.parentId===t&&h.push(f.numPoints?i(f):this.points[f.id])}if(0===h.length)throw new Error(e);return h},getLeaves:function(t,n,o){n=n||10,o=o||0;var e=[];return this._appendLeaves(e,t,n,o,0),e},getTile:function(t,n,o){var e=this.trees[this._limitZoom(t)],r=Math.pow(2,t),i=this.options.extent,s=this.options.radius/i,u=(o-s)/r,a=(o+1+s)/r,h={features:[]};return this._addTileFeatures(e.range((n-s)/r,u,(n+1+s)/r,a),e.points,n,o,r,h),0===n&&this._addTileFeatures(e.range(1-s/r,u,1,a),e.points,r,o,r,h),n===r-1&&this._addTileFeatures(e.range(0,u,s/r,a),e.points,-1,o,r,h),h.features.length?h:null},getClusterExpansionZoom:function(t){for(var n=t%32-1;n<this.options.maxZoom;){var o=this.getChildren(t);if(n++,1!==o.length)break;t=o[0].properties.cluster_id}return n},_appendLeaves:function(t,n,o,e,r){for(var i=this.getChildren(n),s=0;s<i.length;s++){var u=i[s].properties;if(u&&u.cluster?r+u.point_count<=e?r+=u.point_count:r=this._appendLeaves(t,u.cluster_id,o,e,r):r<e?r++:t.push(i[s]),t.length===o)break}return r},_addTileFeatures:function(t,n,o,e,r,i){for(var u=0;u<t.length;u++){var a=n[t[u]];i.features.push({type:1,geometry:[[Math.round(this.options.extent*(a.x*r-o)),Math.round(this.options.extent*(a.y*r-e))]],tags:a.numPoints?s(a):this.points[a.id].properties})}},_limitZoom:function(t){return Math.max(this.options.minZoom,Math.min(t,this.options.maxZoom+1))},_cluster:function(t,n){for(var o=[],e=this.options.radius/(this.options.extent*Math.pow(2,n)),r=0;r<t.length;r++){var i=t[r];if(!(i.zoom<=n)){i.zoom=n;var s=this.trees[n+1],u=s.within(i.x,i.y,e),a=i.numPoints||1,h=i.x*a,p=i.y*a,f=null;this.options.reduce&&(f=this.options.initial(),this._accumulate(f,i));for(var c=(r<<5)+(n+1),l=0;l<u.length;l++){var d=s.points[u[l]];if(!(d.zoom<=n)){d.zoom=n;var m=d.numPoints||1;h+=d.x*m,p+=d.y*m,a+=m,d.parentId=c,this.options.reduce&&this._accumulate(f,d)}}1===a?o.push(i):(i.parentId=c,o.push(function(t,n,o,e,r){return{x:t,y:n,zoom:1/0,id:o,parentId:-1,numPoints:e,properties:r}}(h/a,p/a,c,a,f)))}}return o},_accumulate:function(t,n){var o=n.numPoints?n.properties:this.options.map(this.points[n.id].properties);this.options.reduce(t,o)}}},{kdbush:2}],2:[function(t,n,o){"use strict";function e(t,n,o,e,i){n=n||function(t){return t[0]},o=o||function(t){return t[1]},i=i||Array,this.nodeSize=e||64,this.points=t,this.ids=new i(t.length),this.coords=new i(2*t.length);for(var s=0;s<t.length;s++)this.ids[s]=s,this.coords[2*s]=n(t[s]),this.coords[2*s+1]=o(t[s]);r(this.ids,this.coords,this.nodeSize,0,this.ids.length-1,0)}var r=t("./sort"),i=t("./range"),s=t("./within");n.exports=function(t,n,o,r,i){return new e(t,n,o,r,i)},e.prototype={range:function(t,n,o,e){return i(this.ids,this.coords,t,n,o,e,this.nodeSize)},within:function(t,n,o){return s(this.ids,this.coords,t,n,o,this.nodeSize)}}},{"./range":3,"./sort":4,"./within":5}],3:[function(t,n,o){"use strict";n.exports=function(t,n,o,e,r,i,s){for(var u,a,h=[0,t.length-1,0],p=[];h.length;){var f=h.pop(),c=h.pop(),l=h.pop();if(c-l<=s)for(var d=l;d<=c;d++)u=n[2*d],a=n[2*d+1],u>=o&&u<=r&&a>=e&&a<=i&&p.push(t[d]);else{var m=Math.floor((l+c)/2);u=n[2*m],a=n[2*m+1],u>=o&&u<=r&&a>=e&&a<=i&&p.push(t[m]);var v=(f+1)%2;(0===f?o<=u:e<=a)&&(h.push(l),h.push(m-1),h.push(v)),(0===f?r>=u:i>=a)&&(h.push(m+1),h.push(c),h.push(v))}}return p}},{}],4:[function(t,n,o){"use strict";function e(t,n,o,i,s,u){if(!(s-i<=o)){var a=Math.floor((i+s)/2);r(t,n,a,i,s,u%2),e(t,n,o,i,a-1,u+1),e(t,n,o,a+1,s,u+1)}}function r(t,n,o,e,s,u){for(;s>e;){if(s-e>600){var a=s-e+1,h=o-e+1,p=Math.log(a),f=.5*Math.exp(2*p/3),c=.5*Math.sqrt(p*f*(a-f)/a)*(h-a/2<0?-1:1);r(t,n,o,Math.max(e,Math.floor(o-h*f/a+c)),Math.min(s,Math.floor(o+(a-h)*f/a+c)),u)}var l=n[2*o+u],d=e,m=s;for(i(t,n,e,o),n[2*s+u]>l&&i(t,n,e,s);d<m;){for(i(t,n,d,m),d++,m--;n[2*d+u]<l;)d++;for(;n[2*m+u]>l;)m--}n[2*e+u]===l?i(t,n,e,m):i(t,n,++m,s),m<=o&&(e=m+1),o<=m&&(s=m-1)}}function i(t,n,o,e){s(t,o,e),s(n,2*o,2*e),s(n,2*o+1,2*e+1)}function s(t,n,o){var e=t[n];t[n]=t[o],t[o]=e}n.exports=e},{}],5:[function(t,n,o){"use strict";function e(t,n,o,e){var r=t-o,i=n-e;return r*r+i*i}n.exports=function(t,n,o,r,i,s){for(var u=[0,t.length-1,0],a=[],h=i*i;u.length;){var p=u.pop(),f=u.pop(),c=u.pop();if(f-c<=s)for(var l=c;l<=f;l++)e(n[2*l],n[2*l+1],o,r)<=h&&a.push(t[l]);else{var d=Math.floor((c+f)/2),m=n[2*d],v=n[2*d+1];e(m,v,o,r)<=h&&a.push(t[d]);var g=(p+1)%2;(0===p?o-i<=m:r-i<=v)&&(u.push(c),u.push(d-1),u.push(g)),(0===p?o+i>=m:r+i>=v)&&(u.push(d+1),u.push(f),u.push(g))}}return a}},{}]},{},[1])(1)});