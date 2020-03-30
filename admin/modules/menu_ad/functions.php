<?php
/*================================================================================*\
|| 							Name code : funtions.php 		 			      	         		  # ||
||  				Copyright © 2007 by Thai Son - CMS vnTRUST                          # ||
\*================================================================================*/
/**
 * @version : 7.0
 * @date upgrade : 21/04/2017 by Thai Son
 **/
 
if (!defined('IN_vnT')) {
  die('Hacking attempt!');
}
define('MOD_DIR_UPLOAD', '../vnt_upload/menu/');
define('ROOT_UPLOAD', 'vnt_upload/menu');
define('MOD_ROOT_URL', $conf['rooturl'] . 'modules/menu/');


class Model
{
  function __construct()
  {
    $lang = ($_GET['lang']) ? $_GET['lang'] : "vn";
    $this->loadSetting($lang);
  }

  /*-------------- loadSetting --------------------*/
  function loadSetting($lang = "vn")
  {
    global $vnT ;
    $vnT->setting['arr_target'] =  array("_self"=>"Tại trang (_self)","_blank"=>"Cửa sổ mới (_blank)","_parent"=>"Cửa sổ cha (_parent)","_top"=>"Cửa sổ trên cùng (_top)") ;

    $vnT->setting['arr_pos'] = array(
      'horizontal' => $vnT->lang['horizontal_menu'] ,
      'header' => "Menu Top Right" ,
      'footer' => $vnT->lang['footer_menu'] ,
      'footer_key' => 'Top tìm kiếm',
    );
  }

  // Ham del submenu
  function del_submenu ($cid)
  {
    global $vnT , $DB, $func ;
    $query = $DB->query("SELECT * FROM menu WHERE parentid={$cid} order by menu_order");
    while ($row = $DB->fetch_row($query))
    {
      $this->del_submenu($row['menu_id']);
    }
    $DB->query("DELETE FROM menu WHERE menu_id=" . $cid);
    $DB->query("DELETE FROM menu_desc WHERE menu_id=" . $cid);
  }



  /*-------------- List_Parent --------------------*/
  function List_Parent ($pos, $did = -1, $lang = "vn", $ext = "")
  {
    global $func, $DB, $conf;
    $where = " AND pos='$pos' AND lang='$lang' "  ;
    $text = "<select size=1 id=\"parentid\" name=\"parentid\" class='select' {$ext} >";
    $text .= "<option value=\"0\">-- ROOT --</option>";
    $query = $DB->query("SELECT  n.menu_id,nd.title FROM menu n ,menu_desc nd WHERE n.menu_id=nd.menu_id AND parentid=0 $where ORDER BY pos ASC, menu_order ASC , n.menu_id ASC");
    while ($row = $DB->fetch_row($query))
    {
      $title = $func->HTML($row['title']);
      if ($row['menu_id'] == $did) $text .= "<option value=\"{$row['menu_id']}\" selected>{$title}</option>";
      else
        $text .= "<option value=\"{$row['menu_id']}\" >{$title}</option>";
      $n = 1;
      $text .= $this->Get_Sub($row['menu_id'], $n, $did, $lang);
    }
    $text .= "</select>";
    return $text;
  }

  /*-------------- List_Target --------------------*/
  function Get_Sub ($cid, $n, $did = -1, $lang)
  {
    global $func, $DB, $conf;
    $output = "";
    $k = $n;
    $query = $DB->query("SELECT n.menu_id,nd.title FROM menu n ,menu_desc nd WHERE n.menu_id=nd.menu_id AND parentid={$cid} AND lang='$lang' ORDER BY pos ASC, menu_order ASC , n.menu_id ASC");
    while ($row = $DB->fetch_row($query))
    {
      $title = $func->HTML($row['title']);
      if ($row['menu_id'] == $did)
      {
        $output .= "<option value=\"{$row['menu_id']}\" selected>";
        for ($i = 0; $i < $k; $i ++)
          $output .= "|--";
        $output .= "{$title}</option>";
      } else
      {
        $output .= "<option value=\"{$row['menu_id']}\" >";
        for ($i = 0; $i < $k; $i ++)
          $output .= "|--";
        $output .= "{$title}</option>";
      }
      $n = $k + 1;
      $output .= $this->Get_Sub($row['menu_id'], $n, $did, $lang);
    }
    return $output;
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

  /*-------------- List_Pos --------------------*/
  function List_Pos ($did = 0, $type_show = "option")
  {
    global $vnT;

    $text ='';
    $text .= "<option value=\"\" selected> " . $vnT->lang['select_position'] . " </option>";
    foreach ($vnT->setting['arr_pos'] as $key => $value)
    {
      $selected = ($key == $did) ? "selected" : "";
      $text .= "<option value=\"{$key}\" {$selected} > " . $value . " </option>";
    }

    if($type_show=="option"){
      $textout = $text;
    }else{
      $textout =  "<select size=1 name=\"pos\" id=\"pos\" class='select'   >";
      $textout .= $text;
      $textout .= '</select>';
    }

    return $textout;
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
    $pos = ($vnT->input['pos']) ?  $vnT->input['pos'] : "horizontal";
    $where ="  ";
    $ext_page='';
    $ext='';

    if($pos){
      $where .= " AND pos='".$pos."' ";
    }


    $data['list_pos'] = $this->List_Pos($pos ,"option");

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
      }else{
        unset($_SESSION['vnt_csrf_token']);

        //xoa cache
        $func->clear_cache();
        $mess ='';
        if ($vnT->input["del_id"]) $h_id = $vnT->input["del_id"];
        switch ($vnT->input["do_action"])
        {
          case "do_edit":
            $arr_order  = (isset($vnT->input["txt_Order"])) ? $vnT->input["txt_Order"] : array();

            $mess .= "- " . $vnT->lang['edit_success'] . " ID: <strong>";
            $str_mess = "";
            for ($i = 0; $i < count($h_id); $i ++)
            {
              $dup = array();
              $dup['menu_order'] = $arr_order[$h_id[$i]];

              $ok = $DB->do_update( "menu" , $dup, "menu_id=" . $h_id[$i]);
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
              $ok = $DB->do_update("menu_desc", $dup, "lang='".$lang."' AND menu_id=" . $h_id[$i]);
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
              $ok = $DB->do_update("menu_desc", $dup, "lang='".$lang."' AND menu_id=" . $h_id[$i]);
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

        $ok = $DB->query("Update menu_desc SET display=1 WHERE lang='".$lang."' AND menu_id=".$vnT->input["do_display"]);
        if($ok){
          $mess .= "- " . $vnT->lang['display_success'] . "  ID: <strong>".$vnT->input["do_display"] . "</strong><br>";
          $err = $func->html_mess($mess);
          $ok_up = 1;
        }
        //xoa cache
        $func->clear_cache();
      }

    }

    if((int)$vnT->input["do_hidden"]){
      if(empty($vnT->input['csrf_token']) || ($vnT->input['csrf_token'] != $_SESSION['vnt_csrf_token']) ) {
        $err =  $func->html_err($vnT->lang['err_csrf_token']) ;
      }else{
        unset($_SESSION['vnt_csrf_token']);

        $ok = $DB->query("Update menu_desc SET display=0 WHERE lang='".$lang."' AND menu_id=".$vnT->input["do_hidden"]);
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