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

// Ham Get_Sub
function Get_Sub ($cid, $n, $did = -1, $lang)
{
  global $func, $DB, $conf;
  $output = "";
  $k = $n;
  $query = $DB->query("SELECT  n.menu_id,nd.title FROM menu n ,menu_desc nd WHERE n.menu_id=nd.menu_id AND parentid={$cid} AND lang='$lang' ORDER BY menu_order ASC, n.menu_id ASC");
  while ($row = $DB->fetch_row($query)) {
    $title = $func->HTML($row['title']);
    if ($row['menu_id'] == $did) {
      $output .= "<option value=\"{$row['menu_id']}\" selected>";
      for ($i = 0; $i < $k; $i ++)
        $output .= "|--";
      $output .= "{$title}</option>";
    } else {
      $output .= "<option value=\"{$row['menu_id']}\" >";
      for ($i = 0; $i < $k; $i ++)
        $output .= "|--";
      $output .= "{$title}</option>";
    }
    $n = $k + 1;
    $output .= Get_Sub($row['menu_id'], $n, $did, $lang);
  }
  return $output;
}
$pos = $_POST['pos'];
$did = (int) $_POST['did'];
$lang = ($_POST['lang']) ? $_POST['lang'] : "vn";
$sql = "SELECT  n.menu_id,nd.title FROM menu n ,menu_desc nd WHERE n.menu_id=nd.menu_id AND parentid=0 AND pos='$pos'  AND lang='$lang' ORDER BY menu_order ASC , n.menu_id ASC ";
$res = $DB->query($sql);
$jsout = "<select size=1 id=\"parentid\" name=\"parentid\" class='select' {$ext} >";
$jsout .= "<option value=\"0\">-- ROOT --</option>";
if ($num = $DB->num_rows($res)) {
  while ($row = $DB->fetch_row($res)) {
    $title = $func->HTML($row['title']);
    if ($row['menu_id'] == $did)
      $jsout .= "<option value=\"{$row['menu_id']}\" selected>{$title}</option>";
    else
      $jsout .= "<option value=\"{$row['menu_id']}\" >{$title}</option>";
    $n = 1;
    $jsout .= Get_Sub($row['menu_id'], $n, $did, $lang);
  }
}
$jsout .= "</select>";
flush();
echo $jsout;
exit();
?>