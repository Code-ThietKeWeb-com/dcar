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

    if ($vnT->admininfo['level'] == 0 ) {


      switch ($vnT->input['sub']) {
        case 'add':
          $nd['f_title'] = $vnT->lang['add_admin'];
          $nd['content'] = $this->do_Add($lang);
          break;
        case 'edit':
          $nd['f_title'] = $vnT->lang["edit_admin"];
          $nd['content'] = $this->do_Edit($lang);
          break;
        case 'del':
          $this->do_Del($lang);
          break;
        default:
          $nd['f_title'] = $vnT->lang['manage_admin'];
          $nd['content'] = $this->do_Manage($lang);
          break;
      }

      $nd['menu'] = $func->getToolbar($this->module, $this->action, $lang);
    }else{

      if((int)$vnT->input['id']) {
        $func->header_redirect($this->linkUrl);
      }

      $nd['f_title'] = $vnT->lang["edit_admin"];
      $nd['content'] = $this->do_Edit($lang);
      $nd['menu'] = $func->getToolbar_Small($this->module, $this->action, $lang);
    }

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
    //check la admin
    $ok_add = 1;
    if($vnT->admininfo['level'] != 0){
      $ok_add = 0;
      $mess = $vnT->lang['err_permission'];
    }



    if($ok_add)
    {

      if ($vnT->input['do_submit'] == 1)
      {

        $check =  $this->checkSubmit() ;
        if ($check['ok']==1)
        {

          $data = $_POST;
          $cot['username'] = $vnT->input['username'];
          $cot['password'] = $func->md10($vnT->input['password']);
          $cot['email'] = $vnT->input['email'];
          $cot['lastlogin'] = "";
          $cot['ip'] = $_SERVER['REMOTE_ADDR'];
          $cot['level'] = $vnT->input['level'];
          $cot['code_reset'] = $func->m_random_str(6);

          // Check for Error
          $res_ch = $DB->query("SELECT adminid FROM admin WHERE username='" . $cot['username'] . "' ");
          if ($DB->num_rows($res_ch))
            $err = $func->html_err("Username existed !");

          $res_ch = $DB->query("SELECT adminid FROM admin WHERE email='" . $cot['email'] . "' ");
          if ($DB->num_rows($res_ch))
            $err = $func->html_err("Email existed !");

          // insert CSDL
          if (empty($err)) {
            $ok = $DB->do_insert("admin", $cot);
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

        }else{
          $err  = $func->html_err($check['mess']);
        }

      }

      $data['list_group'] = $this->List_Level($data['level']) ;
      $data['err'] = $err;
      $data['link_action'] = $this->linkUrl . "&sub=add";


      if (! isset($_SESSION['vnt_csrf_token'])) {
        $_SESSION['vnt_csrf_token'] = md5(uniqid(rand(), TRUE)) ;
      }
      $data['csrf_token'] = $_SESSION['vnt_csrf_token'] ;
      $data['rand_autocomplete'] = $vnT->func->m_random_str(6);

      /*assign the array to a template variable*/
      $this->skin->assign('data', $data);
      $this->skin->parse("edit");
      return $this->skin->text("edit");


    }else{
      $link = $this->linkUrl ;
      @header("Location: " . $link);
      echo "<meta http-equiv='refresh' content='0; url=" . $link . "' />";
    }

  }

  /**
   * function do_Edit
   * Cap nhat admin
   **/
  function do_Edit ($lang)
  {
    global $vnT, $func, $DB, $conf;
    $id = ($vnT->admininfo['level']==0) ? (int) $vnT->input['id'] :  $vnT->admininfo['adminid'];

    if ($vnT->input['do_submit'] == 1)
    {
      $data = $_POST;
      $check =  $this->checkSubmit() ;



      if ($check['ok']==1)
      {

        $cot = array();

        if ($vnT->input['password'] && ! empty($vnT->input['password']))
        {
          $cot['password'] = $func->md10($vnT->input['password']);

          //check pass old
          if( $id == $vnT->admininfo['adminid']) {
            if($vnT->input['password_old']){

              $pass_old = $func->md10($vnT->input['password_old']);

              // Check for Error
              $res_ch = $DB->query("SELECT adminid FROM admin WHERE password='" . $pass_old. "' and adminid=".$id);
              if (!$DB->num_rows($res_ch))
                $err = $func->html_err("Password cũ không đúng !");
            }else{
              $err = $func->html_err("Vui lòng nhập password cũ");
            }
          }
        }

        $cot['email'] = $func->txt_HTML($vnT->input['email']);
        $cot['ip'] = $_SERVER['REMOTE_ADDR'];

        if (isset($vnT->input['level']) && $vnT->admininfo['level']==0) {
          $cot['username'] = $vnT->input['username'];
          $cot['level'] = $vnT->input['level'];
        }

        // Check for Error
        $res_ch = $DB->query("SELECT adminid FROM admin WHERE username='" . $cot['username'] . "' and adminid<>$id");
        if ($check = $DB->num_rows($res_ch))
          $err = $func->html_err("Username existed !");

        $res_ch = $DB->query("SELECT adminid FROM admin WHERE email='" . $cot['email'] . "' and adminid<>$id");
        if ($check = $DB->num_rows($res_ch))
          $err = $func->html_err("Email existed !");

        // insert CSDL
        if (empty($err)) {
          $ok = $DB->do_update("admin", $cot, "adminid=".$id);
          if ($ok) {
            unset($_SESSION['vnt_csrf_token']);
            //insert adminlog
            $func->insertlog("Edit", $_GET['act'], $id);
            $mess = $vnT->lang['edit_success'];
            $url = $this->linkUrl;
            $func->html_redirect($url, $mess);
          } else {
            $err = $func->html_err($vnT->lang['edit_failt']);
          }
        }
      }else{
        $err  = $func->html_err($check['mess']);
      }

    }


    $where = ($vnT->admininfo['adminid'] == 1) ? "" : " AND adminid<>1 ";
    $result = $DB->query("SELECT a.*, g.title,g.permission 
												FROM admin a left join admin_group  g
												ON a.level = g.gid 
												where adminid=$id {$where} ");
    if ($data = $DB->fetch_row($result)) {
    } else {
      flush();
      $mess = "Khong tim thay admin voi ID = " . $id;
      $url = $this->linkUrl;
      echo $func->html_redirect($url, $mess);
      exit();
    }

    if ($vnT->admininfo['level'] == 0) {
      $data['list_group'] = ($id == 1) ?  "<b>" . $vnT->lang['group_admin'] . "</b>" :  $this->List_Level($data['level']);
    } else {
      $data['readonly'] = "readonly='true'";
      $data['list_group'] = "<b>" . $func->HTML($data['title']) . "</b>";
    }

    $data['rand_autocomplete'] = $vnT->func->m_random_str(6);

    if( $id == $vnT->admininfo['adminid']) {

      $this->skin->assign('data', $data);
      $this->skin->parse("edit.html_pass_old");
    }

    if (! isset($_SESSION['vnt_csrf_token'])) {
      $_SESSION['vnt_csrf_token'] = md5(uniqid(rand(), TRUE)) ;
    }
    $data['csrf_token'] = $_SESSION['vnt_csrf_token'] ;


    $data['link_action'] =  ($vnT->admininfo['level']==0) ? $this->linkUrl . "&sub=edit&id=$id" : $this->linkUrl ;
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
    //check la admin
    $ok_del = 1;
    if($vnT->admininfo['level'] != 0){
      $ok_del = 0;
    }




    if ($ok_del ) {
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

        $ok = $DB->query( 'DELETE FROM admin WHERE adminid<>1 AND adminid IN (' . $ids . ')');
        if ($ok) {
          unset($_SESSION['vnt_csrf_token']);
          $mess = $vnT->lang["del_success"];
        } else{
          $mess = $vnT->lang["del_failt"];
        }
      }

      $ext_page = str_replace("|", "&", $ext);
      $url = $this->linkUrl . "&{$ext_page}";
      $func->html_redirect($url, $mess);

    }else{
      $link = $this->linkUrl . "&sub=edit&id=" . $vnT->admininfo['adminid'];
      @header("Location: {$link}");
      echo "<meta http-equiv='refresh' content='0; url={$link}' />";
    }

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
    $id = $row['adminid'];
    $row_id = "row_" . $id;

    if ($row['adminid'] != 1){
      $output['check_box'] = vnT_HTML::checkbox("del_id[]", $id, 0, " ");
    }
    $link_edit = $this->linkUrl . '&sub=edit&id=' . $id . '&ext=' . $row['ext_page'];
    $link_del = "javascript:del_item('" . $this->linkUrl . "&sub=del&id=" . $id . "&csrf_token=".$_SESSION['vnt_csrf_token']."&ext=" . $row['ext_page'] . "')";


    $text_edit = "admin|username|adminid=" . $id ;
    $output['username'] = "<strong>" . $row['username'] . "</strong> <span class=font_err>[".$row['email']."]</span>";

    if (empty($row['level'])) {
      $output['level'] = $vnT->lang['group_admin'];
    } else {
      $output['level'] = $func->HTML($row['title']);
    }
    $text_permission = "";
    if (empty($row['permission']))
      $text_permission = $vnT->lang['all_permission'];
    else {
      $text_permission = "<a href='javascript:;' onClick=\"showhide('ext_permission" . $id . "')\">" . $vnT->lang['click_to_view_permission'] . "</a><div id='ext_permission" . $id . "' style='display:none'>";
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
      $text_permission . "</div>";
    }
    $output['permission'] = $text_permission;
    $output['ip'] = $row['ip'];
    if ($row['lastlogin'])
      $output['lastlogin'] = @date("H:i d/m/Y", $row['lastlogin']) . "&nbsp;";
    else
      $output['lastlogin'] = '--';
    $output['action'] = '<input name=h_id[]" type="hidden" value="' . $id . '" />';

    if ($row['adminid'] != 1){
      $output['action'] .= '<a href="' . $link_edit . '"><img src="' . $vnT->dir_images . '/edit.gif"  alt="Edit "></a>&nbsp;';
    }else{
      if( $vnT->admininfo['adminid']==1 )	{
        $output['action'] .= '<a href="' . $link_edit . '"><img src="' . $vnT->dir_images . '/edit.gif"  alt="Edit "></a>&nbsp;';
      }
    }
    if ($row['adminid'] != 1)
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
    //check la admin
    if ($vnT->admininfo['level'] != 0 ) {
      $link = $this->linkUrl . "&sub=edit&id=" . $vnT->admininfo['adminid'];
      @header("Location: {$link}");
      echo "<meta http-equiv='refresh' content='0; url={$link}' />";
      die();
    }

    if (! isset($_SESSION['vnt_csrf_token'])) {
      $_SESSION['vnt_csrf_token'] = md5(uniqid(rand(), TRUE)) ;
    }

    $p = ((int) $vnT->input['p']) ? $p = $vnT->input['p'] : 1;
    $n = ($conf['record']) ? $conf['record'] : 30;
    $query = $DB->query("SELECT * FROM admin  ");
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
      'username' => "Username|17%|left" ,
      'level' => "Group|18%|center" ,
      'permission' => "Permission|30%|center" ,
      'lastlogin' => "Last login|10%|center" ,
      'ip' => "IP|10%|center" ,
      'action' => "Action|10%|center");
    $sql = "SELECT a.*, g.title,g.permission 
						FROM admin a left join admin_group  g
						ON a.level = g.gid
						ORDER BY adminid  LIMIT $start,$n";
    //print "sql = ".$sql."<br>";
    $result = $DB->query($sql);
    if ($DB->num_rows($result)) {
      $row = $DB->get_array($result);
      for ($i = 0; $i < count($row); $i ++) {
        $row_info = $this->render_row($row[$i], $lang);
        $row_field[$i] = $row_info;
        $row_field[$i]['stt'] = ($i + 1);
        $row_field[$i]['row_id'] = "row_" . $row[$i]['adminid'];
        $row_field[$i]['ext'] = "";
      }
      $table['row'] = $row_field;
    } else {
      $table['row'] = array();
      $table['extra'] = "<div align=center class=font_err >" . $vnT->lang['no_have_admin'] . "</div>";
    }
    $table['button'] .= '<input type="button" name="btnDel" value=" ' . $vnT->lang['delete'] . ' " class="button" onclick="del_selected(\'' . $this->linkUrl . '&sub=del&csrf_token='.$_SESSION['vnt_csrf_token'].'&ext=' . $ext_page . '\')">';
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
