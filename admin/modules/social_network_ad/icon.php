<?php
/*================================================================================*\
|| 							Name code : advertise.php 		 		            	  ||
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
  var $module = "social_network";
  var $action = "icon";

  /**
   * function vntModule ()
   * Khoi tao 
   **/
  function vntModule ()
  {
    global $Template, $vnT, $func, $DB, $conf;
    require_once ("function_" . $this->module . ".php");
    $this->skin = new XiTemplate(DIR_MODULE . DS . $this->module . "_ad" . DS . "html" . DS . $this->action . ".tpl");
    $this->skin->assign('LANG', $vnT->lang);
    $this->skin->assign('DIR_IMAGE', $vnT->dir_images);
    $lang = ($vnT->input['lang']) ? $lang = $vnT->input['lang'] : $func->get_lang_default();
    $this->linkUrl = "?mod=" . $this->module . "&act=" . $this->action . "&lang=" . $lang;
    switch ($vnT->input['sub']) {
      case 'add':
        $nd['f_title'] = $vnT->lang['add_icon'];
        $nd['content'] = $this->do_Add($lang);
      break;
      case 'edit':
        $nd['f_title'] = $vnT->lang['edit_icon'];
        $nd['content'] = $this->do_Edit($lang);
      break;
      case 'del':
        $this->do_Del($lang);
      break;
      default:
        $nd['f_title'] = $vnT->lang['manage_icon'];
        $nd['content'] = $this->do_Manage($lang);
      break;
    }
    $nd['menu'] = $func->getToolbar($this->module, $this->action, $lang); 
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
    $data['link'] = "http://";
    $vnT->input['target'] = "_blank";
    $vnT->input['display'] = 1;
    $err = "";
    if ($vnT->input['do_submit'] == 1) {
      $data = $_POST;
      $title = chop($vnT->input['title']);
      $picture = chop($vnT->input['picture']);
      $link = chop($vnT->input['link']);
      $data['err'] = "";

      if(empty($vnT->input['csrf_token']) || ($vnT->input['csrf_token'] != $_SESSION['vnt_csrf_token']) ) {
        $err =  $func->html_err($vnT->lang['err_csrf_token']) ;
      }

      // insert CSDL
      if (empty($err)) {
        $cot['title'] = $title;
        $cot['picture'] = $picture;
        $cot['link'] = $link;
        $cot['pos'] = $vnT->input['pos'];
        $cot['target'] = $vnT->input['target'];
        $cot['type'] = $type; 
        $cot['display'] = $vnT->input['display'];
        $ok = $DB->do_insert("social_network_icon", $cot);
        if ($ok) {

          unset($_SESSION['vnt_csrf_token']);

          //xoa cache
          $func->clear_cache();
          //insert adminlog
          $func->insertlog("Add", $_GET['act'], $DB->insertid());
          $mess = $vnT->lang['add_success'];
          $url = $this->linkUrl . "&sub=add";
          $func->html_redirect($url, $mess);
        } else {
          $err = $func->html_err($vnT->lang['add_failt'] . $DB->debug());
        }
      }
    } 
    $data['list_target'] = List_Target($vnT->input['target']);
    $data['list_display'] = vnT_HTML::list_yesno("display", $vnT->input['display']);

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
    if ($vnT->input['do_submit']) {
      $data = $_POST;
      $title = $vnT->input['title'];
      $picture = $vnT->input['picture'];
      $link = $vnT->input['link'];

      if(empty($vnT->input['csrf_token']) || ($vnT->input['csrf_token'] != $_SESSION['vnt_csrf_token']) ) {
        $err =  $func->html_err($vnT->lang['err_csrf_token']) ;
      }

      if (empty($err)) {
 
        $cot['picture'] = $picture;
        $cot['target'] = $vnT->input['target'];
        $cot['link'] = $link;
        $cot['title'] = $title;
        $cot['width'] = (int) $vnT->input['width'];
        $cot['height'] = (int) $vnT->input['height'];
        $cot['display'] = $vnT->input['display'];
        $ok = $DB->do_update("social_network_icon", $cot, "id=$id");
        if ($ok) {

          unset($_SESSION['vnt_csrf_token']);

          //xoa cache
          $func->clear_cache();
          //insert adminlog
          $func->insertlog("Edit", $_GET['act'], $id);
          $err = $vnT->lang["edit_success"];
          $url = $this->linkUrl . "&sub=manage&pos=" . $vnT->input['pos'];
          $func->html_redirect($url, $err);
        } else
          $err = $func->html_err($vnT->lang["edit_failt"] . $DB->debug());
      }
    }
    $query = $DB->query("SELECT * FROM social_network_icon WHERE id='{$id}' ");
    if ($data = $DB->fetch_row($query)) {
      if ($data['picture']) { 	
        $data['pic'] = " <img src=\"".$vnT->conf['rooturl']."vnt_upload/weblink/".$data['picture']."\" />  <a href=\"javascript:del_picture('picture')\" class=\"del\">Xóa</a>";
				$data['style_upload'] = "style='display:none' ";
      } else {
        $data['pic'] = "";
      }
      $data['title'] = $func->txt_unHTML($data['title']);
    } else {
      $mess = $vnT->lang['not_found'] . " ID : " . $id;
      $url = $this->linkUrl;
      $func->html_redirect($url, $mess);
    }

    if (! isset($_SESSION['vnt_csrf_token'])) {
      $_SESSION['vnt_csrf_token'] = md5(uniqid(rand(), TRUE)) ;
    }
    $data['csrf_token'] = $_SESSION['vnt_csrf_token'] ;

    $data['err'] = $err;
    $data['list_target'] = List_Target($data['target']);
    $data['list_display'] = vnT_HTML::list_yesno("display", $data['display']);
    $data['link_action'] = $this->linkUrl . "&sub=edit&id={$id}";
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
    if(empty($vnT->input['csrf_token']) || ($vnT->input['csrf_token'] != $_SESSION['vnt_csrf_token']) ) {
      $mess =  $vnT->lang['err_csrf_token'] ;
    }else{
      unset($_SESSION['vnt_csrf_token']);
      $del = 0;
      $qr = "";
      if ($id != 0) {
        $del = 1;
        $qr = " OR id='{$id}' ";
      }
      if (isset($vnT->input["del_id"])) {
        $ids = implode(',', $vnT->input["del_id"]);
        $key = $vnT->input["del_id"];
      }
      for ($i = 0; $i < count($key); $i ++) {
        $del = 1;
        $qr .= " OR id='{$key[$i]}' ";
      }
      // Del Image
      $query = $DB->query("SELECT picture FROM social_network_icon WHERE id=-1" . $qr);
      while ($img = $DB->fetch_row($query)) {
        if ((file_exists(MOD_DIR_UPLOAD . "/" . $img['picture'])) && (! empty($img['picture'])))
          @unlink(MOD_DIR_UPLOAD . "/" . $img['picture']);
      }
      // End del image
      $query = "DELETE FROM social_network_icon WHERE id=-1" . $qr;
      if ($ok = $DB->query($query)) {
        $mess = $vnT->lang["del_success"];
      } else
        $mess = $vnT->lang["del_failt"];
    }

    $ext_page = str_replace("|", "&", $ext);
    $url = $this->linkUrl . "&sub=manage&{$ext_page}";
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
    $link_edit = $this->linkUrl . '&sub=edit&id=' . $id . '&ext=' . $row['ext_page'];
    $link_del = "javascript:del_item('" . $this->linkUrl . "&sub=del&csrf_token=".$_SESSION['vnt_csrf_token']."&id=" . $id . "&ext=" . $row['ext_page'] . "')";
    $output['order'] = "<input name=\"txt_Order[{$id}]\" type=\"text\" size=\"2\" maxlength=\"2\" style=\"text-align:center\" value=\"{$row['display_order']}\"  onkeypress=\"return is_num(event,'txtOrder')\" onchange='javascript:do_check($id)' />";
    if ($row['picture']) {
      $src = MOD_DIR_UPLOAD . "/" . $row['picture'];
       $img = "<img src=\"{$src}\" >";      
    } else
      $img = "No image";
    $output['img'] = $img   ;
    $output['link'] = $row['link']  ;
		
    $output['title'] = '<a href="' . $link_edit . '"><strong>' . $row['title'] . '</strong></a>';

    $link_display = $this->linkUrl . $row['ext_link']."&csrf_token=".$_SESSION['vnt_csrf_token'];
    if ($row['display'] == 1) {
      $display = "<a class='i-display' href='" . $link_display . "&do_hidden=$id' data-toggle='tooltip' data-placement='top'  title='" . $vnT->lang['click_do_hidden'] . "' ><i class=\"fa fa-eye\" aria-hidden=\"true\"></i></a>";
    } else {
      $display = "<a class='i-display'  href='" . $link_display . "&do_display=$id'  data-toggle='tooltip' data-placement='top'  title='" . $vnT->lang['click_do_display'] . "' ><i class=\"fa fa-eye-slash\" aria-hidden=\"true\"></i></a>";
    }

    $output['action'] = '<div class="action-buttons"><input name=h_id[]" type="hidden" value="' . $id . '" />';
    $output['action'] .= '<a href="' . $link_edit . '" class="i-edit"  data-toggle=\'tooltip\' data-placement=\'top\' title="Cập nhật"  ><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>';
    $output['action'] .= $display;
    $output['action'] .= '<a href="' . $link_del . '" class="i-del"  data-toggle=\'tooltip\' data-placement=\'top\' title="Xóa" ><i class="fa fa-trash-o" aria-hidden="true"></i></a>';
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
      if(empty($vnT->input['csrf_token']) || ($vnT->input['csrf_token'] != $_SESSION['vnt_csrf_token']) ) {
        $err =  $func->html_err($vnT->lang['err_csrf_token']) ;
      }else{
        unset($_SESSION['vnt_csrf_token']);

        //xoa cache
        $func->clear_cache();
        if ($vnT->input["del_id"])  $h_id = $vnT->input["del_id"];
        switch ($vnT->input["do_action"]) {
          case "do_edit":
            if (isset($vnT->input["txt_Order"])) $arr_order = $vnT->input["txt_Order"];
            $mess .= "- " . $vnT->lang['edit_success'] . " ID: <strong>";
            $str_mess = "";
            for ($i = 0; $i < count($h_id); $i ++) {
              $dup['display_order'] = $arr_order[$h_id[$i]];
              $ok = $DB->do_update("social_network_icon", $dup, "id={$h_id[$i]}");
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
              $ok = $DB->do_update("social_network_icon", $dup, "id=" . $h_id[$i]);
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
              $ok = $DB->do_update("social_network_icon", $dup, "id=" . $h_id[$i]);
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
        $ok = $DB->query("Update social_network_icon SET display=1 WHERE  id=".$vnT->input["do_display"]);
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
        $ok = $DB->query("Update social_network_icon SET display=0 WHERE   id=".$vnT->input["do_hidden"]);
        if($ok){
          $mess .= "- " . $vnT->lang['hidden_success'] . "  ID: <strong>".$vnT->input["do_hidden"] . "</strong><br>";
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
		
    $where = "";
    $ext = "";
    $query = $DB->query("SELECT * FROM social_network_icon $where");
    $totals = intval($DB->num_rows($query));
    $num_pages = ceil($totals / $n);
    if ($p > $num_pages)
      $p = $num_pages;
    if ($p < 1)
      $p = 1;
    $start = ($p - 1) * $n;
    $nav = $func->paginate($totals, $n, $ext, $p);
    $table['link_action'] = $this->linkUrl . "&sub=manage&p=$p";
    $table['title'] = array(
      'check_box' => "<input type=\"checkbox\" name=\"checkall\" id=\"checkall\" class=\"checkbox\" />|5%|center" , 
      'order' => $vnT->lang['order'] . "|10%|center" , 
      'img' => $vnT->lang['picture'] . "|20%|center" , 
			'title' => $vnT->lang['title'] . "|20%|center" ,       
      'link' => "link |30%|center" , 
      'action' => "Action|10%|center");
    $sql = "SELECT * FROM social_network_icon $where ORDER BY display_order LIMIT $start,$n";
    //print "sql = ".$sql."<br>";
    $result = $DB->query($sql);
    if ($DB->num_rows($result)) {
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
      if ($pos) {
        $table['extra'] = "<div align=center class=font_err >" . $vnT->lang['no_have_advertise'] . "</div>";
      } else {
        $table['extra'] = "<div align=center class=font_err > Vui lòng chọn vị trí </div>";
      }
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
?>