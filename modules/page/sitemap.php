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
  var $ROOT_URI = "sitemap";
  var $module = "page";
	var $action = "sitemap";

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
 		//navation
		$vnT->setting['menu_active'] = $this->action;
		 // extra title
    $vnT->conf['indextitle'] = $vnT->lang['page']['sitemap'] ;    
	  $link_seo = ($vnT->muti_lang) ? ROOT_URL.$vnT->lang_name."/" : ROOT_URL ;
		$link_seo .= "sitemap.html";			
		$vnT->conf['meta_extra'] .= "\n".'<link rel="canonical" href="'.$link_seo.'" />';
		$vnT->conf['meta_extra'] .= "\n". '<link rel="alternate" media="handheld" href="'.$link_seo.'"/>';
			
		$data['main'] = $this->do_SiteMap();
		$data['box_sidebar'] = box_sidebar();  
    $this->skin->assign("data", $data);
    $this->skin->parse("modules"); 		
		$vnT->output .= $this->skin->text("modules");
  }
	
	/**
   * function do_SiteMap ()
   *  
   **/
  function do_SiteMap ()
  {
    global $vnT, $input;
    
		$content="";
		
	 
				
		$text ='<div class="box_sitemap"><table width="100%" border="0" cellspacing="0" cellpadding="0">
		<tr><td><h2><a href="'.ROOT_URL.'">'.$vnT->lang['page']['home'].'</a></h2></td> </tr>
		<tr><td><ul>';	
		$query = $vnT->DB->query("select * from menu where parentid=0 and display=1 and pos='horizontal' and lang='$vnT->lang_name' order by menu_order ASC ,menu_id DESC");    
		if ($num = $vnT->DB->num_rows($query)) 
		{ 
			$i = 0;       
			while ($row = $vnT->DB->fetch_row($query)) {
				$i ++;
				$menu_link = $vnT->func->HTML($row['menu_link']) ;			 	
			  $link = (! strstr($menu_link, "http://")) ? $vnT->link_root . $menu_link : $menu_link;				
        $title = $vnT->func->HTML($row['title']); 
				
				$text .= "<li  ><a href=\"{$link}\" >{$title}</a></li>";		
			}					
		} 
		
		$text .= "</ul></td></tr></table></div>";
		$content .= $text; 
		
 		
		$text ='<div class="box_sitemap"><table width="100%" border="0" cellspacing="0" cellpadding="0">
		<tr><td><h2><a href="'.ROOT_URL.'news.html">'.$vnT->lang['page']['about'].'</a></h2></td> </tr>
		<tr><td><ul>';	
		 
		$query = $vnT->DB->query("SELECT *
															 FROM about  
															 WHERE lang='{$vnT->lang_name}'  
															 AND parentid=0
															 order by display_order ASC , aid DESC");    
		if ($num = $vnT->DB->num_rows($query)) 
		{ 
			$i = 0;       
			while ($row = $vnT->DB->fetch_row($query)) {
				$i ++; 
				$link = $vnT->link_root."about/".$row['friendly_url'].".html" ;			 	 				
				$title = $vnT->func->HTML($row['title']); 						 		 
				
				$text .= "<li  ><a href=\"{$link}\" >{$title}</a></li>";		
			}					
		} 
		$text .= "</ul></td></tr></table></div>";
		$content .= $text; 
  		
		
		$data['content'] = $content;
		
    $this->skin->assign("data", $data);
    $this->skin->parse("html_sitemap"); 
 		$nd['content'] =  $this->skin->text("html_sitemap");
		$nd['f_title'] = $vnT->lang['page']['sitemap']; 		
		return $vnT->skin_box->parse_box("box_middle", $nd);
		
  }
  // end class
}
?>