<?php
define('IN_vnT', 1);
define('PATH_ADMIN', dirname(__FILE__));
define('DS', DIRECTORY_SEPARATOR);
require_once ("../_config.php");
require_once ("../includes/admin.inc.php");

$vnT->conf['indextitle'] = '[Admin]';
$logo = $vnT->conf['rooturl']."skins/default/images/logo.png" ;
$res_l = $vnT->DB->query("select * from advertise where pos='logo' and display=1 AND lang='en' order by l_order LIMIT 0,1");
if ($row_l = $vnT->DB->fetch_row($res_l)) {
  $logo = $vnT->conf['rooturl'] . "vnt_upload/weblink/" . $row_l['img'];
}
$vnT->conf['logo'] = $logo ;
$vnT->conf['favicon'] = ($vnT->conf['favicon']) ? $vnT->conf['favicon'] : $vnT->conf['rooturl'] . 'favicon.ico';



$Template = new XiTemplate(DIR_SKIN . DS . 'global.tpl');
$Template->assign("DIR_SKIN", "skins/".$vnT->conf['skin_acp']);
$Template->assign("DIR_IMAGE", $vnT->dir_images);
$Template->assign("DIR_STYLE", $vnT->dir_style);
$Template->assign("DIR_JS", $vnT->dir_js);
$Template->assign("LANG", $vnT->lang);

$Template->assign("FOLDER_ADMIN", FOLDER_ADMIN);
$Template->assign("admininfo", $vnT->admininfo);

$Template->assign("marquee_hotline",$vnT->lang['marquee_help']);

$vnT->output = "";

$_GET['mod'] = str_replace("'", "&#039;", $_GET['mod']);
$_GET['block'] = str_replace("'", "&#039;", $_GET['block']);
if (! empty($_GET['block']))
{
  $folder = "blocks";
  $option = trim($_GET['block']);
} elseif (! empty($_GET['mod']))
{
  $folder = "modules";
  $option = trim($_GET['mod']);
} else
{
  $folder = "modules";
  $option = "main";
}
if(empty($_SESSION['admin_session']))
  $_SESSION['admin_session'] = $vnT->admininfo['adminid']  ;

$_GET['act'] = str_replace("\\'", "&#039;", $_GET['act']);
$act = (empty($_GET['act'])) ? "main" : $_GET['act'];
$sub = (empty($_GET['sub'])) ? "manage" : $_GET['sub'];

// header

//left
$box_left = $vnT->menu->box_left();
$ck_permission = 1;

if (! empty($vnT->admininfo['permission']))
{

  if (strstr($vnT->myPre_sub[$act], $sub) || in_array($act, $vnT->act_allow))
  {

  } else {
    $ck_permission = 0;
  }
}

if($ck_permission)
{

  // main
  switch ($act)
  {
    case "login":  include "login.php"; break;
    case "lostpass": include "lostpass.php";   break;
    case "logout":  include "logout.php"; 	break;
    default:
      $file_action = $folder . "/" . $option . "_ad/" . $act . ".php";
      if (file_exists($file_action)) include ($file_action);
      else
        include ("modules/main_ad/main.php");
      break;
  }

  $data = array();
  $data['ver'] = "6.0";
  $data['box_lang'] = $func->box_lang();
  $data['lang'] = ($vnT->input['lang']) ?  $vnT->input['lang'] : $func->get_lang_default();
  $data['langcp'] = ($vnT->input['langcp']) ?  $vnT->input['langcp'] : $vnT->conf['langcp'];
  $Template->assign("data", $data );
  $Template->assign("CONF", $vnT->conf);
  $Template->assign("EXT_HEAD", $vnT->html->fetchHead());
  $Template->assign("BOX_LEFT", $box_left);
  $Template->assign("PAGE_CONTENT", $vnT->output);

  $Template->parse("body");
  $Template->out("body");

}else{
  flush();
  $mess = $vnT->lang['not_permission'];
  $url = "?mod=main";
  echo $func->html_redirect($url, $mess);
  exit();
}


$DB->close();
?>