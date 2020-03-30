<?php
@ini_set("display_errors", "0");
define('IN_vnT', 1);
define('DS', DIRECTORY_SEPARATOR);
require_once ("../../../_config.php");
require_once ($conf['rootpath'] . "includes/class_db.php");
$DB = new DB();
include ($conf['rootpath'] . "includes/class_functions.php");
$func = new Func_Global();
$conf = $func->fetchDbConfig($conf);
$vnT->lang_name = (isset($_GET['lang'])) ? $_GET['lang'] : "vn"; 
$func->load_language('contact');

$id = (int) $_GET['id'];
$map1 = (int) $_GET['map1']; 

$w = ($_GET['w']) ? $_GET['w'] : 420;
$h = ($_GET['h']) ? $_GET['h'] : 500;
$query = $DB->query("SELECT * FROM contact_config WHERE id=$id");
if ($row_m = $DB->fetch_row($query)) {
  $info_contact = '<div class="info_dealer">'.$row_m['description'].'</div>';  
	$map_desc = str_replace("\r\n", "<br>", $row_m['map_desc']);
	$data['map_lat'] = ($row_m['map_lat']) ? $row_m['map_lat'] : "10.804866895605";
	$data['map_lng'] = ($row_m['map_lng']) ? $row_m['map_lng'] : "106.64199984239";
  $data['map_picture'] = $conf['rooturl'] . $row_m['map_picture'] ;
  $data['map_embed'] =   $func->txt_unHTML($row_m['map_embed']);
} else {
  $data['map_lat'] = "10.804866895605";
  $data['map_lng'] = "106.64199984239";
}
//		echo $data['description'] ;
$description = '<div style="width: 300px; padding-right: 5px; font-family: Arial; font-size: 12px;">';
$description .= $map_desc;
$description .= '</div>';
?>

<?php
switch ($row_m['map_type'])
{
case 1 :
?>

<script type="text/javascript">
jQuery(document).ready( function($) {
	ceMap.lat	= <?php echo $data['map_lat']; ?>;	
	ceMap.lng	= <?php echo $data['map_lng']; ?>;	
	ceMap.zoom	= 16;	
	ceMap.useDirections	= true;	
	ceMap.showCoordinates	= true;	
	ceMap.mapTitle	= '';	
	ceMap.infoWindowDisplay	= 'alwaysOn';	
	ceMap.infoWindowContent	= '<?php echo $description; ?>';	
	ceMap.scrollwheel	= true;	
	ceMap.mapContainer	= 'ce_map_container';	
	ceMap.mapCanvas	= 'ce_map_canvas';	
	ceMap.jsObjName	= 'ceMap';	
	ceMap.markerImage	= false;	
	ceMap.markerShadow	= false;	
	ceMap.companyMarkerDraggable	= false;	
	ceMap.typeControl	= '1';	
	ceMap.typeId	= 'ROADMAP';	
	ceMap.navigationControl	= '1';	
	ceMap.travelMode	= 'DRIVING';	
	ceMap.input.lat	= false;	
	ceMap.input.lng	= false;	
	ceMap.input.zoom	= false;	
	ceMap.input.address	= 'dir_address';	
	ceMap.input.highways	= 'dir_highways';	
	ceMap.input.tolls	= 'dir_tolls';	
	ceMap.lang.showIPBasedLocation	= 'Showing IP-based location';	
	ceMap.lang.directionsFailed	= 'Directions failed';	
	ceMap.lang.geocodeError	= 'Geocode was not successful for the following reason';	
	ceMap.typeId=google.maps.MapTypeId.ROADMAP ;
	ceMap.getMarkerImage(ceMap.markerImage,ceMap.markerShadow);
	ceMap.init();
	
	jQuery("#ce-map-cpanel-container").hide();
	jQuery('.ce-route').click(function() {
		jQuery("#ce-map-cpanel-container").slideToggle(150);
		jQuery('#dir_address').focus();
	});	
	
});
 

</script>

  
<div id="ce_map_container">
  <div id="ce_map_canvas"  style="height:<?php echo $h; ?>px;"></div>
  <div id="ce-map-coordinates" style="display:none;">
    <div class="ce-map-lat"><span class="ce-map-coord-label">Latitude: </span><span class="ce-map-coord-value"><?php echo $data['map_lat']; ?></span></div>
    <div class="ce-map-lng"><span class="ce-map-coord-label">Longitude: </span><span class="ce-map-coord-value"><?php echo $data['map_lng']; ?></span></div>
  </div>
  <div id="ce-map-cpanel-switch"><a href="javascript:void(0);" class="ce-route ce-boxed" ><?php echo $vnT->lang['contact']['get_directions']; ?></a></div>
  <div id="ce-map-cpanel-container">
    <div id="ce-map-cpanel" class="ce-map-cpanel">
      <form action="" onSubmit="return false;">
        <fieldset>
          <legend><?php echo $vnT->lang['contact']['route_options']; ?></legend>
          <table width="100%" border="0" cellspacing="0" cellpadding="0"><tr><td width="65%"><div class="from-address"><?php echo $vnT->lang['contact']['from_address']; ?></div>
            <input type="text"  id="dir_address" name="address" value="" class="textfiled" style="width:98%" />  
    <div >
            <label for="highways" class="labelCheckbox">  <input type="checkbox" class="inputbox" id="dir_highways" name="highways" /> <?php echo $vnT->lang['contact']['avoid_highways']; ?></label> &nbsp;
            <label for="tolls" class="labelCheckbox"> <input type="checkbox" class="inputbox" id="dir_tolls" name="tolls" />  <?php echo $vnT->lang['contact']['avoid_tolls']; ?></label>
          </div>
     </td>
    <td> <div class="submit"> 
            	<button  id="ce-map-submit" name="ce-map-submit" type="button" class="btn" onclick="ceMap.getDirections();"  ><span ><?php echo $vnT->lang['contact']['btn_go']; ?></span></button> 
              <button type="reset"  class="btn" id="ce-map-submit"	onclick="ceMap.reset();"	><span> <?php echo $vnT->lang['contact']['btn_reset']; ?></span> </button> 
          </div></td>
  </tr>
</table>

          
          
        </fieldset>
      </form>
    </div>
  </div>
  <div id="ce-directionsPanel"></div>
</div>

<?php
  break;
  case 2 :
    echo '<div id="Map" class="maps" ><img src="'.$data['map_picture'].'" alt="map_picture"   /></div>';
  break;
  case 3 :
    echo  '<div align="center" class="embed-responsive embed-responsive-16by9">'.$data['map_embed'].'</div>';
  break;
}
?>