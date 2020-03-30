/*
 * Google map v3
 */
 
/* add script for admin google map */
var geocoder;
var map;
var marker;
var old_marker ;
var infowindow;
var contentString = '<div style="margin:5px ; width:300px; height:150px "><div align="left" style="font-size:11px; font-weight:bold; color:#30A602">Thông tin vị trí </div><div style="margin:5px 0px 5px 0px"><textarea class="form_control" id="gmap_information" name="gmap_information" style="width:300px; height:70px">' + information + '</textarea><div align="left" style="color:#787878; font-size:10px">Tối đa 200 ký tự.</div><div align="center" style="color:#FF0000; font-size:11px">Bạn phải ấn nút <b>"Lưu vị trí"</b> vị trí trên bản đồ mới được lưu</div></div><div><input type="button" class="form_button" value="Lưu vị trí" style="width:75px; font-size:11px" onclick="set_position_map()" />&nbsp;<input type="button" class="form_button" value="Xóa" style="width:75px; font-size:11px" onclick="clear_position_map()" /></div></div>';

window.onload = function()
{
    	//initalizetab("tabheader");
		geocoder = new google.maps.Geocoder();
        
		var options = {
				zoom: 17, 
				mapTypeId: google.maps.MapTypeId.ROADMAP
		};
		map = new google.maps.Map(document.getElementById('map'), options);  
  
		// get map lat & lng
		var map_lat = document.getElementById("map_lat").value;
		var map_lng = document.getElementById("map_lng").value;
		var realPosition = new google.maps.LatLng(map_lat, map_lng);
		
		//var info = document.getElementById("address").value;
		var info = getAddress();
	
		if ( map_lat == 0 && map_lng == 0 )
		{
			// dung dia chi
	        //var address = document.getElementById("address").value;
	        var address = getAddress();
	        geocoder.geocode( { 'address': address}, function(results, status) {
	            if (status == google.maps.GeocoderStatus.OK) 
							{
	                map.setCenter(results[0].geometry.location);
	                marker = new google.maps.Marker({
	                    map: map,
	                    position: results[0].geometry.location,
	                    clickable: true,
	                    mapTypeId: google.maps.MapTypeId.ROADMAP,
											draggable: true
	                });
									
	                updateMarkerPosition(marker);
	                var infowindow = new google.maps.InfoWindow({
	                    content: info
	                });//end InfoWindow
	                google.maps.event.addListener(marker, 'click', function() {
	                    infowindow.open(map, marker);
	                });//end infoWindow
	                google.maps.event.addListener(marker, 'dragend', function() {
	                	updateMarkerPosition(marker);
	                });
									
	            } else {
								
	            	map.setCenter(defaultPosition);
	                marker = new google.maps.Marker({
	                    map: map,
	                    position: defaultPosition,
	                    title: 'Default',
	                    clickable: true,
	                    mapTypeId: google.maps.MapTypeId.ROADMAP,
											draggable: true
	                });
	                updateMarkerPosition(marker);
	                var infowindow = new google.maps.InfoWindow({
	                    content: 'Default position'
	                });//end InfoWindow
									
	                google.maps.event.addListener(marker, 'click', function() {
	                    infowindow.open(map, marker);
	                });//end infoWindow
	                google.maps.event.addListener(marker, 'dragend', function() {
										updateMarkerPosition(marker);
	                });
	            }
	        });
		}
		else
		{
			// dung 2 thong so map_lat va map_lng
	
			map.setCenter(realPosition);
	        marker = new google.maps.Marker({
	            map: map,
	            position: realPosition,
	            clickable: true,
	            mapTypeId: google.maps.MapTypeId.ROADMAP,
							draggable: true
            });
           
					 old_marker = marker ;
	        	updateMarkerPosition(marker);
 				
            var infowindow = new google.maps.InfoWindow({
            	content: contentString
            });//end InfoWindow            
						infowindow.open(map, marker);
						
            google.maps.event.addListener(marker, 'click', function() {            	
            	infowindow.open(map, marker);
						});//end infoWindow
			
            google.maps.event.addListener(marker, 'dragend', function() {
            	updateMarkerPosition(marker);
            });
		}
		//divfooter();
        /* add script for admin google map */
		
			
}

function getAddress()
{
	//alert('vao toi address');
	var address = document.getElementById("map_address").value;	 
	return address;	
}

function updateMap( address )
{
	//alert('update map');
	// get position by address
	// var currentPos = getPositionByAddress( address );
	geocoder.geocode(
			{ 'address': address },
			function(results, status)
			{
		    	if (status == google.maps.GeocoderStatus.OK)
		        {
		        	var pos = results[0].geometry.location;
		        	map.setCenter(pos);

		        	marker.setPosition(pos);
		        	updateMarkerPosition(marker);
		        }
		    	else
		    	{
			    	//alert('<?php echo JText::_('ADDRESS_NOT_FOUND');?>');
		    	}
			});
}

function updateMarkerPosition(curMarker)
{
	//alert('update marker');
	/*if(document.getElementById("gmap_information")){
		document.getElementById("map_information").value = document.getElementById("gmap_information").value;
	}
	document.getElementById("map_lat").value = curMarker.getPosition().lat();
	document.getElementById("map_lng").value = curMarker.getPosition().lng();*/
}



function updateCenterMap(mapobj, cur_marker)
{
	map.setCenter(marker.getPosition());
}

function createMap(divid,maplat,maplng,_zoom){
    var latlng = new google.maps.LatLng(maplat,maplng);
    var myOptions = {
      zoom: _zoom,
      center: latlng,
      mapTypeId: google.maps.MapTypeId.ROADMAP
    };
    var mapobj = new google.maps.Map(document.getElementById(divid),myOptions);   
    
    // add marker
    var markerobj = new google.maps.Marker({
        position: latlng, 
        map: mapobj,
			  draggable: true
    });
    google.maps.event.addListener(markerobj, 'dragend', function() {
    	mapobj.setCenter(markerobj.getPosition());
    	updateMarkerPosition( markerobj );
    });
}
function renderMap(divid,maplat,maplng,_zoom){
    var latlng = new google.maps.LatLng(maplat,maplng);
    var myOptions = {
      zoom: _zoom,
      center: latlng,
      mapTypeId: google.maps.MapTypeId.ROADMAP
    };
    var mapobj = new google.maps.Map(document.getElementById(divid),myOptions);   
    
    // add marker
    var markerobj = new google.maps.Marker({
        position: latlng, 
        map: mapobj		
    });
}
/*
 * end Google map v3
 */
function onChangeAddress()
{
	// confirm changed
	//if (confirm("Bạn có muốn cập nhật vị trí mới trên bản đồ"))
	//if (confirm("<?php echo JText::_('CONFIRM_UPDATE_MAP');?>"))
	//{
		// get new address
		var address = getAddress();			
		// update marker & map
		updateMap(address);		
		
	//}
}


function set_position_map()
{
	//alert('update marker');
	if (confirm("Bạn có chắc muốn cập nhật thông tin vị trí mới này trên bản đồ"))
	{
		if(document.getElementById("gmap_information")){
			document.getElementById("map_information").value = document.getElementById("gmap_information").value;
		} 
		document.getElementById("map_lat").value = marker.getPosition().lat();
		document.getElementById("map_lng").value = marker.getPosition().lng();
	}
}

		function mapCancel(){
			 
			marker.setMap(null);
			
			map.setCenter(defaultPosition);
			marker = new google.maps.Marker({
					map: map,
					position: defaultPosition, 
					clickable: true,
					mapTypeId: google.maps.MapTypeId.ROADMAP,
					draggable: true
			});
			updateMarkerPosition(marker); 
			var infowindow = new google.maps.InfoWindow({
				content: contentString
			});//end InfoWindow            
			infowindow.open(map, marker);
						
		 
			
			google.maps.event.addListener(marker, 'click', function() {
					infowindow.open(map, marker);
			});//end infoWindow
			google.maps.event.addListener(marker, 'dragend', function() {
				updateMarkerPosition(marker);
			});
		}
		
		function clear_position_map(){
			
			ob1 = document.getElementById("map_lat");
			ob2 = document.getElementById("map_lng");
			ob3 = document.getElementById("map_information");
			 
			ob1.value	= map_lat;
			ob2.value	= map_lng;
			ob3.value	= information;
			
			mapCancel(); 
		}
		




