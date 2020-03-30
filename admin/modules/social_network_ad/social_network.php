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
	var $action = "social_network";

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
		
    
		switch ($vnT->input['sub'])
    {      
      case 'edit':
	   		 $nd['content']=$this->do_Edit($lang);
      break;
			default:
        $nd['f_title'] = $vnT->lang['social_network_setting'];
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
        $err =  $vnT->lang['err_csrf_token'] ;
      }else{

        $data = $_POST;
        $res_check = $DB->query("select * from social_network_setting where id=1 ");
        if($row = $DB->fetch_row($res_check))
        {
          foreach ($row as $key => $value)
          {
            if ($key != 'id') {
              $dup[$key] = $vnT->input[$key];
            }
          }
          $DB->do_update("social_network_setting", $dup, "id=1");

        }
        unset($_SESSION['vnt_csrf_token']);
        //xoa cache
        $func->clear_cache();
        //insert adminlog
        $func->insertlog("Setting", $_GET['act'], $id);
        $err = $vnT->lang["edit_success"];
      }

			$url = $this->linkUrl;
			$func->html_redirect($url, $err);
		}
  }





  /**
   * function do_Manage() 
   * Quan ly cac gioi thieu
   **/
  function do_Manage ($lang)
  {
    global $vnT, $func, $DB, $conf;
		
		$result = $DB->query("select * from social_network_setting where id=1 ");
		$data = $DB->fetch_row($result) ;
		$data['list_facebook']  = vnT_HTML::list_yesno("facebook",$data['facebook']);
		$data['list_google']  = vnT_HTML::list_yesno("google",$data['google']);
		
		$data['list_twitter']  = vnT_HTML::list_yesno("twitter",$data['twitter']);
    $data['link_check_facebook'] = $this->linkUrl."&sub=check_facebook"  ;

    $redirect_uri = urlencode("http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
    $data['redirect_uri'] = $redirect_uri;
    if (! isset($_SESSION['vnt_csrf_token'])) {
      $_SESSION['vnt_csrf_token'] = md5(uniqid(rand(), TRUE)) ;
    }
    $data['csrf_token'] = $_SESSION['vnt_csrf_token'] ;

    $data['link_action'] = $this->linkUrl."&sub=edit"  ;

    /*assign the array to a template variable*/
    $this->skin->assign('data', $data);
    $this->skin->parse("manage");
    return $this->skin->text("manage");
  }
  // end class
}
?>