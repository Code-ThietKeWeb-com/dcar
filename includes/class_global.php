<?php

/**
 * Class to store commonly-used variables
 *
 * @date		$Date: 2008-06-24 04:40:46 -0500 (Tue, 24 Jun 2008) $
 */
class vnT_Registry
{
  // general objects
  /**
   * Input cleaner object.
   *
   * @var	vB_Input_Cleaner
   */
  var $input;
  /**
   * Database object.
   *
   * @var	vB_Database
   */
  var $DB;
  /**
   * Lib object.
   *
   * @var	vB_Database
   */
  var $lib;

  /**
   * Cache object.
   */
  var $Cache;
  /**
   * mailer object.
   */
  var $mailer;
  /**
   * plugins object.
   * @var	array
   */
  var $plugins = array();
  // user
  /**
   * user object
   * @var	array
   */

  var $user;
  // user
  /**
   * user object
   * @var	array
   */
  var $product ;

  /**
   * session object
   */
  var $session;
  /**
   * skin object.
   */
  var $skin;
  var $content_skin = "";
  /**
   * Array of data from config.php.
   *
   * @var	array
   */
  var $conf = array();
  var $setting = array();
  var $output = "";
  var $cmd = "?vnTRUST";
  var $lang_name = "vn";
  var $lang = array();
  var $arr_lang_name = array();
  var $lang_default = "vn";
  var $link_lang = array();
  var $muti_lang = 0;
  var $link_root = ROOT_URI;
  var $array = array();
  var $stats;
  var $dir_mod;
  var $dir_skin;
  var $dir_image;
  var $dir_js;
  var $dir_style;
  var $deviceType ="destop";
  var $advertise ;

  /**
   * Constructor - initializes
   */
  function vnT_Registry ()
  {
    global $conf;
    // initialize
    $this->DB =  new DB();
    $this->func =  new Func_Global();
    //load Session
    $this->func->include_libraries('vntrust.session.session');
    $this->session = new vnT_Session();
    $this->fetchDbConfig($conf);
    //$this->load_content_skin();
    //$this->load_language();
    require_once (PATH_INCLUDE . DS . 'class_cache.php');
    $this->Cache =  new Cache();
    require_once (PATH_INCLUDE . DS . 'class_libs.php');
    $this->lib =  new Lib();

    $this->cmd = "?" . $this->conf['cmd'];
    $this->dir_skin = ROOT_URI . "skins/" . $this->conf['skin'];
    $this->dir_images = ROOT_URI . "skins/" . $this->conf['skin'] . "/images";
    $this->dir_style = ROOT_URI . "skins/" . $this->conf['skin'] . "/style";
    $this->dir_js = ROOT_URI . "js";
    $this->dir_mod = PATH_ROOT . "/modules";
    $this->dir_upload = PATH_ROOT . "/vnt_upload";
  }

  //-----------------fetchDbConfig
  function fetchDbConfig ($arr_old = "")
  {
    $sql = "SELECT array FROM config ";
    $result = $this->DB->query($sql);
    while ($row = $this->DB->fetch_row($result)) {
      if ($row['array'] != "") {
        $base64Encoded = unserialize($row['array']);
        foreach ($base64Encoded as $key => $value) {
          $this->conf[base64_decode($key)] = stripslashes(base64_decode($value));
        }
      }
    }
    if ($arr_old) {
      foreach ($arr_old as $key => $value) {
        $this->conf[$key] = $value;
      }
    }
    return $this->conf;
  }

  //-----------------load_language
  function load_language ($file = "main", $type = "modules")
  {
    global $input;

    $dirLang = (isset($_GET["lang"])) ? $_GET["lang"] : $this->lang_name;

    //load lang global
    $lang_global = DIR_LANG . DS . $dirLang . DS . "global.php";
    if (file_exists($lang_global)) {
      require_once ($lang_global);
      if (is_array($lang)) {
        foreach ($lang as $k => $v) {
          $this->lang['global'][$k] = stripslashes($v);
        }
      }
      unset($lang);
    }

    //load lang  modules ,block
    $file = ($file) ? $file : $this->conf['module'];
    $file_lang = DIR_LANG . DS . $dirLang . DS . $type . DS . $file . ".php";
    if (file_exists($file_lang)) {
      require_once ($file_lang);
      if (is_array($lang)) {
        foreach ($lang as $k => $v) {
          $this->lang[$file][$k] = stripslashes($v);
        }
      }
      unset($lang);
    }
  }

  //-----------------load_unit
  function load_unit ()
  {
    $result = $this->DB->query("select * FROM language WHERE name ='{$vnT->lang_name}' ");
    if ($row = $this->DB->fetch_row($result)) {
      $this->conf['date_format'] = $row['date_format'];
      $this->conf['time_format'] = $row['time_format'];
      $this->conf['unit'] = $row['unit'];
      $this->conf['num_format'] = $row['num_format'];
    }
  }

  //-----------------load_content_skin
  function load_content_skin ()
  {
    $result = $this->DB->query("select * FROM templates WHERE name ='" . $this->conf['skin'] . "' ");
    if ($row = $this->DB->fetch_row($result)) {
      $this->content_skin = $this->func->NDK_decode($row['content']);
      $this->content_skin = str_replace("[LOGO]", "image.php?type=tempale&name=img_logo", $this->content_skin);
      $this->content_skin = str_replace("[MADEBY]", "<a href='http://www.thietkeweb.com' target='_blank'>TRUST.vn</a>", $this->content_skin);
    }
  }
}
?>