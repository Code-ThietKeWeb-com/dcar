<?php
/*================================================================================*\
|| 							Name code : cat_product.php 		 		            	  ||
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
	var $action = "remote";

	/**
	 * function vntModule ()
	 * Khoi tao
	 **/
	function vntModule ()
	{
		global $Template, $vnT, $func, $DB, $conf;
		include (PATH_INCLUDE ."/JSON.php");

    $vnT->func->load_language("product");
    //load skin
    $this->loadSkinModule($this->action);

		$lang = ($vnT->input['lang']) ? $lang = $vnT->input['lang'] : $func->get_lang_default();
		$this->linkUrl = "?mod=" . $this->module . "&act=" . $this->action . "&lang=" . $lang;


		switch ($_GET['do']) {
      case "street_custom" : $jout =  $this->do_StreetCustom($lang);  break;
      case "add_street_price" : $jout =  $this->do_add_street_price($lang);  break;

			default : $jout =  'Error';  break;

		}
		flush();
		echo  $jout ;
		exit();

	}


  /**
   * function do_StreetPrice
   **/
  function do_StreetCustom ($lang)
  {
    global $vnT, $func, $DB, $conf;
    $city = (isset($vnT->input['city'])) ? $vnT->input['city'] : 1 ;
    $state = (isset($vnT->input['state'])) ?  $vnT->input['state'] : 2;

    $err ='';

    if(isset($_POST['do_submit']))
    {
      $data = $_POST;

      $price =  str_replace(array(",", "."), "", $_POST['price']);
      $cot = array();
      $cot['city'] = $_POST['city'];
      $cot['state'] =  $_POST['state'];
      $cot['name'] = $vnT->func->txt_HTML($_POST['name']);
      $cot['price'] = $price;
      $ok = $DB->do_insert("product_street_custom", $cot);
      if ($ok) {

        $data['id'] = $DB->insertid();
        $data['mess'] = 'Thêm thành công';
        $this->skin->assign("data", $data);
        $this->skin->parse("html_street_custom.html_sucess");


        $err = '<div class="alert alert-success" role="alert"><a href="#" class="close" data-dismiss="alert">&times;</a><strong>' . $vnT->lang['global']['announce'] . ' :</strong><div>' . $vnT->lang['add_success'] . '</div></div>';


      }else{
        $err = '<div class="alert alert-danger"><a href="#" class="close" data-dismiss="alert">&times;</a><strong>' . $vnT->lang['error'] . ' :</strong><div>' . $vnT->lang['add_failt'] . '</div></div>';


        $func->html_err($vnT->lang['add_failt'].$DB->debug());
      }

    }

    $data['option_city'] =  $this->List_Option_Array($vnT->setting['arr_city'],$city,"--- Chọn ---");
    $data['option_state'] =  $this->List_Option_Array($vnT->setting['arr_list_state'][$city],$state,"--- Chọn ---");

    $data["err"] = $err;

    $data['link_action'] = '?mod=land_price&act=remote&do=street_custom' ;
    $data['module'] = $this->module;
    $data['upload_dir'] = $this->module ;

    $this->skin->assign("data", $data);
    $this->skin->parse("html_street_custom");
    $textout =  $this->skin->text("html_street_custom");



    return $textout ;
  }

  /**
   * function do_add_street_price
   **/
  function do_add_street_price ($lang)
  {
    global $vnT, $func, $DB, $conf;

    $mess ='';
    $ok=0 ;
    $street = (int)$_POST['street'] ;

    if($street==0){
      die('Vui lòng chọn dữ liệu');
    }


    $txt_value =  str_replace(array(",", "."), "", $_POST['txt_value']);
    $cot = array();
    $cot['street'] = $street;
    $cot['txt_label'] = $_POST['txt_label'];
    $cot['txt_value'] = $txt_value;
    $cot['display_order'] = (int)$_POST['display_order'];
    $cot['date_post'] = time();
    $cot['date_update'] = time();
    $kq = $DB->do_insert("product_street_price", $cot);
    if ($kq) {
      $ok = 1;
      $mess = $vnT->lang['add_success'] ;
      $html = 'aaaaaaaaaa';

    }else{
      $mess = $vnT->lang['add_failt'] ;
    }



    $arr_json['html'] = $html;
    $arr_json['ok'] = $ok;
    $arr_json['mess'] = $mess;

    $json = new Services_JSON( );
    $textout = $json->encode($arr_json);

    return $textout;

  }

  // end class
}
$vntModule = new vntModule();
?>
