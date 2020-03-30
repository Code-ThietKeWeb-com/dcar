<?php
/*================================================================================*\
|| 							Name code : news.php	 		 																		  # ||
||  				Copyright © 2007 by Thai Son - CMS vnTRUST                					# ||
\*================================================================================*/
/**
 * @version : 1.0
 * @date upgrade : 11/12/2007 by Thai Son
 **/
if (! defined('IN_vnT'))
{
  die('Access denied');
}
$nts = new sMain();
class sMain
{
  var $output = "";
  var $skin = "";
  var $linkUrl = "";
  var $module = "search";
  var $action = "search";
  
	/**
   * function sMain ()
   * Khoi tao 
   **/
  function sMain ()
  {
    global $vnT, $input;
    include ("function_".$this->module.".php"); 
    $this->skin = new XiTemplate(DIR_MODULE . "/" . $this->module . "/html/" . $this->module . ".tpl");
    $this->skin->assign('DIR_MOD', DIR_MOD);
    $this->skin->assign('LANG', $vnT->lang);
    $this->skin->assign('INPUT', $input);
    $this->skin->assign('CONF', $vnT->conf);
    $this->skin->assign('DIR_IMAGE', $vnT->dir_images);
    $vnT->html->addStyleSheet(DIR_MOD . "/css/" . $this->module . ".css");
		
		$vnT->setting['menu_active'] = "main";    
		// extra title
    $vnT->conf['indextitle'] = $vnT->lang['search']['f_navation'] ;
    //SEO
		$link_seo = ($vnT->muti_lang) ? ROOT_URL.$vnT->lang_name."/" : ROOT_URL ;
		$link_seo .= $vnT->setting['seo_name'][$vnT->lang_name]['search'].".html";			
		$vnT->conf['meta_extra'] .= "\n".'<link rel="canonical" href="'.$link_seo.'" />';
		$vnT->conf['meta_extra'] .= "\n". '<link rel="alternate" media="handheld" href="'.$link_seo.'"/>';
		
    if ($input['keyword'])   {
      $content = $this->Result_Search($input["keyword"]);
    }
 		
 		$data['navation'] ='<ul><li><a href="/">Trang chủ</a></li><li>Kết quả tìm kiếm</li></ul>';
		$nd['content'] = $content;
    $nd['f_title'] = $vnT->lang['search']['f_search'];  
		$data['main'] = $vnT->skin_box->parse_box("box_middle", $nd);
		 
 		
		$this->skin->assign("data", $data);
    $this->skin->parse("modules");
   
    $vnT->output .= $this->skin->text("modules"); 
   
  }
 
	
	/**
   * function Result_Search ()
   * 
   **/
	function Result_Search ($keyword)
	{
		global $vnT, $input, $func,$DB,$conf;

		$text="";
		$p = ((int) $input['p']) ? (int) $input['p'] : 1; 
 		$arr_search = array();
		$stt=0;
		$qu_find = array('[#]' , '[/#]');
		$qu_replace = array('<font class="font_keyword">' , '</font>');
		$ext_pag = "&keyword=".$keyword;		 
		$key =  $vnT->func->get_keyword($keyword) ; 
		
		//about		
		$sql = "SELECT n.aid, n.date_post, nd.title, nd.short, nd.friendly_url FROM about n , about_desc nd  WHERE n.aid=nd.aid AND display=1 AND lang='$vnT->lang_name' AND  (title like '%".$key."%' or LOWER(title) like '%".$key."%' ) ORDER BY date_post DESC ";

		$result =  $vnT->DB->query($sql);
		if($num = $vnT->DB->num_rows($result))
		{			
			while($row = $vnT->DB->fetch_row($result))
			{
				$arr_search[$stt]['mod'] = $vnT->lang['search']['about'];
				$arr_search[$stt]['title'] = $vnT->func->HTML($row['title']);
				$arr_search[$stt]['short'] = $row['short'];
				$arr_search[$stt]['link'] = $vnT->link_root .$row['friendly_url'].".html";
				$arr_search[$stt]['date_post'] = $row['date_post'];
				$stt++;
			}			
		}
		
		//news 
		$sql = "SELECT n.newsid, n.date_post, nd.title, nd.short, nd.friendly_url  FROM news n , news_desc nd WHERE n.newsid=nd.newsid AND display=1 AND lang='$vnT->lang_name' AND  (title like '%".$key."%' or LOWER(title) like '%".$key."%' )  ORDER BY date_post DESC " ;
		$result =  $vnT->DB->query($sql);
		if($num = $vnT->DB->num_rows($result))
		{
			 
			while($row = $vnT->DB->fetch_row($result))
			{
				$arr_search[$stt]['mod'] = $vnT->lang['search']['news'];
				$arr_search[$stt]['title'] = $vnT->func->HTML($row['title']);
				$arr_search[$stt]['short'] = $row['short'];
				$arr_search[$stt]['link'] = $vnT->link_root.  $row['friendly_url'].".html";
				$arr_search[$stt]['date_post'] = $row['date_post'];
				$stt++;
			}			 
		}
		
		//product 
		$sql = "SELECT n.p_id, p_name, description, friendly_url, date_post  FROM products n , products_desc nd WHERE n.p_id=nd.p_id AND display=1 AND lang='$vnT->lang_name' AND  (p_name like '%".$key."%' or LOWER(p_name) like '%".$key."%' )  ORDER BY date_post DESC " ;
		//echo $sql ;
		$result =  $vnT->DB->query($sql);
		if($num = $vnT->DB->num_rows($result))
		{
			 
			while($row = $vnT->DB->fetch_row($result))
			{
				$arr_search[$stt]['mod'] = $vnT->lang['search']['product'];
				$arr_search[$stt]['title'] = $vnT->func->HTML($row['p_name']);				
				$arr_search[$stt]['short'] = $vnT->func->cut_string($vnT->func->check_html($row['description'],'ho_html'),100,1);
				//$arr_search[$stt]['link'] = $vnT->link_root.$row['friendly_url'].".html";

				$arr_search[$stt]['link'] = $vnT->link_root . $vnT->setting['seo_name'][$vnT->lang_name]["product"]."/".$row['p_id']."/".$row['friendly_url'].".html";

				$arr_search[$stt]['date_post'] = $row['date_post'];
				$stt++;
			}			 
		}
		
		//service 
		$sql = "SELECT n.service_id, n.date_post, nd.title, nd.short, nd.friendly_url  FROM service n, service_desc nd WHERE n.service_id=nd.service_id AND  display=1 AND lang='$vnT->lang_name' AND  (title like '%".$key."%' or LOWER(title) like '%".$key."%' )  ORDER BY date_post DESC " ;
		$result =  $vnT->DB->query($sql);
		if($num = $vnT->DB->num_rows($result))
		{
			 
			while($row = $vnT->DB->fetch_row($result))
			{
				$arr_search[$stt]['mod'] = $vnT->lang['search']['service'];
				$arr_search[$stt]['title'] = $vnT->func->HTML($row['title']);
				$arr_search[$stt]['short'] = $row['short'];
				$arr_search[$stt]['link'] =  $vnT->link_root.$row['service_id'].".html";
				$arr_search[$stt]['date_post'] = $row['date_post'];
				$stt++;
			}			 
		}
		
 		//xep theo date_post
		usort($arr_search, "cmp");
 		
		//echo "<pre>";
		//print_r($arr_search) ;
		//echo "</pre>";
		
		$totals = count($arr_search);
		$n =10 ;
		$num_pages = ceil($totals / $n);
    if ($p > $num_pages)    $p = $num_pages;
    if ($p < 1)     $p = 1;
    $start = ($p - 1) * $n;
		if($num_pages>1)
		{
			$root_link = LINK_MOD.".html";
			$nav = "<div class=\"pagination\">".$vnT->func->paginate_search($root_link,$totals,$n,$ext_pag,$p)."</div>" ;
		} 
 			
    if ($totals) 
		{ 
      for ($i = $start; ($i < ($start+$n) && $i<$totals) ; $i ++) 
			{
				$j=$i+1;
				$row = $arr_search[$i];				
        $link = $row['link']; 
				$title = $row['title']; 
				$short = $row['short'];  
				$date_post = @date("H:i, d/m/Y",$row['date_post']) ;
			 
 				$arr_key = array($key,strtolower($key),strtoupper($key));
				$arr_keyRe = array("[#]".$key."[/#]","[#]".strtolower($key)."[/#]","[#]".strtoupper($key)."[/#]");
				$title = str_replace($arr_key,$arr_keyRe,$title); 
				$title = str_replace($qu_find, $qu_replace, $title); 
	
 				$title = $row['mod'].' <a href="' . $link . '">' . $title . '</a> <span class="date_post">('.$date_post.')</span>'; 				
 				
				$last = ($j == ($start+$n) || $j==$totals ) ? " last" : "";
				$text .= '<div class="box-item'.$last.'">';
				$text .= $pic."<h3>{$title}</h3><div class='short'>{$short}</div>";
				$text .= '<br class="clear">';
				$text .= '</div>';
     
      }
       
    } else {
     	$text = '<div class="no_result">'.$vnT->lang['search']['no_have_result'].'</div>';
		}
 		
		$data['list_search']=$text;
		$data['nav']=$nav;
		$str_keyword.="<span class=\"font_keyword\">".$keyword."</span>" ; 
		$data['note_keyword'] = str_replace("{keyword}",$str_keyword,$vnT->lang['search']['note_key_search']);  
		
		$note_result = str_replace("{totals}","<b class=font_err>".$totals."</b>",$vnT->lang['search']['note_result']); 
		$data['note_result'] = str_replace("{num_pages}","<b>".$num_pages."</b>",$note_result);
		
		
    $this->skin->assign("data", $data);
    $this->skin->parse("html_list_search");		
    return $this->skin->text("html_list_search"); 
	}

  
// end class
}
?>