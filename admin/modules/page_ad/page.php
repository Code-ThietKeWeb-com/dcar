<?php
/*================================================================================*\
|| 							Name code : pages.php 		 		            	  ||
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
  var $module = "page";
  var $action = "page";
  
  /**
   * function vntModule ()
   * Khoi tao 
   **/
  function vntModule ()
  {
    global $Template, $vnT, $func, $DB, $conf;
    require_once ("function_" . $this->module . ".php");
    $this->skin = new XiTemplate(DIR_MODULE . DS . $this->module . "_ad" . DS . "html" . DS . $this->action . ".tpl");
    $this->skin->assign('CONF', $vnT->conf);
		$this->skin->assign('LANG', $vnT->lang);
		$this->skin->assign("DIR_JS", $vnT->dir_js);
    $lang = ($vnT->input['lang']) ? $lang = $vnT->input['lang'] : $func->get_lang_default();
    $this->linkUrl = "?mod=" . $this->module . "&act=" . $this->action . "&lang=" . $lang;
		$nd['menu'] = $func->getToolbar($this->module, $this->action, $lang);
    $nd['row_lang'] = $func->html_lang("?mod=" . $this->module . "&act=" . $this->action, $lang);
		
    switch ($vnT->input['sub'])
    {
      case 'add':
        $nd['f_title'] = $vnT->lang['add_page'];
        $nd['content'] = $this->do_Add($lang);
        break;
      case 'edit':
        $nd['f_title'] = $vnT->lang['edit_page'];
        $nd['content'] = $this->do_Edit($lang);
        break;
      case 'del':
        $this->do_Del($lang);
        break;
      case 'rebuild':
        $this->do_Rebuild($lang);
        break;

      default:
        $nd['f_title'] = $vnT->lang['manage_page'];
        $nd['content'] = $this->do_Manage($lang);
        break;
    } 
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
    if ($vnT->input['do_submit'] == 1)
    {
      $data = $_POST;
      
      $title = $vnT->input['title']; 
			$friendly_url = (trim($vnT->input['friendly_url'])) ? trim($vnT->input['friendly_url']) :  $func->make_url($title);
			
      // Check for existed
      $res_chk = $DB->query("SELECT * FROM pages WHERE friendly_url='{$friendly_url}' AND lang='$lang' ");
      if ($check = $DB->fetch_row($res_chk)) $err = $func->html_err("URL existed");

      if(empty($vnT->input['csrf_token']) || ($vnT->input['csrf_token'] != $_SESSION['vnt_csrf_token']) ) {
        $err =  $func->html_err($vnT->lang['err_csrf_token']) ;
      }

      // insert CSDL
      if (empty($err))
      { 
        $cot['title'] = $title;
				
				$cot['is_popup'] = $vnT->input['is_popup'];
        $cot['content'] = $DB->mySQLSafe($_POST['content']);
				//SEO
				$cot['friendly_url'] = $friendly_url ;
				$cot['friendly_title'] = (trim($vnT->input['friendly_title'])) ? trim($vnT->input['friendly_title']) : $title  ;
				$cot['metakey'] = $vnT->input['metakey'];
				$cot['metadesc'] = $vnT->input['metadesc']; 
        $cot['lang'] = $lang;
				
        $ok = $DB->do_insert("pages", $cot);
        if ($ok)
        {
					$page_id = $DB->insertid();
					$DB->query("UPDATE pages SET id_lang=$page_id WHERE id=".$page_id);
					
					//build seo_url
					$friendly_url = $cot['friendly_url'] ;
					$res_ck = $DB->query("SELECT * FROM seo_url WHERE name='".$cot['friendly_url']."' " )	 ;			
					if($DB->num_rows($res_ck))
					{
						$friendly_url = $friendly_url."-".time();  
						$existed = 1 ;
					}
					 
					$seo['modules'] = $this->module;
					$seo['action'] = $this->action;
					$seo['name_id'] = "itemID";
					$seo['item_id'] = $page_id;
					$seo['name'] = $friendly_url ;
					$seo['lang'] = $lang;					
					$seo['query_string'] = "mod:".$this->module."|act:".$this->action."|itemID:".$page_id;
					$seo['date_post'] = time();		
					$DB->do_insert("seo_url", $seo);

					if($existed) {
						$DB->query("UPDATE pages SET friendly_url='".$friendly_url."' WHERE id=".$page_id) ;	
						$cot['friendly_url'] = $friendly_url ;
					}
					 
          //check muti lang 					 
					$cot['id_lang'] = $page_id ;
					$pid="";
					$query_lang = $DB->query("select name from language WHERE name<>'" . $lang . "' ");
					while ($row_lang = $DB->fetch_row($query_lang)) {
						$cot['lang'] = $row_lang['name'];
						$DB->do_insert("pages", $cot);  						
						$pid = $DB->insertid();
						//build seo_url 
						$seo = array();
						$seo['modules'] = $this->module;
						$seo['action'] = $this->action;
						$seo['name_id'] = "itemID";
						$seo['item_id'] = $pid;
						$seo['name'] = $friendly_url ;
						$seo['lang'] = $row_lang['name'] ;					
						$seo['query_string'] = "mod:".$this->module."|act:".$this->action."|itemID:".$pid;
						$seo['date_post'] = time();	
						$DB->do_insert("seo_url", $seo);
						
					}

          unset($_SESSION['vnt_csrf_token']);

          //xoa cache
          $func->clear_cache();
          //insert adminlog
          $func->insertlog("Add", $_GET['act'], $page_id);
          $mess = $vnT->lang['add_success'];
          $url = $this->linkUrl . "&sub=add";
          $func->html_redirect($url, $mess);
        } else
        {
          $err = $func->html_err($vnT->lang['add_failt'] . $DB->debug());
        }
      }
    } 
		$data['list_is_popup'] = vnT_HTML::list_yesno("is_popup", $data['is_popup']);
    $data["html_content"] = $vnT->editor->doDisplay('content', $vnT->input['content'], '100%', '500', "Default");


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
   * Cap nhat admin
   **/
  function do_Edit ($lang)
  {
    global $vnT, $func, $DB, $conf;
    $id = (int) $vnT->input['id'];
    if ($vnT->input['do_submit'])
    {
      $data = $_POST;
      
			$title = $vnT->input['title']; 
			$friendly_url = (trim($vnT->input['friendly_url'])) ? trim($vnT->input['friendly_url']) :  $func->make_url($title);
			
			// Check for existed
      $res_chk = $DB->query("SELECT * FROM pages WHERE friendly_url='{$friendly_url}' AND lang='$lang' AND id<>$id ");
      if ($check = $DB->fetch_row($res_chk)) $err = $func->html_err("Name existed");

      if(empty($vnT->input['csrf_token']) || ($vnT->input['csrf_token'] != $_SESSION['vnt_csrf_token']) ) {
        $err =  $func->html_err($vnT->lang['err_csrf_token']) ;
      }

      if (empty($err))
      { 
			
        $cot['title'] = $title;
				$cot['is_popup'] = $vnT->input['is_popup'];
        $cot['content'] = $DB->mySQLSafe($_POST['content']);
				//SEO
				$cot['friendly_url'] = $friendly_url ;
				$cot['friendly_title'] = (trim($vnT->input['friendly_title'])) ? trim($vnT->input['friendly_title']) : $title ;
				$cot['metakey'] = $vnT->input['metakey'];
				$cot['metadesc'] = $vnT->input['metadesc']; 
				
        $ok = $DB->do_update("pages", $cot, "id=$id ");
        if ($ok)
        {
					//build seo_url
					$seo['sub'] = 'edit';
					$seo['modules'] = $this->module;
					$seo['action'] = $this->action;
					$seo['item_id'] = $id;
					$seo['friendly_url'] = $friendly_url ;
					$seo['lang'] = $lang;					
					$seo['query_string'] = "mod:".$this->module."|act:".$this->action."|itemID:".$id;
					$res_seo = $func->build_seo_url($seo);
					if($res_seo['existed']==1){
						$DB->query("UPDATE pages SET friendly_url='".$res_seo['friendly_url']."' WHERE lang='".$lang."' AND id=".$id) ;
					}

          unset($_SESSION['vnt_csrf_token']);
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
    $query = $DB->query("SELECT * FROM pages WHERE id=$id");
    if ($data = $DB->fetch_row($query))
    { 
			 
    } else
    {
      $mess = $vnT->lang['not_found'] . " ID : " . $id;
      $url = $this->linkUrl;
      $func->html_redirect($url, $mess);
    }
		
		$data['list_is_popup'] = vnT_HTML::list_yesno("is_popup", $data['is_popup']);
 
    $data["html_content"] = $vnT->editor->doDisplay('content', $data['content'], '100%', '500', "Default");


    if (! isset($_SESSION['vnt_csrf_token'])) {
      $_SESSION['vnt_csrf_token'] = md5(uniqid(rand(), TRUE)) ;
    }
    $data['csrf_token'] = $_SESSION['vnt_csrf_token'] ;

    $data['err'] = $err;
    $data['link_action'] = $this->linkUrl . "&sub=edit&id=$id";
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

      $query = 'DELETE FROM pages WHERE id IN (' . $ids . ')';

      if ($ok = $DB->query($query))
      {
        unset($_SESSION['vnt_csrf_token']);
        $DB->query("DELETE FROM seo_url WHERE item_id IN (" . $ids . ") ");
        $mess = $vnT->lang["del_success"];
      } else {
        $mess = $vnT->lang["del_failt"];
      }
    }

    $ext_page = str_replace("|", "&", $ext);
    $url = $this->linkUrl . "&{$ext_page}";
    $func->html_redirect($url, $mess);
  }



  /**
   * function do_Rebuild
   **/
  function do_Rebuild ($lang)
  {
    global $vnT, $func, $DB, $conf;

    $stt =0 ;
    $result = $DB->query("SELECT * FROM pages ");
    while($row = $DB->fetch_row($result))
    {

      $arr_seo[$stt]['table'] = "pages";
      $arr_seo[$stt]['table_id'] = "id";

      $arr_seo[$stt]['modules'] =  $this->module;
      $arr_seo[$stt]['action'] =  $this->action;
      $arr_seo[$stt]['name_id'] = "itemID";

      $arr_seo[$stt]['item_id'] = $row['id'];
      $arr_seo[$stt]['lang'] = $row['lang'];
      $arr_seo[$stt]['friendly_url'] = $row['friendly_url'];
      $arr_seo[$stt]['query_string'] = "mod:". $this->module."|act:".$this->action."|itemID:".$row['id'];
      $stt++;

    }

    //del cu
    $DB->query("DELETE FROM seo_url WHERE modules='".$this->module."' ") ;

    foreach ($arr_seo as $seo)
    {
      $friendly_url = $seo['friendly_url'] ;

      //check
      $res = $DB->query("SELECT id,name FROM seo_url WHERE modules='".$seo['modules']."' AND action='".$seo['action']."' AND name_id='".$seo['name_id']."' AND item_id=".$seo['item_id']." AND lang='".$seo['lang']."' ");
      if($row = $DB->fetch_row($res))
      {// update

        if($friendly_url != $row['name'])
        {
          $res_ck = $DB->query("SELECT id FROM seo_url WHERE name='".$seo['friendly_url']."' AND lang='".$seo['lang']."'  AND id<>".$row['id'] )	 ;
          if($row_ck = $DB->fetch_row($res_ck))
          {
            $friendly_url = $seo['friendly_url']."-".time();
            $DB->query("UPDATE ".$seo['table']." SET friendly_url='".$friendly_url."' WHERE  lang='".$seo['lang']."' AND ".$seo['table_id']."=".$seo['item_id']." ")	;
          }

          $cot['name'] = $friendly_url;
          $cot['date_post'] = time();
          $DB->do_update("seo_url", $cot,"modules='".$seo['modules']."' AND action='".$seo['action']."' AND name_id='".$seo['name_id']."' AND item_id=".$seo['item_id']." AND lang='".$seo['lang']."'");
        }
      }else{//insert

        $res_ck = $DB->query("SELECT * FROM seo_url WHERE name='".$seo['friendly_url']."' AND lang='".$seo['lang']."' " )	 ;
        if($row_ck = $DB->fetch_row($res_ck))
        {
          $friendly_url = $seo['friendly_url']."-".time();
          $DB->query("UPDATE ".$seo['table']." SET friendly_url='".$friendly_url."' WHERE  lang='".$seo['lang']."' AND ".$seo['table_id']."=".$seo['item_id']." ")	;
        }

        $cot['modules'] = $seo['modules'];
        $cot['action'] = $seo['action'];
        $cot['name_id'] = $seo['name_id'];
        $cot['item_id'] = $seo['item_id'];
        $cot['lang'] = $seo['lang'];
        $cot['name'] = $friendly_url;
        $cot['query_string'] = $seo['query_string'];
        $cot['date_post'] = time();

        $DB->do_insert("seo_url", $cot);
      }
    }

    //xoa cache
    $func->clear_cache();
    //insert adminlog
    $func->insertlog("Rebuild", $_GET['act'], 0);
    $err = "Rebuild Link Success";
    $url = $this->linkUrl;
    $func->html_redirect($url, $err);
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
    
    $output['title'] = "<strong><a href='" . $link_edit . "'>" . $func->HTML($row['title']) . "</a></strong>";
    $output['link_page'] =   $row['friendly_url'] . ".html";


    $link_display = $this->linkUrl . $row['ext_link']."&csrf_token=".$_SESSION['vnt_csrf_token'];
    if ($row['display'] == 1) {
      $display = "<a class='i-display' href='" . $link_display . "&do_hidden=$id' data-toggle='tooltip' data-placement='top'  title='" . $vnT->lang['click_do_hidden'] . "' ><i class=\"fa fa-eye\" aria-hidden=\"true\"></i></a>";
    } else {
      $display = "<a class='i-display'  href='" . $link_display . "&do_display=$id'  data-toggle='tooltip' data-placement='top'  title='" . $vnT->lang['click_do_display'] . "' ><i class=\"fa fa-eye-slash\" aria-hidden=\"true\"></i></a>";
    }

    $output['action'] = '<div class="action-buttons"><input name=h_id[]" type="hidden" value="' . $id . '" />';
    $output['action'] .= '<a href="' . $link_edit . '" class="i-edit"  data-toggle=\'tooltip\' data-placement=\'top\' title="Cập nhật"  ><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>';
    $output['action'] .= $display;
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


    //update
    if ($vnT->input["do_action"]) {
      if(empty($vnT->input['csrf_token']) || ($vnT->input['csrf_token'] != $_SESSION['vnt_csrf_token']) ) {
        $err =  $func->html_err($vnT->lang['err_csrf_token']) ;
      }else{
        unset($_SESSION['vnt_csrf_token']);

        //xoa cache
        $func->clear_cache();
        if ($vnT->input["del_id"])  $h_id = $vnT->input["del_id"];
        switch ($vnT->input["do_action"]) {
          case "do_edit":

            break;
          case "do_hidden":
            $mess .= "- " . $vnT->lang['hidden_success'] . " ID: <strong>";
            for ($i = 0; $i < count($h_id); $i ++) {
              $dup['display'] = 0;
              $ok = $DB->do_update("pages", $dup, "id=" . $h_id[$i]);
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
              $ok = $DB->do_update("pages", $dup, "id=" . $h_id[$i]);
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

    if((int)$vnT->input["do_display"]) {
      if(empty($vnT->input['csrf_token']) || ($vnT->input['csrf_token'] != $_SESSION['vnt_csrf_token']) ) {
        $err =  $func->html_err($vnT->lang['err_csrf_token']) ;
      }else{
        unset($_SESSION['vnt_csrf_token']);
        $ok = $DB->query("Update pages SET display=1 WHERE  id=".$vnT->input["do_display"]);
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
        $ok = $DB->query("Update pages SET display=0 WHERE   id=".$vnT->input["do_hidden"]);
        if($ok){
          $mess .= "- " . $vnT->lang['hidden_success'] . "  ID: <strong>".$vnT->input["do_hidden"] . "</strong><br>";
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
    $n = ($conf['record']) ? $conf['record'] : 30;
    $query = $DB->query("SELECT id FROM pages WHERE lang='$lang' ");
    $totals = intval($DB->num_rows($query));
    $num_pages = ceil($totals / $n);
    if ($p > $num_pages) $p = $num_pages;
    if ($p < 1) $p = 1;
    $start = ($p - 1) * $n;
    $nav = $func->paginate($totals, $n, $ext, $p);
    $table['link_action'] = $this->linkUrl . "{$ext}&p=$p"; 
		$ext_link = $ext."&p=$p" ;
		
    $table['title'] = array(
      'check_box' => "<input type=\"checkbox\" name=\"checkall\" id=\"checkall\" class=\"checkbox\" />|5%|center" ,
			'title' => $vnT->lang['title'] . " |30%|left" , 
			'link_page' => $vnT->lang['page_link'] . "|50%|left" ,
      'action' => "Action|15%|center"
    );
    $sql = "SELECT * FROM pages WHERE lang='$lang'  ORDER BY  id DESC  LIMIT $start,$n";
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
      $table['extra'] = "<div align=center class=font_err >" . $vnT->lang['no_have_page'] . "</div>";
    }
    $table['button'] = '<input type="button" name="btnHidden" value=" ' . $vnT->lang['hidden'] . ' " class="button" onclick="do_submit(\'do_hidden\')">&nbsp;';
    $table['button'] .= '<input type="button" name="btnDisplay" value=" ' . $vnT->lang['display'] . ' " class="button" onclick="do_submit(\'do_display\')">&nbsp;';
    $table['button'] .= '<input type="button" name="btnDel" value=" ' . $vnT->lang['delete'] . ' " class="button" onclick="del_selected(\'' . $this->linkUrl . '&sub=del&csrf_token='.$_SESSION['vnt_csrf_token'].'&ext=' . $ext_page . '\')">';
    $table['csrf_token'] = $_SESSION['vnt_csrf_token'] ;
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