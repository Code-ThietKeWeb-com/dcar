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

define("MOD_NAME","advertise");
define("DIR_MODULES", DIR_MODULE . "/".MOD_NAME."_ad");
define("INCLUDE_PATH", dirname(__FILE__));
define('MOD_DIR_UPLOAD', '../vnt_upload/weblink/');
define('ROOT_UPLOAD', 'vnt_upload/weblink/');

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
    $setting = array();
    $result = $vnT->DB->query("select * from ad_pos WHERE display=1 ORDER BY display_order ASC , id DESC ");
    $i = 0;
    while ($row = $vnT->DB->fetch_row($result)) {
      $i ++;
      if ($i == 1) {
        $vnT->setting['pos'] = $row['name'];
      }
      $vnT->setting[$row['name']]['width'] = $row['width'];
      $vnT->setting[$row['name']]['height'] = $row['height'];
      $vnT->setting[$row['name']]['align'] = $row['align'];
      $vnT->setting[$row['name']]['type_show'] = $row['type_show'];
    }
    unset($setting);

    $vnT->setting['arr_target'] =  array("_self"=>"Tại trang (_self)","_blank"=>"Cửa sổ mới (_blank)","_parent"=>"Cửa sổ cha (_parent)","_top"=>"Cửa sổ trên cùng (_top)") ;

    $vnT->setting['arr_type'] = array(0 => 'Dạng hình logo, banner' ,   1 => 'Dạng chữ (text)' ,    2 => 'Dạng script '    );

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



  /*------ get_format_time_by_string ---------*/
  function get_format_time_by_string($text){
    global $vnT ;
    $out = '' ;
    if($text){
      $tmp = @explode("-",$text);
      $gio =  explode(":", trim($tmp[0]));
      $ngay =  explode("/", trim($tmp[1]));

      $out = mktime($gio[0], $gio[1], 0, $ngay[1], $ngay[0], $ngay[2]);
    }
    return $out;
  }


  /*------ listAlign ---------*/
  function listAlign ($did)
  {
    global $func, $DB, $conf;
    $text = "<select size=1 name=\"align\">";
    if ($did == "left")
      $text .= "<option value=\"left\" selected> Left </option>";
    else
      $text .= "<option value=\"left\" > Left </option>";
    if ($did == "center")
      $text .= "<option value=\"center\" selected> Center </option>";
    else
      $text .= "<option value=\"center\"> Center </option>";
    if ($did == "right")
      $text .= "<option value=\"right\" selected> Right </option>";
    else
      $text .= "<option value=\"right\"> Right </option>";
    $text .= "</select>";
    return $text;
  }


  /*------ List_Type_Show ---------*/
  function List_Type_Show ($did)
  {
    global $vnT;
    $arr_type_show = array(  0 => 'Theo chiều dọc' , 1 => 'Theo chiều ngang' );
    $text = "<select size=1 name=\"type_show\">";
    $p = 0;
    foreach ($vnT->setting['arr_type']  as $key => $value) {
      if ($did == $key)
        $text .= '<option value="' . $key . '" selected>' . $value . '</option>';
      else
        $text .= '<option value="' . $key . '">' . $value . '</option>';
    }
    $text .= "</select>";
    return $text;
  }


  /*-------------- List_Target --------------------*/
  function List_Target ($did = '_self',$ext="")
  {
    global $vnT;

    $text = "<select size=1 name=\"target\" id='target' class='select'  {$ext} >";

    foreach ($vnT->setting['arr_target'] as $key => $value)
    {
      $selected = ($key==$did) ? "selected" : "";
      $text .= "<option value=\"{$key}\" {$selected} > {$value} </option>";
    }

    $text .= "</select>";
    return $text;
  }

//---------- list_type_ad
  function list_type_ad ($did , $type_show = "option" )
  {
    global $vnT;


    $text = "";
    foreach ($vnT->setting['arr_type'] as $key => $value)
    {
      $selected	 = ($key==$did) ? " selected ": "" ;
      $text .= "<option value=\"{$key}\" {$selected} > {$value} </option>";
    }


    if($type_show=="option"){
      $textout = $text;
    }else{
      $textout =  "<select size=1 name=\"type_ad\" id=\"type_ad\" class='select'   >";
      $textout .= $text;
      $textout .= '</select>';
    }
    return $textout;
  }


//---------- List_Pos
  function List_Pos ($did = -1 , $type_show = "option")
  {
    global $func, $DB, $conf;
    $text = "";
    $res = $DB->query("select * from ad_pos ORDER BY  display_order ASC , id DESC");
    while ($row = $DB->fetch_row($res)) {
      if ($did == $row['name'])
        $text .= "<option value=\"{$row['name']}\" selected> " . $row['title'] . " </option>";
      else
        $text .= "<option  value=\"{$row['name']}\" > " . $row['title'] . " </option>";
    }

    if($type_show=="option"){
      $textout = $text;
    }else{
      $textout =  "<select size=1 name=\"module\" id=\"module\" class='select'   >";
      $textout .= $text;
      $textout .= '</select>';
    }

    return $textout;
  }

//--------------------
  function List_Module_Show ($did = "")
  {
    global $func, $DB, $conf, $vnT;
    $all = "";
    if ($did)
      $arr_selected = explode(",", $did);
    else {
      $arr_selected = array();
      $all = "selected";
    }
    $text = "<select name=\"module_show[]\" id=\"module_show\" size=\"10\" multiple style='width:50%'>";
    $text .= "<option value='' {$all} >-- " . $vnT->lang['all'] . " --</option>";
    if (in_array("main", $arr_selected)) {
      $text .= "<option value='main' selected  > " . $vnT->lang['home'] . " </option>";
    } else {
      $text .= "<option value='main'  > " . $vnT->lang['home'] . " </option>";
    }
    if (in_array("about", $arr_selected)) {
      $text .= "<option value='about' selected  > " . $vnT->lang['about'] . " </option>";
    } else {
      $text .= "<option value='about'  > " . $vnT->lang['about'] . " </option>";
    }
    if (in_array("contact", $arr_selected)) {
      $text .= "<option value='contact' selected  > " . $vnT->lang['contact'] . " </option>";
    } else {
      $text .= "<option value='contact'  > " . $vnT->lang['contact'] . " </option>";
    }
    if (in_array("page", $arr_selected)) {
      $text .= "<option value='page' selected  > " . $vnT->lang['mod_page'] . " </option>";
    } else {
      $text .= "<option value='page'  > " . $vnT->lang['mod_page'] . " </option>";
    }
    $sql = "select * from modules order by id DESC";
    $result = $DB->query($sql);
    while ($row = $DB->fetch_row($result)) {
      $text .= "<option value=\"" . $row['mod_name'] . "\"";
      if (in_array($row['mod_name'], $arr_selected)) {
        $text .= " selected";
      }
      $text .= ">" . $row['name'] . "</option>\n";
    }
    $text .= "</select>";
    return $text;
  }


//==========
  function List_Module ($did = "", $type_show = "option")
  {
    global $func, $DB, $conf, $vnT;

    $text = "<option value='' selected  > -- Tất cả module -- </option>";
    if ($did == "main") {
      $text .= "<option value='main' selected  >  " . $vnT->lang['home'] . " </option>";
    } else {
      $text .= "<option value='main'  >  " . $vnT->lang['home'] . "</option>";
    }
    if ($did == "about") {
      $text .= "<option value='about' selected  >  " . $vnT->lang['about'] . " </option>";
    } else {
      $text .= "<option value='about'  >  " . $vnT->lang['about'] . " </option>";
    }
    if ($did == "contact") {
      $text .= "<option value='contact' selected  >  " . $vnT->lang['contact'] . " </option>";
    } else {
      $text .= "<option value='contact'  >  " . $vnT->lang['contact'] . " </option>";
    }
    if ($did == "page") {
      $text .= "<option value='page' selected  >  " . $vnT->lang['mod_page'] . " </option>";
    } else {
      $text .= "<option value='page'  >  " . $vnT->lang['mod_page'] . " </option>";
    }
    $sql = "select * from modules order by id DESC";
    $result = $DB->query($sql);
    while ($row = $DB->fetch_row($result)) {
      $text .= "<option value=\"" . $row['mod_name'] . "\"";
      if ($did == $row['mod_name']) {
        $text .= " selected";
      }
      $text .= ">" . $row['name'] . "</option>\n";
    }
    if($type_show=="option"){
      $textout = $text;
    }else{
      $textout =  "<select size=1 name=\"module\" id=\"module\" class='select'   >";
      $textout .= $text;
      $textout .= '</select>';
    }

    return $textout;
  }


//======================= List_Search =======================
  function List_Search($did, $type_show = "list")
  {
    global $func, $DB, $conf, $vnT;
    $arr_where = array('title'=>  $vnT->lang['title'],'l_link'=> "Link ", 'date_post' => $vnT->lang['date_post'] );

    $text = '';
    foreach ($arr_where as $key => $value) {
      $selected = ($did == $key) ? "selected" : "";
      $text .= "<option value=\"{$key}\" {$selected} > {$value} </option>";
    }

    if($type_show=="option") {
      $textout = "<select size=1 name=\"search\" id='search' class='form-control'  >";
      $textout .= $text ;
      $textout .= "</select>";
    }else{
      $textout = $text ;
    }

    return $textout;
  }





  //------load_more_data
  function load_more_data($data,$lang="vn")
  {
    global $vnT , $input;
    $out = array();


    $out['list_type_ad'] = $this->list_type_ad($data['type_ad'],"option");
    $out['list_module_show'] = $this->List_Module_Show($data['module_show']);
    $out['list_display'] = vnT_HTML::list_yesno("display", $data['display']);
    $out['list_target'] = $this->List_Target($data['target']);
    $out['readonly'] = 'readonly="readonly"';


    return $out ;
  }


  /*-------------- buildInfoItem --------------------*/
  function buildInfoItem( )
  {
    global $vnT , $func, $DB, $conf ;
    $out = array();
    $pos = $vnT->input['pos'];
    $title = $vnT->func->txt_HTML($_POST['title']);

    $date_add = $this->get_format_time_by_string("0:0 - ".$vnT->input['date_add']) ;
    $date_expire = $this->get_format_time_by_string("0:0 - ".$vnT->input['date_expire']) ;

    $module_show = ($_POST['module_show'])? @implode(",",  $_POST['module_show']) : '';
    $type_ad = $vnT->input['type_ad'];
    $file_type ='';
    switch ($type_ad)
    {
      case 1 :
        $picture = $DB->mySQLSafe($_POST['content']);
        break	;
      case 2 :
        $picture = $func->txt_HTML($_POST['script']);
        break	;
      default :
        $picture = $vnT->input['picture'];
        $file_type =  strtolower(substr($picture, strrpos($picture, ".") + 1));
        break;
    }

    //cot
    $out['pos'] = $pos ;
    $out['title'] = $title;
    $out['img'] =  $picture;
    $out['type'] = $file_type;
    //$out['description'] = $DB->mySQLSafe($_POST['description']);
    $out['link'] = trim($vnT->input['l_link']) ;
    $out['type_ad'] = $type_ad ;

    $out['width'] = trim($vnT->input['width']) ;
    $out['height'] = trim($vnT->input['height']) ;
    $out['target'] = $vnT->input['target']  ;


    $out['date_add'] = $date_add ;
    $out['date_expire'] = $date_expire ;
    $out['module_show'] = $module_show ;
    $out['display'] = $vnT->input['display'] ;

    return $out;
  }


  /*------ process_info_more ---------*/
  function process_info_more(){
    global $vnT ;
    $out = array() ;

    return $out;
  }


  /*------ process_info_search ---------*/
  function process_info_search($info){
    global $vnT ;
    $out = array() ;
    $lang = ($info['lang']) ? $info['lang'] : 'vn' ;
    $pos = ($vnT->input['pos']) ?  $vnT->input['pos'] :  $vnT->setting['pos'];
    $module = $vnT->input['module'];
    $search = ($vnT->input['search']) ?  $vnT->input['search'] : "title";
    $keyword = ($vnT->input['keyword']) ?  $vnT->input['keyword'] : "";
    $where ="  ";
    $ext_page='';
    $ext='';

    if($pos){
      $where .= " AND pos='".$pos."' ";
    }

    if ($module) {
      $where .= " and  (FIND_IN_SET('$module',module_show) or (module_show='') ) ";
      $ext = "&module=$module";
    }

    if(!empty($search)){
      $ext_page.="search=$search|";
      $ext.="&search={$search}";
    }

    if(!empty($keyword)){
      switch($search){
        case "date_post" : $where .=" and DATE_FORMAT(FROM_UNIXTIME(date_post),'%d/%m/%Y') = '{$keyword}' "; break;
        default :$where .=" and $search like '%$keyword%' ";break;
      }

      $ext_page.="keyword=$keyword|";
      $ext.="&keyword={$keyword}";
    }



    $data = array();
    $data['list_pos'] = $this->List_Pos($pos ,"option");
    $data['list_module'] = $this->List_Module($module, "option");
    $data["search"] = $search;
    $data["keyword"] = $keyword;
    $data['list_search']=$this->List_Search($search,"option");

    $out['data'] = $data ;
    $out['where'] = $where;
    $out['ext_page'] = $ext_page;
    $out['ext'] = $ext;


    return $out;
  }



  /*------ do_ProcessUpdate ---------*/
  function do_ProcessUpdate($lang="vn"){
    global $vnT ,$func ,$DB, $conf ;
    $ok_up = 0;
    $err = '';

    if ($vnT->input["do_action"])
    {
      if(empty($vnT->input['csrf_token']) || ($vnT->input['csrf_token'] != $_SESSION['vnt_csrf_token']) ) {
        $err =  $func->html_err($vnT->lang['err_csrf_token']) ;
      }else {
        unset($_SESSION['vnt_csrf_token']);
        //xoa cache
        $func->clear_cache();
        $mess =''; $str_mess='';
        if ($vnT->input["del_id"]) $h_id = $vnT->input["del_id"];
        switch ($vnT->input["do_action"])
        {
          case "do_edit":
            $arr_order  = (isset($vnT->input["txt_Order"])) ? $vnT->input["txt_Order"] : array();
            $mess .= "- " . $vnT->lang['edit_success'] . " ID: <strong>";
            for ($i = 0; $i < count($h_id); $i ++) {
              $dup['l_order'] = $arr_order[$h_id[$i]];
              $ok = $DB->do_update("advertise", $dup, "l_id=" . $h_id[$i]);
              if ($ok) {
                $str_mess .= $h_id[$i] . ", ";
              }
            }
            $mess .= substr($str_mess, 0, - 2) . "</strong><br>";
            $err = $func->html_mess($mess);
            $ok_up = 1;
            break;
          case "do_hidden":
            $mess .= "- " . $vnT->lang['hidden_success'] . " ID: <strong>";
            for ($i = 0; $i < count($h_id); $i ++) {
              $dup['display'] = 0;
              $ok = $DB->do_update("advertise", $dup, "l_id=" . $h_id[$i]);
              if ($ok) {
                $str_mess .= $h_id[$i] . ", ";
              }
            }
            $mess .= substr($str_mess, 0, - 2) . "</strong><br>";
            $err = $func->html_mess($mess);
            $ok_up = 1;
            break;
          case "do_display":
            $mess .= "- " . $vnT->lang['display_success'] . "  ID: <strong>";
            for ($i = 0; $i < count($h_id); $i ++) {
              $dup['display'] = 1;
              $ok = $DB->do_update("advertise", $dup, "l_id=" . $h_id[$i]);
              if ($ok) {
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
      }else {
        unset($_SESSION['vnt_csrf_token']);
        $ok = $DB->query("Update advertise SET display=1 WHERE l_id=".$vnT->input["do_display"]);
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
      }else {
        unset($_SESSION['vnt_csrf_token']);
        $ok = $DB->query("Update advertise SET display=0 WHERE l_id=".$vnT->input["do_hidden"]);
        if($ok){
          $mess .= "- " . $vnT->lang['display_success'] . "  ID: <strong>".$vnT->input["do_hidden"] . "</strong><br>";
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