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

    //load skin
    $this->loadSkinModule($this->action);

		$lang = ($vnT->input['lang']) ? $lang = $vnT->input['lang'] : $func->get_lang_default();
		$this->linkUrl = "?mod=" . $this->module . "&act=" . $this->action . "&lang=" . $lang;


		switch ($_GET['do']) {
      case "add_payment" : $jout =  $this->do_Add_Payment($lang);  break;
      case "edit_payment" : $jout =  $this->do_Edit_Payment($lang);  break;

      case "add_info_cus" : $jout =  $this->do_Add_InfoCus($lang);  break;
      case "edit_info_cus" : $jout =  $this->do_Edit_InfoCus($lang);  break;

			default : $jout =  'Error';  break;

		}
		flush();
		echo  $jout ;
		exit();

	}


  /**
   * function do_Add_Info
   **/
  function do_Add_Payment ($lang)
  {
    global $vnT, $func, $DB, $conf;
    $err ='';
    if(isset($_POST['do_submit']))
    {
      $data = $_POST;

      $title = $vnT->func->txt_HTML($_POST['title']);
      if(empty($_POST['csrf_token']) || ($_POST['csrf_token'] != $_SESSION['vnt_csrf_token']) ) {
        $err =  '<div class="alert alert-danger"><a href="#" class="close" data-dismiss="alert">&times;</a><strong>' . $vnT->lang['error'] . ' :</strong><div>' . $vnT->lang['err_csrf_token'] . '</div></div>';
      }

      if(empty($err))
      {
        $cot = array();
        $cot['title'] = $title;
        $cot['picture'] = $_POST['picture'];
        $cot['content'] = $DB->mySQLSafe($_POST['content']);
        $cot['date_post'] = time();
        $cot['date_update'] = time();
        $cot['adminid'] =$vnT->admininfo['adminid'];

        $ok = $DB->do_insert("about_payment", $cot);
        if ($ok) {
          $id = $DB->insertid();

          //insert adminlog
          $func->insertlog("Add", $vnT->input['sub'], $id);


          $data['id'] = $id ;
          $data['mess'] = 'Thêm thành công';
          $this->skin->assign("data", $data);
          $this->skin->parse("html_payment_form.html_sucess");


          $err = '<div class="alert alert-success" role="alert"><a href="#" class="close" data-dismiss="alert">&times;</a><strong>' . $vnT->lang['announce'] . ' :</strong><div>' . $vnT->lang['add_success'] . '</div></div>';


        }else{
          $err = '<div class="alert alert-danger"><a href="#" class="close" data-dismiss="alert">&times;</a><strong>' . $vnT->lang['error'] . ' :</strong><div>' . $vnT->lang['add_failt'] . '</div></div>';
        }
      }
    }

    if (! isset($_SESSION['vnt_csrf_token'])) {
      $_SESSION['vnt_csrf_token'] = md5(uniqid(rand(), TRUE)) ;
    }
    $data['csrf_token'] = $_SESSION['vnt_csrf_token'] ;

    $data["err"] = $err;

    $data['link_action'] = '?mod='.MOD_NAME.'&act=remote&do=add_payment' ;
    $data['link_upload'] = '?mod=media&act=popup_media&module='.$this->module.'&folder='.$this->module.'&obj=picture&type=image&TB_iframe=true&width=900&height=474';
    $data['module'] = $this->module ;
    $data['folder_browse'] = $this->module ;

    $this->skin->assign("data", $data);
    $this->skin->parse("html_payment_form");
    $textout =  $this->skin->text("html_payment_form");


    return $textout ;
  }



  /**
   * function do_Edit_Info
   **/
  function do_Edit_Payment ($lang)
  {
    global $vnT, $func, $DB, $conf;

    $id = (int)$vnT->input['id'];
    $res_ck = $DB->query("SELECT *	FROM  about_payment	WHERE id=".$id) ;
    if($data = $DB->fetch_row($res_ck))
    {
      $err ='';

      if(isset($_POST['do_submit']))
      {
        $data = $_POST;

        $title = $vnT->func->txt_HTML($_POST['title']);

        if(empty($_POST['csrf_token']) || ($_POST['csrf_token'] != $_SESSION['vnt_csrf_token']) ) {
          $err = '<div class="alert alert-danger"><a href="#" class="close" data-dismiss="alert">&times;</a><strong>' . $vnT->lang['error'] . ' :</strong><div>' . $vnT->lang['err_csrf_token'] . '</div></div>';
        }

        if(empty($err))
        {
          $cot['title'] = $title;
          $cot['picture'] = $_POST['picture'];
          $cot['content'] = $DB->mySQLSafe($_POST['content']);
          $cot['date_update'] = time();

          $ok = $DB->do_update("about_payment", $cot,"id=".$id);
          if ($ok) {

            unset($_SESSION['vnt_csrf_token']);
            $data['id'] = $id ;
            $data['mess'] = 'Cập nhật thành công';
            $this->skin->assign("data", $data);
            $this->skin->parse("html_payment_form.html_sucess");

            $err = '<div class="alert alert-success" role="alert"><a href="#" class="close" data-dismiss="alert">&times;</a><strong>' . $vnT->lang['announce'] . ' :</strong><div>' . $vnT->lang['edit_success'] . '</div></div>';


          }else{
            $err = '<div class="alert alert-danger"><a href="#" class="close" data-dismiss="alert">&times;</a><strong>' . $vnT->lang['error'] . ' :</strong><div>' . $vnT->lang['edit_failt'] . '</div></div>';
          }
        }


      }

      if (! isset($_SESSION['vnt_csrf_token'])) {
        $_SESSION['vnt_csrf_token'] = md5(uniqid(rand(), TRUE)) ;
      }
      $data['csrf_token'] = $_SESSION['vnt_csrf_token'] ;

      $data["err"] = $err;
      $data['link_action'] = '?mod='.MOD_NAME.'&act=remote&do=edit_payment&id='.$id ;
      $data['link_upload'] = '?mod=media&act=popup_media&module='.$this->module.'&folder='.$this->module.'&obj=picture&type=image&TB_iframe=true&width=900&height=474';

      $this->skin->assign("data", $data);
      $this->skin->parse("html_payment_form");
      $textout =  $this->skin->text("html_payment_form");

    }else{
      $textout = 'Not Found' ;
    }

    return $textout ;
  }


  /**
   * function do_Add_InfoCus
   **/
  function do_Add_InfoCus ($lang)
  {
    global $vnT, $func, $DB, $conf;

    $item_id = (int)$vnT->input['item_id'];
    $res_ck = $DB->query("SELECT n.*, nd.title
				FROM step_customer n, step_customer_desc nd
				WHERE n.sid=nd.sid
				AND nd.lang='$lang'
				AND n.sid=".$item_id) ;
		if($row_item = $DB->fetch_row($res_ck))
		{
      $err ='';
      if(isset($_POST['do_submit']))
      {
        $data = $_POST;

        $title = $vnT->func->txt_HTML($_POST['title']);
        if(empty($_POST['csrf_token']) || ($_POST['csrf_token'] != $_SESSION['vnt_csrf_token']) ) {
          $err =  '<div class="alert alert-danger"><a href="#" class="close" data-dismiss="alert">&times;</a><strong>' . $vnT->lang['error'] . ' :</strong><div>' . $vnT->lang['err_csrf_token'] . '</div></div>';
        }

        if(empty($err))
        {
          $cot = array();
          $cot['sid'] = $item_id;
          $cot['title'] = $title;
          $cot['picture'] = $_POST['picture'];
          $cot['description'] = $DB->mySQLSafe($_POST['description']);
          $cot['date_post'] = time();
          $cot['date_update'] = time();
          $cot['adminid'] =$vnT->admininfo['adminid'];

          $ok = $DB->do_insert("step_customer_info", $cot);
          if ($ok) {
            $id = $DB->insertid();

            //insert adminlog
            $func->insertlog("Add", $vnT->input['sub'], $id);


            $data['id'] = $id ;
            $data['mess'] = 'Thêm thành công';
            $this->skin->assign("data", $data);
            $this->skin->parse("html_info_form.html_sucess");


            $err = '<div class="alert alert-success" role="alert"><a href="#" class="close" data-dismiss="alert">&times;</a><strong>' . $vnT->lang['announce'] . ' :</strong><div>' . $vnT->lang['add_success'] . '</div></div>';


          }else{
            $err = '<div class="alert alert-danger"><a href="#" class="close" data-dismiss="alert">&times;</a><strong>' . $vnT->lang['error'] . ' :</strong><div>' . $vnT->lang['add_failt'] . '</div></div>';


          }

        }


      }

      if (! isset($_SESSION['vnt_csrf_token'])) {
        $_SESSION['vnt_csrf_token'] = md5(uniqid(rand(), TRUE)) ;
      }
      $data['csrf_token'] = $_SESSION['vnt_csrf_token'] ;

      $data["err"] = $err;

      $data['link_action'] = '?mod='.MOD_NAME.'&act=remote&do=add_info_cus&item_id='.$item_id ;
      $data['link_upload'] = '?mod=media&act=popup_media&module='.$this->module.'&folder='.$this->module.'&obj=picture&type=image&TB_iframe=true&width=900&height=474';
      $data['module'] = $this->module ;
      $data['folder_browse'] = $this->module ;

      $this->skin->assign("data", $data);
      $this->skin->parse("html_info_form");
      $textout =  $this->skin->text("html_info_form");

    }else{
      $textout = 'Not Found' ;
    }
    return $textout ;
  }




  /**
   * function do_Edit_InfoCus
   **/
  function do_Edit_InfoCus ($lang)
  {
    global $vnT, $func, $DB, $conf;
    $w = ($vnT->setting['img_width']) ? $vnT->setting['img_width'] : 750 ;
    $w_thumb = ($vnT->setting['img_width_grid']) ? $vnT->setting['img_width_grid'] : 100 ;

    $id = (int)$vnT->input['id'];
    $res_ck = $DB->query("SELECT *	FROM  step_customer_info	WHERE id=".$id) ;
    if($data = $DB->fetch_row($res_ck))
    {
      $err ='';

      if(isset($_POST['do_submit']))
      {
        $data = $_POST;

        if(empty($_POST['csrf_token']) || ($_POST['csrf_token'] != $_SESSION['vnt_csrf_token']) ) {
          $err =  '<div class="alert alert-danger"><a href="#" class="close" data-dismiss="alert">&times;</a><strong>' . $vnT->lang['error'] . ' :</strong><div>' . $vnT->lang['err_csrf_token'] . '</div></div>';
        }

        if(empty($err))
        {
          $title = $vnT->func->txt_HTML($_POST['title']);

          $cot['title'] = $title;
          $cot['picture'] = $_POST['picture'];
          $cot['description'] = $DB->mySQLSafe($_POST['description']);
          $cot['date_update'] = time();

          $ok = $DB->do_update("step_customer_info", $cot,"id=".$id);
          if ($ok) {

            unset($_SESSION['vnt_csrf_token']);

            $data['id'] = $id ;
            $data['mess'] = 'Cập nhật thành công';
            $this->skin->assign("data", $data);
            $this->skin->parse("html_info_form.html_sucess");

            $err = '<div class="alert alert-success" role="alert"><a href="#" class="close" data-dismiss="alert">&times;</a><strong>' . $vnT->lang['announce'] . ' :</strong><div>' . $vnT->lang['edit_success'] . '</div></div>';


          }else{
            $err = '<div class="alert alert-danger"><a href="#" class="close" data-dismiss="alert">&times;</a><strong>' . $vnT->lang['error'] . ' :</strong><div>' . $vnT->lang['edit_failt'] . '</div></div>';
            $func->html_err($vnT->lang['add_failt'].$DB->debug());
          }

        }

      }


      if ($data['picture']) {
        $data['pic'] = $this->get_picture($data['picture'],$w_thumb) . "  <a href=\"javascript:del_picture('picture')\" class=\"del\">Xóa</a>";
        $data['style_upload'] = "style='display:none' ";
      } else {
        $data['pic'] = "";
      }

      if (! isset($_SESSION['vnt_csrf_token'])) {
        $_SESSION['vnt_csrf_token'] = md5(uniqid(rand(), TRUE)) ;
      }
      $data['csrf_token'] = $_SESSION['vnt_csrf_token'] ;

      $data["err"] = $err;
      $data['link_action'] = '?mod='.MOD_NAME.'&act=remote&do=edit_info_cus&id='.$id ;
      $data['link_upload'] = '?mod=media&act=popup_media&module='.$this->module.'&folder='.$this->module.'&obj=picture&type=image&TB_iframe=true&width=900&height=474';

      $this->skin->assign("data", $data);
      $this->skin->parse("html_info_form");
      $textout =  $this->skin->text("html_info_form");

    }else{
      $textout = 'Not Found' ;
    }

    return $textout ;
  }

  // end class
}
$vntModule = new vntModule();
?>
