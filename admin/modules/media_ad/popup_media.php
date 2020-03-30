<?php
/*================================================================================*\
|| 							Name code : media.php 		 		            	  ||
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
  var $module = "media";
	var $action = "popup_media";

  /**
   * function vntModule ()
   * Khoi tao 
   **/
  function vntModule ()
  {
    global $Template, $vnT, $func, $DB, $conf;
    require_once ("function_media.php");
    $this->skin = new XiTemplate(DIR_MODULE . DS . "media_ad" . DS . "html" . DS . "popup_media.tpl");
    $this->skin->assign('PATH_ROOT', $conf['rootpath']);
    $this->skin->assign('LANG', $vnT->lang);		
	  $this->skin->assign('CONF', $vnT->conf);
		$this->skin->assign('ROOT_URI', ROOT_URI);
    $this->skin->assign("DIR_SKIN", "skins/".$vnT->conf['skin_acp']);
    $this->skin->assign('DIR_IMAGE', $vnT->dir_images);
	  $this->skin->assign('DIR_STYLE', $vnT->dir_style );		 
    $this->skin->assign('DIR_JS', $conf['rooturl'] . "js");
    $this->skin->assign("admininfo", $vnT->admininfo);
    $this->skin->assign('DIR_IMAGE_MEDIA', DIR_IMAGE_MEDIA);

    $this->linkUrl = "?mod=" . $this->module . "&act=" . $this->action  ;
		switch ($_GET['stype'])
		{
      case "gallery"  : $data['main'] = $this->do_PopupMedia(); break ;
      case "editor"  : $data['main'] = $this->do_PopupMedia(); break ;
      case "editor_gallery"	 : $data['main'] = $this->do_PopupMedia(); break ;
			default : $data['main'] =   $this->do_PopupMedia(); break ;
		}    
		$data['obj_return'] = $_GET['obj'];
    $this->skin->assign('data', $data);		
    $this->skin->parse("html_popup");
    $output =  $this->skin->text("html_popup");
		
    flush();
    echo $output;
    exit();
  }
 
	//============ do_PopupMedia
  function do_PopupMedia ()
  {
    global $vnT, $func, $DB, $conf;
    $err ='';
		$obj_return = $_GET['obj'];
		$module = $_GET['module'] ;		
		$folder = ($_GET['folder']== $_GET['module']."/") ? $_GET['module'] : $_GET['folder'] ; 				
		$type = ($_GET['type']) ? $_GET['type'] : "file";
    $style_btn_insert = 'style="display:none"' ;

		$data['path']	 = ($module) ? $module : "File";
		$data['folder']	 = $folder;
		$data['path_img'] = $folder ;
		
		if($_GET['CKEditorFuncNum']){
			$data['CKEditorFuncNum'] = $_GET['CKEditorFuncNum'];
			$obj_return = $_GET['CKEditor'];
		}else{
			$data['CKEditorFuncNum'] = "";
		}		

		if($_GET['stype']=="gallery") {
      $style_btn_insert = '' ;
      $data['btn_insert'] = 'vnTMedia.insertGallery()';
      $data['obj_gallery'] = $obj_return ;
    }
    if($_GET['stype']=="editor_gallery") {
      $style_btn_insert = '' ;
      $data['path']	 = 'File';
      $data['folder']	 = 'File/Image';
      $data['path_img'] = 'File/Image';
      $data['btn_insert'] = 'vnTMedia.insertEditor()';
    }

    $max_upload = ini_get('upload_max_filesize');
		$data['module'] = $module;
		$data['type'] = $type ;
    $data['max_upload'] = ini_get('upload_max_filesize');
    $data['max_size_bytes'] = $max_upload * 1000000;
    $data['err'] = $err; 
    $data['obj_return'] = $obj_return;
		$data['show_pic'] = (int)$_GET['show_pic'];
		$data['option_filetype'] = list_option_filetype ($type);

		$data['style_btn_insert'] = $style_btn_insert ;
		
    $data['link_action'] = $this->linkUrl ;
    /*assign the array to a template variable*/
    $this->skin->assign('data', $data);
    $this->skin->parse("html_popup_media");
    return $this->skin->text("html_popup_media");
  }
	  




   // end class
}
?>