<?php
/*================================================================================*\
|| 							Name code : order.php 		 		            	  ||
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
  var $module = "admin";
  var $action = "recycle_bin";

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
    $vnT->html->addStyleSheet("modules/" . $this->module . "_ad/css/" . $this->module . ".css");
    switch ($vnT->input['sub']) {

      case 'restore':
        $this->do_Restore($lang);
        break;
      case 'del':
        $this->do_Del($lang);
        break;
      default:
        $nd['f_title'] = 'Quản lý tin xóa';
        $nd['content'] = $this->do_Manage($lang);
        break;
    }
    $nd['menu'] = $func->getToolbar_Small($this->module, $this->action, $lang);

    $Template->assign("data", $nd);
    $Template->parse("box_main");
    $vnT->output .= $Template->text("box_main");
  }




  /**
   * function do_Restore
   *
   **/
  function do_Restore ($lang)
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

    $res_ck = $DB->query("SELECT * FROM recycle_bin WHERE id IN (" . $ids . ")");
    while($row_ck = $DB->fetch_row($res_ck))
    {
      $ext_wh = ($row_ck['lang']) ? " AND lang='".$row_ck['lang']."' " : "";
      $DB->query("UPDATE ".$row_ck['tbl_data']." SET display=1 WHERE ".$row_ck['name_id']."=".$row_ck['item_id'] .$ext_wh);
    }

    $del = $DB->query("DELETE FROM recycle_bin WHERE id IN (" . $ids . ") ");
    if ($del) {
      $mess = "Phục hồi tin thành công";
    } else
      $mess = $vnT->lang["del_failt"];
    $ext_page = str_replace("|", "&", $ext);
    $url = $this->linkUrl . "&{$ext_page}";
    $func->html_redirect($url, $mess);
  }

  /**
   * function do_Task
   *
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

    $res_ck = $DB->query("SELECT * FROM recycle_bin WHERE id IN (" . $ids . ")");
    while($row_ck = $DB->fetch_row($res_ck))
    {
      if($row_ck['lang']){
        if (@strstr($row_ck['tbl_data'],"_desc"))
        {
          $res_d = $DB->query("SELECT id FROM ".$row_ck['tbl_data']." WHERE ".$row_ck['name_id']." = ".$row_ck['item_id']."  AND lang<>'".$row_ck['lang']."' ");
          if(!$DB->num_rows($res_d)){
            $DB->query("DELETE FROM ".@str_replace("_desc","",$row_ck['tbl_data'])." WHERE  ".$row_ck['name_id']." = ".$row_ck['item_id'] );
          }
        }
        $DB->query("DELETE FROM ".$row_ck['tbl_data']."  WHERE ".$row_ck['name_id']." = ".$row_ck['item_id']."  AND lang='".$row_ck['lang']."' ");
      }else{
        $DB->query("DELETE FROM ".$row_ck['tbl_data']."  WHERE ".$row_ck['name_id']." = ".$row_ck['item_id'] );
      }

    }

    $del = $DB->query("DELETE FROM recycle_bin WHERE id IN (" . $ids . ") ");
    if ($del) {
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

    $link_restore = $this->linkUrl . "&sub=restore&id={$id}";
    $link_del = "javascript:del_item('" . $this->linkUrl . "&sub=del&id=" . $id . "&ext=" . $row['ext_page'] . "')";

    $link_view = "?mod=".$row['module'] . "&act=".$row['action'] . "&sub=edit&id=".$row['item_id'];

    $output['module'] = '<b >'.$row['module'].'</b>';
    $output['act'] = '<b >'.$row['action'].'</b>';
    $output['item_id'] = '<b ><a href="'.$link_view.'" target="_blank">'.$row['item_id'].'</a></b>';
    $output['lang'] = '<b >'.$row['lang'].'</b>';
    $output['datesubmit'] = date("H:i, d/m/Y", $row['datesubmit']);

    $output['action'] = '<input name=h_id[]" type="hidden" value="' . $id . '" />';
    $output['action'] .= '<a href="' . $link_restore . '" class="button"><i class="fa fa-refresh" aria-hidden="true"></i> Phục hồi</a>&nbsp;';
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
    $vnT->html->addStyleSheet( $vnT->dir_js . "/jquery_ui/themes/base/ui.all.css");
    $vnT->html->addScript($vnT->dir_js . "/jquery_ui/ui.core.js");
    $vnT->html->addScript($vnT->dir_js . "/jquery_ui/ui.datepicker.js");
    $vnT->html->addScriptDeclaration("
	 		$(function() {
				$('#date_begin').datepicker({ 
						buttonImageOnly: true,
						changeMonth: true,
						changeYear: true
					});
					$('#date_end').datepicker({ 
						buttonImageOnly: true,
						changeMonth: true,
						changeYear: true
					});

			});
		
		");

    //update
    if ($vnT->input["do_action"]) {
      //xoa cache
      $func->clear_cache();
      if ($vnT->input["del_id"])   $h_id = $vnT->input["del_id"];
      if ($vnT->input["hmem"])   $hmem = $vnT->input["hmem"];

      switch ($vnT->input["do_action"]) {
        case "do_display":
          $mess .= "- Xét đã thanh toán cho  ID: <strong>";
          for ($i = 0; $i < count($h_id); $i ++) {
            $dup['status'] = 1;
            $ok = $DB->do_update("member_money", $dup, "id=" . $h_id[$i]);
            if ($ok) {
              $res_c = $DB->query("SELECT * FROM member_money WHERE id=".$h_id[$i]." ") ;
              if($row_c = $DB->fetch_row($res_c))
              {
                $DB->query("UPDATE members SET mem_point=mem_point+".$row_c['value']." WHERE mem_id=".$row_c['mem_id']." ");

                //inser gold_history
                $cot_h['mem_id'] = $row_c['mem_id'];
                $cot_h['action'] = 'add';
                $cot_h['value'] = $row_c['value'];
                $cot_h['reason'] = "Nạp tiền vào tài khoản";
                $cot_h['id_reason'] = $h_id[$i];
                $cot_h['code_reason'] = $row_c['order_code'];
                $cot_h['notes'] =  "Nạp ".$func->format_number($row_c['value'])." đ vào Tài Khoản từ thẻ điện thoại";
                $cot_h['datesubmit'] = time();
                $DB->do_insert("money_history", $cot_h);
              }

              $str_mess .= $h_id[$i] . ", ";
            }
          }
          $mess .= substr($str_mess, 0, - 2) . "</strong><br>";
          $err = $func->html_mess($mess);
          break;
        case "do_hidden":
          $mess .= "- Xóa đã thanh toán cho  ID: <strong>";
          for ($i = 0; $i < count($h_id); $i ++) {
            $dup['status'] = 0;
            $ok = $DB->do_update("member_money", $dup, "id=" . $h_id[$i]);
            if ($ok) {
              $res_c = $DB->query("SELECT * FROM member_money WHERE id=".$h_id[$i]) ;
              if($row_c = $DB->fetch_row($res_c))
              {
                $DB->query("UPDATE members SET mem_point=mem_point-".$row_c['value']." WHERE mem_id=".$row_c['mem_id']." ");
                //del gold_history
                $DB->query("DELETE FROM money_history WHERE mem_id=".$row_c['mem_id']." AND id_reason=".$h_id[$i]."  ");
              }

              $str_mess .= $h_id[$i] . ", ";
            }
          }
          $mess .= substr($str_mess, 0, - 2) . "</strong><br>";
          $err = $func->html_mess($mess);
          break;
      }
    }
    $p = ((int) $vnT->input['p']) ? $p = $vnT->input['p'] : 1;
    $n = ($conf['record']) ? $conf['record'] : 30;

    $status = (isset($vnT->input['status'])) ? $vnT->input['status'] : "-1";
    $search = ($vnT->input['search']) ? $vnT->input['search'] : "id";
    $keyword = (trim($vnT->input['keyword'])) ? trim($vnT->input['keyword']) : "";
    $date_begin = ($vnT->input['date_begin']) ?  $vnT->input['date_begin'] : "";
    $date_end = ($vnT->input['date_end']) ?  $vnT->input['date_end'] : "";

    $where ="";
    if($status!="-1") {
      $where.=" AND status=$status ";
      $ext.="&status=".$status;
      $ext_page.= "status=".$status."|";
    }
    if($date_begin || $date_end )
    {
      $tmp1 = @explode("/", $date_begin);
      $time_begin = @mktime(0, 0, 0, $tmp1[1], $tmp1[0], $tmp1[2]);

      $tmp2 = @explode("/", $date_end);
      $time_end = @mktime(23, 59, 59, $tmp2[1], $tmp2[0], $tmp2[2]);

      $where.=" AND (datesubmit BETWEEN {$time_begin} AND {$time_end} ) ";
      $ext.="&date_begin=".$date_begin."&date_end=".$date_end;
      $ext_page.= "date_begin=".$date_begin."|date_end=".$date_end."|";
    }

    if (! empty($keyword)) {
      if ($search == "datesubmit") {
        $where .= " and DATE_FORMAT(FROM_UNIXTIME(datesubmit),'%d/%m/%Y') = '{$keyword}' ";
      } else {
        $where .= " and $search like '%$keyword%' ";
      }
      $ext_page .= "keyword=$keyword|";
      $ext .= "&search={$search}&keyword={$keyword}";
    }

    $where .=" AND id>0 ";

    $query = $DB->query("SELECT  id FROM recycle_bin  WHERE id>0  $where ");
    $totals = intval($DB->num_rows($query));
    $num_pages = ceil($totals / $n);
    if ($p > $num_pages)    $p = $num_pages;
    if ($p < 1)   $p = 1;
    $start = ($p - 1) * $n;
    $nav = $func->paginate($totals, $n, $ext, $p);
    $table['link_action'] = $this->linkUrl . $ext;
    $table['title'] = array(
      'check_box' => "<input type=\"checkbox\" name=\"checkall\" id=\"checkall\" class=\"checkbox\" />|5%|center" ,
      'module' => "Module|15%|center" ,
      'act' => "Act|15%|center" ,
      'item_id' => "ID|15%|center" ,
      'lang' => "Lang|15%|center" ,
      'datesubmit' => "Ngày xóa||center" ,
      'action' => "Action|5%|center");
    $sql = "SELECT * FROM recycle_bin WHERE id>0 $where ORDER BY  datesubmit DESC  LIMIT $start,$n";
    //print "sql = ".$sql."<br>";
    $reuslt = $DB->query($sql);
    if ($DB->num_rows($reuslt)) {
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
      $table['extra'] = "<div align=center class=font_err > Chưa có tin nào</div>";
    }

    $data['list_search'] =  vnT_HTML::selectbox("search", array('module' => 'Module' ,'action'  => 'Action' ,'item_id ' => 'ID' ,'lang ' => 'Lang'  ,"datesubmit"=>"Ngày xóa" ), $search);
    $table['button'] = '<input type="button" name="btnUpdate" value=" Phục hồi tin " class="button"   onclick="action_selected(\''.$this->linkUrl.'&sub=restore&ext='.$ext_page.'\', \'Bạn có chắc muốn Phục Hồi tin?\')"  >';


    $table['button'] .= '<input type="button" name="btnDel" value=" ' . $vnT->lang['delete'] . ' " class="button" onclick="del_selected(\'' . $this->linkUrl . '&sub=del&ext=' . $ext_page . '\')">';

    $table_list = $func->ShowTable($table);
    $data['table_list'] = $table_list;
    $data['totals'] = $totals;
    $data['keyword'] = $keyword;

    $data['date_begin'] = $date_begin;
    $data['date_end'] = $date_end;

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