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

define("MOD_NAME","contact");
define("DIR_MODULES", DIR_MODULE . "/".MOD_NAME."_ad");
define("INCLUDE_PATH", dirname(__FILE__));
define('MOD_DIR_UPLOAD', '../vnt_upload/'.MOD_NAME.'/');
define('ROOT_UPLOAD', 'vnt_upload/'.MOD_NAME.'/');
define('MOD_ROOT_URL', $conf['rooturl'] . 'modules/'.MOD_NAME.'/');

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

    $res_city = $vnT->DB->query( "SELECT * FROM iso_cities where country='VN' AND display=1  order by c_order ASC , name ASC  " );
    while ($row_city = $vnT->DB->fetch_row($res_city)) {
      $vnT->setting['arr_city'][$row_city['id']]	= $row_city;
    }

    $res_state = $vnT->DB->query( "SELECT * FROM iso_states where  display=1  order by s_order ASC , name ASC  " );
    while ($row_state = $vnT->DB->fetch_row($res_state)) {
      $vnT->setting['arr_state'][$row_state['id']] = $row_state;
    }
    $vnT->setting['arr_status_contact'] = array(0 =>'Chưa xem' , 1 => 'Đã xem' );
    $vnT->setting['arr_status_register'] = array(0 =>'Đăng ký mới' , 1 => 'Đã liên lạc hỗ trợ' , 2 => 'Đã hủy bỏ');
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



//-------- get_text_address
  function get_text_address ($info)
  {
    global $vnT;
    $text = $info['address'];
    if ($info['state'])
      $text .= ", " . $vnT->setting['arr_state'][$info['state']]['name'];
    if ($info['city'])
      $text .= ", " .  $vnT->setting['arr_city'][$info['city']]['name'];
    return $text;
  }



  /*-------------- buildInfoItem --------------------*/
  function buildInfoItem( )
  {
    global $vnT , $func, $DB, $conf ;
    $out = array();


    $out['title'] =  $vnT->func->txt_HTML($_POST['title']) ;
    $out['company'] = $vnT->func->txt_HTML($_POST['company']) ;
    $out['address'] =  $vnT->func->txt_HTML($_POST['address']) ;
    $out['phone'] = $vnT->input['phone'] ;
    $out['fax'] = $vnT->input['fax'] ;
    $out['email'] = $vnT->input['email'] ;
    $out['website'] = $vnT->input['website'] ;
    $out['description'] = $DB->mySQLSafe($_POST['description']);

    $out['map_address'] = $vnT->input['map_address'];
    $out['map_type'] = $vnT->input['map_type'];
    $out['map_desc'] = $vnT->func->txt_HTML($_POST['map_information']) ;
    $out['map_lat'] = $vnT->input['map_lat'] ;
    $out['map_lng'] = $vnT->input['map_lng'] ;
    $out['map_picture'] = $vnT->input['map_picture'] ;
    $out['map_embed'] =  $vnT->func->txt_HTML($_POST['map_embed']) ;



    return $out;
  }



  //------load_more_data
  function load_more_data($data,$lang="vn")
  {
    global $vnT , $input , $DB;
    $out = array();


    return $out ;
  }



  /*------ process_info_more ---------*/
  function process_info_more(){
    global $vnT ;
    $out = array() ;

    return $out;
  }



//======================= List_Search =======================
  function List_Search($did, $type_show = "list")
  {
    global $func, $DB, $conf, $vnT;
    $arr_where = array('name' => $vnT->lang['full_name'],  'subject' => $vnT->lang['subject'], 'phone' => $vnT->lang['phone'] , 'email' => "Email",  'date_post' => $vnT->lang['date_post'] . " (d/m/Y)");

    $text = '';
    foreach ($arr_where as $key => $value) {
      $selected = ($did == $key) ? "selected" : "";
      $text .= "<option value=\"{$key}\" {$selected} > {$value} </option>";
    }

    if($type_show=="option") {
      $textout = $text ;
    }else{
      $textout = "<select size=1 name=\"search\" id='search' class='form-control'  >";
      $textout .= $text ;
      $textout .= "</select>";
    }

    return $textout;
  }

  /*------ process_info_search ---------*/
  function process_info_search($info){
    global $vnT ;
    $out = array() ;
    $lang = ($info['lang']) ? $info['lang'] : 'vn' ;

    $status = (isset($vnT->input['status'])) ?   $vnT->input['status'] : "-1";
    $search = ($vnT->input['search']) ?  $vnT->input['search'] : "title";
    $keyword = ($vnT->input['keyword']) ?  $vnT->input['keyword'] : "";
    $date_begin = ($vnT->input['date_begin']) ?  $vnT->input['date_begin'] : "";
    $date_end = ($vnT->input['date_end']) ?  $vnT->input['date_end'] : "";

    $where ="  ";
    $ext_page='';
    $ext='';


    if($status != "-1"){
      $where .=" and status=".$status;
      $ext_page .="status=$status|";
      $ext.="&status=$status";
    }


    if($date_begin || $date_end )
    {
      $tmp1 = @explode("/", $date_begin);
      $time_begin = @mktime(0, 0, 0, $tmp1[1], $tmp1[0], $tmp1[2]);

      $tmp2 = @explode("/", $date_end);
      $time_end = @mktime(23, 59, 59, $tmp2[1], $tmp2[0], $tmp2[2]);

      $where.=" AND (datesubmit BETWEEN {$time_begin} AND {$time_end} ) ";
      $ext.="&date_begin=".$date_begin."&date_end=".$date_end;
      $ext_page.= "date_begin=".$date_begin."|date_end=".$date_end."|";
    }

    if(!empty($search)){
      $ext_page.="search=$search|";
      $ext.="&search={$search}";
    }

    if(!empty($keyword)){
      switch($search){
        case "date_post" : $where .=" and DATE_FORMAT(FROM_UNIXTIME(datesubmit),'%d/%m/%Y') = '{$keyword}' "; break;
        default :$where .=" and $search like '%$keyword%' ";break;
      }

      $ext_page.="keyword=$keyword|";
      $ext.="&keyword={$keyword}";
    }



    $data["status"] = $status;
    $data["search"] = $search;

    $list_status ='<option value="-1">--- Tất cả ---</option>';
    foreach ($vnT->setting['arr_status_contact'] as $key => $value) {
      $selected = ($status == $key) ? "selected" : "";
      $list_status .= "<option value=\"{$key}\" {$selected} > {$value} </option>";
    }
    $data['list_status'] = $list_status;

    $data['list_search']= $this->List_Search($search,"option");
    $data['keyword'] = $keyword;
    $data['date_begin'] = $date_begin;
    $data['date_end'] = $date_end;

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
      //xoa cache
      $func->clear_cache();
      $mess ='';
      if ($vnT->input["del_id"]) $h_id = $vnT->input["del_id"];
      switch ($vnT->input["do_action"])
      {
        case "do_edit":
          $arr_order  = (isset($vnT->input["txt_Order"])) ? $vnT->input["txt_Order"] : array();
          $arr_focus  = (isset($vnT->input["txt_Focus"])) ? $vnT->input["txt_Focus"] : array();

          $mess .= "- " . $vnT->lang['edit_success'] . " ID: <strong>";
          $str_mess = "";
          for ($i = 0; $i < count($h_id); $i ++)
          {
            $dup = array();
            $dup['p_order'] = $arr_order[$h_id[$i]];
            $dup['focus'] = $arr_focus[$h_id[$i]];
            $dup['date_update'] = time();

            $ok = $DB->do_update( "products" , $dup, "p_id=" . $h_id[$i]);
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
          $str_mess ='';
          for ($i = 0; $i < count($h_id); $i ++)
          {
            $dup['display'] = 0;
            $ok = $DB->do_update("products_desc", $dup, "lang='".$lang."' AND p_id=" . $h_id[$i]);
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
            $ok = $DB->do_update("products_desc", $dup, "lang='".$lang."' AND p_id=" . $h_id[$i]);
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

    if((int)$vnT->input["do_display"]) {
      $ok = $DB->query("Update products_desc SET display=1 WHERE lang='".$lang."' AND p_id=".$vnT->input["do_display"]);
      if($ok){
        $mess .= "- " . $vnT->lang['display_success'] . "  ID: <strong>".$vnT->input["do_display"] . "</strong><br>";
        $err = $func->html_mess($mess);
        $ok_up = 1;
      }
      //xoa cache
      $func->clear_cache();
    }
    if((int)$vnT->input["do_hidden"]) {
      $ok = $DB->query("Update products_desc SET display=0 WHERE lang='".$lang."' AND p_id=".$vnT->input["do_display"]);
      if($ok){
        $mess .= "- " . $vnT->lang['hidden_success'] . "  ID: <strong>".$vnT->input["do_hidden"] . "</strong><br>";
        $err = $func->html_mess($mess);
        $ok_up = 1;
      }
      //xoa cache
      $func->clear_cache();
    }

    $out['ok'] = $ok_up  ;
    $out['err'] = $err ;
    return $out;
  }



}
$model = new Model();
?>