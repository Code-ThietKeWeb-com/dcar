<?php
/*================================================================================*\
|| 							Name code : product.php 		 		 																	  # ||
||  				Copyright © 2007 by Thai Son - CMS vnTRUST                					# ||
\*================================================================================*/
/**
 * @version : 6.0
 * @date upgrade : 12/05/2015 by Thai Son
 **/

if (!defined('IN_vnT')) {
  die('Access denied');
}
define("MOD_NAME","contact");
define("DIR_MODULES", PATH_ROOT . "/modules/".MOD_NAME);
define("INCLUDE_PATH", dirname(__FILE__));
define("DIR_MOD", ROOT_URI . "modules/".MOD_NAME);
define("MOD_DIR_IMAGE", ROOT_URI . "modules/".MOD_NAME."/images");
define("LINK_MOD", $vnT->link_root . $vnT->setting['seo_name'][$vnT->lang_name]['contact']);
class Model
{

  /**
   * The Constructor.
   */
  public function __construct()
  {
    global $vnT ;
    //autoloader
    include_once( INCLUDE_PATH .DS . 'autoloader.php' );
  }

  /**
   * Take a class name and turn it into a file name.
   *
   * @param  string $class
   * @return string
   */
  function loadSkinModule($file_tpl , $data = array())
  {
    global $vnT , $input;
    $this->skin = new XiTemplate( DIR_MODULES . "/html/". $file_tpl . ".tpl");
    $this->skin->assign('DIR_MOD', DIR_MOD);
    $this->skin->assign('LANG_MOD', $vnT->lang[MOD_NAME]);
    $this->skin->assign('LANG', $vnT->lang);
    $this->skin->assign('INPUT', $input);
    $this->skin->assign('CONF', $vnT->conf);
    $this->skin->assign('DIR_IMAGE', $vnT->dir_images); 
    $this->skin->assign('DIR_JS', $vnT->dir_js);
    $this->skin->assign('data', $data);
  }



  /**
   * function load_html ()
   *
   **/
  function load_html ($file, $data)
  {
    global $vnT, $input;
    $html = new XiTemplate( DIR_MODULES . "/html/" . $file . ".tpl");

    $html->assign('DIR_MOD', DIR_MOD);
    $html->assign('LANG', $vnT->lang);
    $html->assign('INPUT', $input);
    $html->assign('CONF', $vnT->conf);
    $html->assign('DIR_IMAGE', $vnT->dir_images);
    $html->assign("data", $data);

    $html->parse($file);
    return $html->text($file);
  }


  /**
   * function check_submit_form ()
   *
   **/

  function getDepartment ($did = -1, $ext = "")
  {
    global $func, $DB, $conf, $vnT;
    $text = "<select name=\"staff\" id=\"staff\" class='form-control' {$ext} >";
    $query = $DB->query("select * from contact_staff WHERE display=1  order by staff_order");
    while ($row = $DB->fetch_row($query)) {
      $title = $func->fetch_array($row['title']);
      if ($row['email'] == $did)
        $text .= "<option value=\"{$row['email']}\" selected>" . $title . "</option>";
      else
        $text .= "<option value=\"{$row['email']}\">" . $title . "</option>";
    }
    $text .= "</select>";
    return $text;
  }



  /**
   * function check_submit_form ()
   *
   **/
  function check_submit_form()
  {
    global $vnT, $input;
    $mess = "";
    $ok = 1;


    if($vnT->conf['captcha_type']=="reCAPTCHA"){
      $ok_captcha = $vnT->func->check_security_code("reCAPTCHA");
    }else{
      $ok_captcha = $vnT->func->check_security_code("session_sec_code");
    }

    if(!$ok_captcha){
      $ok = 0;
      $mess .= '<div class="err-item">'.$vnT->lang['global']['security_code_invalid'] .'</div>' ;
    }

    if(empty($input['csrf_token']) || ($input['csrf_token'] != $_SESSION['csrf_token']) ) {
      $ok = 0;
      $mess .= '<div class="err-item">'.$vnT->lang['global']['err_csrf_token'].'</div>' ;
    }


    $name = trim($input['name']);
    $phone = trim($input['phone']);
    $email = trim($input['email']);

    //check input
    if ( empty($name) || strlen($name) < 3) {
      $ok = 0;
      $mess =  ( empty($name)) ? '<div class="err-item">'.$vnT->lang['global']['err_name_empty'].'</div>' : '<div class="err-item">'.$vnT->lang['global']['err_name_invalid'].'</div>' ;

    }else{
      if ( strlen($name) < 3 ) {
        $ok = 0;
        $mess .= '<div class="err-item">'.$vnT->lang['global']['err_name_invalid'].'</div>' ;
      }
    }

    if(empty($email)){
      $ok =0;
      $mess .= '<div class="err-item">'.$vnT->lang['global']['err_email_empty'].'</div>';
    }else{
      if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $ok = 0;
        $mess .= '<div class="err-item">'.$vnT->lang['global']['err_email_invalid'].'</div>' ;
      }
    }

    if(empty($phone)){
      $ok =0;
      $mess .= '<div class="err-item">'.$vnT->lang['global']['err_phone_empty'].'</div>';
    }else{
      if ( !preg_match('/^[0-9]{9,12}+$/', $phone) ) {
        $ok = 0;
        $mess .= '<div class="err-item">'.$vnT->lang['global']['err_phone_invalid'].'</div>' ;
      }
    }

    //check
    $arr_filed_check = array("subject","content") ;
    foreach ($arr_filed_check as $val)
    {
      if(empty($_POST[$val])){
        $ok = 0;
        $mess .= '<div class="err-item">Vui lòng nhập đầy đủ thông tin</div>' ;
      }
    }

    //xu ly post lien tiep
    $sec_limit = 60;
    $time_post = time() - $sec_limit;
    if (isset($_SESSION['last_post'])) {
      $sec = $_SESSION['last_post'] - $time_post;
      if ($sec > 0) {
        $ok = 0;
        $mess= str_replace("{sec}", $sec, $vnT->lang['global']['err_time_post']);
      }
    }

    $out['ok'] = $ok;
    $out['mess'] = $mess;
    return $out;
  }

  /**
   *
   * @param
   * @return
   */
  function box_sidebar()
  {
    global $vnT , $input ;
    $textout = '' ;
    return $textout;
  }

}
$model = new Model();
?>