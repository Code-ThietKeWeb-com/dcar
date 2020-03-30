var ceMap = {
	mapCanvas: null,
	dirContainer: null,
	mapInUse: false,
	companyPos: null,
	lat: 40,
	lng: -100,
	zoom: 8,
	travelMode: 'DRIVING',
	companyMarker: null,
	markerImage: null,
	markerShadow: null,
	companyImage: null,
	companyShadow: null,
	infowindow: null,
	infoWindowDisplay: 'alwaysOn',
	mapTypeControl: true,
	mapTypeId: google.maps.MapTypeId.ROADMAP,
	mapStyle: null,
	navigationControl: true,
	scrollwheel: true,
	dirService: null,
	dirRenderer: null,
	map: null,
	geocoder: new google.maps.Geocoder(),
	input: {
		highways: null,
		tolls: null,
		address: null,
		lat: null,
		lng: null,
		zoom: null
	},
	lang: {
		directionsFailed: 'Directions failed',
		geocodeError: 'Geocode was not successful for the following reason',
		showIPBasedLocation: 'Show IP-Based Location',
		address: 'Address'
	},
	getMarkerImage: function (markerIcon, markerShadow) {
		if (markerIcon) {
			ceMap.companyImage = new google.maps.MarkerImage(markerIcon)
		}
		if (markerShadow) {
			ceMap.companyMarker = new google.maps.MarkerImage(markerShadow)
		}
	},
	showDirections: function (dirResult, dirStatus) {
		if (dirStatus != google.maps.DirectionsStatus.OK) {
			alert(ceMap.lang.directionsFailed + ': ' + dirStatus);
			return
		}
		ceMap.dirRenderer.setMap(ceMap.map);
		ceMap.dirRenderer.setPanel(ceMap.dirContainer);
		ceMap.dirRenderer.setDirections(dirResult)
	},
	getSelectedTravelMode: function () {
		return ceMap.travelMode
	},
	getDirections: function () {
		if (ceMap.mapInUse) {
			ceMap.reset()
		}
		ceMap.mapInUse = true;
		ceMap.dirService = new google.maps.DirectionsService();
		ceMap.dirRenderer = new google.maps.DirectionsRenderer();
		ceMap.dirContainer = document.getElementById('ce-directionsPanel');
		
		var dirRequest = {
			origin: $('#'+ceMap.input.address).val(),
			destination: ceMap.companyPos,
			travelMode: ceMap.getSelectedTravelMode(),
			provideRouteAlternatives: true,
			avoidHighways: $("#"+ceMap.input.highways).attr('checked'),
			avoidTolls: $("#"+ceMap.input.tolls).attr('checked')
		};
		ceMap.dirService.route(dirRequest, ceMap.showDirections)
	},
	reset: function () {
		if (!ceMap.mapInUse) {
			return false
		}
		ceMap.init();
		ceMap.dirRenderer.setMap(null);
		ceMap.dirRenderer.setPanel(null);
		ceMap.dirRenderer = new google.maps.DirectionsRenderer();
		ceMap.dirRenderer.setMap(ceMap.map);
		ceMap.dirRenderer.setPanel(ceMap.dirContainer)
	},
	init: function () {
		if (ceMap.lat == 40 && ceMap.lng == -100 && google.loader.ClientLocation) {
			ceMap.lat = google.loader.ClientLocation.latitude;
			ceMap.lng = google.loader.ClientLocation.longitude;
			if (!ceMap.infoWindowContent) {
				ceMap.infoWindowContent = ceMap.lang.showIPBasedLocation + ': <br />' + '<b>' + ceMap.getFormattedLocation() + '</b>'
			} 
			if (ceMap.editMode) {
				$("#"+ceMap.input.lat).val(ceMap.lat);
				$("#"+ceMap.input.lng).val(ceMap.lng)
			}
		}
		if ($('#ipBasedLocation') && google.loader.ClientLocation) {
			$('#ipBasedLocation').val(ceMap.getFormattedLocation())
		}
		ceMap.companyPos = new google.maps.LatLng(ceMap.lat, ceMap.lng);
		origin = ceMap.companyPos;
		var companyOptions = {
			zoom: ceMap.zoom,
			center: origin,
			mapTypeControl: ceMap.typeControl,
			mapTypeId: ceMap.typeId,
			navigationControl: ceMap.navigationControl,
			scrollwheel: ceMap.scrollwheel
		};


		var obj_maps = document.getElementById(ceMap.mapCanvas);
		ceMap.map = new google.maps.Map(obj_maps, companyOptions);
		if(ceMap.mapStyle){
			ceMap.map.setOptions({styles: ceMap.mapStyle});
		}


		ceMap.companyMarker = new google.maps.Marker({
			position: ceMap.companyPos,
			map: ceMap.map,
			icon: ceMap.companyImage,
			shadow: ceMap.companyShadow,
			title: ceMap.mapTitle,
			draggable: ceMap.companyMarkerDraggable,
			zIndex: 4
		});
		ceMap.infowindow = new google.maps.InfoWindow({
			content: ceMap.infoWindowContent
		});
		if (ceMap.infoWindowDisplay == 'onClick') {
			google.maps.event.addListener(ceMap.companyMarker, 'click', function () {
				ceMap.infowindow.open(ceMap.map, ceMap.companyMarker)
			})
		} else if (ceMap.infoWindowDisplay == 'alwaysOn') {
			ceMap.infowindow.open(ceMap.map, ceMap.companyMarker)
		}
		if (ceMap.companyMarkerDraggable) {
			google.maps.event.addListener(ceMap.companyMarker, 'drag', function () {
				ceMap.infowindow.close()
			});
			google.maps.event.addListener(ceMap.companyMarker, 'dragend', function () {
				if (ceMap.editMode) {
					$("#"+ceMap.input.lat).val(ceMap.companyMarker.getPosition().lat());
					$("#"+ceMap.input.lng).val(ceMap.companyMarker.getPosition().lng())
				}
				ceMap.infowindow.open(ceMap.map, ceMap.companyMarker)
			})
		}
		if (ceMap.editMode) {
			google.maps.event.addListener(ceMap.map, 'zoom_changed', function () {
				zoomLevel = ceMap.map.getZoom();
				if (zoomLevel == 0) {
					ceMap.map.setZoom(10)
				}
				$("#"+ceMap.input.zoom).val(ceMap.map.getZoom())
			})
		}
	},
	getFormattedLocation: function () {
		var neighborhood = '';
		if (google.loader.ClientLocation.address.neighborhood) {
			var neighborhood = google.loader.ClientLocation.address.neighborhood + ', '
		}
		if (google.loader.ClientLocation.address.region) {
			return neighborhood + google.loader.ClientLocation.address.city + ', ' + google.loader.ClientLocation.address.region.toUpperCase() + ', ' + google.loader.ClientLocation.address.country
		} else {
			return neighborhood + google.loader.ClientLocation.address.city + ', ' + google.loader.ClientLocation.address.country
		}
	},
	codeAddress: function () {
		var address = $("#"+ceMap.input.address).val();
		ceMap.geocoder.geocode({
			'address': address
		},
		function (results, status) {
			if (status == google.maps.GeocoderStatus.OK) {
				ceMap.map.setCenter(results[0].geometry.location);
				ceMap.companyMarker.setPosition(results[0].geometry.location);
				if (ceMap.map.getZoom() < 10) {
					ceMap.map.setZoom(15)
				}
				ceMap.infowindow.setContent($("#"+ceMap.input.address).val());
				$("#"+ceMap.input.lat).val(results[0].geometry.location.lat());
				$("#"+ceMap.input.lng).val(results[0].geometry.location.lng())
			} else {
				alert(ceMap.lang.geocodeError + ": " + status + "\n\n" + ceMap.lang.address + ": " + address)
			}
		})
	}
};