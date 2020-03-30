<?php 

$admin_user = (isset($vnT->input['txtUsername'])) ? $vnT->input['txtUsername'] : '';
$admin_pass = (isset($vnT->input['txtPassword'])) ? $vnT->input['txtPassword'] : '';
$admin_sec = (isset($vnT->input['txtPassSec'])) ? $vnT->input['txtPassSec'] : '';

$admin_user = str_replace("'", "", $admin_user);
$admin_pass = str_replace("'", "", $admin_pass);
$admin_sec = md5(base64_encode($admin_sec));

$ok_login = 0;
$mess = "";
if ($_POST["btnLogin"])
{


  $ok_submit = 1;
  $ok_captcha  = 1;
  if($vnT->conf['captcha_admin']){
    if($vnT->conf['captcha_type']=="reCAPTCHA"){
      $ok_captcha = $vnT->func->check_security_code("reCAPTCHA");
    }else{
      $ok_captcha = $vnT->func->check_security_code("session_sec_code");
    }

    if(!$ok_captcha) {
      $ok_submit = 0 ;
      $mess .= '<div class="err-item">'.$vnT->lang['err_security_code_invalid'].'</div>' ;
    }
  }else{
    if ($admin_sec != "53f8797e875fb1b667c86e4ae15cee24") {
      $ok_submit = 0 ;
      $mess .= '<div class="err-item">'.$vnT->lang['err_sec_password'] .'</div>' ;
    }
  }


  if(empty($admin_user)) {
    $ok_submit = 0 ;
    $mess .= '<div class="err-item">'.$vnT->lang['err_empty'].' '.$vnT->lang['username'].'</div>' ;
  }
  if(empty($admin_pass)) {
    $ok_submit = 0 ;
    $mess .= '<div class="err-item">'.$vnT->lang['err_empty'].' '.$vnT->lang['password'].'</div>' ;
  }

  //check login_attempt
  if($vnT->conf['login_attempt']){
    //load Blocker
    $func->include_libraries('vntrust.Core.Blocker');
    $blocker = new vnT_Blocker($vnT->conf['rootpath'] . 'vnt_upload/logs/ip_logs');
    if($vnT->conf['login_attempt_num']){
      $rules = array($vnT->conf['login_attempt_num'], $vnT->conf['login_attempt_time'], $vnT->conf['login_attempt_time_ban']) ;
      $blocker->trackLogin($rules);
    }

    if($blocker->is_blocklogin($admin_user)) {
      $ok_submit = 0;
      $mess .= '<div class="err-item">'.str_replace(array("{num}", "{time}"), array($vnT->conf['login_attempt_num'], @date('H:i d/m/Y', $blocker->login_block_end)), $vnT->lang['err_login_blocked']).'</div>';
    }
  }


  if ($ok_submit)
  {

      $admin_pass = $func->md10($admin_pass);
      $query = "select * from admin WHERE username='" . $admin_user . "' AND password='" . $admin_pass . "' ";
      $data_arr = $DB->query($query);
      if ($ok = $DB->fetch_row($data_arr))
      {
				@session_regenerate_id() ;
        $adminid = $ok['adminid'];
        $dataup = array();
        $dataup['lastlogin'] = time();
        $dataup['ip'] = $_SERVER['REMOTE_ADDR'];
        $updb = $DB->do_update("admin", $dataup, "adminid='{$adminid}'");
        
        $ses['s_id'] = md5(uniqid(microtime()));
        $ses['time'] = time();
        $ses['ip'] = $_SERVER['REMOTE_ADDR'];
        $ses['agent'] = $_SERVER['HTTP_USER_AGENT'];
        $checkss = $DB->query("SELECT * FROM adminsessions WHERE adminid='{$adminid}'");
        if ($DB->num_rows($checkss))
        {
          $doit = $DB->do_update("adminsessions", $ses, "adminid='{$adminid}'");
          $ses['adminid'] = $adminid;
        } else
        {
          $ses['adminid'] = $adminid;
          $doit = $DB->do_insert("adminsessions", $ses);
        }
        
        // Insert Admin log				
        $uplog['adminid'] = $adminid;
        $uplog['time'] = $ses['time'];
        $uplog['ip'] = $ses['ip'];
        $uplog['action'] = "Login";
        $uplog['cat'] = "";
        $uplog['pid'] = "";
        
        $doitlog = $DB->do_insert("adminlogs", $uplog);
        // End			        

        $ok_login = 1;
				//$_SESSION['adminid'] = $adminid ;
				$_SESSION['admin_session'] = $adminid ;
				$func->vnt_set_auth_cookie($adminid,$_POST['ck_remember']);

        $_SESSION['langcp'] =  $_POST['langcp'];

        /*$arr_old = $func->fetchDbConfig();
        $duplang['langcp'] = $_POST['langcp'];
        $func->writeDbConfig("config", $duplang, $arr_old);*/
        
        $ref = $func->NDK_decode($_GET['ref']);
        if (empty($ref)){
          $ref = "?mod=main";
        }
        $mess = str_replace('{username}',$admin_user,$vnT->lang['login_sucess']);
				$func->html_redirect($ref,$mess);
       
      } else{
        $mess = $vnT->lang['err_login'];
        if($vnT->conf['login_attempt']) {
          $rs_blocker = $blocker->set_loginFailed($admin_user);
          $num_remain = ($vnT->conf['login_attempt_num'] - $rs_blocker['count']) + 1;
          $mess =  str_replace(array("{user}","{num}"),array($admin_user,$num_remain),$vnT->lang['err_login_attempt']) ;
        }
      }

  }

}

if ($ok_login==0)
{

  $background = $vnT->conf['rooturl'] . "modules/member/images/bg_login.jpg";
  $res_bg = $vnT->DB->query("select * from advertise where pos='bg_login' and display=1  order by l_order LIMIT 0,1");
  if ($row_bg = $vnT->DB->fetch_row($res_bg)) {
    $background = $vnT->conf['rooturl'] . "vnt_upload/weblink/" . $row_bg['img'];
  }
  $data['background'] = $background;
  $vnT->conf['favicon'] = ($vnT->conf['favicon']) ? $vnT->conf['favicon'] : $vnT->conf['rooturl'] . 'favicon.ico';

  $login_tpl = new XiTemplate(DIR_SKIN . DS . "login.tpl");
  $login_tpl->assign('LANG', $vnT->lang);
  $login_tpl->assign("DIR_IMAGE", $vnT->dir_images);
  $login_tpl->assign("DIR_STYLE", $vnT->dir_style);
  $login_tpl->assign("DIR_JS", $vnT->dir_js);
  $login_tpl->assign("CONF", $conf);
  
  $data['ref'] =  htmlspecialchars($ref);

  if($mess){
    $data['zoom'] = '';
    $data['err'] =  $vnT->func->html_err($mess);
  }else{
    $data['zoom'] = " zoomIn";
    $data['err'] = '';
  }

  $data['list_lang'] = vnT_HTML::selectbox("langcp", array(    'vn' => 'Tiếng Việt' , 'en' => 'English'  ), $conf['langcp']);

  if($vnT->conf['captcha_admin'])
  {
    if($vnT->conf['captcha_type']=="reCAPTCHA"){
      $captcha = array();
      $captcha['reCAPTCHA_site_key'] = $vnT->conf['reCAPTCHA_site_key'];
      $login_tpl->assign("captcha", $captcha);
      $login_tpl->parse("login.html_recaptcha");
    }else{
      $captcha = array();
      $captcha['ver_img'] = ROOT_URL . "includes/captcha.php?w=100&h=40&size=25&nocache=".rand(1000,9999);
      $login_tpl->assign("captcha", $captcha);
      $login_tpl->parse("login.html_captcha");
    }

  }else{
    $login_tpl->parse("login.html_pass_sec");
  }

  $login_tpl->assign("data", $data);
  $login_tpl->parse("login");
  flush();
	$login_tpl->out("login");
	exit();
}

?>
