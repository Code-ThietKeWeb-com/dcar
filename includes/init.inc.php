<?php
/*================================================================================*\
|| 							Name code : init.inc.php 		 		 															  # ||
||  				Copyright © 2008 by Thai Son - CMS vnTRUST                					# ||
\*================================================================================*/
/**
 * @version : 1.0
 * @date upgrade : 11/12/2008 by Thai Son
 **/
error_reporting( E_CORE_ERROR | E_CORE_WARNING | E_COMPILE_ERROR | E_ERROR | E_WARNING | E_PARSE | E_USER_ERROR | E_USER_WARNING | E_RECOVERABLE_ERROR );
@ini_set("display_errors", "1");
@header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");

require_once (PATH_ROOT . DS . 'includes' . DS . 'defines.php');
require_once (PATH_INCLUDE . DS . 'AntiSQLInjection.php');
require_once (PATH_INCLUDE . DS . 'class_db.php');
require_once (PATH_INCLUDE . DS . 'class_functions.php');
require_once (PATH_INCLUDE . DS . 'defines.php');
require_once (PATH_INCLUDE . DS . 'class.XiTemplate.php'); 
require_once (PATH_INCLUDE . DS . 'class_global.php');
// initialize the data registry
$vnT = new vnT_Registry();
$conf = $vnT->conf ;
$DB = $vnT->DB ;
$func = $vnT->func ;

require_once (PATH_INCLUDE . DS . 'seo_url.php');
//echo "qt = ".$_SERVER['QUERY_STRING'];
// parse the configuration 	
$vnTRUST = (isset($_GET[$vnT->conf['cmd']])) ? $_GET[$vnT->conf['cmd']] : "";
$input = $vnT->func->Get_Input($vnTRUST);
 
//check page 
if($cur_mod=="" || strstr($cur_mod,"?") ){
	$input['mod'] =  (isset($input['mod'])) ? $input['mod'] :  $vnT->conf['module'];
}
 
$vnT->load_language($input['mod']);
$vnT->conf['indextitle'] = $vnT->conf['indextitle_'.$vnT->lang_name.''];
$vnT->conf['meta_description'] = $vnT->conf['meta_description_'.$vnT->lang_name.''];
$vnT->conf['meta_keyword'] = $vnT->conf['meta_keyword_'.$vnT->lang_name.''];

$vnT->stats = $vnT->func->Get_Stats();
$vnT->user = $vnT->func->Get_User_Info($vnT->session->get("s_id"));
//load mailer
$func->include_libraries('phpmailer.phpmailer');
$vnT->mailer = new PHPMailer();
//load HTML
$func->include_libraries('vntrust.html.html');
$vnT->html = new vnT_HTML();

//load social_network
$res_sn = $vnT->DB->query("SELECT * FROM social_network_setting WHERE id=1");
if($row_sn = $vnT->DB->fetch_row($res_sn))
{
	$vnT->setting['social_network_setting'] = $row_sn ;
	$vnT->setting['meta_social_network'] = '';
	if($row_sn['facebook_appId']){
		$vnT->setting['meta_social_network'] .= "\n".'<meta property="fb:app_id" content="'.$row_sn['facebook_appId'].'" />' ;
	}
	if($row_sn['social_network_picture']){
		$social_network_picture = str_replace(ROOT_URI."vnt_upload/File",$conf['rooturl']."vnt_upload/File",$row_sn['social_network_picture']);
		$vnT->setting['meta_social_network'] .= "\n".'<meta property="og:image" content="'.$social_network_picture.'" />';
		$vnT->setting['meta_social_network'] .= "\n".'<link rel="image_src" href="'.$social_network_picture.'" />';
	}
	if($row_sn['google']==1){
		$vnT->conf['extra_footer'] .= "\n".'<script type="text/javascript" src="https://apis.google.com/js/plusone.js"></script>';
	}
	if($row_sn['twitter']==1){
		$vnT->conf['extra_footer'] .= "\n".'<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>';
	} 
}


require_once (PATH_INCLUDE . DS . 'Mobile_Detect.php');
$detect = new Mobile_Detect;
$deviceType = ($detect->isMobile() ? ($detect->isTablet() ? 'tablet' : 'phone') : 'computer');
if($deviceType == "phone"  ){
  $vnT->deviceType = "mobile";
}


//load plugins
$res_plugins = $vnT->DB->query("SELECT * FROM plugins where display=1 order by ordering  ");
while ($row_plugins = $vnT->DB->fetch_row($res_plugins)) {
  $file_plugins = PATH_PLUGINS . DS . $row_plugins['folder'] . DS . $row_plugins['name'] . ".php";
  if (file_exists($file_plugins)) {
    include ($file_plugins);
  }
}

//load banner-logo
$vnT->advertise = array();
$sql_adv = "SELECT * FROM advertise WHERE  display=1  and lang='".$vnT->lang_name."'  AND (FIND_IN_SET('".$input['mod']."',module_show) or (module_show=''))  order by type_ad DESC,l_order ";
$res_adv = $vnT->DB->query($sql_adv);
while ($row_adv = $vnT->DB->fetch_row($res_adv))
{
  $src = ROOT_URL . "vnt_upload/weblink/" . $vnT->func->HTML($row_adv['img']);
  $l_link = (!strstr($row_adv['link'], "http")) ? $vnT->link_root . $row_adv['link'] : $row_adv['link'];
  $target = ($row_adv['target']) ? $row_adv['target'] : "_blank";

  //more
  $show=1;
  $row_adv['id'] = $row_adv['l_id'];
  $row_adv['link'] = $l_link;
  $row_adv['title'] = $vnT->func->HTML($row_adv['title']);
  $row_adv['target'] = $target;
  $row_adv['src'] = $src;

  if(!in_array($row_adv['pos'],array('logo','logo_footer'))) {
    if($row_adv['date_expire']<time() && $row_adv['date_expire']>0)  {
      $show=0;
    }
  }


  if($show){
    $vnT->advertise[$row_adv['pos']][$row_adv['l_id']] = $row_adv;
  }
}
$vnT->DB->free_result($res_adv) ;
// end load banner-logo

?>