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
  var $module = "social_network";
 	var $action = "config_module";
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
    $this->skin->assign('DIR_IMAGE', $vnT->dir_images);
    //$vnT->html->addScript(DIR_MODULE . DS . $this->module . "_ad/js/" . $this->module . ".js");
    $lang = ($vnT->input['lang']) ? $lang = $vnT->input['lang'] : $func->get_lang_default();
    $this->linkUrl = "?mod=" . $this->module . "&act=" . $this->action . "&lang=" . $lang;
		
    switch ($vnT->input['sub']) {
      case 'edit':
        $nd['content'] = $this->do_Edit($lang);
      break; 
      default:
        $nd['f_title'] = $vnT->lang['config_module'];
        $nd['content'] = $this->do_Manage($lang);
      break;
    }
    $nd['menu'] =  $func->getToolbar_Small($this->module, $this->action, $lang);
		$nd['row_lang'] = $func->html_lang("?mod=" . $this->module . "&act=" . $this->action, $lang);
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
    if ($vnT->input['btnUpdate']) 
		{

      if(empty($vnT->input['csrf_token']) || ($vnT->input['csrf_token'] != $_SESSION['vnt_csrf_token']) ) {
        $mess =  $vnT->lang['err_csrf_token'] ;
      }else{

        $arr_modules = array();
        $result = $DB->query("select mod_name from modules order by ordering ASC, id ASC");
        while ($row_mod = $DB->fetch_row($result)) {
          $arr_modules[] = $row_mod['mod_name'];
        }

        foreach ($arr_modules as $modules)
        {
          $social_network_share = @implode(",",$_POST[$modules."_social_network_share"]) ;
          $social_network_like = @implode(",",$_POST[$modules."_social_network_like"]) ;

          $dup['social_network']	= $_POST[$modules."_social_network"] ;
          $dup['facebook_comment']	= $_POST[$modules."_facebook_comment"] ;
          $dup['social_network_share']	= $social_network_share ;
          $dup['social_network_like']	= $social_network_like ;
          $DB->do_update($modules."_setting",$dup," lang='$lang'" );
        }

        unset($_SESSION['vnt_csrf_token']);

        //xoa cache
        $func->clear_cache();
        $mess = $vnT->lang["edit_success"];
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
		$vnT->html->addScript($vnT->dir_js . "/jquery_plugins/jquery.cookie.js"); 		
    $vnT->html->addScriptDeclaration("$(function() {	 
	 		$('#tabs').tabs({ cookie: { expires: 30 } });
    });");
		
		$link_action = $this->linkUrl . "&sub=edit";
		$arr_onoff = array("0" => "Off" , 1 => "On") ;
		$arr_share = array("facebook" => "Facebook" , "google" => "Google Plus" , "twitter" => "Twitter") ;
		$arr_like = array("facebook" => "Facebook" , "google" => "Google Plus"  ) ;
   	
		$res_a = $DB->query("select social_network,social_network_share,social_network_like from about_setting WHERE lang='$lang' ");
		$row_a = $DB->fetch_row($res_a);
		$data['about_social_network'] = vnT_HTML::selectbox("about_social_network",$arr_onoff,$row_a['social_network']);
		
		$data['about_facebook_comment'] = vnT_HTML::selectbox("about_facebook_comment",$arr_onoff,$row_a['facebook_comment']) ;
		$data['about_list_share'] = List_CheckBox("about_social_network_share",$arr_share,$row_a['social_network_share']);
		$data['about_list_like'] = List_CheckBox("about_social_network_like",$arr_like,$row_a['social_network_like']);
		
		
		
		$list_li = ''; $list_div ='';
		$result = $DB->query("select * from modules order by ordering ASC, id ASC");
		while ($row_mod = $DB->fetch_row($result)) 
		{ 
			$tbl_setting = $row_mod['mod_name']."_setting";
			$res_s = $DB->query("SELECT * FROM {$tbl_setting} WHERE lang='$lang'");
			$row_s = $DB->fetch_row($res_s);
			$list_li .= '<li><a href="#tab-'.$row_mod['mod_name'].'"><span>SEO Module '.strtoupper($row_mod['mod_name']).'</span></a></li>';
			$list_div .='<div id="tab-'.$row_mod['mod_name'].'"><br>
	 <table width="100%"  border="0"  cellspacing="1" cellpadding="1" class="admintable">
		 
		 <tr >
			<td width="25%" align="right" class="row1"> <strong>Chức năng MXH : </strong></td>
			<td  align="left" class="row0">'. vnT_HTML::selectbox($row_mod['mod_name']."_social_network",$arr_onoff,$row_s['social_network']) .'</td>
		</tr>
		 <tr >
			<td  align="right" class="row1"> <strong>Share MXH : </strong></td>
			<td  align="left" class="row0">'. List_CheckBox($row_mod['mod_name']."_social_network_share",$arr_share,$row_s['social_network_share']).'</td>
		</tr>
		<tr>
			<td align="right" class="row1"><strong> Like MXH : </strong></td>
			<td  align="left" class="row0">'.List_CheckBox($row_mod['mod_name']."_social_network_like",$arr_like,$row_s['social_network_like']).'</td>
		</tr> 
		
		<tr >
			<td width="25%" align="right" class="row1"> <strong>Facebook Comment : </strong></td>
			<td  align="left" class="row0">'. vnT_HTML::selectbox($row_mod['mod_name']."_facebook_comment",$arr_onoff,$row_s['facebook_comment']).'</td>
		</tr>
		
   </table>
	</div>';
		} 
		$data['list_li'] = $list_li ;
		$data['list_div'] = $list_div ;
    $data['link_action'] =  $link_action ;


    if (! isset($_SESSION['vnt_csrf_token'])) {
      $_SESSION['vnt_csrf_token'] = md5(uniqid(rand(), TRUE)) ;
    }
    $data['csrf_token'] = $_SESSION['vnt_csrf_token'] ;

		
    /*assign the array to a template variable*/
    $this->skin->assign('data', $data);
    $this->skin->parse("manage");
    return $this->skin->text("manage");
  }
  // end class
}
?>