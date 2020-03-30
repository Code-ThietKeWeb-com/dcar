<?php
/*================================================================================*\
|| 							Name code : funtions_support.php 		 			          	     		  # ||
||  				Copyright © 2007 by Thai Son - CMS vnTRUST                          # ||
\*================================================================================*/
/**
 * @version : 1.0
 * @date upgrade : 11/12/2007 by Thai Son
 **/
if (! defined('IN_vnT')) {
  die('Hacking attempt!');
}

function getToolbar ($act = "plugins", $lang = "vn")
{
  global $func, $DB, $conf, $vnT;
  $menu = array(
    "edit" => array(
      'icon' => "i_edit" , 
      'title' => "Edit" , 
      'link' => "javascript:alert('" . $vnT->lang['action_no_active'] . "')") , 
    "manage" => array(
      'icon' => "i_manage" , 
      'title' => "Manage" , 
      'link' => "?mod=plugins&act=$act") , 
    "help" => array(
      'icon' => "i_help" , 
      'title' => "Help" , 
      'link' => "'help/index.php?mod=plugins&act=$act','AdminCPHelp',1000, 600, 'yes','center'" , 
      'newwin' => 1));
  return $func->getMenu($menu);
}
?>