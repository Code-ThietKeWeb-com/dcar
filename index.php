<?php
define('IN_vnT', 1);
define('PATH_ROOT', dirname(__FILE__));
define('DS', DIRECTORY_SEPARATOR);
$vntdebug_start=microtime();
require_once ("_config.php"); 
if (file_exists("firewall.php")){
	include_once "firewall.php";
}

include ("includes/init.inc.php");
if ($vnT->conf['web_close']){
  flush();
  include ("webclose.php");
  exit();
} 

//load tpl box
$vnT->skin_box = new XiTemplate(DIR_SKIN . DS . $vnT->conf['skin'] . DS . 'box.tpl');
$vnT->skin_box->assign("DIR_IMAGE", $vnT->dir_images);
$vnT->skin_box->assign("DIR_STYLE", $vnT->dir_style);
$vnT->skin_box->assign("CONF", $vnT->conf);
$vnT->skin_box->assign("LANG", $vnT->lang);

 //Cache -------------------------------------------
$vnT->Cache->mod_no_cache =  array( "contact" ,"search","advertise","member");
$vnT->Cache->act_no_cache =  array( "product|detail" ,"product|cart" ,"product|checkout_confirmation" ,"product|checkout_finished" );
$vnT->Cache->box_no_cache = array( "box_statistics" );
$vnT->Cache->begin_cache();
//-------------------------------------------------		
 
//Template
$Template = new XiTemplate(DIR_SKIN . DS. $vnT->conf['skin'] .DS. 'global.tpl');
//$Template = new XiTemplate($vnT->content_skin);
$vnT->skin =&  $Template ;
$Template->assign("DIR_SKIN", $vnT->dir_skin);
$Template->assign("DIR_IMAGE", $vnT->dir_images);
$Template->assign("DIR_STYLE", $vnT->dir_style);
$Template->assign("DIR_JS", $vnT->dir_js);
$Template->assign("LANG", $vnT->lang);
$Template->assign("INPUT", $input);

 	
// main	 
	$input['act'] = ($input['act']) ? $input['act'] : $input['mod'];      
	$fileactname = "modules/{$input['mod']}/{$input['act']}.php";
	//echo $fileactname ;
	if (file_exists($fileactname)) include $fileactname;
	else {
		include ("404.php");
	}
// end main

$data['lang'] = $vnT->lang_name;
$data['meta_lang'] = ($vnT->lang_name=="en") ? "en" : "vi";
$data['mem_id'] = (int)$vnT->user['mem_id'];
$data['link_mod'] =  $vnT->link_root . $vnT->setting['seo_name'][$vnT->lang_name][$input['mod']];
$data['logo'] = $vnT->lib->get_logos();
$data['menu'] = $vnT->lib->getMenus();

//more data
$rs_out = $vnT->lib->loadDataGlobal();
if (is_array($rs_out)){
  foreach ($rs_out as $key => $val)	{
    $data[$key] = $val;
  }
}


$vnT->conf['favicon'] = ($vnT->conf['favicon']) ? $vnT->conf['favicon'] : ROOT_URL.'favicon.ico';
$vnT->conf['meta_extra'] .= $vnT->setting['meta_social_network'];
$EXT_HEAD =  $vnT->html->fetchHead();
$EXT_FOOTER = $vnT->html->fetchFooter() ;
if($vnT->conf['extra_header']) {
  $EXT_HEAD .= $vnT->conf['extra_header'];
}
if($vnT->conf['extra_footer']) {
  $EXT_FOOTER .= $vnT->conf['extra_footer'];
}

$Template->assign("CONF", $vnT->conf);
$Template->assign("EXT_HEAD", $EXT_HEAD);
$Template->assign("EXT_FOOTER", $EXT_FOOTER);
$Template->assign("data", $data);
$Template->assign("PAGE_CONTENT", $vnT->output);
$Template->parse("body");

if ($vnT->Cache->turn_on)
{
  //Set cache php
  $vnT->Cache->end_cache();
}
else
{
  $Template->out("body");
}

$vntdebug_end=microtime();

/* // text Debug
if ($vnT->conf['debug']==1) {
		echo "qt = ".$_SERVER['QUERY_STRING'];
		$time_start = $DB->micro_time($vntdebug_start);
		$time_stop = $DB->micro_time($vntdebug_end);
		echo $DB->debug_log();
		echo "<br>";
		echo "Exec time: ".bcsub($time_stop, $time_start, 6)." s";
	}
*/
$vnT->DB->close();
?>
