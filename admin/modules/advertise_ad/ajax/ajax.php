<?php
define('IN_vnT', 1);
define('DS', DIRECTORY_SEPARATOR);
require_once ("../../../../_config.php");
include ($conf['rootpath'] . "includes/class_db.php");
$DB = new DB();
require $conf['rootpath'] . "includes/JSON.php";
switch ($_POST['do']) {
  case "get_size":
    $jsout = get_size();
  break;
  default:
    $jsout = "Error";
  break;
}

//-----------
function get_size ()
{
  global $DB, $func, $conf, $vnT;
  $textout = array();
  $pos = $_POST['pos'];
  $result = $DB->query("SELECT  * FROM ad_pos WHERE name='{$pos}' ");
  if ($row = $DB->fetch_row($result)) {
    $textout['width'] = $row['width'];
    $textout['height'] = $row['height'];
  }
  return $textout;
}
$json = new Services_JSON();
print $json->encode($jsout);
?>