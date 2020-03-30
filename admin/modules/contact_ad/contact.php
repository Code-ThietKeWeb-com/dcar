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

    $vnT->html->addStyleSheet( $vnT->dir_js."/jquery-ui/jquery-ui.min.css");
    $vnT->html->addScript($vnT->dir_js . "/jquery-ui/jquery-ui.min.js");
    $vnT->html->addScriptDeclaration("
	 		$(function() {
				$('.datepicker').datepicker({
					  showOn: 'both',
						buttonImage: '" . $vnT->dir_images . "/calendar.gif',
						buttonImageOnly: true,
						changeMonth: true,
						changeYear: true
					}); 

			});
		
		");

    switch ($vnT->input['sub']) {
      case 'edit':
        $nd['f_title'] = $vnT->lang['edit_contact'];
        $nd['content'] = $this->do_Edit($lang);
        break;
      case 'del':
        $this->do_Del($lang);
        break;

      default:
        $nd['f_title'] = $vnT->lang['manage_contact'];
        $nd['content'] = $this->do_Manage($lang);
        break;
    }

    $nd['menu'] = $func->getToolbar_Small($this->module, $this->action, $lang);
    $nd['row_lang'] = $func->html_lang("?mod=" . $this->module . "&act=" . $this->action, $lang);
    $Template->assign("data", $nd);
    $Template->parse("box_main");
    $vnT->output .= $Template->text("box_main");

  }

  /**
   * function do_Edit
   * Cap nhat admin
   **/
  function do_Edit ($lang)
  {
    global $vnT, $func, $DB, $conf;
    $id = (int) $vnT->input['id'];
    if ($vnT->input['btnReply']) {
      $data = $_POST;
      $cot['re_subject'] = $vnT->input['re_subject'];
      $cot['re_content'] = $DB->mySQLSafe($_POST['re_content']);
      $cot['dateanswer'] = time();
      $ok = $DB->do_update("contact", $cot, "id=$id");
      if ($ok) {
        //send email
        $email_to = $data['email_to'];
        $re_content = stripslashes ($_POST['re_content']);
        $re_content = str_replace(ROOT_URI."vnt_upload/File",$conf['rooturl']."vnt_upload/File",$re_content);
        $sent = $func->doSendMail($email_to, $vnT->input['re_subject'], $re_content, $conf['email']);
        if ($sent) {
          //insert adminlog
          $func->insertlog("Send Contact", $_GET['act'], $id);
          $DB->query("update contact set status=2 where id={$id}");
          $mess = "Đã trả lời liên hệ tới " . $data['email_to'];
          $url = $this->linkUrl;
          $func->html_redirect($url, $mess);
        } else {
          $mess = "Vui lòng nhập lại email gửi trả lời";
          $err = $func->html_err($mess);
        }
      } else {
        $mess = $DB->debug();
        $err = $func->html_err($mess);
      }
    }
    if ($vnT->input['btnForward']) {
      $data = $_POST;
      $subject = $data['re_subject'] . " Forward from " . $_SERVER['SERVER_NAME'];
      $re_content = stripslashes($_POST['re_content']);
      $re_content = str_replace(ROOT_URI . "vnt_upload/File", $conf['rooturl'] . "vnt_upload/File", $re_content);
      $content = $re_content . "<br>-------------Forwwarded message-------------<br><br>" . $data['content'];
      $sent = $func->doSendMail($vnT->input['email_to'], $subject, $content, $conf['email']);
      if ($sent) {
        $DB->query("update contact set status=3 where id={$id}");
        $mess = "Đã chuyển tiếp email tới " . $data['email_to'];
        $url = $this->linkUrl;
        $func->html_redirect($url, $mess);
      } else {
        $mess = "Vui lòng nhập lại email để chuyển tiếp";
        $err = $func->html_err($mess);
      }
    }


    $sql = "select * from contact where id=$id";
    $result = $DB->query($sql);
    if ($data = $DB->fetch_row($result)) {
      $res = $DB->query("select email,title from contact_staff where email='" . $data['staff'] . "' ");
      if ($row = $DB->fetch_row($res)) {
        $data['department'] = "<b class=font_err>" . $func->fetch_content($row['title'], $lang) . "</b> (Email : " . $row['email'] . ")";
      }
      $data['content'] = $func->HTML($data['content']);
      if ($data['status'] == 0){
        $DB->query("update contact set status=1 where id={$id}");
      }
      $location = '';
      if ($data['state'])
        $location .=  get_state_name($data['state']);
      if ($data['city']){
        if($location) {
          $location .=', ';
        }
        $location .= get_city_name($data['city']);
      }
      $data['location'] =  $location ;


    }
    if ($data['type'] == 1)   $data['style'] = "style=\"display:none\"";

    $data["html_content"] = $vnT->editor->doDisplay('re_content', $_POST['re_content'], '100%', '500', "Default");
    $data['link_action'] = $this->linkUrl . "&sub=edit&id=$id";
    $data['err'] = $err;
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

      $del = 0;
      $qr = "";
      if ($id != 0) {
        $ids = $id;
      }
      if (isset($vnT->input["del_id"])) {
        $ids = implode(',', $vnT->input["del_id"]);
      }
      $query = 'DELETE FROM contact WHERE id IN (' . $ids . ')';
      if ($ok = $DB->query($query)) {
        $mess = $vnT->lang["del_success"];
      } else
        $mess = $vnT->lang["del_failt"];
      $ext_page = str_replace("|", "&", $ext);
    }

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
    $link_del = "javascript:del_item('" . $this->linkUrl . "&sub=del&csrf_token=".$_SESSION['vnt_csrf_token']."&id=" . $id . "&ext=" . $row['ext_page'] . "')";
    $output['name'] = "<a href=\"{$link_edit}\"><strong>" . $row['name'] . "</strong></a>";
    $output['email'] = $func->HTML($row['email']);
    $output['datesubmit'] = date("H:i, d/m/Y", $row['datesubmit']);





    $info ='';

    if ($row['subject'])
      $info .= '<div style="margin-bottom: 2px;" ><b>'.$row['subject'].'</b></div>';
    $info .= '<div >'.$row['content'].'</div>';

    $output['info'] = $info;
    switch ($row['status']) {
      case 1:
        $output['status'] = '<span class="e-status read"><i class="fa fa-envelope-open" aria-hidden="true"></i> '.$vnT->setting['arr_status_contact'][1].'</span>' ;
        break;
      default:
        $output['status'] = '<span class="e-status unread"><i class="fa fa-envelope" aria-hidden="true"></i> '.$vnT->setting['arr_status_contact'][0].'</span>' ;
        break;
    }

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
          case "do_hidden":
            $mess .= "- " . $vnT->lang['hidden_success'] . " ID: <strong>";
            for ($i = 0; $i < count($h_id); $i ++) {
              $dup['status'] = 0;
              $ok = $DB->do_update("contact", $dup, "id=" . $h_id[$i]);
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
              $dup['status'] = 1;
              $ok = $DB->do_update("contact", $dup, "id=" . $h_id[$i]);
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


    $info_search['lang'] = $lang ;
    $res_where = $this->process_info_search($info_search) ;
    $where = $res_where['where'];
    $ext_page = $res_where['ext_page'];
    $ext = $res_where['ext'];

    $query = $DB->query("SELECT id FROM contact WHERE id>0 ".$where);
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
      'name' => $vnT->lang['full_name'] . " |20%|left" ,
      'info' => $vnT->lang['info_contact'] . " ||left" ,
      'datesubmit' => $vnT->lang['date_submit'] . "|15%|center" ,
      'status' => $vnT->lang['status'] . "|10%|center" ,
      'action' => "Action|15%|center");
    $sql = "SELECT * FROM contact  WHERE id>0 ".$where." ORDER BY  id DESC  LIMIT $start,$n";
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
      $table['extra'] = "<div align=center class=font_err >" . $vnT->lang['no_have_contact'] . "</div>";
    }
    $table['button'] = '<input type="button" name="btnHidden" value=" ' . $vnT->lang['btn_status0'] . ' " class="button" onclick="do_submit(\'do_hidden\')">&nbsp;';
    $table['button'] .= '<input type="button" name="btnDisplay" value=" ' . $vnT->lang['btn_status1'] . ' " class="button" onclick="do_submit(\'do_display\')">&nbsp;';
    $table['button'] .= '<input type="button" name="btnDel" value=" ' . $vnT->lang['delete'] . ' " class="button" onclick="del_selected(\'' . $this->linkUrl . '&sub=del&csrf_token='.$_SESSION['vnt_csrf_token'].'&ext=' . $ext_page . '\')">';

    $table['csrf_token'] = $_SESSION['vnt_csrf_token'] ;

    $table_list = $func->ShowTable($table);
    $data['table_list'] = $table_list;
    $data['totals'] = $totals;
    foreach ($res_where['data'] as $key => $val) {
      $data[$key] = $val ;
    }

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