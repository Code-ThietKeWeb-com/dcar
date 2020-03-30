<?php
define('IN_vnT', 1);
define('DS', DIRECTORY_SEPARATOR);
require_once ("../../../../_config.php");
include ($conf['rootpath'] . "includes/class_db.php");
$DB = new DB();
//Functions
include ($conf['rootpath'] . 'includes/class_functions.php');
include($conf['rootpath'] . 'includes/admin.class.php');
$func  = new Func_Admin;
$conf = $func->fetchDbConfig($conf);

switch ($_GET['do']) { 
	case "folder":   $aResults = get_estore();  break;
  default:   $aResults = array();  break;
}
 
//get_estore
function get_estore ()
{
  global $DB, $func, $conf, $vnT;
  $textout = array();
  $lang = ($_GET['lang']) ? $_GET['lang'] : "vn";
  $keyword = $_GET['input'];
  $keyword = strtolower($func->utf8_to_ascii($keyword));
  $sql = "SELECT * FROM media_folders  
					WHERE display=1
					AND folder_path like '%" . $keyword . "%' order by folder_path ASC , date_create DESC";
  
	$res = $DB->query($sql);
  if ($num = $DB->num_rows($res)) {
    while ($row = $DB->fetch_row($res)) { 
      $textout[] = array(
        'id' => $row['folder_id'] , 		  			
        'value' => $row['folder_path'] , 
        'info' =>  "");
    }
  }
  return $textout;
}

header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // always modified
header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Pragma: no-cache"); // HTTP/1.0
if (isset($_REQUEST['json'])) {
  
	header("Content-Type: application/json");
  echo "{\"results\": [";
  $arr = array();
  for ($i = 0; $i < count($aResults); $i ++) {
    $arr[] = "{\"id\": \"" . $aResults[$i]['id'] . "\", \"value\": \"" . $aResults[$i]['value'] . "\", \"info\": \"" . $aResults[$i]['info'] . "\"}";
  }
  echo implode(", ", $arr);
  echo "]}";
} else {
  header("Content-Type: text/xml");
  echo "<?xml version=\"1.0\" encoding=\"utf-8\" ?><results>";
  for ($i = 0; $i < count($aResults); $i ++) {
    echo "<rs id=\"" . $aResults[$i]['id']  . "\" info=\"" . $aResults[$i]['info'] . "\">" . $aResults[$i]['value'] . "</rs>";
  }
  echo "</results>";
}
?>
