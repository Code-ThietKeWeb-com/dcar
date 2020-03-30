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
  var $action = MOD_NAME;

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


    switch ($vnT->input['sub']) {
      case 'add':
        $nd['f_title'] = $vnT->lang['add_about'];
        $nd['content'] = $this->do_Add($lang);
        break;
      case 'edit':
        $nd['f_title'] = $vnT->lang["edit_about"];
        $nd['content'] = $this->do_Edit($lang);
        break;
      case 'del':
        $this->do_Del($lang);
        break;
      default:
        $nd['f_title'] = $vnT->lang['manage_about'];
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
  function do_Add($lang)
  {
    global $vnT, $func, $DB, $conf;

    $err = "";
    $data['display'] = 1;
    $w = ($vnT->setting['img_width']) ? $vnT->setting['img_width'] : 500;
    $w_thum = ($vnT->setting['imgthumb_width']) ? $vnT->setting['imgthumb_width'] : 100;
    $dir = "";
    if (!empty($vnT->input['do_submit'])) {
      $data = $_POST;
      $parentid = $vnT->input['parentid'];
      $title = $vnT->input['title'];
      $picture = $vnT->input['picture'];

      if(empty($vnT->input['csrf_token']) || ($vnT->input['csrf_token'] != $_SESSION['vnt_csrf_token']) ) {
        $err =  $func->html_err($vnT->lang['err_csrf_token']) ;
      }

      if (empty($err)) {
        $cot['parentid'] = $parentid;
        $cot['picture'] = $picture;
        $cot['date_post'] = time();
        $cot['date_update'] = time();
        $cot['adminid'] = $vnT->admininfo['adminid'];

        $ok = $DB->do_insert("about", $cot);
        if ($ok) {
          $aid = $DB->insertid();
          //update content
          $cot_d['aid'] = $aid;
          $cot_d['title'] = $title;
          $cot_d['short'] = $DB->mySQLSafe($_POST['short']);
          $cot_d['content'] = $DB->mySQLSafe($_POST['content']);
          //SEO
          $cot_d['friendly_url'] = (trim($vnT->input['friendly_url'])) ? $func->make_url($vnT->input['friendly_url']) : $func->make_url($title);
          $cot_d['friendly_title'] = (trim($vnT->input['friendly_title'])) ? trim($vnT->input['friendly_title']) : $title;
          $cot_d['metakey'] = (trim($vnT->input['metakey'])) ? trim($vnT->input['metakey']) : $p_name;
          $cot_d['metadesc'] = (trim($vnT->input['metadesc'])) ? trim($vnT->input['metadesc']) : $func->cut_string($func->check_html($_POST['content'], 'nohtml'), 200, 1);

          $cot_d['display'] = $vnT->input['display'];

          $query_lang = $DB->query("select name from language ");
          while ($row = $DB->fetch_row($query_lang)) {
            $cot_d['lang'] = $row['name'];
            $DB->do_insert("about_desc", $cot_d);
          }

          //build seo_url
          $seo['sub'] = 'add';
          $seo['modules'] = $this->module;
          $seo['action'] = $this->action;
          $seo['item_id'] = $aid;
          $seo['friendly_url'] = $cot_d['friendly_url'];
          $seo['lang'] = $lang;
          $seo['query_string'] = "mod:" . $this->module . "|act:" . $this->action . "|itemID:" . $aid;
          $res_seo = $func->build_seo_url($seo);
          if ($res_seo['existed'] == 1) {
            $DB->query("UPDATE about_desc SET friendly_url='" . $res_seo['friendly_url'] . "' WHERE aid=" . $aid);
          }

          unset($_SESSION['vnt_csrf_token']);
          //xoa cache
          $func->clear_cache();
          //insert adminlog
          $func->insertlog("Add", $vnT->input['act'], $aid);
          $mess = $vnT->lang["add_success"];
          if (isset($_POST['btn_preview'])) {
            $url = $this->linkUrl . "&sub=edit&id=$aid&preview=1";
            $DB->query("Update about SET display=0 WHERE p_id=$aid ");
          } else {
            $url = $this->linkUrl . "&sub=add";
          }
          $func->html_redirect($url, $mess);
        } else {
          $err = $func->html_err($vnT->lang["add_failt"] . $DB->debug());
        }
      }
    }
    $data["html_short"] = $vnT->editor->doDisplay('short', $data['short'], '100%', '200', "Normal", $this->module, $dir);
    $data["html_content"] = $vnT->editor->doDisplay('content', $data['content'], '100%', '500', "Default", $this->module, $dir);
    $data['list_cat'] = $this->Get_Cat($vnT->input['parentid'], $lang);
    $data['list_display'] = vnT_HTML::list_yesno("display", $data['display']);

    $data['link_upload'] = '?mod=media&act=popup_media&module=' . $this->module . '&folder=' . $this->module . '&obj=picture&type=image&TB_iframe=true&width=900&height=474';

    $data['module'] =  $this->module;
    $data['folder_browse'] =  ($dir) ? $this->module."/".$dir : $this->module;

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
   * Cap nhat gioi thieu
   **/
  function do_Edit($lang)
  {
    global $vnT, $func, $DB, $conf;
    $vnT->html->addStyleSheet($vnT->dir_js . "/jquery_ui/themes/base/ui.all.css");
    $vnT->html->addScript($vnT->dir_js . "/jquery_ui/ui.core.js");
    $vnT->html->addScript($vnT->dir_js . "/jquery_ui/ui.datepicker.js");
    $vnT->html->addScriptDeclaration("
	 		$(function() {
				$('#ngay').datepicker({
					changeMonth: true,
					changeYear: true
				});

			});
		
		");
    $id = (int)$vnT->input['id'];
    $ext = $vnT->input['ext'];
    $err = "";

    $w = ($vnT->setting['img_width']) ? $vnT->setting['img_width'] : 500;
    $w_thum = ($vnT->setting['imgthumb_width']) ? $vnT->setting['imgthumb_width'] : 100;
    $dir = "";
    if ($vnT->input['do_submit']) {
      $parentid = $vnT->input['parentid'];
      $title = $vnT->input['title'];
      $picture = $vnT->input['picture'];

      if ($parentid == $id) $err = $func->html_err($vnT->lang['err_parentid_invalid']);
      if(empty($vnT->input['csrf_token']) || ($vnT->input['csrf_token'] != $_SESSION['vnt_csrf_token']) ) {
        $err =  $func->html_err($vnT->lang['err_csrf_token']) ;
      }


      if (empty($err)) {
        $cot['parentid'] = $parentid;
        $cot['picture'] = $picture;
        $cot['date_update'] = time();

        $kq = $DB->do_update("about", $cot, "aid=$id");
        if ($kq) {
          //update content
          $cot_d['title'] = $title;
          $cot_d['short'] = $DB->mySQLSafe($_POST['short']);
          $cot_d['content'] = $DB->mySQLSafe($_POST['content']);

          //SEO
          $cot_d['friendly_url'] = (trim($vnT->input['friendly_url'])) ? $func->make_url($vnT->input['friendly_url']) : $func->make_url($title);
          $cot_d['friendly_title'] = (trim($vnT->input['friendly_title'])) ? trim($vnT->input['friendly_title']) : $title;
          $cot_d['metakey'] = (trim($vnT->input['metakey'])) ? trim($vnT->input['metakey']) : $p_name;
          $cot_d['metadesc'] = (trim($vnT->input['metadesc'])) ? trim($vnT->input['metadesc']) : $func->cut_string($func->check_html($_POST['content'], 'nohtml'), 200, 1);
          $cot_d['display'] = $vnT->input['display'];

          $DB->do_update("about_desc", $cot_d, "aid=$id and lang='$lang'");

          //build seo_url
          $seo['sub'] = 'edit';
          $seo['modules'] = $this->module;
          $seo['action'] = $this->action;
          $seo['name_id'] = "itemID";
          $seo['item_id'] = $id;
          $seo['friendly_url'] = $cot_d['friendly_url'];
          $seo['lang'] = $lang;
          $seo['query_string'] = "mod:" . $this->module . "|act:" . $this->action . "|itemID:" . $id;
          $res_seo = $func->build_seo_url($seo);
          if ($res_seo['existed'] == 1) {
            $DB->query("UPDATE about_desc SET friendly_url='" . $res_seo['friendly_url'] . "' WHERE lang='" . $lang . "' AND aid=" . $id);
          }

          unset($_SESSION['vnt_csrf_token']);
          //xoa cache
          $func->clear_cache();
          //insert adminlog
          $func->insertlog("Edit", $vnT->input['act'], $id);
          $err = $vnT->lang["edit_success"];


          if (isset($_POST['btn_preview'])) {
            $url = $this->linkUrl . "&sub=edit&id=$id&preview=1";
          } else {
            $url = $this->linkUrl;
          }

          $func->html_redirect($url, $err);
        } else {
          $err = $func->html_err($vnT->lang["edit_failt"]);
        }
      }
    }

    $sql = "SELECT * 
					FROM about n, about_desc nd 
					WHERE n.aid=nd.aid 
					AND nd.lang='" . $lang . "' 
					AND n.aid=" . $id;
    $result = $DB->query($sql);
    if ($data = $DB->fetch_row($result)) {
      if ($vnT->input['preview'] == 1) {

        $link_preview = $conf['rooturl'];
        if ($vnT->muti_lang) $link_preview .= $lang . "/";

        $link_preview .= ($lang == "vn") ? "gioi-thieu/" . $data['friendly_url'] . ".html/?preview=1" : "about/" . $data['friendly_url'] . ".html/?preview=1";

        $mess_preview = str_replace(array("{title}", "{link}"), array($data['title'], $link_preview), $vnT->lang['mess_preview']);
        $data['js_preview'] = "tb_show('" . $mess_preview . "', '" . $link_preview . "&TB_iframe=true&width=1000&height=700',null)";
      }

      if ($data['picture']) {
        $dir = substr($data['picture'], 0, strrpos($data['picture'], "/"));
        $data['pic'] = "<img src=\"" . MOD_DIR_UPLOAD . "/" . $data['picture'] . "\" width='{$w_thum}' /> <a href=\"javascript:del_picture('picture')\" class=\"del\">Xóa</a>";
        $data['style_upload'] = "style='display:none' ";
      } else {
        $data['pic'] = "";
      }


      $data['list_display'] = vnT_HTML::list_yesno("display", $data['display']);
      $data['list_cat'] = $this->Get_Cat($data['parentid'], $lang);
    }
    $data["html_short"] = $vnT->editor->doDisplay('short', $data['short'], '100%', '200', "Normal", $this->module, $dir);
    $data["html_content"] = $vnT->editor->doDisplay('content', $data['content'], '100%', '500', "Default", $this->module, $dir);

    $data['link_upload'] = '?mod=media&act=popup_media&module=' . $this->module . '&folder=' . $this->module . '&obj=picture&type=image&TB_iframe=true&width=900&height=474';

    $data['module'] =  $this->module;
    $data['folder_browse'] =  ($dir) ? $this->module."/".$dir : $this->module;

    if (! isset($_SESSION['vnt_csrf_token'])) {
      $_SESSION['vnt_csrf_token'] = md5(uniqid(rand(), TRUE)) ;
    }
    $data['csrf_token'] = $_SESSION['vnt_csrf_token'] ;

    $data['err'] = $err;
    $data['link_action'] = $this->linkUrl . "&sub=edit&id={$id}&ext={$ext}";
    /*assign the array to a template variable*/
    $this->skin->assign('data', $data);
    $this->skin->parse("edit");
    return $this->skin->text("edit");
  }

  /**
   * function do_Del
   * Xoa 1 ... n  gioi thieu
   **/
  function do_Del($lang)
  {
    global $func, $DB, $conf, $vnT;
    $id = (int)$vnT->input['id'];
    $ext = $vnT->input["ext"];

    if(empty($vnT->input['csrf_token']) || ($vnT->input['csrf_token'] != $_SESSION['vnt_csrf_token']) ) {
      $mess =  $vnT->lang['err_csrf_token'] ;
    }else{
      $del = 0;
      $qr = "";
      if ($id != 0) {
        $ids = $id;
      }
      if (isset($vnT->input["del_id"])) {
        $ids = implode(',', $vnT->input["del_id"]);
      }

      $res = $DB->query("SELECT * FROM about WHERE aid IN (" . $ids . ") ");
      if ($DB->num_rows($res)) {
        while ($row = $DB->fetch_row($res)) {
          $res_d = $DB->query("SELECT id FROM about_desc WHERE aid=" . $row['aid'] . " AND lang<>'" . $lang . "' ");
          if (!$DB->num_rows($res_d)) {
            $DB->query("DELETE FROM about WHERE  aid=" . $row['aid'] . "  ");
          }
          $DB->query("DELETE FROM about_desc WHERE  aid=" . $row['aid'] . " AND lang='" . $lang . "' ");
        }
        $mess = $vnT->lang["del_success"];

        //build seo_url
        $seo['sub'] = 'del';
        $seo['modules'] = $this->module;
        $seo['action'] = $this->action;
        $seo['item_id'] = $ids;
        $seo['lang'] = $lang;
        $res_seo = $func->build_seo_url($seo);

        //xoa cache
        $func->clear_cache();
      } else {
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
    $id = $row['aid'];
    $row_id = "row_" . $id;
    $output['check_box'] = vnT_HTML::checkbox("del_id[]", $id, 0, " ");
    $link_edit = $this->linkUrl . '&sub=edit&id=' . $id . '&ext=' . $row['ext_page'];
    $link_del = "javascript:del_item('" . $this->linkUrl .  "&sub=del&csrf_token=".$_SESSION['vnt_csrf_token']."&id=" . $id . "&ext=" . $row['ext_page'] . "')";

    if ($row['picture']) {
      $output['picture'] = "<img src=\"" . MOD_DIR_UPLOAD . "/" . $row['picture'] . "\" width=50 />";
    } else
      $output['picture'] = "No image";

    if ($row['is_sub']) {
      $ext = '&nbsp;<img src="' . $vnT->dir_images . '/line3.gif" align="absmiddle" />&nbsp;';
      $title = $ext . "<span id='edit-text-" . $id . "' >" . $func->HTML($row['title']) . "</span>";
    } else {
      if ($row['is_sub_sub']) {
        $ext = '&nbsp;&nbsp;&nbsp;<img src="' . $vnT->dir_images . '/line3.gif" align="absmiddle" />&nbsp;';
        $title = $ext . "<span id='edit-text-" . $id . "' >" . $func->HTML($row['title']) . "</span>";
      } else {
        $title = "<strong><span id='edit-text-" . $id . "' >" . $func->HTML($row['title']) . "</span></strong>";
      }
    }
    $output['title'] = '<a href="'.$link_edit.'">'.$title.'</a>';


    $output['order'] = $ext . "<input name=\"txt_Order[$id]\" type=\"text\" size=\"2\" maxlength=\"2\" style=\"text-align:center\" value=\"{$row['display_order']}\"  onkeypress=\"return is_num(event,'txtOrder')\" onchange='javascript:do_check($id)' />";
    $output['date_post'] = date("d/m/Y", $row['date_post']);
    $output['link'] = $row['friendly_url'] . '.html';

    $output['is_focus'] = vnT_HTML::list_yesno("is_focus[$id]", $row['is_focus'], "onchange='javascript:do_check($id)'");

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
   * Quan ly cac gioi thieu
   **/
  function do_Manage($lang)
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
        if ($vnT->input["del_id"]) $h_id = $vnT->input["del_id"];
        switch ($vnT->input["do_action"]) {
          case "do_edit":
            if (isset($vnT->input["txt_Order"])) $arr_order = $vnT->input["txt_Order"];
            if (isset($vnT->input["is_focus"])) $is_focus = $vnT->input["is_focus"];
            $mess .= "- " . $vnT->lang['edit_success'] . " ID: <strong>";
            $str_mess = "";
            for ($i = 0; $i < count($h_id); $i++) {
              $dup['display_order'] = $arr_order[$h_id[$i]];
              $dup['is_focus'] = $is_focus[$h_id[$i]];
              $ok = $DB->do_update("about", $dup, "aid=" . $h_id[$i]);
              if ($ok) {
                $str_mess .= $h_id[$i] . ", ";
              } else {
                $mess .= "- " . $vnT->lang['edit_failt'] . " ID: <strong>" . $h_id[$i] . "</strong><br>";
              }
            }
            $mess .= substr($str_mess, 0, -2) . "</strong><br>";
            $err = $func->html_mess($mess);
            break;
          case "do_hidden":
            $mess .= "- " . $vnT->lang['hidden_success'] . " ID: <strong>";
            for ($i = 0; $i < count($h_id); $i++) {
              $dup['display'] = 0;
              $ok = $DB->do_update("about_desc", $dup, " lang='$lang' AND aid=" . $h_id[$i]);
              if ($ok) {
                $str_mess .= $h_id[$i] . ", ";
              }
            }
            $mess .= substr($str_mess, 0, -2) . "</strong><br>";
            $err = $func->html_mess($mess);
            break;
          case "do_display":
            $mess .= "- " . $vnT->lang['display_success'] . "  ID: <strong>";
            for ($i = 0; $i < count($h_id); $i++) {
              $dup['display'] = 1;
              $ok = $DB->do_update("about_desc", $dup, "lang='$lang' AND aid=" . $h_id[$i]);
              if ($ok) {
                $str_mess .= $h_id[$i] . ", ";
              }
            }
            $mess .= substr($str_mess, 0, -2) . "</strong><br>";
            $err = $func->html_mess($mess);
            break;
        }
      }
    }

    if ((int)$vnT->input["do_display"]) {
      if(empty($vnT->input['csrf_token']) || ($vnT->input['csrf_token'] != $_SESSION['vnt_csrf_token']) ) {
        $err =  $func->html_err($vnT->lang['err_csrf_token']) ;
      }else{
        $ok = $DB->query("Update about_desc SET display=1 WHERE lang='$lang' AND  aid=" . $vnT->input["do_display"]);
        if ($ok) {
          $mess .= "- " . $vnT->lang['display_success'] . "  ID: <strong>" . $vnT->input["do_display"] . "</strong><br>";
          $err = $func->html_mess($mess);
        }
        unset($_SESSION['vnt_csrf_token']);
        //xoa cache
        $func->clear_cache();
      }

    }
    if ((int)$vnT->input["do_hidden"]) {
      if(empty($vnT->input['csrf_token']) || ($vnT->input['csrf_token'] != $_SESSION['vnt_csrf_token']) ) {
        $err =  $func->html_err($vnT->lang['err_csrf_token']) ;
      }else{
        $ok = $DB->query("Update about_desc SET display=0 WHERE lang='$lang' AND aid=" . $vnT->input["do_hidden"]);
        if ($ok) {
          $mess .= "- " . $vnT->lang['display_success'] . "  ID: <strong>" . $vnT->input["do_hidden"] . "</strong><br>";
          $err = $func->html_mess($mess);
        }
        unset($_SESSION['vnt_csrf_token']);
        //xoa cache
        $func->clear_cache();
      }

    }

    if (! isset($_SESSION['vnt_csrf_token'])) {
      $_SESSION['vnt_csrf_token'] = md5(uniqid(rand(), TRUE)) ;
    }
    $p = ((int)$vnT->input['p']) ? $p = $vnT->input['p'] : 1;
    $search = ($vnT->input['search']) ? $vnT->input['search'] : "aid";
    $keyword = ($vnT->input['keyword']) ? $vnT->input['keyword'] : "";
    $where = $ext = $ext_page = "";

    if (!empty($keyword)) {
      if ($search == "aid") {
        $where .= " and n.aid =" . $keyword;
      } elseif ($search == "date_post") {
        $where .= " and DATE_FORMAT(FROM_UNIXTIME(date_post),'%d/%m/%Y') = '" . $keyword . "' ";
      } else {
        $where .= " and $search like '%" . $keyword . "%' ";
      }
      $ext_page .= "keyword=" . $keyword . "|";
      $ext .= "&search=" . $search . "&keyword=" . $keyword;
    }
    $query = $DB->query("SELECT n.aid 
					FROM about n, about_desc nd 
					WHERE n.aid=nd.aid AND lang='$lang' " . $where);
    $totals = $DB->num_rows($query);
    $n = ($conf['record']) ? $conf['record'] : 30;
    $num_pages = ceil($totals / $n);
    if ($p > $num_pages) $p = $num_pages;
    if ($p < 1) $p = 1;
    $start = ($p - 1) * $n;
    $nav = $func->paginate($totals, $n, $ext, $p);
    $ext_page = $ext_page . "order=aid|direction=DESC|p=" . $p;

    $table['link_action'] = $this->linkUrl . "{$ext}&p=$p";
    $ext_link = $ext . "&p=$p";
    $table['title'] = array(
      'check_box' => "<input type=\"checkbox\" name=\"checkall\" id=\"checkall\" class=\"checkbox\" />|50px|center",
      'order' => $vnT->lang['order'] . "|7%|center",
      'picture' => $vnT->lang['picture'] . "|10%|center",
      'title' => $vnT->lang['title'] . "||left",
      'link' => "Link |30%|left",
      'is_focus' => "Focus |10%|center",
      'action' => "Action|10%|center"
    );
    $sql = "SELECT * 
					FROM about n, about_desc nd 
					WHERE n.aid=nd.aid 
					AND lang='" . $lang . "'
					AND parentid=0
					" . $where . " 
					ORDER BY display_order ASC, date_post ASC 
					LIMIT " . $start . "," . $n;

    $result = $DB->query($sql);
    if ($DB->num_rows($result)) {
      $row = $DB->get_array($result);
      $stt = 0;
      for ($i = 0; $i < count($row); $i++) {
        $row[$i]['ext_link'] = $ext_link;
        $row[$i]['ext_page'] = $ext_page;
        $row_info = $this->render_row($row[$i], $lang);
        $row_field[$stt] = $row_info;
        $row_field[$stt]['stt'] = ($i + 1);
        $row_field[$stt]['row_id'] = "row_" . $row[$i]['aid'];
        $row_field[$stt]['ext'] = "";
        //check sub
        $sql_sub = "SELECT * 
					FROM about n, about_desc nd 
					WHERE n.aid=nd.aid 
					AND lang='" . $lang . "'
					AND parentid=" . $row[$i]['aid'] . "
        	ORDER BY display_order ASC, date_post ASC";
        $res_sub = $DB->query($sql_sub);
        if ($DB->num_rows($res_sub)) {
          $row_sub = $DB->get_array($res_sub);
          for ($j = 0; $j < count($row_sub); $j++) {
            $stt++;
            $row_sub[$j]['ext_link'] = $ext_link;
            $row_sub[$j]['ext_page'] = $ext_page;
            $row_sub[$j]['is_sub'] = 1;

            $row_info = $this->render_row($row_sub[$j], $lang);
            $row_field[$stt] = $row_info;
            $row_field[$stt]['stt'] = ($j + 1);
            $row_field[$stt]['row_id'] = "row_" . $row_sub[$j]['aid'];
            $row_field[$stt]['ext'] = "";
            //check sub
            $sql_sub_sub = "SELECT * 
						FROM about n, about_desc nd 
						WHERE n.aid=nd.aid 
						ANDlang='$lang'
						AND parentid=" . $row_sub[$j]['aid'] . "
						ORDER BY display_order ASC, date_post ASC";
            $res_sub_sub = $DB->query($sql_sub_sub);
            if ($DB->num_rows($res_sub_sub)) {

              $row_sub_sub = $DB->get_array($res_sub_sub);
              for ($k = 0; $k < count($row_sub_sub); $k++) {
                $stt++;
                $row_sub_sub[$k]['ext_link'] = $ext_link;
                $row_sub_sub[$k]['ext_page'] = $ext_page;
                $row_sub_sub[$k]['is_sub_sub'] = 1;
                $row_info = $this->render_row($row_sub_sub[$k], $lang);
                $row_field[$stt] = $row_info;
                $row_field[$stt]['stt'] = ($k + 1);
                $row_field[$stt]['row_id'] = "row_" . $row_sub_sub[$k]['aid'];
                $row_field[$stt]['ext'] = "";
              }
            }
          } // end for sub
        } // end if sub
        $stt++;
      } // end for
      $table['row'] = $row_field;
    } else {
      $table['row'] = array();
      $table['extra'] = "<div align=center class=font_err >" . $vnT->lang['no_have_about'] . "</div>";
    }
    $table['button'] = '<input type="button" name="btnHidden" value=" ' . $vnT->lang['hidden'] . ' " class="button" onclick="do_submit(\'do_hidden\')">&nbsp;';
    $table['button'] .= '<input type="button" name="btnDisplay" value=" ' . $vnT->lang['display'] . ' " class="button" onclick="do_submit(\'do_display\')">&nbsp;';
    $table['button'] .= '<input type="button" name="btnEdit" value=" ' . $vnT->lang['update'] . ' " class="button" onclick="do_submit(\'do_edit\')">&nbsp;';
    $table['button'] .= '<input type="button" name="btnDel" value=" ' . $vnT->lang['delete'] . ' " class="button" onclick="del_selected(\'' . $this->linkUrl . '&sub=del&csrf_token='.$_SESSION['vnt_csrf_token'].'&ext=' . $ext_page . '\')">';
    $table['csrf_token'] = $_SESSION['vnt_csrf_token'] ;
    $data['table_list'] = $func->ShowTable($table);
    $data['totals'] = (int)$totals;
    $data['link_fsearch'] = $this->linkUrl;
    $data['list_search'] = $this->List_Search($search);
    $data['keyword'] = $keyword;


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