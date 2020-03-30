<?php
// no direct access
if (! defined('IN_vnT')) {
  die('Hacking attempt!');
}
if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') {
  $vnT->seo_url = "https://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
}else{
  $vnT->seo_url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
}

$router_name = $text_url = str_replace($conf['rooturl'],"",$vnT->seo_url);
$vnT->url_array = explode('/', $text_url);
$vnT->link_root = ROOT_URI; 

$arr_cur_mod = @explode("?",str_replace(".html","",$vnT->url_array[0]));
$cur_mod =  trim($arr_cur_mod[0]);

$mod_site = array();
$mod_site[] = array( "mod_name" => "intro" ,  "seo_name_vn" => "intro" ,  "seo_name" => "intro" );
$mod_site[] = array( "mod_name" => "main" ,  "seo_name_vn" => "trang-chu" ,  "seo_name" => "main" );
$mod_site[] = array( "mod_name" => "about" ,  "seo_name_vn" => "gioi-thieu" ,  "seo_name" => "about" );
$mod_site[] = array( "mod_name" => "contact" ,  "seo_name_vn" => "lien-he" ,  "seo_name" => "contact" );
$mod_site[] = array( "mod_name" => "page" ,  "seo_name_vn" => "trang" ,  "seo_name" => "page" );
$mod_site[] = array( "mod_name" => "search" ,  "seo_name_vn" => "tim-kiem" ,  "seo_name" => "search" ); 

$res_mod = $vnT->DB->query("SELECT * FROM modules ");
while ($row_mod = $vnT->DB->fetch_row($res_mod)) {
	 $mod_site[] = $row_mod;
} 

$res_lang = $vnT->DB->query("SELECT * FROM  language ") ;
if ($num_lang = $vnT->DB->num_rows($res_lang))
{	
	while ($row_lang = $vnT->DB->fetch_row($res_lang)) 	{	
		if($row_lang['is_default']==1){
      $vnT->lang_default = $row_lang['name'];
		}	
		$vnT->arr_lang_name[] = $row_lang['name'];  
		
		foreach ($mod_site as $mod_value) {
			$seo_name = ($mod_value["seo_name_".$row_lang['name']]) ? $mod_value["seo_name_".$row_lang['name']] : $mod_value["seo_name"] ;
			$vnT->setting['seo_name'][$row_lang['name']][$mod_value['mod_name']] = $seo_name ;
		}
		
	}  

	if($num_lang>1)
	{
		$vnT->muti_lang=1;		
		if(!in_array($vnT->url_array[0],$vnT->arr_lang_name))
		{			
			$vnT->lang_name = $vnT->lang_default ;
			if($text_url)
			{
				$link_ref = $conf['rooturl'].$vnT->lang_default."/".$text_url;
				if($text_url){
					$arr_url = @explode("/",$text_url);
					if($arr_url[0]!=$vnT->lang_name){ 
						$vnT->func->header_redirect($link_ref) ;
					}
				}else{
					$vnT->func->header_redirect($link_ref) ;
				}			
			}
		}else{
			$vnT->lang_name = $vnT->url_array[0];
		}		
		$vnT->link_root = ROOT_URI . $vnT->lang_name."/" ;
		
		
		$arr_cur_mod = @explode("?",str_replace(".html","",$vnT->url_array[1]));
		$cur_mod =  trim($arr_cur_mod[0]);

		$router_name = str_replace($conf['rooturl'].$vnT->lang_name."/","",$vnT->seo_url);
	}else{	
		$vnT->lang_name = $vnT->lang_default ;
	}	
}

$_GET["lang"] = $vnT->lang_name;

//load file 
foreach ($mod_site as $mod_value) {
	$file_seo = PATH_ROOT . "/modules/" . $mod_value['mod_name'] . "/seo_url.php";
	$seo_name = ($mod_value["seo_name_".$vnT->lang_name]) ? $mod_value["seo_name_".$vnT->lang_name] : $mod_value["seo_name"] ;
	if ($seo_name==$cur_mod ) 
	{
    if (file_exists($file_seo))   require_once ($file_seo);
  }	 
} 


  
//main
if ($cur_mod == $vnT->setting['seo_name'][$vnT->lang_name]['main']) {
  $_GET[$vnT->conf['cmd']] = "mod:main";
  $QUERY_STRING = $vnT->conf['cmd'] . "=mod:main";
}
//about
if ($cur_mod == $vnT->setting['seo_name'][$vnT->lang_name]['about'] ) {
  $_GET[$vnT->conf['cmd']] = "mod:about";
  $QUERY_STRING = $vnT->conf['cmd'] . "=mod:about";
 
  if (in_array($vnT->setting['seo_name'][$vnT->lang_name]['about'], $vnT->url_array)) { 
		$pos = array_search($vnT->setting['seo_name'][$vnT->lang_name]['about'], $vnT->url_array);
		$act = $vnT->url_array[$pos + 1];
		$name = substr($vnT->url_array[$pos + 1], 0, strpos($vnT->url_array[$pos + 1], ".html"));
		//detail
		$_GET[$vnT->conf['cmd']] = "mod:about|name:$name";
		$QUERY_STRING = $vnT->conf['cmd'] . "=mod:about|name:$name"; 
		
		//print
		if ($act == "print") {
			$aID = $vnT->url_array[$pos + 2];
			$_GET[$vnT->conf['cmd']] = "mod:about|act:print|aID:$aID";
			$QUERY_STRING = $vnT->conf['cmd'] . "=mod:about|act:print|aID:$aID";
		}
	}	
}

//contact
if ($cur_mod== $vnT->setting['seo_name'][$vnT->lang_name]['contact']) {
  $_GET[$vnT->conf['cmd']] = "mod:contact";
  $QUERY_STRING = $vnT->conf['cmd'] . "=mod:contact";
}

//sitemap
if ($cur_mod=="sitemap") {
  $_GET[$vnT->conf['cmd']] = "mod:page|act:sitemap";
  $QUERY_STRING = $vnT->conf['cmd'] . "=mod:page|act:sitemap";
}

//page
if ($cur_mod== $vnT->setting['seo_name'][$vnT->lang_name]['page'] ) {
  $pos = array_search($vnT->setting['seo_name'][$vnT->lang_name]['page'], $vnT->url_array);
  $pname = $vnT->url_array[$pos + 1];
  $pname = substr($vnT->url_array[$pos + 1], 0, strpos($vnT->url_array[$pos + 1], ".html"));
  $_GET[$vnT->conf['cmd']] = "mod:page|name:$pname";
  $QUERY_STRING = $vnT->conf['cmd'] . "=mod:page|name:$pname";
}
//search
if($cur_mod== $vnT->setting['seo_name'][$vnT->lang_name]['search'] )	
{ 
	$_GET[$vnT->conf['cmd']] = "mod:search" ;
	$QUERY_STRING = $vnT->conf['cmd']."=mod:search" ;
}	 


// SEO rut gon
$router_name = str_replace(".html","",$router_name);
if(strstr($router_name,"/p-")){
	$tmp_url = @explode("/p-",$router_name);
}else{
	$tmp_url = @explode("/?",$router_name);	
}	
$arr_name_url = @explode("?",$tmp_url[0]);
$name_url =  trim($arr_name_url[0]);
if($name_url){
	$res_seo = $vnT->DB->query("SELECT * FROM seo_url WHERE lang='$vnT->lang_name' AND name='".$name_url."'") ;
	if($row_seo = $vnT->DB->fetch_row($res_seo))
	{	
		$ext_link = '';  	
		$url_last = $vnT->url_array[count($vnT->url_array)-1] ;
		 
		if( !strstr($url_last,".html")){
			$cmd_arr = @explode(",",$url_last)	;
			foreach ($cmd_arr as $value)
			{
				if (! empty($value))
				{
					$k = trim(substr($value, 0, strpos($value, "-")));
					$v = trim(substr($value, strpos($value, "-") + 1));
					$ext_link .= "|".$k.":".$v;
				}
			}
		} 
		
		if($row_seo['query_string'])
		{
			$_GET[$vnT->conf['cmd']] = $row_seo['query_string'].$ext_link;
			$QUERY_STRING = $vnT->conf['cmd'] ."=". $row_seo['query_string'].$ext_link;	
		}else{
			$_GET[$vnT->conf['cmd']] = "mod:".$row_seo['modules']."|act:".$row_seo['action']."|".$row_seo['name_id'].":".$row_seo['item_id'].$ext_link;
			$QUERY_STRING = $vnT->conf['cmd'] . "=mod:".$row_seo['modules']."|act:".$row_seo['action']."|".$row_seo['name_id'].":".$row_seo['item_id'].$ext_link;	
		} 
		
	}
}

//lang 
$QUERY_STRING = ($QUERY_STRING) ? $QUERY_STRING . "&amp;lang=" . $vnT->lang_name :  $QUERY_STRING . "lang=" . $vnT->lang_name;
 
$_SERVER['QUERY_STRING'] = $QUERY_STRING;
$REQUEST_URI = 'index.php?' . $QUERY_STRING;
$_SERVER['REQUEST_URI'] = $REQUEST_URI;
?>