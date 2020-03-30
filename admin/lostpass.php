<?php

$action_tpl = new XiTemplate(DIR_SKIN . DS . "lostpass.tpl");
$action_tpl->assign('LANG', $vnT->lang);
$action_tpl->assign("DIR_IMAGE", $vnT->dir_images);
$action_tpl->assign("DIR_STYLE", $vnT->dir_style);
$action_tpl->assign("DIR_JS", $vnT->dir_js);
$action_tpl->assign("CONF", $conf);



  if($_GET['code']){

    $code_reset = str_replace("'", "",  trim($_GET['code']));
    $res_ck = $DB->query("SELECT * FROM admin WHERE code_reset='".$code_reset."' ");
    if($row_ck = $DB->fetch_row($result))
    {

      if($_POST['do_submit'])
      {

        $ok_submit = 1 ;

        if(empty($vnT->input['new_pass'])) {
          $ok_submit = 0 ;
          $mess = $vnT->lang['err_password_empty'];
        }
        if($vnT->input['new_pass']!=$vnT->input['re_new_pass'])
        {
          $ok_submit = 0 ;
          $mess = $vnT->lang['err_re_password_incorrect'];
        }

        $ok_capcha = $vnT->func->check_security_code("reCAPTCHA");
        if(!$ok_capcha){
          $ok_submit = 0 ;
          $mess= $vnT->lang['err_security_code_invalid'];
        }

        if ($ok_submit) {

          $dataup = array();
          $dataup["password"] = $func->md10($vnT->input['new_pass']);
          $dataup['code_reset'] = md5(uniqid(microtime()));
          $kq = $vnT->DB->do_update("admin", $dataup, "adminid=" . $row_ck['adminid']);
          if ($kq) {
            $mess = $vnT->lang['mess_changepass_success'];
            $link_ref = "?act=login";
            $func->html_redirect($link_ref, $mess);
          } else {
            $err = $func->html_err($vnT->lang['edit_failt']);
          }
        } else {
          $err = $func->html_err($mess);

        }
      }

      $data['err'] = $err;
      $data['link_action'] = "?act=lostpass&code=".$_GET['code'];
      $action_tpl->assign("data", $data);
      $action_tpl->parse("reset_pass");
      $form_content =  $action_tpl->text("reset_pass");

    }else{
      $mess = $vnT->lang['reset_code_not_exist'];
      $link_ref = "?act=lostpass";
      $func->html_redirect($link_ref,$mess);
    }

  }else{

    if($_POST['do_submit'])
    {

      if (isset($_POST['txtUsername'])) $admin_user=$_POST['txtUsername']; else $admin_user='';
      if (isset($_POST['txtEmail'])) $admin_email=$_POST['txtEmail']; else $admin_email='';

      $ok_captcha  = 1;
      if($vnT->conf['captcha_admin']){
        if($vnT->conf['captcha_type']=="reCAPTCHA"){
          $ok_captcha = $vnT->func->check_security_code("reCAPTCHA");
        }else{
          $ok_captcha = $vnT->func->check_security_code("session_sec_code");
        }
      }

      if ($ok_captcha)
      {

        // Check
        $result = $DB->query("SELECT * FROM admin WHERE username='".$admin_user."' AND email='".$admin_email."' ");
        if ($row = $DB->fetch_row($result))
        {

          $code_reset =  $row["code_reset"];
          $link_reset = $vnT->conf['rooturl'] . FOLDER_ADMIN . "/?act=lostpass&code=" . $code_reset;
          $to =  $row['email'] ;
          $from = ($vnT->conf['email']) ? $vnT->conf['email'] : $row['email'] ;



          $subject = "Thong tin Reset mau khau tai khoan ".$row['username']." (".$_SERVER['HTTP_HOST'].")";
          $message = "Chao ".$row['username']." - website ".$_SERVER['HTTP_HOST']."<br>";
          $message .= "Ban da dung chuc nang tim lai mat khau trang quan tri . <br>";
          $message .= "Ban hay click vao duong link duoi day de reset lai ma khau . <br>";
          $message .= "Link : <a href='".$link_reset."'>".$link_reset."</a> <br>";
          $message .= "Ban quan tri ".$_SERVER['HTTP_HOST'] ;

          $sent = $func->doSendMail($to, $subject, $message,$from );

          if ($sent){
            $mess = str_replace("{email}",$to,$vnT->lang['mess_send_changepass_success']) ;
            $link_ref = "?act=login";
            $func->html_redirect($link_ref, $mess);
          }
          else $err= $func->html_err("He thong email co loi , Vui long quay lai sau ");

        } else {
          $err = $func->html_err("Username hoac Email khong chinh xac");
        }

      } else {
        $err = $func->html_err($vnT->lang['err_security_code_invalid']);
      }
    }


    $background = $vnT->conf['rooturl'] . "modules/member/images/bg_login.jpg";
    $res_bg = $DB->query("select * from advertise where pos='bg_login' and display=1  order by l_order LIMIT 0,1");
    if ($row_bg = $DB->fetch_row($res_bg)) {
      $background = $vnT->conf['rooturl'] . "vnt_upload/weblink/" . $row_bg['img'];
    }
    $data['background'] = $background;
    $data['err'] = $err;
    if (isset($_SESSION['mess']) && $_SESSION['mess'] != '') {
      $data['err'] = $_SESSION['mess'];
      unset($_SESSION['mess']);
    }

    $vnT->conf['favicon'] = ($vnT->conf['favicon']) ? $vnT->conf['favicon'] : $vnT->conf['rooturl'] . 'favicon.ico';



    if($vnT->conf['captcha_admin']){
      if($vnT->conf['captcha_type']=="reCAPTCHA"){
        $captcha = array();
        $captcha['reCAPTCHA_site_key'] = $vnT->conf['reCAPTCHA_site_key'];
        $action_tpl->assign("captcha", $captcha);
        $action_tpl->parse("lostpass.html_recaptcha");
      }else{
        $captcha = array();
        $captcha['ver_img'] = ROOT_URL . "includes/captcha.php?w=100&h=40&size=18&nocache=".rand(1000,9999);
        $action_tpl->assign("captcha", $captcha);
        $action_tpl->parse("lostpass.html_captcha");
      }
    }


    $action_tpl->assign("data", $data);
    $action_tpl->parse("lostpass");
    $form_content =  $action_tpl->text("lostpass");
  }

  $action_tpl->assign("form_content", $form_content);
  $action_tpl->parse("body_tpl");
  flush();
  echo  $action_tpl->out("body_tpl");
  exit();

?>