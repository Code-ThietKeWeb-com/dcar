<?php
/*================================================================================*\
|| 							Name code : layout.php 		 		            	  ||
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
  var $module = "layout";
  var $action = "layout";

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
    $this->linkUrl = "?mod=layout&act=layout";
    $vnT->html->addScript($vnT->dir_js . "/admin/layout.js");
    
		switch ($vnT->input['sub']) {
      case 'add':
        $nd['f_title'] = $vnT->lang['add_layout'];
        $nd['content'] = $this->do_Add();
      break;
      case 'edit':
        $nd['f_title'] = $vnT->lang['edit_layout'];
        $nd['content'] = $this->do_Edit();
      break;
      case 'list_block':
        $nd['f_title'] = "List Block";
        $nd['content'] = $this->do_List();
      break;
      case 'config_block':
        $nd['f_title'] = "config block";
        $nd['content'] = $this->do_Config_Block();
      break;
      case 'del':
        $this->do_Del();
      break;
      default:
        $nd['f_title'] = $vnT->lang['manage_layout'];
        $nd['content'] = $this->do_Manage();
      break;
    }
    $nd['menu'] = getToolbar();
    $Template->assign("data", $nd);
    $Template->parse("box_main");
    $vnT->output .= $Template->text("box_main");
  }

  //========================================== do_Add
  function do_Add ()
  {
    global $vnT, $func, $DB, $conf;
    if (! empty($_POST['do_submit'])) {
      $data = $_POST;
      $name = trim($data['name']);
      $title = $func->txt_HTML($data['title']);
      $type = $data['type'];
      $arr_page = $_POST['module_show'];
      $module_show = implode(",", $arr_page);
      // Check for Error
      $query = $DB->query("SELECT name FROM layout WHERE name='{$name}' ");
      if ($check = $DB->fetch_row($query))
        $err = "Block Name existed";
        // End check
      if (empty($err)) {
        $cot['name'] = $name;
        $cot['title'] = $title;
        $cot['content'] = $DB->mySQLSafe($_POST['content']);
        $cot['type'] = 1;
        $cot['module_show'] = $module_show;
        $cot['align'] = $_POST['align'];
        $cot['cache'] = $_POST['cache'];
        $cot['l_order'] = 100;
        $ok = $DB->do_insert("layout", $cot);
        if ($ok) {
          //xoa cache
          $func->clear_cache();
          //insert adminlog
          $func->insertlog("Add", $_GET['act'], $DB->insertid());
          $err = $vnT->lang["add_success"];
          $url = $this->linkUrl . "&sub=add";
          $func->html_redirect($url, $err);
        } else
          $err = $func->html_err($vnT->lang["add_failt"]);
      }
    }
    $data["html_content"] = $vnT->editor->doDisplay('content', $vnT->input['content'], '100%', '500', "Default");
    $data['err'] = $err;
    $data['list_type'] = "HTML BLOCK";
    $data['list_align'] = List_Align($align);
    $data['list_cache'] = vnT_HTML::list_yesno("cache", $_POST['cache']);
    $data['list_module_show'] = List_Module_show($module_show);
    $data['link_action'] = $this->linkUrl . "&sub=add";
		
		/*assign the array to a template variable*/
    $this->skin->assign('data', $data);
    $this->skin->parse("edit");
    return $this->skin->text("edit");
		
     
  }

  //================================================
  function do_Edit ()
  {
    global $vnT, $func, $DB, $conf;
    $err = "";
    $id = (int) $vnT->input['id'];
		
    if (! empty($_POST['do_submit'])) {
      $data = $_POST;
      $name = trim($data['name']);
      $title = $func->txt_HTML($data['title']);
      $type = $data['type'];
      $arr_page = $_POST['module_show'];
      $module_show = implode(",", $arr_page);
      $dataup['name'] = $name;
      $dataup['title'] = $title;
      $dataup['content'] = $DB->mySQLSafe($_POST['content']);
      $dataup['align'] = $_POST['align'];
      $dataup['cache'] = $_POST['cache'];
      $dataup['module_show'] = $module_show;
      if (empty($err)) {
        $ok = $DB->do_update("layout", $dataup, "id=$id");
        if ($ok) {
          //xoa cache
          $func->clear_cache();
          $err = $vnT->lang["edit_success"];
          $url = $this->linkUrl;
          $func->html_redirect($url, $err);
        } else
          $err = $func->html_err($vnT->lang["edit_failt"]);
      }
    }
    $sql = "SELECT * FROM layout WHERE id=$id ";
    $result = $DB->query($sql);
    if ($row = $DB->fetch_row($result)) {
      if ($row['type'] == 0) {
        $row['style_des'] = "style='display:none'";
      }
      $list_type = ($row['type'] == 0) ? "PHP BLOCK" : "HTML BLOCK";
    }
    $row['err'] = $err;
    $row["html_content"] = $vnT->editor->doDisplay('content', $row['content'], '100%', '500', "Default");
    $row['list_type'] = $list_type;
    $row['list_align'] = List_Align($row['align']);
    $row['list_cache'] = vnT_HTML::list_yesno("cache", $row['cache']);
    $row['list_module_show'] = List_Module_show($row['module_show']);
    $row['link_action'] = $this->linkUrl . "&sub=edit&id=$id";
    /*assign the array to a template variable*/
    $this->skin->assign('data', $row);
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
      $ids = $id;
    }
    if (isset($vnT->input["del_id"])) {
      $ids = implode(',', $vnT->input["del_id"]);
    }
    $query = 'DELETE FROM layout WHERE id IN (' . $ids . ')';
    if ($ok = $DB->query($query)) {
       //xoa cache
      $func->clear_cache();
      $mess = $vnT->lang["del_success"];
    } else
      $mess = $vnT->lang["del_failt"];
    $ext_page = str_replace("|", "&", $ext);
    $url = $this->linkUrl . "&sub=list_block";
    $func->html_redirect($url, $mess);
  }
	
 
  //=======do_Manage
  function do_Manage ()
  {
    global $vnT, $func, $DB, $conf;
    $mess = "";
    if (isset($_POST["btnEdit"])) {
      $sql = "select * from layout ";
      $result = $DB->query($sql);
      while ($row_up = $DB->fetch_row($result)) {
        $name = $row_up['name'];
        $value = intval($_POST[$name]);
        $sql_up = "UPDATE layout SET l_show=$value WHERE name ='{$name}' ";
        $kq = $DB->query($sql_up);
      }
      //insert adminlog
      $func->insertlog("On/Off ", $_GET['act'], 1);
      $err = "C&#7853;p nh&#7853;t th&#224;nh c&#244;ng";
      $url = $this->linkUrl;
      $func->html_redirect($url, $err);
    }
		
		$module = $vnT->input['module'] ;		 
    if ($module) {
      $where = " and  (FIND_IN_SET('$module',module_show) or (module_show='') ) ";
    }
    // LEFT
    $box_left = "";
    $sql = "select * from layout where align='left' $where order by l_order ";
    $result = $DB->query($sql);
    while ($row = $DB->fetch_row($result)) {
      $row['r_check'] = List_Check($row['name'], $row['l_show']);
      $box_left .= '<tr>
										<td width="50%" align="right">'.$row['title'].'</td>
										<td width="50%" align="left">'.$row['r_check'].'</td>
									</tr>';
    }
    // RIGHT
    $box_right = "";
    $sql = "select * from layout where align='right' $where order by l_order ";
    $result = $DB->query($sql);
    while ($row = $DB->fetch_row($result)) {
      $row['r_check'] = List_Check($row['name'], $row['l_show']);
      $box_right .= '<tr>
										<td width="50%" align="right">'.$row['title'].'</td>
										<td width="50%" align="left">'.$row['r_check'].'</td>
									</tr>';
    }
    $data['err'] = $err;
    $data['box_left'] = $box_left;
    $data['box_middle'] = $box_middle;
    $data['box_right'] = $box_right;
    $data['list_module'] = List_Module($module, "onChange='submit();'");
		
		 /*assign the array to a template variable*/
    $this->skin->assign('data', $data);
    $this->skin->parse("manage");
    return $this->skin->text("manage");
		 
  }

  //========================================== do_Config_Block
  function do_Config_Block ()
  {
    global $vnT, $func, $DB, $conf;
    $mess = "";
    $module = $vnT->input['module'] ;
    if ($module) {
      $where = " and  (FIND_IN_SET('$module',module_show) or (module_show='') ) ";
    }
    if (isset($_POST["btnSubmit"])) {
      //LEFT
      $layout_l = $_POST['layout_l'];
      $a_left = explode(",", $layout_l);
      for ($i = 0; $i < count($a_left); $i ++) {
        $id = $a_left[$i];
        $l_order = $i + 1;
        $sql_up = "UPDATE layout SET align='left',l_order=$l_order where id=$id ";
        $DB->query($sql_up);
      }
      //RIGHT
      $layout_r = $_POST['layout_r'];
      $a_right = explode(",", $layout_r);
      for ($i = 0; $i < count($a_right); $i ++) {
        $id = $a_right[$i];
        $l_order = $i + 1;
        $sql_up = "UPDATE layout SET align='right',l_order=$l_order where id=$id ";
        $DB->query($sql_up);
      }
      //insert adminlog
      $func->insertlog("Organize Blocks ", $_GET['act'], 1);
      $err = " Organize Blocks Successfull ";
      $url = $this->linkUrl .= "&module=" . $module;
      $func->html_redirect($url, $err);
    }
    // LEFT
    $option_left = "";
    $sql = "select * from layout where align='left' and l_show=1 $where order by l_order ";
    $result = $DB->query($sql);
    while ($row = $DB->fetch_row($result)) {
      $option_left .= "<option value=\"{$row['id']}\">{$row['title']}</option>";
    }
    // RIGHT
    $option_right = "";
    $sql = "select * from layout where align='right' and l_show=1 $where  order by l_order ";
    $result = $DB->query($sql);
    while ($row = $DB->fetch_row($result)) {
      $option_right .= "<option value=\"{$row['id']}\">{$row['title']}</option>";
    }
    $data['err'] = $err;
    $data['option_left'] = $option_left;
    $data['option_right'] = $option_right;
    $data['list_module'] = List_Module($module, "onChange='submit();'");
    $data['link_action'] = $this->linkUrl .= "&sub=config_block&module=" . $module;
		
		 /*assign the array to a template variable*/
    $this->skin->assign('data', $data);
    $this->skin->parse("config_layout");
    return $this->skin->text("config_layout");

  }

  //================
  function render_row ($row_info)
  {
    global $func, $DB, $conf, $vnT;
    $row = $row_info;
    // Xu ly tung ROW
    $id = $row['id'];
    $row_id = "row_" . $id;
    $output['check_box'] = vnT_HTML::checkbox("del_id[]", $id, 0, " ");
    $link_edit = $this->linkUrl . '&sub=edit&id=' . $id;
    $link_del = "javascript:del_item('" . $this->linkUrl . "&sub=del&id=" . $id . "')";
    $output['name'] = "<a href=\"{$link_edit}\"><strong>" . $row['name'] . "</strong></a>";
    $output['title'] = $row['title'];
    $output['align'] = $row['align'];
    $output['type'] = ($row['type'] == 0) ? "PHP BLOCK" : "HTML BLOCK";
    $output['module_show'] = ($row['module_show'] == '') ? "All modules" : $row['module_show'];
    if ($row['l_show'] == 1) {
      $display = "<img src=\"{$vnT->dir_images}/dispay.gif\" width=15  />";
    } else {
      $display = "<img src=\"{$vnT->dir_images}/nodispay.gif\"  width=15 />";
    }
    $output['action'] = "
		<input name=\"h_id[]\" type=\"hidden\" value=\"{$id}\" />
		{$display}&nbsp;
		<a href=\"{$link_edit}\"><img src=\"{$vnT->dir_images}/edit.gif\"  alt=\"Edit \"></a>&nbsp;";
    if ($row['type'] == 1)
      $output['action'] .= "<a href=\"{$link_del}\"><img src=\"{$vnT->dir_images}/delete.gif\"  alt=\"Delete \"></a>";
    return $output;
  }

  //================================================
  function do_List ()
  {
    global $vnT, $func, $DB, $conf;
    $list = "";
    //update
    if ($vnT->input["do_action"]) {
      //xoa cache
      $func->clear_cache();
      if ($vnT->input["del_id"])
        $h_id = $vnT->input["del_id"];
      switch ($vnT->input["do_action"]) {
        case "do_hidden":
          $mess .= "- " . $vnT->lang['hidden_success'] . " ID: <strong>";
          for ($i = 0; $i < count($h_id); $i ++) {
            $dup['l_show'] = 0;
            $ok = $DB->do_update("layout", $dup, "id=" . $h_id[$i]);
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
            $dup['l_show'] = 1;
            $ok = $DB->do_update("layout", $dup, "id=" . $h_id[$i]);
            if ($ok) {
              $str_mess .= $h_id[$i] . ", ";
            }
          }
          $mess .= substr($str_mess, 0, - 2) . "</strong><br>";
          $err = $func->html_mess($mess);
        break;
      }
    }
    $table['link_action'] = $this->linkUrl . "&sub=list_block";
    $table['title'] = array(
      'check_box' => "<input type=\"checkbox\" name=\"checkall\" id=\"checkall\" class=\"checkbox\" />|5%|center" , 
      'name' => "T&#234;n |10%|center" , 
      'title' => "Ti&#234;u &#273;&#7873; |35%|center" , 
      'type' => "Loại|10%|center" , 
      'align' => "Vi tr&#237;|10%|center" , 
      'module_show' => "Module Show|20%|center" , 
      'action' => "Action|15%|center");
    $sql = "SELECT * FROM layout  ORDER BY align,l_order  ";
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
      $table['extra'] = "<div align=center class=font_err >" . $vnT->lang['no_have_block'] . "</div>";
    }
    $table['button'] = '<input type="button" name="btnHidden" value=" ' . $vnT->lang['hidden'] . ' " class="button" onclick="do_submit(\'do_hidden\')">&nbsp;';
    $table['button'] .= '<input type="button" name="btnDisplay" value=" ' . $vnT->lang['display'] . ' " class="button" onclick="do_submit(\'do_display\')">&nbsp;';
    $table_list = $func->ShowTable($table);
    $data['table_list'] = $table_list;
    $data['totals'] = $totals;
    $data['err'] = $err;
    $data['nav'] = $nav;
		
		/*assign the array to a template variable*/
    $this->skin->assign('data', $data);
    $this->skin->parse("list_block");
    return $this->skin->text("list_block");
		
  }
  // end class
}
?>