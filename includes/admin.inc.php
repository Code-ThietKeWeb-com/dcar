<?php
/*================================================================================*\
|| 							Name code : admin.inc.php 		 		 			  ||											  # ||
||  				Copyright © 2008 by Thai Son - CMS vnTRUST                    ||
\*================================================================================*/
/**
 * @version : 1.0
 * @date upgrade : 11/12/2007 by Thai Son
 **/

/*if (preg_match("/admin.inc.php /i", $_SERVER['PHP_SELF']) || preg_match("/admin.inc.php/i", $_SERVER['PHP_SELF'])) {
  die ("<html>\r\n<head>\r\n<title>Forbidden 403</title>\r\n</head>\r\n<body><h3>Forbidden 403</h3>\r\nThe document you are requesting is forbidden.<br />\r\n<a href='http://www.trust.vn'>www.trust.vn</a>\r\n</body>\r\n</html>");
  exit();
}*/

session_start();
error_reporting( E_CORE_ERROR | E_CORE_WARNING | E_COMPILE_ERROR | E_ERROR | E_WARNING | E_PARSE | E_USER_ERROR | E_USER_WARNING | E_RECOVERABLE_ERROR );
@ini_set("display_errors", "1");
@ini_set("arg_separator.output", "&amp;");
if (! isset($_SESSION['ses_admin'])) {
  $_SESSION['ses_admin'] = md5(uniqid(rand(), TRUE)) ;
}
//Defines
$parts = explode(DS, PATH_ADMIN);
array_pop($parts);
define('PATH_ROOT', implode(DS, $parts));
define('PATH_INCLUDE', PATH_ROOT . DS . 'includes');
define('PATH_UPLOAD', PATH_ROOT .DS . 'vnt_upload');
define('PATH_LIBRARIES', PATH_ROOT . DS . 'libraries');
define('PATH_PLUGINS', PATH_ROOT . DS . 'plugins'); 
define('ROOT_URL', $conf['rooturl']);
if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') {   
	$rootURI = str_replace('https://' . $_SERVER['HTTP_HOST'], "", $conf['rooturl']);
  @setcookie('PHPSESSID', session_id(), time()+180, '/', "",true, true);
}else{
	$rootURI = str_replace('http://' . $_SERVER['HTTP_HOST'], "", $conf['rooturl']);
}
$folder_admin = ($conf['folder_admin']) ? $conf['folder_admin'] : "admin";
define('ROOT_URI', $rootURI);
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
require_once (PATH_INCLUDE . DS . 'class_db.php');
require_once (PATH_INCLUDE . DS . 'class_functions.php');
require_once (PATH_INCLUDE . DS . 'admin.class.php');
require_once (PATH_INCLUDE . DS . 'class.XiTemplate.php');
require_once (PATH_INCLUDE . DS . 'admin.global.php');

// initialize the data registry
$vnT = new vnT_Registry();
$conf = $vnT->conf ;
$input  = $vnT->input ;
$DB = $vnT->DB ;
$func = $vnT->func ;


// load lang
if (isset($_GET["langcp"])) {
  $conf['langcp'] = $_GET["langcp"];
  $_SESSION['langcp'] = $_GET["langcp"];
}
if (isset($_SESSION['langcp'])){
  $conf['langcp'] = $_SESSION['langcp'];
}

$mod = ($_GET['mod']) ? $_GET['mod'] : "main";
$func->load_language_admin($mod);
//load HTML
$func->include_libraries('vntrust.html.html');
$vnT->html = new vnT_HTML();
//load Format
$func->include_libraries('vntrust.html.format');
$vnT->format = new vnT_Format();
//load Editor
$func->include_libraries('vntrust.html.editor');
$vnT->editor = vnT_Editor::loadEditor();
$vnT->editor->doInit();
//load mailer
$func->include_libraries('phpmailer.phpmailer');
$vnT->mailer = new PHPMailer();

//File 
include (PATH_INCLUDE . DS . 'class_files.php');
$vnT->File  = new FileSystem ;


define('DIR_SKIN', PATH_ADMIN . DS . 'skins' . DS . $conf['skin_acp']);
define('DIR_MODULE', PATH_ADMIN . DS . 'modules');


$vnT->conf['indextitle'] = ($vnT->conf['indextitle_vn']) ? $vnT->conf['indextitle_vn'] : $vnT->conf['indextitle'];
$vnT->act_allow = array("admin" , "admin_permission" , "admin_menu" , "main" , "popup_media" ,"popup_gallery" , "remote" , "editor" , "login" , "logout" , "lostpass" , "help");
require_once (PATH_INCLUDE . DS . 'class.XiTemplate.php');
require_once (PATH_INCLUDE . DS . 'checkadmincp.php');
require_once (PATH_ADMIN . DS . 'menu.php');
$vnT->menu = new Menu();
?>
