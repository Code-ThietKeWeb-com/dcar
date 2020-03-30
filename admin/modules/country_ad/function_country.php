<?php
/*================================================================================*\
|| 							Name code : funtion_product.php	 		 			          	     		  # ||
||  				Copyright © 2007 by Thai Son - CMS vnTRUST                          # ||
\*================================================================================*/
/**
 * @version : 1.0
 * @date upgrade : 11/12/2007 by Thai Son
 **/
if (! defined('IN_vnT')) {
  die('Hacking attempt!');
}

//====== List_Display
  function List_Display ($did = -1, $ext = "")
  {
    global $func, $DB, $conf, $vnT;
    $text = "<select size=1 name=\"display\" id=\"display\" class='select form-control' {$ext} >";
    $text .= "<option value=\"-1\"> Tất cả </option>";
    if ($did == "1")
      $text .= "<option value=\"1\" selected> Hiển thị </option>";
    else
      $text .= "<option value=\"1\" > Hiển thị </option>";
    if ($did == "0")
      $text .= "<option value=\"0\" selected> Ần </option>";
    else
      $text .= "<option value=\"0\">Ần</option>";
    $text .= "</select>";
    return $text;
  }

function get_city_name ($id)
{
  global $vnT, $func, $DB, $conf;
  $text = $id;
  $sql = "select name from iso_cities where id=".$id;
  $result = $DB->query($sql);
  if ($row = $DB->fetch_row($result)) {
    $text = $func->HTML($row['name']);
  }
  return $text;
}

function get_state_name ($id)
{
  global $vnT, $func, $DB, $conf;
  $text = $id;
  $sql = "select name from iso_states where id=".$id;
  $result = $DB->query($sql);
  if ($row = $DB->fetch_row($result)) {
    $text = $func->HTML($row['name']);
  }
  return $text;
}

function get_country_name ($code)
{
  global $vnT, $func, $DB, $conf;
  $text = "";
  $sql = "select name from iso_countries where iso='$code' ";
  $result = $DB->query($sql);
  if ($row = $DB->fetch_row($result)) {
    $text = $func->HTML($row['name']);
  }
  return $text;
}

//-----------------  List_Country
function List_Country ($did = "", $ext = "")
{
  global $vnT, $conf, $DB, $func;
  $text = "<select name=\"country\" id=\"country\" class='select form-control load_city' data-city='city' {$ext}   >";
  $text .= "<option value=\"\" selected>-- Chọn quốc gia --</option>";
  $sql = "SELECT * FROM iso_countries where display=1 order by name ASC ";
  $result = $DB->query($sql);
  while ($row = $DB->fetch_row($result)) {
    if ($row['iso'] == $did) {
      $text .= "<option value=\"{$row['iso']}\" selected>" . $func->HTML($row['name']) . "</option>";
    } else {
      $text .= "<option value=\"{$row['iso']}\">" . $func->HTML($row['name']) . "</option>";
    }
  }
  $text .= "</select>";
  return $text;
}

//-----------------  List_City
function List_City ($did = "" ,$type_show="list" , $default = "" , $ext = "")
{
  global $vnT, $conf, $DB, $func;
  $text ='';
  if($default){
    $text .= "<option value=\"\" selected>-- Chọn tỉnh thành --</option>";
  }

  $sql = "SELECT * FROM iso_cities where display=1  order by c_order ASC , name ASC  ";
  $result = $DB->query($sql);
  while ($row = $DB->fetch_row($result)) {
    $selected = ($did == $row['id']) ? " selected" : '';
    $text .= "<option value=\"{$row['id']}\" ".$selected.">" . $func->HTML($row['name']) . "</option>";
  }

  if($type_show=='option')
  {
    $textout = $text;
  }else{
    $textout = "<select name=\"city\" id=\"city\" class='select form-control load_state' data-state='state'  {$ext}   >";
    $textout .= $text ;
    $textout .= "</select>";
  }

  return $textout;
}

//-----------------  List_State
function List_State ($city, $did = "", $ext = "")
{
  global $vnT, $conf, $DB, $func;
  $text = "<select name=\"state\" id=\"state\" class='select form-control load_ward' data-ward='ward''  {$ext}   >";
  $text .= "<option value=\"\" selected>-- Chọn Quận huyện--</option>";
  $sql = "SELECT * FROM iso_states where display=1 and city='$city'  order by s_order ASC , name ASC  ";
  $result = $DB->query($sql);
  while ($row = $DB->fetch_row($result)) {
    if ($row['id'] == $did) {
      $text .= "<option value=\"{$row['id']}\" selected>" . $func->HTML($row['name']) . "</option>";
    } else {
      $text .= "<option value=\"{$row['id']}\">" . $func->HTML($row['name']) . "</option>";
    }
  }
  $text .= "</select>";
  return $text;
}


function build_seo_product ($seo,$lang="vn"){
	global $func,$DB,$conf,$vnT;
	$arr_category = array();
	$result = $DB->query("SELECT cat_id,friendly_url FROM product_category_desc "); 
	while($row = $DB->fetch_row($result))
	{
		$arr_category[$row['cat_id']]  =  $row['friendly_url'];	
	} 
	
	if($seo['table']=="city")
	{
		$arr_state = array();
		$res_state = $vnT->DB->query("SELECT id,city,name FROM iso_states WHERE  city=".$seo['id']);	
		while($row_state = $vnT->DB->fetch_row($res_state))
		{
			$arr_state[$row_state['id']] = $row_state['name'] ;
		} 
	
		foreach ($arr_category as $cat_id => $friendly_url)	
		{
			$name_seo = $friendly_url."-". $vnT->func->make_url($seo['name']);
			$res_ck = $vnT->DB->query("SELECT name FROM seo_url WHERE name='".$name_seo."' ") ;
			if(!$vnT->DB->num_rows($res_ck))
			{
				$cot = array();
				$query_string = "mod:product|act:product|catID:".$cat_id."|city:".$seo['id'];
				$cot['modules'] = "product";
				$cot['action'] = "product";
				$cot['name_id'] = "itemID";
				$cot['item_id'] = $cat_id;
				$cot['name'] = $name_seo;
				$cot['query_string'] =  $query_string;
				$cot['date_post'] = time();			 
				$cot['lang'] = $lang;
				$vnT->DB->do_insert("seo_url", $cot);			 
			}	
			
			foreach ($arr_state as $k => $val)		
			{
				$name_seo1 = $friendly_url."-". $vnT->func->make_url($seo['name'])."-".$vnT->func->make_url($val);
				$res_ck1 = $vnT->DB->query("SELECT name FROM seo_url WHERE name='".$name_seo1."' ") ;
				if(!$vnT->DB->num_rows($res_ck1))
				{
					$cot1 = array();
					$query_string1 = "mod:product|act:product|catID:".$cat_id."|city:".$seo['id']."|state:".$k;
					$cot1['modules'] = "product";
					$cot1['action'] = "product";
					$cot1['name_id'] = "itemID";
					$cot1['item_id'] = $cat_id;
					$cot1['name'] = $name_seo1;
					$cot1['query_string'] =  $query_string1;
					$cot1['date_post'] = time();			 
					$cot1['lang'] = $lang;
					$vnT->DB->do_insert("seo_url", $cot1);			 
				}					
			}
		}
	}
	
	if($seo['table']=="state")
	{
		foreach ($arr_category as $cat_id => $friendly_url)	
		{
			$name_seo = $friendly_url."-". $vnT->func->make_url($seo['city_name'])."-".$vnT->func->make_url($seo['name']);
			$res_ck = $vnT->DB->query("SELECT name FROM seo_url WHERE name='".$name_seo."' ") ;
			if(!$vnT->DB->num_rows($res_ck))
			{
				$cot = array();
				$query_string = "mod:product|act:product|catID:".$cat_id."|city:".$seo['city']."|state:".$seo['id'];
				$cot['modules'] = "product";
				$cot['action'] = "product";
				$cot['name_id'] = "itemID";
				$cot['item_id'] = $cat_id;
				$cot['name'] = $name_seo;
				$cot['query_string'] =  $query_string;
				$cot['date_post'] = time();			 
				$cot['lang'] = $lang;
				$vnT->DB->do_insert("seo_url", $cot);			 
			}							 
		}// foreach 
	}
	
}

?>