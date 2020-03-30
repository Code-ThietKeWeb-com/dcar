<?php
@ini_set("display_errors", "0");
define('IN_vnT', 1);
define('DS', DIRECTORY_SEPARATOR);
require_once ("../../../_config.php");
require_once ($conf['rootpath'] . "includes/class_db.php");
$DB = new DB();
include ($conf['rootpath'] . "includes/class_functions.php");
$func = new Func_Global();
$conf = $func->fetchDbConfig();

//maps
$id = (int) $_GET['id'];
$w = ($_GET['w']) ? $_GET['w'] : 625;
$h = ($_GET['h']) ? $_GET['h'] : 505;
$query = $DB->query("SELECT * FROM contact_config WHERE id=$id");
if ($row_m = $DB->fetch_row($query)) {
  $data['description'] = str_replace("\r\n", "<br>", $row_m['map_desc']);
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
$description .= $data['description'];
$description .= '</div>';
?>
<html>
<head>
<title>GoogleMap</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

<body style="margin:0px;" >
<?php
  switch ($row_m['map_type'])
  {
    case 1 :
?>
      <div id="map_canvas" style="width: 100%; height: 100%"></div>
      <script type="text/javascript">
        var map;
        var infowindow;

        function initMap(){
          contentString = '<?php
            echo $description;
            ?>';

          var defaultLatLng = new google.maps.LatLng(<?php echo $data['map_lat']; ?>, <?php echo $data['map_lng']; ?>);
          var myOptions= {zoom: 15,
            center: defaultLatLng,
            scrollwheel : false,
            mapTypeId: google.maps.MapTypeId.ROADMAP};
          map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
          map.setCenter(defaultLatLng);

          clickmarker = new google.maps.Marker({
            position: defaultLatLng,
            clickable: true,
            cursor: "pointer",
            map: map
          });

          infowindow= new google.maps.InfoWindow({content: contentString});
          infowindow.open(map, clickmarker);

          google.maps.event.addListener(clickmarker, 'click', function(){
            infowindow.open(map, clickmarker);
          });

        }

      </script>
      <script type="text/javascript" src="//maps.google.com/maps/api/js?key=<?php echo $conf['GoogleMapsAPIKey']?>&language=vi&callback=initMap"></script>
<?php
      break;
    case 2 :
?>
    <div style=	"background-image: url(<?=$data['map_picture']?>); height: 100%; background-position: center; background-repeat: no-repeat; "></div>
<?php
      break;
    case 3 :
      echo  $data['map_embed'];
      break;
  }
?>

</body>
</html>