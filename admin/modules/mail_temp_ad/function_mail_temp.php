<?php
/*================================================================================*\
|| 							Name code : funtions_mail_temp.php 		 			               		  # ||
||  				Copyright © 2007 by Thai Son - CMS vnTRUST                          # ||
\*================================================================================*/
/**
 * @version : 1.0
 * @date upgrade : 11/12/2007 by Thai Son
 **/
if (! defined('IN_vnT')) {
  die('Hacking attempt!');
}

function getToolbar ($act = "mail_temp", $lang = "vn")
{
  global $func, $DB, $conf, $vnT;
  $menu = array(
    "add" => array(
      'icon' => "i_add" , 
      'title' => "Add" , 
      'link' => "?mod=mail_temp&act=$act&sub=add&lang=$lang") , 
    "edit" => array(
      'icon' => "i_edit" , 
      'title' => "Edit" , 
      'link' => "javascript:alert('" . $vnT->lang['action_no_active'] . "')") , 
    "manage" => array(
      'icon' => "i_manage" , 
      'title' => "Manage" , 
      'link' => "?mod=mail_temp&act=$act&lang=$lang") , 
    "help" => array(
      'icon' => "i_help" , 
      'title' => "Help" , 
      'link' => "'help/index.php?mod=mail_temp&act=$act','AdminCPHelp',1000, 600, 'yes','center'" , 
      'newwin' => 1));
  return $func->getMenu($menu);
}
?>