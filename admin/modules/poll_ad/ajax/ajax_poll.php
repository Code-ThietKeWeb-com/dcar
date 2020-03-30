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

require_once ($conf['rootpath'] . "includes" . DS . 'class.XiTemplate.php');
$lang = ($_GET['lang']) ? $_GET['lang'] : "vn";
switch ($_GET['do']) {
  case "del_option":
    $jsout = del_option();
  break;
  default:
    $jsout = "Error";
  break;
}

function del_option ()
{
  global $vnT, $DB, $func, $conf;
  $poll_id = (int) $_POST['poll_id'];
  $op_id = (int) $_POST['op_id'];
  $op_order = (int) $_POST['op_order'];
  $ok = $DB->query("DELETE FROM poller_option WHERE pollerID=$poll_id AND id=$op_id ");
  if ($ok) {
    $textout = "<p class=font_err>Xóa thành công Option  ID <strong>" . $op_id . "</strong></p>";
  } else {
    $textout = "<p class=font_err>Xóa thất bại Option  ID " . $op_id . "</strong></p>";
  }
  return $textout;
}
flush();
echo $jsout;
exit();
?>
