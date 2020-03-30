<?php
// no direct access
if (! defined('IN_vnT')) {
  die('Hacking attempt!');
}
//Defines
define('ROOT_URL', $conf['rooturl']);
$rootURI = str_replace('http://' . $_SERVER['HTTP_HOST'], "", $conf['rooturl']);
define('ROOT_URI', $rootURI);
define('PATH_INCLUDE', PATH_ROOT . DS . 'includes');
define('PATH_LIBRARIES', PATH_ROOT . DS . 'libraries');
define('PATH_PLUGINS', PATH_ROOT . DS . 'plugins');
define('DIR_LANG', PATH_ROOT . DS . 'language');
define('DIR_SKIN', PATH_ROOT . DS . 'skins');
define('DIR_MODULE', PATH_ROOT . DS . 'modules');
define('DIR_BLOCK', PATH_ROOT . DS . 'blocks');

if ( !defined('COOKIE_DOMAIN') )
	define('COOKIE_DOMAIN', false);
if ( !defined('COOKIE_PATH') )
	define('COOKIE_PATH', preg_replace('|https?://[^/]+|i', '', $conf['rooturl'] ) );

if ( !defined('COOKIEHASH') )
	define('COOKIEHASH', md5($conf['rooturl']));	
if ( !defined('AUTH_COOKIE') )
	define('MEMBER_COOKIE', 'vnt_member_'.COOKIEHASH);
	
// Define safe_mode
define('SAFEMODE', (@ini_get('safe_mode') == 1 or strtolower(@ini_get('safe_mode')) == 'on') ? true : false);
?>