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
			$xml_write->add_tag('loc',$rootSite."/cong-ty-thiet-ke-website-trustvn.html");
			$xml_write->add_tag('lastmod',date("c"));
			$xml_write->add_tag('changefreq',"monthly");
			$xml_write->add_tag('priority','1');
			$xml_write->close_group();
			$xml_write->doc.=	"\r\n";
			//
			//item
			$xml_write->add_group('url');
			$xml_write->add_tag('loc',$rootSite."/thiet-ke-web.html");
			$xml_write->add_tag('lastmod',date("c"));
			$xml_write->add_tag('changefreq',"monthly");
			$xml_write->add_tag('priority','1');
			$xml_write->close_group();
			$xml_write->doc.=	"\r\n";
			//
			//item
			$xml_write->add_group('url');
			$xml_write->add_tag('loc',$rootSite."/quang-cao-web.html");
			$xml_write->add_tag('lastmod',date("c"));
			$xml_write->add_tag('changefreq',"monthly");
			$xml_write->add_tag('priority','1');
			$xml_write->close_group();
			$xml_write->doc.=	"\r\n";
			//
			//item
			$xml_write->add_group('url');
			$xml_write->add_tag('loc',$rootSite."/dich-vu-web.html");
			$xml_write->add_tag('lastmod',date("c"));
			$xml_write->add_tag('changefreq',"monthly");
			$xml_write->add_tag('priority','1');
			$xml_write->close_group();
			$xml_write->doc.=	"\r\n";
			//
			
			//menu
		 $result = $DB->query("SELECT * FROM menu WHERE display=1 AND pos<>'footer' AND parentid=0 ORDER BY menu_order ASC ,menu_id ASC   ");
			if($num = $DB->num_rows($result))
			{
				while($row = $DB->fetch_row($result))	
				{
					if($row['menu_link']!="#" && $row['menu_link']!="")
					{
						 
						$link = (! strstr($row['menu_link'], "http://")) ? $rootSite."/".$row['menu_link'] : $row['menu_link'] ;
 						
						//item
						$xml_write->add_group('url');
						$xml_write->add_tag('loc',$link);
						$xml_write->add_tag('lastmod',date("c"));
						$xml_write->add_tag('changefreq',"monthly");
						$xml_write->add_tag('priority','1');
						$xml_write->close_group();
						$xml_write->doc.=	"\r\n";
						//			
					}
					 
					//check sub
					 $res_sub = $DB->query("SELECT * FROM menu WHERE display=1 AND parentid=".$row['menu_id']." ORDER BY menu_order ASC ,menu_id ASC  ");
					if($num_sub = $DB->num_rows($res_sub))
					{
 						while($row_sub = $DB->fetch_row($res_sub))	
						{
							if($row_sub['menu_link']!="#" && $row_sub['menu_link']!="")
							{
								$link_sub = (! strstr($row_sub['menu_link'], "http://")) ? $rootSite."/".$row_sub['menu_link'] : $row_sub['menu_link'] ;
								//item
								$xml_write->add_group('url');
								$xml_write->add_tag('loc',$link_sub);
								$xml_write->add_tag('lastmod',date("c"));
								$xml_write->add_tag('changefreq',"monthly");
								$xml_write->add_tag('priority','0.50');
								$xml_write->close_group();
								$xml_write->doc.=	"\r\n";
								//			
							}							
						}
	 				}  			
					        
				}
			}
			//end menu
			
			
			//product			 
			$arr_pro =  box_product_category();
			foreach ($arr_pro as $link_pro)
			{
				//item
				$xml_write->add_group('url');
				$xml_write->add_tag('loc',$rootSite."/".$link_pro);
				$xml_write->add_tag('lastmod',date("c"));
				$xml_write->add_tag('changefreq',"monthly");
				$xml_write->add_tag('priority','0.50');
				$xml_write->close_group();
				$xml_write->doc.=	"\r\n";
				//
			}			
			//end news
			
			//news
			$xml_write->add_group('url');
			$xml_write->add_tag('loc',$rootSite."/tu-van-thiet-ke-web.html");
			$xml_write->add_tag('lastmod',date("c"));
			$xml_write->add_tag('changefreq',"monthly");
			$xml_write->add_tag('priority','1');
			$xml_write->close_group();
			$xml_write->doc.=	"\r\n";
			$arr_news =  box_news_category();
			foreach ($arr_news as $link_news)
			{
				//item
				$xml_write->add_group('url');
				$xml_write->add_tag('loc',$rootSite."/".$link_news);
				$xml_write->add_tag('lastmod',date("c"));
				$xml_write->add_tag('changefreq',"monthly");
				$xml_write->add_tag('priority','0.50');
				$xml_write->close_group();
				$xml_write->doc.=	"\r\n";
				//
			}			
			//end news
			
			//gallery
			$xml_write->add_group('url');
			$xml_write->add_tag('loc',$rootSite."/thiet-ke-web-dep.html");
			$xml_write->add_tag('lastmod',date("c"));
			$xml_write->add_tag('changefreq',"monthly");
			$xml_write->add_tag('priority','1');
			$xml_write->close_group();
			$xml_write->doc.=	"\r\n";
			$arr_gallery =  box_gallery_category();
			foreach ($arr_gallery as $link_g)
			{
				//item
				$xml_write->add_group('url');
				$xml_write->add_tag('loc',$rootSite."/".$link_g);
				$xml_write->add_tag('lastmod',date("c"));
				$xml_write->add_tag('changefreq',"monthly");
				$xml_write->add_tag('priority','0.50');
				$xml_write->close_group();
				$xml_write->doc.=	"\r\n";
				//
			}			
			//end gallery
			
			//about
			$xml_write->add_group('url');
			$xml_write->add_tag('loc',$rootSite."/thiet-ke-web-vntrust.html");
			$xml_write->add_tag('lastmod',date("c"));
			$xml_write->add_tag('changefreq',"monthly");
			$xml_write->add_tag('priority','1');
			$xml_write->close_group();
			$xml_write->doc.=	"\r\n";
			$arr_about=  box_about_category();
			foreach ($arr_about as $link_a)
			{
				//item
				$xml_write->add_group('url');
				$xml_write->add_tag('loc',$rootSite."/".$link_a);
				$xml_write->add_tag('lastmod',date("c"));
				$xml_write->add_tag('changefreq',"monthly");
				$xml_write->add_tag('priority','0.50');
				$xml_write->close_group();
				$xml_write->doc.=	"\r\n";
				//
			}			
			//end about
			
			//download
			$xml_write->add_group('url');
			$xml_write->add_tag('loc',$rootSite."/download-thiet-ke-web.html");
			$xml_write->add_tag('lastmod',date("c"));
			$xml_write->add_tag('changefreq',"monthly");
			$xml_write->add_tag('priority','1');
			$xml_write->close_group();
			$xml_write->doc.=	"\r\n";
			$arr_dowload=  box_download_category();
			foreach ($arr_dowload as $link_d)
			{
				//item
				$xml_write->add_group('url');
				$xml_write->add_tag('loc',$rootSite."/".$link_d);
				$xml_write->add_tag('lastmod',date("c"));
				$xml_write->add_tag('changefreq',"monthly");
				$xml_write->add_tag('priority','0.50');
				$xml_write->close_group();
				$xml_write->doc.=	"\r\n";
				//
			}			
			//end download
			
			//video
			$xml_write->add_group('url');
			$xml_write->add_tag('loc',$rootSite."/video-thiet-ke-web.html");
			$xml_write->add_tag('lastmod',date("c"));
			$xml_write->add_tag('changefreq',"monthly");
			$xml_write->add_tag('priority','1');
			$xml_write->close_group();
			$xml_write->doc.=	"\r\n";
			$arr_video=  box_video_category();
			foreach ($arr_video as $link_v)
			{
				//item
				$xml_write->add_group('url');
				$xml_write->add_tag('loc',$rootSite."/".$link_v);
				$xml_write->add_tag('lastmod',date("c"));
				$xml_write->add_tag('changefreq',"monthly");
				$xml_write->add_tag('priority','0.50');
				$xml_write->close_group();
				$xml_write->doc.=	"\r\n";
				//
			}			
			//end video
			
			//faqs
			$xml_write->add_group('url');
			$xml_write->add_tag('loc',$rootSite."/hoi-dap-thiet-ke-web.html");
			$xml_write->add_tag('lastmod',date("c"));
			$xml_write->add_tag('changefreq',"monthly");
			$xml_write->add_tag('priority','1');
			$xml_write->close_group();
			$xml_write->doc.=	"\r\n";
			$arr_faqs=  box_faqs_category();
			foreach ($arr_faqs as $link_f)
			{
				//item
				$xml_write->add_group('url');
				$xml_write->add_tag('loc',$rootSite."/".$link_f);
				$xml_write->add_tag('lastmod',date("c"));
				$xml_write->add_tag('changefreq',"monthly");
				$xml_write->add_tag('priority','0.50');
				$xml_write->close_group();
				$xml_write->doc.=	"\r\n";
				//
			}			
			//end faqs 
			
			
			//Khách hàng
			$xml_write->add_group('url');
			$xml_write->add_tag('loc',$rootSite."/khach-hang-thiet-ke-web.html");
			$xml_write->add_tag('lastmod',date("c"));
			$xml_write->add_tag('changefreq',"monthly");
			$xml_write->add_tag('priority','1');
			$xml_write->close_group();
			$xml_write->doc.=	"\r\n";
			//
			
			//Estore
			$xml_write->add_group('url');
			$xml_write->add_tag('loc',$rootSite."/all.html");
			$xml_write->add_tag('lastmod',date("c"));
			$xml_write->add_tag('changefreq',"monthly");
			$xml_write->add_tag('priority','1');
			$xml_write->close_group();
			$xml_write->doc.=	"\r\n";
			//
			
			//Contact
			$xml_write->add_group('url');
			$xml_write->add_tag('loc',$rootSite."/lien-he-thiet-ke-web.html");
			$xml_write->add_tag('lastmod',date("c"));
			$xml_write->add_tag('changefreq',"monthly");
			$xml_write->add_tag('priority','1');
			$xml_write->close_group();
			$xml_write->doc.=	"\r\n";
			
			$xml_write->add_group('url');
			$xml_write->add_tag('loc',$rootSite."/dat-hang-thiet-ke-web.html");
			$xml_write->add_tag('lastmod',date("c"));
			$xml_write->add_tag('changefreq',"monthly");
			$xml_write->add_tag('priority','1');
			$xml_write->close_group();
			$xml_write->doc.=	"\r\n";
			//
			
			
			$content_xml = '<?xml version="1.0" encoding="UTF-8"?>
<?xml-stylesheet type="text/xsl" href="'.$conf['rooturl'].'/gss.xsl"?>
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