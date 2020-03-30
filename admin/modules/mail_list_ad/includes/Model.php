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

define("MOD_NAME","mail_list");
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
    $setting = array();

    $vnT->setting['arr_category'] = array();
    $res_cat = $DB->query("SELECT *  FROM maillist_category  order by cat_order ");
    while ($row_cat = $DB->fetch_row($res_cat)) {
      $vnT->setting['arr_category'][$row_cat['cat_id']] = $row_cat['cat_name'];
    }

    unset($setting);
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




  /*** Ham Get_Cat ****/
  function Get_Cat ($did = -1, $ext = "")
  {
    global $func, $DB, $conf, $vnT;
    $text = "<select size=1 id=\"cat_id\" name=\"cat_id\" {$ext}  class='form-control'>";
    $text .= "<option value=\"0\">-- " . $vnT->lang['all'] . " --</option>";
    $query = $DB->query("SELECT * FROM maillist_category order by cat_order");
    while ($cat = $DB->fetch_row($query)) {
      $cat_name = $func->HTML($cat['cat_name']);
      if ($cat['cat_id'] == $did)
        $text .= "<option value=\"{$cat['cat_id']}\" selected>{$cat_name}</option>";
      else
        $text .= "<option value=\"{$cat['cat_id']}\" >{$cat_name}</option>";
    }
    $text .= "</select>";
    return $text;
  }

  function get_cat_name ($cat_id)
  {
    global $vnT;
    $cat_name = ($cat_id) ? $vnT->setting['arr_category'][$cat_id] : "Chưa có nhóm";
    return $cat_name;
  }


  /*------ process_info_search ---------*/
  function process_info_search($info){
    global $vnT ;
    $out = array() ;
    $lang = ($info['lang']) ? $info['lang'] : 'vn' ;


    $cat_id = (isset($vnT->input['cat_id'])) ? $vnT->input['cat_id'] : 0 ;

    $search = ($vnT->input['search']) ?  $vnT->input['search'] : "title";
    $keyword = ($vnT->input['keyword']) ?  $vnT->input['keyword'] : "";
    $date_begin = ($vnT->input['date_begin']) ?  $vnT->input['date_begin'] : "";
    $date_end = ($vnT->input['date_end']) ?  $vnT->input['date_end'] : "";

    $where ="  ";
    $ext_page='';
    $ext='';

    if($cat_id)
    {
      $where.=" AND cat_id=".$cat_id;
      $ext.="&cat_id=".$cat_id;
      $ext_page.= "cat_id=".$cat_id."|";
    }


    if($date_begin || $date_end )
    {
      $tmp1 = @explode("/", $date_begin);
      $time_begin = @mktime(0, 0, 0, $tmp1[1], $tmp1[0], $tmp1[2]);

      $tmp2 = @explode("/", $date_end);
      $time_end = @mktime(23, 59, 59, $tmp2[1], $tmp2[0], $tmp2[2]);

      $where.=" AND (date_post BETWEEN {$time_begin} AND {$time_end} ) ";
      $ext.="&date_begin=".$date_begin."&date_end=".$date_end;
      $ext_page.= "date_begin=".$date_begin."|date_end=".$date_end."|";
    }

    if(!empty($search)){
      $ext_page.="search=$search|";
      $ext.="&search={$search}";
    }

    if(!empty($keyword)){
      switch($search){
        case "p_id" : $where .=" and  n.p_id = $keyword ";   break;
        case "date_post" : $where .=" and DATE_FORMAT(FROM_UNIXTIME(date_post),'%d/%m/%Y') = '{$keyword}' "; break;
        default :$where .=" and $search like '%$keyword%' ";break;
      }

      $ext_page.="keyword=$keyword|";
      $ext.="&keyword={$keyword}";
    }



    $data["search"] = $search;
    $data["keyword"] = $keyword;
    $data['list_cat'] = $this->Get_Cat($cat_id);
    $data['cat_id'] = $cat_id;
    $data['date_begin'] = $date_begin;
    $data['date_end'] = $date_end;

    $out['data'] = $data ;
    $out['where'] = $where;
    $out['ext_page'] = $ext_page;
    $out['ext'] = $ext;


    return $out;
  }



  /*------ calculate_LandPrice ---------*/
  function calculate_LandPrice($info){
    global $vnT ;
    $out = 0;

    $out = ($info['price']-$info['price_build']) ;
    if($info['total_area_used']) {
      $out =  ($out / $info['total_area_used']) ;
    }

    if($info['price_advantage_defect']) {
      $out =  $out + $info['price_advantage_defect'];
    }

    $out =  floor($out/1000)*1000 ; // lam tron

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
          $arr_price  = (isset($vnT->input["txtPrice"])) ? $vnT->input["txtPrice"] : array();
          $arr_total_area_used  = (isset($vnT->input["txt_total_area_used"])) ? $vnT->input["txt_total_area_used"] : array();
          $arr_price_build  = (isset($vnT->input["txt_price_build"])) ? $vnT->input["txt_price_build"] : array();
          $arr_price_advantage_defect  = (isset($vnT->input["txt_price_advantage_defect"])) ? $vnT->input["txt_price_advantage_defect"] : array();


          $mess .= "- " . $vnT->lang['edit_success'] . " ID: <strong>";
          $str_mess = "";
          for ($i = 0; $i < count($h_id); $i ++)
          {


            $dup = array();
            $dup['price'] = str_replace(array(",", "."), "", $arr_price[$h_id[$i]]);
            $dup['total_area_used'] = $arr_total_area_used[$h_id[$i]];
            $dup['price_build'] = str_replace(array(",", "."), "", $arr_price_build[$h_id[$i]]);
            $dup['price_advantage_defect'] = str_replace(array(",", "."), "", $arr_price_advantage_defect[$h_id[$i]]);
            $land_price = $this->calculate_LandPrice($dup);

            $dup['land_price'] = $land_price;
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
      }
    }


    $out['ok'] = $ok_up  ;
    $out['err'] = $err ;
    return $out;
  }



}
$model = new Model();
?>