<?php
/*================================================================================*\
|| 							Name code : product.php 		 		 																	  # ||
||  				Copyright © 2007 by Thai Son - CMS vnTRUST                					# ||
\*================================================================================*/
/**
 * @version : 6.0
 * @date upgrade : 12/05/2015 by Thai Son
 **/

if (!defined('IN_vnT')) {
  die('Access denied');
}
define("MOD_NAME","main");
define("DIR_MODULES", PATH_ROOT . "/modules/".MOD_NAME);
define("INCLUDE_PATH", dirname(__FILE__));
define("DIR_MOD", ROOT_URI . "modules/".MOD_NAME);
define("MOD_DIR_IMAGE", ROOT_URI . "modules/".MOD_NAME."/images");
define("LINK_PRO", $vnT->link_root . $vnT->setting['seo_name'][$vnT->lang_name]['product']);

class Model
{

  /**
   * The Constructor.
   */
  public function __construct()
  {
    global $vnT ;

    //load setting
    $this->loadSetting();
  }


  /**
   * Take a class name and turn it into a file name.
   *
   * @param  string $class
   * @return string
   */
  function loadSetting()
  {
    global $vnT ;

  }

  /**
   * Take a class name and turn it into a file name.
   *
   * @param  string $class
   * @return string
   */
  function loadSkinModule($file_tpl , $data = array())
  {
    global $vnT , $input;
    $this->skin = new XiTemplate( DIR_MODULES . "/html/". $file_tpl . ".tpl");
    $this->skin->assign('DIR_MOD', DIR_MOD);
    $this->skin->assign('LANG_MOD', $vnT->lang[MOD_NAME]);
    $this->skin->assign('LANG', $vnT->lang);
    $this->skin->assign('INPUT', $input);
    $this->skin->assign('CONF', $vnT->conf);
    $this->skin->assign('DIR_IMAGE', $vnT->dir_images); 
    $this->skin->assign('DIR_JS', $vnT->dir_js);
    $this->skin->assign('data', $data);
  }



  /**
   * function load_html ()
   *
   **/
  function load_html ($file, $data)
  {
    global $vnT, $input;
    $html = new XiTemplate( DIR_MODULES . "/html/" . $file . ".tpl");

    $html->assign('DIR_MOD', DIR_MOD);
    $html->assign('LANG', $vnT->lang);
    $html->assign('INPUT', $input);
    $html->assign('CONF', $vnT->conf);
    $html->assign('DIR_IMAGE', $vnT->dir_images);
    $html->assign("data", $data);

    $html->parse($file);
    return $html->text($file);
  }

  /**
   *
   * @param
   * @return
   */
  function box_sidebar($info = array())
  {
    global $vnT , $input ;
    $textout = '' ;
    return $textout;
  }

}
$model = new Model();
?>