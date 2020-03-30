<?php
/*================================================================================*\
|| 							Name code : statistics.php 		 		            	  ||
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
$vntModule = new vntModule();

class vntModule
{
  var $output = "";
  var $skin = "";
  var $linkUrl = "";
  var $module = "country";
  var $action = "city";
  
  /**
   * function vntModule ()
   * Khoi tao 
   **/
  function vntModule ()
  {
    global $Template, $vnT, $func, $DB, $conf;
    require_once ("function_" . $this->module . ".php");
    $this->skin = new XiTemplate(DIR_MODULE . DS . $this->module . "_ad" . DS . "html" . DS . $this->action . ".tpl");
    $this->skin->assign('DIR_IMAGE', $vnT->dir_images);
    $this->skin->assign('LANG', $vnT->lang);
    $lang = ($vnT->input['lang']) ? $lang = $vnT->input['lang'] : $func->get_lang_default();
    $this->linkUrl = "?mod=" . $this->module . "&act=" . $this->action . "&lang=" . $lang;
    
    switch ($vnT->input['sub'])
    {
      case 'add':
        $nd['f_title'] = "Thêm tỉnh thành mới";
        $nd['content'] = $this->do_Add($lang);
        break;
      case 'edit':
        $nd['f_title'] = "Cập nhật tỉnh thành";
        $nd['content'] = $this->do_Edit($lang);
        break;
      case 'del':
        $this->do_Del($lang);
        break;
      default:
        $nd['f_title'] = "Quản lý tỉnh thành";
        $nd['content'] = $this->do_Manage($lang);
        break;
    }
    $nd['menu'] = $func->getToolbar($this->module, $this->action, $lang);
    $Template->assign("data", $nd);
    $Template->parse("box_main");
    $vnT->output .= $Template->text("box_main");
  }
  
  //========= Add ============
  function do_Add ($lang)
  {
    global $vnT, $func, $DB, $conf;
    $num = 1;
    $country = "VN";
    if (! empty($_POST['do_submit']))
    {
      $data = $_POST;
      $code = strtoupper(trim($_POST['code']));
      $country = $vnT->input['country'];
      $name = $func->txt_HTML($_POST['name']);
      // Check for Error
      $res_chk = $DB->query("SELECT * FROM iso_cities  WHERE code='{$code}' and country='$country' ");
      if ($check = $DB->fetch_row($res_chk)) $err = $func->html_err("Code name tồn tại");

      if(empty($vnT->input['csrf_token']) || ($vnT->input['csrf_token'] != $_SESSION['vnt_csrf_token']) ) {
        $err =  $func->html_err($vnT->lang['err_csrf_token']) ;
      }

      // End check
      if (empty($err))
      {
        $cot['code'] = $code;
        $cot['country'] = $country;
        $cot['name'] = $name;
        $cot['name_en'] = $vnT->input['name_en'];
        $kq = $DB->do_insert("iso_cities", $cot);
        if ($kq)
        {
					$city = $DB->insertid();

          unset($_SESSION['vnt_csrf_token']);
          //xoa cache				
          $func->clear_cache();
          
          //insert adminlog
          $func->insertlog("Add", $_GET['act'], $city);
          $err = $vnT->lang["add_success"];
          $url = $this->linkUrl . "&sub=add";
          $func->html_redirect($url, $err);
        } else
        {
          $err = $DB->debug();
        }
      }
    }
    $data['list_country'] = List_Country($country);

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
  
  //================
  function do_Edit ($lang)
  {
    global $vnT, $func, $DB, $conf;
    $id = (int) $vnT->input['id'];
    $err = "";
    if (isset($_POST['do_submit']))
    {
      $data = $_POST;
      $code = strtoupper(trim($_POST['code']));
      $country = $vnT->input['country'];
      $name = $func->txt_HTML($_POST['name']);
      // Check for Error
      $res_chk = $DB->query("SELECT * FROM iso_cities  WHERE code='{$code}' and id<>$id and country='$country'");
      if ($check = $DB->fetch_row($res_chk)) $err = $func->html_err("Code name tồn tại");

      if(empty($vnT->input['csrf_token']) || ($vnT->input['csrf_token'] != $_SESSION['vnt_csrf_token']) ) {
        $err =  $func->html_err($vnT->lang['err_csrf_token']) ;
      }

      // End check
      if (empty($err))
      {
        $cot['code'] = $code;
        $country = $_POST['country'];
        $cot['name'] = $name;
        $cot['name_en'] = $vnT->input['name_en'];
        $kq = $DB->do_update("iso_cities", $cot, "id=$id");
        if ($kq)
        {


          unset($_SESSION['vnt_csrf_token']);
          //xoa cache				
          $func->clear_cache(); 
          
          //insert adminlog
          $func->insertlog("Edit", $_GET['act'], $id);
          $err = $vnT->lang["edit_success"];
          $url = $this->linkUrl;
          $func->html_redirect($url, $err);
        } else
        {
          $err = $DB->debug();
        }
      }
    }
    $query = $DB->query("SELECT * FROM iso_cities WHERE id=$id ");
    if ($data = $DB->fetch_row($query))
    { 		
      $data['name'] = $func->txt_unHTML($data['name']);
    }
    $data['list_country'] = List_Country($data['country']);


    if (! isset($_SESSION['vnt_csrf_token'])) {
      $_SESSION['vnt_csrf_token'] = md5(uniqid(rand(), TRUE)) ;
    }
    $data['csrf_token'] = $_SESSION['vnt_csrf_token'] ;
    $data['err'] = $err;
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
      if ($id != 0)
      {
        $ids = $id;
      }
      if (isset($vnT->input["del_id"]))
      {
        $ids = implode(',', $vnT->input["del_id"]);
      }
      $query = 'DELETE FROM iso_cities WHERE id IN (' . $ids . ')';
      if ($ok = $DB->query($query))
      {
        //xoa cache
        $func->clear_cache();
        $mess = $vnT->lang["del_success"];
      } else
        $mess = $vnT->lang["del_failt"];
    }

    $ext_page = str_replace("|", "&", $ext);
    $url = $this->linkUrl . "&{$ext_page}";
    $func->html_redirect($url, $mess);
  }
  
  //================
  function render_row ($row_info, $lang)
  {
    global $vnT, $func, $DB, $conf, $vnT;
    $row = $row_info;
    // Xu ly tung ROW
    $id = $row['id'];
    $row_id = "row_" . $id;
    $output['check_box'] = vnT_HTML::checkbox("del_id[]", $id, 0, " ");
    $link_edit = $this->linkUrl . '&sub=edit&id=' . $id . '&ext=' . $row['ext_page'];
    $link_del = "javascript:del_item('" . $this->linkUrl . "&sub=del&csrf_token=".$_SESSION['vnt_csrf_token']."&id=" . $id . "&ext=" . $row['ext_page'] . "')";
    $output['order'] = $row['ext'] . "<input name=\"txt_Order[{$id}]\" type=\"text\" size=\"2\" maxlength=\"2\" style=\"text-align:center\" value=\"{$row['c_order']}\"  onkeypress=\"return is_num(event,'txtOrder')\" onchange='javascript:do_check($id)' />";
    $output['code'] = "<a href='$link_edit'><strong class=font_err>" . $func->HTML($row['code']) . "</strong></a>";

    $output['id'] = "<a href='$link_edit'><b class=font_err>".$func->HTML($row['id'])."</b></a>";

    $output['name'] = "<b>".$func->HTML($row['name'])."</b>";
    $output['name_en'] = "<b>".$func->HTML($row['name_en'])."</b>";
		
		$output['focus'] = vnT_HTML::list_yesno("focus[{$id}]", $row['focus'], "onchange='javascript:do_check($id)'");
    $output['s_price'] = "<input name=\"txtPrice[{$id}]\" type=\"text\" style=\"text-align:center\" size=\"15\"  value=\"{$row['s_price']}\" onchange='javascript:do_check($id)' />";

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
  
  function do_Manage ($lang)
  {
    global $vnT, $func, $DB, $conf;
    
    //update
    if (isset($_POST["do_action"]))
    {
      if(empty($vnT->input['csrf_token']) || ($vnT->input['csrf_token'] != $_SESSION['vnt_csrf_token']) ) {
        $err =  $func->html_err($vnT->lang['err_csrf_token']) ;
      }else{
        unset($_SESSION['vnt_csrf_token']);

        //xoa cache
        $func->clear_cache();
        if (isset($_POST["del_id"])) $h_id = $_POST["del_id"];
        switch ($_POST["do_action"])
        {
          case "do_edit":
            if (isset($vnT->input["txt_Order"])) $arr_order = $vnT->input["txt_Order"];
            if (isset($vnT->input["focus"])) $arr_focus = $vnT->input["focus"];
            if (isset($vnT->input["txtPrice"])) $arr_price = $vnT->input["txtPrice"] ; else $arr_price="";

            $mess .= "- " . $vnT->lang['edit_success'] . " ID: <strong>";
            $str_mess = "";
            for ($i = 0; $i < count($h_id); $i ++) {
              $dup['c_order'] = $arr_order[$h_id[$i]];
              $dup['focus'] = $arr_focus[$h_id[$i]];
              $dup['s_price'] = $arr_price[$h_id[$i]];
              $ok = $DB->do_update("iso_cities", $dup, "id=" . $h_id[$i]);
              if ($ok) {
                $str_mess .= $h_id[$i] . ", ";
              }
            }
            $mess .= substr($str_mess, 0, - 2) . "</strong><br>";
            $err = $func->html_mess($mess);
            break;
          case "do_hidden":
            $mess .= "- " . $vnT->lang['hidden_success'] . " ID: <strong>";
            for ($i = 0; $i < count($h_id); $i ++)
            {
              $dup['display'] = 0;
              $ok = $DB->do_update("iso_cities", $dup, "id={$h_id[$i]}");
              if ($ok)
              {
                $str_mess .= $h_id[$i] . ", ";
              }
            }
            $mess .= substr($str_mess, 0, - 2) . "</strong><br>";
            $err = $func->html_mess($mess);
            //insert adminlog
            $func->insertlog("Hidden", $_GET['act'], $str_mess);

            break;
          case "do_display":
            $mess .= "- " . $vnT->lang['display_success'] . "  ID: <strong>";
            for ($i = 0; $i < count($h_id); $i ++)
            {
              $dup['display'] = 1;
              $ok = $DB->do_update("iso_cities", $dup, "id={$h_id[$i]}");
              if ($ok)
              {
                $str_mess .= $h_id[$i] . ", ";
              }
            }
            $mess .= substr($str_mess, 0, - 2) . "</strong><br>";
            $err = $func->html_mess($mess);
            //insert adminlog
            $func->insertlog("Display", $_GET['act'], $str_mess);

            break;
        }
      }

    }
    if((int)$vnT->input["do_display"]) {
      if(empty($vnT->input['csrf_token']) || ($vnT->input['csrf_token'] != $_SESSION['vnt_csrf_token']) ) {
        $err =  $func->html_err($vnT->lang['err_csrf_token']) ;
      }else{
        unset($_SESSION['vnt_csrf_token']);
        $ok = $DB->query("Update iso_cities SET display=1 WHERE id=".$vnT->input["do_display"]);
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
        $ok = $DB->query("Update iso_cities SET display=0 WHERE id=".$vnT->input["do_hidden"]);
        if($ok){
          $mess .= "- " . $vnT->lang['display_success'] . "  ID: <strong>".$vnT->input["do_hidden"] . "</strong><br>";
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
    $country = ($vnT->input['country']) ? $vnT->input['country'] : "VN";
    $search = ($vnT->input['search']) ? $vnT->input['search'] : "code";
    $keyword = $vnT->input['keyword'];
    $display = (isset($vnT->input['display'])) ? $vnT->input['display'] : "-1";
    
    $where = "where country ='{$country}' ";
    if ($display != - 1)
    {
      $where .= " and display = $display ";
      $ext_page .= "display=$display|";
      $ext = "&display=$display";
    }
    if (! empty($search) && ! empty($keyword))
    {
      $where .= " and $search like '%$keyword%' ";
      $ext_page .= "search=$search|keyword=$keyword|";
      $ext .= "&search={$search}&keyword={$keyword}";
    }
    $query = $DB->query("SELECT * FROM iso_cities $where ");
    $totals = $DB->num_rows($query);
    $n = 30;
    $num_pages = ceil($totals / $n);
    if ($p > $num_pages) $p = $num_pages;
    if ($p < 1) $p = 1;
    $start = ($p - 1) * $n;
    $nav = $func->paginate($totals, $n, $ext, $p, $class = "pagelink");
    $table['link_action'] = $this->linkUrl . "{$ext}&p=$p"; 
		$ext_link = $ext."&p=$p" ;
    $table['title'] = array(
      'check_box' => "<input type=\"checkbox\" name=\"checkall\" id=\"checkall\" class=\"checkbox\" />|5%|center" ,
      'order' => "Thứ tự|7%|center" ,
      'id' => "ID tỉnh thành|10%|center" ,
      'name' => "Tên tỉnh thành (VN)||left"  ,
      'name_en' => "Tên tỉnh thành (EN)|20%|left"  ,
      's_price' => "Phí vận chuyển|15%|center" ,
      'action' => "Action|10%|center"
    );
    $sql = "SELECT * FROM iso_cities  $where ORDER BY c_order ASC ,name ASC  LIMIT $start,$n";
    //print "sql = ".$sql."<br>";
    $result = $DB->query($sql);
    if ($DB->num_rows($result))
    {
      $row = $DB->get_array($result);
      for ($i = 0; $i < count($row); $i ++)
      {
				$row[$i]['ext_link'] = $ext_link ;
				$row[$i]['ext_page'] = $ext_page;
        $row_info = $this->render_row($row[$i], $lang);
        $row_field[$i] = $row_info;
        $row_field[$i]['stt'] = ($i + 1);
        $row_field[$i]['row_id'] = "row_" . $row[$i]['id'];
        $row_field[$i]['ext'] = "";
      }
      $table['row'] = $row_field;
    } else
    {
      $table['row'] = array();
      $table['extra'] = "<div align=center class=font_err >Chưa có tỉnh thành nào</div>";
    }
    $table['button'] = '<input type="button" name="btnHidden" value=" ' . $vnT->lang['hidden'] . ' " class="button" onclick="do_submit(\'do_hidden\')">&nbsp;';
    $table['button'] .= '<input type="button" name="btnDisplay" value=" ' . $vnT->lang['display'] . ' " class="button" onclick="do_submit(\'do_display\')">&nbsp;';
    $table['button'] .= '<input type="button" name="btnEdit" value=" ' . $vnT->lang['update'] . ' " class="button" onclick="do_submit(\'do_edit\')">&nbsp;';
    $table['button'] .= '<input type="button" name="btnDel" value=" ' . $vnT->lang['delete'] . ' " class="button" onclick="del_selected(\'' . $this->linkUrl . '&sub=del&csrf_token='.$_SESSION['vnt_csrf_token'].'&ext=' . $ext_page . '\')">';
    $table['csrf_token'] = $_SESSION['vnt_csrf_token'] ;
    $table_list = $func->ShowTable($table);
    $data['table_list'] = $table_list;
    $data['list_country'] = List_Country($country, "onChange='submit();'");
    $data['keyword'] = $keyword;
    $data['list_search'] = $this->List_Search($search);
    $data['list_display'] = List_Display($display, "onChange='submit();'");

    $data['link_excel'] = $conf['rooturl']."admin/modules/country_ad/excel_city.php?lang=$lang".$ext ;

    $data['totals'] = $totals;
    $data['err'] = $err;
    $data['nav'] = $nav;
    /*assign the array to a template variable*/
    $this->skin->assign('data', $data);
    $this->skin->parse("manage");
    return $this->skin->text("manage");
  }
  
  //====== List_Search
  function List_Search ($did)
  {
    global $func, $DB, $conf, $vnT;
    $text = "<select size=1 name=\"search\" id=\"search\" class='select'>";
    if ($did == "code") $text .= "<option value=\"code\" selected> Mã tỉnh thành </option>";
    else
      $text .= "<option value=\"code\" >  Mã tỉnh thành </option>";
    if ($did == "name") $text .= "<option value=\"name\" selected> Tên tỉnh thành </option>";
    else
      $text .= "<option value=\"name\">Tên tỉnh thành</option>";
    $text .= "</select>";
    return $text;
  }

} //end class
?>