<?php
/*================================================================================*\
|| 							Name code : config.php 		 		            	  ||
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
  var $module = "config";
  var $action = "config";
  /**
   * function vntModule ()
   * Khoi tao 
   **/
  function vntModule ()
  {
    global $Template, $vnT, $func, $DB, $conf;
    require_once ("function_config.php");
    $this->skin = new XiTemplate(DIR_MODULE . DS . "config_ad" . DS . "html" . DS . "config.tpl");
    $this->skin->assign('DIR_IMAGE', $vnT->dir_images);
		$this->skin->assign('LANG', $vnT->lang);
    $lang = ($vnT->input['lang']) ? $lang = $vnT->input['lang'] : $func->get_lang_default();
    $this->linkUrl = "?mod=config&act=config&lang=" . $lang;
    switch ($vnT->input['sub']) {
      case 'edit':
        $nd['content'] = $this->do_Edit($lang);
      break;
      case 'robots':
        $nd['content'] = $this->do_Robots($lang);
        break;
      default:
        $nd['f_title'] = $vnT->lang['manage_config'];
        $nd['content'] = $this->do_Manage($lang);
      break;
    }
    $nd['menu'] =  $func->getToolbar_Small($this->module, $this->action, $lang);
		$nd['icon'] = 'icon-'.$this->module;
    $Template->assign("data", $nd);
    $Template->parse("box_main");
    $vnT->output .= $Template->text("box_main");
  }

  /**
   * function do_Edit 
   * Cap nhat gioi thieu 
   **/
  function do_Edit ($lang)
  {
    global $vnT, $func, $DB, $conf; 
    if ($vnT->input['do_submit']) {

      if(empty($vnT->input['csrf_token']) || ($vnT->input['csrf_token'] != $_SESSION['vnt_csrf_token']) ) {
        $mess =  $vnT->lang['err_csrf_token'];
      }else{
        $cot = $_POST['cot'];         
        $arr_old = $func->fetchDbConfig();
        $ok = $func->writeDbConfig("config", $cot, $arr_old);
        if ($ok) {
          unset($_SESSION['vnt_csrf_token']);

          //xoa cache
          $func->clear_cache();

          $mess = $vnT->lang["edit_success"];
        } else {
          $mess = $vnT->lang["edit_failt"];
        }

        $func->insertlog("Update", $_GET['act'], 1);
      }

    }
    $url = $this->linkUrl;
    $func->html_redirect($url, $mess);
  }

  /**
   * function do_Robots
   * Cap nhat gioi thieu
   **/
  function do_Robots ($lang)
  {
    global $vnT, $func, $DB, $conf;
    $err = '';

    if ($vnT->input['do_submit'])
    {
      if(empty($vnT->input['csrf_token']) || ($vnT->input['csrf_token'] != $_SESSION['vnt_csrf_token']) ) {
        $mess =  $vnT->lang['err_csrf_token'];
      }else{
        $text_robots = $_POST['robots'] ;

        $file_robots = $conf['rootpath']."robots.txt" ;
        @chmod($file_robots, 0666);
        if($handle = @fopen($file_robots, "w"))
        {
          @fwrite($handle, $text_robots, strlen($text_robots));
          @fclose($handle);
        }else{
          $err .= "Không thể truy cập file robots.txt . Vui lòng Chmod lại 666";
        }

        $mess  = ($err) ? $err :   "Cập nhật thành công";
        unset($_SESSION['vnt_csrf_token']);

        $func->insertlog("Update", $_GET['act'], 1);
      }

    }



    $url = $this->linkUrl;
    $func->html_redirect($url, $mess);
  }


  /**
   * function do_Manage() 
   * Quan ly cac gioi thieu
   **/
  function do_Manage ($lang)
  {
    global $vnT, $func, $DB, $conf;
		
		$vnT->html->addStyleSheet( $vnT->dir_js . "/jquery_ui/themes/base/ui.all.css");
		$vnT->html->addStyleSheet( $vnT->dir_js . "/jquery_ui/custom.css");
		$vnT->html->addScript($vnT->dir_js . "/jquery_ui/ui.core.js");		
		$vnT->html->addScript($vnT->dir_js . "/jquery_ui/ui.tabs.js"); 
		$vnT->html->addScript($vnT->dir_js . "/jquery_plugins/jquery_cookie.js"); 		
    $vnT->html->addScriptDeclaration("$(function() {	 
	 		$('#tabs').tabs({ cookie: { expires: 30 } });
    });");
		
    $data = $func->fetchDbConfig();

    $data['list_login_attempt'] =   vnT_HTML::list_yesno("cot[login_attempt]", $data['login_attempt']);
    $data['list_captcha_admin'] =  vnT_HTML::list_yesno("cot[captcha_admin]", $data['captcha_admin']);
    $data['list_captcha_type'] =  vnT_HTML::selectbox("cot[captcha_type]", array( 'session_sec_code' => 'Captcha mặc định' ,'reCAPTCHA' => 'reCAPTCHA'  ), $data['captcha_type']);

    $data['list_method_email'] = List_Method_Email($data["method_email"]);
		$data['list_smtp_type_encryption']  = List_Smtp_Type_Encryption ($data["smtp_type_encryption"]); 
		$data['list_smtp_autentication']  = vnT_HTML::list_yesno("cot[smtp_autentication]", $data['smtp_autentication']);
    $data['list_smtp_from'] =  vnT_HTML::list_yesno("cot[smtp_from]", $data['smtp_from']);
    
    $data['list_backup'] = List_Backup($data["auto_backup"]);
    $data['list_backup_email'] = List_Backup_Email($data["backup_email"]);
    $data['list_cache'] = List_Cache($data["cache"]);
    $data['list_skin'] = List_Skin($data['skin']);
    $data['list_module'] = List_Module_Show($data['module']);
    $data['list_editor'] = List_Editor($data['editor']);
    $data['list_counter'] = List_Counter($data["counter"]);
    $data['phpversion'] = phpversion();
    $data['mysql_get_server_info'] = mysql_get_server_info();
    $data['SERVER_SOFTWARE'] = $_SERVER["SERVER_SOFTWARE"];
    $data['HTTP_USER_AGENT'] = $_SERVER["HTTP_USER_AGENT"];
    $data['REMOTE_ADDR'] = $_SERVER['REMOTE_ADDR'];
    $data['lis_web_close'] = List_Web_Close($data['web_close']);
		
	
		
		$data['menu_admin'] = $this->Menu_Admin ($lang);
		$data['link_menu_admin'] = $this->linkUrl . "&sub=menu_admin";



    $err_robots='';
    $data['link_robots'] = $this->linkUrl . "&sub=robots";
    $file_robots = $conf['rootpath']."robots.txt" ;
    if (! is_writable($file_robots)) {
      $err_robots .= '<p>'.str_replace("{file}","robots.txt",$vnT->lang['mess_file_not_write']).'</p>' ;
    }else{
      $robots ='';
      if (file_exists($file_robots)) {

        if ($FH = @fopen($file_robots, 'rb')) {
          $robots = @fread($FH, filesize($file_robots));
          @fclose($FH);
        }
      }
      $data['robots'] =  $robots ;
    }
    if($err_robots) {
      $data['err_robots'] = $func->html_err($err_robots);
    }


    ob_start();
    phpinfo(INFO_GENERAL | INFO_CONFIGURATION | INFO_MODULES);
    $phpinfo = ob_get_contents();
    ob_end_clean();
    preg_match_all('#<body[^>]*>(.*)</body>#siU', $phpinfo, $phpinfo);
    $phpinfo = preg_replace('#<table#', '<table class="adminlist" align="center"', $phpinfo[1][0]);
    $phpinfo = preg_replace('#(\w),(\w)#', '\1, \2', $phpinfo);
    $phpinfo = preg_replace('#border="0" cellpadding="3" width="600"#', 'border="0" cellspacing="1" cellpadding="4" width="95%"', $phpinfo);
    $phpinfo = preg_replace('#<hr />#', '', $phpinfo);
    $phpinfo = str_replace('<div class="center">', '', $phpinfo);
    $phpinfo = str_replace('</div>', '', $phpinfo);

    $data['phpinfo'] = $phpinfo;




    if (! isset($_SESSION['vnt_csrf_token'])) {
      $_SESSION['vnt_csrf_token'] = md5(uniqid(rand(), TRUE)) ;
    }
    $data['csrf_token'] = $_SESSION['vnt_csrf_token'] ;

    $data['link_action'] = $this->linkUrl . "&sub=edit";
		
    /*assign the array to a template variable*/
    $this->skin->assign('data', $data);
    $this->skin->parse("manage");
    return $this->skin->text("manage");
  }
	
	
	

	
	 //============
  function Menu_Admin ($lang)
  {
    global $vnT, $func, $DB, $conf;
     
      //update
    if (isset($_POST["do_action"])) {
      //xoa cache
      $func->clear_cache();
      if (isset($_POST["del_id"]))  $h_id = $_POST["del_id"];     
			switch ($_POST["do_action"]) 
			{
        case "do_edit":
         {
            $mess .= "- " . $vnT->lang['edit_success'] . " ID: <strong>";
            $str_mess = "";
            if (isset($_POST["txt_Order"]))   $arr_order = $_POST["txt_Order"];
						
            for ($i = 0; $i < count($h_id); $i ++) {
              $dup['displayorder'] = $arr_order[$h_id[$i]];
						  $ok = $DB->do_update("admin_menu", $dup, "id={$h_id[$i]}");
              if ($ok) {
                $str_mess .= $h_id[$i] . ", ";
              }
            }
            $mess .= substr($str_mess, 0, - 2) . "</strong><br>";
            $err = $func->html_mess($mess);
          }
        break;        
      }
    }
    
		
    $data['link_action'] = $this->linkUrl . "&p=$p";    
    $sql = "SELECT * FROM admin_menu where parentid=0 AND display=1  ORDER BY  displayorder  ";
    //print "sql = ".$sql."<br>";
    $result = $DB->query($sql);
    if ($DB->num_rows($result))
    {
      $i = 0;
      while ($row = $DB->fetch_row($result))
      {
        $i ++;
        $row['ext'] = "";
				$row['ext_link'] = "&p=".$p ;
        $row_info = $this->render_row($row, $lang);
        $row_info['class'] = ($i % 2) ? "row1" : "row0";
        $this->skin->assign('row', $row_info);
        $this->skin->parse("html_menu_admin.html_row");
        $n = 1;
        $this->Row_Sub($row['id'], $n, $i, $lang);
      }
    }
    else
    {
      $mess = "No item";
      $this->skin->assign('mess', $mess);
      $this->skin->parse("html_menu_admin.html_row_no");
    }
    
    $data['button'] = '<input type="button" name="btnEdit" value=" ' . $vnT->lang['update'] . ' " class="button" onclick="do_submit(\'do_edit\')">&nbsp;';
     
    $data['totals'] = $totals;
    $data['err'] = $err;
    $data['nav'] = $nav;


    if (! isset($_SESSION['vnt_csrf_token'])) {
      $_SESSION['vnt_csrf_token'] = md5(uniqid(rand(), TRUE)) ;
    }
    $data['csrf_token'] = $_SESSION['vnt_csrf_token'] ;

    /*assign the array to a template variable*/
    $this->skin->assign('data', $data);
    $this->skin->parse("html_menu_admin");
    return $this->skin->text("html_menu_admin");
  }

   /**
   * function Row_Sub() 
   * 
   **/
  function Row_Sub ($cid, $n, $i, $lang)
  {
    global $func, $DB, $conf, $vnT;
    $textout = "";
    $space = "&nbsp;&nbsp;&nbsp;&nbsp;";
    $n1 = $n;
    $sql = "SELECT * FROM admin_menu WHERE parentid='{$cid}' AND display=1  ORDER BY displayorder ";
    //	print "sql = ".$sql."<br>";
    $result = $DB->query($sql);
    while ($row = $DB->fetch_row($result))
    {
      $i ++;
			$row['ext_link'] = "&p=".$_GET['p'] ;
      $row['ext'] = "&nbsp;<img src=\"{$vnT->dir_images}/line3.gif\" align=\"absmiddle\"/>";
			$width="";
      for ($k = 1; $k < $n1; $k ++)
      {
        $width.=$space;
				
        $row['ext'] = $width . "&nbsp;<img src=\"{$vnT->dir_images}/line3.gif\" align=\"absmiddle\"/>";
      }
      
      $row_info = $this->render_row($row, $lang);
      $row_info['class'] = ($i % 2) ? "row1" : "row0";
      $this->skin->assign('row', $row_info);
      $this->skin->parse("html_menu_admin.html_row");
      
      $n = $n1 + 1;
      $textout .= $this->Row_Sub($row['id'], $n, $i, $lang);
    }
		    
    return $textout;
  }
	
	
			//================
  function render_row ($row_info, $lang)
  {
    global $func, $DB, $conf, $vnT;
    $str_title = "title_" . $lang;
    $row = $row_info;
    // Xu ly tung ROW
    $id = $row['id'];
    $row_id = "row_" . $id;
    $output['row_id'] = $row_id;
    $output['check_box'] = "<input type=\"checkbox\" name=\"del_id[]\" value=\"{$id}\" class=\"checkbox\" onclick=\"select_row('{$row_id}')\">";
    $link_edit = $this->linkUrl . "&sub=edit&id={$id}";
    $link_del = "javascript:del_item('" . $this->linkUrl . "&sub=del&id={$id}')";
    $output['order'] = $row['ext'] . "<input name=\"txt_Order[{$id}]\" type=\"text\" size=\"2\" maxlength=\"3\" style=\"text-align:center\" value=\"{$row['displayorder']}\"  onkeypress=\"return is_num(event,'txtOrder')\" onchange='javascript:do_check($id)' />";
    $output['g_name'] = $row['g_name'];
    $output['title'] = $row['ext'] . "<strong><a href=\"{$link_edit}\">" . $func->HTML($row[$str_title]) . "</a></strong>";
    $output['mod'] = (! empty($row['block'])) ? $row['block'] : $row['module'];
    $output['act'] = $row['act'];
    $output['sub'] = $row['sub'];
    if ($row['display'] == 1) {
      $display = "<img src=\"{$vnT->dir_images}/dispay.gif\" width=15  />";
    } else {
      $display = "<img src=\"{$vnT->dir_images}/nodispay.gif\"  width=15 />";
    }
    $output['action'] = "
		<input name=\"h_id[]\" type=\"hidden\" value=\"{$id}\" />
		<a href=\"{$link_edit}\"><img src=\"{$vnT->dir_images}/edit.gif\"  alt=\"Edit \"></a>&nbsp;	
		{$display} &nbsp;
	 	<a href=\"{$link_del}\"><img src=\"{$vnT->dir_images}/delete.gif\"  alt=\"Delete \"></a>";
    return $output;
  }
  // end class
}
?>