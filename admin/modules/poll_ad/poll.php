<?php
/*================================================================================*\
|| 							Name code : modname.php 		 		            	  ||
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
  var $module = "poll";
  var $action = "poll";

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
    $vnT->html->addScript("modules/poll_ad/js/poll.js");
    switch ($vnT->input['sub']) {
      case 'add':
        $nd['f_title'] = $vnT->lang['add_poll'];
        $nd['content'] = $this->do_Add($lang);
      break;
      case 'edit':
        $nd['f_title'] = $vnT->lang['edit_poll'];
        $nd['content'] = $this->do_Edit($lang);
      break;
      case 'del':
        $this->do_Del($lang);
      break;
      default:
        $nd['f_title'] = $vnT->lang['manage_poll'];
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
   * Them  moi 
   **/
  function do_Add ($lang)
  {
    global $vnT, $func, $DB, $conf;
    //load Format
    $func->include_libraries('vntrust.html.format');
    $err = "";
    $num = 5;
    if ($vnT->input['do_submit'] == 1) {
      $data = $_POST;
      $num = $vnT->input['num'];
      $pollerTitle = $vnT->input['pollerTitle'];
      $optionTexts = $vnT->input['optionText'];
      //upload
      if ($vnT->input['chk_upload'] && ! empty($_FILES['image']) && $_FILES['image']['name'] != "") {
        $up['path'] = MOD_DIR_UPLOAD;
        $up['dir'] = "";
        $up['file'] = $_FILES['image'];
        $up['type'] = "hinh";
        $up['w'] = 500;
        $result = $vnT->File->Upload($up);
        if (empty($result['err'])) {
          $picture = $result['link'];
          $file_type = $result['type'];
        } else {
          $err = $func->html_err($result['err']);
        }
      } else {
        if ($vnT->input['picture']) {
          $up['path'] = MOD_DIR_UPLOAD;
          $up['dir'] = "";
          $up['url'] = $vnT->input['picture'];
          $up['type'] = "hinh";
          $up['w'] = 500;
          $result = $vnT->File->UploadURL($up);
          if (empty($result['err'])) {
            $picture = $result['link'];
            $file_type = $result['type'];
          } else {
            $err = $func->html_err($result['err']);
          }
        }
      } //end upload
      // insert CSDL
      if (empty($err)) {
        $res = $DB->query("select name from language ");
        while ($r = $DB->fetch_row($res)) {
          $arr_title[$r['name']] = $pollerTitle;
        }
        $cot['pollerTitle'] = serialize($arr_title);
        $cot['picture'] = $picture;
        $cot['multiple'] = $vnT->input["multiple"];
        $ok = $DB->do_insert("poller", $cot);
        if ($ok) {
          $poll_id = $DB->insertid();
          // add option
          $i = 0;
          foreach ($optionTexts as $optionText) {
            $i ++;
            if ($optionText != "") {
              $cot_op['pollerID'] = $poll_id;
              $cot_op['pollerOrder'] = $i;
              $res = $DB->query("select * from language ");
              while ($r = $DB->fetch_row($res)) {
                $tmp1[$r['name']] = $optionText;
              }
              $cot_op['optionText'] = serialize($tmp1);
              $DB->do_insert("poller_option", $cot_op);
            }
          }
          //xoa cache
          $func->clear_cache();
          //insert adminlog
          $func->insertlog("Add", $_GET['act'], $poll_id);
          $mess = $vnT->lang['add_success'];
          $url = $this->linkUrl . "&sub=add";
          $func->html_redirect($url, $mess);
        } else {
          $err = $func->html_err($vnT->lang['add_failt'] . $DB->debug());
        }
      }
    }
    for ($i = 1; $i <= $num; $i ++) {
      $j = $i - 1;
      $row['stt'] = $i;
      $row['optionText'] = $vnT->input['optionText'][$j];
      $this->skin->assign('row', $row);
      $this->skin->parse("edit.html_row");
    }
    $data['num'] = $num;
    $data['poll_noquestion'] = $num;
    $data['list_multiple'] = vnT_HTML::list_yesno("multiple", $vnT->input['multiple']);
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
    //load Format
    $func->include_libraries('vntrust.html.format');
    $vnT->format = new vnT_Format();
    $id = (int) $vnT->input['id'];
    if ($vnT->input['do_submit']) {
      $data = $_POST;
      $num = $vnT->input['num'];
      $pollerTitle = $vnT->input['pollerTitle'];
      //upload
      if ($vnT->input['chk_upload'] && ! empty($_FILES['image']) && $_FILES['image']['name'] != "") {
        $up['path'] = MOD_DIR_UPLOAD;
        $up['dir'] = "";
        $up['file'] = $_FILES['image'];
        $up['type'] = "hinh";
        $up['w'] = 500;
        $result = $vnT->File->Upload($up);
        if (empty($result['err'])) {
          $picture = $result['link'];
          $file_type = $result['type'];
        } else {
          $err = $func->html_err($result['err']);
        }
      } else {
        if ($vnT->input['picture']) {
          $up['path'] = MOD_DIR_UPLOAD;
          $up['dir'] = "";
          $up['url'] = $vnT->input['picture'];
          $up['type'] = "hinh";
          $up['w'] = 500;
          $result = $vnT->File->UploadURL($up);
          if (empty($result['err'])) {
            $picture = $result['link'];
            $file_type = $result['type'];
          } else {
            $err = $func->html_err($result['err']);
          }
        }
      } //end upload
      if (empty($err)) {
        $res = $DB->query("select name from language ");
        while ($r = $DB->fetch_row($res)) {
          $arr_title[$r['name']] = $pollerTitle;
        }
        $cot['pollerTitle'] = $func->update_content("poller", "pollerTitle", "id=$id ", $lang, $pollerTitle);
        $cot['multiple'] = $vnT->input["multiple"];
        if ($vnT->input['chk_upload'] == 1 || ! empty($picture)) {
          $img_q = $DB->query("SELECT picture FROM poller WHERE id=$id ");
          if ($img = $DB->fetch_row($img_q)) {
            $file_pic = MOD_DIR_UPLOAD . $img['picture'];
            if ((file_exists($file_pic)) && (! empty($img['picture'])))
              unlink($file_pic);
          }
          $cot['picture'] = $picture;
        }
        $ok = $DB->do_update("poller", $cot, "id=$id");
        if ($ok) {
          $num_od = (int) $vnT->input['num_old'];
          $optionTexts = $vnT->input['optionText'];
          $arr_answers = $vnT->input['answers'];
          $arr_votes = $vnT->input['votes'];
          //update option
          if (is_array($arr_answers)) {
            foreach ($arr_answers as $key => $value) {
              $dup['optionText'] = $func->update_content("poller_option", "optionText", "id=$key ", $lang, $value);
              $dup['vote'] = $arr_votes[$key];
              $DB->do_update("poller_option", $dup, "id=$key");
            }
          }
          // add option
          if (is_array($optionTexts)) {
            $i = 0;
            foreach ($optionTexts as $optionText) {
              $i ++;
              if ($optionText != "") {
                $cot_op['pollerID'] = $id;
                $cot_op['pollerOrder'] = ($num_od + $i);
                $res = $DB->query("select * from language ");
                while ($r = $DB->fetch_row($res)) {
                  $tmp1[$r['name']] = $optionText;
                }
                $cot_op['optionText'] = serialize($tmp1);
                $DB->do_insert("poller_option", $cot_op);
              }
            }
          }
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
    $query = $DB->query("SELECT * FROM poller WHERE id=" . $id);
    if ($data = $DB->fetch_row($query)) {
      $data['pollerTitle'] = $func->fetch_content($data['pollerTitle'], $lang);
      if (! empty($data['picture'])) {
        $data['pic'] = "<img src='" . MOD_DIR_UPLOAD . $data['picture'] . "' ><br>";
      }
      $res_anwser = $DB->query("SELECT * FROM poller_option WHERE pollerID=" . $id . " order by pollerOrder ASC , id DESC ");
      $num = $DB->num_rows($res_anwser);
      $data['num_old'] = $num;
      $i = 0;
      while ($poll_answers = $DB->fetch_row($res_anwser)) {
        $i ++;
        $poll_answer[$i]['vote'] = $poll_answers['vote'];
        $poll_answer[$i]['op_id'] = $poll_answers['id'];
        $poll_answer[$i]['optionText'] = $func->fetch_content($poll_answers['optionText'], $lang);
      }
    } else {
      $mess = $vnT->lang['not_found'] . " ID : " . $id;
      $url = $this->linkUrl;
      $func->html_redirect($url, $mess);
    }
    for ($i = 1; $i <= $num; $i ++) {
      $row['stt'] = $i;
      $row['op_id'] = $poll_answer[$i]['op_id'];
      $row['vote'] = $poll_answer[$i]['vote'];
      $row['optionText'] = $poll_answer[$i]['optionText'];
      $row['btn_del'] = '<input type="button" class="button" onclick="delete_poll_ans(' . $id . ', ' . $poll_answer[$i]['op_id'] . ', ' . $i . ', \'' . $vnT->format->js_escape($vnT->lang['are_you_sure_del']) . '\');" value="Delete"/>';
      $this->skin->assign('row', $row);
      $this->skin->parse("edit.html_rowold");
    }
    $data['num'] = $num;
    $data['poll_noquestion'] = $num;
    $data['list_multiple'] = vnT_HTML::list_yesno("multiple", $vnT->input['multiple']);
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
      $ids = $id;
    }
    if (isset($vnT->input["del_id"])) {
      $ids = implode(',', $vnT->input["del_id"]);
    }
    $query = 'DELETE FROM poller WHERE id IN (' . $ids . ')';
    if ($ok = $DB->query($query)) {
      $DB->query("DELETE FROM poller_option WHERE pollerID=$poll_id");
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
    $link_edit = $this->linkUrl . '&sub=edit&id=' . $row['id'];
    $link_del = "javascript:del_item('" . $this->linkUrl . "&sub=del&id=" . $id . "')";
    $output['order'] = "<input name=\"txt_Order[{$id}]\" type=\"text\" size=\"2\" maxlength=\"2\" style=\"text-align:center\" value=\"{$row['p_order']}\"  onkeypress=\"return is_num(event,'txtOrder')\" onchange='javascript:do_check($id)' />";
    if ($row['picture']) {
      $output['picture'] = "<img src=\"" . MOD_DIR_UPLOAD . $row['picture'] . "\" width=50 />";
    } else
      $output['picture'] = "No image";
    $output['pollerTitle'] = $func->fetch_content($row['pollerTitle'], $lang);
    $output['num_vote'] = intval($row['num_vote']);
    $link_display = $this->linkUrl.$row['ext_link']; 		
    if ($row['display'] == 1) {
      $display = "<a href='".$link_display."&do_hidden=$id' title='".$vnT->lang['click_do_hidden']."' ><img src=\"" . $vnT->dir_images . "/dispay.gif\" width=15  /></a>";
    } else {
      $display = "<a href='".$link_display."&do_display=$id'  title='".$vnT->lang['click_do_display']."' ><img src=\"" . $vnT->dir_images . "/nodispay.gif\"  width=15 /></a>";
    }
    $output['action'] = '<input name=h_id[]" type="hidden" value="' . $id . '" />';
    $output['action'] .= '<a href="' . $link_edit . '"><img src="' . $vnT->dir_images . '/edit.gif"  alt="Edit "></a>&nbsp;';
    $output['action'] .= $display . '&nbsp;';
    $output['action'] .= '<a href="' . $link_del . '"><img src="' . $vnT->dir_images . '/delete.gif"  alt="Delete "></a>';
    return $output;
  }

  /**
   * function do_Manage() 
   * Quan ly option
   **/
  function do_Manage ($lang)
  {
    global $vnT, $func, $DB, $conf;
    $poll_id = ($vnT->input['poll_id']) ? $vnT->input['poll_id'] : 0;
    //update
    if ($vnT->input["do_action"]) {
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
            $dup['p_order'] = $arr_order[$h_id[$i]];
            $ok = $DB->do_update("poller", $dup, "id=" . $h_id[$i]);
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
            $ok = $DB->do_update("poller", $dup, "id=" . $h_id[$i]);
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
            $ok = $DB->do_update("poller", $dup, "id=" . $h_id[$i]);
            if ($ok) {
              $str_mess .= $h_id[$i] . ", ";
            }
          }
          $mess .= substr($str_mess, 0, - 2) . "</strong><br>";
          $err = $func->html_mess($mess);
        break;
      }
    }
		if((int)$vnT->input["do_display"]) {				
			$ok = $DB->query("Update poller SET display=1 WHERE id=".$vnT->input["do_display"]);
			if($ok){
				$mess .= "- " . $vnT->lang['display_success'] . "  ID: <strong>".$vnT->input["do_display"] . "</strong><br>";	
				$err = $func->html_mess($mess);
			}        
			//xoa cache
      $func->clear_cache();
		}
		if((int)$vnT->input["do_hidden"]) {				
			$ok = $DB->query("Update poller SET display=0 WHERE id=".$vnT->input["do_hidden"]);
			if($ok){
				$mess .= "- " . $vnT->lang['display_success'] . "  ID: <strong>".$vnT->input["do_hidden"] . "</strong><br>";	
				$err = $func->html_mess($mess);
			}    
			//xoa cache
      $func->clear_cache();
		}
		
    $p = ((int) $vnT->input['p']) ? $p = $vnT->input['p'] : 1;
    $n = ($conf['record']) ? $conf['record'] : 30;
    $query = $DB->query("SELECT id FROM poller ");
    $totals = intval($DB->num_rows($query));
    $num_pages = ceil($totals / $n);
    if ($p > $num_pages)
      $p = $num_pages;
    if ($p < 1)
      $p = 1;
    $start = ($p - 1) * $n;
    $nav = $func->paginate($totals, $n, $ext, $p);
    $table['link_action'] = $this->linkUrl . "{$ext}&p=$p"; 
		$ext_link = $ext."&p=$p" ;
		
    $table['title'] = array(
      'check_box' => "<input type=\"checkbox\" name=\"checkall\" id=\"checkall\" class=\"checkbox\" />|5%|center" , 
      'order' => $vnT->lang['order'] . "|10%|center" , 
      'picture' => $vnT->lang['picture'] . "|15%|center" , 
      'pollerTitle' => $vnT->lang['poll_option'] . "|50%|left" , 
      'num_vote' => "Num Vote |10%|center" , 
      'action' => "Action|15%|center");
    $sql = "SELECT * FROM poller ORDER BY p_order ASC, id DESC LIMIT $start,$n ";
    //print "sql = ".$sql."<br>";
    $result = $DB->query($sql);
    if ($DB->num_rows($result)) {
      $row = $DB->get_array($result);
      for ($i = 0; $i < count($row); $i ++) {
				$row[$i]['ext_link'] = $ext_link ;
				$row[$i]['ext_page'] = $ext_page;
        $row_info = $this->render_row($row[$i], $lang);
        $row_field[$i] = $row_info;
        $row_field[$i]['stt'] = ($i + 1);
        $row_field[$i]['row_id'] = "row_" . $row[$i]['id'];
        $row_field[$i]['ext'] = "";
      }
      $table['row'] = $row_field;
    } else {
      $table['row'] = array();
      $table['extra'] = "<div align=center class=font_err >" . $vnT->lang['pls_select_poll_title'] . "</div>";
    }
    $table['button'] = '<input type="button" name="btnHidden" value=" ' . $vnT->lang['hidden'] . ' " class="button" onclick="do_submit(\'do_hidden\')">&nbsp;';
    $table['button'] .= '<input type="button" name="btnDisplay" value=" ' . $vnT->lang['display'] . ' " class="button" onclick="do_submit(\'do_display\')">&nbsp;';
    $table['button'] .= '<input type="button" name="btnEdit" value=" ' . $vnT->lang['update'] . ' " class="button" onclick="do_submit(\'do_edit\')">&nbsp;';
    $table['button'] .= '<input type="button" name="btnDel" value=" ' . $vnT->lang['delete'] . ' " class="button" onclick="del_selected(\'' . $this->linkUrl . '&sub=del&poll_id=' . $poll_id . '\')">';
    $table_list = $func->ShowTable($table);
    $data['table_list'] = $table_list;
    $data['totals'] = $totals;
    $data['ext_button'] = ' <a href="' . $this->linkUrl . '&sub=edit&id=' . $poll_id . '"><img src="' . $vnT->dir_images . '/edit.gif" width="16"  alt="Edit poller" align="absmiddle"></a>&nbsp;
	  <a href="javascript:del_item(\'' . $this->linkUrl . '&sub=del&poll_id=' . $poll_id . '\')" ><img src="' . $vnT->dir_images . '/delete.gif" width="16"  alt="Delete poller" align="absmiddle"></a>';
    $data['err'] = $err;
    /*assign the array to a template variable*/
    $this->skin->assign('data', $data);
    $this->skin->parse("manage");
    return $this->skin->text("manage");
  }
  // end class
}
?>