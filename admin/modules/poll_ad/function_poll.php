<?php
/*================================================================================*\
|| 							Name code : funtions_poll.php 		 			               		  # ||
||  				Copyright © 2007 by Thai Son - CMS vnTRUST                          # ||
\*================================================================================*/
/**
 * @version : 1.0
 * @date upgrade : 11/12/2007 by Thai Son
 **/
if (! defined('IN_vnT')) {
  die('Hacking attempt!');
}
define('MOD_DIR_UPLOAD', '../vnt_upload/poll/');
define('MOD_ROOT_URL', $conf['rooturl'] . 'modules/poll/');

function getToolbar ($lang = "vn")
{
  global $func, $DB, $conf, $vnT;
  $menu = array(
    "add" => array(
      'icon' => "i_add" , 
      'title' => "Add" , 
      'link' => "?mod=poll&act=poll&sub=add&lang=$lang") , 
    "edit" => array(
      'icon' => "i_edit" , 
      'title' => "Edit" , 
      'link' => "javascript:alert('" . $vnT->lang['action_no_active'] . "')") , 
    "manage" => array(
      'icon' => "i_manage" , 
      'title' => "Manage" , 
      'link' => "?mod=poll&act=poll&lang=$lang") , 
    "help" => array(
      'icon' => "i_help" , 
      'title' => "Help" , 
      'link' => "'help/index.php?id=20','AdminCPHelp',1000, 600, 'yes','center'" , 
      'newwin' => 1));
  return $func->getMenu($menu);
}

//=================Functions===============
function List_Cat ($did = -1, $lang, $ext = "")
{
  global $func, $DB, $conf, $vnT;
  $text = "<select size=1 name=\"poll_id\" id=\"poll_id\"  onchange=\"submit()\" class='select' >";
  $text .= "<option value=\"\" > " . $vnT->lang['pls_select_poll_title'] . " </option>";
  $sql = "SELECT * FROM poller  order by id ";
  $result = $DB->query($sql);
  while ($row = $DB->fetch_row($result)) {
    $pollerTitle = $func->fetch_content($row['pollerTitle'], $lang);
    if ($did == $row["id"])
      $text .= '<option value="' . $row["id"] . '" selected>' . $pollerTitle . "</option>";
    else
      $text .= '<option value="' . $row["id"] . '">' . $pollerTitle . "</option>";
  }
  $text .= "</select>";
  return $text;
}
?>