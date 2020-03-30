<?php
/*================================================================================*\
|| 							Name code : about.php 		 		            	  ||
||  				Copyright @2008 by Thai Son - CMS vnTRUST                     ||
\*================================================================================*/
/**
 * @version : 2.0
 * @date upgrade : 09/01/2009 by Thai Son
 **/
if (!defined('IN_vnT')) {
  die('Hacking attempt!');
}
//load Model
include(dirname(__FILE__) . "/functions.php");

class vntModule extends Model
{
  public $model;
  var $output = "";
  var $skin = "";
  var $linkUrl = "";
  var $module = "menu";
  var $action = "menu";
  var $setting = array();

  /**
   * function vntModule ()
   * Khoi tao
   **/
  function vntModule()
  {
    global $Template, $vnT, $func, $DB, $conf;
    $this->skin = new XiTemplate(DIR_MODULE . DS . $this->module . "_ad" . DS . "html" . DS . $this->action . ".tpl");
    $this->skin->assign('CONF', $vnT->conf);
    $this->skin->assign('LANG', $vnT->lang);
    $this->skin->assign("DIR_JS", $vnT->dir_js);
    $this->skin->assign("DIR_IMAGE", $vnT->dir_images);

    $vnT->html->addScript("modules/" . $this->module . "_ad" . "/js/" . $this->module . ".js");

    $lang = ($vnT->input['lang']) ? $lang = $vnT->input['lang'] : $func->get_lang_default();
    $this->linkUrl = "?mod=" . $this->module . "&act=" . $this->action . "&lang=" . $lang;
    switch ($vnT->input['sub'])
    {
      case 'add':
        $nd['f_title'] = $vnT->lang['add_menu'];
        $nd['content'] = $this->do_Add($lang);
        break;
      case 'edit':
        $nd['f_title'] = $vnT->lang['edit_menu'];
        $nd['content'] = $this->do_Edit($lang);
        break;
      case 'del':
        $this->do_Del($lang);
        break;
      default:
        $nd['f_title'] = $vnT->lang['manage_menu'];
        $nd['content'] = $this->do_Manage($lang);
        break;
    }
    $nd['menu'] = $func->getToolbar($this->module, $this->action."&pos=".$vnT->input['pos'], $lang);
    $nd['row_lang'] = $func->html_lang("?mod=" . $this->module . "&act=" . $this->action."&pos=".$vnT->input['pos'], $lang);
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
    $data['pos'] = $vnT->input['pos'];
    if ($vnT->input['do_submit'] == 1)
    {
      $data = $_POST;

      if(empty($vnT->input['csrf_token']) || ($vnT->input['csrf_token'] != $_SESSION['vnt_csrf_token']) ) {
        $err =  $func->html_err($vnT->lang['err_csrf_token']) ;
      }

      // insert CSDL
      if (empty($err))
      {
        
        $cot['target'] = $vnT->input['target'];				
        $cot['parentid'] = $vnT->input['parentid'];
        $cot['pos'] = $vnT->input['pos']; 
        $ok = $DB->do_insert("menu", $cot);
        if ($ok)
        {					
					 $menu_id = $DB->insertid();
          
          //update cat content
          $cot_d['menu_id'] = $menu_id;
					$cot_d['name'] = $vnT->input['name'];
          $cot_d['title'] = $vnT->input['title'];       
					$cot_d['menu_link'] = trim($vnT->input['menu_link']) ;
          $cot_d['picture'] = $vnT->input['picture'];
					
          $query_lang = $DB->query("select name from language ");
          while ($row = $DB->fetch_row($query_lang))
          {
            $cot_d['lang'] = $row['name'];
            $DB->do_insert("menu_desc", $cot_d);
          }

          unset($_SESSION['vnt_csrf_token']);
          //xoa cache
          $func->clear_cache();
          //insert adminlog
          $func->insertlog("Add", $_GET['act'], $DB->insertid());

          $mess = $func->html_mess( $vnT->lang['add_success'] ,"jAlert");
          $url = $this->linkUrl . "&sub=add&pos=" . $vnT->input['pos'];
          $func->header_redirect($url, $mess);
        } else
        {
          $err = $func->html_err($vnT->lang['add_failt'] . $DB->debug());
        }
      }
    }

    $data['list_target'] = $this->List_Target($data['target']);
    $data['list_parent'] = $this->List_Parent($data['pos'], $data['parentid'], $lang);
    $data['list_pos'] = $this->List_Pos($data['pos'] );

    if (! isset($_SESSION['vnt_csrf_token'])) {
      $_SESSION['vnt_csrf_token'] = md5(uniqid(rand(), TRUE)) ;
    }
    $data['csrf_token'] = $_SESSION['vnt_csrf_token'] ;

    $data['err'] = $func->display_error_message($err) ;
    $data['link_action'] = $this->linkUrl . "&sub=add";
    /*assign the array to a template variable*/
    $this->skin->assign('data', $data);
    $this->skin->parse("edit");
    return $this->skin->text("edit");
  }
  
  /**
   * function do_Edit 
   * Cap nhat admin
   **/
  function do_Edit ($lang)
  {
    global $vnT, $func, $DB, $conf;
    
    $id = (int) $vnT->input['id'];
    $ext = $vnT->input['ext'];
    if ($vnT->input['do_submit'])
    {
      $data = $_POST;
      $parentid = $vnT->input['parentid'];
      // Check for Error
      if ($parentid == $id) $err = $func->html_err("Danh mục cha không hợp lệ");

      if(empty($vnT->input['csrf_token']) || ($vnT->input['csrf_token'] != $_SESSION['vnt_csrf_token']) ) {
        $err =  $func->html_err($vnT->lang['err_csrf_token']) ;
      }

      if (empty($err))
      {
        
        $cot['pos'] = $vnT->input['pos'];
				$cot['target'] = $vnT->input['target'];				
        $cot['parentid'] = $vnT->input['parentid'];        
				
        $ok = $DB->do_update("menu", $cot, "menu_id=$id ");
        if ($ok)
        {
					$cot_d['title'] = $vnT->input['title'];       
					$cot_d['menu_link'] = trim($vnT->input['menu_link']) ;
					$cot_d['name'] = $vnT->input['name'];
          $cot_d['picture'] = $vnT->input['picture'];
					$DB->do_update("menu_desc", $cot_d, "menu_id=$id AND lang='$lang'");

          unset($_SESSION['vnt_csrf_token']);

          //xoa cache
          $func->clear_cache();
          //insert adminlog
          $func->insertlog("Edit", $_GET['act'], $id);
          $err = $vnT->lang["edit_success"];
          $ext_page = str_replace("|", "&", $ext);
          $url = $this->linkUrl . "&pos=" . $vnT->input['pos'];
          $func->html_redirect($url, $err);
        } else
          $err = $func->html_err($vnT->lang["edit_failt"] . $DB->debug());
      }
    }
    $query = $DB->query("SELECT  *  FROM menu n ,menu_desc nd WHERE n.menu_id=nd.menu_id AND lang='$lang' AND n.menu_id=$id");
    if ($data = $DB->fetch_row($query))
    {

      $data['style_upload']='';  $data['pic']='';
      if ($data['picture'])
      {
        $data['pic'] = "<img src='" . $data['picture'] . "' ><br>";
        $data['style_upload'] = "style='display:none' ";
      }
      $data['list_target'] = $this->List_Target($data['target']);
      $data['list_parent'] = $this->List_Parent($data['pos'], $data['parentid'], $lang);
      $data['list_pos'] = $this->List_Pos($data['pos'] );
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
    $data['link_action'] = $this->linkUrl . "&sub=edit&id=$id&ext={$ext}";
    /*assign the array to a template variable*/
    $this->skin->assign('data', $data);
    $this->skin->parse("edit");
    return $this->skin->text("edit");
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
      $res = $DB->query("SELECT * FROM menu WHERE menu_id IN (" . $ids . ") ");
      if ($DB->num_rows($res))
      {
        while ($row = $DB->fetch_row($res))  {
          $this->del_submenu($row['menu_id']);
        }
        $mess = $vnT->lang["del_success"];
        //xoa cache
        $func->clear_cache();
      } else
      {
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
  function render_row ($row_info, $lang)
  {
    global $func, $DB, $conf, $vnT;
    $row = $row_info;
    // Xu ly tung ROW
    $id = $row['menu_id'];
    $row_id = "row_" . $id;
    $output['row_id'] = $row_id;
    $output['check_box'] = vnT_HTML::checkbox("del_id[]", $id, 0, " ");
    $link_edit = $this->linkUrl . '&sub=edit&id=' . $id . '&ext=' . $row['ext_page'];
    $link_del = "javascript:del_item('" . $this->linkUrl . "&sub=del&csrf_token=".$_SESSION['vnt_csrf_token']."&id=" . $id . "&ext=" . $row['ext_page'] . "')";
    if ($row['picture'])
    {
      $output['picture'] = '<img src="' . $conf['rooturl'] . $row['picture'] . '"  />';
    } else
    {
      $output['picture'] = "No Icon";
    }
    $output['order'] = $row['ext'] . "<input name=\"txt_Order[{$id}]\" type=\"text\" size=\"2\" maxlength=\"2\" style=\"text-align:center\" value=\"{$row['menu_order']}\"  onkeypress=\"return is_num(event,'txtOrder')\" onchange='javascript:do_check($id)' />";
    
		$output['name'] = $row['name'];
		 
		$title = $func->HTML($row['title']);
    if ($row['ext'])
    {
      $output['title'] = $row['ext'] . "&nbsp;<a href=\"{$link_edit}\">" . $title . "</a>";
    } else
    {
      $row['ext'] = "&nbsp;";
      $output['title'] = "<strong><a href=\"{$link_edit}\">" . $title . "</a></strong>";
    }
    
    $output['menu_link'] = $row['menu_link'] . "&nbsp;(" . $row['target'] . ")";
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
  function do_Manage ($lang)
  {
    global $vnT, $func, $DB, $conf;

    //update
    $rs_up = $this->do_ProcessUpdate($lang);
    $err = $rs_up['err'];

    if (! isset($_SESSION['vnt_csrf_token'])) {
      $_SESSION['vnt_csrf_token'] = md5(uniqid(rand(), TRUE)) ;
    }
    $p = ((int) $vnT->input['p']) ? $p = $vnT->input['p'] : 1;
    $n = ($conf['record']) ? $conf['record'] : 30;
    $pos = (isset($vnT->input['pos'])) ? $vnT->input['pos'] : 'horizontal';

    $info_search['lang'] = $lang ;
    $res_where = $this->process_info_search($info_search) ;

    $where = $res_where['where'];
    $ext_page = $res_where['ext_page'];
    $ext = $res_where['ext'];

		
    $query = $DB->query("SELECT n.menu_id  FROM menu n ,menu_desc nd WHERE n.menu_id=nd.menu_id AND lang='$lang' AND parentid=0 $where ");
    $totals = intval($DB->num_rows($query));
    $num_pages = ceil($totals / $n);
    if ($p > $num_pages) $p = $num_pages;
    if ($p < 1) $p = 1;
    $start = ($p - 1) * $n;
    $nav = $func->paginate($totals, $n, $ext, $p);
    $sql = "SELECT  *  FROM menu n ,menu_desc nd WHERE n.menu_id=nd.menu_id AND lang='$lang' AND parentid=0 $where  ORDER BY  menu_order ASC , n.menu_id ASC   LIMIT $start,$n";
    $result = $DB->query($sql);
    if ($DB->num_rows($result))
    {
      $i = 0;
      while ($row = $DB->fetch_row($result))
      {
        $i ++;
        $row['ext'] = "";
        $row['ext_page'] = $ext_page;
				$row['ext_link'] = $ext."&p=".$p ;
        $row_info = $this->render_row($row, $lang);
        $row_info['class'] = ($i % 2) ? "row1" : "row0";
        $this->skin->assign('row', $row_info);
        $this->skin->parse("manage.html_row");
        $n = 1;
        $this->Row_Sub($row['menu_id'], $n, $i, $lang);
      }
    } else
    {
      if ($pos)
      {
        $mess = $vnT->lang['no_have_menu'];
      } else
      {
        $mess = $vnT->lang['select_position'];
      }
      $this->skin->assign('mess', $mess);
      $this->skin->parse("manage.html_row_no");
    }
    $data['link_action'] = $this->linkUrl . "&p=$p" . $ext;
    $data['button'] = '<input type="button" name="btnHidden" value=" ' . $vnT->lang['hidden'] . ' " class="button" onclick="do_submit(\'do_hidden\')">&nbsp;';
    $data['button'] .= '<input type="button" name="btnDisplay" value=" ' . $vnT->lang['display'] . ' " class="button" onclick="do_submit(\'do_display\')">&nbsp;';
    $data['button'] .= '<input type="button" name="btnEdit" value=" ' . $vnT->lang['update'] . ' " class="button" onclick="do_submit(\'do_edit\')">&nbsp;';
    $data['button'] .= '<input type="button" name="btnDel" value=" ' . $vnT->lang['delete'] . ' " class="button" onclick="del_selected(\'' . $this->linkUrl . '&sub=del&csrf_token='.$_SESSION['vnt_csrf_token'].'&ext=' . $ext_page . '\')">';
    $data['csrf_token'] = $_SESSION['vnt_csrf_token'] ;
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
  
  //===========Row_Sub=========
  function Row_Sub ($cid, $n, $i, $lang)
  {
    global $func, $DB, $conf, $vnT;
    $textout = "";
    $space = "&nbsp;&nbsp;&nbsp;&nbsp;";
    $n1 = $n;
    $sql = "SELECT * FROM menu n ,menu_desc nd WHERE n.menu_id=nd.menu_id AND lang='$lang' AND parentid={$cid} ORDER BY menu_order ASC ,n.menu_id ASC ";
    //	print "sql = ".$sql."<br>";
    $result = $DB->query($sql);
    while ($row = $DB->fetch_row($result))
    {
      $i ++;
			$row['ext_link'] = "&pos=" .$_GET['pos']."&p=".$_GET['p'] ;
      $row['ext'] = "&nbsp;<img src=\"{$vnT->dir_images}/line3.gif\" align=\"absmiddle\"/>";
      $width = "";
      for ($k = 1; $k < $n1; $k ++)
      {
        $width .= $space;
        $row['ext'] = $width . "&nbsp;<img src=\"{$vnT->dir_images}/line3.gif\" align=\"absmiddle\"/>";
      }
      $row_info = $this->render_row($row, $lang);
      $row_info['class'] = ($i % 2) ? "row1" : "row0";
      $this->skin->assign('row', $row_info);
      $this->skin->parse("manage.html_row");
      $n = $n1 + 1;
      $this->Row_Sub($row['menu_id'], $n, $i, $lang);
    }
  }
  // end class
}
$vntModule = new vntModule();
?>