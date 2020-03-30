<?php
/*================================================================================*\
|| 							Name code : funtions_config.php 		 			                      # ||
||  				Copyright © 2007 by Thai Son - CMS vnTRUST                          # ||
\*================================================================================*/
/**
 * @version : 1.0
 * @date upgrade : 11/12/2007 by Thai Son
 **/
if (! defined('IN_vnT')) {
  die('Hacking attempt!');
}
define('DIR_MOD', DIR_MODULE . '/social_network_ad');
define('MOD_DIR_UPLOAD', '../vnt_upload/weblink');

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

//---------- List_Type
function List_Type ($did, $ext = "")
{
  global $func, $DB, $conf;
  $arr_type = array(1 => 'Dạng hình icon' ,  2 => 'Dạng Iframe ');
	
	$text = "<select size=1 name=\"type\" id=\"type\"  {$ext} class='select' >";  
	foreach ($arr_type as $key => $value)
	{
		$selected	 = ($key==$did) ? " selected ": "" ;
		$text .= "<option value=\"{$key}\" {$selected} > {$value} </option>";
	} 
	
  $text .= "</select>";
  return $text;
}

 

function List_CheckBox ($name,$array = array() ,$did , $ext = "")
{
  global $func, $DB, $conf;
	
	if ($did)
		$arr_selected = explode(",",$did);
	else{
		$arr_selected = array();
	}
	
	foreach ($array as $key => $value)
	{
		if (in_array($key,$arr_selected)){
			$checked ="checked";	
		}else{
			$checked ="";	
		}	
		
		$text .= '<input name="'.$name.'[]" id="'.$name.'" type="checkbox" value="'.$key.'" '.$checked.'  />&nbsp;'.$value.' &nbsp;'; 		
	}  
	
	return $text;
}
?>