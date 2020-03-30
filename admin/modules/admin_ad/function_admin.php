<?php
/*================================================================================*\
|| 							Name code : funtions_admin.php 		 			                   		  # ||
||  				Copyright © 2007 by Thai Son - CMS vnTRUST                          # ||
\*================================================================================*/
/**
 * @version : 1.0
 * @date upgrade : 11/12/2007 by Thai Son
 **/
if (! defined('IN_vnT')) {
  die('Hacking attempt!');
}
 
//-------------------- List Level ---------------------
function List_Level ($did = "", $ext = "")
{
  global $vnT, $func, $DB, $conf;
  $text = "<select name='level' id='level' " . $ext . " >";
  $text .= "<option value='0' selected> " . $vnT->lang['group_admin'] . "  </option>";
  $sql = "SELECT * FROM admin_group order by a_order ";
  $result = $DB->query($sql);
  while ($row = $DB->fetch_row($result)) {
    $title = $func->HTML($row['title']);
    if ($row['gid'] == $did) {
      $text .= "<option value=\"{$row['gid']}\" selected>" . $title . "</option>";
    } else {
      $text .= "<option value=\"{$row['gid']}\">" . $title . "</option>";
    }
  }
  $text .= "</select>";
  return $text;
}

//-------------------- List_Permission ---------------------
function List_Permission ($did = "")
{
  global $vnT;
  $arr_selected = explode(",", $did);
  $text = "<select name=\"permission[]\" size=\"5\" multiple style='width:250px;'>";
  if ($did == "")
    $text .= '<option value="" selected>-- All --</option>';
  else
    $text .= '<option value="" >-- All --</option>';
  foreach ($vnT->permission as $key => $value) {
    if (in_array($key, $arr_selected))
      $text .= '<option value="' . $key . '" selected>' . $value . '</option>';
    else
      $text .= '<option value="' . $key . '">' . $value . '</option>';
  }
  $text .= "</select>";
  return $text;
}
?>