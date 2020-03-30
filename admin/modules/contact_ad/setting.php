<?php
/*================================================================================*\
|| 							Name code : tour.php 		 		            	  ||
||  				Copyright @2008 by Thai Son - CMS vnTRUST                     ||
\*================================================================================*/
/**
 * @version : 2.0
 * @date upgrade : 09/01/2009 by Thai Son
 **/
if (! defined('IN_vnT'))
{
  die('Hacking attempt!');
}

//load Model
include_once dirname( __FILE__ ) . '/includes/Model.php';

class vntModule extends Model
{
  var $output = "";
  var $skin = "";
  var $linkUrl = "";
  var $module = MOD_NAME;
  var $action = "setting";

  /**
   * function vntModule ()
   * Khoi tao
   **/
  function vntModule ()
  {
    global $Template, $vnT, $func, $DB, $conf;
    //load skin
    $this->loadSkinModule($this->action);
    $lang = ($vnT->input['lang']) ? $lang = $vnT->input['lang'] : $func->get_lang_default();
    $this->linkUrl = "?mod=" . $this->module . "&act=" . $this->action . "&lang=" . $lang;

    $vnT->html->addScript($vnT->dir_js . "/eModal/eModal.min.js");
    $vnT->html->addStyleSheet( $vnT->dir_js."/jquery-ui/jquery-ui.min.css");
    $vnT->html->addScript($vnT->dir_js . "/jquery-ui/jquery-ui.min.js");
    $vnT->html->addStyleSheet("modules/" . $this->module . "_ad/css/" . $this->module . ".css");
    $vnT->html->addScript("modules/" . $this->module . "_ad" . "/js/" . $this->module . ".js");
		
    switch ($vnT->input['sub'])
    {      
			case 'rebuild':
	   		 $nd['content']=$this->do_Rebuild($lang);
      break;
      case 'edit':
	   		 $nd['content']=$this->do_Edit($lang);
      break;
			default:
        $nd['f_title'] = $vnT->lang['manage_setting'];
        $nd['content'] = $this->do_Manage($lang);
        break;
    }
    $nd['menu'] = $func->getToolbar_Small($this->module,$this->action, $lang);
		$nd['row_lang'] = $func->html_lang("?mod=" . $this->module . "&act=" . $this->action, $lang);
    $Template->assign("data", $nd);
    $Template->parse("box_main");
    $vnT->output .= $Template->text("box_main");
  
  }

  /**
   * function do_Rebuild
   **/
  function do_Rebuild ($lang)
  {
    global $vnT, $func, $DB, $conf;


    //service_desc
    $result = $DB->query("SELECT * FROM about_desc ");
    while($row = $DB->fetch_row($result))
    {

      $arr_seo[$stt]['table'] = "about_desc";
      $arr_seo[$stt]['table_id'] = "aid";

      $arr_seo[$stt]['modules'] = "about";
      $arr_seo[$stt]['action'] = "about";
      $arr_seo[$stt]['name_id'] = "itemID";

      $arr_seo[$stt]['item_id'] = $row['aid'];
      $arr_seo[$stt]['lang'] = $row['lang'];
      $arr_seo[$stt]['friendly_url'] = $row['friendly_url'];
      $arr_seo[$stt]['query_string'] = "mod:about|act:about|itemID:".$row['aid'];
      $stt++;

    }

    foreach ($arr_seo as $seo)
    {
      $friendly_url = $seo['friendly_url'] ;

      //check
      $res = $DB->query("SELECT id,name FROM seo_url WHERE modules='".$seo['modules']."' AND action='".$seo['action']."' AND name_id='".$seo['name_id']."' AND item_id=".$seo['item_id']." AND lang='".$seo['lang']."'");
      if($row = $DB->fetch_row($res))
      {// update

        if($friendly_url != $row['name'])
        {
          $res_ck = $DB->query("SELECT id FROM seo_url WHERE name='".$seo['friendly_url']."' AND lang='".$seo['lang']."'  AND id<>".$row['id'] )	 ;
          if($row_ck = $DB->fetch_row($res_ck))
          {
            $friendly_url = $seo['friendly_url']."-".time();
            $DB->query("UPDATE ".$seo['table']." SET friendly_url='".$friendly_url."' WHERE  lang='".$seo['lang']."' AND ".$seo['table_id']."=".$seo['item_id']." ")	;
          }

          $cot['name'] = $friendly_url;
          $cot['date_post'] = time();
          $DB->do_update("seo_url", $cot,"modules='".$seo['modules']."' AND action='".$seo['action']."' AND name_id='".$seo['name_id']."' AND item_id=".$seo['item_id']." AND lang='".$seo['lang']."'");
        }
      }else{//insert

        $res_ck = $DB->query("SELECT * FROM seo_url WHERE name='".$seo['friendly_url']."' AND lang='".$seo['lang']."' " )	 ;
        if($row_ck = $DB->fetch_row($res_ck))
        {
          $friendly_url = $seo['friendly_url']."-".time();
          $DB->query("UPDATE ".$seo['table']." SET friendly_url='".$friendly_url."' WHERE  lang='".$seo['lang']."' AND ".$seo['table_id']."=".$seo['item_id']." ")	;
        }

        $cot['modules'] = $seo['modules'];
        $cot['action'] = $seo['action'];
        $cot['name_id'] = $seo['name_id'];
        $cot['item_id'] = $seo['item_id'];
        $cot['lang'] = $seo['lang'];
        $cot['name'] = $friendly_url;
        $cot['query_string'] = $seo['query_string'];
        $cot['date_post'] = time();

        $DB->do_insert("seo_url", $cot);
      }
    }


    //xoa cache
    $func->clear_cache();
    //insert adminlog
    $func->insertlog("Rebuild", $_GET['act'], $id);
    $err = "Rebuild Link Success";
    $url = $this->linkUrl;
    $func->html_redirect($url, $err);
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
        $err =  $vnT->lang['err_csrf_token'];
      }else{
        $data = $_POST;
        $res_check = $DB->query("select * from about_setting where id=1 ");
        if($row = $DB->fetch_row($res_check))
        {
          foreach ($row as $key => $value)
          {
            if ($key != 'id' && isset($vnT->input[$key]) ) {
               $dup[$key] = $vnT->input[$key];
            } 

          }

          $res_lang = $DB->query("SELECT id FROM about_setting WHERE lang='$lang'");
          if($DB->num_rows($res_lang))
          {
            $ok = $DB->do_update("about_setting", $dup, "lang='$lang'");
          }else{
            $dup['lang'] = $lang ;
            $ok = $DB->do_insert("about_setting", $dup );
          }
        }

        //xoa cache
        $func->clear_cache();
        //insert adminlog
        $func->insertlog("Setting", $_GET['act'], 1);
        $err = $vnT->lang["edit_setting_success"];

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

    $result = $DB->query("select * from about_setting where lang='$lang' ");
    $data = $DB->fetch_row($result) ; 

    if ($data['picture']) {
      $data['pic'] = '<img src="'.MOD_DIR_UPLOAD.'/'.$data['picture'].'" width="150" />' . "  <a href=\"javascript:del_picture('picture')\" class=\"del\">Xóa</a>";
      $data['style_upload'] = "style='display:none' ";
    } else {
      $data['pic'] = "";
    }
 

    if (! isset($_SESSION['vnt_csrf_token'])) {
      $_SESSION['vnt_csrf_token'] = md5(uniqid(rand(), TRUE)) ;
    }
    $data['csrf_token'] = $_SESSION['vnt_csrf_token'] ;


    $data['module'] = $this->module;
    $data['link_action'] = $this->linkUrl."&sub=edit";
    $data['link_rebuild'] = $this->linkUrl."&sub=rebuild";
    /*assign the array to a template variable*/
    $this->skin->assign('data', $data);

    $this->skin->parse("manage");
    return $this->skin->text("manage");
  }


  // end class
}
$vntModule = new vntModule();
?>
