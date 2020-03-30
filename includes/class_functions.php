<?php

/*================================================================================*\
|| 							Name code : class_functions.php 		 		 											  # ||
||  				Copyright © 2007 by Thai Son - CMS vnTRUST                					# ||
\*================================================================================*/
/**
 * @version : 1.0
 * @date upgrade : 21/12/2007 by Thai Son
 **/
class Func_Global
{
  var $base_input = array();

  function Func_Global ()
  {
    global $vnT, $conf;
    if (version_compare(PHP_VERSION, '5.4.0', '<')) {
      $this->check_gpc();
    }
  }

  //=========
  function check_gpc ()
  {
    global $vnT;
    if (! get_magic_quotes_gpc())
    {
      if (is_array($_GET))
      {
        while (list ($key, $_3FEA0) = each($_GET))
        {
          if (is_array($_GET[$key]))
          {
            while (list ($key2, $_3FF1D) = each($_GET[$key]))
            {
              if (! is_array($_3FF1D))
              {
                $_GET[$key][$key2] = addslashes($_3FF1D);
              }
            }
            @reset($_GET[$key]);
          } else
          {
            $_GET[$key] = addslashes($_3FEA0);
          }
        }
        @reset($_GET);
      }
      if (is_array($_POST))
      {
        while (list ($key, $_3FEA0) = each($_POST))
        {
          if (is_array($_POST[$key]))
          {
            while (list ($key2, $_3FF1D) = each($_POST[$key]))
            {
              if (! is_array($_3FF1D))
              {
                $_POST[$key][$key2] = addslashes($_3FF1D);
              }
            }
            @reset($_POST[$key]);
          } else
          {
            $_POST[$key] = addslashes($_3FEA0);
          }
        }
        @reset($_POST);
      }
      if (is_array($_COOKIE))
      {
        while (list ($key, $_3FEA0) = each($_COOKIE))
        {
          if (is_array($_COOKIE[$key]))
          {
            while (list ($key2, $_3FF1D) = each($_COOKIE[$key]))
            {
              if (! is_array($_3FF1D))
              {
                $_COOKIE[$key][$key2] = addslashes($_3FF1D);
              }
            }
            @reset($_COOKIE[$key]);
          } else
          {
            $_COOKIE[$key] = addslashes($_3FEA0);
          }
        }
        @reset($_COOKIE);
      }
      if (is_array($_FILES))
      {
        while (list ($key, $_3FEA0) = each($_FILES))
        {
          if (is_array($_FILES[$key]))
          {
            while (list ($key2, $_3FF1D) = each($_FILES[$key]))
            {
              if (! is_array($_3FF1D))
              {
                $_FILES[$key][$key2] = addslashes($_3FF1D);
              }
            }
            @reset($_FILES[$key]);
          } else
          {
            $_FILES[$key] = addslashes($_3FEA0);
          }
        }
        @reset($_FILES);
      }
    }
  }

  /**
   * Determine if SSL is used.
   *
   * @since 2.6.0
   *
   * @return bool True if SSL, false if not used.
   */
  function is_ssl() {
    if ( isset($_SERVER['HTTPS']) ) {
      if ( 'on' == strtolower($_SERVER['HTTPS']) )
        return true;
      if ( '1' == $_SERVER['HTTPS'] )
        return true;
    } elseif ( isset($_SERVER['SERVER_PORT']) && ( '443' == $_SERVER['SERVER_PORT'] ) ) {
      return true;
    }
    return false;
  }


  /******************************* fetchDbConfig ************************************
  $$confName : ten cua config can load
  tra ve : 1 mang $array_conf[$key] = $value
   *************************************************/
  function fetchDbConfig ($arr_old = "")
  {
    global $conf, $DB, $vnT;
    $sql = "SELECT array FROM config ";
    $result = $DB->query($sql);

    while ($row = $DB->fetch_row($result))
    {
      if ($row['array'] != "")
      {
        $base64Encoded = unserialize($row['array']);
        foreach ($base64Encoded as $key => $value)
        {
          $array_conf[base64_decode($key)] = stripslashes(base64_decode($value));
        }
      }
    }

    if ($arr_old)
    {
      foreach ($arr_old as $key => $value)
      {
        $array_conf[$key] = $value;
      }
    }
    return $array_conf;
  }

  //-----------------is_muti_lang
  function is_muti_lang ()
  {
    global $vnT, $input, $DB;
    $result = $DB->query("SELECT lang_id FROM language ");
    $num_lang = $DB->num_rows($result);
    return ($num_lang>1) ? 1 : 0 ;
  }
  //-----------------load_language
  function load_language ($file = "", $type = "modules")
  {
    global $vnT, $input, $conf;
    $dirLang = ($vnT->lang_name) ? $vnT->lang_name : "vn";

    //load lang global
    $lang_global = $conf['rootpath'] . "language/" . $dirLang . "/global.php";
    if (file_exists($lang_global))
    {
      require_once ($lang_global);
      if (is_array($lang))
      {
        foreach ($lang as $k => $v)
        {
          $vnT->lang['global'][$k] = stripslashes($v);
        }
      }
      unset($lang);
    }
    //load lang  modules ,block
    if (empty($file)) $file = $conf['module'];

    $file_lang = $conf['rootpath'] . "language/" . $dirLang . "/" . $type . "/" . $file . ".php";
    if (file_exists($file_lang))
    {
      require_once ($file_lang);
      if (is_array($lang))
      {
        foreach ($lang as $k => $v)
        {
          $vnT->lang[$file][$k] = stripslashes($v);
        }
      }
      unset($lang);
    }
  }

  //-----------------load_unit
  function load_unit ()
  {
    global $conf, $DB, $vnT;
    $result = $DB->query("select * FROM language WHERE name ='{$vnT->lang_name}' ");
    if ($row = $DB->fetch_row($result))
    {
      $conf['date_format'] = $row['date_format'];
      $conf['time_format'] = $row['time_format'];
      $conf['unit'] = $row['unit'];
      $conf['num_format'] = $row['num_format'];
    }
  }

  //-----------------load_SiteDoc
  function load_SiteDoc ($sitename)
  {
    global $conf, $DB, $vnT;
    $out = "";
    if ($vnT->lang_name)
    {
      $sql = "SELECT doc_content FROM sitedoc WHERE doc_name ='{$sitename}' and lang='{$vnT->lang_name}'";
    } else
    {
      $sql = "SELECT doc_content FROM sitedoc WHERE doc_name ='{$sitename}' ";
    }
    $result = $DB->query($sql);
    if ($row = $DB->fetch_row($result))
    {
      $out = $row['doc_content'];
    }
    return $out;
  }

  //-----------------load_MailTemp
  function load_MailTemp ($name)
  {
    global $conf, $DB, $vnT;
    $out = "";
    if ($vnT->lang_name)
    {
      $sql = "SELECT content FROM mail_template WHERE name ='{$name}' and lang='" . $vnT->lang_name . "'";
    } else
    {
      $sql = "SELECT content FROM mail_template WHERE name ='{$name}' ";
    }
    $result = $DB->query($sql);
    if ($row = $DB->fetch_row($result))
    {
      $out = $row['content'];
    }
    return $out;
  }

  //----------------- getIp
  function getIp() {
    $ip = $_SERVER['REMOTE_ADDR'];
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
      $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
      $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    }
    return $ip;
  }

  //----------------- getBrowser
  function getBrowser ($browser)
  {
    if ($browser)
    {
      if (strpos($browser, "Mozilla/5.0")) $browsertyp = "Mozilla";
      if (strpos($browser, "Mozilla/4")) $browsertyp = "Netscape";
      if (strpos($browser, "Mozilla/3")) $browsertyp = "Netscape";
      if (strpos($browser, "Firefox") || strpos($browser, "Firebird")) $browsertyp = "Firefox";
      if (strpos($browser, "MSIE")) $browsertyp = "Internet Explorer";
      if (strpos($browser, "Opera")) $browsertyp = "Opera";
      if (strpos($browser, "Opera Mini")) $browsertyp = "Opera Mini";
      if (strpos($browser, "Netscape")) $browsertyp = "Netscape";
      if (strpos($browser, "Camino")) $browsertyp = "Camino";
      if (strpos($browser, "Galeon")) $browsertyp = "Galeon";
      if (strpos($browser, "Konqueror")) $browsertyp = "Konqueror";
      if (strpos($browser, "Safari")) $browsertyp = "Safari";
      if (strpos($browser, "Chrome")) $browsertyp = "Chrome";
      if (strpos($browser, "OmniWeb")) $browsertyp = "OmniWeb";
      if (strpos($browser, "Flock")) $browsertyp = "Firefox Flock";
      if (strpos($browser, "Lynx")) $browsertyp = "Lynx";
      if (strpos($browser, "Mosaic")) $browsertyp = "Mosaic";
      if (strpos($browser, "Shiretoko")) $browsertyp = "Shiretoko";
      if (strpos($browser, "IceCat")) $browsertyp = "IceCat";
      if (strpos($browser, "BlackBerry")) $browsertyp = "BlackBerry";
      if (strpos($browser, "Googlebot") || strpos($browser, "www.google.com")) $browsertyp = "Google Bot";
      if (strpos($browser, "Yahoo")) $browsertyp = "Yahoo Bot";
      if (! isset($browsertyp)) $browsertyp = "UnKnown";
    }

    return $browsertyp;
  }

  //-----------------  getOs
  function getOs ($os)
  {
    if ($os)
    {
      if (strpos($os, "Win95") || strpos($os, "Windows 95")) $ostyp = "Windows 95";
      if (strpos($os, "Win98") || strpos($os, "Windows 98")) $ostyp = "Windows 98";
      if (strpos($os, "WinNT") || strpos($os, "Windows NT")) $ostyps = "Windows NT";
      if (strpos($os, "WinNT 5.0") || strpos($os, "Windows NT 5.0")) $ostyp = "Windows 2000";
      if (strpos($os, "WinNT 5.1") || strpos($os, "Windows NT 5.1")) $ostyp = "Windows XP";
      if (strpos($os, "WinNT 6.0") || strpos($os, "Windows NT 6.0")) $ostyp = "Windows Vista";
      if (strpos($os, "WinNT 6.1") || strpos($os, "Windows NT 6.1")) $ostyp = "Windows 7";
      if (strpos($os, "WinNT 6.2") || strpos($os, "Windows NT 6.2")) $ostyp = "Windows 8";
      if (strpos($os, "Linux")) $ostyp = "Linux";
      if (strpos($os, "OS/2")) $ostyp = "OS/2";
      if (strpos($os, "Sun")) $ostyp = "Sun OS";
      if (strpos($os, "iPod")) $ostyp = "iPodTouch";
      if (strpos($os, "iPhone")) $ostyp = "iPhone";
      if (strpos($os, "iPad")) $ostyp = "iPad";
      if (strpos($os, "Android")) $ostyp = "Android";
      if (strpos($os, "Windows Phone")) $ostyp = "Windows Phone";
      if (strpos($os, "Macintosh") || strpos($os, "Mac_PowerPC")) $ostyp = "Mac OS";
      if (strpos($os, "Googlebot") || strpos($os, "www.google.com")) $ostyp = "Google Bot";

      if (! isset($ostyp)) $ostyp = "UnKnown";

    }
    return $ostyp;
  }


  /**
   * Sets the authentication cookies based User ID.
   *
   * The $remember parameter increases the time that the cookie will be kept. The
   * default the cookie is kept without remembering is two days. When $remember is
   * set, the cookies will be kept for 14 days or two weeks.
   *
   * @since 2.5
   *
   * @param int $user_id User ID
   * @param bool $remember Whether to remember the user or not
   */
  function vnt_set_member_cookie($mem_id, $remember = false)
  {
    global $vnT, $func, $DB, $conf;

    if ( $remember ) {
      $expiration = $expire = time() + 1209600; // 14 ngay
    } else {
      $expiration = time() + 172800;
      $expire = 0;
    }

    $auth_cookie = $this->vnt_generate_member_cookie($mem_id, $expiration, 'auth');

    // Set httponly if the php version is >= 5.2.0
    if ( version_compare(phpversion(), '5.2.0', 'ge') ) {
      setcookie(MEMBER_COOKIE, $auth_cookie, $expire, COOKIE_PATH, COOKIE_DOMAIN, false, true);

    } else {
      $cookie_domain = COOKIE_DOMAIN;
      if ( !empty($cookie_domain) )
        $cookie_domain .= '; HttpOnly';
      setcookie(MEMBER_COOKIE, $auth_cookie, $expire, COOKIE_PATH, $cookie_domain);
    }
  }

  /**
   * Removes all of the cookies associated with authentication.
   *
   * @since 2.5
   */
  function vnt_clear_member_cookie() {
    global $vnT, $func, $DB, $conf;
    setcookie(MEMBER_COOKIE, ' ', time() - 31536000, COOKIE_PATH, COOKIE_DOMAIN);
  }


  /**
   * Generate authentication cookie contents.
   *

   * @param int $mem_id User ID
   * @param int $expiration Cookie expiration in seconds
   * @param string $scheme Optional. The cookie scheme to use: auth, secure_auth, or logged_in
   * @return string Authentication cookie contents
   */
  function vnt_generate_member_cookie($mem_id, $expiration) {
    global $func, $DB, $conf;
    $info = $this->get_member_info($mem_id);
    $hash = md5($info['mem_id'] . '|' . $info['password']);
    $member_cookie = $info['username'] . '|' . $hash . '|' . $expiration ;
    return $member_cookie ;
  }

  //==========
  function Insert_Session ()
  {
    global $DB, $vnT, $input, $vnTRUST, $conf;
    $s_id = md5(uniqid(microtime()));
    $time = time();
    $ip = $this->getIp();
    $agent = $_SERVER['HTTP_USER_AGENT'];
    $browser = $this->getBrowser($agent);
    $os = $this->getOs($agent);
    $date = @date("d/m/Y");
    $location = str_replace($vnT->conf['rooturl'],"",$vnT->seo_url);
    $cot['s_id'] = $s_id;
    $cot['ip'] = $ip;
    $cot['agent'] = $agent;
    $cot['time'] = $time;
    $cot['location'] = $location;
    $DB->do_insert("sessions", $cot);

    if(isset($_COOKIE[MEMBER_COOKIE]))
    {
      $arr_cookie_member =  explode("|",$_COOKIE[MEMBER_COOKIE])  ;
      if(trim($arr_cookie_member[0]))
      {
        $res_mem = $DB->query("SELECT * FROM members WHERE username='".$arr_cookie_member[0]."' ");
        if($row_mem = $DB->fetch_row($res_mem))
        {
          $mem_hash = md5($row_mem['mem_id'] . '|' . $row_mem['password']);
          if($arr_cookie_member[1] ==$mem_hash )
          {
            $DB->query("UPDATE sessions SET mem_id={$row_mem['mem_id']} WHERE s_id='{$s_id}'");
          }
        }
      }
    }

    $vnT->session->set('s_id', $s_id);
    $vnT->user['session_id'] = $s_id;
    $_SESSION['insert_counter'] = 1 ;
    //insert counter
    if ($vnT->conf['counter'])
    {
      $sql = "SELECT * FROM counter WHERE date_log ='{$date}' ";
      $result = $DB->query($sql);
      if ($row = $DB->fetch_row($result))
      {
        $query = "UPDATE counter SET count=count+1 WHERE id={$row['id']}";
      } else
      {
        $query = "INSERT INTO counter (date_log,count) VALUES ('{$date}',1)";
      }
      $DB->query($query);
      $cot_d['date_log'] = $date;
      $cot_d['browser'] = $browser;
      $cot_d['ip'] = $ip;
      $cot_d['os'] = $os;
      $cot_d['date_time'] = $time;
      $DB->do_insert("counter_detail", $cot_d);
      //echo $DB->debug();
    }
    // Delete Old Session
    $thoihan = time() - 1800;
    $run_delete = $DB->query("DELETE FROM sessions WHERE time < {$thoihan} ");

    // End Delete


  }

  // End func
  //===================================================================
  function Update_Session ()
  {
    global $DB, $vnT, $vnTRUST, $input;

    $s_id = $vnT->session->get("s_id");
    //print "s_id = ".$s_id."<br>";
    $location = str_replace($vnT->conf['rooturl'],"",$vnT->seo_url);
    $isonline = $DB->query("SELECT * FROM sessions WHERE s_id='{$s_id}' ");
    if ($ok = $DB->fetch_row($isonline))
    {
      $time = time();
      $sql_update = "UPDATE sessions SET time='{$time}', location='{$location}' WHERE s_id='{$s_id}'";
      //print "sql_update = ".$sql_update."<br>";
      $run_update = $DB->query($sql_update);
    } else
    {
      //print "Update ma insert <br>";
      $this->Insert_Session();
    }
    // Delete Old Session
    $thoihan = time() - 1800;
    $run_delete = $DB->query("DELETE FROM sessions WHERE time < {$thoihan} ");
  }

  // End func
  //====== get_security_code
  function get_security_code ()
  {
    global $DB, $vnT;
    $s_id = $vnT->session->get("s_id");
    $code = rand(100000, 999999);
    if(!$_SESSION["sec_code"] || $_SESSION["sec_code_time"] < (time() - 1))
    {
      $_SESSION["sec_code"] = $code;
      $_SESSION["sec_code_time"] = time();
    }
    $code = $_SESSION["sec_code"];
    $DB->query("UPDATE sessions SET sec_code='{$code}' WHERE s_id='{$s_id}'");
    return $code;
  }

  //====== check_security_code
  function check_security_code ($type="user_sec_code")
  {
    global $vnT , $input;

    $out =  0 ;
    if($type=="user_sec_code"){
      if( $vnT->user['sec_code'] == $input['security_code']) {
        $out = 1;
      }
    }

    if($type=="session_sec_code"){
      if( $_SESSION['sec_code'] == $input['security_code']) {
        $out = 1;
      }
    }

    if($type=="reCAPTCHA"){

      $api_url     = $vnT->conf['reCAPTCHA_api_url'];
      $site_key    =  $vnT->conf['reCAPTCHA_site_key'];
      $secret_key  =  $vnT->conf['reCAPTCHA_secret_key'];

      //lấy dữ liệu được post lên
      $site_key_post    = $_POST['g-recaptcha-response'];

      //lấy IP của khach
      if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $remoteip = $_SERVER['HTTP_CLIENT_IP'];
      } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $remoteip = $_SERVER['HTTP_X_FORWARDED_FOR'];
      } else {
        $remoteip = $_SERVER['REMOTE_ADDR'];
      }

      if($vnT->conf['reCAPTCHA_type_check']=="get_url"){
        //tạo link kết nối
        $api_url = $api_url.'?secret='.$secret_key.'&response='.$site_key_post.'&remoteip='.$remoteip;
        //lấy kết quả trả về từ google
        $response = file_get_contents($api_url);
        //dữ liệu trả về dạng json
        $response = json_decode($response);
        if($response->success == true) {
          $out = 1 ;
        }
      }else{
        //load recaptchalib
        require_once (PATH_INCLUDE . DS ."recaptchalibv2.php");
        $resp = recaptcha_check_answer ($secret_key, $remoteip, $site_key_post);
        if ($resp->success) {
          $out = 1 ;
        }
      }

    }

    return $out;
  }


  // 
  //==================================================================
  function Get_Stats ()
  {
    global $DB, $vnT;
    $stats = array();

    // Session Start
    if (! $vnT->session->has('s_id'))
    {
      //print "insert moi <br>";
      $this->Insert_Session();
    } else
    {
      //print "update  <br>";
      $this->Update_Session();
    }
    // End Session


    //thong ke web counter
    if(isset($_SERVER['HTTP_REFERER']))
    {
      $domain_ref = trim($_SERVER['HTTP_REFERER']);
      $domain_ref = str_replace("https://", "", $domain_ref);
      $domain_ref = str_replace("http://", "", $domain_ref);
      $domain_ref = str_replace("www.", "", $domain_ref);
      $domain_ref = substr($domain_ref, 0, strpos($domain_ref, "/"));

      $my_domain = $_SERVER['HTTP_HOST'];
      $my_domain = str_replace("www.", "", $my_domain);

      if (($domain_ref != $my_domain) && ($domain_ref != 'localhost') && ($domain_ref != ''))
      {
        //check
        $res_ck = $DB->query("SELECT id,num_click FROM counter_website WHERE domain='" . $domain_ref . "' ");
        if ($row_ck = $DB->fetch_row($res_ck))
        {
          $cot_s['num_click'] = ($row_ck['num_click'] + 1);
          $cot_s['date_click'] = time();
          $DB->do_update("counter_website", $cot_s, "id=" . $row_ck['id']);
        } else
        {
          $cot_s['domain'] = $domain_ref;
          $cot_s['num_click'] = 1;
          $cot_s['date_click'] = time();
          $DB->do_insert("counter_website", $cot_s);
        }
      }
    }


    return $stats;
  }

  // End func
  //==========================================================================
  function User_Login ($info)
  {
    global $DB, $vnT;
    @session_regenerate_id() ;
    $time = $this->getTimestamp();
    $ip = $this->getIp();
    $agent = $_SERVER['HTTP_USER_AGENT'];
    if ($vnT->session->has('s_id'))
    {
      $s_id = $vnT->session->get("s_id");
      $run_update = $DB->query("UPDATE sessions SET time='{$time}',mem_id='{$info['mem_id']}' WHERE s_id='{$s_id}'");
    } else
    {
      $s_id = md5(uniqid(microtime()));
      $query = "INSERT INTO sessions (s_id,ip,agent,time,mem_id) VALUES ('{$s_id}','{$ip}','{$agent}','{$time}','{$info['mem_id']}') ";
      $data_arr = $DB->query($query);
      $vnT->session->set('s_id', $s_id);
    }
    $vnT->session->set('mem_id', $info['mem_id']);
    return 1;
  }


  /**
   * function get_member_info()
   * @param	mem_id
   * @return	array
   */
  function get_member_info($mem_id) {
    global $vnT, $func, $DB, $conf;

    $result = $DB->query("SELECT * FROM members WHERE mem_id=".$mem_id);
    $info = $DB->fetch_row($result) ;
    return $info ;
  }


  //==========================================================================
  function Get_User_Info ($s_id)
  {
    global $DB, $vnT;
    $info_arr = $DB->query("SELECT * FROM sessions WHERE s_id='{$s_id}' ");
    if ($info = $DB->fetch_row($info_arr))
    {
      $info['session_id'] = $info['s_id'];
      if ($info['mem_id'] != 0)
      {
        $user_arr = $DB->query("SELECT * FROM members WHERE mem_id=".$info['mem_id'] );
        if ($user = $DB->fetch_row($user_arr))
        {
          while (list ($k, $v) = each($user))  {
            $info[$k] = $v;
          }
        }else{
          $info['mem_id'] = 0;
        }
      }
    }
    return $info;
  }

  //--------------
  function format_size ($rawSize)
  {
    if ($rawSize / 1048576 > 1) return round($rawSize / 1048576, 1) . ' MB';
    else
      if ($rawSize / 1024 > 1) return round($rawSize / 1024, 1) . ' KB';
      else
        return round($rawSize, 1) . ' Bytes';
  }

  /**
   * @function : format_number
   * @param 		: $num -> chuoi so
   *							$seperator-> dau phan cach
   * @return		: chuoi so
   */
  function format_number ($num, $seperator = ",")
  {
    $string = strrev(substr(chunk_split(strrev($num), 3, $seperator), 0, - 1));
    return $string;
  }

  // getTimestamp
  function getTimestamp ($GMT = 0)
  {
    global $vnT, $conf;
    $h = ($GMT) ? $GMT : $vnT->conf['GMT'] ;
    $hm = ($h * 60);
    $ms = ($hm * 60);
    $timestamp = time() - ($ms);
    return $timestamp;
  }

  // getDateTimeFormat
  function getDateTimeFormat ($str_format, $GMT = 0)
  {
    global $vnT, $conf;
    $out = date($str_format, $this->getTimestamp($GMT));
    return $out;
  }

  // endcode
  function NTS_encode ($t)
  {
    global $vnT;
    $t = trim($t);
    $code = base64_encode($t);
    $code = time() . "_" . $code . $code = substr($code, 5, strlen($code) - 7) . substr($code, 0, 5) . substr($code, strlen($code) - 2);
    $code = substr($code, 0, 3) . substr($code, 6, strlen($code) - 8) . substr($code, 3, 3) . substr($code, strlen($code) - 2);
    return $code;
  }

  function NTS_decode ($t)
  {
    global $vnT;
    $code = trim($t);
    $code = substr($code, 0, 3) . substr($code, strlen($code) - 5, 3) . substr($code, 3, strlen($code) - 8) . substr($code, strlen($code) - 2);
    $code = substr($code, strlen($code) - 7, 5) . substr($code, 0, strlen($code) - 7) . substr($code, strlen($code) - 2);
    $code = substr($code, strrpos($code, "_") + 1);
    $code = base64_decode($code);
    return $code;
  }

  // NDK_encode
  function NDK_encode ($t)
  {
    global $vnT;
    $t = trim($t);
    $code = base64_encode($t);
    $code = substr($code, 5, strlen($code) - 7) . substr($code, 0, 5) . substr($code, strlen($code) - 2);
    $code = substr($code, 0, 3) . substr($code, 6, strlen($code) - 8) . substr($code, 3, 3) . substr($code, strlen($code) - 2);
    return $code;
  }

  // NDK_decode
  function NDK_decode ($t)
  {
    global $vnT;
    $code = trim($t);
    $code = substr($code, 0, 3) . substr($code, strlen($code) - 5, 3) . substr($code, 3, strlen($code) - 8) . substr($code, strlen($code) - 2);
    $code = substr($code, strlen($code) - 7, 5) . substr($code, 0, strlen($code) - 7) . substr($code, strlen($code) - 2);
    $code = base64_decode($code);
    return $code;
  }

  function base64url_encode($data) {
    return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
  }

  function base64url_decode($data) {
    return base64_decode(str_pad(strtr($data, '-_', '+/'), strlen($data) % 4, '=', STR_PAD_RIGHT));
  }

  // Get_Input
  function Get_Input ($t)
  {
    global $vnT;
    $code = trim($t);
    $code = str_replace("%7C", "|", $t);
    $cmd_arr = explode("|", $code);
    foreach ($cmd_arr as $value)
    {
      if (! empty($value))
      {
        $k = trim(substr($value, 0, strpos($value, ":")));
        $v = trim(substr($value, strpos($value, ":") + 1));
        if ($k) $in_arr[$k] = $v;
        $this->base_input[$k] = $v;
      }
    }
    if (is_array($_GET))
    {

      while (list ($k, $v) = each($_GET))
      {
        if (is_array($_GET[$k]))
        {
          while (list ($k2, $v2) = each($_GET[$k]))
          {
            $in_arr[$this->clean_key($k)][$this->clean_key($k2)] = $this->clean_value($v2);
          }
        } else
        {
          if ($k != $vnT->conf['cmd']) $in_arr[$this->clean_key($k)] = $this->clean_value($v);
        }
      }
    }
    if (is_array($_POST))
    {
      while (list ($k, $v) = each($_POST))
      {
        if (is_array($_POST[$k]))
        {
          while (list ($k2, $v2) = each($_POST[$k]))
          {
            $in_arr[$this->clean_key($k)][$this->clean_key($k2)] = $this->clean_value($v2);
          }
        } else
        {
          $in_arr[$this->clean_key($k)] = $this->clean_value($v);
        }
      }
    }


    return $in_arr;
  }

  // Location
  function Location ()
  {
    global $input;
    $txt = "";
    while (list ($k, $v) = each($input))
    {
      if ((! empty($k)) && (! empty($v))) $txt .= $k . ":" . $v . "|";
    }
    return $txt;
  }

  // clean_key
  function clean_key ($key)
  {
    $key = preg_replace("/\.\./", "", $key);
    $key = preg_replace("/\_\_(.+?)\_\_/", "", $key);
    $key = preg_replace("/^([\w\.\-\_]+)$/", "$1", $key);
    return $key;
  }

  //==== clean_value
  function clean_value ($val)
  {
    if ($val == "")
    {
      return "";
    }
    $val = $this->xss_clean($val);
    $val = @htmlspecialchars($val);
    $val = str_replace("&#032;", " ", $val);
    $val = str_replace("<!--", "&#60;&#33;--", $val);
    $val = str_replace("-->", "--&#62;", $val);
    $val = preg_replace("/<script/i", "&#60;script", $val);
    $val = preg_replace("/\n/", "<br />", $val); // Convert literal newlines
    $val = preg_replace("/\\\$/", "&#036;", $val);
    $val = preg_replace("/\r/", "", $val); // Remove literal carriage returns
    $val = str_replace("!", "&#33;", $val);



    // Ensure unicode chars are OK
    $val = preg_replace("/&amp;#([0-9]+);/s", "&#\\1;", $val);
    // Inject SQL
    $val = str_replace("union select", "", $val);
    $val = str_replace("information_schema", "", $val);
    //    	$val = preg_replace( "/\\\(?!&amp;#|\?#)/", "&#092;", $val ); 
    return $val;
  }


  //==== xss_clean
  function xss_clean ($data)
  {
    if ($data == "")  {
      return "";
    }

    // Fix &entity\n;
    $data = str_replace(array('&amp;','&lt;','&gt;'), array('&amp;amp;','&amp;lt;','&amp;gt;'), $data);
    $data = preg_replace('/(&#*\w+)[\x00-\x20]+;/u', '$1;', $data);
    $data = preg_replace('/(&#x*[0-9A-F]+);*/iu', '$1;', $data);
    $data = html_entity_decode($data, ENT_COMPAT, 'UTF-8');

// Remove any attribute starting with "on" or xmlns
    $data = preg_replace('#(<[^>]+?[\x00-\x20"\'])(?:on|xmlns)[^>]*+>#iu', '$1>', $data);

// Remove javascript: and vbscript: protocols
    $data = preg_replace('#([a-z]*)[\x00-\x20]*=[\x00-\x20]*([`\'"]*)[\x00-\x20]*j[\x00-\x20]*a[\x00-\x20]*v[\x00-\x20]*a[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu', '$1=$2nojavascript...', $data);
    $data = preg_replace('#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*v[\x00-\x20]*b[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu', '$1=$2novbscript...', $data);
    $data = preg_replace('#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*-moz-binding[\x00-\x20]*:#u', '$1=$2nomozbinding...', $data);

// Only works in IE: <span style="width: expression(alert('Ping!'));"></span>
    $data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?expression[\x00-\x20]*\([^>]*+>#i', '$1>', $data);
    $data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?behaviour[\x00-\x20]*\([^>]*+>#i', '$1>', $data);
    $data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:*[^>]*+>#iu', '$1>', $data);

// Remove namespaced elements (we do not need them)
    $data = preg_replace('#</*\w+:\w[^>]*+>#i', '', $data);

    do
    {
      // Remove really unwanted tags
      $old_data = $data;
      $data = preg_replace('#</*(?:applet|b(?:ase|gsound|link)|embed|frame(?:set)?|i(?:frame|layer)|l(?:ayer|ink)|meta|object|s(?:cript|tyle)|title|xml)[^>]*+>#i', '', $data);
    }
    while ($old_data !== $data);

    return $data;

  }



  //========================
  function md10 ($txt)
  { // MD10 Encode by NDK
    $txt = md5($txt);
    $txt = base64_encode($txt);
    $txt = md5($txt);
    return $txt;
  }

//========================
  function isStrongPassword($password)
  {
    $strongPasswd_reg = "/^((?=(.*[a-zAZ]){1,})(?=(.*[\\d]){1,})(?=(.*[\\W]){1,})(?!.*\\s)).{6,}$/" ;
    $blacklistPassword = array("123456", "123456a@");
    return (preg_match($strongPasswd_reg, $password) && !in_array(strtolower($password), $blacklistPassword ));

  }


  // br2n
  function br2n ($text = "")
  {
    //	$t = addslashes($t);
    $text = str_replace("\r\n", "<br>", $text);
    return $text;
  }

  // txt_tooltip
  function txt_tooltip ($text = "")
  {
    $text = preg_replace("/&(?!#[0-9]+;)/s", '&amp;', $text);
    $text = str_replace("<", "&lt;", $text);
    $text = str_replace(">", "&gt;", $text);
    $text = str_replace('"', "&quot;", $text);
    $text = str_replace("'", "&#39;", $text);
    $text = str_replace("\r\n", "", $text);
    $text = str_replace("\n", "<br>", $text);
    return $text;
  }

  // cut_string
  function cut_string ($str, $len, $more)
  {
    if ($str == '' || $str == NULL) return $str;
    if (is_array($str)) return $str;
    $str = trim($str);
    if (strlen($str) <= $len) return $str;
    $str = substr($str, 0, $len);
    if ($str != '')
    {
      if (! substr_count($str, " "))
      {
        if ($more) $str .= " ...";
        return $str;
      }
      while (strlen($str) && ($str[strlen($str) - 1] != " "))
        $str = substr($str, 0, - 1);
      $str = substr($str, 0, - 1);
      if ($more) $str .= " ...";
    }
    return $str;
  }

  // NTS_cut_string
  function NTS_cut_string ($str, $len, $ext)
  {
    if ($str == '' || $str == NULL) return $str;
    if (is_array($str)) return $str;
    $str = trim($str);
    if (strlen($str) <= $len) return $str;
    $tmp = trim(substr($str, 0, $len));
    $pos = strrpos($tmp, " ");
    //	echo "pos = $pos <br>";
    $str1 = substr($str, 0, $pos) . " ...";
    //	echo "str1 = $str1 <br>";
    $str2 = substr($str, $pos);
    //	echo "str2 = $str2 <br>";
    $text = "<span style=\"display: none;\" id=\"" . $ext . "_s\">{$str1}</span>
	<span style=\"display: none;\" id=\"" . $ext . "_l\">{$str}</span>
	<span id=\"" . $ext . "_t\">{$str1}</span> 
	<span onclick=\"ToggleTextView('" . $ext . "_s', '" . $ext . "_l', '" . $ext . "_t', '" . $ext . "_b'); return false;\" id=\"" . $ext . "_b\" style=\"cursor:pointer;\">[+]</span>";
    return $text;
  }

  // safe_html
  function safe_html ($str, $tags = "<script><style><link>", $stripContent = false)
  {
    preg_match_all("/<([^>]+)>/i", $tags, $allTags, PREG_PATTERN_ORDER);
    foreach ($allTags[1] as $tag)
    {
      $replace = "%(<$tag.*?>)(.*?)(<\/$tag.*?>)%is";
      if ($stripContent)
      {
        $str = preg_replace($replace, '', $str);
      }
      $str = preg_replace($replace, '${2}', $str);
    }
    return $str;
  }

  //- strip_all_tags
  function strip_all_tags($string, $remove_breaks = false) {
    $string = preg_replace( '@<(script|style)[^>]*?>.*?</\\1>@si', '', $string );
    $string = strip_tags($string);

    if ( $remove_breaks )
      $string = preg_replace('/[\r\n\t ]+/', ' ', $string);

    return trim( $string );
  }

  //- trim_words
  function trim_words( $text, $num_words = 55, $more = null ) {
    if ( null === $more )
      $more = "...";

    $original_text = $text;
    $text = $this->strip_all_tags( $text );

    $words_array = preg_split( "/[\n\r\t ]+/", $text, $num_words + 1, PREG_SPLIT_NO_EMPTY );
    $sep = ' ';

    if ( count( $words_array ) > $num_words ) {
      array_pop( $words_array );
      $text = implode( $sep, $words_array );
      $text = $text . $more;
    } else {
      $text = implode( $sep, $words_array );
    }
    return $text ;
  }

  //- HTML
  function HTML ($t = "")
  {
    //	$t = addslashes($t);
    $text = nl2br($t);
    $text = str_replace("[url]http://", "[url]", $text);
    $text = str_replace("[url=http://", "[url=", $text);
    //$text = preg_replace("/(http.*:\/\/.+)\s/U", "<a href=\"$1\">$1</a> ", $text);
    $text = preg_replace('/(\[b\])(.+?)(\[\/b\])/', '<b>\\2</b>', $text);
    $text = preg_replace('/(\[i\])(.+?)(\[\/i\])/', '<i>\\2</i>', $text);
    $text = preg_replace('/(\[u\])(.+?)(\[\/u\])/', "<u>\\2</u>", $text);
    $text = preg_replace('/(\[color=(.+?)\])(.+?)(\[\/color\])/', '<font color=\\2>\\3</font>', $text);
    $text = preg_replace('/(\[email\])(.+?)(\[\/email\])/', "<a href=\"mailto:\\2\">\\2 </a>", $text);
    $text = preg_replace('/(\[email=(.+?)\])(.+?)(\[\/email\])/', "<a href=\"mailto:\\2\">\\3</a>", $text);
    $text = preg_replace('/(\[url\])(.+?)(\[\/url\])/', "<a href=\"http://\\2\" target=\"_blank\">\\2</a>", $text);
    $text = preg_replace('/(\[url=\])(.+?)(\[\/url\])/', "<a href=\"http://\\2\" target=\"_blank\">\\2</a>", $text);
    $text = preg_replace('/(\[url=(.+?)\])(.+?)(\[\/url\])/', "<a href=\"http://\\2\" target=\"_blank\">\\3</a>", $text);

    $text = preg_replace("#\[url href=([^\]]*)\]([^\[]*)\[/url\]#i", '<a href="\\1" target="_blank" rel="nofollow">\\2</a>', $text);
    $text = preg_replace('#\[img\]([^\[]*)\[/img\]#i', '<img src="\\1" alt=""/>', $text);

    //$text = stripslashes($text);
    $text = str_replace("!!!!", "!", $text);
    return $text;
  }

  /**
   * @function : txt_HTML
   * @param 		: $t -> 1 chuoi string
   * @return		: 1 chuoi string
   */
  function txt_HTML ($t = "")
  {
    //	$t = addslashes($t);
    //	$t = preg_replace("/&(?!#[0-9]+;)/s", '&amp;', $t );
    $t = str_replace("<", "&lt;", $t);
    $t = str_replace(">", "&gt;", $t);
    $t = str_replace('"', "&quot;", $t);
    $t = str_replace("'", '&#039;', $t);
    return $t;
  }

  /**
   * @function : txt_unHTML
   * @param 		: $t -> 1 chuoi string
   * @return		: 1 chuoi string
   */
  function txt_unHTML ($t = "")
  {
    //	$t = stripslashes($t);
    //	$t = nl2br($t);
    $t = str_replace("&lt;", "<", $t);
    $t = str_replace("&gt;", ">", $t);
    $t = str_replace("&quot;", '"', $t);
    $t = str_replace('&#039;', "'", $t);
    return $t;
  }

  // m_random_str
  function m_random_str ($len = 5)
  {
    $s = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    mt_srand((double) microtime() * 1000000);
    $unique_id = '';
    for ($i = 0; $i < $len; $i ++)
      $unique_id .= substr($s, (mt_rand() % (strlen($s))), 1);
    return $unique_id;
  }

   // generator_password
  function generator_password ($len = 8)
  {
    //$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_-=+;:,.?";
    $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_-=+";
    $length = rand(10, 16);
    $password = substr( str_shuffle(sha1(rand() . time()) . $chars ), 0, $len );
    return $password;
  }
  
  // utf8_to_ascii
  function utf8_to_ascii($str) {
    $chars = array(
      'a'	=>	array('ấ','ầ','ẩ','ẫ','ậ','ắ','ằ','ẳ','ẵ','ặ','á','à','ả','ã','ạ','â','ă'),
      'A'	=>	array('Ấ','Ầ','Ẩ','Ẫ','Ậ','Ắ','Ằ','Ẳ','Ẵ','Ặ','Á','À','Ả','Ã','Ạ','Â','Ă'),
      'e' =>	array('ế','ề','ể','ễ','ệ','é','è','ẻ','ẽ','ẹ','ê'),
      'E' =>	array('Ế','Ề','Ể','Ễ','Ệ','É','È','Ẻ','Ẽ','Ẹ','Ê'),
      'i'	=>	array('í','ì','ỉ','ĩ','ị'),
      'I'	=>	array('Í','Ì','Ỉ','Ĩ','Ị'),
      'o'	=>	array('ố','ồ','ổ','ỗ','ộ','ớ','ờ','ở','ỡ','ợ','ó','ò','ỏ','õ','ọ','ô','ơ'),
      'O'	=>	array('Ố','Ồ','Ổ','Ô','Ộ','Ớ','Ờ','Ở','Ỡ','Ợ','Ó','Ò','Ỏ','Õ','Ọ','Ô','Ơ'),
      'u'	=>	array('ứ','ừ','ử','ữ','ự','ú','ù','ủ','ũ','ụ','ư'),
      'U'	=>	array('Ứ','Ừ','Ử','Ữ','Ự','Ú','Ù','Ủ','Ũ','Ụ','Ư'),
      'y'	=>	array('ý','ỳ','ỷ','ỹ','ỵ'),
      'Y'	=>	array('Ý','Ỳ','Ỷ','Ỹ','Ỵ'),
      'd'	=>	array('đ'),
      'D'	=>	array('Đ'),
    );
    foreach ($chars as $key => $arr)
      foreach ($arr as $val)
        $str = str_replace($val,$key,$str);
    return $str;
  }

  // make_url
  function make_url ($str)
  {
    $str = $this->utf8_to_ascii($str);
    $str = trim($str);
    $str = preg_replace('/[^a-zA-Z0-9-\/\s]/', '-', $str );
    $str = str_replace(" ", "-", $str);
    while (strstr($str,"--"))	{
      $str = str_replace("--", "-", $str);
    }

    $str = strtolower($str);

    return $str;
  }


  //------- get_keyword ---------------------
  function get_keyword ($keyword)
  {
    global $input, $conf, $vnT;
    $lower = '
		a|b|c|d|e|f|g|h|i|j|k|l|m|n|o|p|q|r|s|t|u|v|w|x|y|z
		|á|à|ả|ã|ạ|ă|ắ|ặ|ằ|ẳ|ẵ|â|ấ|ầ|ẩ|ẫ|ậ
		|đ
		|é|è|ẻ|ẽ|ẹ|ê|ế|ề|ể|ễ|ệ
		|í|ì|ỉ|ĩ|ị
		|ó|ò|ỏ|õ|ọ|ô|ố|ồ|ổ|ỗ|ộ|ơ|ớ|ờ|ở|ỡ|ợ
		|ú|ù|ủ|ũ|ụ|ư|ứ|ừ|ử|ữ|ự
		|ý|ỳ|ỷ|ỹ|ỵ';
    $upper = '
		A|B|C|D|E|F|G|H|I|J|K|L|M|N|O|P|Q|R|S|T|U|V|W|X|Y|Z
		|Á|À|Ả|Ã|Ạ|Ă|Ắ|Ặ|Ằ|Ẳ|Ẵ|Â|Ấ|Ầ|Ẩ|Ẫ|Ậ
		|Đ
		|É|È|Ẻ|Ẽ|Ẹ|Ê|Ế|Ề|Ể|Ễ|Ệ
		|Í|Ì|Ỉ|Ĩ|Ị
		|Ó|Ò|Ỏ|Õ|Ọ|Ô|Ố|Ồ|Ổ|Ỗ|Ộ|Ơ|Ớ|Ờ|Ở|Ỡ|Ợ
		|Ú|Ù|Ủ|Ũ|Ụ|Ư|Ứ|Ừ|Ử|Ữ|Ự
		|Ý|Ỳ|Ỷ|Ỹ|Ỵ';
    $arrayUpper = explode('|', preg_replace("/\n|\t|\r/", "", $upper));
    $arrayLower = explode('|', preg_replace("/\n|\t|\r/", "", $lower));
    $text = str_replace($arrayUpper, $arrayLower, $keyword);
    return $text;
  }

  // check_html
  function check_html ($str, $strip = "")
  {
    global $vnT;

    $AllowableHTML = ($strip != "nohtml") ? $strip : "" ;
    $txt = strip_tags($str,$AllowableHTML);

    preg_match_all("/<([^>]+)>/i",$AllowableHTML,$allTags,PREG_PATTERN_ORDER);
    foreach ($allTags[1] as $tag){
      $txt = preg_replace("/<".$tag."[^>]*>/i","<".$tag.">",$txt);
    }


    $txt = str_replace("\n\r","",$txt);
    $txt = str_replace("\n","",$txt);
    $txt = str_replace("\r","",$txt);
    return $txt;
  }

  //- embed code
  function tranferHTML ($t = "")
  {
    $t = str_replace("&lt;", "<", $t);
    $t = str_replace("&gt;", ">", $t);
    $t = str_replace('&quot;', '"', $t);
    $t = str_replace("&#039;", "'", $t);
    return $t;
  }

  /**
   * vnt_preg_quote()
   *
   * @param string $a
   * @return
   */
  function vnt_preg_quote( $a )
  {
    return preg_quote( $a, "/" );
  }

  /**
   * thum
   *
   * @params  string
   * @params  string
   *
   * @return
   */
  function thum ($imgfile = "", $path, $maxWidth, $maxHeight="",$crop=0)
  {
    $info = @getimagesize($imgfile);
    $mime = $info[2];
    $fext = ($mime == 1 ? 'image/gif' : ($mime == 2 ? 'image/jpeg' : ($mime == 3 ? 'image/png' : NULL)));
    switch ($fext)
    {
      case 'image/jpeg':
        if (! function_exists('imagecreatefromjpeg'))
        {
          die('No create from JPEG support');
        } else
        {
          $img['src'] = @imagecreatefromjpeg($imgfile);
        }
        break;
      case 'image/png':
        if (! function_exists('imagecreatefrompng'))
        {
          die("No create from PNG support");
        } else
        {
          $img['src'] = @imagecreatefrompng($imgfile);
        }
        break;
      case 'image/gif':
        if (! function_exists('imagecreatefromgif'))
        {
          die("No create from GIF support");
        } else
        {
          $img['src'] = @imagecreatefromgif($imgfile);
        }
        break;
    }
    $img['old_w'] = @imagesx($img['src']);
    $img['old_h'] = @imagesy($img['src']);

    if($crop){
      // Ratio cropping
      $offsetX	= 0;
      $offsetY	= 0;

      $cropRatio		= explode(':', (string) $crop );
      if (count($cropRatio) == 2)
      {
        $ratioComputed		= $img['old_w'] /  $img['old_h'];
        $cropRatioComputed	= (float) $cropRatio[0] / (float) $cropRatio[1];

        if ($ratioComputed < $cropRatioComputed)
        { // Image is too tall so we will crop the top and bottom
          $origHeight	= $img['old_h'];
          $img['old_h']		= $img['old_w'] / $cropRatioComputed;
          $offsetY	= ($origHeight - $img['old_h']) / 2;
        }
        else if ($ratioComputed > $cropRatioComputed)
        { // Image is too wide so we will crop off the left and right sides
          $origWidth	= $img['old_w'];
          $img['old_w']		= $img['old_h'] * $cropRatioComputed;
          $offsetX	= ($origWidth - $img['old_w']) / 2;
        }
      }

      $xRatio		= $maxWidth / $img['old_w'];
      $yRatio		= $maxHeight / $img['old_h'];

      if ($xRatio * $height < $maxHeight)
      { // Resize the image based on width
        $new_h	= ceil($xRatio * $img['old_h']);
        $new_w	= $maxWidth;
      }
      else // Resize the image based on height
      {
        $new_w	= ceil($yRatio * $img['old_w']);
        $new_h	= $maxHeight;
      }

    }else{
      $new_h = $img['old_h'];
      $new_w = $img['old_w'];
      $offsetX=0;
      $offsetY = 0 ;

      if ($img['old_w'] > $maxWidth)
      {
        $new_w = $maxWidth;
        $new_h = ($maxWidth / $img['old_w']) * $img['old_h'];
      }
      if ($new_h > $maxWidth)
      {
        $new_h = $maxWidth;
        $new_w = ($new_h / $img['old_h']) * $img['old_w'];
      }
    }

    $img['des'] = @imagecreatetruecolor($new_w, $new_h);
    if($fext=="image/png"){
      @imagealphablending($img['des'], false );
      @imagesavealpha($img['des'], true);
    }else{
      $white = @imagecolorallocate($img['des'], 255, 255, 255);
      @imagefill($img['des'], 1, 1, $white);
    }
    @imagecopyresampled($img['des'], $img['src'], 0, 0, $offsetX, $offsetY, $new_w, $new_h, $img['old_w'], $img['old_h']);
    //	print "path = ".$path."<br>";	
    @touch($path);
    switch ($fext)
    {
      case 'image/pjpeg':
      case 'image/jpeg':
      case 'image/jpg':
        @imagejpeg($img['des'], $path, 90);
        break;
      case 'image/png':
        @imagepng($img['des'], $path);
        break;
      case 'image/gif':
        @imagegif($img['des'], $path, 90);
        break;
    }
    // Finally, we destroy the images in memory.
    @imagedestroy($img['des']);
  }

  // makedate
  function makedate ($text)
  {
    $tmp = explode("-", $text);
    return $tmp[2] . "/" . $tmp[1] . "/" . $tmp[0];
  }

  // makedatetoMySQL
  function makedatetoMySQL ($text)
  {
    $tmp = explode("/", $text);
    return $tmp[2] . "-" . $tmp[1] . "-" . $tmp[0];
  }

  // Create_Link
  function Create_Link ($mod,$act="",$id=0,$title="",$extra="")
  {
    global $input, $conf, $vnT;

    $link_mod = $vnT->link_root . $vnT->setting['seo_name'][$vnT->lang_name][$mod];
    if($act){
      switch ($act)
      {
        case "category" : $linkout = $link_mod."/".$title."-".$id.".html";	 break ;
        case "detail" : $linkout = $link_mod."/".$id."/".$title.".html";	 break ;
        default : $linkout = $link_mod."/".$act."/".$id."/".$vnT->func->make_url($title).".html";	 break ;
      }
      $linkout .= ($extra) ? "/".$extra : "" ;
    }else{
      $linkout = $link_mod .".html";
    }

    return $linkout;
  }



  //======paginate_js
  function paginate_js ($numRows, $maxRows, $cPage = 1, $object, $pmore = 4, $class = "pagelink")
  {
    global $input, $vnT;
    $navigation = "";
    // get total pages
    $totalPages = ceil($numRows / $maxRows);
    $next_page = $pmore;
    $prev_page = $pmore;
    if ($cPage < $pmore) $next_page = $pmore + $pmore - $cPage;
    if ($totalPages - $cPage < $pmore) $prev_page = $pmore + $pmore - ($totalPages - $cPage);
    if ($totalPages > 1)
    {
      $navigation .= "<ul>";
      // Show first page
      if ($cPage > ($pmore + 1))
      {
        $pLink = $object . "1)";
        $navigation .= "<li><a href=\"javascript:void(0)\" onclick=\"" . $pLink . "\" ><i class=\"fa fa-angle-double-left\"></i></a></li>";
      }
      // End
      // Show Prev page
      if ($cPage > 1)
      {
        $numpage = $cPage - 1;
        $pLink = $object . "{$numpage})";
        $navigation .= "<li><a href=\"javascript:void(0)\" onclick=\"" . $pLink . "\" ><i class=\"fa fa-angle-left\"></i></a></li>";

      }
      // End
      // Left
      for ($i = $prev_page; $i >= 0; $i --)
      {
        $pagenum = $cPage - $i;
        if (($pagenum > 0) && ($pagenum < $cPage))
        {
          $pLink = $object . "{$pagenum})";
          $navigation .= "<li><a href=\"javascript:void(0)\" onclick=\"" . $pLink . "\"  class='" . $class . "'>" . $pagenum . "</a></li>";
        }
      }
      // End
      // Current
      $navigation .= "<li><span class=\"pagecur\">" . $cPage . "</span></li>";
      // End

      // Right
      for ($i = 1; $i <= $next_page; $i ++)
      {
        $pagenum = $cPage + $i;
        if (($pagenum > $cPage) && ($pagenum <= $totalPages))
        {
          $pLink = $object . "{$pagenum})";
          $navigation .= "<li><a href=\"javascript:void(0)\" onclick=\"" . $pLink . "\"  class='" . $class . "'>" . $pagenum . "</a></li>";
        }
      }
      // End
      // Show Next page
      if ($cPage < $totalPages)
      {
        $numpage = $cPage + 1;
        $pLink = $object . "{$numpage})";
        $navigation .= "<li><a href=\"javascript:void(0)\" onclick=\"" . $pLink . "\" class='btnPage'><i class=\"fa fa-angle-right\"></i></a></li>";

      }
      // End
      // Show Last page
      if ($cPage < ($totalPages - $pmore))
      {
        $pLink = $object . "{$totalPages})";
        $navigation .= "<li><a  href=\"javascript:void(0)\" onclick=\"" . $pLink . "\"  class='btnPage' ><i class=\"fa fa-angle-double-right\"></i></a></li>";
      }
      // End
    } // end if total pages is greater than one
    $navigation .= "</ul>";
    return $navigation;
  }
  //======htaccess_paginate	
  function htaccess_paginate ($root_link, $numRows, $maxRows, $extra = "", $cPage = 1, $p = "p", $pmore = 4, $class = "pagelink")
  {
    global $input, $vnT, $conf;
    $navigation = "";
    $extra = str_replace(array("&","="),array(",","-"),$extra);
    // get total pages
    $totalPages = ceil($numRows / $maxRows);
    $next_page = $pmore;
    $prev_page = $pmore;
    if ($cPage < $pmore) $next_page = $pmore + $pmore - $cPage;
    if ($totalPages - $cPage < $pmore) $prev_page = $pmore + $pmore - ($totalPages - $cPage);
    if ($totalPages > 1)
    {
      $navigation .= "<ul>";
      // Show first page
      if ($cPage > ($pmore + 1))
      {
        $pLink = $root_link . "/{$p}-1,{$extra}";
        $navigation .= "<li><a href='" . $pLink . "'  ><i class=\"fa fa-angle-double-left\"></i></a></li>";
      }
      // End
      // Show Prev page
      if ($cPage > 1)
      {
        $numpage = $cPage - 1;
        $pLink =  $root_link . "/{$p}-" . $numpage . "{$extra}" ;
        $navigation .= "<li><a href='" . $pLink . "' ><i class=\"fa fa-angle-left\"></i></a></li>";
      }
      // End	
      // Left
      for ($i = $prev_page; $i >= 0; $i --)
      {
        $pagenum = $cPage - $i;
        if (($pagenum > 0) && ($pagenum < $cPage))
        {
          $pLink = $root_link . "/{$p}-{$pagenum}{$extra}";
          $navigation .= "<li><a href='" . $pLink . "' class='" . $class . "'>" . $pagenum . "</a></li>";
        }
      }
      // End	
      // Current
      $navigation .= "<li><span class=\"pagecur\">" . $cPage . "</span></li>";
      // End
      // Right
      for ($i = 1; $i <= $next_page; $i ++)
      {
        $pagenum = $cPage + $i;
        if (($pagenum > $cPage) && ($pagenum <= $totalPages))
        {
          $pLink = $root_link . "/{$p}-{$pagenum}{$extra}";
          $navigation .= "<li><a href='" . $pLink . "' class='" . $class . "'>" . $pagenum . "</a></li>";
        }
      }
      // End
      // Show Next page
      if ($cPage < $totalPages)
      {
        $numpage = $cPage + 1;
        $pLink = $root_link . "/{$p}-" . $numpage . "{$extra}";
        $navigation .= "<li><a href='" . $pLink . "' class='btnPage'><i class=\"fa fa-angle-right\"></i></a></li>";
      }
      // End		
      // Show Last page
      if ($cPage < ($totalPages - $pmore))
      {
        $pLink = $root_link . "/{$p}-" . $totalPages . "{$extra}";
        $navigation .= "<li><a href='" . $pLink . "' class='btnPage' ><i class=\"fa fa-angle-double-right\"></i></a></li>";
      }
      // End

      $navigation .= "</ul>";
    } // end if total pages is greater than one
    return $navigation;
  }


  // paginate
  function paginate_search ($root_link, $numRows, $maxRows, $extra = "", $cPage = 1, $p = "p", $pmore = 4, $class = "pagelink")
  {
    global $input, $vnT, $conf;
    $navigation = "";
    // get total pages
    $totalPages = ceil($numRows / $maxRows);
    $next_page = $pmore;
    $prev_page = $pmore;
    if ($cPage < $pmore) $next_page = $pmore + $pmore - $cPage;
    if ($totalPages - $cPage < $pmore) $prev_page = $pmore + $pmore - ($totalPages - $cPage);
    if ($totalPages > 1)
    {
      $navigation .= "<ul >";
      // Show first page
      if ($cPage > ($pmore + 1))
      {
        $pLink = $root_link . "/?{$p}=1{$extra}";
        $navigation .= "<li><a href='" . $pLink . "'  ><i class=\"fa fa-angle-double-left\"></i></a></li>";
      }
      // End
      // Show Prev page
      if ($cPage > 1)
      {
        $numpage = $cPage - 1;
        $pLink = $root_link . "/?{$p}=" . $numpage . "{$extra}";
        $navigation .= "<li><a href='" . $pLink . "' ><i class=\"fa fa-angle-left\"></i></a></li>";
      }
      // End
      // Left
      for ($i = $prev_page; $i >= 0; $i --)
      {
        $pagenum = $cPage - $i;
        if (($pagenum > 0) && ($pagenum < $cPage))
        {
          $pLink = $root_link . "/?{$p}={$pagenum}{$extra}";
          $navigation .= "<li><a href='" . $pLink . "' class='" . $class . "'>" . $pagenum . "</a></li>";
        }
      }
      // End
      // Current
      $navigation .= ' </li><li><span class="pagecur">'.$cPage.'</span>'  ;
      // End
      // Right
      for ($i = 1; $i <= $next_page; $i ++)
      {
        $pagenum = $cPage + $i;
        if (($pagenum > $cPage) && ($pagenum <= $totalPages))
        {
          $pLink = $root_link . "/?{$p}={$pagenum}{$extra}";
          $navigation .= "<li><a href='" . $pLink . "' class='" . $class . "'>" . $pagenum . "</a></li>";
        }
      }
      // End
      // Show Next page
      if ($cPage < $totalPages)
      {
        $numpage = $cPage + 1;
        $pLink = $root_link . "/?{$p}=" . $numpage . "{$extra}";
        $navigation .= "<li><a href='" . $pLink . "' class='btnPage'><i class=\"fa fa-angle-right\"></i></a></li>";
      }
      // End
      // Show Last page
      if ($cPage < ($totalPages - $pmore))
      {
        $pLink = $root_link . "/?{$p}=" . $totalPages . "{$extra}";
        $navigation .= "<li><a href='" . $pLink . "' class='btnPage' ><i class=\"fa fa-angle-double-right\"></i></a></li>";
      }
      // End

      $navigation .= "</ul>";

    } // end if total pages is greater than one
    return $navigation;
  }

  /**
   * Ham  include_libraries
   * @param	$filePath : duong dan toi file libraries
   * @ouput	: OK neu thanh cong
   */
  function include_libraries ($filePath, $base = "libraries", $key = 'libraries.')
  {
    global $vnT, $conf;
    static $paths;
    if (! isset($paths))
    {
      $paths = array();
    }

    $base = $keyPath = $key ? $key . $filePath : $filePath;
    if (! isset($paths[$keyPath]))
    {

      $rootDir = dirname(__FILE__);
      $rootDir = substr($rootDir, 0, strrpos($rootDir, DS));
      $base = $rootDir . DS . "libraries";

      $parts = explode('.', $filePath);
      $classname = array_pop($parts);
      $classname = ucfirst($classname);
      $path = str_replace('.', DS, $filePath);
      if (strpos($filePath, 'vntrust') === 0)
      {
        $classname = 'vnT_' . $classname;
        if (class_exists($classname))
        {
          return;
        } else
        {
          $rs = include ($base . DS . $path . '.php');
        }
      } else
      {
        $rs = include ($base . DS . $path . '.php');
      }
      $paths[$keyPath] = $rs;
    }
    return $paths[$keyPath];
  }

   
  /*--------------- doSendMail -----------*/
  function doSendMail ($mailto, $subject, $message, $mailfrom, $file_attach = "",$is_admin=1)
  {
    global $DB, $input, $conf, $vnT;

    $vnT->mailer->IsSMTP();
    $vnT->mailer->SMTPAuth =  ($vnT->conf['smtp_autentication']) ?  true : false ; // enable SMTP authentication

    switch ($vnT->conf['method_email'])
    {
      case "gmail":
        $vnT->mailer->SMTPSecure = ($vnT->conf['smtp_type_encryption']) ?  $vnT->conf['smtp_type_encryption'] : "tls"; // sets the prefix to the servier
        $vnT->mailer->Host = ($vnT->conf['smtp_host']) ?  $vnT->conf['smtp_host'] : "smtp.gmail.com"; // sets GMAIL as the SMTP server
        $vnT->mailer->Port = ($vnT->conf['smtp_port']) ?  $vnT->conf['smtp_port'] : 587; // set the SMTP port for the GMAIL server
        $vnT->mailer->Username = $vnT->conf['smtp_username']; // GMAIL username
        $vnT->mailer->Password = $vnT->conf['smtp_password']; // GMAIL password
        break;
      case "smtp":
        $vnT->mailer->Host = ($vnT->conf['smtp_host']) ?  $vnT->conf['smtp_host'] : "localhost";
        $vnT->mailer->SMTPSecure = ($vnT->conf['smtp_type_encryption']) ?  $vnT->conf['smtp_type_encryption'] : "tls"; // sets
        $vnT->mailer->Port = ($vnT->conf['smtp_port']) ?  $vnT->conf['smtp_port'] : 25;
        $vnT->mailer->Username = $vnT->conf['smtp_username'];
        $vnT->mailer->Password = $vnT->conf['smtp_password']; // Password E-mail
        break;
      default:
        $vnT->mailer->Mailer = "mail";
        break;
    }

    //$vnT->mailer->SMTPDebug = 2;
    //$vnT->mailer->Debugoutput = 'html';

    $vnT->mailer->CharSet = "utf-8";
    $vnT->mailer->IsHTML(true);


    //reset
    $vnT->mailer->clearAddresses();
    $vnT->mailer->clearCCs();
    $vnT->mailer->clearBCCs();
    $vnT->mailer->clearReplyTos();
    $vnT->mailer->clearAllRecipients();


    $arrFrom = explode(",", $mailfrom);
    if (strstr($mailfrom,";")){
      $arrFrom = explode(";", $mailfrom);
    }
    $mailFrom = $arrFrom[0];

    if($vnT->conf['smtp_from']) {
      $mailFrom = ($vnT->conf['from_email']) ? $vnT->conf['from_email'] : $vnT->conf['smtp_username'] ;
    }


    $vnT->mailer->From = $mailFrom;
    $vnT->mailer->FromName = ($vnT->conf['from_name']) ? $vnT->conf['from_name'] : $arrFrom[0];
    $vnT->mailer->addReplyTo( $arrFrom[0]);

    //xu ly nhieu email
    $arrTo = array();
    $arr1 = explode(",", $mailto);
    foreach ($arr1 as $valTo){
      if (strstr($valTo,";")){
        $arr2 = explode(";", trim($valTo));
        foreach ($arr2 as $val){
          $arrTo[] = trim($val) ;
        }
      }else{
        $arrTo[] = trim($valTo) ;
      }
    }
    for ($i = 0; $i < count($arrTo); $i ++)
    {
      $email_to = trim($arrTo[$i]) ;
      if ($i == 0)
      {
        $vnT->mailer->addAddress($email_to);
      }
      else{

        $vnT->mailer->addCC($email_to);
      }
    }

    $vnT->mailer->Subject = $subject;
    //$vnT->mailer->Body = $message;
    $vnT->mailer->msgHTML($message);

    if (! empty($file_attach))
    {
      if(is_array($file_attach))
      {
        foreach ($file_attach as $file_a){
          $vnT->mailer->addAttachment($file_a, "{$file_a}");
        }
      }else{
        $vnT->mailer->addAttachment($file_attach, "{$file_attach}");
      }
    }
    $sent = $vnT->mailer->Send();
    return $sent;
  }

  /*--------------- html_redirect  -----------*/
  function html_redirect ($url, $mess, $time_ref = 1)
  {
    global $conf, $vnT;
    $data['url'] = $url;
    $data['mess'] = $mess;
    $data['mess_redirect'] = "<a href='{$url}'>" . $vnT->lang['global']['mess_redirect'] . "</a>";
    $data['host_name'] = $_SERVER['HTTP_HOST'];
    $data['time_ref'] = $time_ref;
    flush();
    echo $vnT->skin_box->parse_box("box_redirect", $data);
    exit();
  }
  /*--------------- header_redirect  -----------*/
  function header_redirect ($url, $mess="" )
  {
    $url = str_replace('&amp;', '&', $url);
    if($mess) $_SESSION['mess'] = $mess;

    @header("Location: {$url}");
    exit();
  }
  /*------------------------------------*/
  function html_err ($err,$class="")
  {
    global $conf, $vnT;
    if($class=='jAlert'){
      $text ="<script> jAlert('".$err."','". $vnT->lang['global']['error'] ."');	</script>";
    }else{
      $text ='<div class="alert alert-danger'.$class.'"><a href="#" class="close" data-dismiss="alert">&times;</a><strong>' . $vnT->lang['global']['error'] . ' :</strong><div>' . $err . '</div></div>';
    }

    return $text ;
  }
//-------------  html_mess ----------------------
  function html_mess ($mess,$class='')
  {
    global $conf, $vnT;
    if($class=='jAlert'){
      $text ="<script> jAlert('".$mess."','". $vnT->lang['global']['announce'] ."');	</script>";
    }else{
      $text = '<div class="alert alert-success'.$class.'" role="alert"><a href="#" class="close" data-dismiss="alert">&times;</a><strong>' . $vnT->lang['global']['announce'] . ' :</strong><div>' . $mess . '</div></div>';
    }
    return $text ;
  }
//-------------  display_error_message ----------------------
  function display_error_message ($mess='' )
  {
    global $vnT;
    if(isset($_SESSION['mess']) && $_SESSION['mess']!=''){
      $mess = 	$_SESSION['mess'];
      unset($_SESSION['mess']);
    }
    return $mess ;
  }

  //-------------  fetch_array ----------------------
  function fetch_array ($arr_str)
  {
    global $conf, $DB, $vnT;
    $tmp = unserialize($arr_str);
    $out = $tmp[$vnT->lang_name];
    return $out;
  }

  /**
   * read_static_cache
   *
   * @params  string  $cache_name
   *
   * @return  array   $data
   */
  function read_static_cache($cache_name)
  {
    global $conf, $vnT;

    static $result = array();
    if (!empty($result[$cache_name]))
    {
      return $result[$cache_name];
    }
    $cache_file_path = $vnT->conf['rootpath'] . 'cache/static_caches/' . $cache_name . '.php';
    if (file_exists($cache_file_path))
    {
      include_once($cache_file_path);
      $result[$cache_name] = $cache_data;
      return $result[$cache_name];
    }
    else
    {
      return false;
    }
  }

  /**
   * write_static_cache
   *
   * @params  string  $cache_name
   * @params  string  $caches
   *
   * @return
   */
  function write_static_cache($cache_name, $caches)
  {
    global $conf, $vnT;

    $cache_file_path = $vnT->conf['rootpath'] . 'cache/static_caches/' . $cache_name . '.php';
    $content = "<?php\r\n";
    $content .= "\$cache_data = " . var_export($caches, true) . ";\r\n";
    $content .= "?>";
    @file_put_contents($cache_file_path, $content, LOCK_EX);
  }

  //------------------------------ create_folder ---------------------
  function create_folder ($path_upload, $dir="")
  {
    global $input, $conf, $vnT, $DB;
    $text = "";

    if($dir=="")
      $dir = date("m_Y");
    $dir_thumbs = $dir."/thumbs";
    $path_dir = $path_upload.$dir;
    $path_thumb_dir = $path_upload.$dir_thumbs;

    if(!is_dir($path_dir))	{
      mkdir($path_dir,0777);
      chmod($path_dir,0777);
    }
    return $dir;
  }

  /**
   * @function : vnt_mkdir
   * @param 		:
   * @return		:
   */

  function vnt_mkdir( $path  , $dir_name ,$thumb=0 )
  {
    global $vnT;
    $path_dir =  $path ."/". $dir_name ;
    $arr_mess = array();

    if( ! is_dir( $path_dir ) )
    {

      if (ini_get('safe_mode'))// safe_mode = On
      {
        if($vnT->conf['ftp_enable'])
        {
          $vnT->func->include_libraries('vntrust.myftp.ftp');
          $myFTP = vnT_FTP::getInstance($vnT->conf['ftp_host'],$vnT->conf['ftp_port'],"",$vnT->conf['ftp_user'],$vnT->conf['ftp_pass']);

          if($myFTP->isConnected() )
          {
            $path_folder = $vnT->conf['ftp_path'] ."/".str_replace( $vnT->conf['rootpath'] , '', str_replace( '\\', '/', $path_dir ) );
            $ok =$myFTP->mkdir($path_folder) ;
            if($ok) {
              $myFTP->chmod($path_folder,"0777") ;

              //thumbs
              if($thumb){
                $path_folder_thumb = $path_folder ."/thumbs" ;
                $myFTP->mkdir($path_folder_thumb) ;
                $myFTP->chmod($path_folder_thumb,"0777") ;
              }

              @file_put_contents( $path_dir . '/index.html', '' );

              $arr_mess = array( 1, "Tao thanh cong thu muc ". $path_dir );
            }else{
              $arr_mess = array( 0, "Khong tao duoc folder" );
            }
          }else{
            $arr_mess = array( 0, "Khong connect duoc FTP" );
          }
        }else{
          $arr_mess = array( 0, "Chua cau hinh FTP" );
        }

      }else{

        if( ! is_writable( $path ) ){
          @chmod( $path, 0777 );
        }
        if( ! is_writable( $path ) ) return array( 0,  "Thu muc ".$path." khong the ghi Vui long Chmod 777 " );

        $oldumask = @umask( 0 );
        $res = @mkdir( $path_dir );
        @exec("chmod 777 {$path_dir}");
        @umask( $oldumask );
        if( ! $res ) return array( 0, "Tao thu muc ".$path_dir." that bai " );
        @file_put_contents( $path_dir . '/index.html', '' );

        if($thumb)
        {
          $dir_thumbs = $dir_name."/thumbs";
          $path_dir_thumbs = $path ."/". $dir_name."/thumbs";
          @mkdir( $path_dir_thumbs );
          @chmod( $path_dir_thumbs , 0777);
          @exec("chmod 777 {$path_dir}");
          @file_put_contents( $path_dir_thumbs . '/index.html', '' );
        }

        $arr_mess = array( 1, "Tao thanh cong thu muc ". $path_dir );
      }
    }else{
      $arr_mess = array( 1, "Thu muc ". $path_dir ." da ton tai" );
    }
    return $arr_mess ;
  }

  //------get_dir_upload_module
  function get_dir_upload_module($module,$folder_type=1) {
    global $vnT,$func,$DB,$conf;

    $MOD_PATH_UPLOAD = $vnT->conf['rootpath'].'vnt_upload/'.$module ;
    $dir= "";
    //check parentid
    $res_ck = $vnT->DB->query("SELECT folder_id FROM media_folders WHERE folder_name='".$module."'");
    if($row_ck = $vnT->DB->fetch_row($res_ck))
    {
      $parentid  = (int)$row_ck['folder_id'];
    }else{
      $cot['parentid']	=0;
      $cot['folder_path']	= $module;
      $cot['folder_name']	=	$module;
      $cot['date_create']	=	time();
      $cot['folder_type'] = "module";
      $vnT->DB->do_insert("media_folders",$cot);
      $parentid  = $vnT->DB->insertid();
    }

    // tao thu muc
    switch ($folder_type)
    {
      case 1 :
        $dir = date("m_Y");
        $res_dir = $vnT->func->vnt_mkdir($MOD_PATH_UPLOAD , $dir,1);
        if($res_dir[0]==1)
        {
          $res_ck = $vnT->DB->query("SELECT folder_id FROM media_folders WHERE parentid=".$parentid." AND folder_name='".$dir."'");
          if(!$vnT->DB->fetch_row($res_ck))
          {
            $cot['parentid'] = $parentid;
            $cot['folder_path'] = $module."/".$dir;
            $cot['folder_name'] = $dir;
            $cot['date_create'] = time();
            $vnT->DB->do_insert("media_folders",$cot);
          }
        }
        break ;

      case 2 :
        $dir = date("d_m_Y");
        $res_dir = $vnT->func->vnt_mkdir($MOD_PATH_UPLOAD , $dir,1);
        if($res_dir[0]==1)
        {
          $res_ck = $vnT->DB->query("SELECT folder_id FROM media_folders WHERE parentid=".$parentid."  AND folder_name='".$dir."'");
          if(!$vnT->DB->fetch_row($res_ck))
          {
            $cot['parentid'] = $parentid;
            $cot['folder_path'] = $module."/".$dir;
            $cot['folder_name'] = $dir;
            $cot['date_create'] = time();
            $vnT->DB->do_insert("media_folders",$cot);
          }
        }
        break ;
      case 3 :
        $dir_thang = date("m_Y");
        $res_dir = $vnT->func->vnt_mkdir($MOD_PATH_UPLOAD , $dir_thang);
        if($res_dir[0]==1)
        {
          $res_ck = $vnT->DB->query("SELECT folder_id FROM media_folders WHERE parentid=".$parentid."  AND folder_name='".$dir_thang."'");
          if($row_ck = $vnT->DB->fetch_row($res_ck))
          {
            $parent_thang  = (int)$row_ck['folder_id'];
          }else{
            $cot['parentid'] = $parentid;
            $cot['folder_path'] = $module."/".$dir_thang;
            $cot['folder_name'] = $dir_thang;
            $cot['date_create'] = time();
            $vnT->DB->do_insert("media_folders",$cot);
            $parent_thang = $vnT->DB->insertid();
          }

          $dir_day = date("d") ;
          $res_dir1 = $vnT->func->vnt_mkdir($MOD_PATH_UPLOAD."/".$dir_thang , $dir_day,1);
          if($res_dir1[0]==1)
          {
            $res_ck = $vnT->DB->query("SELECT folder_id FROM media_folders WHERE parentid=".$parent_thang."  AND folder_name='".$dir_day."'");
            if(!$vnT->DB->fetch_row($res_ck))
            {
              $cot['parentid'] = $parent_thang;
              $cot['folder_path'] = $module."/".$dir_thang."/".$dir_day;
              $cot['folder_name'] = $dir_day;
              $cot['date_create'] = time();
              $vnT->DB->do_insert("media_folders",$cot);
            }
          }

        }

        $dir = $dir_thang."/".$dir_day;

        break ;
      case 4 :
        $dir_nam = date("Y");
        $res_dir = $vnT->func->vnt_mkdir($MOD_PATH_UPLOAD , $dir_nam);
        if($res_dir[0]==1)
        {
          $res_ck = $vnT->DB->query("SELECT folder_id FROM media_folders WHERE parentid=".$parentid."  AND folder_name='".$dir_nam."'");
          if($row_ck = $vnT->DB->fetch_row($res_ck))
          {
            $parent_nam  = (int)$row_ck['folder_id'];
          }else{
            $cot['parentid'] = $parentid;
            $cot['folder_path'] = $module."/".$dir_nam;
            $cot['folder_name'] = $dir_nam;
            $cot['date_create'] = time();
            $vnT->DB->do_insert("media_folders",$cot);
            $parent_nam = $vnT->DB->insertid();
          }

          $dir_thang = date("m") ;
          $res_dir1 = $vnT->func->vnt_mkdir($MOD_PATH_UPLOAD."/".$dir_nam , $dir_thang,1);
          if($res_dir1[0]==1)
          {
            $res_ck = $vnT->DB->query("SELECT folder_id FROM media_folders WHERE parentid=".$parent_nam."  AND folder_name='".$dir_thang."'");
            if(!$vnT->DB->fetch_row($res_ck))
            {
              $cot['parentid'] = $parent_nam;
              $cot['folder_path'] = $module."/".$dir_nam."/".$dir_thang;
              $cot['folder_name'] = $dir_thang;
              $cot['date_create'] = time();
              $vnT->DB->do_insert("media_folders",$cot);
            }
          }

        }

        $dir = $dir_nam."/".$dir_thang;

        break ;

      default :
        if($folder_type){
          $dir = $folder_type ;
          $res_dir = $vnT->func->vnt_mkdir($MOD_PATH_UPLOAD , $dir,1);
          if($res_dir[0]==1)
          {
            $res_ck = $vnT->DB->query("SELECT folder_id FROM media_folders WHERE parentid=".$parentid." AND folder_name='".$dir."'");
            if(!$vnT->DB->fetch_row($res_ck))
            {
              $cot['parentid'] = $parentid;
              $cot['folder_path'] = $module."/".$dir;
              $cot['folder_name'] = $dir;
              $cot['date_create'] = time();
              $vnT->DB->do_insert("media_folders",$cot);
            }
          }
        }else{
           $dir = '' ;
        }    
        
        break ;

    } // end switch

    return $dir;
  }

  /**
   * thumbs
   *
   * @params  string
   * @params  string
   *
   * @return
   */
  function thumbs ($imgfile = "", $path, $maxWidth, $maxHeight="",$crop=0, $arr_more=array())
  {
    global $vnT ;

    $info = @getimagesize($imgfile);
    $mime = $info[2];
    $fext = ($mime == 1 ? 'image/gif' : ($mime == 2 ? 'image/jpeg' : ($mime == 3 ? 'image/png' : NULL)));
    switch ($fext)
    {
      case 'image/jpeg':
        if (! function_exists('imagecreatefromjpeg'))
        {
          die('No create from JPEG support');
        } else
        {
          $img['src'] = @imagecreatefromjpeg($imgfile);
        }
        break;
      case 'image/png':
        if (! function_exists('imagecreatefrompng'))
        {
          die("No create from PNG support");
        } else
        {
          $img['src'] = @imagecreatefrompng($imgfile);
        }
        break;
      case 'image/gif':
        if (! function_exists('imagecreatefromgif'))
        {
          die("No create from GIF support");
        } else
        {
          $img['src'] = @imagecreatefromgif($imgfile);
        }
        break;
    }
    $img['old_w'] = @imagesx($img['src']);
    $img['old_h'] = @imagesy($img['src']);

    if($crop){
      // Ratio cropping
      $offsetX	= 0;
      $offsetY	= 0;

      if (!$maxWidth && $maxHeight)	{
        $maxWidth	= 99999999999999;
      }elseif ($maxWidth && !$maxHeight){
        $maxHeight	= 99999999999999;
      }

      $cropRatio		= explode(':', (string) $crop );
      if (count($cropRatio) == 2)
      {
        $ratioComputed		= $img['old_w'] /  $img['old_h'];
        $cropRatioComputed	= (float) $cropRatio[0] / (float) $cropRatio[1];

        if ($ratioComputed < $cropRatioComputed)
        { // Image is too tall so we will crop the top and bottom
          $origHeight	= $img['old_h'];
          $img['old_h']		= $img['old_w'] / $cropRatioComputed;
          $offsetY	= ($origHeight - $img['old_h']) / 2;
        }
        else if ($ratioComputed > $cropRatioComputed)
        { // Image is too wide so we will crop off the left and right sides
          $origWidth	= $img['old_w'];
          $img['old_w']		= $img['old_h'] * $cropRatioComputed;
          $offsetX	= ($origWidth - $img['old_w']) / 2;
        }
      }

      $xRatio		= $maxWidth / $img['old_w'];
      $yRatio		= $maxHeight / $img['old_h'];

      if ($xRatio * $height < $maxHeight)
      { // Resize the image based on width
        $new_h	= ceil($xRatio * $img['old_h']);
        $new_w	= $maxWidth;
      }
      else // Resize the image based on height
      {
        $new_w	= ceil($yRatio * $img['old_w']);
        $new_h	= $maxHeight;
      }

    }else{
      $new_h = $img['old_h'];
      $new_w = $img['old_w'];
      $offsetX=0;
      $offsetY = 0 ;

      if($arr_more["fix_width"]==1)
      {
        $new_w = $maxWidth;
        $new_h = ($maxWidth / $img['old_w']) * $img['old_h'];
      }elseif($arr_more["fix_height"]==1)	{
        $new_h = $maxHeight;
        $new_w = ($maxHeight / $img['old_h']) * $img['old_w'];
      }elseif($arr_more["fix_min"]==1)
      {
        if ($img['old_w'] > $img['old_h'])
        {
          $new_h = $maxHeight;
          $new_w = ($maxHeight / $img['old_h']) * $img['old_w'];

          if($new_w < $maxWidth)
          {
            $new_w = $maxWidth;
            $new_h = ($maxWidth / $img['old_w']) * $img['old_h'];
          }
        }
        else
        {
          $new_w = $maxWidth;
          $new_h = ($maxWidth / $img['old_w']) * $img['old_h'];

          if($new_h < $maxHeight)
          {
            $new_h = $maxHeight;
            $new_w = ($maxHeight / $img['old_h']) * $img['old_w'];
          }
        }
      }
      elseif($arr_more["fix_max"]==1)
      {
        if($maxWidth > 0 && $maxHeight > 0)
        {
          $tl = $img['old_w'] / $img['old_h'];
          $tl_get = $maxWidth / $maxHeight;

          if ($tl > $tl_get)
          {
            if ($img['old_w'] > $maxWidth)
            {
              $new_w = $maxWidth;
              $new_h = ($maxWidth / $img['old_w']) * $img['old_h'];
            }
          }
          else
          {
            if ($img['old_h'] > $maxHeight)
            {
              $new_h = $maxHeight;
              $new_w = ($new_h / $img['old_h']) * $img['old_w'];
            }
          }
        }
      }
      elseif($arr_more["zoom_max"]==1)
      {
        $tl = $img['old_w'] / $img['old_h'];
        $tl_get = $maxWidth / $maxHeight;

        if ($tl_get > $tl)
        {
          $new_h = $maxHeight;
          $new_w = ($maxHeight / $img['old_h']) * $img['old_w'];
        }
        else
        {
          $new_w = $maxWidth;
          $new_h = ($maxWidth / $img['old_w']) * $img['old_h'];
        }
      }
      else
      {
        if($maxWidth && $maxHeight)
        {
          $new_w = $maxWidth;
          $new_h = $maxHeight;
        }else{
          $new_h = $img['old_h'];
          $new_w = $img['old_w'];
          $offsetX=0;
          $offsetY = 0 ;

          if ($img['old_w'] > $maxWidth)
          {
            $new_w = $maxWidth;
            $new_h = ($maxWidth / $img['old_w']) * $img['old_h'];
          }
          if ($new_h > $maxWidth)
          {
            $new_h = $maxWidth;
            $new_w = ($new_h / $img['old_h']) * $img['old_w'];
          }
        }
      }
    }

    $img['des'] = @imagecreatetruecolor($new_w, $new_h);
    if($fext=="image/png"){
      @imagealphablending($img['des'], false );
      @imagesavealpha($img['des'], true);
    }else{
      $white = @imagecolorallocate($img['des'], 255, 255, 255);
      @imagefill($img['des'], 1, 1, $white);
    }
    @imagecopyresampled($img['des'], $img['src'], 0, 0, $offsetX, $offsetY, $new_w, $new_h, $img['old_w'], $img['old_h']);
    if($arr_more['watermark']){

      $info_watermark['image_watermark'] = $arr_more['watermark'];
      $info_watermark['image_watermark_no_zoom_out'] = true;
      $info_watermark['image_watermark_no_zoom_in']  = true;
      $info_watermark['image_watermark_position'] = ($arr_more['watermark_position']) ? $arr_more['watermark_position'] : "TL";
      $info_watermark['image_watermark_x'] = ($arr_more['image_watermark_x']) ? $arr_more['image_watermark_x'] : "";
      $info_watermark['image_watermark_y'] = ($arr_more['image_watermark_y']) ? $arr_more['image_watermark_y'] : "";
      $info_watermark['image_dst'] = $img['des'] ;
      $info_watermark['image_dst_x'] = $new_w;
      $info_watermark['image_dst_y'] = $new_h;
      $this->add_watermark($info_watermark);
    }
    //	print "path = ".$path."<br>";
    @touch($path);
    $quality = ($vnT->conf['pic_quality']) ? $vnT->conf['pic_quality'] : 75 ;
    switch ($fext)
    {
      case 'image/pjpeg':
      case 'image/jpeg':
      case 'image/jpg':
        @imagejpeg($img['des'], $path, $quality);
        break;
      case 'image/png':
        @imagepng($img['des'], $path);
        break;
      case 'image/gif':
        @imagegif($img['des'], $path, $quality);
        break;
    }
    // Finally, we destroy the images in memory.
    @imagedestroy($img['des']);
  }

  /*-------------- add_watermark --------------------*/
  function add_watermark ($info)
  {
    // add watermark image
    if (@file_exists($info['image_watermark'])) {
      $info['image_watermark_position'] = strtolower($info['image_watermark_position']);
      $watermark_info = getimagesize($info['image_watermark']);
      $watermark_type = (array_key_exists(2, $watermark_info) ? $watermark_info[2] : null); // 1 = GIF, 2 = JPG, 3 = PNG
      $filter = @imagecreatefrompng($info['image_watermark']);
      $watermark_checked = ($filter)  ? true : false ;
      if ($watermark_checked) {
        $watermark_dst_width  = $watermark_src_width  = @imagesx($filter);
        $watermark_dst_height = $watermark_src_height = @imagesy($filter);

        // if watermark is too large/tall, resize it first
        if ((!$info['image_watermark_no_zoom_out'] && ($watermark_dst_width > $info['image_dst_x'] || $watermark_dst_height > $info['image_dst_y']))
          || (!$info['image_watermark_no_zoom_in'] && $watermark_dst_width < $info['image_dst_x'] && $watermark_dst_height < $info['image_dst_y'])) {
          $canvas_width  = $info['image_dst_x'] - abs($info['image_watermark_x']);
          $canvas_height = $info['image_dst_y'] - abs($info['image_watermark_y']);
          if (($watermark_src_width/$canvas_width) > ($watermark_src_height/$canvas_height)) {
            $watermark_dst_width = $canvas_width;
            $watermark_dst_height = intval($watermark_src_height*($canvas_width / $watermark_src_width));
          } else {
            $watermark_dst_height = $canvas_height;
            $watermark_dst_width = intval($watermark_src_width*($canvas_height / $watermark_src_height));
          }
        }
        // determine watermark position
        $watermark_x = 0;
        $watermark_y = 0;
        if (is_numeric($info['image_watermark_x'])) {
          if ($info['image_watermark_x'] < 0) {
            $watermark_x = $info['image_dst_x'] - $watermark_dst_width + $info['image_watermark_x'];
          } else {
            $watermark_x = $info['image_watermark_x'];
          }
        } else {
          if (strpos($info['image_watermark_position'], 'r') !== false) {
            $watermark_x = $info['image_dst_x'] - $watermark_dst_width;
          } else if (strpos($info['image_watermark_position'], 'l') !== false) {
            $watermark_x = 0;
          } else {
            $watermark_x = ( $info['image_dst_x'] - $watermark_dst_width) / 2;
          }
        }
        if (is_numeric($info['image_watermark_y'])) {
          if ($info['image_watermark_y'] < 0) {
            $watermark_y = $info['image_dst_y'] - $watermark_dst_height + $info['image_watermark_y'];
          } else {
            $watermark_y = $info['image_watermark_y'];
          }
        } else {
          if (strpos($info['image_watermark_position'], 'b') !== false) {
            $watermark_y = $info['image_dst_y'] - $watermark_dst_height;
          } else if (strpos($info['image_watermark_position'], 't') !== false) {
            $watermark_y = 0;
          } else {
            $watermark_y = ($info['image_dst_y'] - $watermark_dst_height) / 2;
          }
        }
        @imagealphablending($info['image_dst'], true);
        @imagecopyresampled($info['image_dst'], $filter, $watermark_x, $watermark_y, 0, 0, $watermark_dst_width, $watermark_dst_height, $watermark_src_width, $watermark_src_height);
      }
    }
  }

  /*-------------- get_pic_modules --------------------*/
  function get_pic_modules ($picture, $w = "", $h = "", $ext="",$thumb=1 ,$crop=0, $arr_more=array())
  {
    global $vnT,$func,$conf;
    $out = "";
    $overwrite = ($arr_more['overwrite']) ? $arr_more['overwrite'] : 0 ;

    if($overwrite){
      $pre = ""  ;
    }else{
      $pre = $w ;
      if($h)  {
        $pre = "(".$w."x".$h.")" ;
      }
      if($arr_more['fix_min']) {
        $pre .= "_fm" ;
      }
      if($arr_more['fix_max']) {
        $pre .= "_fmax" ;
      }
      if($arr_more['fix_width']) {
        $pre .= "_fw" ;
      }
      if($arr_more['fix_height']) {
        $pre .= "_fh" ;
      }
      if($arr_more['zoom_max']) {
        $pre .= "_zmax" ;
      }
      if($crop) {
        $pre .= "_crop" ;
      }
      $pre.="_";
    }

    $linkhinh = "vnt_upload/".$picture;
    $linkhinh = str_replace("//","/",$linkhinh);
    $dir = substr($linkhinh,0,strrpos($linkhinh,"/"));
    $pic_name = substr($linkhinh,strrpos($linkhinh,"/")+1) ;

    if ($w)
    {
      if($thumb && file_exists($vnT->conf['rootpath'] . $linkhinh)){
        $file_thumbs = $dir . "/thumbs/{$pre}_" . substr($linkhinh, strrpos($linkhinh, "/") + 1);
        $linkhinhthumbs = $vnT->conf['rootpath'] . $file_thumbs;
        if (! file_exists($linkhinhthumbs)) {
          if (@is_dir($vnT->conf['rootpath'] . $dir . "/thumbs")) {
            @chmod($vnT->conf['rootpath'] . $dir . "/thumbs", 0777);
          } else {
            @mkdir($vnT->conf['rootpath'] . $dir . "/thumbs", 0777);
            @chmod($vnT->conf['rootpath'] . $dir . "/thumbs", 0777);
          }
          // thum hinh
          $vnT->func->thumbs($vnT->conf['rootpath'] . $linkhinh, $linkhinhthumbs, $w, $h, $crop, $arr_more);
        }
        $src =  $conf['rooturl'] . $file_thumbs;
      }else{
        $src = $conf['rooturl'] . $dir ."/thumbs/".$pic_name;
      }
    } else {
      $src = $conf['rooturl'] . 'vnt_upload/' . $picture;
    }

    $alt = substr($pic_name, 0, strrpos($pic_name, "."));
    $out = "<img  src=\"{$src}\"  {$ext} />";
    return $out;
  }

  /*-------------- get_src_modules --------------------*/
  function get_src_modules ($picture, $w = "", $h = "",$thumb=1 ,$crop=0, $arr_more=array())
  {
    global $vnT,$func,$conf;
    $out = "";
    $overwrite = ($arr_more['overwrite']) ? $arr_more['overwrite'] : 0 ;

    if($overwrite){
      $pre = ""  ;
    }else{
      $pre = $w ;
      if($h)  {
        $pre = "(".$w."x".$h.")" ;
      }
      if($arr_more['fix_min']) {
        $pre .= "_fm" ;
      }
      if($arr_more['fix_max']) {
        $pre .= "_fmax" ;
      }
      if($arr_more['fix_width']) {
        $pre .= "_fw" ;
      }
      if($arr_more['fix_height']) {
        $pre .= "_fh" ;
      }
      if($arr_more['zoom_max']) {
        $pre .= "_zmax" ;
      }
      if($crop) {
        $pre .= "_crop" ;
      }
      $pre.="_";
    }


    $linkhinh = "vnt_upload/".$picture;
    $linkhinh = str_replace("//","/",$linkhinh);
    $dir = substr($linkhinh,0,strrpos($linkhinh,"/"));
    $pic_name = substr($linkhinh,strrpos($linkhinh,"/")+1) ;

    if ($w)
    {
      if($thumb && file_exists($vnT->conf['rootpath'] . $linkhinh)){
        $file_thumbs = $dir . "/thumbs/{$pre}" . substr($linkhinh, strrpos($linkhinh, "/") + 1);
        $linkhinhthumbs = $vnT->conf['rootpath'] . $file_thumbs;
        if (! file_exists($linkhinhthumbs) || $overwrite ==1 ) {
          if (@is_dir($vnT->conf['rootpath'] . $dir . "/thumbs")) {
            @chmod($vnT->conf['rootpath'] . $dir . "/thumbs", 0777);
          } else {
            @mkdir($vnT->conf['rootpath'] . $dir . "/thumbs", 0777);
            @chmod($vnT->conf['rootpath'] . $dir . "/thumbs", 0777);
          }
          // thum hinh
          $vnT->func->thumbs($vnT->conf['rootpath'] . $linkhinh, $linkhinhthumbs, $w, $h, $crop, $arr_more);
        }
        $src =  $conf['rooturl'] . $file_thumbs;
      }else{
        $src = $conf['rooturl'] . $dir ."/thumbs/".$pic_name;
      }

    } else {
      $src = $conf['rooturl'] . 'vnt_upload/' . $picture;
    }

    return $src;
  }


  /*-------------- get_price_format --------------------*/
  function get_price_format ($price, $default="", $unit="đ" ,$rate =0){
    global $func,$DB,$conf,$vnT;

    if($default == "")
      $default = $vnT->lang["global"]["call"];

    if ($price){

      if($rate){
        $price = $price / $rate;
      }

      $nguyen = (int) $price;
      $dot =strpos($price,".");
      if ($dot)
        $du = substr ($price,strpos($price,"."),3);
      else $du = "";
      $price = $func->format_number($nguyen).$du ." ".$unit ;
    }else{
      $price = $default;
    }
    return $price;
  }

  /**
   * function get_text_time_remain ()
   *
   **/
  function get_text_time_remain ($time )
  {
    global $vnT;
    $secs = $time % 60;
    $mins = (int)($time / 60) % 60;
    $hours = (int)($time / 3600) % 60;
    $out = $hours ." : ". $mins . " : " . $secs;
    return $out;
  }


   /*-------------- get_list_tags --------------------*/
  function get_list_tags ($module,$tags , $type_show=0) {
    global $vnT, $func, $DB, $conf;
    $text = ""; $arr_item = array();
    $sql = "SELECT *
          FROM {$module}_tags
          WHERE display=1  
          AND FIND_IN_SET(tag_id,'".$tags."')>0 
          ORDER BY name ASC, date_post DESC";
    //echo $sql;
    $query = $vnT->DB->query($sql);
    if(($num = $vnT->DB->num_rows($query))){
      $i=0;
      while($row = $vnT->DB->fetch_row($query)){
        $text .= '<span><a href="'.  $vnT->link_root . $vnT->setting['seo_name'][$vnT->lang_name][$module] .'/tags/'.$row['tag_id']."/".$vnT->func->make_url ($row['name']).'.html" >'.$row["name"].'</a></span>';
        $arr_item[] =  array('id'=> $row['tag_id'] , 'name' => $row['name'] );
      }
    }

    if($type_show==0){
      $out = $text ;
    }else{
      $out = $arr_item ;
    }
    return $out;
  }

  /*-------------- get_tag_name --------------------*/
  function get_tag_name ($module,$tagID) {
    global $vnT, $func, $DB, $conf;
    $text = "";

    $query = $vnT->DB->query("SELECT name	FROM {$module}_tags	WHERE tag_id=".$tagID);
    if($row = $vnT->DB->fetch_row($query)){
      $text =  $vnT->func->HTML($row['name']);
    }
    return $text;
  }

 

  /**
   * function getPlugins ()
   *
   **/
  function getPlugins ($script,$css) {
    global  $vnT, $input;
    if ($css) {
      $_css_ = explode(',',$css);
      foreach ($_css_ as $key=>$value) {
        $vnT->html->addStyleSheet($vnT->dir_js . "/" . $value);
      }
    }
    if ($script) {
      $_script_ = explode(',',$script);
      foreach ($_script_ as $key=>$value) {
        $vnT->html->addScript($vnT->dir_js . "/" . $value);
      }
    }
  }
// end class
}
?>