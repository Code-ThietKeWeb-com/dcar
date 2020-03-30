<?php
/*================================================================================*\
|| 							Name code : cat_product.php 		 		            	  ||
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
  var $action = "template_xml";

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
        $nd['f_title'] = "Thêm mới";
        $nd['content'] = $this->do_Add($lang);
        break;
      case 'edit':
        $nd['f_title'] = "Cập nhật";
        $nd['content'] = $this->do_Edit($lang);
        break;
      case 'build_xml':
        $this->do_BuildXml($lang);
        break;
      case 'del':
        $this->do_Del($lang);
        break;
      default:
        $nd['f_title'] = "Quản lý mẫu nội dung";
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
    $err = "";
    $vnT->input['display'] = 1;
    if ($vnT->input['do_submit'] == 1) {



      //upload
      if( $vnT->input['chk_upload'] && !empty($_FILES['image']) && $_FILES['image']['name']!="" ) {
        $up['path'] = '../vnt_upload/ckeditor/';
        $up['dir']= "";
        $up['file']= $_FILES['image'];
        $up['type']= "hinh";
        $up['w']= 500;

        $result = $vnT->File->Upload($up);
        if (empty($result['err'])) {
          $picture = $result['link'];
        } else {
          $err = $func->html_err($result['err']);
        }
      }else {
        if( $vnT->input['picture'] ) {
          $up['path'] = '../vnt_upload/ckeditor/';
          $up['dir'] = "";
          $up['url'] = $vnT->input['picture'];
          $up['type'] = "hinh";
          $up['w']= 500;

          $result = $vnT->File->UploadURL($up);
          if (empty($result['err'])) {
            $picture = $result['link'];
          } else {
            $err = $func->html_err($result['err']);
          }
        }
      }//end upload

      if (empty($err)) {
        $cot['title'] =  $func->txt_HTML($_POST['title']);
        $cot['picture'] =  $picture;
        $cot['description'] =  $func->txt_HTML($_POST['description']);
        $cot['content_html'] =  $DB->mySQLSafe($_POST['content_html']);
        $cot['content_css'] =  $func->txt_HTML($_POST['content_css']);
        $cot['display'] = $vnT->input['display'];
        $cot['date_post'] = time();
        $cot['date_update'] =time();

        $ok = $DB->do_insert("template_xml", $cot);
        if ($ok) {
          //$this->do_BuildXml($lang);
          //xoa cache
          $func->clear_cache();
          $mess = $vnT->lang['add_success'];
          $url = $this->linkUrl . "&sub=add";
          $func->html_redirect($url, $mess);
        } else {
          $err = $func->html_err($vnT->lang['add_failt'] . $DB->debug());
        }
      }
    }
    $data['list_display'] = vnT_HTML::list_yesno("display", $vnT->input['display']);
    $data["html_content"] = $vnT->editor->doDisplay('content_html', $data['content_html'], '100%', '500');
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

      //upload
      if( $vnT->input['chk_upload'] && !empty($_FILES['image']) && $_FILES['image']['name']!="" ) {
        $up['path'] = '../vnt_upload/ckeditor/';
        $up['dir']= "";
        $up['file']= $_FILES['image'];
        $up['type']= "hinh";
        $up['w']= 500;

        $result = $vnT->File->Upload($up);
        if (empty($result['err'])) {
          $picture = $result['link'];
        } else {
          $err = $func->html_err($result['err']);
        }
      }else {
        if( $vnT->input['picture'] ) {
          $up['path'] = '../vnt_upload/ckeditor/';
          $up['dir'] = "";
          $up['url'] = $vnT->input['picture'];
          $up['type'] = "hinh";
          $up['w']= 500;

          $result = $vnT->File->UploadURL($up);
          if (empty($result['err'])) {
            $picture = $result['link'];
          } else {
            $err = $func->html_err($result['err']);
          }
        }
      }//end upload


      if (empty($err)) {
        $cot['title'] =  $func->txt_HTML($_POST['title']);
        if ($vnT->input['chk_upload']==1 || !empty($picture) )
        {
          $cot['picture']=$picture;
        }

        $cot['description'] =  $func->txt_HTML($_POST['description']);
        $cot['content_html'] =  $DB->mySQLSafe($_POST['content_html']);
        $cot['content_css'] =  $func->txt_HTML($_POST['content_css']);
        $cot['display'] = $vnT->input['display'];
        $cot['date_update'] =time();

        $ok = $DB->do_update("template_xml", $cot, "tpl_id=$id");
        if ($ok) {
          //$this->do_BuildXml($lang);

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
    $query = $DB->query("SELECT * FROM template_xml WHERE tpl_id=$id");
    if ($data = $DB->fetch_row($query)) {

      if ($data['picture']) {
        $data['pic'] = " <img src=\"".$vnT->conf['rooturl']."vnt_upload/ckeditor/".$data['picture']."\" width=100 /><br>";
      } else {
        $data['pic'] = "";
      }

    } else {
      $mess = $vnT->lang['not_found'] . " ID : " . $id;
      $url = $this->linkUrl;
      $func->html_redirect($url, $mess);
    }
    $data['err'] = $err;
    $data["html_content"] = $vnT->editor->doDisplay('content_html', $data['content_html'], '100%', '500');
    $data['list_display'] = vnT_HTML::list_yesno("display", $data['display']);
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
    $query = 'DELETE FROM template_xml WHERE tpl_id IN (' . $ids . ')';
    if ($ok = $DB->query($query)) {
      $this->do_BuildXml($lang);
      $mess = $vnT->lang["del_success"];
    } else
      $mess = $vnT->lang["del_failt"];
    $ext_page = str_replace("|", "&", $ext);
    $url = $this->linkUrl . "&{$ext_page}";
    $func->html_redirect($url, $mess);
  }


  /**
   * function do_BuildXml
   **/
  function do_BuildXml ($lang)
  {
    global $func, $DB, $conf, $vnT;
    $out = array();
    $mess = '';
    $ok = 1;
    $content_xml ='<?xml version="1.0" encoding="UTF-8"?>';
    $content_xml .='<temp>';
    $result = $DB->query("SELECT * FROM template_xml WHERE display=1 ORDER BY display_order ASC, tpl_id DESC");
    while($row = $DB->fetch_row($result))
    {
      $picture = ($row['picture']) ? $row['picture'] : "template1.gif";
      $content ='';
      if($row['content_css']){
        $content .= '<div><style>';
        $content_css = $func->txt_unHTML($row['content_css']);

        $content_css = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $content_css);
        $content_css = str_replace(': ', ':', $content_css);
        $content_css = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $content_css);

        $content .= $content_css ;
        $content .= '</style></div>';
      }
      $content .= trim($func->txt_unHTML($row['content_html']));



      $item_xml = '<item>'."\n";
      $item_xml .= '<title>'.htmlspecialchars($row['title']).'</title>'."\n";
      $item_xml .= '<image>'.$picture.'</image>'."\n";
      $item_xml .= '<description>'.htmlspecialchars($row['description']).'</description>'."\n";
      $item_xml .= '<html>'. $content .'</html>'."\n";
      $item_xml .= '</item>'."\n";

      $content_xml .= $item_xml ;
    }
    $content_xml .= '</temp>';

    $path = "../vnt_upload/ckeditor/template.xml";
    if($handle = @fopen($path, "w")){
      fwrite($handle, $content_xml, strlen($content_xml));
      fclose($handle);
      $mess =   "Cập nhật thành công";
    }else{
      $ok = 0 ;
      $mess =  $func->html_err("Khong mo duoc file template.xml ");
    }

    $out['ok'] = $ok;
    $out['mess'] = $mess;
    return $out;
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
    $id = $row['tpl_id'];
    $row_id = "row_" . $id;
    $output['row_id'] = $row_id;
    $output['check_box'] = vnT_HTML::checkbox("del_id[]", $id, 0, " ");
    $link_edit = $this->linkUrl . '&sub=edit&id=' . $id . '&ext=' . $row['ext_page'];
    $link_del = "javascript:del_item('" . $this->linkUrl . "&sub=del&id=" . $id . "&ext=" . $row['ext_page'] . "')";
    $output['order'] = $row['ext'] . "<input name=\"txt_Order[{$id}]\" type=\"text\" size=\"2\" maxlength=\"2\" style=\"text-align:center\" value=\"{$row['display_order']}\"  onkeypress=\"return is_num(event,'txtOrder')\" onchange='javascript:do_check($id)' />";

    $title = $func->HTML($row['title']);
    $output['title'] = "<strong><a href=\"{$link_edit}\">" . $title . "</a></strong>";

    $picture = "No image";
    if ($row['picture']) {
      $src = $vnT->conf['rooturl']."vnt_upload/ckeditor/" . $row['picture'];
      $picture = "<img src=\"{$src}\" width='100' >";
    } 

    $output['picture'] = $picture;

    $output['info'] = $func->HTML($row['description']);
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
   * Quan ly
   **/
  function do_Manage ($lang)
  {
    global $vnT, $func, $DB, $conf;
    //update
    if ($vnT->input["do_action"]) {

      //xoa cache
      $func->clear_cache();
      if ($vnT->input["del_id"])
        $h_id = $vnT->input["del_id"];
      switch ($vnT->input["do_action"]) {
        case "do_edit":
          if (isset($vnT->input["txt_Order"]))     $arr_order = $vnT->input["txt_Order"];
          $mess .= "- " . $vnT->lang['edit_success'] . " ID: <strong>";
          $str_mess = "";
          for ($i = 0; $i < count($h_id); $i ++) {
            $dup['display_order'] = $arr_order[$h_id[$i]];
            $ok = $DB->do_update("template_xml", $dup, "tpl_id=" . $h_id[$i]);
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
            $ok = $DB->do_update("template_xml", $dup, "tpl_id=" . $h_id[$i]);
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
            $ok = $DB->do_update("template_xml", $dup, "tpl_id=" . $h_id[$i]);
            if ($ok) {
              $str_mess .= $h_id[$i] . ", ";
            }
          }
          $mess .= substr($str_mess, 0, - 2) . "</strong><br>";
          $err = $func->html_mess($mess);
          break;
      }

      $this->do_BuildXml($lang);
    }
    if((int)$vnT->input["do_display"]) {
      $ok = $DB->query("Update template_xml SET display=1 WHERE tpl_id=".$vnT->input["do_display"]);
      if($ok){
        $mess .= "- " . $vnT->lang['display_success'] . "  ID: <strong>".$vnT->input["do_display"] . "</strong><br>";
        $err = $func->html_mess($mess);
      }
      $this->do_BuildXml($lang);
      //xoa cache
      $func->clear_cache();
    }
    if((int)$vnT->input["do_hidden"]) {
      $ok = $DB->query("Update template_xml SET display=0 WHERE tpl_id=".$vnT->input["do_hidden"]);
      if($ok){
        $mess .= "- " . $vnT->lang['display_success'] . "  ID: <strong>".$vnT->input["do_hidden"] . "</strong><br>";
        $err = $func->html_mess($mess);
      }
      $this->do_BuildXml($lang);
      //xoa cache
      $func->clear_cache();
    }

    $p = ((int) $vnT->input['p']) ?  $vnT->input['p'] : 1;
    $n = ($conf['record']) ? $conf['record'] : 30;
    $query = $DB->query("SELECT tpl_id FROM template_xmls");
    $totals = intval($DB->num_rows($query));
    $num_pages = ceil($totals / $n);
    if ($p > $num_pages)      $p = $num_pages;
    if ($p < 1)      $p = 1;
    $start = ($p - 1) * $n;
    $nav = $func->paginate($totals, $n, $ext, $p);
    $table['link_action'] = $this->linkUrl . "{$ext}&p=$p";
    $ext_link = $ext."&p=$p" ;
    $table['title'] = array(
      'check_box' => "<input type=\"checkbox\" name=\"checkall\" id=\"checkall\" class=\"checkbox\" />|5%|center" ,
      'order' => $vnT->lang['order'] . "|10%|center" ,
      'picture' => $vnT->lang['picture'] . "|15%|center" ,
      'title' => $vnT->lang['title'] . " |30%|left" ,
      'info' => "Thông tin ||left" ,
      'action' => "Action|15%|center");
    $sql = "SELECT * FROM template_xml ORDER BY  display_order ASC, tpl_id DESC  LIMIT $start,$n";
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
        $row_field[$i]['row_id'] = "row_" . $row[$i]['tpl_id'];
        $row_field[$i]['ext'] = "";
      }
      $table['row'] = $row_field;
    } else {
      $table['row'] = array();
      $table['extra'] = "<div align=center class=font_err >Chưa có đối template nào</div>";
    }


    $table['button'] = '<input type="button" name="btnHidden" value=" ' . $vnT->lang['hidden'] . ' " class="button" onclick="do_submit(\'do_hidden\')">&nbsp;';
    $table['button'] .= '<input type="button" name="btnDisplay" value=" ' . $vnT->lang['display'] . ' " class="button" onclick="do_submit(\'do_display\')">&nbsp;';
    $table['button'] .= '<input type="button" name="btnEdit" value=" ' . $vnT->lang['update'] . ' " class="button" onclick="do_submit(\'do_edit\')">&nbsp;';
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