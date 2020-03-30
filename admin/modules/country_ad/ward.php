<?php
/*================================================================================*\
|| 							Name code : statistics.php 		 		            	  ||
||  				Copyright @2008 by Thai Son - CMS vnTRUST                     ||
\*================================================================================*/
/**
 * @version : 2.0
 * @date upgrade : 09/01/2009 by Thai Son
 **/
if (! defined('IN_vnT')) {
  die('Hacking attempt!');
}
$vntModule = new vntModule();

class vntModule
{
  var $output = "";
  var $skin = "";
  var $linkUrl = "";
  var $module = "country";
  var $action = "ward";

  /**
   * function vntModule ()
   * Khoi tao 
   **/
  function vntModule ()
  {
    global $Template, $vnT, $func, $DB, $conf;
    require_once ("function_" . $this->module . ".php");
    $this->skin = new XiTemplate(DIR_MODULE . DS . $this->module . "_ad" . DS . "html" . DS . $this->action . ".tpl");
    $this->skin->assign('DIR_IMAGE', $vnT->dir_images);
    $this->skin->assign('LANG', $vnT->lang);
    $lang = ($vnT->input['lang']) ? $lang = $vnT->input['lang'] : $func->get_lang_default();
    $this->linkUrl = "?mod=" . $this->module . "&act=" . $this->action . "&lang=" . $lang;
    $vnT->html->addScript($vnT->dir_js . "/ajax.js");
    $vnT->html->addScript("modules/" . $this->module . "_ad/js/" . $this->module . ".js");

    switch ($vnT->input['sub']) {
      case 'add':
        {
          $nd['f_title'] = "Thêm phường xã mới";
          $nd['content'] = $this->do_Add($lang);
        }
        ;
      break;
      case 'edit':
        {
          $nd['f_title'] = "Cập nhật phường xã";
          $nd['content'] = $this->do_Edit($lang);
        }
        ;
      break;
      case 'del':
        $this->do_Del($lang);
      break;
      default:
        {
          $nd['f_title'] = "Quản lý phường xã";
          $nd['content'] = $this->do_Manage($lang);
        }
        ;
      break;
    }

    $nd['menu'] = $func->getToolbar($this->module, $this->action, $lang);
    $Template->assign("data", $nd);
    $Template->parse("box_main");
    $vnT->output .= $Template->text("box_main");
  }

  /**
   * function do_Add 
   * Them  moi 
   **/
  function do_Add ($lang)
  {
    global $vnT, $func, $DB, $conf;
    $err = "";
    $data['city'] = "2";
    if ($vnT->input['do_submit'] == 1) {
      $data = $_POST;
      $state = $vnT->input['state'];
      $code = trim($vnT->input['code']);
      $name = $vnT->input['name'];
      // Check for Error
      $query = $DB->query("SELECT name FROM iso_wards  WHERE code='{$code}' ");
      if ($check = $DB->fetch_row($query))
        $err = $func->html_err("Code existed");

      if(empty($vnT->input['csrf_token']) || ($vnT->input['csrf_token'] != $_SESSION['vnt_csrf_token']) ) {
        $err =  $func->html_err($vnT->lang['err_csrf_token']) ;
      }

      // insert CSDL
      if (empty($err)) {
        $cot['city'] = $vnT->input['city'];
        $cot['state'] = $state;
        $cot['code'] = $code;
        $cot['name'] = $name;
        $cot['name_en'] = $vnT->input['name_en'];
        $kq = $DB->do_insert("iso_wards", $cot);
        if ($kq) {
          unset($_SESSION['vnt_csrf_token']);
          //xoa cache
          $func->clear_cache();
          //insert adminlog
          $func->insertlog("Add", $_GET['act'], $fid);
          $mess = $vnT->lang['add_success'];
          $url = $this->linkUrl . "&sub=add";
          $func->html_redirect($url, $mess);
        } else {
          $err = $func->html_err($vnT->lang['add_failt'] . $DB->debug());
        }
      }
    }
    $data['list_city'] = List_City($data['city'], " onChange=\"LoadAjax('list_state','&city='+this.value,'ext_state')\" ");
    $data['list_state'] = List_State($data['city'], $data['state'], " ");


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
   * Cap nhat 
   **/
  function do_Edit ($lang)
  {
    global $vnT, $func, $DB, $conf;
    $id = (int) $vnT->input['id'];
    if ($vnT->input['do_submit']) {
      $data = $_POST;
      $state = $vnT->input['state'];
      $code = trim($vnT->input['code']);
      $name = $vnT->input['name'];
      // Check for Error
      $query = $DB->query("SELECT name FROM iso_wards  WHERE code='{$code}' and id<>$id ");
      if ($check = $DB->fetch_row($query))
        $err = $func->html_err("Code existed");

      if(empty($vnT->input['csrf_token']) || ($vnT->input['csrf_token'] != $_SESSION['vnt_csrf_token']) ) {
        $err =  $func->html_err($vnT->lang['err_csrf_token']) ;
      }

      if (empty($err)) {
        $cot['city'] = $vnT->input['city'];
        $cot['state'] = $state;
        $cot['code'] = $code;
        $cot['name'] = $name;
        $cot['name_en'] = $vnT->input['name_en'];
        $kq = $DB->do_update("iso_wards", $cot, "id=$id");
        if ($kq) {
          unset($_SESSION['vnt_csrf_token']);
          //xoa cache
          $func->clear_cache();
          //update products
          $DB->query("UPDATE products SET ward='$code' WHERE ward='" . $vnT->input['h_code'] . "' ");
          //insert adminlog
          $func->insertlog("Edit", $_GET['act'], $id);
          $err = $vnT->lang["edit_success"];
          $url = $this->linkUrl . "&sub=edit&id=$id";
          $func->html_redirect($url, $err);
        } else
          $err = $func->html_err($vnT->lang["edit_failt"] . $DB->debug());
      }
    }
    $query = $DB->query("SELECT * FROM iso_wards WHERE id=$id");
    if ($data = $DB->fetch_row($query)) {
      $data['name'] = $func->txt_unHTML($data["name"]);
    } else {
      $mess = $vnT->lang['not_found'] . " ID : " . $id;
      $url = $this->linkUrl;
      $func->html_redirect($url, $mess);
    }
    $data['list_city'] = List_City($data['city'], " onChange=\"LoadAjax('list_state','&city='+this.value,'ext_state')\" ");
    $data['list_state'] = List_State($data['city'], $data['state'], " ");

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
   * Xoa 1 ... n  gioi thieu 
   **/
  function do_Del ($lang)
  {
    global $func, $DB, $conf, $vnT;
    $id = (int) $vnT->input['id'];
    $ext = $vnT->input["ext"];
    $del = 0;
    $qr = "";
    if ($id != 0) {
      $del = 1;
      $qr = " OR id='{$id}' ";
    }
    if (isset($vnT->input["del_id"])) {
      $ids = implode(',', $vnT->input["del_id"]);
      $key = $_POST["del_id"];
    }
    for ($i = 0; $i < count($key); $i ++) {
      $del = 1;
      $qr .= " OR id='{$key[$i]}' ";
    }
    $query = "DELETE FROM iso_wards WHERE id=-1" . $qr;
    if ($ok = $DB->query($query)) {
      //xoa cache
      $func->clear_cache();
      $mess = $vnT->lang["del_success"];
    } else
      $mess = $vnT->lang["del_failt"];
    $ext_page = str_replace("|", "&", $ext);
    $url = $this->linkUrl . "&{$ext_page}";
    $func->html_redirect($url, $mess);
  }

  /**
   * function render_row 
   * list cac record
   **/
  function render_row ($row_info, $lang)
  {
    global $func, $DB, $conf, $vnT;
    $row = $row_info;
    // Xu ly tung ROW
    $id = $row['id'];
    $row_id = "row_" . $id;
    $output['check_box'] = vnT_HTML::checkbox("del_id[]", $id, 0, " ");
    $link_edit = $this->linkUrl . "&sub=edit&id={$id}";
    $link_del = "javascript:del_item('" . $this->linkUrl . "&sub=del&id={$id}')";
    $output['order'] = $row['ext'] . "<input name=\"txt_Order[{$id}]\" type=\"text\" size=\"2\" maxlength=\"2\" style=\"text-align:center\" value=\"{$row['w_order']}\"  onkeypress=\"return is_num(event,'txtOrder')\" onchange='javascript:do_check($id)' />";
    $output['code'] = "<a href='$link_edit'><strong class=font_err>" . $func->HTML($row['code']) . "</strong></a>";
    $text_edit = "iso_wards|name|id=" . $row['id'];
    $output['name'] = "<b>" . $func->HTML($row['name']) . "</b>";

    $output['name_en'] = "<b>".$func->HTML($row['name_en'])."</b>";
    $link_display = $this->linkUrl . $row['ext_link'] ;
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
   * Quan ly cac gioi thieu
   **/
  function do_Manage ($lang)
  {
    global $vnT, $func, $DB, $conf;
    //update
    if ($vnT->input["do_action"]) {
      //xoa cache
      $func->clear_cache();
      if ($vnT->input["del_id"])  $h_id = $vnT->input["del_id"];
      switch ($vnT->input["do_action"]) {
        case "do_edit":
          if (isset($vnT->input["txt_Order"]))
            $arr_order = $vnT->input["txt_Order"];
          $mess .= "- " . $vnT->lang['edit_success'] . " ID: <strong>";
          $str_mess = "";
          for ($i = 0; $i < count($h_id); $i ++) {
            $dup['w_order'] = $arr_order[$h_id[$i]];
            $ok = $DB->do_update("iso_wards", $dup, "id={$h_id[$i]}");
            if ($ok) {
              $str_mess .= $h_id[$i] . ", ";
            }
          }
          $mess .= substr($str_mess, 0, - 2) . "</strong><br>";
          $err = $func->html_mess($mess);
        break;
        case "do_hidden":
          $mess .= "- " . $vnT->lang['hidden_success'] . " ID: <strong>";
          for ($i = 0; $i < count($h_id); $i ++) {
            $dup['display'] = 0;
            $ok = $DB->do_update("iso_wards", $dup, "id={$h_id[$i]}");
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
            $ok = $DB->do_update("iso_wards", $dup, "id={$h_id[$i]}");
            if ($ok) {
              $str_mess .= $h_id[$i] . ", ";
            }
          }
          $mess .= substr($str_mess, 0, - 2) . "</strong><br>";
          $err = $func->html_mess($mess);
        break;
      }
    }
    $p = ((int) $vnT->input['p']) ? $p = $vnT->input['p'] : 1;
    $n = ($conf['record']) ? $conf['record'] : 30;
    $city = ($vnT->input['city']) ? $vnT->input['city'] : "2";
    $state = ($vnT->input['state']) ? $vnT->input['state'] : "1";
    $keyword = ($vnT->input['keyword']) ? $vnT->input['keyword'] : "";
    $search = ($vnT->input['search']) ? $vnT->input['search'] : "code";
    $where = "where state = {$state} ";
		$where .= " AND city = {$city} ";
    $ext .= "&city={$city}&state={$state}";
    if (! empty($search) && ! empty($keyword)) {
      $where .= " and $search like '%$keyword%' ";
      $ext_page .= "search=$search|keyword=$keyword|";
      $ext .= "&search={$search}&keyword={$keyword}";
    }
    $query = $DB->query("SELECT id FROM iso_wards $where ");
    $totals = intval($DB->num_rows($query));
    $num_pages = ceil($totals / $n);
    if ($p > $num_pages)
      $p = $num_pages;
    if ($p < 1)
      $p = 1;
    $start = ($p - 1) * $n;
    $nav = $func->paginate($totals, $n, $ext, $p);
    $table['link_action'] = $this->linkUrl . $ext;
    $table['title'] = array(
      'check_box' => "<input type=\"checkbox\" name=\"checkall\" id=\"checkall\" class=\"checkbox\" />|5%|center" , 
      'order' => "Thứ tự|7%|center" , 
      'code' => "Mã phường xã|15%|center" , 
      'name' => "Tên phường xã (VN)|20%|left" , 
      'name_en' => "Tên phường xã (EN)|20%|left"  ,
      'action' => "Action|10%|center");
    $sql = "SELECT * FROM iso_wards $where ORDER BY w_order ASC , id DESC LIMIT $start,$n";
    // print "sql = ".$sql."<br>";
    $reuslt = $DB->query($sql);
    if ($DB->num_rows($reuslt)) {
      $row = $DB->get_array($result);
      for ($i = 0; $i < count($row); $i ++) {
        $row_info = $this->render_row($row[$i], $lang);
        $row_field[$i] = $row_info;
        $row_field[$i]['stt'] = ($i + 1);
        $row_field[$i]['row_id'] = "row_" . $row[$i]['id'];
        $row_field[$i]['ext'] = "";
      }
      $table['row'] = $row_field;
    } else {
      $table['row'] = array();
      $mess = (empty($state)) ? "Vui lòng chọn 1 quận huyện " : "Chưa có phường xã nào ";
      $table['extra'] = "<div align=center class=font_err > " . $mess . " </div>";
    }
    $table['button'] = '<input type="button" name="btnHidden" value=" ' . $vnT->lang['hidden'] . ' " class="button" onclick="do_submit(\'do_hidden\')">&nbsp;';
    $table['button'] .= '<input type="button" name="btnDisplay" value=" ' . $vnT->lang['display'] . ' " class="button" onclick="do_submit(\'do_display\')">&nbsp;';
    $table['button'] .= '<input type="button" name="btnEdit" value=" ' . $vnT->lang['update'] . ' " class="button" onclick="do_submit(\'do_edit\')">&nbsp;';
    $table['button'] .= '<input type="button" name="btnDel" value=" ' . $vnT->lang['delete'] . ' " class="button" onclick="del_selected(\'' . $this->linkUrl . '&sub=del&ext=' . $ext_page . '\')">';
    $table_list = $func->ShowTable($table);
    $data['table_list'] = $table_list;
    $data['totals'] = $totals;
    $data['list_city'] = List_City($city, " onChange=\"LoadAjax('list_state','&city='+this.value,'ext_state')\" ");
    $data['list_state'] = List_State($city, $state, " ");
    $data['keyword'] = $keyword;
    $data['list_search'] = $this->List_Search($search);
    $data['err'] = $err;
    $data['nav'] = $nav;
    /*assign the array to a template variable*/
    $this->skin->assign('data', $data);
    $this->skin->parse("manage");
    return $this->skin->text("manage");
  }

  //====== List_Search
  function List_Search ($did)
  {
    global $func, $DB, $conf, $vnT;
    $text = "<select size=1 name=\"search\" id=\"search\" class='select'>";
    if ($did == "code")
      $text .= "<option value=\"code\" selected> Mã phường xã </option>";
    else
      $text .= "<option value=\"code\" >  Mã phường xã </option>";
    if ($did == "name")
      $text .= "<option value=\"name\" selected> Tên phường xã </option>";
    else
      $text .= "<option value=\"name\">Tên phường xã</option>";
    $text .= "</select>";
    return $text;
  }
  //end class
}
?>