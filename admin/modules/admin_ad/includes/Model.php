<?php
/*================================================================================*\
|| 							Name code : tourl.php 		 		 																	  # ||
||  				Copyright © 2007 by Thai Son - CMS vnTRUST                					# ||
\*================================================================================*/
/**
 * @version : 6.0
 * @date upgrade : 12/05/2015 by Thai Son
 **/

if (!defined('IN_vnT')) {
  die('Access denied');
}

define("MOD_NAME","admin");
define("DIR_MODULES", DIR_MODULE . "/".MOD_NAME."_ad");
define("INCLUDE_PATH", dirname(__FILE__));

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

    $lang = ($_GET['lang']) ? $_GET['lang'] : "vn";
    $this->loadSetting($lang);
  }



  /*-------------- loadSetting --------------------*/
  function loadSetting($lang = "vn")
  {
    global $vnT, $func, $DB, $conf;

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
    $this->skin->assign('CONF', $vnT->conf);
    $this->skin->assign('LANG', $vnT->lang);
    $this->skin->assign('LANG_MOD',$vnT->lang['tourl']);
    $this->skin->assign('INPUT', $vnT->input);
    $this->skin->assign("DIR_JS", $vnT->dir_js);
    $this->skin->assign('DIR_MOD', "modules/".MOD_NAME."_ad");
    $this->skin->assign('DIR_IMAGE', $vnT->dir_images);
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
    $html->assign('DIR_MOD', "modules/".MOD_NAME."_ad");
    $html->assign('LANG', $vnT->lang);
    $html->assign('INPUT', $input);
    $html->assign('CONF', $vnT->conf);
    $html->assign('DIR_IMAGE', $vnT->dir_images);
    $html->assign("data", $data);

    $html->parse($file);
    return $html->text($file);
  }



//-------------------- List Level ---------------------
  function List_Level ($did = "", $ext = "")
  {
    global $vnT, $func, $DB, $conf;
    $text = "<select name='level' id='level' " . $ext . " >";
    $text .= "<option value='0' selected> " . $vnT->lang['group_admin'] . "  </option>";
    $sql = "SELECT * FROM admin_group order by a_order ";
    $result = $DB->query($sql);
    while ($row = $DB->fetch_row($result)) {
      $title = $func->HTML($row['title']);
      if ($row['gid'] == $did) {
        $text .= "<option value=\"{$row['gid']}\" selected>" . $title . "</option>";
      } else {
        $text .= "<option value=\"{$row['gid']}\">" . $title . "</option>";
      }
    }
    $text .= "</select>";
    return $text;
  }

//-------------------- List_Permission ---------------------
  function List_Permission ($did = "")
  {
    global $vnT;
    $arr_selected = explode(",", $did);
    $text = "<select name=\"permission[]\" size=\"5\" multiple style='width:250px;'>";
    if ($did == "")
      $text .= '<option value="" selected>-- All --</option>';
    else
      $text .= '<option value="" >-- All --</option>';
    foreach ($vnT->permission as $key => $value) {
      if (@in_array($key, $arr_selected))
        $text .= '<option value="' . $key . '" selected>' . $value . '</option>';
      else
        $text .= '<option value="' . $key . '">' . $value . '</option>';
    }
    $text .= "</select>";
    return $text;
  }





  /**
   * function checkSubmit ()
   *
   **/
  function checkSubmit()
  {
    global $vnT, $DB, $func, $conf;
    $mess = "";
    $ok = 1;


    $username= trim($vnT->input['username']);
    $email  = trim($vnT->input['email']);
    $password = $vnT->input['password'];
    $re_password = $vnT->input['re_password'];

    if(empty($vnT->input['csrf_token']) || ($vnT->input['csrf_token'] != $_SESSION['vnt_csrf_token']) ) {
      $ok = 0;
      $mess = '<div class="err-item">'.$vnT->lang['err_csrf_token'].'</div>' ;
    }

    //check input
    if ( empty($username) || strlen($username) < 3) {
      $ok = 0;
      $mess =  ( empty($username)) ? '<div class="err-item">'.$vnT->lang['err_username_empty'].'</div>' : '<div class="err-item">'.$vnT->lang['err_username_invalid'].'</div>' ;

    }else{
      if ( strlen($username) < 3 ) {
        $ok = 0;
        $mess .= '<div class="err-item">'.$vnT->lang['err_name_invalid'].'</div>' ;
      }

      if (!preg_match("/^[a-zA-Z0-9_.]{4,30}$/", $username)) {
        $ok = 0;
        $mess =  '<div class="err-item">'.$vnT->lang['err_username_invalid'].'</div>';

      }
    }

    if(empty($email)){
      $ok =0;
      $mess .= '<div class="err-item">'.$vnT->lang['err_email_empty'].'</div>';
    }else{
      if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $ok = 0;
        $mess .= '<div class="err-item">'.$vnT->lang['err_email_invalid'].'</div>' ;
      }
    }

    if($password !='') {
      /*if(!$func->isStrongPassword($password)){
        $ok =0;
        $mess .= '<div class="err-item">'.$vnT->lang['err_password_invalid'].'</div>';
      }*/
      if($password != $re_password) {
        $ok =0;
        $mess .= '<div class="err-item">'.$vnT->lang['err_re_password_incorrect'].'</div>';
      }
    }


    $out['ok'] = $ok;
    $out['mess'] = $mess;
    return $out;
  }




}
$model = new Model();
?>