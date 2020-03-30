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
 	var $action = "seo";
  /**
   * function vntModule ()
   * Khoi tao 
   **/
  function vntModule ()
  {
    global $Template, $vnT, $func, $DB, $conf;
    require_once ("function_config.php");
    $this->skin = new XiTemplate(DIR_MODULE . DS . $this->module . "_ad" . DS . "html" . DS . $this->action . ".tpl");
    $this->skin->assign('LANG', $vnT->lang);
    $this->skin->assign('DIR_IMAGE', $vnT->dir_images);
    $lang = ($vnT->input['lang']) ? $lang = $vnT->input['lang'] : $func->get_lang_default();
    $this->linkUrl = "?mod=" . $this->module . "&act=" . $this->action . "&lang=" . $lang;
		
    switch ($vnT->input['sub']) {
      case 'edit':
        $nd['content'] = $this->do_Edit($lang);
      break; 
      default:
        $nd['f_title'] = $vnT->lang['manage_seo'];
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
    if ($vnT->input['btnUpdate']) {
      if (empty($vnT->input['csrf_token']) || ($vnT->input['csrf_token'] != $_SESSION['vnt_csrf_token'])) {
        $mess = $vnT->lang['err_csrf_token'];
      } else {
        unset($_SESSION['vnt_csrf_token']);
        $arr_old = $func->fetchDbConfig();
        $cot['indextitle_' . $lang . ''] = $_POST['indextitle'];
        $cot['meta_keyword_' . $lang . ''] = $_POST['meta_keyword'];
        $cot['meta_description_' . $lang . ''] = $_POST['meta_description'];
        $cot['meta_extra'] = $_POST['meta_extra'];
        $func->writeDbConfig("config", $cot, $arr_old);

        $arr_friendly_title = $_POST['friendly_title'];
        $arr_metakey = $_POST['metakey'];
        $arr_metadesc = $_POST['metadesc'];

        foreach ($arr_friendly_title as $key => $value) {
          $dup['friendly_title'] = $value;
          $dup['metakey'] = $arr_metakey[$key];
          $dup['metadesc'] = $arr_metadesc[$key];
          $DB->do_update($key . "_setting", $dup, " lang='$lang'");
        }

        //xoa cache
        $func->clear_cache();
        $mess = $vnT->lang["edit_success"];
        $func->insertlog("Update", $_GET['act'], 1);

      }

      $url = $this->linkUrl;
      $func->html_redirect($url, $mess);
    }
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
		
    $conf_old = $func->fetchDbConfig();
		$data['indextitle'] = $conf_old['indextitle_'.$lang.''];
		$data['meta_keyword'] = $conf_old['meta_keyword_'.$lang.''];
		$data['meta_description'] = $conf_old['meta_description_'.$lang.''];
		$data['meta_extra'] = $conf_old['meta_extra'];
		
		
		
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
		 <tr>
      <td class="row1"  width="25%"  align="right">Friendly Title :</td>
      <td class="row0"><input name="friendly_title['.$row_mod['mod_name'].']"  type="text" size="70" maxlength="250" value="'.$row_s['friendly_title'].'" class="textfield" style="width:95%"></td>
    </tr>
    <tr>
      <td class="row1" align="right">Meta Keyword :</td>
      <td class="row0"><textarea name="metakey['.$row_mod['mod_name'].']" rows="3" cols="50" style="width:95%">'.$row_s['metakey'].'</textarea></td>
    </tr>
    <tr>
      <td class="row1" align="right">Meta Description : </td>
      <td class="row0"><textarea name="metadesc['.$row_mod['mod_name'].']" rows="4" cols="50"  class="textarea" style="width:95%">'.$row_s['metadesc'].'</textarea></td>
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