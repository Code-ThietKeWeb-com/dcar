<?php
/*
if(empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest') {
	die('Hacking attempt!');
}*/
@ini_set("display_errors", "0");
session_start();
define('IN_vnT', 1);
define('PATH_ROOT', dirname(__FILE__));
define('DS', DIRECTORY_SEPARATOR);
require_once("_config.php");
require_once (PATH_ROOT . DS . 'includes' . DS . 'defines.php');
require_once("includes/class_db.php");
require_once("includes/class_functions.php");
require_once ('includes/class_global.php');
// initialize the data registry
$vnT = new vnT_Registry();
$DB = $vnT->DB ;
$func = $vnT->func ;
$conf = $vnT->conf ;

require_once("includes/JSON.php");
$vnT->user = array();
$vnT->user['mem_id'] =0;
$arr_cookie_member = explode("|",$_COOKIE[MEMBER_COOKIE]);
$res_mem = $DB->query("SELECT mem_id,password,username,avatar,email,phone FROM members WHERE username='".$arr_cookie_member[0]."' ");
if($row_mem = $DB->fetch_row($res_mem))
{
  $mem_hash = md5($row_mem['mem_id'] . '|' . $row_mem['password']);
  if($arr_cookie_member[1] ==$mem_hash )	{
    $vnT->user  = $row_mem  ;
  }
}


/*echo "<pre>";
print_r($vnT->user);
echo "</pre>";
*/
$vnT->lang_name = (isset($_POST['lang'])) ? $_POST['lang']  : "vn" ;
$func->load_language('global');

switch ($_GET['do'])
{
  case "option_city" : $jsout = get_option_city() ;break;
  case "list_city" : $jsout = get_list_city() ;break;
  case "option_state" : $jsout = get_option_state() ;break;
  case "list_state" : $jsout = get_list_state() ;break;
  case "option_ward" : $jsout = get_option_ward() ;break;
  case "option_street" : $jsout = get_option_street() ;break;


  case "comment_list" : $jsout = do_comment_list(); break;
  case "comment_post" : $jsout = do_comment_post(); break;

  case "regMaillist" : $jsout = do_regMaillist() ; break;
  case "check_sec_code" : $jsout = do_checkSecCode() ; break;
  case "ajax_login" : $jsout = do_ajax_login() ; break;
  case "ajax_logout" : $jsout = do_ajax_logout() ; break;
  case "statistics" : $jsout = do_Statistics() ; break;
  case "popupBanner" : $jsout = get_popupBanner() ;break;
  case "auto_facebook" : $jsout = do_auto_facebook() ;break;



  default :  $jsout ="Error" ; break;
}


//get_list_city
function get_list_city() {
  global $DB,$func,$conf,$vnT;
  $textout="";

  $country = $_POST['country'];
  $selname = ($_POST['selname']) ? $_POST['selname'] : "city";
  $ext = ($_POST['ext']) ? $_POST['ext'] : "";

  $sql="SELECT * FROM iso_cities where display=1 and country='$country'  order by c_order ASC , name ASC  ";
  //	echo $sql;
  $result = $DB->query ($sql);
  if($num = $DB->num_rows($result))
  {
    $textout= "<select name=\"{$selname}\" id=\"{$selname}\" class='select form-control'  {$ext} >";
    $textout.="<option value=\"0\" selected>".$vnT->lang['global']['select_city']."</option>";
    while ($row = $DB->fetch_row($result)){
      $textout .= "<option value=\"{$row['id']}\"  >".$func->HTML($row['name'])."</option>";
    }
    $textout.="</select>";
  }else{
    $textout  ="<input type='text' value='' class='textfiled form-control' name='{$selname}' id='{$selname}' {$ext} />";
  }

  return $textout;
}

//get_list_state
function get_list_state() {
  global $DB,$func,$conf,$vnT;
  $textout="";
  $ext="";
  $city = $_POST['city'];

  $sql="SELECT * FROM iso_states where display=1 and city=".$city."  order by s_order ASC , name ASC  ";
  // echo $sql;
  $result = $DB->query ($sql);
  if($num = $DB->num_rows($result))
  {
    $textout= "<select name=\"state\" id=\"state\" class='select form-control'  {$ext} >";
    $textout.="<option value=\"0\" selected>".$vnT->lang['global']['select_state']."</option>";
    while ($row = $DB->fetch_row($result)){
      if ($row['id']==$did){
        $textout .= "<option value=\"{$row['id']}\" selected>".$func->HTML($row['name'])."</option>";
      } else{
        $textout .= "<option value=\"{$row['id']}\">".$func->HTML($row['name'])."</option>";
      }
    }
    $textout.="</select>";
  }else{
    $textout  ="<input type='text'  value='".$did."' class='textfiled form-control' name='state' id='state' {$ext} />";
  }

  return $textout;
}

//get_option_city
function get_option_city()
{
  global $DB, $func, $conf, $vnT;

  $text = "";
  $country =  $_REQUEST['country'];
  $city = (int)$_REQUEST['city'];

  $text .= "<option value='' >" . $vnT->lang['global']['select_city'] . "</option>";

  $sql = "SELECT * FROM iso_cities where display=1 and country='".$country."'  order by c_order ASC , name ASC  ";
  //	echo $sql;
  $result = $DB->query($sql);
  if ($num = $DB->num_rows($result)) {
    while ($row = $DB->fetch_row($result)) {
      $name = ($vnT->lang_name=="en") ? $func->HTML($row['name_en']) : $func->HTML($row['name']);
      $selected = ($row['id'] == $city) ? "selected" : "";
      $text .= "<option value=\"{$row['id']}\" {$selected} >" . $name . "</option>";
    }
  }

  return $text;
}

//get_option_state
function get_option_state() {
  global $DB,$func,$conf,$vnT;

  $city = (int)$_REQUEST['city'];
  $state = (int)$_REQUEST['state'];
  $arr_json = array();


  $text  ="<option value='' >" . $vnT->lang['global']['select_state'] . "</option>";
  $sql="SELECT * FROM iso_states where display=1 and city=".$city."  order by s_order ASC , name ASC  ";
  //	echo $sql;
  $result = $DB->query ($sql);
  if($num = $DB->num_rows($result))
  {
    while ($row = $DB->fetch_row($result)) {
      $name = ($vnT->lang_name=="en") ? $func->HTML($row['name_en']) : $func->HTML($row['name']);
      $selected = ($row['id'] == $state) ? "selected" : "";
      $text .= "<option value=\"{$row['id']}\" {$selected} >" . $name . "</option>";
    }
  }

  return $text;
}

//get_option_ward
function get_option_ward()
{
  global $DB, $func, $conf, $vnT;
  $textout = "";
  $state = (int)$_REQUEST['state'];

  $textout .= "<option value='' >" . $vnT->lang['global']['select_ward'] . "</option>";
  $sql = "SELECT * FROM iso_wards where display=1 and state=" . $state . "  order by  w_order ASC , name ASC  ";
  $result = $vnT->DB->query($sql);
  while ($row = $vnT->DB->fetch_row($result)) {
    $name = ($vnT->lang_name=="en") ? $func->HTML($row['name_en']) : $func->HTML($row['name']);
    $textout .= "<option value=\"{$row['id']}\">" . $name . "</option>";
  }

  return $textout;
}

//get_street_ward
function get_option_street()
{
  global $DB, $func, $conf, $vnT;
  $textout = "";
  $state = (int)$_REQUEST['state'];

  $textout .= "<option value='' selected>" . $vnT->lang['global']['select_street'] . "</option>";
  $sql = "SELECT * FROM iso_street where display=1 and state=" . $state . "  order by  s_order ASC , name ASC  ";
  $result = $vnT->DB->query($sql);
  while ($row = $vnT->DB->fetch_row($result)) {
    $textout .= "<option value=\"{$row['id']}\">" . $vnT->func->HTML($row['name']) . "</option>";
  }

  return $textout;
}




/**
 * function do_comment_list ()
 *
 **/
function do_comment_list()
{
  global  $vnT;
  $arr_json = array();

  $p = ((int)$_POST['p']) ? $_POST['p'] : 1;
  $id = (int)$_POST['com_id'];
  $n = ((int)$_POST['num']) ? $_POST['num'] : 5;
  $mod = $_POST['com_mod'];
  $tbl_comment = $mod . "_comment";
  $ok = 0;
  $sql_num = "select * from " . $tbl_comment . " where item_id=$id and display=1 ";
  $res_num = $vnT->DB->query($sql_num);
  $totals = $vnT->DB->num_rows($res_num);
  $vnT->DB->free_result($res_num);


  $num_pages = ceil($totals / $n);
  if ($p > $num_pages) $p = $num_pages;
  if ($p < 1) $p = 1;
  $start = ($p - 1) * $n;
  $object = "vnTcomment.show_comment({$id},";
  if ($num_pages > 1)
    $nav = "<div class=\"pagination\">" . $vnT->func->paginate_js($totals, $n, $p, $object) . "</div>";

  $sql = "select * from " . $tbl_comment . " where  item_id=$id AND parentid=0 and display=1 order by cid DESC LIMIT $start,$n";
  $result = $vnT->DB->query($sql);
  if ($num = $vnT->DB->num_rows($result))
  {
    $ok =1 ;
    while ($row = $vnT->DB->fetch_row($result))
    {
      $cid = $row['cid'];

      $src_avatar = ($row['avatar']) ? $vnT->conf['rooturl'] . 'vnt_upload/member/avatar/' . $row['avatar'] : $vnT->dir_images.'/avatar.png';
      $date_post =  @date("H:i - d/m/Y", $row['date_post']);
      $email = ($row['hidden_email'] == 0) ? ' <span class="email">(' . $row['email'] . ')</span>' : "";



      //load reply
      $list_answer ='';
      $res_sub = $vnT->DB->query("SELECT * FROM " . $tbl_comment . " WHERE parentid=" . $cid . " AND display=1 ORDER BY date_post DESC ");
      if ($num_sub = $vnT->DB->num_rows($res_sub)) {
        $list_answer ='<div class="listanswer">';
        while ($row_sub = $vnT->DB->fetch_row($res_sub)) {

          $sub_src_avatar = ($row_sub['avatar']) ? $vnT->conf['rooturl'] . 'vnt_upload/member/avatar/' . $row_sub['avatar'] :  $vnT->dir_images . '/avatar.png';
          $sub_date_post = $vnT->lang['global']['post_at'] . ' ' . @date("H:i - d/m/Y", $row_sub['date_post']);
          $sub_name = $vnT->func->HTML($row_sub['name']);
          $sub_content = $vnT->func->HTML($row_sub['content']);

          $text_reply = '<div class="nodeanswer">';
          $text_reply .= '<div class="avatar"><img src="' . $sub_src_avatar . '" alt="' . $row_sub['name'] . '" /></div>';
          $text_reply .= '<div class="info-comment">';
          $text_reply .= '<div class="info-preson"><span class="name">' . $sub_name . '</span>  -  <span class="time">' . $sub_date_post . '</span></div>';
          $text_reply .= '<div class="ccomment">' . $sub_content . '</div>';
          $text_reply .= '</div>';
          $text_reply .= '</div>';

          $list_answer .= $text_reply;
        }

        $list_answer .= '</div>';
      }



      $item = array();
      $item['cid'] = $cid;
      $item['item_id'] = $row['item_id'] ;
      $item['title'] =  $vnT->func->HTML($row['title']);
      $item['content'] = $vnT->func->HTML($row['content']);
      $item['src_avatar'] = $src_avatar ;
      $item['name'] =  $vnT->func->HTML($row['name']);
      $item['email'] = $email;
      $item['date_post'] = $date_post;
      $item['list_answer'] = $list_answer;
      $items[] = $item ;

    }
  }

  $vnT->DB->free_result($result);
  $arr_json['ok'] = $ok;
  $arr_json['nav'] = $nav;
  $arr_json['items'] = $items;
  if($vnT->conf['debug']){
    $arr_json['sql'] = $sql;
  }
  $json = new Services_JSON( );
  $textout = $json->encode($arr_json);
  return $textout;
}

/**
 * function do_comment_post ()
 *
 **/
function do_comment_post()
{
  global $DB, $func, $conf, $vnT;

  $arr_json = array();
  $mod = $_POST['com_mod'];
  $id = (int)$_POST['com_id'];
  $display = (isset($vnT->conf['comment_display'])) ? $vnT->conf['comment_display'] : 0;
  $ok_post = 1;

  //xu ly post lien tiep
  $sec_limit = 5;
  $time_post = time() - $sec_limit;
  if (isset($_SESSION['last_post'])) {
    $sec = $_SESSION['last_post'] - $time_post;
    if ($sec > 0) {
      $ok_post = 0;
      $arr_json['ok'] = 0;
      $arr_json['mess'] = str_replace("{sec}", $sec, $vnT->lang['global']['err_time_post_comment']);
    }
  }

  if ($ok_post) {
    $cot['item_id'] = $id;
    $cot['item_title'] = $vnT->func->txt_HTML($_POST['com_title']);
    $cot['name'] = $vnT->func->txt_HTML($_POST['com_name']);
    $cot['email'] = $_POST['com_email'];
    $cot['hidden_email'] = $_POST['h_email'];
    $cot['content'] = $vnT->func->txt_HTML($_POST['com_content']);
    $cot['display'] = $display;
    $cot['mem_id'] = $vnT->user['mem_id'];
    $cot['avatar'] = $vnT->user['avatar'];
    $cot['date_post'] = time();
    $ok = $vnT->DB->do_insert($mod . "_comment", $cot);
    if ($ok) {
      $cid = $vnT->DB->insertid();
      $_SESSION['last_post'] = time();
      $arr_json['ok'] = 1;
      $arr_json['display'] = $display;
      $arr_json['mess'] =   $vnT->lang['global']['send_comment_success']  ;

      if($display==1){

        $src_avatar = ($cot['avatar']) ? $vnT->conf['rooturl'] . 'vnt_upload/member/avatar/' . $cot['avatar'] : $vnT->dir_images.'/avatar.png';
        $date_post =  @date("H:i - d/m/Y", $cot['date_post']);
        $email = ($cot['hidden_email'] == 0) ? ' <span class="email">(' . $cot['email'] . ')</span>' : "";

        $item = array();
        $item['cid'] = $cid;
        $item['item_id'] = $cot['item_id'] ;
        $item['title'] =  $vnT->func->HTML($cot['title']);
        $item['content'] = $vnT->func->HTML($cot['content']);
        $item['src_avatar'] = $src_avatar ;
        $item['name'] =  $vnT->func->HTML($cot['name']);
        $item['email'] = $email;
        $item['date_post'] = $date_post;
        $item['list_answer'] = '';

        $arr_json['item'] = $item;
      }
    } else {
      $arr_json['ok'] = 0;
      $arr_json['mess'] = "Error Database" . $vnT->DB->debug();
    }
  }


  $json = new Services_JSON();
  $textout = $json->encode($arr_json);

  return $textout;
}

/**
 * function do_Like ()
 **/
function do_Like ()
{
  global $vnT, $input;
  $arr_json = array();

  $ok = 0 ;
  $mess = '';
  $mem_id = (int)$vnT->user['mem_id'];
  $mod = $_POST['mod'];
  $mod_id = $_POST['mod_id'];
  $id = (int)$_POST['id'];
  $like = (int)$_POST['like'];
  $ok_post = 1;

  if($_SESSION['vnt_csrf_token']!=$_POST['csrf_token']) {
    $ok_post  = 0;
    $mess = $vnT->lang['global']['err_csrf_token'] ;
  }
  if($mem_id==0)
  {
    $ok_post  = 0;
    $mess =  $vnT->lang['global']['error_only_member'];
  }

  if($ok_post)
  {

    $res_ck = $vnT->DB->query("SELECT {$mod_id}, num_like, list_like FROM ".$mod." WHERE {$mod_id}=".$id);
    if($row_ck = $vnT->DB->fetch_row($res_ck))
    {
      $arr_old = @explode(",",$row_ck['list_like']) ;
      $num_like = $row_ck['num_like']  ;
      $list_like =  $row_ck['list_like']  ;

      $dup = array();

      if($like==1)
      {
        if (!in_array($mem_id,$arr_old))
        {
          $num_like = $row_ck['num_like']+1 ;
          $list_like = ($row_ck['list_like']) ? $row_ck['list_like'].",".$mem_id : $mem_id ;
        }
        $mess = $vnT->lang['global']['mess_like_success'];
      }else{
        if (in_array($mem_id,$arr_old))
        {
          $num_like = ($row_ck['num_like']>1) ? $row_ck['num_like'] - 1 : 0 ;
          if (($key = array_search($mem_id, $arr_old)) !== false) {
            unset($arr_old[$key]);
          }
          $list_like = (count($arr_old) > 0) ?  @implode(",",$arr_old) : '';
        }
        $mess = $vnT->lang['global']['mess_dislike_success'];
      }

      $dup['num_like'] = $num_like ;
      $dup['list_like'] = $list_like ;
      $vnT->DB->do_update($mod,$dup,"{$mod_id} =".$id);
      $ok = 1;
      $arr_json['num_like'] = $num_like ;
    }else{
      $mess = $vnT->lang['global']['not_found'];
    }

  }


  $arr_json['ok'] = $ok ;
  $arr_json['mess'] = $mess ;
  $json = new Services_JSON( );
  $textout = $json->encode($arr_json);

  return $textout;
}


//do_regMaillist
function do_regMaillist()
{
  global $DB, $func, $conf, $vnT;
  $arr_json = array();
  $email = $_POST['email'];
  $name = $_POST['name'];
  $ok = 1;

  $res = $DB->query("SELECT id FROM listmail WHERE  email='$email' ");
  if (!$row = $DB->fetch_row($res)) {
    $cot['email'] = $email;
    $cot['cat_id'] = ($vnT->user['mem_id']) ? 1 : 2;
    $cot['name'] = ($name) ? $name : "Khach hang";
    $cot['datesubmit'] = time();
    $DB->do_insert("listmail", $cot);
    $mess = str_replace("{email}", $email, $vnT->lang['global']['mess_register_maillist_success']);
  } else {
    $ok =0;
    $mess = str_replace("{email}", $email, $vnT->lang['global']['mess_register_maillist_error']);
  }
  $arr_json['ok'] = $ok;
  $arr_json['mess'] = $mess;
  $json = new Services_JSON();
  $textout = $json->encode($arr_json);

  return $textout;
}


//do_checkSecCode
function do_checkSecCode() {
  global $DB,$func,$conf,$vnT;
  $textout="";
  $security_code = $_REQUEST['security_code'];
  if ($security_code == $_SESSION['sec_code']) {
    $textout =  "true";
  } else {
    $textout = "false";
  }

  return $textout;

}



//do_ajax_login
function do_ajax_login() {
  global $DB,$func,$conf,$vnT;
  $arr_json = array();
  //$email = $_POST['email'] ;
  $user = str_replace("'", "",trim($_POST['user'])) ;
  $pass = $_POST['pass'] ;
  $save = $_POST['save'] ;
  $ok = 1;
  $mess = "";

  if($user=='') {
    $ok  = 0;
    $mess= $vnT->lang['global']['err_empty']." " .$vnT->lang['global']['login_user'];
  }
  if($pass=='')  {
    $ok  = 0;
    $mess = $vnT->lang['global']['err_empty']." " .$vnT->lang['global']['login_password'];
  }

  if($_SESSION['vnt_csrf_token']!=$_POST['csrf_token']) {
    $ok  = 0;
    $mess = $vnT->lang['global']['err_csrf_token'] ;
  }

  // Check
  if($ok){
    $password = $func->md10($pass);
    $ch_remember = ($save) ? 1 : "0";

    $check_qr = $DB->query("SELECT * FROM members WHERE mem_id>0 AND (username='".$user."' OR email='".$user."'  OR phone='".$user."') AND password='".$password."' ");
    if ($info = $DB->fetch_row($check_qr))
    {

      if ($info['m_status'] == 0) {
        $ok=0;
        $mess = $vnT->lang['global']['account_not_active'];
      }

      if ($info['m_status'] == 2) {
        $ok=0;
        $mess = $vnT->lang['global']['account_ban'];
      }

    } else {
      $ok=0;
      $mess = $vnT->lang['global']['mess_login_failt'];
    }
  }

  // End check
  if ($ok==1)
  {
    //echo "mem_id".$info['mem_id'];
    $vnT->func->vnt_set_member_cookie($info['mem_id'],$ch_remember);
    $vnT->func->User_Login($info);
    $vnT->DB->query("Update members set last_login=" . time() . " , num_login=num_login+1 where mem_id=" . $info['mem_id'] . " ");
  }

  $arr_json['ok'] = $ok ;
  $arr_json['mess'] = $mess;
  $json = new Services_JSON( );
  $textout = $json->encode($arr_json);

  return $textout;
}


//do_ajax_logout
function do_ajax_logout() {
  global $DB,$func,$conf,$vnT;
  $arr_json = array();
  $time = time();
  $mem_id = $vnT->user['mem_id'];
  $vnT->DB->query("UPDATE sessions SET time='{$time}',mem_id=0 WHERE ( mem_id=".$mem_id." OR s_id='".$vnT->session->get("s_id")."' ) ");
  $vnT->func->vnt_clear_member_cookie();
  $arr_json['ok'] = 1 ;
  $json = new Services_JSON( );
  $textout = $json->encode($arr_json);

  return $textout;
}


//get_popupBanner
function get_popupBanner() {
  global $DB,$func,$conf,$vnT;
  $text="";
  $arr_json = array();
  $arr_json['show'] = 0;

  if( empty($_SESSION["show_popup"]))	{

    $res_b = $DB->query(" Select * from advertise where pos='popup' and display=1  and lang='$vnT->lang_name'   order by l_order LIMIT 0,1");
    if ($row_b = $DB->fetch_row($res_b))
    {

      $title = $func->HTML($row_b['title']);
      $popup_w = ($row_b['width']) ? $row_b['width'] : 600 ;
      $popup_h = ($row_b['height']) ? $row_b['height'] : 500 ;


      if($row_b['type_ad']==1)
      {
        $popup_banner = '<div style="padding:10px; text-align:left;">'.$row_b['img']."</div>";
      }else{
        $src = ROOT_URL . "vnt_upload/weblink/" . $row_b['img'];
        $target = ($row_b['target']) ? $row_b['target'] : "_blank";
        $l_link = $vnT->conf['rooturl'].'?vnTRUST=mod:advertise|type:advertise|lid:'.$row_b['l_id'] ;
        $popup_banner = "<a href ='{$l_link}' target='{$target}' title='{$title}' ><img  src='{$src}'  alt='{$title}' /></a>";
      }


      $text = '<div id="vnt-popup-banner" >  '.$popup_banner.'</div> ' ;

      $arr_json['show']=1;
      $arr_json['popup_w'] = $popup_w;
      $arr_json['popup_h'] = $popup_h;
      $arr_json['html'] = $text;
    }

    $_SESSION["show_popup"]=1;
  }

  $json = new Services_JSON( );
  $textout = $json->encode($arr_json);
  return $textout;
}

//do_Statistics
function do_Statistics() {
  global $DB,$func,$conf,$vnT;
  $arr_json = array();


  $thoihan = time() - 1800;
  // so online
  $get_online = $DB->query("SELECT s_id FROM sessions WHERE time >= {$thoihan} ");
  $online = (int)$DB->num_rows($get_online);
  // He he....Cai na`y goi la` an gian ^.^
  //$randnum = rand(1,5);
  if ($vnT->conf['random_online'])
  {
    $randnum = rand(1, $vnT->conf['random_online']);
  }
  $online += $randnum;
  // so truy cap
  $totals = (int) $vnT->conf['counter_default'];
  $res_totals = $DB->query("select sum(count) as totals  from counter");
  if ($row = $DB->fetch_row($res_totals))
  {
    $totals += $row['totals'];
  }

  // so thanh vien
  $query = $DB->query("SELECT s_id FROM sessions WHERE time >= {$thoihan} and mem_id<>0 ");
  $mem_online = (int)$DB->num_rows($query);

  $arr_json['totals'] = $totals;
  $arr_json['online'] = $online;
  $arr_json['mem_online'] = $mem_online;

  $json = new Services_JSON( );
  $textout = $json->encode($arr_json);

  return $textout;
}



//do_auto_facebook
function do_auto_facebook() {
  global $DB,$func,$conf,$vnT;
  $arr_json = array();
  $ok =1;
  $mess ='';
  $sub = $_GET['sub'] ;

  $res_sn = $vnT->DB->query("SELECT * FROM social_network_setting WHERE id=1");
  if($row_sn = $vnT->DB->fetch_row($res_sn))
  {
    $user_id = $row_sn['facebook_id'];
    $access_token = $row_sn['facebook_access_token'];

    $page_id = $row_sn['fanpage_id'];
    $fanpage_access_token = $row_sn['fanpage_access_token'];
  }

  if($sub=="check"){
    if(empty($user_id) && empty($page_id)){
      $ok=0;
      $mess = 'Chưa cập nhật Uer or Page ID';
    }
    if(empty($access_token) && empty($fanpage_access_token) ){
      $ok=0;
      $mess = 'Chưa cập nhật Access Token ';
    }

  }else{

    $mod = ($_POST['mod']) ? $_POST['mod'] : "news";
    $id = (int)$_POST['id'];

    switch($mod){
      case "product" :
        // $sql =" ";
        $sql = "SELECT n.p_id, n.price, n.price_old, n.date_from, n.date_to, n.picture, nd.p_name as title, nd.friendly_url, nd.metadesc FROM products n, products_desc nd WHERE n.p_id=nd.p_id AND lang='".$vnT->lang_name."' AND n.p_id=".$id;
        break;
      default :
        $sql = "SELECT n.newsid,n.picture ,nd.title,nd.friendly_url,nd.metadesc FROM news n, news_desc nd WHERE n.newsid=nd.newsid AND lang='".$vnT->lang_name."' AND n.newsid=".$id;
        break;
    }
    $res_ck = $vnT->DB->query($sql);
    if($row_ck  = $vnT->DB->fetch_row($res_ck))
    {

      $price_item = $row_ck['price'];


      $picture = ($row_ck['picture']) ? $vnT->conf['rooturl']."vnt_upload/".$mod."/".$row_ck['picture'] : '';
      $link = $vnT->conf['rooturl'].$row_ck['friendly_url'].".html";
      $caption = $vnT->func->HTML($row_ck['title']) . ' - ' . $func->format_number($price_item)." VNĐ";;
      // $description = $vnT->func->HTML($row_ck['metadesc']);
      $description = '';

      $data = array();
      $data['picture'] = $picture;
      $data['link'] = $link ;
      $data['message'] =  $caption;
      $data['caption'] = '';
      $data['description'] = $description;

      $mess = 'Đăng thành công lên Facebook với Post ID: ';
      $ok1 = 1;	 $err1 = ''; $ok2=1 ; $err2 ='';
      if($user_id && $access_token){


        $data['access_token'] = $access_token;
        $post_url = 'https://graph.facebook.com/'.$user_id.'/feed';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $post_url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $return = curl_exec($ch);
        curl_close($ch);
        $obj_value = json_decode($return,true);

        if($obj_value['id']){
          $arr_json['post_id'] = $obj_value['id'] ;
          $mess .=  $obj_value['id'].", ";
        }else{
          $ok1 = 0;
          $err1 = $obj_value['error']['message'];
        }

      }

      if($page_id && $fanpage_access_token){

        $data['access_token'] = $fanpage_access_token;
        $post_url = 'https://graph.facebook.com/'.$page_id.'/feed';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $post_url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $return = curl_exec($ch);
        curl_close($ch);
        $obj_value = json_decode($return,true);

        if($obj_value['id']){
          $arr_json['post_id'] = $obj_value['id'] ;
          $mess .=  $obj_value['id'].", ";
        }else{
          $ok2 = 0;
          $err2 = $obj_value['error']['message'];
        }

      }

      if($ok1==0 && $ok2==0) {
        $ok=0;
        $mess = $err1 ." , ".$err2;
      }


    }else{
      $ok=0;
      $mess = 'Không tìm thấy ID';
    }
  }

  $arr_json['ok'] = $ok ;
  $arr_json['mess'] = $mess ;
  $json = new Services_JSON( );
  $textout = $json->encode($arr_json);

  return $textout;
}



$vnT->DB->close();

flush();
echo $jsout;
exit();
?>