<?php
/*================================================================================*\
|| 							Name code : tour.php 		 		            	  ||
||  				Copyright @2008 by Thai Son - CMS vnTRUST                     ||
\*================================================================================*/
/**
 * @version : 2.0
 * @date upgrade : 09/01/2009 by Thai Son
 **/
if (! defined('IN_vnT'))
{
  die('Hacking attempt!');
}

//load Model
include_once dirname( __FILE__ ) . '/includes/Model.php';

class vntModule extends Model
{
  var $output = "";
  var $skin = "";
  var $linkUrl = "";
  var $module = MOD_NAME;
  var $action = "config";

  /**
   * function vntModule ()
   * Khoi tao
   **/
  function vntModule ()
  {
    global $Template, $vnT, $func, $DB, $conf;

    //load skin
    $this->loadSkinModule($this->action);

    $lang = ($vnT->input['lang']) ? $lang = $vnT->input['lang'] : $func->get_lang_default();
    $this->linkUrl = "?mod=" . $this->module . "&act=" . $this->action . "&lang=" . $lang;

    $vnT->html->addStyleSheet("modules/" . $this->module . "_ad/css/" . $this->module . ".css");
    $vnT->html->addScript("modules/" . $this->module . "_ad" . "/js/" . $this->module . ".js");

    //load QrCode
    $func->include_libraries('qrcode.qrcode');
    $vnT->qrcode = new QrCodes;

    switch ($vnT->input['sub']) {
      case 'add':
        $nd['f_title'] = $vnT->lang['add_config'];
        $nd['content'] = $this->do_Add($lang);
        break;
      case 'edit':
        $nd['f_title'] = $vnT->lang["edit_config"];
        $nd['content'] = $this->do_Edit($lang);
        break;
      case 'del':
        $this->do_Del($lang);
        break;
      default:
        $nd['f_title'] = $vnT->lang['manage_config'];
        $nd['content'] = $this->do_Manage($lang);
        break;
    }

    $nd['menu'] = $func->getToolbar($this->module, $this->action, $lang);
    $nd['row_lang'] = $func->html_lang("?mod=" . $this->module . "&act=" . $this->action, $lang);
    $Template->assign("data", $nd);
    $Template->parse("box_main");
    $vnT->output .= $Template->text("box_main");

  }



  /**
   * function do_Add
   * Them gioi thieu moi
   **/
  function do_Add ($lang)
  {
    global $vnT, $func, $DB, $conf;
    $err = "";
    $data['CurrentTab'] = 1;
    if ($vnT->input['do_submit'] == 1)
    {
      $data = $_POST;
      $title = $vnT->input['title'];
      // Check for existed
      $res_chk = $DB->query("SELECT * FROM contact_config WHERE title='{$title}' AND lang='$lang' ");
      if ($check = $DB->fetch_row($res_chk)) $err = $func->html_err("Title existed");


      if(empty($vnT->input['csrf_token']) || ($vnT->input['csrf_token'] != $_SESSION['vnt_csrf_token']) ) {
        $err =  $func->html_err($vnT->lang['err_csrf_token']) ;
      }

      // insert CSDL
      if (empty($err))
      {


        $cot = array();
        $cot['lang'] = $lang;
        $res_info = $this->buildInfoItem();
        foreach ($res_info as $key => $val) {
          $cot[$key] = $val;
        }
        $cot['date_post'] = time();
        $cot['date_update'] = time();
        $cot['adminid'] =  $vnT->admininfo['adminid'];

        $kq = $DB->do_insert("contact_config", $cot);
        if ($kq) {

          //check muti lang
          $res = $DB->query("select name from language where name<>'$lang' ");
          while ($r = $DB->fetch_row($res))
          {
            $cot['lang'] = $r['name'];
            $DB->do_insert("contact_config", $cot);
          }


          unset($_SESSION['vnt_csrf_token']);
          //xoa cache
          $func->clear_cache();
          //insert adminlog
          $func->insertlog("Add", $_GET['act'], $DB->insertid());
          $mess = $vnT->lang['add_success'];
          $url = $this->linkUrl . "&sub=add";
          $func->html_redirect($url, $mess);
        } else
        {
          $err = $func->html_err($vnT->lang['add_failt'] . $DB->debug());
        }
      }
    }

    $data['map_lat'] = "10.804866895605";
    $data['map_lng'] = "106.64199984239";
    $data['checked'][1] = 'checked="checked"' ;

    $data['maps'] = '<iframe height="500" frameborder="0" style="width:780px;" scrolling="no" border="false" noresize="" src="modules/contact_ad/popup/edit_map.php" marginheight="0" marginwidth="0"/></iframe>';

    $data['qrcode'] = $vnT->qrcode->GetVcard("","","","","","","","");

    $data["html_content"] = $vnT->editor->doDisplay('description', $data['description'], '100%', '350', "Default");
    if (! isset($_SESSION['vnt_csrf_token'])) {
      $_SESSION['vnt_csrf_token'] = md5(uniqid(rand(), TRUE)) ;
    }
    $data['csrf_token'] = $_SESSION['vnt_csrf_token'] ;

    $data['err'] = $err;
    $data['link_action'] = $this->linkUrl . "&sub=add";
    /*assign the array to a template variable*/
    $this->skin->assign('data', $data);
    $this->skin->parse("edit");
    return $this->skin->text("edit");
  }

  /**
   * function do_Edit
   * Cap nhat admin
   **/
  function do_Edit ($lang)
  {
    global $vnT, $func, $DB, $conf;
    $id = (int) $vnT->input['id'];
    if ($vnT->input['do_submit'])
    {
      $err ='';

      $data = $_POST;
      $title = $vnT->input['title'];

      // Check for existed
      $res_chk = $DB->query("SELECT * FROM contact_config WHERE title='".$title."' AND lang='".$lang."' AND id<>".$id);
      if ($check = $DB->fetch_row($res_chk)) $err = $func->html_err("Title existed");

      if(empty($vnT->input['csrf_token']) || ($vnT->input['csrf_token'] != $_SESSION['vnt_csrf_token']) ) {
        $err =  $func->html_err($vnT->lang['err_csrf_token']) ;
      }

      if (empty($err))
      {
        $cot = array();
        $cot['lang'] = $lang;
        $res_info = $this->buildInfoItem();
        foreach ($res_info as $key => $val) {
          $cot[$key] = $val;
        }
        $cot['date_update'] = time();
        $cot['adminid'] =  $vnT->admininfo['adminid'];
        $ok = $DB->do_update("contact_config", $cot, "id=".$id);
        if ($ok)
        {

          unset($_SESSION['vnt_csrf_token']);
          //xoa cache
          $func->clear_cache();
          //insert adminlog
          $func->insertlog("Edit", $_GET['act'], $id);
          $err = $vnT->lang["edit_success"];
          $url = $this->linkUrl . "&sub=edit&id=$id";
          $func->html_redirect($url, $err);
        } else
          $err = $func->html_err($vnT->lang["edit_failt"] . $DB->debug());
      }
    }
    $query = $DB->query("SELECT * FROM contact_config WHERE id=$id");
    if ($data = $DB->fetch_row($query))
    {
      $data['qrcode'] = $vnT->qrcode->GetVcard($data['full_name'],$data['company'],"",$data['phone'],$data['fax'],$data['email'],$data['website'],$data['address']);

      if($data['map_picture']) {
        $data['img_maps']	 = '<img src="'.$data['map_picture'].'" alt="map_picture" />';
      }

      $data['map_information'] = str_replace("\r\n", "<br>", $data['map_desc']);
      $data['checked'][$data['map_type']] = 'checked="checked"' ;
      //$data['map_embed'] = $vnT->func->txt_unHTML($_POST['map_embed']);

    } else  {
      $mess = $vnT->lang['not_found'] . " ID : " . $id;
      $url = $this->linkUrl;
      $func->html_redirect($url, $mess);
    }
    $data['maps'] = '<iframe id="ifrMpas" name="ifrMpas" height="500" frameborder="0" style="width:780px;" scrolling="no" border="false" noresize="" src="modules/contact_ad/popup/edit_map.php?id=' . $data['id'] . '" marginheight="0" marginwidth="0"/></iframe>';

    $data["html_content"] = $vnT->editor->doDisplay('description', $data['description'], '100%', '350', "Default");

    $data['CurrentTab'] = $data['map_type']  ;

    if (! isset($_SESSION['vnt_csrf_token'])) {
      $_SESSION['vnt_csrf_token'] = md5(uniqid(rand(), TRUE)) ;
    }
    $data['csrf_token'] = $_SESSION['vnt_csrf_token'] ;


    $data['err'] = $err;
    $data['link_action'] = $this->linkUrl . "&sub=edit&id=$id";
    /*assign the array to a template variable*/
    $this->skin->assign('data', $data);
    $this->skin->parse("edit");
    return $this->skin->text("edit");
  }

  /**
   * function do_Del
   * Xoa 1 ... n
   **/
  function do_Del ($lang)
  {
    global $func, $DB, $conf, $vnT;
    $id = (int) $vnT->input['id'];
    $ext = $vnT->input["ext"];

    if(empty($vnT->input['csrf_token']) || ($vnT->input['csrf_token'] != $_SESSION['vnt_csrf_token']) ) {
      $mess =  $vnT->lang['err_csrf_token'] ;
    }else{

      $del = 0;
      $qr = "";
      if ($id != 0) {
        $ids = $id;
      }
      if (isset($vnT->input["del_id"]))  {
        $ids = implode(',', $vnT->input["del_id"]);
      }
      $ok = $DB->query("DELETE FROM contact_config WHERE id IN (" . $ids . ") ");
      if ($ok)
      {
        unset($_SESSION['vnt_csrf_token']);
        $mess = $vnT->lang["del_success"];
      } else
      {
        $mess = $vnT->lang["del_failt"];
      }
    }

    $ext_page = str_replace("|", "&", $ext);
    $url = $this->linkUrl . "&{$ext_page}";
    $func->html_redirect($url, $mess);
  }

  /**
   * function render_row
   * list cac record
   **/
  function render_row($row_info, $lang)
  {
    global $func, $DB, $conf, $vnT;
    $row = $row_info;
    // Xu ly tung ROW
    $id = $row['id'];
    $row_id = "row_" . $id;
    $output['check_box'] = vnT_HTML::checkbox("del_id[]", $id, 0, " ");
    $link_edit = $this->linkUrl . '&sub=edit&id=' . $id . '&ext=' . $row['ext_page'];
    $link_del = "javascript:del_item('" . $this->linkUrl .  "&sub=del&csrf_token=".$_SESSION['vnt_csrf_token']."&id=" . $id . "&ext=" . $row['ext_page'] . "')";

    $output['title'] = "<a href=\"{$link_edit}\"><strong>" . $row['title'] . "</strong></a>";

    $output['order'] =  "<input name=\"txt_Order[$id]\" type=\"text\" size=\"2\" maxlength=\"2\" style=\"text-align:center\" value=\"{$row['display_order']}\"  onkeypress=\"return is_num(event,'txtOrder')\" onchange='javascript:do_check($id)' />";


    $link_display = $this->linkUrl . $row['ext_link']."&csrf_token=".$_SESSION['vnt_csrf_token'];
    if ($row['display'] == 1) {
      $display = "<a  class='i-display'  href='" . $link_display . "&do_hidden=$id' data-toggle='tooltip' data-placement='top'  title='" . $vnT->lang['click_do_hidden'] . "' ><i class='fa fa-eye' ></i></a>";
    } else {
      $display = "<a class='i-display'  href='" . $link_display . "&do_display=$id'  data-toggle='tooltip' data-placement='top'  title='" . $vnT->lang['click_do_display'] . "' ><i class='fa fa-eye-slash' ></i></a>";
    }


    $output['action'] = '<div class="action-buttons"><input name=h_id[]" type="hidden" value="' . $id . '" />';
    $output['action'] .= '<a href="' . $link_edit . '" class="i-edit" ><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>';
    $output['action'] .= $display;
    $output['action'] .= '<a href="' . $link_del . '" class="i-del" ><i class="fa fa-trash-o" aria-hidden="true"></i></a>';
    $output['action'] .= '</div>';

    return $output;
  }


  /**
   * function do_Manage()
   * Quan ly
   **/
  function do_Manage ($lang)
  {
    global $vnT, $func, $DB, $conf;

    //update
    if ($vnT->input["do_action"]) {
      if(empty($vnT->input['csrf_token']) || ($vnT->input['csrf_token'] != $_SESSION['vnt_csrf_token']) ) {
        $err =  $func->html_err($vnT->lang['err_csrf_token']) ;
      }else{
        unset($_SESSION['vnt_csrf_token']);

        //xoa cache
        $func->clear_cache();
        if ($vnT->input["del_id"])     $h_id = $vnT->input["del_id"];
        switch ($vnT->input["do_action"]) {
          case "do_edit":
            if (isset($vnT->input["txt_Order"]))        $arr_order = $vnT->input["txt_Order"];
            $mess .= "- " . $vnT->lang['edit_success'] . " ID: <strong>";
            $str_mess = "";
            for ($i = 0; $i < count($h_id); $i ++) {
              $dup['display_order'] = $arr_order[$h_id[$i]];
              $ok = $DB->do_update("contact_config", $dup, "id=" . $h_id[$i]);
              if ($ok) {
                $str_mess .= $h_id[$i] . ", ";
              }
            }
            $mess .= substr($str_mess, 0, - 2) . "</strong><br>";
            $err = $func->html_mess($mess);
            ;
            break;
          case "do_hidden":
            $mess .= "- " . $vnT->lang['hidden_success'] . " ID: <strong>";
            for ($i = 0; $i < count($h_id); $i ++) {
              $dup['display'] = 0;
              $ok = $DB->do_update("contact_config", $dup, "id={$h_id[$i]} AND lang='$lang' ");
              if ($ok) {
                $str_mess .= $h_id[$i] . ", ";
              }
            }
            $mess .= substr($str_mess, 0, - 2) . "</strong><br>";
            $err = $func->html_mess($mess);
            break;
          case "do_display":
            $mess .= "- " . $vnT->lang['display_success'] . "  ID: <strong>";
            for ($i = 0; $i < count($h_id); $i ++) {
              $dup['display'] = 1;
              $ok = $DB->do_update("contact_config", $dup, "id={$h_id[$i]}  AND lang='$lang' ");
              if ($ok) {
                $str_mess .= $h_id[$i] . ", ";
              }
            }
            $mess .= substr($str_mess, 0, - 2) . "</strong><br>";
            $err = $func->html_mess($mess);
            break;
        }
      }

    }

    if((int)$vnT->input["do_display"]) {
      if(empty($vnT->input['csrf_token']) || ($vnT->input['csrf_token'] != $_SESSION['vnt_csrf_token']) ) {
        $err =  $func->html_err($vnT->lang['err_csrf_token']) ;
      }else{
        unset($_SESSION['vnt_csrf_token']);

        $ok = $DB->query("Update contact_config SET display=1 WHERE   lang='$lang' AND id=".$vnT->input["do_display"]);
        if($ok){
          $mess .= "- " . $vnT->lang['display_success'] . "  ID: <strong>".$vnT->input["do_display"] . "</strong><br>";
          $err = $func->html_mess($mess);
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

        $ok = $DB->query("Update contact_config SET display=0 WHERE   lang='$lang' AND  id=".$vnT->input["do_hidden"]);
        if($ok){
          $mess .= "- " . $vnT->lang['display_success'] . "  ID: <strong>".$vnT->input["do_hidden"] . "</strong><br>";
          $err = $func->html_mess($mess);
        }
        //xoa cache
        $func->clear_cache();
      }

    }

    if (! isset($_SESSION['vnt_csrf_token'])) {
      $_SESSION['vnt_csrf_token'] = md5(uniqid(rand(), TRUE)) ;
    }

    $p = ((int) $vnT->input['p']) ? $p = $vnT->input['p'] : 1;
    $n = ($conf['record']) ? $conf['record'] : 30;
    $ext_page = $ext ="";

    $query = $DB->query("SELECT id FROM contact_config WHERE lang='$lang' ");
    $totals = intval($DB->num_rows($query));
    $num_pages = ceil($totals / $n);
    if ($p > $num_pages) $p = $num_pages;
    if ($p < 1) $p = 1;
    $start = ($p - 1) * $n;
    $nav = $func->paginate($totals, $n, $ext, $p);
    $table['link_action'] = $this->linkUrl . "&sub=manage";
    $table['title'] = array(
      'check_box' => "<input type=\"checkbox\" name=\"checkall\" id=\"checkall\" class=\"checkbox\" />|5%|center" ,
      'order' => $vnT->lang['order'] . "|15%|center" ,
      'title' => $vnT->lang['title'] . " ||left" ,
      'action' => "Action|15%|center"
    );
    $sql = "SELECT * FROM contact_config WHERE lang='$lang'  ORDER BY display_order ASC, id DESC  LIMIT $start,$n";
    //print "sql = ".$sql."<br>";
    $result = $DB->query($sql);
    if ($DB->num_rows($result))
    {
      $row = $DB->get_array($result);
      for ($i = 0; $i < count($row); $i ++)
      {
        $row_info = $this->render_row($row[$i], $lang);
        $row_field[$i] = $row_info;
        $row_field[$i]['stt'] = ($i + 1);
        $row_field[$i]['row_id'] = "row_" . $row[$i]['id'];
        $row_field[$i]['ext'] = "";
      }
      $table['row'] = $row_field;
    } else
    {
      $table['row'] = array();
      $table['extra'] = "<div align=center class=font_err >" . $vnT->lang['no_have_contact'] . "</div>";
    }
    $table['button'] = '<input type="button" name="btnHidden" value=" ' . $vnT->lang['hidden'] . ' " class="button" onclick="do_submit(\'do_hidden\')">&nbsp;';
    $table['button'] .= '<input type="button" name="btnDisplay" value=" ' . $vnT->lang['display'] . ' " class="button" onclick="do_submit(\'do_display\')">&nbsp;';
    $table['button'] .= '<input type="button" name="btnEdit" value=" ' . $vnT->lang['update'] . ' " class="button" onclick="do_submit(\'do_edit\')">&nbsp;';
    $table['button'] .= '<input type="button" name="btnDel" value=" ' . $vnT->lang['delete'] . ' " class="button" onclick="del_selected(\'' . $this->linkUrl . '&sub=del&csrf_token='.$_SESSION['vnt_csrf_token'].'&ext=' . $ext_page . '\')">';
    $table['csrf_token'] = $_SESSION['vnt_csrf_token'] ;
    $table_list = $func->ShowTable($table);
    $data['table_list'] = $table_list;
    $data['totals'] = $totals;
    $data['err'] = $err;
    $data['nav'] = $nav;
    /*assign the array to a template variable*/
    $this->skin->assign('data', $data);
    $this->skin->parse("manage");
    return $this->skin->text("manage");
  }

  // end class
}

$vntModule = new vntModule();
?>