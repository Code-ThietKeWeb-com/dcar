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
        $nd['f_title'] = $vnT->lang["add_mail_list"];
        $nd['content'] = $this->do_Add($lang);
        break;
      case 'send_mail':
        $nd['f_title'] = $vnT->lang["f_send_mail"];
        $nd['content'] = $this->do_Send_Mail($lang);
        break;
      case 'send_all':
        $nd['f_title'] = $vnT->lang["f_send_all"];
        $nd['content'] = $this->do_Send_All($lang);
        break;
      case 'del':
        $this->do_Del($lang);
        break;
      default:
        $nd['f_title'] = $vnT->lang['manage_mail_list'];
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
   *
   **/
  function do_Add ($lang)
  {
    global $vnT, $func, $DB, $conf;
    $err = "";
    if ($vnT->input['do_submit'] == 1) {
      $data = $_POST;
      $text_email = trim($_POST['text_email']);
      $arr_dong = explode("\n", $text_email);
      foreach ($arr_dong as $k => $v) {
        $arr_email = explode(",", trim($v));
        foreach ($arr_email as $key => $value) {
          $email = trim($value);
          if ($email) {
            //check existed
            $res_ck = $DB->query("select email from listmail where email='$email'");
            if (! $DB->num_rows($res_ck)) {
              $cot['cat_id'] = $_POST['cat_id'];
              $cot['email'] = $email;
              $cot['datesubmit'] = time();
              $ok = $DB->do_insert("listmail", $cot);
            }
          }
        }
      }
      //insert adminlog
      $func->insertlog("Add", $_GET['act'], "");
      $mess = $vnT->lang['add_success'];
      $url = $this->linkUrl . "&sub=add";
      $func->html_redirect($url, $mess);
    }
    $data['list_cat'] = $this->Get_Cat($data['cat_id']);
    $data['err'] = $err;
    $data['link_action'] = $this->linkUrl . "&sub=add";
    /*assign the array to a template variable*/
    $this->skin->assign('data', $data);
    $this->skin->parse("edit");
    return $this->skin->text("edit");
  }


  /**
   * function do_Send_Page
   *
   **/
  function do_Send_Mail ($lang)
  {
    global $vnT, $func, $DB, $conf;
    $ids = $vnT->input['ids'];
    $id = (int) $vnT->input['id'];
    if ($id != 0) {
      $ids = $id;
    }
    
    if ($vnT->input['do_submit']) {
      $data = $_POST;

      //die($message);
      $count = 0;
      $result = $DB->query("select * from listmail where id in (" . $ids . ") ");
      $arr_email = array();
      while ($row = $DB->fetch_row($result)) {
        $email = $row['email'];
        $link_del = $conf['rooturl'] . "del_mailist.php?email=" . $email;
        $message = stripslashes ($_POST['content']);
        $message = str_replace(ROOT_URI."vnt_upload/File",$conf['rooturl']."vnt_upload/File",$message);
        $message .= "<br><hr>";
        $message .= "Nếu bạn không muốn nhận thông tin mới từ chúng tôi vui lòng click vào link bên dưới <br>Link hủy newsletter : <a href='" . $link_del . "'>" . $link_del . "</a>";
        $sent = $func->doSendMail($email, $vnT->input['subject'], $message, $conf['email']);
        $count = $count + 1;
      }
      //insert adminlog
      $func->insertlog("Send mail", $_GET['act'], $ids);
      //insert adminlog
      $func->insertlog("Edit", $_GET['act'], $ids);
      $err = "<p>Có <b>$count</b> khách hàng nhận được email này !!! </p>";
      $url = $this->linkUrl . "&sub=send_mail&ids=".$ids;
      $func->html_redirect($url, $err);
    }
    $result = $DB->query("select id from listmail where id in (" . $ids . ") ");
    $total = $DB->num_rows($result);
    $data['send_for'] = str_replace("{totals}", $total, $vnT->lang['send_for']);
    $data["html_content"] = $vnT->editor->doDisplay('content', $_POST['content'], '100%', '500', "Default");
    $data['err'] = $err;
    $data['link_action'] = $this->linkUrl . "&sub=send_mail&ids=" . $ids;
    /*assign the array to a template variable*/
    $this->skin->assign('data', $data);
    $this->skin->parse("send_mail");
    return $this->skin->text("send_mail");
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
    $query = 'DELETE FROM listmail WHERE id IN (' . $ids . ')';
    if ($ok = $DB->query($query)) {
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
    $output['check_box'] = "<input type=\"checkbox\" name=\"del_id[]\" value=\"{$id}\" class=\"checkbox\" onclick=\"select_row('{$row_id}')\">";

    $link_send = $this->linkUrl . "&sub=send_mail&id={$id}";
    $link_del = "javascript:del_item('" . $this->linkUrl . "&sub=del&id=" . $id . "&ext=" . $row['ext_page'] . "')";
    $output['email'] = "<a href=\"{$link_send}\" ><strong>" . $func->HTML($row['email']) . "</strong></a>";
    $output['cat_name'] = $vnT->setting['arr_category'][$row['cat_id']];
    $output['name'] = ($row['name']) ? $row['name'] : "Khach hang ".$id;
    $output['datesubmit'] = date("H:i, d/m/Y", $row['datesubmit']);

    $output['action'] = '<div class="action-buttons"><input name=h_id[]" type="hidden" value="' . $id . '" />';
    $output['action'] .= '<a href="' . $link_send . '" class="i-edit"  data-toggle=\'tooltip\' data-placement=\'top\' title="Cập nhật"  ><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>';
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

    if ($vnT->input['btnUpdate'])
    {
      $cot['popup_newsletter'] = $_POST['popup_newsletter'];

      $arr_old = $func->fetchDbConfig();
      $ok = $func->writeDbConfig("config", $cot, $arr_old);
      if ($ok) {
        $mess = $vnT->lang["edit_success"];
      } else {
        $mess = $vnT->lang["edit_failt"];
      }
      $url = $this->linkUrl;
      $func->html_redirect($url, $mess);
    }



    //update
    $rs_up = $this->do_ProcessUpdate($lang);
    if($rs_up['ok']==1){
      $err = $rs_up['err'];
    }


    $p = ((int) $vnT->input['p']) ? $p = $vnT->input['p'] : 1;
    $n = ($conf['record']) ? $conf['record'] : 30;

    $info_search['lang'] = $lang ;
    $res_where = $this->process_info_search($info_search) ;

    $where = $res_where['where'];
    $ext_page = $res_where['ext_page'];
    $ext = $res_where['ext'];

    $sortField = ($vnT->input['sortField']) ? $vnT->input['sortField'] : "p.p_id";
    $sortOrder = ($vnT->input['sortOrder']) ? $vnT->input['sortOrder'] : "DESC";
    $OrderBy = " ORDER BY $sortField $sortOrder , date_post DESC ";
    $ext_page=$ext_page."p=$p";


    $query = $DB->query("SELECT id FROM listmail WHERE id<>0 ".$where);
    $totals = intval($DB->num_rows($query));
    $num_pages = ceil($totals / $n);
    if ($p > $num_pages) $p = $num_pages; 
		if ($p < 1)   $p = 1;
    $start = ($p - 1) * $n;
    $nav = $func->paginate($totals, $n, $ext, $p);
    $table['link_action'] = $this->linkUrl . "{$ext}&p=$p";
		$ext_link = $ext."&p=$p" ;

    $table['title'] = array(
      'check_box' => "<input type=\"checkbox\" name=\"checkall\" id=\"checkall\" class=\"checkbox\" />|5%|center" ,
      'email' => "Email |35%|left" ,
      'name' => "Name |20%|left" ,
      'cat_name' => $vnT->lang['group'] . "|20%|left" ,
      'datesubmit' => $vnT->lang['date_submit'] . "|15%|center" ,
      'action' => "Action|10%|center"
    );
    $sql = "SELECT * FROM listmail WHERE id<>0 ".$where."  ORDER BY  id DESC  LIMIT $start,$n";
    $result = $DB->query($sql);
    if ($DB->num_rows($result))
    {
      $row = $DB->get_array($result);
      for ($i = 0; $i < count($row); $i ++)
      {
        $row[$i]['ext_link'] = $ext_link ;
        $row[$i]['ext_page'] = $ext_page;
        $row_info = $this->render_row($row[$i],$lang);
        $row_field[$i] = $row_info;
        $row_field[$i]['stt'] = ($i + 1);
        $row_field[$i]['row_id'] = "row_" . $row[$i]['id'];
        $row_field[$i]['ext'] = "";
      }
      $table['row'] = $row_field;
    } else  {
       $table['row'] = array();
       $table['extra'] = "<div align=center class=font_err >" . $vnT->lang['no_have_product'] ."</div>";
    }


    $table['button'] .= '<input type="button" name="btnEdit" value=" ' . $vnT->lang['send_email_chose'] . ' " class="button" onclick="vnTMailList.do_send_maillist()">&nbsp;';
    $table['button'] .= '<input type="button" name="btnDel" value=" ' . $vnT->lang['delete'] . ' " class="button" onclick="del_selected(\'' . $this->linkUrl . '&sub=del&ext=' . $ext_page . '\')">';
    $table_list = $func->ShowTable($table);
    $data['table_list'] = $table_list;
    $data['totals'] = $totals;

    foreach ($res_where['data'] as $key => $val) {
      $data[$key] = $val ;
    }

    $data['link_download'] = 'modules/mail_list_ad/_download.php?cat_id='.$data['cat_id'];


    $list_popup_newsletter = '';
    $arr_option = array("0" => 'Tắt' , "1" => 'Hiện');
    foreach ($arr_option as $key => $value)
    {
      $selected = ($key==$conf['popup_newsletter']) ? 'selected' : '';
      $list_popup_newsletter .='<option value="'.$key.'" '.$selected.'>'.$value.'</option>';
    }

    $data['list_popup_newsletter'] = $list_popup_newsletter;
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
