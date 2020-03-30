<?php
/*================================================================================*\
|| 							Name code : funtions_mail_temp.php 		 			               		  # ||
||  				Copyright © 2007 by Thai Son - CMS vnTRUST                          # ||
\*================================================================================*/
/**
 * @version : 1.0
 * @date upgrade : 11/12/2007 by Thai Son
 **/
if (! defined('IN_vnT'))
{
  die('Hacking attempt!');
}
define('MOD_DIR_UPLOAD', '../vnt_upload/File/Image');

function getToolbar ($act = "menu", $pos = "", $lang = "vn")
{
  global $func, $DB, $conf, $vnT;
  $menu = array(
    "add" => array(
    'icon' => "i_add" , 'title' => "Add" , 'link' => "?mod=menu&act=$act&sub=add&lang=" . $lang . "&pos=" . $pos
  ) , "edit" => array(
    'icon' => "i_edit" , 'title' => "Edit" , 'link' => "javascript:alert('" . $vnT->lang['action_no_active'] . "')"
  ) , "manage" => array(
    'icon' => "i_manage" , 'title' => "Manage" , 'link' => "?mod=menu&act=$act&lang=" . $lang . "&pos=" . $pos
  ) , "help" => array(
    'icon' => "i_help" , 'title' => "Help" , 'link' => "'help/index.php?mod=menu&act=$act','AdminCPHelp',1000, 600, 'yes','center'" , 'newwin' => 1
  )
  );
  return $func->getMenu($menu);
}

// Ham List_Cat
function List_Cat ($pos, $did = -1, $lang = "vn", $ext = "")
{
  global $func, $DB, $conf;
  $where = " AND pos='$pos' AND lang='$lang' "  ;
  $text = "<select size=1 id=\"parentid\" name=\"parentid\" class='select' {$ext} >";
  $text .= "<option value=\"0\">-- ROOT --</option>";
  $query = $DB->query("SELECT  n.menu_id,nd.title FROM menu n ,menu_desc nd WHERE n.menu_id=nd.menu_id AND parentid=0 $where ORDER BY pos ASC, menu_order ASC , n.menu_id ASC");
  while ($row = $DB->fetch_row($query))
  {
    $title = $func->HTML($row['title']);
    if ($row['menu_id'] == $did) $text .= "<option value=\"{$row['menu_id']}\" selected>{$title}</option>";
    else
      $text .= "<option value=\"{$row['menu_id']}\" >{$title}</option>";
    $n = 1;
    $text .= Get_Sub($row['menu_id'], $n, $did, $lang);
  }
  $text .= "</select>";
  return $text;
}

// Ham Get_Sub
function Get_Sub ($cid, $n, $did = -1, $lang)
{
  global $func, $DB, $conf;
  $output = "";
  $k = $n;
  $query = $DB->query("SELECT n.menu_id,nd.title FROM menu n ,menu_desc nd WHERE n.menu_id=nd.menu_id AND parentid={$cid} AND lang='$lang' ORDER BY pos ASC, menu_order ASC , n.menu_id ASC");
  while ($row = $DB->fetch_row($query))
  {
    $title = $func->HTML($row['title']);
    if ($row['menu_id'] == $did)
    {
      $output .= "<option value=\"{$row['menu_id']}\" selected>";
      for ($i = 0; $i < $k; $i ++)
        $output .= "|--";
      $output .= "{$title}</option>";
    } else
    {
      $output .= "<option value=\"{$row['menu_id']}\" >";
      for ($i = 0; $i < $k; $i ++)
        $output .= "|--";
      $output .= "{$title}</option>";
    }
    $n = $k + 1;
    $output .= Get_Sub($row['menu_id'], $n, $did, $lang);
  }
  return $output;
}

// Ham del submenu
function del_submenu ($cid)
{
  global $func, $DB, $conf;
  $query = $DB->query("SELECT * FROM menu WHERE parentid={$cid} order by menu_order");
  while ($row = $DB->fetch_row($query))
  {
    del_submenu($row['menu_id']);
  }
  $DB->query("DELETE FROM menu WHERE menu_id=" . $cid);
	$DB->query("DELETE FROM menu_desc WHERE menu_id=" . $cid);
}

function List_Target ($did = '_selt',$ext="")
{
  global $func, $DB, $conf;
	$arr_item = array("_self"=>"Tại trang (_self)","_blank"=>"Cửa sổ mới (_blank)","_parent"=>"Cửa sổ cha (_parent)","_top"=>"Cửa sổ trên cùng (_top)") ;
	
  $text = "<select size=1 name=\"target\" id='target' class='select'  {$ext} >";
	foreach ($arr_item as $key => $value)
	{
		$selected = ($key==$did) ? "selected" : "";
		$text .= "<option value=\"{$key}\" {$selected} > {$value} </option>";
	}
  
  $text .= "</select>";
  return $text;
}

function List_Pos ($did = 0, $ext = "")
{
  global $func, $DB, $conf, $vnT;
  $arr_pos = array(
    'horizontal' => $vnT->lang['horizontal_menu'] , 		 
		'vertical' => $vnT->lang['vertical_menu'] , 		 
		'footer' => $vnT->lang['footer_menu'] 
  );
  
  $text = "<select size=1 name=\"pos\" id=\"pos\" class='select'  {$ext} >";
  $text .= "<option value=\"\" selected> " . $vnT->lang['select_position'] . " </option>";
  foreach ($arr_pos as $key => $value)
  {
    $selected = ($key == $did) ? "selected" : "";
    $text .= "<option value=\"{$key}\" {$selected} > " . $value . " </option>";
  }
  $text .= "</select>";
  return $text;
}
?>