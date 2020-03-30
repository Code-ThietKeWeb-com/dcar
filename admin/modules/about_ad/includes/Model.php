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

define("MOD_NAME","about");
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
    $setting = array();
    $result = $DB->query("select * from about_setting WHERE lang='".$lang."' ");
    $setting = $DB->fetch_row($result);
    foreach ($setting as $k => $v) {
      $vnT->setting[$k] = stripslashes($v);
    }
    unset($setting);
    $vnT->setting['arr_status_register'] = array(0 =>'Đăng ký mới' , 1 => 'Đã liên lạc hỗ trợ' , 2 => 'Đã hủy bỏ');
    $vnT->setting['arr_step_customer']  = array(1 => 'Tải ứng dụng và đăng ký', 2 => 'Nạp tiền từ thẻ ATM ngân hàng nội địa', 3 => 'Nạp tiền tại 200.000 điểm giao dịch Viettel');

    $vnT->setting['arr_step_customer'] = array();
    $vnT->setting['arr_list_step_customer'] = array();
    $result = $vnT->DB->query("SELECT  *  FROM step_customer n ,step_customer_desc nd WHERE n.sid=nd.sid   and display=1 and lang='$vnT->lang_name'  order by  parentid ASC , display_order ASC , date_post ASC ");
    while ($row = $vnT->DB->fetch_row($result)) {
      $vnT->setting['arr_list_step_customer'][$row['parentid']][$row['sid']] = $row;
      $vnT->setting['arr_step_customer'][$row['sid']] = $row ;
    }
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


  //-----------------  get_picture
  function get_picture($picture, $w = "")
  {
    global $vnT, $func, $DB, $conf;
    $out = "";
    $ext = "";
    $w_thumb = ($vnT->setting['imgthumb_width']) ? $vnT->setting['imgthumb_width'] : 100;
    if ($picture) {
      $linkhinh = "../vnt_upload/".MOD_NAME."/" . $picture;
      $linkhinh = str_replace("//", "/", $linkhinh);
      $dir = substr($linkhinh, 0, strrpos($linkhinh, "/"));
      $pic_name = substr($linkhinh, strrpos($linkhinh, "/") + 1);
      $src = $dir . "/thumbs/" . $pic_name;
    }
    if ( ($w > 0) && ($w < $w_thumb)) $ext = " width='$w' ";
    $out = "<img  src=\"{$src}\" {$ext} >";
    return $out;
  }





  /*** Ham List_Option_Array ****/
  function List_Option_Array($arr_item , $did = 0 , $default = "", $default_val="")
  {
    global $vnT, $func, $DB, $conf;
    if ($did)
      $arr_selected = explode(",",$did);
    else{
      $arr_selected = array();
    }

    $text = "";
    if($default){
      $text = "<option value='".$default_val."'>".$default."</option>";
    }

    if(is_array($arr_item) && count($arr_item)>0){
      foreach ($arr_item as $key => $val) {
        $title = is_array($val) ? $val['title'] : $val ;
        $selected = in_array($key, $arr_selected)? " selected" : "";
        $text .= "<option value='".$key."' " . $selected . ">".$title."</option>";
      }
    }


    return $text;
  }



  /*** Ham List_Option_Array ****/
  function List_Parent_Customer( $did = 0 , $type_show="option")
  {
    global $vnT, $func, $DB, $conf;

    $text = "<option value=\"0\">-- Root --</option>";
    if(is_array($vnT->setting['arr_list_step_customer'][0]) && count($vnT->setting['arr_list_step_customer'][0])>0){
      foreach ($vnT->setting['arr_list_step_customer'][0] as $key => $val) {
        $title = is_array($val) ? $val['title'] : $val ;
        $selected = ($key==$did)? " selected" : "";
        $text .= "<option value='".$key."' " . $selected . ">".$title."</option>";

        $n = 1;
        $text .= $this->List_Sub_Parent_Customer($key , $did , $n);

      }
    }

    if($type_show=="select"){
      $textout = "<select size=1 id=\"parentid\" name=\"parentid\"  class='form-control'  >";

      $textout .= $text ;
      $textout .= '</select>';
    }else{
      $textout = $text ;
    }

    return $textout;
  }


  /*** Ham List_Option_Array ****/
  function List_Sub_Parent_Customer( $cid, $did = 0 , $n)
  {
    global $vnT, $func, $DB, $conf;
    $k = $n;


    $text ='';
    if(is_array($vnT->setting['arr_list_step_customer'][$cid]) && count($vnT->setting['arr_list_step_customer'][$cid])>0){
      foreach ($vnT->setting['arr_list_step_customer'][$cid] as $key => $val) {
        $title = is_array($val) ? $val['title'] : $val ;
        $selected = ($key==$did)? " selected" : "";
        $text .= "<option value='".$key."' " . $selected . ">" ;
        for ($i = 0; $i < $k; $i ++)
          $text .= "|-- ";
        $text .= $title. "</option>";

        $n = $k + 1;
        $text .= $this->List_Sub_Parent_Customer($key,$did, $n);

      }
    }

    return $text;
  }



  //======================= List_Search =======================
  function List_Search ($did)
  {
    global $func, $DB, $conf, $vnT;
    $arr_search = array(
      'aid' => "About ID" , 'title' => $vnT->lang['title'] , 'date_post' => $vnT->lang['date_post']
    );
    $text = vnT_HTML::selectbox("search", $arr_search, $did);
    return $text;
  }

  /*** Ham Get_Cat ****/
  function Get_Cat ($did = -1, $ext = "", $lang = "vn")
  {
    global $func, $DB, $conf;
    $text = "<select size=1 id=\"parentid\" name=\"parentid\" class='form-control' {$ext} >";
    $text .= "<option value=\"0\">-- Root --</option>";
    $query = $DB->query("SELECT * FROM about n,about_desc nd 
					WHERE n.aid=nd.aid AND nd.lang='$lang'
					AND parentid=0
					ORDER BY display_order ASC, date_post ASC");
    while ($cat = $DB->fetch_row($query))
    {
      $title = $func->HTML($cat['title']);
      if ($cat['aid'] == $did) $text .= "<option value=\"{$cat['aid']}\" selected>{$title}</option>";
      else
        $text .= "<option value=\"{$cat['aid']}\" >{$title}</option>";
      $n = 1;
      $text .= $this->Get_Sub($did, $cat['aid'], $n, $lang);
    }
    $text .= "</select>";
    return $text;
  }

  /*** Ham Get_Sub   */
  function Get_Sub ($did, $cid, $n, $lang)
  {
    global $func, $DB, $conf;
    //	print "SELECT * FROM about WHERE parentid={$cid} and lang='$lang' order by a_order<br>";
    $output = "";
    $k = $n;
    $query = $DB->query("SELECT * FROM about n,about_desc nd 
					WHERE n.aid=nd.aid AND nd.lang='$lang'
					AND parentid=$cid
					$where 
					ORDER BY display_order ASC, date_post ASC");
    while ($cat = $DB->fetch_row($query))
    {
      $title = $func->HTML($cat['title']);
      if ($cat['aid'] == $did)
      {
        $output .= "<option value=\"{$cat['aid']}\" selected>";
        for ($i = 0; $i < $k; $i ++)
          $output .= "|-- ";
        $output .= "{$title}</option>";
      } else
      {
        $output .= "<option value=\"{$cat['aid']}\" >";
        for ($i = 0; $i < $k; $i ++)
          $output .= "|-- ";
        $output .= "{$title}</option>";
      }
      $n = $k + 1;
      $output .= $this->Get_Sub($did, $cat['aid'], $n, $lang);
    }
    return $output;
  }

  /***** Ham List_SubCat *****/
  function List_SubCat ($cat_id)
  {
    global $func, $DB, $conf;
    $output = "";
    $query = $DB->query("SELECT * FROM about WHERE parentid={$cat_id}");
    while ($cat = $DB->fetch_row($query))
    {
      $output .= $cat["aid"] . ",";
      $output .= $this->List_SubCat($cat['aid']);
    }
    return $output;
  }


  /*------ get_format_time_by_string ---------*/
  function get_format_time_by_string($text){
    global $vnT ;
    $out = '' ;
    if($text){
      $tmp = @explode(" - ",$text);
      $ngay =  explode("/", trim($tmp[0]));
      $gio =  explode(":", trim($tmp[1]));

      $out = mktime($gio[0], $gio[1], 0, $ngay[1], $ngay[0], $ngay[2]);
    }
    return $out;
  }

  function get_code_img ($url_youtube)
  {
    global $conf, $func, $vnT;

    $code_img = str_replace("http://www.youtube.com/watch?", "", $url_youtube);
    $code_img = str_replace("https://www.youtube.com/watch?", "", $code_img);
    $code_img = str_replace("//www.youtube.com/watch?", "", $code_img);
    $tmp = explode("&",$code_img);
    foreach($tmp as $vk)
    {
      $tmp_1 = explode("=",$vk);
      $code_img = ($tmp_1[0] == "v") ? $tmp_1[1] : $code_img;
    }

    return $code_img;
  }

//================== get_img_youtube =============
  function get_img_youtube ($str, $dir, $type="embed")
  {
    global $conf, $func, $vnT;
    $text = "";

    if($type == "url_youtube")
    {
      $code_img = str_replace("http://www.youtube.com/watch?v=", "", $str);
      $code_img = str_replace("https://www.youtube.com/watch?v=", "", $code_img);
      $code_img = str_replace("//www.youtube.com/watch?v=", "", $code_img);
      $tmp = explode("&",$code_img);
      $code_img = ($tmp[0]) ? $tmp[0] : $code_img;

      $text = save_img_youtube ($code_img, $dir);

      return $text;
    }

    $tmp = $str;
    $str = str_replace(array('\"',"\'","\&quot;"),'"',$str);

    preg_match_all("/ src=['|\"](.*?)['|\"]/", $str, $arr_src);

    foreach($arr_src[1] as $key => $value)
    {
      $code_img = $value;
      if($type == "embed")
      {
        $code_img = str_replace("https://www.youtube-nocookie.com/embed/", "", $code_img);
        $code_img = str_replace("http://www.youtube-nocookie.com/embed/", "", $code_img);
        $code_img = str_replace("//www.youtube-nocookie.com/embed/", "", $code_img);
        $code_img = str_replace("https://www.youtube.com/embed/", "", $code_img);
        $code_img = str_replace("http://www.youtube.com/embed/", "", $code_img);
        $code_img = str_replace("//www.youtube.com/embed/", "", $code_img);

        $tmp = explode("?",$code_img);
        $code_img = ($tmp[0]) ? $tmp[0] : $code_img;
        //$code_img = str_replace("?rel=0", "", $code_img);
      }

      if($code_img)
      {
        $text = save_img_youtube ($code_img, $dir);
        if ($text) {
          return $text;
          break;
        }
      }
    }
    return $text;
  }

//================== save_img_youtube =============
  function save_img_youtube ($code_img="", $dir="")
  {
    global $conf, $func, $vnT;
    $text = "";

    if ($dir) {
      $path_dir = MOD_DIR_UPLOAD . $dir . "/";
      $rooturl = str_replace('http://' . $_SERVER['HTTP_HOST'], "", $conf['rooturl']) . ROOT_UPLOAD . $dir . "/";
    } else {
      $path_dir = MOD_DIR_UPLOAD;
      $rooturl = str_replace('http://' . $_SERVER['HTTP_HOST'], "", $conf['rooturl']) . ROOT_UPLOAD;
    }
    $path_thumb = $path_dir . "thumbs";
    if (! is_dir($path_thumb)) {
      @mkdir($path_thumb, 0777);
      @exec("chmod 777 {$path_thumb}");
    }

    if($code_img)
    {
      $img_http = "http://img.youtube.com/vi/".$code_img."/0.jpg";
      $file_name = time().".jpg";
      $fname = $path_dir . $file_name;
      if (file_exists($fname)) {
        $fname = $path_dir . time() . "_" . $file_name;
      }

      $file = @fopen($fname, "w");
      if ($f = @fopen($img_http, "r")) {
        while (! @feof($f)) {
          @fwrite($file, fread($f, 1024));
        }
        @fclose($f);
        @fclose($file);
        $url = $rooturl . $file_name;

        if($dir)
        {
          $text = $dir."/".$file_name;
        }
        else
        {
          $text = $file_name;
        }
      }
    }
    return $text;
  }

  //================== make_iframe_youtube =============
  function make_iframe_youtube ($code_img, $op_view_video=array(), $media_w=700, $media_h=525)
  {
    global $conf, $func, $vnT;

    $ext = '';
    $attr = '';
    if(is_array($op_view_video)){
      foreach($op_view_video as $k => $v)
      {
        $is_ext = 1;

        if($k == "allowfullscreen")
        {
          $is_ext = 0;
        }

        if($is_ext == 1)
        {
          if($k == "start")
          {
            $tmp = explode(":",$v);
            $v = $tmp[0]*60*60+$tmp[1]*60+$tmp[2];
          }
          $ext .= ($ext) ?  '&' : '?';
          $ext .= $k.'='.$v;
        }
        else
        {
          $attr .= ' '.$k;
        }
      }
    }


    $text = '&lt;iframe width=\&quot;'.$media_w.'\&quot; height=\&quot;'.$media_h.'\&quot; src=\&quot;http://www.youtube.com/embed/'.$code_img.$ext.'\&quot; frameborder=\&quot;0\&quot; '.$attr.'&gt;&lt;/iframe&gt;';

    return $text;
  }



  /*-------------- buildInfoItem --------------------*/
  function buildInfoItem( )
  {
    global $vnT , $func, $DB, $conf ;
    $out = array();

    $cat_id = $vnT->input['cat_id'];
    $maso = trim($vnT->input['maso']);
    $p_name = $vnT->func->txt_HTML($_POST['p_name']);

    $picture =  $vnT->input['picture'];
    $gio = explode(":", $vnT->input['gio']);
    $ngay = explode("/", $vnT->input['ngay']);
    $date_post = mktime($gio[0], $gio[1], 0, $ngay[1], $ngay[0], $ngay[2]);
    $options = $vnT->input['options'];

    //cot
    $out['cat_id'] = $cat_id;
    $out['maso'] = $maso;
    $out['picture'] = $picture;
    $out['date_post'] = $date_post;

    $desc['p_name'] = $p_name;
    $desc['area']  = $vnT->func->txt_HTML($_POST['area']);
    $desc['bedroom']  = $vnT->func->txt_HTML($_POST['bedroom']);
    $desc['num_person']  = $vnT->func->txt_HTML($_POST['num_person']);

    $desc['short'] =   $vnT->func->txt_HTML($_POST['short']);
    $desc['description'] = $DB->mySQLSafe($_POST['description']);
    $desc['options'] = $options;
    $desc['key_search'] =  strtolower($func->utf8_to_ascii($p_name)) ;
    $desc['display'] = $vnT->input['display'];

    //SEO
    $desc['friendly_url'] = (trim($vnT->input['friendly_url'])) ? trim($vnT->input['friendly_url']) :  $func->make_url($p_name);
    $desc['friendly_title'] = (trim($vnT->input['friendly_title'])) ? trim($vnT->input['friendly_title']) :  $func->utf8_to_ascii($p_name);
    $desc['metakey'] = (trim($vnT->input['metakey'])) ? trim($vnT->input['metakey']) :  $p_name ;
    $desc['metadesc'] = (trim($vnT->input['metadesc'])) ? trim($vnT->input['metadesc']) :  $func->cut_string($func->check_html($_POST['short'],'nohtml'),200,1) ;
    $desc['meta_extra'] = $_POST['meta_extra'];
    $desc['extra_header'] = $_POST['extra_header'];
    $desc['extra_footer'] = $_POST['extra_footer'];

    $out['desc'] = $desc ;


    $out['desc'] = $desc ;

    return $out;
  }



  //------load_more_data
  function load_more_data($data,$lang="vn")
  {
    global $vnT , $input;
    $out = array();



    return $out ;
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

    $cat_id = ((int) $vnT->input['cat_id']) ?   $vnT->input['cat_id'] : 0;

    $search = ($vnT->input['search']) ?  $vnT->input['search'] : "title";
    $keyword = ($vnT->input['keyword']) ?  $vnT->input['keyword'] : "";
    $date_begin = ($vnT->input['date_begin']) ?  $vnT->input['date_begin'] : "";
    $date_end = ($vnT->input['date_end']) ?  $vnT->input['date_end'] : "";

    $where ="  ";
    $ext_page='';
    $ext='';


    if(!empty($cat_id)){
      $where .=" and cat_id=".$cat_id;
      $ext_page .="cat_id=$cat_id|";
      $ext.="&cat_id=$cat_id";
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



    $data["cat_id"] = $cat_id;
    $data["search"] = $search;
    $data["keyword"] = $keyword;
    $data['list_search']=$this->List_Search($search,"option");


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