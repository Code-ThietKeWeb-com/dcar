<?php
if(empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest') {  
  die('Hacking attempt!');
}
session_start();
error_reporting( E_CORE_ERROR | E_CORE_WARNING | E_COMPILE_ERROR | E_ERROR | E_WARNING | E_PARSE | E_USER_ERROR | E_USER_WARNING | E_RECOVERABLE_ERROR );
@ini_set("display_errors", "1");
@ini_set("arg_separator.output", "&amp;");
if (! isset($_SESSION['ses_admin'])) {
  $_SESSION['ses_admin'] = md5(uniqid(rand(), TRUE)) ;
}

require_once ("../_config.php");
//Defines
define('IN_vnT', 1);
define('DS', DIRECTORY_SEPARATOR);
if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') {
  $rootURI = str_replace('https://' . $_SERVER['HTTP_HOST'], "", $conf['rooturl']);
  @setcookie('PHPSESSID', session_id(), time()+180, '/', "",true, true);
}else{
  $rootURI = str_replace('http://' . $_SERVER['HTTP_HOST'], "", $conf['rooturl']);
}
$folder_admin = ($conf['folder_admin']) ? $conf['folder_admin'] : "admin";

define('FOLDER_ADMIN', $folder_admin);
if ( !defined('COOKIE_DOMAIN') )
  define('COOKIE_DOMAIN', false);
if ( !defined('COOKIEPATH') )
  define('COOKIEPATH', preg_replace('|https?://[^/]+|i', '', $conf['rooturl'] ) );
if ( !defined('ADMIN_COOKIE_PATH') )
  define( 'ADMIN_COOKIE_PATH', COOKIEPATH . $folder_admin );
if ( !defined('COOKIEHASH') )
  define('COOKIEHASH', md5($conf['rooturl']));
if ( !defined('AUTH_COOKIE') )
  define('AUTH_COOKIE', 'vnt_admin_'.COOKIEHASH);
if ( !defined('LOGGED_IN_COOKIE') )
  define('LOGGED_IN_COOKIE', 'vnt_login_'.COOKIEHASH);

//Functions
include ($conf['rootpath'] . 'includes/class_db.php');
include ($conf['rootpath'] . 'includes/class_functions.php');
require_once($conf['rootpath'] ."includes/JSON.php");

$DB = new DB;
$func = new Func_Global;

$go_login = 1;
$arr_auth = explode("|",$_COOKIE[AUTH_COOKIE]);

if($arr_auth[2] == $_SESSION['ses_admin']){
  $res_ck = $DB->query("SELECT * FROM admin WHERE username='".base64_decode($arr_auth[0])."' ");
  if($row_ck = $DB->fetch_row($res_ck))
  {
    $auth_hash = md5($row_ck['adminid'] . '|' . $row_ck['password']);
    if($arr_auth[1] ==$auth_hash )
    {
      $go_login=0;
    }
  }
}
if($go_login==1) {die('Hacking attempt!');}

switch ($_GET['do']) {
	case "friendly_url":    $jsout = do_friendly_url();  break;
  case "notifi":    $jsout = do_Notifi();  break;
  default:    $jsout = "Error";  break;
}
$DB->close();

//do_friendly_url
function do_friendly_url() 
{
	global $DB,$func,$conf,$vnT;
	$arr_json = array(); 
	$text = $_POST['text'];  
	$html = $func->make_url($text) ;
	
	$arr_json['html'] = $html ;
	$json = new Services_JSON( );
	$textout = $json->encode($arr_json);

	return $textout;
}

//do_Notifi
function do_Notifi() {
  global $DB, $vnT;
  $arr_menu = @explode(",",$_POST['menu_list']);

  $arr_json = array();
  $arr_json['contact'] = 0;

  $res = $DB->query("SELECT id FROM contact WHERE status = 0");
  if($num = $DB->num_rows($res)){
    $arr_json['contact'] = $num;
  }
  $DB->free_result($res);
  if(@in_array("order",$arr_menu))
	{
    $arr_json['order'] = 0;
    $res = $DB->query( "select order_id from order_sum where status = 1" );
    if($num = $DB->num_rows($res)){
      $arr_json['order'] = $num;
    }
    $DB->free_result($res);

	}

  $json = new Services_JSON();
  $textout = $json->encode($arr_json);
  return $textout;
}


flush();
echo $jsout;
exit();
?>