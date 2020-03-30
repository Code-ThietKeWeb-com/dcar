<?php
@error_reporting( E_CORE_ERROR | E_CORE_WARNING | E_COMPILE_ERROR | E_ERROR | E_WARNING | E_PARSE | E_USER_ERROR | E_USER_WARNING | E_RECOVERABLE_ERROR );
@ini_set("display_errors", "1");
define('IN_vnT', 1);
require_once("../_config.php");
require_once("../includes/class_db.php");

// initialize the data registry
class vnT_Registry
{
  var $DB;
  var $conf = array();
  public function __construct() {  }
}
$vnT = new vnT_Registry();
$vnT->DB = $DB = new DB;
$lang_name = ($_REQUEST['lang']) ? $_REQUEST['lang'] : "vn";

$content_js = '';

//load lang global
$lang_js = '';
$lang_global = $conf['rootpath']  . "language/".$lang_name."/global.php";
if (file_exists($lang_global)) {
  require_once ($lang_global);
  if (is_array($lang)) {
    foreach ($lang as $k => $v) {
      $lang_js .=  "js_lang['".$k."']='".stripslashes($v)."'; ";
    }
  }
}
$content_js .= $lang_js."\n\n" ; 

$vnT->DB->close();
header("Content-type: application/x-javascript");
flush();
echo $content_js;
exit();
?>