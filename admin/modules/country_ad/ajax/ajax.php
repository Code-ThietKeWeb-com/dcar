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
  case "list_city":
    $jsout = get_list_city();
  break;
  case "list_state":
    $jsout = get_list_state();
  break;
  default:
    $jsout = "Error";
  break;
}

//get_list_city
function get_list_city ()
{
  global $DB, $func, $conf, $lang;
  $textout = "";
  $country = $_GET['country'];
  $textout = "<select name=\"city\" id=\"city\" class='select'  {$ext}   >";
  $textout .= "<option value=\"\" selected>-- Chọn tỉnh thành --</option>";
  $sql = "SELECT * FROM iso_cities where display=1 and country='$country'  order by c_order ASC , id DESC  ";
  //	echo $sql;
  $result = $DB->query($sql);
  while ($row = $DB->fetch_row($result)) {
    if ($row['id'] == $did) {
      $textout .= "<option value=\"{$row['id']}\" selected>" . $func->HTML($row['name']) . "</option>";
    } else {
      $textout .= "<option value=\"{$row['id']}\">" . $func->HTML($row['name']) . "</option>";
    }
  }
  $textout .= "</select>";
  return $textout;
}

//get_list_state
function get_list_state ()
{
  global $DB, $func, $conf, $vnT;
  $textout = "";
  $city = $_GET['city'];
  $textout = "<select name=\"state\" id=\"state\" class='select'  style='width:350px;' >";
  $textout .= "<option value=\"0\" selected>-- Chọn quận huyện --</option>";
  $sql = "SELECT * FROM iso_states where display=1 and city={$city}  order by s_order ASC , id DESC  ";
  //	echo $sql;	
  $result = $DB->query($sql);
  if ($num = $DB->num_rows($result)) {
    while ($row = $DB->fetch_row($result)) {
      if ($row['id'] == $did) {
        $textout .= "<option value=\"{$row['id']}\" selected>" . $func->HTML($row['name']) . "</option>";
      } else {
        $textout .= "<option value=\"{$row['id']}\">" . $func->HTML($row['name']) . "</option>";
      }
    }
  }
  $textout .= "</select>";
  return $textout;
}
flush();
echo $jsout;
exit();
?>
