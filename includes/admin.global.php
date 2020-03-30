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
  public static $DB;
 
  
  /**
   * mailer object.
   */
  var $mailer;   
   
 

  /**
   * session object
   */
  var $session;
  
  /**
   * skin object.
   */
  var $skin;
  
  /**
   * Array of data from config.php.
   *
   * @var	array
   */
  var $conf = array();
  var $setting = array();  
  var $cmd = "?vnTRUST";
  var $lang_name = "vn";
  var $lang = array();
  var $dir_mod;
  var $dir_skin;
  var $dir_image;
  var $dir_js;
  var $dir_style;

  /**
   * Constructor - initializes
   */
  function vnT_Registry ()
  {
    global $conf;
    // initialize
    $this->DB =  new DB();
    $this->func =  new Func_Admin();
     

    $this->fetchDbConfig($conf);
    $this->input = $this->func->get_request();
    $this->cmd = "?" . $this->conf['cmd'];
    $this->dir_skin =  "skins/" . $this->conf['skin_acp'];
    $this->dir_images =  "skins/" . $this->conf['skin_acp'] . "/images";
    $this->dir_style =  "skins/" . $this->conf['skin_acp'] . "/style";
    $this->dir_js =  "../js";
    $this->dir_mod =  "modules";
    $this->dir_upload =  "vnt_upload";

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

}
?>