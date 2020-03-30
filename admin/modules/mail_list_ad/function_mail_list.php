<?php
/*================================================================================*\
|| 							Name code : funtions_mail_list.php 		 			          	     		  # ||
||  				Copyright © 2007 by Thai Son - CMS vnTRUST                          # ||
\*================================================================================*/
/**
 * @version : 1.0
 * @date upgrade : 11/12/2007 by Thai Son
 **/
if (! defined('IN_vnT')) {
  die('Hacking attempt!');
}
 
/*** Ham Get_Cat ****/
function Get_Cat ($did = -1, $ext = "")
{
  global $func, $DB, $conf, $vnT;
  $text = "<select size=1 id=\"cat_id\" name=\"cat_id\" {$ext}  class='select'>";
  $text .= "<option value=\"0\">-- " . $vnT->lang['all'] . " --</option>";
  $query = $DB->query("SELECT * FROM maillist_category order by cat_order");
  while ($cat = $DB->fetch_row($query)) {
    $cat_name = $func->HTML($cat['cat_name']);
    if ($cat['cat_id'] == $did)
      $text .= "<option value=\"{$cat['cat_id']}\" selected>{$cat_name}</option>";
    else
      $text .= "<option value=\"{$cat['cat_id']}\" >{$cat_name}</option>";
  }
  $text .= "</select>";
  return $text;
}

function get_cat_name ($cat_id)
{
  global $func, $DB, $conf;
  $cat_name = "Chưa có nhóm";
  $query = $DB->query("SELECT cat_name FROM maillist_category WHERE cat_id=$cat_id");
  if ($cat = $DB->fetch_row($query)) {
    $cat_name = $func->HTML($cat['cat_name']);
  }
  return $cat_name;
}
 
?>