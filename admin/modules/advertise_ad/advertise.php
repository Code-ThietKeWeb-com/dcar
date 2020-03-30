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

    $vnT->html->addStyleSheet("modules/" . $this->module . "_ad/css/" . $this->module . ".css");
    $vnT->html->addScript("modules/" . $this->module . "_ad" . "/js/" . $this->module . ".js");

    $lang = ($vnT->input['lang']) ? $lang = $vnT->input['lang'] : $func->get_lang_default();
    $pos = ($vnT->input['pos']) ? $vnT->input['pos'] : $vnT->setting['pos'] ;
    $this->linkUrl = "?mod=" . $this->module . "&act=" . $this->action."&pos=".$pos . "&lang=" . $lang;
		
    switch ($vnT->input['sub']) {
      case 'add':
        $nd['f_title'] = $vnT->lang['add_advertise'];
        $nd['content'] = $this->do_Add($pos, $lang);
      break;
      case 'edit':
        $nd['f_title'] = $vnT->lang['edit_advertise'];
        $nd['content'] = $this->do_Edit($pos, $lang);
      break;
      case 'manage':
      	$nd['f_title'] = $vnT->lang['manage_advertise'];
        $nd['content'] = $this->do_Manage($pos, $lang);
      break;
      case 'del':    $this->do_Del($pos, $lang);  break;
      default:
        $nd['f_title'] = $vnT->lang['manage_advertise'];
        $nd['content'] = $this->do_Manage($pos, $lang);
      break;
    }

    $nd['menu'] = $func->getToolbar($this->module, $this->action."&pos=".$pos, $lang);
    $nd['row_lang'] = $func->html_lang("?mod=" . $this->module . "&act=" . $this->action."&pos=".$pos, $lang);

    $Template->assign("data", $nd);
    $Template->parse("box_main");
    $vnT->output .= $Template->text("box_main");
  }
  

  /**
   * function do_Add 
   * Them gioi thieu moi 
   **/
  function do_Add ($pos, $lang)
  {
    global $vnT, $func, $DB, $conf;
		
		$vnT->html->addStyleSheet( $vnT->dir_js . "/jquery_ui/themes/base/ui.all.css");
		$vnT->html->addScript($vnT->dir_js . "/jquery_ui/ui.core.js");		
		$vnT->html->addScript($vnT->dir_js . "/jquery_ui/ui.datepicker.js");  
    $vnT->html->addScriptDeclaration("
	 		$(function() {
				$('.dates').datepicker({
					changeMonth: true,
					changeYear: true
				}); 
			});
		
		");
    $err = "";
    $data['l_link'] = "http://";
    $err = "";
    $data['date_add'] = date("d/m/Y");
    $data['date_expire'] = date("d/m/Y", (time() + 30 * 24 * 3600));
    $data['target'] = "_blank";
    $data['display'] = 1;
    $data['width'] = $vnT->setting[$pos]['width'];
    $data['height'] = $vnT->setting[$pos]['height'];
		$data['type_ad']=0;
		
    if ($vnT->input['do_submit'] == 1)
    {
      $data = $_POST;

      //check err
      if ($type_ad = 0 && $vnT->input['picture']==''){
        $err = $func->html_err("No Web link Image selected");
      }

      if(empty($vnT->input['csrf_token']) || ($vnT->input['csrf_token'] != $_SESSION['vnt_csrf_token']) ) {
        $err =  $func->html_err($vnT->lang['err_csrf_token']) ;
      }

      // insert CSDL
      if (empty($err)) {

        $cot = array();
        $res_info = $this->buildInfoItem();
        foreach ($res_info as $key => $val) {
          $cot[$key] = $val;
        }

        $cot['lang'] = $lang;
        $ok = $DB->do_insert("advertise", $cot);
        if ($ok) {
					
					//update lang
          $query_lang = $DB->query("select name from language WHERE name<>'" . $lang . "' ");
          while ($row_lang = $DB->fetch_row($query_lang)) {
            $cot['lang'] = $row_lang['name'];
            $DB->do_insert("advertise", $cot);
          }

          unset($_SESSION['vnt_csrf_token']);
          //xoa cache
          $func->clear_cache();
          //insert adminlog
          $func->insertlog("Add", $_GET['act'], $DB->insertid());
          $mess = $vnT->lang['add_success'];
          $url = $this->linkUrl . "&sub=add&pos={$pos}";
          $func->html_redirect($url, $mess);
        } else {
          $err = $func->html_err($vnT->lang['add_failt'] . $DB->debug());
        }
      }
    }

    //more
    $res_more = $this->load_more_data($data);
    if (is_array($res_more)){
      foreach ($res_more as $key => $val)	{
        $data[$key] = $val;
      }
    }
    $data['list_pos'] = $this->List_Pos($pos, "option");
    $data['list_display'] = vnT_HTML::list_yesno("display", $data['display']);

    if (! isset($_SESSION['vnt_csrf_token'])) {
      $_SESSION['vnt_csrf_token'] = md5(uniqid(rand(), TRUE)) ;
    }
    $data['csrf_token'] = $_SESSION['vnt_csrf_token'] ;

    $data['err'] = $err;
    $data['link_action'] = $this->linkUrl . "&sub=add&pos=$pos";
    /*assign the array to a template variable*/
    $this->skin->assign('data', $data);
    $this->skin->parse("edit");
    return $this->skin->text("edit");
  }

  /**
   * function do_Edit 
   * Cap nhat admin
   **/
  function do_Edit ($pos, $lang)
  {
    global $vnT, $func, $DB, $conf;
    $vnT->html->addStyleSheet( $vnT->dir_js . "/jquery_ui/themes/base/ui.all.css");
		$vnT->html->addScript($vnT->dir_js . "/jquery_ui/ui.core.js");		
		$vnT->html->addScript($vnT->dir_js . "/jquery_ui/ui.datepicker.js");
		
    $vnT->html->addScriptDeclaration("
	 		$(function() {
				$('.dates').datepicker({
					changeMonth: true,
					changeYear: true
				});  
			});
		
		");
    $id = (int) $vnT->input['id'];
    if ($vnT->input['do_submit']) {
      $data = $_POST;

      //check err
      if ($type_ad = 0 && $vnT->input['picture']==''){
        $err = $func->html_err("No Web link Image selected");
      }

      if(empty($vnT->input['csrf_token']) || ($vnT->input['csrf_token'] != $_SESSION['vnt_csrf_token']) ) {
        $err =  $func->html_err($vnT->lang['err_csrf_token']) ;
      }


      if (empty($err)) {

        $cot = array();
        $res_info = $this->buildInfoItem();
        foreach ($res_info as $key => $val) {
          $cot[$key] = $val;
        }

        $ok = $DB->do_update("advertise", $cot, "l_id=$id");
        if ($ok) {

          unset($_SESSION['vnt_csrf_token']);

          //xoa cache
          $func->clear_cache();
          //insert adminlog
          $func->insertlog("Edit", $_GET['act'], $id);
          $err = $vnT->lang["edit_success"];
          $url = $this->linkUrl . "&sub=manage&pos=$pos";
          $func->html_redirect($url, $err);
        } else
          $err = $func->html_err($vnT->lang["edit_failt"] . $DB->debug());
      }
    }
    $query = $DB->query("SELECT * FROM advertise WHERE l_id={$id} ");
    if ($data = $DB->fetch_row($query)) {
      $data['l_link'] = $data['link'];
      $data['date_add'] = ($data['date_add']) ? @date("d/m/Y", $data['date_add']) : "";
      $data['date_expire'] = ($data['date_expire']) ? @date("d/m/Y", $data['date_expire']) : "";

      switch ($data['type_ad'])
      {
        case 1 :
          $data['style0']=" style='display:none' ";
          $data['style1']="";
          $data['style2']=" style='display:none' ";
          $data['content'] = $data['img'];
          break	;
        case 2 :
          $data['style0']=" style='display:none' ";
          $data['style1']=" style='display:none' ";
          $data['script'] = $func->txt_unHTML($data['img']);
          break	;
        default :
          $data['style0']="";
          $data['style1']=" style=\"display:none;\" ";
          $data['style2']=" style='display:none' ";
          if($data['img'])
          {
            $src =  (strstr($data['img'],"http")) ? $data['img'] :  MOD_DIR_UPLOAD . "/" .  $data['img'] ;
            $data['html_img'] = "<img src=\"{$src}\" />";
            $data['picture'] = $data['img'];
          }

          break;
      }

      //more
      $res_more = $this->load_more_data($data);
      if (is_array($res_more)){
        foreach ($res_more as $key => $val)	{
          $data[$key] = $val;
        }
      }
      $data['list_pos'] = $this->List_Pos($pos, "option");
      $data['list_display'] = vnT_HTML::list_yesno("display", $data['display']);
     
    } else {
      $mess = $vnT->lang['not_found'] . " ID : " . $id;
      $url = $this->linkUrl;
      $func->html_redirect($url, $mess);
    }

    if (! isset($_SESSION['vnt_csrf_token'])) {
      $_SESSION['vnt_csrf_token'] = md5(uniqid(rand(), TRUE)) ;
    }
    $data['csrf_token'] = $_SESSION['vnt_csrf_token'] ;
    $data['err'] = $err;
    $data['link_action'] = $this->linkUrl . "&sub=edit&pos=$pos&id=$id";
    /*assign the array to a template variable*/
    $this->skin->assign('data', $data);
    $this->skin->parse("edit");
    return $this->skin->text("edit");
  }

  /**
   * function do_Del 
   * Xoa 1 ... n  gioi thieu 
   **/
  function do_Del ($pos, $lang)
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
      if ($id != 0) {
        $ids = $id;
      }
      if (isset($vnT->input["del_id"])) {
        $ids = implode(',', $vnT->input["del_id"]);
      }
      $query = 'DELETE FROM advertise WHERE l_id IN (' . $ids . ')';
      if ($ok = $DB->query($query)) {
        $mess = $vnT->lang["del_success"];
        //xoa cache
        $func->clear_cache();
      } else
        $mess = $vnT->lang["del_failt"];
    }

    $ext_page = str_replace("|", "&", $ext);
    $url = $this->linkUrl . "&sub=manage&pos=$pos&{$ext_page}";
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
    $id = $row['l_id'];
    $row_id = "row_" . $id;
    $pos = $row['pos'];

    $output['check_box'] = vnT_HTML::checkbox("del_id[]", $id, 0, " ");
    $link_edit = $this->linkUrl . '&sub=edit&pos=' . $pos . '&id=' . $id . '&ext=' . $row['ext_page'];
    $link_del = "javascript:del_item('" . $this->linkUrl . "&sub=del&sub=del&csrf_token=".$_SESSION['vnt_csrf_token']."&pos=" . $pos . "&id=" . $id . "&ext=" . $row['ext_page'] . "')";
    $output['order'] = "<input name=\"txt_Order[{$id}]\" type=\"text\" size=\"2\" maxlength=\"2\" style=\"text-align:center\" value=\"{$row['l_order']}\"  onkeypress=\"return is_num(event,'txtOrder')\" onchange='javascript:do_check($id)' />";
    
		switch ($row['type_ad'])
		{
			case 1 : 
				$output['picture'] = $row['img']  ;
			break	;
			case 2 :
				$output['picture'] = $row['title'] . "<br>Link : ". $row['link'];
			break	;
			case 3 : 				 
				$output['picture'] = $func->HTML($row['img']);
			break	;
			default :
				if($row['img'])
				{
					$src =  (strstr($row['img'],"http://")) ? $row['img'] :  MOD_DIR_UPLOAD . "/" .  $row['img'] ;	
				
					if ($row['type'] == "swf") {
						$picture = '<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,19,0"  width="' . $row['width'] . '"  height="' . $row['height'] . '" >
							<param name="movie" value="' . $src . '" />
							<param name="quality" value="high" />
							<embed src="' . $src . '" quality="high" pluginspage="http://www.macromedia.com/go/getflashplayer" type="application/x-shockwave-flash"   width="' . $row['width'] . '" height="' . $row['height'] . '"></embed>
						</object>';
					} else {
						$picture = "<img src=\"" . $src . "\">";
					}
				} else  $picture = "No image";
				
				$output['picture'] = $row['title'] . "<br>" . $picture . "<br>" . $row['link'];
			break;
		}
			
		
		
		
    
    $output['num_click'] = $row['num_click'] . "&nbsp;l&#7847;n";

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
  function do_Manage ($pos, $lang)
  {
    global $vnT, $func, $DB, $conf;

    if (! isset($_SESSION['vnt_csrf_token'])) {
      $_SESSION['vnt_csrf_token'] = md5(uniqid(rand(), TRUE)) ;
    }

    //update
    $rs_up = $this->do_ProcessUpdate($lang);
    $err = $rs_up['err'];


    $p = ((int) $vnT->input['p']) ? $p = $vnT->input['p'] : 1;
    $n = ($conf['record']) ? $conf['record'] : 30;
    $info_search['lang'] = $lang ;
    $res_where = $this->process_info_search($info_search) ;

    $where = $res_where['where'];
    $ext_page = $res_where['ext_page'];
    $ext = $res_where['ext'];



    $query = $DB->query("SELECT l_id FROM advertise WHERE lang='$lang' $where ");
    $totals = intval($DB->num_rows($query));
    $num_pages = ceil($totals / $n);
    if ($p > $num_pages) $p = $num_pages;
    if ($p < 1) $p = 1;
    $start = ($p - 1) * $n;
    $nav = $func->paginate($totals, $n, $ext, $p);
    $table['link_action'] = $this->linkUrl . "&sub=manage&pos=$pos{$ext}&p=$p"; 
		$ext_link = $ext."&p=$p" ;
    $table['title'] = array(
      'check_box' => "<input type=\"checkbox\" name=\"checkall\" id=\"checkall\" class=\"checkbox\" />|5%|center" , 
      'order' => $vnT->lang['order'] . "|10%|center" , 
      'picture' => $vnT->lang['logo'] . " |60%|center" , 
      'num_click' => "Click |7%|center" , 
      'action' => "Action|15%|center");
    $sql = "SELECT * FROM advertise WHERE lang='$lang' $where ORDER BY l_order LIMIT $start,$n";
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
        $row_field[$i]['row_id'] = "row_" . $row[$i]['l_id'];
        $row_field[$i]['ext'] = "";
      }
      $table['row'] = $row_field;
    } else {
      $table['row'] = array();
      $table['extra'] = "<div align=center class=font_err >" . $vnT->lang['no_have_advertise'] . "</div>";
    }
    $table['button'] = '<input type="button" name="btnHidden" value=" ' . $vnT->lang['hidden'] . ' " class="button" onclick="do_submit(\'do_hidden\')">&nbsp;';
    $table['button'] .= '<input type="button" name="btnDisplay" value=" ' . $vnT->lang['display'] . ' " class="button" onclick="do_submit(\'do_display\')">&nbsp;';
    $table['button'] .= '<input type="button" name="btnEdit" value=" ' . $vnT->lang['update'] . ' " class="button" onclick="do_submit(\'do_edit\')">&nbsp;';
    $table['button'] .= '<input type="button" name="btnDel" value=" ' . $vnT->lang['delete'] . ' " class="button" onclick="del_selected(\'' . $this->linkUrl . '&sub=del&sub=del&csrf_token='.$_SESSION['vnt_csrf_token'].'&pos=$pos&ext=' . $ext_page . '\')">';

    $table['csrf_token'] = $_SESSION['vnt_csrf_token'] ;
    $table_list = $func->ShowTable($table);
    $data['table_list'] = $table_list;


    foreach ($res_where['data'] as $key => $val) {
      $data[$key] = $val ;
    }

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