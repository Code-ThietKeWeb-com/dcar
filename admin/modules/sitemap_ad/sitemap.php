<?php
/*================================================================================*\
|| 							Name code : sitemap.php 		 		            	  ||
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
  var $module = "sitemap";

  /**
   * function vntModule ()
   * Khoi tao 
   **/
  function vntModule ()
  {
    global $Template, $vnT, $func, $DB, $conf;
    require_once('../includes/class_xml.php');
		require_once ("function_sitemap.php");
    $this->skin = new XiTemplate(DIR_MODULE . DS . "sitemap_ad" . DS . "html" . DS . "sitemap.tpl");
    $this->skin->assign('LANG', $vnT->lang);
    $lang = ($vnT->input['lang']) ? $lang = $vnT->input['lang'] : $func->get_lang_default();
    $this->linkUrl = "?mod=sitemap&act=sitemap&lang=" . $lang;
		
		
    switch ($vnT->input['sub']) {
      case 'edit':
        $nd['content'] = $this->do_Edit($lang);
      break;
      default:
        $nd['f_title'] = $vnT->lang['manage_sitemap'];
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
    if ($vnT->input['do_submit']) 
		{
			$arr_item = array();
			
    	$rootSite = substr($conf['rooturl'],0,-1);
 			$xml_write = new XMLexporter();
						
			//item
			$xml_write->add_group('url');
			$xml_write->add_tag('loc',$rootSite);
			$xml_write->add_tag('lastmod',date("c"));
			$xml_write->add_tag('changefreq',"monthly");
			$xml_write->add_tag('priority','1');
			$xml_write->close_group();
			$xml_write->doc.=	"\r\n";
			//
			
			//item
			$xml_write->add_group('url');
			$xml_write->add_tag('loc',$rootSite."/trang-chu.html");
			$xml_write->add_tag('lastmod',date("c"));
			$xml_write->add_tag('changefreq',"monthly");
			$xml_write->add_tag('priority','1');
			$xml_write->close_group();
			$xml_write->doc.=	"\r\n";
			//
			 
			//item
			$xml_write->add_group('url');
			$xml_write->add_tag('loc',$rootSite."/gioi-thieu.html");
			$xml_write->add_tag('lastmod',date("c"));
			$xml_write->add_tag('changefreq',"monthly");
			$xml_write->add_tag('priority','1');
			$xml_write->close_group();
			$xml_write->doc.=	"\r\n";
			//
			//item
			$xml_write->add_group('url');
			$xml_write->add_tag('loc',$rootSite."/lien-he.html");
			$xml_write->add_tag('lastmod',date("c"));
			$xml_write->add_tag('changefreq',"monthly");
			$xml_write->add_tag('priority','1');
			$xml_write->close_group();
			$xml_write->doc.=	"\r\n";
			//
			
			
			//menu
			$arr_menu = box_menu($lang);	 
 			
			//about 
			$arr_about=  box_about($lang);

      //service
      $arr_service=  box_service($lang);

      //product
			$arr_product =  box_product($lang);		 
			
 			//news 
			$arr_news=  box_news($lang);

      //guide
      $arr_guide=  box_guide($lang);


      $arr_item =  array_merge($arr_item,$arr_menu,$arr_about,$arr_service,$arr_product,$arr_news,$arr_guide);
			$arr_item = array_unique($arr_item)	;
			foreach ($arr_item as $link_item)
			{				 
				$link_item = (! strstr($link_item, "http://")) ? $rootSite."/".$link_item : $link_item ;
				//item
				$xml_write->add_group('url');
				$xml_write->add_tag('loc',$link_item);
				$xml_write->add_tag('lastmod',date("c"));
				$xml_write->add_tag('changefreq',"monthly");
				$xml_write->add_tag('priority','0.50');
				$xml_write->close_group();
				$xml_write->doc.=	"\r\n";
				//
			}	
			
 			
			$content_xml = '<?xml version="1.0" encoding="UTF-8"?>
<?xml-stylesheet type="text/xsl" href="gss.xsl"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.google.com/schemas/sitemap/0.84 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">';
			$content_xml .= "\r\n\r\n";
			$content_xml .= $xml_write->output();
			$content_xml .= "\r\n</urlset>";			
			$xml_write = null;
			//echo "content_xml = ".$content_xml;
		 
			$path = "../sitemap.xml";
			if($handle = @fopen($path, "w")){
				fwrite($handle, $content_xml, strlen($content_xml));
				fclose($handle);
				$mess =   "Cập nhật thành công"; 						
			}else{
				$mess =  $func->html_err("Khong mo duoc file sitemap.xml ");
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
		
	 	$data['link_sitemap'] = "<a href='".$conf['rooturl']."sitemap.xml' target='_blank' >".$conf['rooturl']."sitemap.xml</a>";
	 
    $data['link_action'] = $this->linkUrl . "&sub=edit";
    /*assign the array to a template variable*/
    $this->skin->assign('data', $data);
    $this->skin->parse("manage");
    return $this->skin->text("manage");
  }
  // end class
}
?>