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

		//SEO
    $link_seo =  ROOT_URL ;
    if( $vnT->muti_lang && ($vnT->lang_name!= $vnT->lang_default)){
      $link_seo =  ROOT_URL.$vnT->lang_name."/"  ;
    }
		$vnT->conf['meta_extra'] .= "\n".'<link rel="canonical" href="'.$link_seo.'" />';
		$vnT->conf['meta_extra'] .= "\n". '<link rel="alternate" media="handheld" href="'.$link_seo.'"/>';
    $meta_info = array();
    if($vnT->setting['src_logo']){
      $meta_info['image']  = $vnT->setting['src_logo'];
    }
    $vnT->conf['meta_extra'] .= $vnT->lib->build_meta_header($meta_info);
    //END SEO

		$data['main'] = $this->do_Main();
    $data['box_sidebar'] = $this->box_sidebar();

    $this->skin->assign("data", $data);
    $this->skin->parse("modules");
    $vnT->output .= $this->skin->text("modules");
  }


  /**
   * function do_Main ()
   *
   **/
  function do_Main ()
  {
    global $vnT, $input;


    $nd['f_title'] = $vnT->lang['main']['homepage'];
    $nd['content'] =  $vnT->lang['main']['underconstruction'];

    $textout = $vnT->skin_box->parse_box("box_middle", $nd);
    return $textout;
  }

  // end class
}
$controller = new Controller();
?>