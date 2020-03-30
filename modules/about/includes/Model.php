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
define("MOD_NAME","about");
define("DIR_MODULES", PATH_ROOT . "/modules/".MOD_NAME);
define("INCLUDE_PATH", dirname(__FILE__));
define("DIR_MOD", ROOT_URI . "modules/".MOD_NAME);
define("MOD_DIR_IMAGE", ROOT_URI . "modules/".MOD_NAME."/images");
define("LINK_MOD", $vnT->link_root . $vnT->setting['seo_name'][$vnT->lang_name]['about']);
class Model
{

  /**
   * The Constructor.
   */
  public function __construct()
  {
    global $vnT ;
    //autoloader
    include_once( INCLUDE_PATH .DS . 'autoloader.php' );

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

    $vnT->setting['arr_about'] = array();
    $result = $vnT->DB->query("SELECT n.*,nd.title,nd.friendly_url FROM about n,about_desc nd  WHERE n.aid=nd.aid 
										 AND lang='{$vnT->lang_name}'
										 AND display=1
										 And parentid=0
										 order by display_order ASC , date_post ASC");
    if ($num = $vnT->DB->num_rows($result)) {
      while ($row = $vnT->DB->fetch_row($result))
      {
        $vnT->setting['arr_about'][$row['aid']] = $row ;
      }
    }


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
   * create_link
   */
  function create_link ($id, $title, $extra = "")
  {
    global $vnT, $func, $DB, $conf;
    $text = $vnT->link_root . $title . ".html";
    return $text;
  }

  /**
   * get_nav_category
   */
  function get_nav_category ()
  {
    global $func, $DB, $vnT ,$input ;
    $text ='';

    $option_current = $vnT->lang['about']['about'];
    $link_all = LINK_MOD.".html";
    $current_all = ($input["aID"]) ? '' :  ' class="current" ' ;

    $list = '' ;
    if (is_array($vnT->setting['arr_about'])){

      foreach ($vnT->setting['arr_about'] as $key => $val) {

          $link = $this->create_link($val['aid'],$val['friendly_url']);
          $title = $vnT->func->HTML($val['title']);

          $current = '';
          if ( $key == $input["aID"]  )
          {
            $current = ' class="active current" ';
            $option_current = $title;
          }
          $list .='<li  '.$current.' ><a href="'.$link.'"  '.$current.'><span>'.$title.'</span></a></li>' ;
      }


      $textout = '<div class="menu-category">';
      $textout .= '<div class="mc-title">'.$option_current.'</div>';
      $textout .= '<div class="mc-content">';
      $textout .= '<ul>'.$list.'</ul>';
      $textout .= '</div>';
      $textout .= '</div>';
    }


    return $textout;
  }

  /**
   * box_category
   */
  function box_category ()
  {
    global $vnT,  $input;
    $textout = '';

    if (is_array($vnT->setting['arr_about'])) {
      $text = '<div class="box_category" ><ul>' ;
      foreach ($vnT->setting['arr_about'] as $key => $val) {

        $link = $this->create_link($val['aid'], $val['friendly_url']);
        $title = $vnT->func->HTML($val['title']);

        $current = '';
        if ($key == $input["aID"]) {
          $current = ' class="current active"';
        }
        $text .= '<li  "'.$current.' ><a href="' . $link . '"  ' . $current . '><span >' . $title . '</span></a></li>';
      }
      $text .='</ul></div>';

      $nd['f_title']=$vnT->lang['about']['about'];
      $nd['content'] = $text ;
      $textout =  $vnT->skin_box->parse_box("box",$nd);

    }

    return $textout;

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
    $textout .=  $this->box_category();
    return $textout;
  }

}
$model = new Model();
?>