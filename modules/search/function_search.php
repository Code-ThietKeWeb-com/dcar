<?php
/*================================================================================*\
|| 							Name code : function_search.php 		 		 												  # ||
||  				Copyright © 2007 by Thai Son - CMS vnTRUST                					# ||
\*================================================================================*/
/**
 * @version : 1.1
 * @date upgrade : 08/01/2008 by Thai Son
 **/

if (! defined('IN_vnT'))
{
  die('Access denied');
}

define("DIR_MOD", ROOT_URI . "modules/search");
define("MOD_DIR_IMAGE", ROOT_URI . "modules/search/images");
define("LINK_MOD", $vnT->link_root . $vnT->setting['seo_name'][$vnT->lang_name]['search']);
define('DIR_UPLOAD_PRO', ROOT_URI . 'vnt_upload/product');
define("LINK_PRO", ROOT_URI."product");
 
 

function cmp($a, $b)
{
	if ($a["date_post"] == $b["date_post"]) {
		return 0;
	}
	return ($a["date_post"] > $b["date_post"]) ? -1 : 1; 
}
 
/**
 * function box_sidebar ()
 * 
 **/
function box_sidebar ()
{
	global $vnT, $input;
	$output = '';
	include ("widgets.php");	
	   

 	return $output;
}

 

?>