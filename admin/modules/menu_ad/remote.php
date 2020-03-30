<?php
/*================================================================================*\
|| 							Name code : about.php 		 		            	  ||
||  				Copyright @2008 by Thai Son - CMS vnTRUST                     ||
\*================================================================================*/
/**
 * @version : 2.0
 * @date upgrade : 09/01/2009 by Thai Son
 **/
if (!defined('IN_vnT')) {
  die('Hacking attempt!');
}
//load Model
include(dirname(__FILE__) . "/functions.php");

class vntModule extends Model
{
  public $model;
  var $output = "";
  var $skin = "";
  var $linkUrl = "";
  var $module = "menu";
  var $action = "remote";
  var $setting = array();

  /**
   * function Controller ()
   * Khoi tao
   **/
  function vntModule()
  {
    global $Template, $vnT, $func, $DB, $conf;
		include (PATH_INCLUDE ."/JSON.php");

		$lang = ($vnT->input['lang']) ? $lang = $vnT->input['lang'] : $func->get_lang_default();
		$this->linkUrl = "?mod=" . $this->module . "&act=" . $this->action . "&lang=" . $lang;

		switch ($_GET['do']) {
      case "change_pos" : $jout =  $this->do_change_pos($lang);  break;
			default : $jout =  'Error';  break;

		}
		flush();
		echo  $jout ;
		exit();

	}


  /**
   * function do_change_pos
   **/
  function do_change_pos ($lang)
  {
    global $vnT, $func, $DB, $conf;
    $pos = $vnT->input['pos'];
    $textout =  $this->List_Parent($pos,$lang);
    return $textout ;
  }

  // end class
}
$vntModule = new vntModule();
?>