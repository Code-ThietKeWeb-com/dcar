<?php
/*================================================================================*\
|| 							Name code : about.php 		 		 																	  # ||
||  				Copyright © 2007 by Thai Son - CMS vnTRUST                					# ||
\*================================================================================*/
/**
 * @version : 1.0
 * @date upgrade : 11/12/2007 by Thai Son
 **/
if (! defined('IN_vnT')) {
  die('Access denied');
}
//load Model
include_once dirname( __FILE__ ) . '/includes/Model.php';

class Controller extends Model
{

  var $skin = "";
  var $linkUrl = "";
  var $module = MOD_NAME ;
  var $action = MOD_NAME ;

  /**
   *
   * Khoi tao
   **/
  public function __construct()
  {
    global $vnT, $input;

    //load skin
    $this->loadSkinModule($this->module);
    $vnT->html->addStyleSheet(DIR_MOD . "/css/" . $this->module . ".css");
    $vnT->html->addScript(DIR_MOD . "/js/" . $this->module . ".js");

    // menu_active
		$vnT->setting['menu_active'] = $this->module;
		
		$itemID = (int) $input['itemID'];	
	 	if ($itemID) {	
			if($_GET['preview']==1 && isset($_SESSION['admin_session']))	{
				$where = " AND  n.aid=".$itemID ;
			}else{ 
				$where = " AND display=1 AND n.aid=".$itemID;			
			} 
	  }else{
			$where = " AND  display=1  order by display_order ASC , date_post ASC limit 0,1 ";
		}
		
    //check
		$result = $vnT->DB->query("SELECT * FROM about n,about_desc nd  WHERE n.aid=nd.aid AND nd.lang='$vnT->lang_name' $where ");
		if($row = $vnT->DB->fetch_row($result))
		{
			//SEO
			if ($row['metadesc'])	$vnT->conf['meta_description'] = $row['metadesc'];
			if ($row['metakey'])	$vnT->conf['meta_keyword'] = $row['metakey'];
			$vnT->conf['indextitle'] =  $row['friendly_title'] ;

			$link_seo = ($vnT->muti_lang) ? ROOT_URL.$vnT->lang_name."/" : ROOT_URL ;
			$link_seo .= $row['friendly_url'].".html";
			if($input['itemID']) {			
				$vnT->conf['meta_extra'] .= "\n".'<link rel="canonical" href="'.$link_seo.'" />';
				$vnT->conf['meta_extra'] .= "\n". '<link rel="alternate" media="handheld" href="'.$link_seo.'"/>';
			}

      $meta_info = array();
      $meta_info['type'] = "website";
      $meta_info['url'] = $link_seo;
      if ($row['picture']) {
        $meta_info['image']  =   ROOT_URL."vnt_upload/".$this->module."/".$row['picture'];
      }
      $vnT->conf['meta_extra'] .= $vnT->lib->build_meta_header($meta_info);


			//set link_lang			
			if($vnT->muti_lang>0){
				$res_lang = $vnT->DB->query("SELECT aid,friendly_url,lang FROM about_desc WHERE aid=".$row['aid']." AND display=1 ")	;
				while ($row_lang = $vnT->DB->fetch_row($res_lang))
				{
					$vnT->link_lang[$row_lang['lang']] = ROOT_URI.$row_lang['lang']."/".$row_lang['friendly_url'].".html";
				}
			} 
			
			$input['aID'] = $row['aid'];
			$data['banner'] = $vnT->lib->getBanners("top",1 );
			$data['main'] = $this->do_Detail($row);

      $data['nav_category'] = $this->get_nav_category();
      $data['box_sidebar'] = $this->box_sidebar();

			$arr_navation[] = array("link" => "","title" => $vnT->func->HTML($row['title']) );
			$data['navation'] =  $vnT->lib->get_navation($arr_navation);

		}else{
			$vnT->func->header_redirect($vnT->link_root) ;
		}


    $this->skin->assign("data", $data);
    $this->skin->parse("modules");
    $vnT->output .= $this->skin->text("modules");
  }

  //---------
  function do_Detail ($info)
  {
    global $vnT, $input ;

		$data['content'] = $info['content'];

		$this->skin->assign("data", $data);
		$this->skin->parse("html_about");
		$nd['content'] = $this->skin->text("html_about");
		$nd['f_title'] =  $vnT->func->HTML($info['title']);
    return $vnT->skin_box->parse_box("box_middle", $nd);
  }
  // end class
}

$controller = new Controller();
?>