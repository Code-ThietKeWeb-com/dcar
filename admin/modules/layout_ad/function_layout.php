<?php
/*================================================================================*\
|| 							Name code : funtions_layout.php 		 			          	     		  # ||
||  				Copyright © 2007 by Thai Son - CMS vnTRUST                          # ||
\*================================================================================*/
/**
 * @version : 1.0
 * @date upgrade : 11/12/2007 by Thai Son
 **/
if (! defined('IN_vnT')) {
  die('Hacking attempt!');
}

function getToolbar ($lang = "vn")
{
  global $func, $DB, $conf, $vnT;
  $menu = array(
    "add" => array(
      'icon' => "i_add" , 
      'title' => "Add Blocks" , 
      'link' => "?mod=layout&act=layout&sub=add") , 
    "config_block" => array(
      'icon' => "i_manage" , 
      'title' => "Organize Blocks" , 
      'link' => "?mod=layout&act=layout&sub=config_block") , 
    "manage" => array(
      'icon' => "i_manage" , 
      'title' => "Manage Layout" , 
      'link' => "?mod=layout&act=layout") , 
    "list_block" => array(
      'icon' => "i_edit" , 
      'title' => "Edit/Del Blocks" , 
      'link' => "?mod=layout&act=layout&sub=list_block") , 
    "help" => array(
      'icon' => "i_help" , 
      'title' => "Help" , 
      'link' => "'help/index.php?mod=layout&act=layout','AdminCPHelp',1000, 600, 'yes','center'" , 
      'newwin' => 1));
  return $func->getMenu($menu);
}

function List_Check ($name, $did)
{
  global $func, $DB, $conf;
  if ($did == 1)
    $check_on = "checked";
  else
    $check_off = "checked";
  $text = "<table width=\"100\" border=\"0\" cellspacing=\"1\" cellpadding=\"1\">
            <tr>
                <td style=\"padding-left:3px\"><input name=\"{$name}\" type=\"radio\" value=\"1\" {$check_on} />&nbsp;On </td>
                <td style=\"padding-left:3px\"><input name=\"{$name}\" type=\"radio\" value=\"0\" {$check_off} />&nbsp;Off</td>
             </tr>
          </table>";
  return $text;
}

function List_Type ($did)
{
  global $func, $DB, $conf;
  $text = "<select size=1 name=\"type\" id=\"type\"  onChange=\"show_des(this);\" >";
  if ($did == "0")
    $text .= "<option value=\"0\" selected> Main Block </option>";
  else
    $text .= "<option value=\"0\" > Main Block </option>";
  if ($did == "1")
    $text .= "<option value=\"1\" selected> Cutom Blocks </option>";
  else
    $text .= "<option value=\"1\"> Cutom Blocks </option>";
  $text .= "</select>";
  return $text;
}

//--------------------
function List_Module_show ($did = "")
{
  global $func, $DB, $conf;
  $all = "";
  if ($did)
    $arr_selected = explode(",", $did);
  else {
    $arr_selected = array();
    $all = "selected";
  }
  $text = "<select name=\"module_show[]\" id=\"module_show\" size=\"10\" multiple style='width:50%'>";
  $text .= "<option value='' {$all} >-- Tất cả --</option>";
  if (in_array("main", $arr_selected)) {
    $text .= "<option value='main' selected  > Trang chủ </option>";
  } else {
    $text .= "<option value='main'  > Trang chủ </option>";
  }
  if (in_array("about", $arr_selected)) {
    $text .= "<option value='about' selected  > Giới thiệu </option>";
  } else {
    $text .= "<option value='about'  > Giới thiệu </option>";
  }
  if (in_array("contact", $arr_selected)) {
    $text .= "<option value='contact' selected  > Liên hệ </option>";
  } else {
    $text .= "<option value='contact'  > Liên hệ </option>";
  }
  $sql = "select * from modules order by id DESC";
  $result = $DB->query($sql);
  while ($row = $DB->fetch_row($result)) {
    $text .= "<option value=\"" . $row['mod_name'] . "\"";
    if (in_array($row['mod_name'], $arr_selected)) {
      $text .= " selected";
    }
    $text .= ">" . $row['name'] . "</option>\n";
  }
  $text .= "</select>";
  return $text;
}

//==========
function List_Module ($did = "", $ext = "")
{
  global $func, $DB, $conf;
  $text = "<select name=\"module\" class='select' {$ext} >";
  $text .= "<option value='' selected  > -- Tất cả module -- </option>";
  if ($did == "main") {
    $text .= "<option value='main' selected  > Trang chủ </option>";
  } else {
    $text .= "<option value='main'  > Trang chủ </option>";
  }
  if ($did == "about") {
    $text .= "<option value='about' selected  > Giới thiệu </option>";
  } else {
    $text .= "<option value='about'  > Giới thiệu </option>";
  }
  if ($did == "contact") {
    $text .= "<option value='contact' selected  > Liên hệ </option>";
  } else {
    $text .= "<option value='contact'  > Liên hệ </option>";
  }
  $sql = "select * from modules order by id DESC";
  $result = $DB->query($sql);
  while ($row = $DB->fetch_row($result)) {
    $text .= "<option value=\"" . $row['mod_name'] . "\"";
    if ($did == $row['mod_name']) {
      $text .= " selected";
    }
    $text .= ">" . $row['name'] . "</option>\n";
  }
  $text .= "</select>";
  return $text;
}

//====================
function List_Align ($did, $ext = "")
{
  global $func, $DB, $conf;
  $text = "<select size=1 name=\"align\" {$ext} >";
  if ($did == "left")
    $text .= "<option value=\"left\" selected> Left </option>";
  else
    $text .= "<option value=\"left\" > Left </option>";
  if ($did == "right")
    $text .= "<option value=\"right\" selected> Right </option>";
  else
    $text .= "<option value=\"right\"> Right </option>";
  $text .= "</select>";
  return $text;
}
?>