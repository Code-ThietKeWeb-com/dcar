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

define("MOD_NAME","lang");
define("DIR_MODULES", DIR_MODULE . "/".MOD_NAME."_ad");
define("INCLUDE_PATH", dirname(__FILE__));
define('MOD_DIR_UPLOAD', '../language/');
define('ROOT_UPLOAD', 'language/');
define('DIR_UPLOAD', '../vnt_upload/lang');

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




//=====
  function List_Lang ($did)
  {
    global $func, $DB, $conf;
    $text = "<select size=1 name=\"name\" onchange=\"location.href='?mod=lang&act=lang&sub=edit_lang&name='+this.value\"  style=\"width:150px\">";
    $query = $DB->query("SELECT * FROM language ");
    while ($row = $DB->fetch_row($query)) {
      if ($row['name'] == $did)
        $text .= "<option value=\"{$row['name']}\" selected>{$row['title']}</option>";
      else
        $text .= "<option value=\"{$row['name']}\" >{$row['title']}</option>";
    }
    $text .= "</select>";
    return $text;
  }

//=====
  function List_Type ($did)
  {
    global $func, $DB, $conf;
    $text = "<select size=1 name=\"type\" style=\"width:150px\" onchange=\"submit();\" >";
    $text .= "<option value=\"global\" selected>Global</option>";
    if ("modules" == $did)
      $text .= "<option value=\"modules\" selected>Modules</option>";
    else
      $text .= "<option value=\"modules\" >Modules</option>";
    if ("blocks" == $did)
      $text .= "<option value=\"blocks\" selected>Blocks</option>";
    else
      $text .= "<option value=\"blocks\" >Blocks</option>";
    $text .= "</select>";
    return $text;
  }

//= List_Phrase
  function List_Phrase ($type, $did = "global")
  {
    global $func, $DB, $conf;
    $text = "<select size=1 name=\"phrase\"  style=\"width:200px\" onchange=\"submit();\" >";
    $text .= "<option value=\"\" selected>-- Select Phrase --</option>";
    $sql = "select * from lang_phrase where type='$type' order by fieldname";
    $result = $DB->query($sql);
    while ($row = $DB->fetch_row($result)) {
      $fieldname = $row['fieldname'];
      $title = $row['title'];
      if ($fieldname == $did)
        $text .= "<option value=\"{$fieldname}\" selected>{$title}</option>";
      else
        $text .= "<option value=\"{$fieldname}\" >{$title}</option>";
    }
    $text .= "</select>";
    return $text;
  }


  /*------ do_ProcessUpdate ---------*/
  function do_ProcessUpdate($lang="vn"){
    global $vnT ,$func ,$DB, $conf ;
    $ok_up = 0;
    $err = '';


    //update
    if ((int) $vnT->input['default_id'])
    {
      $default_id = (int) $vnT->input['default_id'];
      $DB->query("update  language set is_default=0 ");
      $ok = $DB->query("update  language set is_default=1 WHERE lang_id=".$default_id);
      if ($ok) {
        $err = $func->html_mess($vnT->lang['edit_success']);
      } else {
        $err = $func->html_err($vnT->lang['edit_failt']);
      }
      //insert adminlog
      $func->insertlog("Set Default", $_GET['act'], $default_id);
      $ok_up = 1;
    }


    if ($vnT->input["do_action"])
    {
      if(empty($vnT->input['csrf_token']) || ($vnT->input['csrf_token'] != $_SESSION['vnt_csrf_token']) ) {
        $err =  $func->html_err($vnT->lang['err_csrf_token']) ;
      }else{
        unset($_SESSION['vnt_csrf_token']);
        //xoa cache
        $func->clear_cache();
        $mess ='';
        if ($vnT->input["del_id"]) $h_id = $vnT->input["del_id"];
        switch ($vnT->input["do_action"])
        {
          case "do_edit":

            break;
          case "do_hidden":
            $mess .= "- " . $vnT->lang['hidden_success'] . " ID: <strong>";
            $str_mess ='';
            for ($i = 0; $i < count($h_id); $i ++)
            {
              $dup['display'] = 0;
              $ok = $DB->do_update("language", $dup, "lang_id=" . $h_id[$i]);
              if ($ok){
                $str_mess .= $h_id[$i] . ", ";
              }
            }
            $mess .= substr($str_mess, 0, - 2) . "</strong><br>";
            $err = $func->html_mess($mess);
            $ok_up = 1;
            break;
          case "do_display":
            $mess .= "- " . $vnT->lang['display_success'] . "  ID: <strong>";
            $str_mess ='';
            for ($i = 0; $i < count($h_id); $i ++)
            {
              $dup['display'] = 1;
              $ok = $DB->do_update("language", $dup, "lang_id=" . $h_id[$i]);
              if ($ok){
                $str_mess .= $h_id[$i] . ", ";
              }
            }
            $mess .= substr($str_mess, 0, - 2) . "</strong><br>";
            $err = $func->html_mess($mess);
            $ok_up = 1;
            break;
        }
      }

    }

    if((int)$vnT->input["do_display"]) {
      if(empty($vnT->input['csrf_token']) || ($vnT->input['csrf_token'] != $_SESSION['vnt_csrf_token']) ) {
        $err =  $func->html_err($vnT->lang['err_csrf_token']) ;

      }else{
        unset($_SESSION['vnt_csrf_token']);
        $ok = $DB->query("Update language SET display=1 WHERE  lang_id=".$vnT->input["do_display"]);
        if($ok){
          $mess .= "- " . $vnT->lang['display_success'] . "  ID: <strong>".$vnT->input["do_display"] . "</strong><br>";
          $err = $func->html_mess($mess);
          $ok_up = 1;
        }
        //xoa cache
        $func->clear_cache();
      }
    }
    if((int)$vnT->input["do_hidden"]) {

      if(empty($vnT->input['csrf_token']) || ($vnT->input['csrf_token'] != $_SESSION['vnt_csrf_token']) ) {
        $err =  $func->html_err($vnT->lang['err_csrf_token']) ;
      }else{
        unset($_SESSION['vnt_csrf_token']);
        $ok = $DB->query("Update language SET display=0 WHERE lang_id=".$vnT->input["do_hidden"]);
        if($ok){
          $mess .= "- " . $vnT->lang['hidden_success'] . "  ID: <strong>".$vnT->input["do_hidden"] . "</strong><br>";
          $err = $func->html_mess($mess);
          $ok_up = 1;
        }
        //xoa cache
        $func->clear_cache();
      }
    }

    $out['ok'] = $ok_up  ;
    $out['err'] = $err ;
    return $out;
  }



}
$model = new Model();
?>