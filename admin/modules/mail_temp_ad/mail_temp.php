<?php
/*================================================================================*\
|| 							Name code : mail_temp.php 		 		            	  ||
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
  var $module = "mail_temp";
  var $action = "mail_temp";

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
    $lang = ($vnT->input['lang']) ? $lang = $vnT->input['lang'] : $func->get_lang_default();
    $this->linkUrl = "?mod=" . $this->module . "&act=" . $this->action . "&lang=" . $lang;
    switch ($vnT->input['sub']) {
      case 'add':
        $nd['f_title'] = $vnT->lang['add_mail_temp'];
        $nd['content'] = $this->do_Add($lang);
      break;
      case 'edit':
        $nd['f_title'] = $vnT->lang['edit_mail_temp'];
        $nd['content'] = $this->do_Edit($lang);
      break;
      case 'del':
        $this->do_Del($lang);
      break;
      default:
        $nd['f_title'] = $vnT->lang['manage_mail_temp'];
        $nd['content'] = $this->do_Manage($lang);
      break;
    }
    $nd['menu'] = getToolbar($this->action, $lang);
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
    if ($vnT->input['do_submit'] == 1) {
      $data = $_POST;
      $name = $vnT->input['name'];
      $title = $vnT->input['title'];
      // Check for existed
      $res_chk = $DB->query("SELECT * FROM mail_template WHERE name='{$name}' ");
      if ($check = $DB->fetch_row($res_chk))
        $err = $func->html_err("Name existed");

      if(empty($vnT->input['csrf_token']) || ($vnT->input['csrf_token'] != $_SESSION['vnt_csrf_token']) ) {
        $err =  $func->html_err($vnT->lang['err_csrf_token']) ;
      }

        // insert CSDL
      if (empty($err)) {
        $cot['name'] = $name;
        $cot['title'] = $title;
        $cot['content'] = $DB->mySQLSafe($_POST['content']);
        $cot['lang'] = $lang;
        $ok = $DB->do_insert("mail_template", $cot);
        if ($ok) {
          //check muti lang
          $res = $DB->query("select name from language where name<>'$lang' ");
          while ($r = $DB->fetch_row($res)) {
            $cot['lang'] = $r['name'];
            $DB->do_insert("mail_template", $cot);
          }

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
    $data["html_content"] = $vnT->editor->doDisplay('content', $vnT->input['content'], '100%', '500', "Default");


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
    $err ='';

    if ($vnT->input['do_submit']) {
      $data = $_POST;
      $name = $vnT->input['name'];
      $title = $vnT->input['title'];

      if(empty($vnT->input['csrf_token']) || ($vnT->input['csrf_token'] != $_SESSION['vnt_csrf_token']) ) {
        $err =  $func->html_err($vnT->lang['err_csrf_token']) ;
      }

      if (empty($err)) {
        $cot['name'] = $name;
        $cot['title'] = $title;
        $cot['content'] = $DB->mySQLSafe($_POST['content']);
        $ok = $DB->do_update("mail_template", $cot, "id=$id ");
        if ($ok) {

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
    $query = $DB->query("SELECT * FROM mail_template WHERE id=$id");
    if ($data = $DB->fetch_row($query)) {
      $data['name'] = $data['name'];
      $data['title'] = $data['title'];
      $data['content'] = $data['content'];
    } else {
      $mess = $vnT->lang['not_found'] . " ID : " . $id;
      $url = $this->linkUrl;
      $func->html_redirect($url, $mess);
    }
    $data['readonly'] = "readonly='ReadOnly'";
    $data['note'] = $vnT->lang['note_format'];
    $data["html_content"] = $vnT->editor->doDisplay('content', $data['content'], '100%', '500', "Default");


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
    $del = 0;
    $qr = "";
    if ($id != 0) {
      $ids = $id;
    }
    if (isset($vnT->input["del_id"])) {
      $ids = implode(',', $vnT->input["del_id"]);
    }
    $res = $DB->query("SELECT * FROM mail_template WHERE id IN (" . $ids . ") ");
    if ($DB->num_rows($res)) {
      while ($row = $DB->fetch_row($res)) {
        $name = $row['name'];
        $DB->query("DELETE FROM mail_template WHERE name ='" . $name . "' ");
      }
      $mess = $vnT->lang["del_success"];
    } else {
      $mess = $vnT->lang["del_failt"];
    }
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
    $link_edit = $this->linkUrl . '&sub=edit&id=' . $id . '&ext=' . $row['ext_page'];
    $link_del = "javascript:del_item('" . $this->linkUrl . "&sub=del&id=" . $id . "&ext=" . $row['ext_page'] . "')";
    $output['name'] = "<a href=\"{$link_edit}\">#" . $row['name'] . "</a>";
    $output['title'] = $func->HTML($row['title']);
    $output['action'] = '<input name=h_id[]" type="hidden" value="' . $id . '" />';
    $output['action'] .= '<a href="' . $link_edit . '"><img src="' . $vnT->dir_images . '/edit.gif"  alt="Edit "></a>&nbsp;';
    $output['action'] .= '<a href="' . $link_del . '"><img src="' . $vnT->dir_images . '/delete.gif"  alt="Delete "></a>';
    return $output;
  }

  /**
   * function do_Manage() 
   * Quan ly cac gioi thieu
   **/
  function do_Manage ($lang)
  {
    global $vnT, $func, $DB, $conf;
    $p = ((int) $vnT->input['p']) ? $p = $vnT->input['p'] : 1;
    $n = ($conf['record']) ? $conf['record'] : 30;
    $query = $DB->query("SELECT id FROM mail_template WHERE lang='$lang' ");
    $totals = intval($DB->num_rows($query));
    $num_pages = ceil($totals / $n);
    if ($p > $num_pages)
      $p = $num_pages;
    if ($p < 1)
      $p = 1;
    $start = ($p - 1) * $n;
    $nav = $func->paginate($totals, $n, $ext, $p);
    $table['link_action'] = $this->linkUrl . "&sub=manage";
    $table['title'] = array(
      'check_box' => "<input type=\"checkbox\" name=\"checkall\" id=\"checkall\" class=\"checkbox\" />|5%|center" , 
      'name' => $vnT->lang['name'] . "|30%|center" , 
      'title' => $vnT->lang['title'] . " |45%|left" , 
      'action' => "Action|15%|center");
    $sql = "SELECT * FROM mail_template WHERE lang='$lang'  ORDER BY  id DESC  LIMIT $start,$n";
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
      $table['extra'] = "<div align=center class=font_err >" . $vnT->lang['no_have_mail_temp'] . "</div>";
    }
    $table['button'] .= '<input type="button" name="btnDel" value=" ' . $vnT->lang['delete'] . ' " class="button" onclick="del_selected(\'' . $this->linkUrl . '&sub=del&ext=' . $ext_page . '\')">';
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