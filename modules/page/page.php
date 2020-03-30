<?php
/*================================================================================*\
|| 							Name code : main.php 		 		 																	  # ||
||  				Copyright © 2007 by Thai Son - CMS vnTRUST                					# ||
\*================================================================================*/
/**
 * @version : 1.0
 * @date upgrade : 11/12/2007 by Thai Son
 **/
if (! defined('IN_vnT')) {
  die('Access denied');
}
$nts = new sMain();

class sMain
{
  var $output = "";
  var $skin = "";
  var $linkUrl = "";
  var $module = "page";

  /**
   * function sMain ()
   * Khoi tao 
   **/
  function sMain ()
  {
    global $vnT, $input;
    include ("function_" . $this->module . ".php");
    $this->skin = new XiTemplate(DIR_MODULE . "/" . $this->module . "/html/" . $this->module . ".tpl");
    $this->skin->assign('DIR_MOD', DIR_MOD);
    $this->skin->assign('LANG', $vnT->lang);
    $this->skin->assign('INPUT', $input);
    $this->skin->assign('CONF', $vnT->conf);
    $this->skin->assign('DIR_IMAGE', $vnT->dir_images);
    $this->linkMod = $vnT->cmd . "=mod:" . $this->module;
    $vnT->html->addStyleSheet(DIR_MOD . "/css/" . $this->module . ".css");
		
    $id = (int)$input['itemID']; 
    $result = $vnT->DB->query("select * from pages where id=".$id." and lang='{$vnT->lang_name}' AND display=1 ");
		 
    if ($data = $vnT->DB->fetch_row($result)) 
		{     
			$vnT->setting['menu_active'] =  $data['friendly_url'];
			//SEO
			if ($data['metadesc'])	$vnT->conf['meta_description'] = $data['metadesc'];
			if ($data['metakey'])	$vnT->conf['meta_keyword'] = $data['metakey'];
			$vnT->conf['indextitle'] =  $data['friendly_title'] ;
			//SEO
			$link_seo = ($vnT->muti_lang) ? ROOT_URL.$vnT->lang_name."/" : ROOT_URL ;
			$link_seo .= $data['friendly_url'].".html";			
			$vnT->conf['meta_extra'] .= "\n".'<link rel="canonical" href="'.$link_seo.'" />';
			$vnT->conf['meta_extra'] .= "\n". '<link rel="alternate" media="handheld" href="'.$link_seo.'"/>';
		
			//set link_lang			
			if($vnT->muti_lang>0){ 
				$res_lang = $vnT->DB->query("SELECT friendly_url,lang FROM pages WHERE id_lang=".$data['id_lang']." AND display=1 ")	;
				while ($row_lang = $vnT->DB->fetch_row($res_lang)){
					$vnT->link_lang[$row_lang['lang']] = ROOT_URI.$row_lang['lang']."/".$row_lang['friendly_url'].".html";
				}
			}   
			
      $nd['f_title'] = $vnT->func->HTML($data['title']);
      $nd['content'] = '<div class="desc">'.$data['content'].'</div>'; 
			 
    } else {
      $mess = $vnT->lang['page']['page_not_found'];
      $url = $vnT->link_root . "main.html";
      $vnT->func->html_redirect($url, $mess);
    }
     
		if($data['is_popup']) 
		{
			
			$this->skin->assign("data", $data);
			$this->skin->parse("html_popup");
			$textout = $this->skin->text("html_popup");
			
    	flush();
			echo $textout ;
			exit();
		}else{
			$data['box_sidebar'] = box_sidebar(); 
  			
			$data['main'] = $vnT->skin_box->parse_box("box_middle", $nd);
			$this->skin->assign("data", $data);
			$this->skin->parse("modules");
			$vnT->output .= $this->skin->text("modules");
			 
		}
  }
  // end class
}
?>