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
  var $action = "admin_group";

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
		
    switch ($vnT->input['sub']) {
      case 'add':
        $nd['f_title'] = $vnT->lang['add_admin_group'];
        $nd['content'] = $this->do_Add($lang);
      break;
      case 'edit':
        $nd['f_title'] = $vnT->lang['edit_admin_group'];
        $nd['content'] = $this->do_Edit($lang);
      break;
      case 'del':
        $this->do_Del($lang);
      break;
      default:
        $nd['f_title'] = $vnT->lang['manage_admin_group'];
        $nd['content'] = $this->do_Manage($lang);
      break;
    }
    $nd['menu'] = $func->getToolbar($this->module, $this->action, $lang);
		$nd['icon'] = 'icon-'.$this->module;
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
    if ($vnT->input['do_submit'] == 1)
    {
      $data = $_POST;
      $title = $vnT->input['title'];
      $arr_group = array();
      $actions = $vnT->input['action'];
      if (is_array($actions)) {
        foreach ($actions as $g_group => $action) {
          if (is_array($action)) {
            foreach ($action as $act) {
              $res_g = $DB->query("select * from admin_permission where g_name='$g_group' AND act='$act' ");
              $row_g = $DB->fetch_row($res_g);
              $subs = $vnT->input["" . $g_group . ""][$act];
              $text_sub = $act . "=>" . @implode(",", $subs);
              if (@array_key_exists($row_g['g_name'], $arr_permission)) {
                $arr_permission[$row_g['g_name']] .= "|" . $text_sub;
              } else {
                $arr_permission[$row_g['g_name']] = $text_sub;
              }
            }
          }
        }
        $permission = serialize($arr_permission);
      } else {
        $permission = "";
      }
      // Check for Error
      $res_chk = $DB->query("SELECT * FROM admin_group  WHERE title='{$title}'  ");
      if ($check = $DB->fetch_row($res_chk))
        $err = $func->html_err("Name existed");


      if(empty($vnT->input['csrf_token']) || ($vnT->input['csrf_token'] != $_SESSION['vnt_csrf_token']) ) {
        $err =  $func->html_err($vnT->lang['err_csrf_token']) ;
      }

        // insert CSDL
      if (empty($err)) {
        $cot['title'] = $title;
        $cot['permission'] = $permission;
        $ok = $DB->do_insert("admin_group", $cot);
        if ($ok) {
          unset($_SESSION['vnt_csrf_token']);

          //insert adminlog
          $func->insertlog("Add", $_GET['act'], $DB->insertid());
          $mess = $vnT->lang['add_success'];
          $url = $this->linkUrl . "&sub=add";
          $func->html_redirect($url, $mess);
        } else {
          $err = $func->html_err($vnT->lang['add_failt']);
        }
      }
    }
    //phan quyen
    $list_phanquyen = '';
    $str_title = "title_" . $conf['langcp'];
    foreach ($vnT->permission as $key => $value) {
      $list_phanquyen .= '<table width="100%" border="0" cellspacing="1" cellpadding="1" style="border:1px solid #B84120">
										<tr>
											<td style="background:#B84120; color:#FFFFFF; font-weight:bold">' . $value . '</td>
										</tr>';
      //check act
      $sql_mod = "select * from admin_permission where g_name='" . $key . "' AND display=1  order by displayorder ";
      $res_mod = $DB->query($sql_mod);
      while ($row_mod = $DB->fetch_row($res_mod)) {
        $act = $row_mod['act'];
        $listOption = "";
        if ($row_mod['text_option']) {
          $arr_option = @explode(",", $row_mod['text_option']);
          foreach ($arr_option as $option) {
            $listOption .= "<input name=\"{$key}[{$act}][]\" id=\"{$key}[{$act}]\" type=\"checkbox\" value=\"{$option}\"   align=\"absmiddle\" onClick=\"checkOne('" . $key . "','" . $act . "')\"  />" . $option . "&nbsp;&nbsp;";
          }
        }
        $list_phanquyen .= '<tr>
											<td>
												<table width="100%" border="0" cellspacing="2" cellpadding="2">
												<tr>
													<td width="30%">
													<input name="action[' . $key . '][]" id="action[' . $key . ']" type="checkbox" value="' . $act . '"  align="absmiddle" onClick="checkAction(\'' . $key . '\',\'' . $act . '\')" />&nbsp;
													<strong>' . $row_mod[$str_title] . '</strong></td>
													<td align=left>' . $listOption . '</td>
												</tr>
											</table>
											</td>
										</tr>';
      } //end while mod
      $list_phanquyen .= '</table><br>';
    }
    $data['list_phanquyen'] = $list_phanquyen;
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
      $data = $_POST;
      $title = $vnT->input['title'];
      $arr_group = array();
      $actions = $vnT->input['action'];
      if (is_array($actions)) {
        foreach ($actions as $g_group => $action) {
          if (is_array($action)) {
            foreach ($action as $act) {
              $res_g = $DB->query("select * from admin_permission where g_name='$g_group' AND act='$act' ");
              $row_g = $DB->fetch_row($res_g);
              $subs = $vnT->input["" . $g_group . ""][$act];
              $text_sub = $act . "=>" . @implode(",", $subs);
              if (@array_key_exists($row_g['g_name'], $arr_permission)) {
                $arr_permission[$row_g['g_name']] .= "|" . $text_sub;
              } else {
                $arr_permission[$row_g['g_name']] = $text_sub;
              }
            }
          }
        }
        $permission = serialize($arr_permission);
      } else {
        $permission = "";
      }
      // Check for Error
      $res_chk = $DB->query("SELECT * FROM admin_group  WHERE title='{$title}'  and gid<>$id");
      if ($check = $DB->fetch_row($res_chk))
        $err = $func->html_err($vnT->lang['title_existed']);

      if(empty($vnT->input['csrf_token']) || ($vnT->input['csrf_token'] != $_SESSION['vnt_csrf_token']) ) {
        $err =  $func->html_err($vnT->lang['err_csrf_token']) ;
      }

      if (empty($err))
      {
        $cot['title'] = $title;
        $cot['permission'] = $permission;
        $ok = $DB->do_update("admin_group", $cot, "gid=$id");
        if ($ok) {

          unset($_SESSION['vnt_csrf_token']);
          //insert adminlog
          $func->insertlog("Edit", $_GET['act'], $id);
          $err = $vnT->lang["edit_success"];
          $url = $this->linkUrl . "&sub=edit&id=$id";
          $func->html_redirect($url, $err);
        } else
          $err = $func->html_err($vnT->lang["edit_failt"]);
      }
    }
    $query = $DB->query("SELECT * FROM admin_group WHERE gid=$id");
    if ($data = $DB->fetch_row($query)) {
    } else {
      $mess = $vnT->lang['not_found'] . " ID : " . $id;
      $url = $this->linkUrl;
      flush();
      echo $func->html_redirect($url, $mess);
      exit();
    }
    //neu la admin
    if ($vnT->admininfo['adminid'] == 1) {
      $arr_permission = unserialize($data['permission']);
      if (is_array($arr_permission)) {
        foreach ($arr_permission as $key => $value) {
          $arr_act = explode("|", $value);
          foreach ($arr_act as $k => $v) {
            $arr_sub = explode("=>", $v);
            $arr_check[$key][$arr_sub[0]] = $arr_sub[1];
          }
        }
      }
      //phan quyen
      $list_phanquyen = '';
      $str_title = "title_" . $conf['langcp'];
      foreach ($vnT->permission as $key => $value) {
        $list_phanquyen .= '<table width="100%" border="0" cellspacing="1" cellpadding="1" style="border:1px solid #B84120">
											<tr>
												<td style="background:#B84120; color:#FFFFFF; font-weight:bold">' . $value . '</td>
											</tr>';
        //check mod,block
        $sql_mod = "select * from admin_permission where g_name='" . $key . "'  AND display=1 order by displayorder ";
        $res_mod = $DB->query($sql_mod);
        while ($row_mod = $DB->fetch_row($res_mod)) {
          $act = $row_mod['act'];
          $listOption = "";
          if ($row_mod['text_option']) {
            $arr_option = explode(",", $row_mod['text_option']);
            foreach ($arr_option as $option) {
              $chk_sub = (strstr($arr_check[$key][$act], $option)) ? "checked" : "";
              //echo "option = ".$option."<br>br chk_sub = ".$chk_sub."<br><br>";
              $listOption .= "<input name=\"{$key}[{$act}][]\" id=\"{$key}[{$act}]\" type=\"checkbox\" value=\"{$option}\" {$chk_sub} align=\"absmiddle\" onClick=\"checkOne('" . $key . "','" . $act . "')\"  />" . $option . "&nbsp;&nbsp;";
            }
          }
          $chk_act = (@array_key_exists($act, $arr_check[$key])) ? "checked" : "";
          $list_phanquyen .= '<tr>
												<td>
													<table width="100%" border="0" cellspacing="2" cellpadding="2">
													<tr>
														<td width="30%">
														<input name="action[' . $key . '][]" id="action[' . $key . ']" type="checkbox" value="' . $act . '" ' . $chk_act . ' align="absmiddle" onClick="checkAction(\'' . $key . '\',\'' . $act . '\')" />&nbsp;
														<strong>' . $row_mod[$str_title] . '</strong></td>
														<td align=left>' . $listOption . '</td>
													</tr>
												</table>
												</td>
											</tr>';
        } //end while mod
        $list_phanquyen .= '</table><br>';
      } // end while group
      $data['list_phanquyen'] = $list_phanquyen;
    }
    $data['err'] = $err;
    $data['link_action'] = $this->linkUrl . "&sub=edit&id=$id";

    if (! isset($_SESSION['vnt_csrf_token'])) {
      $_SESSION['vnt_csrf_token'] = md5(uniqid(rand(), TRUE)) ;
    }
    $data['csrf_token'] = $_SESSION['vnt_csrf_token'] ;

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

    if(empty($vnT->input['csrf_token']) || ($vnT->input['csrf_token'] != $_SESSION['vnt_csrf_token']) ) {
      $mess =  $vnT->lang['err_csrf_token'] ;
    }else{
      if ($id != 0) {
        $ids = $id;
      }
      if (isset($vnT->input["del_id"])) {
        $ids = implode(',', $vnT->input["del_id"]);
      }
      $query = 'DELETE FROM admin_group WHERE gid IN (' . $ids . ')';
      if ($ok = $DB->query($query)) {
        unset($_SESSION['vnt_csrf_token']);
        $mess = $vnT->lang["del_success"];
      } else
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
    $id = $row['gid'];
    $row_id = "row_" . $id;
    $output['check_box'] = vnT_HTML::checkbox("del_id[]", $id, 0, " ");
    $link_edit = $this->linkUrl . '&sub=edit&id=' . $id . '&ext=' . $row['ext_page'];
    $link_del = "javascript:del_item('" . $this->linkUrl . "&sub=del&id=" . $id . "&csrf_token=".$_SESSION['vnt_csrf_token']."&ext=" . $row['ext_page'] . "')";
    $output['title'] = "<a href=\"{$link_edit}\"><strong>" . $row['title'] . "</strong></a>";
    $output['order'] = $ext . "<input name=\"txt_Order[" . $id . "]\" type=\"text\" size=\"2\" maxlength=\"2\" style=\"text-align:center\" value=\"{$row['a_order']}\" onchange='javascript:do_check($id)' />";
    $text_permission = "";
    if (empty($row['permission']))
      $text_permission = $vnT->lang['all_permission'];
    else {
      $arr_permission = array();
      $arr_permission = unserialize($row['permission']);
      foreach ($arr_permission as $key => $value) {
        $text_permission .= "<p><b class=font_err>" . $vnT->permission[$key] . "</b>";
        $arr_act = explode("|", $value);
        foreach ($arr_act as $k => $v) {
          $arr_sub = explode("=>", $v);
          $text_permission .= "<br>&#8226; <strong>" . $arr_sub[0] . "</strong>[" . $arr_sub[1] . "]";
        }
        $text_permission . "</p>";
      }
    }
    $output['permission'] = $text_permission;
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


    //update
    if ($vnT->input["do_action"]) {

      if(empty($vnT->input['csrf_token']) || ($vnT->input['csrf_token'] != $_SESSION['vnt_csrf_token']) ) {
        $err =  $func->html_err($vnT->lang['err_csrf_token']) ;
      }else{
        unset($_SESSION['vnt_csrf_token']);
        //xoa cache
        $func->clear_cache();
        if ($vnT->input["del_id"])
          $h_id = $vnT->input["del_id"];
        switch ($vnT->input["do_action"]) {
          case "do_edit":
            if (isset($vnT->input["txt_Order"]))
              $arr_order = $vnT->input["txt_Order"];
            $mess .= "- " . $vnT->lang['edit_success'] . " ID: <strong>";
            $str_mess = "";
            for ($i = 0; $i < count($h_id); $i ++) {
              $dup['a_order'] = $arr_order[$h_id[$i]];
              $ok = $DB->do_update("admin_group", $dup, "gid=" . $h_id[$i]);
              if ($ok) {
                $str_mess .= $h_id[$i] . ", ";
              } else {
                $mess .= "- " . $vnT->lang['edit_failt'] . " ID: <strong>" . $h_id[$i] . "</strong><br>";
              }
            }
            $mess .= substr($str_mess, 0, - 2) . "</strong><br>";
            $err = $func->html_mess($mess);
            break;
          case "do_hidden":
            $mess .= "- " . $vnT->lang['hidden_success'] . " ID: <strong>";
            for ($i = 0; $i < count($h_id); $i ++) {
              $dup['display'] = 0;
              $ok = $DB->do_update("admin_group", $dup, "gid=" . $h_id[$i]);
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
              $ok = $DB->do_update("admin_group", $dup, "gid=" . $h_id[$i]);
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

    if (! isset($_SESSION['vnt_csrf_token'])) {
      $_SESSION['vnt_csrf_token'] = md5(uniqid(rand(), TRUE)) ;
    }

    $p = ((int) $vnT->input['p']) ? $p = $vnT->input['p'] : 1;
    $n = ($conf['record']) ? $conf['record'] : 30;
    $query = $DB->query("SELECT gid FROM admin_group  ");
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
      'order' => $vnT->lang['order'] . "|10%|center" , 
      'title' => $vnT->lang['group_name'] . "|20%|center" , 
      'permission' => $vnT->lang['permission'] . "|40%|left" , 
      'action' => "Action|10%|center");
    $sql = "SELECT * FROM admin_group  ORDER BY  a_order  LIMIT $start,$n";
    //print "sql = ".$sql."<br>";
    $result = $DB->query($sql);
    if ($DB->num_rows($result)) {
      $row = $DB->get_array($result);
      for ($i = 0; $i < count($row); $i ++) {
        $row_info = $this->render_row($row[$i], $lang);
        $row_field[$i] = $row_info;
        $row_field[$i]['stt'] = ($i + 1);
        $row_field[$i]['row_id'] = "row_" . $row[$i]['gid'];
        $row_field[$i]['ext'] = "";
      }
      $table['row'] = $row_field;
    } else {
      $table['row'] = array();
      $table['extra'] = "<div align=center class=font_err >" . $vnT->lang['no_have_group_admin'] . "</div>";
    }
    //$table['button'] = '<input type="button" name="btnHidden" value=" ' . $vnT->lang['hidden'] . ' " class="button" onclick="do_submit(\'do_hidden\')">&nbsp;';
    //$table['button'] .= '<input type="button" name="btnDisplay" value=" ' . $vnT->lang['display'] . ' " class="button" onclick="do_submit(\'do_display\')">&nbsp;';
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