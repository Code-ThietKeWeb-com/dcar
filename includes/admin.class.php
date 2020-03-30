<?php
/*================================================================================*\
|| 							Name code : admin.class.php 		 			      ||
||  				Copyright © 2007 by Thai Son - CMS vnTRUST                    ||
\*================================================================================*/
/**
 * @version : 1.0
 * @date upgrade : 11/12/2007 by Thai Son
 **/
if (! defined('IN_vnT')) {
  die('Hacking attempt!');
}

class Func_Admin extends Func_Global
{

  //check_gpc
  function Func_Admin ()
  {
    if (version_compare(PHP_VERSION, '5.4.0', '<')) {
      $this->check_gpc();
    }
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
 	function vnt_set_auth_cookie($admin_id, $remember = false , $ext="" )
	{
		global $vnT, $func, $DB, $conf;	
		 
		if ( $remember ) {
			$expiration = $expire = time() + 1209600; // 14 ngay
		} else {
			$expiration = time() + 172800;
			$expire = 0;
		}
 	
		$auth_cookie = $this->vnt_generate_auth_cookie($admin_id );
  	
		// Set httponly if the php version is >= 5.2.0
		if ( version_compare(phpversion(), '5.2.0', 'ge') ) {
      $secure = false;
      $httponly  = true;
      if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') {
        $secure = true ;
      }
      @setcookie(AUTH_COOKIE, $auth_cookie, $expire, ADMIN_COOKIE_PATH, COOKIE_DOMAIN, $secure, $httponly);
			
		} else {
			$cookie_domain = COOKIE_DOMAIN;
			if ( !empty($cookie_domain) )
				$cookie_domain .= '; HttpOnly';
			setcookie(AUTH_COOKIE, $auth_cookie, $expire, ADMIN_COOKIE_PATH, $cookie_domain);
		}
 
	}
 

	 /**
	 * Removes all of the cookies associated with authentication.
	 *
	 * @since 2.5
	 */
	function vnt_clear_auth_cookie() {
		global $vnT, $func, $DB, $conf;
    $secure = false;
    $httponly  = true;
    if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') {
      $secure = true ;
    }
		setcookie(AUTH_COOKIE, ' ', time() - 31536000, ADMIN_COOKIE_PATH, COOKIE_DOMAIN, $secure, $httponly);
	}
	
	
	/**
 * Generate authentication cookie contents.
 *
 * @since 2.5
 * @uses apply_filters() Calls 'auth_cookie' hook on $cookie contents, User ID
 *		and expiration of cookie.
 *
 * @param int $admin_id User ID
 * @param int $expiration Cookie expiration in seconds
 * @param string $scheme Optional. The cookie scheme to use: auth, secure_auth, or logged_in
 * @return string Authentication cookie contents
 */
	function vnt_generate_auth_cookie($admin_id) {
		global $func, $DB, $conf;		
		$info = $this->get_admininfo($admin_id);
		$hash = md5($info['adminid'] . '|' . $info['password']);  		
		$auth_cookie = base64_encode($info['username']) . '|' . $hash . '|' . $_SESSION['ses_admin'] ;
		return $auth_cookie ;  
	}	
	
	
	/**
   * function get_admininfo()
   * @param	admin_id
   * @return	array
   */
	function get_admininfo($admin_id) {
		global $vnT, $func, $DB, $conf;
		
		 $result = $DB->query("SELECT a.*, g.title,g.permission 
														FROM admin a left join admin_group g
														ON a.level = g.gid
														WHERE adminid='" . $admin_id . "' " 
													);
		 $info = $DB->fetch_row($result) ;
		 return $info ;
	}

  /**
   * Unicode-safe version of htmlspecialchars()
   * @param	string	Text to be made html-safe
   * @return	string
   */
  function htmlspecialchars_uni ($text, $entities = true)
  {
    return str_replace(// replace special html characters
		array('<' ,'>' ,'"'), array('&lt;' ,'&gt;' ,'&quot;'), preg_replace(// translates all non-unicode entities
'/&(?!' . ($entities ? '#[0-9]+' : '(#[0-9]+|[a-z]+)') . ';)/si', '&amp;', $text));
  }

  /**
   * @function : writeDbConfig
   * @param    : $configName  -> ten  config  name
   *							$new -> mang lang moi
   *							$prevArray -> mang lang cu
   * @return   : boolean True on success
   */
  function writeDbConfig ($configName, $new = "", $prevArray)
  {
    global $func, $DB, $conf;
    if (! is_array($new)) {
      exit();
    }
    // add old config vars not in $new array
    if (is_array($prevArray)) {
      foreach ($prevArray as $key => $value) {
        if ($new[$key] !== $prevArray[$key]) {
          $newConfig[$key] = $value;
        }
      }
    }
    // build new config vars from $new array
    if (is_array($new)) {
      foreach ($new as $key => $value) {
        $newConfig[$key] = $value;
      }
    }
    foreach ($newConfig as $key => $value) {
       
      $newConfigBase64[base64_encode($key)] = base64_encode($value);
    }
    $configText = serialize($newConfigBase64);
    // update into database
    $array['array'] = $configText;
    $ok = $DB->do_update("config", $array, "name='{$configName}'");
    return $ok;
  }

  /**
   * @function : load_language_admin
   * @param 		: $file -> ten cua module can load
   * @return		: none
   */
  function load_language_admin ($file = "")
  {
    global $vnT, $input, $conf;
    //load lang global
    $file_global = PATH_ADMIN. DS ."language" . DS  . $conf['langcp'] . DS . "global.php";
    if (file_exists($file_global)) {
      require_once ($file_global);
      if (is_array($lang)) {
        foreach ($lang as $k => $v) {
          $vnT->lang[$k] = stripslashes($v);
        }
      }
    }
    //load lang module
    $file_lang = PATH_ADMIN . DS . "modules" . DS . $file . "_ad". DS ."language". DS . $conf['langcp'] . DS . $file . ".php";
    if (file_exists($file_lang)) {
      require_once ($file_lang);
      if (is_array($lang)) {
        foreach ($lang as $k => $v) {
          $vnT->lang[$k] = stripslashes($v);
        }
      }
    }
    unset($lang);
  }

  /**
   * @function : get_lang_default
   * @param 		: none
   * @return		: lang default
   */
  function get_lang_default ()
  {
    global $conf, $DB, $vnT;
    $res = $DB->query("select name from language where is_default=1");
    if ($row = $DB->fetch_row($res)) {
      $out = $row['name'];
    } else
      $out = "vn";
    return $out;
  }

  /**
   * @function : get_request
   * @param 		: $text -> 1 chuoi string
   * @return		: 1 chuoi string
   */
  function get_request ()
  {
    $res = $this->get_array_input($_GET);
    while (list ($k, $v) = each($res)) {
      $in_arr[$k] = $v;
    }
    $res = $this->get_array_input($_POST);
    while (list ($k, $v) = each($res)) {
      $in_arr[$k] = $v;
    }
    return $in_arr;
  }

  //-----------------------------
  function get_array_input ($arr = array())
  {
    $res = array();
    while (list ($k, $v) = each($arr)) {
      $res[$this->clean_key($k)] = (is_array($v)) ? $this->get_array_input($v) : $this->clean_value_admin($v);
    }
    return $res;
  }
 
  //-----------------------------
  function clean_value_admin ($val)
  {
    if ($val == "") {
      return "";
    }
    $val = str_replace("&#032;", " ", $val);
    $val = str_replace("&", "&amp;", $val);
    $val = str_replace("<!--", "&#60;&#33;--", $val);
    $val = str_replace("-->", "--&#62;", $val);
    $val = preg_replace("/<script/i", "&#60;script", $val);
    $val = str_replace(">", "&gt;", $val);
    $val = str_replace("<", "&lt;", $val);
    $val = str_replace("\"", "&quot;", $val);
    $val = preg_replace("/\\\$/", "&#036;", $val);
    $val = preg_replace("/\r/", "", $val); // Remove literal carriage returns
    $val = str_replace("!", "&#33;", $val);
    $val = str_replace("'", "&#39;", $val); // IMPORTANT: It helps to increase sql query safety.
    // Ensure unicode chars are OK
    $val = preg_replace("/&amp;#([0-9]+);/s", "&#\\1;", $val);
    return $val;
  }

 

  /**
   * Escape single quotes, specialchar double quotes, and fix line endings.
   *
   * @param string $text The text to be escaped.
   * @return string Escaped text.
   */
  function js_escape ($text)
  {
    $safe_text = wp_check_invalid_utf8($text);
    $safe_text = wp_specialchars($safe_text, ENT_COMPAT);
    $safe_text = preg_replace('/&#(x)?0*(?(1)27|39);?/i', "'", stripslashes($safe_text));
    $safe_text = preg_replace("/\r?\n/", "\\n", addslashes($safe_text));
    return $safe_text;
  }
  
  /**
   * @function : make_dirname  
   * @param 		: 
   * @return		: 
   */
  function make_dirname ($text)
  {
    $text = str_replace(".", " .", $text);
    $text = str_replace(" ", "_", $text);
    $text = str_replace("?", " ?", $text);
    $text = $this->utf8_to_ascii($text);
    return $text;
  }


  // make_metakey
  function make_metakey ($str)
  {
    $str = trim($str);
    $str =  str_replace("?", "", $str);
    $str =  str_replace("+", "", $str);
    $str =  str_replace("&", "", $str);
    $str =  str_replace("[", "", $str);
    $str =  str_replace("]", "", $str);
    $str =  str_replace("(", "", $str);
    $str =  str_replace(")", "", $str);
    return $str;
  }

  /**
   * @function : randomPass  
   * @param 		: $t -> 1 chuoi stringnone
   * @return		: 1 chuoi string
   */
  function randomPass ()
  {
    $chars = array("a" , "A" , "b" , "B" , "c" , "C" , "d" , "D" , "e" , "E" , "f" , "F" , "g" , "G" , "h" , "H" , "i" , "I" , "j" , "J" , "k" , "K" , "l" , "L" , "m" , "M" , "n" , "N" , "o" , "O" , "p" , "P" , "q" , "Q" , "r" , "R" , "s" , "S" , "t" , "T" , "u" , "U" , "v" , "V" , "w" , "W" , "x" , "X" , "y" , "Y" , "z" , "Z" , "1" , "2" , "3" , "4" , "5" , "6" , "7" , "8" , "9" , "0");
    $max_chars = count($chars) - 1;
    srand((double) microtime() * 1000000);
    for ($i = 0; $i < 8; $i ++)
    {
      $newPass = ($i == 0) ? $chars[rand(0, $max_chars)] : $newPass . $chars[rand(0, $max_chars)];
    }
    return $newPass;
  }

 
  /**
   * @function : Create_Link  
   * @param 		: $new_param ->new_param
   * @return		: $linkout
   */
  function Create_Link ($new_param)
  {
    $currentPage = $_SERVER['PHP_SELF'];
    $arr_new_param = explode("&", $new_param);
    $queryString = "";
    if (! empty($_SERVER['QUERY_STRING'])) {
      $params = explode("&", $_SERVER['QUERY_STRING']);
      $newParams = array();
      foreach ($params as $param) {
        $tmp = substr($param, 0, strrpos($param, "="));
        if (! in_array($param, $arr_new_param) && ($tmp != "p") && ($tmp != "do_display") && ($tmp != "do_hidden")) {
          array_push($newParams, $param);
        }
      }
      if (count($newParams) != 0) {
        $queryString = htmlentities(implode("&", $newParams));
      }
    }
    $linkout = $currentPage . "?" . $queryString . $new_param;
    return $linkout;
  }

  /**
   * @function : paginate  
   * @param 		: 
   * @return		: 
   */
  function paginate ($numRows, $maxRows, $extra = "", $cPage = 1, $class = "pagelink")
  {
    $navigation = "";
    // get total pages
    $totalPages = ceil($numRows / $maxRows);
    $pmore = 5;
    $next_page = $pmore;
    $prev_page = $pmore;
    if ($cPage < $pmore)
      $next_page = $pmore + $pmore - $cPage;
    if ($totalPages - $cPage < $pmore)
      $prev_page = $pmore + $pmore - ($totalPages - $cPage);
    $navigation .= "<span class=\"pagecur\">" . $totalPages . " Pages</span>";
    //	$navigation .= "<span class=\"{$class}\"><b>".$numRows."</b> b&#224;i h&#225;t</span>&nbsp;";
    if ($totalPages > 1) {
      // Show first page
      if ($cPage > ($pmore + 1)) {
        $pLink = $this->Create_Link($extra) . "&p=1";
        $navigation .= "&nbsp;<a href='" . $pLink . "' class='" . $class . "'><b><font color=\"#FF0000\">&laquo;</font></b></a>";
      }
      // End
      // Show Prev page
      if ($cPage > 1) {
        $numpage = $cPage - 1;
        if (! empty($extra))
          $pLink = $this->Create_Link($extra) . "&p=" . $numpage;
        else
          $pLink = $this->Create_Link($extra) . "&p=" . $numpage;
        $navigation .= "&nbsp;<a href='" . $pLink . "' class='" . $class . "'><b><font color=\"#0000FF\">&lsaquo;</font></b></a>";
      }
      // End	
      // Left
      for ($i = $prev_page; $i >= 0; $i --) {
        $pagenum = $cPage - $i;
        if (($pagenum > 0) && ($pagenum < $cPage)) {
          $pLink = $this->Create_Link($extra) . "&p={$pagenum}";
          $navigation .= "&nbsp;<a href='" . $pLink . "' class='" . $class . "'>" . $pagenum . "</a>";
        }
      }
      // End	
      // Current
      $navigation .= "&nbsp;<span class=\"pagecur\">" . $cPage . "</span>";
      // End
      // Right
      for ($i = 1; $i <= $next_page; $i ++) {
        $pagenum = $cPage + $i;
        if (($pagenum > $cPage) && ($pagenum <= $totalPages)) {
          $pLink = $this->Create_Link($extra) . "&p={$pagenum}";
          $navigation .= "&nbsp;<a href='" . $pLink . "' class='" . $class . "'>" . $pagenum . "</a>";
        }
      }
      // End
      // Show Next page
      if ($cPage < $totalPages) {
        $numpage = $cPage + 1;
        $pLink = $this->Create_Link($extra) . "&p=" . $numpage;
        $navigation .= "&nbsp;<a href='" . $pLink . "' class='" . $class . "'><b><font color=\"#0000FF\">&rsaquo;</font></b></a>";
      }
      // End		
      // Show Last page
      if ($cPage < ($totalPages - $pmore)) {
        $pLink = $this->Create_Link($extra) . "&p=" . $totalPages;
        $navigation .= "&nbsp;<a href='" . $pLink . "' class='" . $class . "'><b><font color=\"#FF0000\">&raquo;</font></b></a>";
      }
      // End
    } // end if total pages is greater than one
    $navigation = "<div style=\"padding:7px 2px 2px 2px\">{$navigation}</div>";
    return $navigation;
  }
 
 
  /**
   * @function : html_lang  
   * @param 		: 
   * @return		: 
   */
  function html_lang ($act, $lang)
  {
    global $DB, $conf, $vnT;
    $class_act = "class='font_lang_act'";
    $class = "class='font_lang'";
    $result = $DB->query("select * from language ");
    if ($num = $DB->num_rows($result)) 
		{
      $w = intval(100 / $num) . "%";
      $row_lang = "";
      while ($row = $DB->fetch_row($result)) {
        $link = $act . "&lang=" . $row['name'];
        $class = ($row['name'] == $lang) ? "class='font_lang_act'" : "class='font_lang'";
				$row_lang .= " &nbsp; <span width=\"{$w}\" $class ><a href=\"{$link}\"><img src=\"{$conf['rooturl']}vnt_upload/lang/{$row['picture']}\"  align=\"absmiddle\"/>&nbsp;<strong>({$row['title']})</strong></a></span> ";
      }
    }
		$vnT->muti_lang = ($num>1) ? 1 : 0 ;
    return  $row_lang ;
  }

  /**
   * @function : fetch_content  
   * @param 		: 
   * @return		: 
   */
  function fetch_content ($arr_str, $lang)
  {
    global $conf, $DB, $vnT;
    $tmp = unserialize($arr_str);
    $out = $tmp[$lang];
    return $out;
  }

  /**
   * @function : write_content  
   * @param 		: 
   * @return		: 
   */
  function write_content ($str)
  {
    global $func, $DB, $conf;
    $res_lang = $DB->query("select * from language ");
    while ($row_lang = $DB->fetch_row($res_lang)) {
      $arr_tmp[$row_lang['name']] = $str;
    }
    $out = serialize($arr_tmp);
    return $out;
  }

  /**
   * @function : write_content  
   * @param 		: 
   * @return		: 
   */
  function update_content ($table, $title, $dieukien, $lang, $str)
  {
    global $func, $DB, $conf;
    $res = $DB->query("select $title from  $table where $dieukien ");
    if ($row = $DB->fetch_row($res)) {
      $arr_old = unserialize($row[$title]);
    }
    $res_lang = $DB->query("select * from language ");
    while ($row_lang = $DB->fetch_row($res_lang)) {
      $name = $row_lang['name'];
      if ($arr_old[$name]) {
        if ($name == $lang) {
          $arr_tmp[$name] = $str;
        } else {
          $arr_tmp[$name] = $arr_old[$name];
        }
      } else {
        $arr_tmp[$name] = $str;
      }
    }
    $arr_text_new = array();
    if (is_array($arr_tmp)) {
      foreach ($arr_tmp as $key => $value) {
        $arr_text_new[$key] = stripslashes($value);
      }
    }
    $out = serialize($arr_text_new);
    return $out;
  }

  /**
   * @function : getMenu
   * @param 		:
   * @return		:
   */
  function getMenu ($menu)
  {
    global $vnT, $func;
    $text = '<div class="vnt-toolbar">';
    foreach ($menu as $key => $value) {
      $MenuItem = $value;
      if ($MenuItem['newwin']) {
        $link = "#";
        $onclick = " onClick=\"javascript:NewWindow(" . $MenuItem['link'] . ");\" ";
      } elseif ($MenuItem['link_js']) {
        $link = "javascript:;";
        $onclick = " onClick=\"{$MenuItem['link_js']}\" ";
      } else {
        $link = $MenuItem['link'];
        $onclick = "";
      }


      $src =  $vnT->dir_images."/icon/" . $MenuItem['icon'] .".png";
      if(file_exists($src)){
        $icon = '<img src="' .$src. '" align="absmiddle" alt="" />';
      }else{
        $icon = $MenuItem['icon'] ;
      }
      $text .= "<span class=font_nav><a href='" . $link . "' {$onclick} >".$icon."&nbsp;" . $MenuItem['title'] . "</a></span>&nbsp;";
    }
    $text .='</div>';
    return  $text ;
  }



  /**
   * Ham  ShowTable ($table) 
   * Mo ta   : ham show noi dung 1 table
   * @param  : 
   * 			$table : array	
   * @return :	none
   */
  //============================ ShowTable =================
  function ShowTable ($table)
  {
    global $Template, $vnT, $func;
    $list = "";
    $numcol = count($table['title']);
    $rowfield = array();
    $rowalign = array();
    $rowextra = array();
    $data['f_title'] = $table['f_title'];
    $data['link_action'] = $table['link_action'];
    $data['link_del'] = $table['link_del'];
    $data['link_edit'] = $table['link_edit'];
    $data['link_display'] = $table['link_display'];
    $data['link_hidden'] = $table['link_hidden'];
    $data['numcol'] = $numcol;
    $data['button'] = $table['button'];
    // Title
    $list .= "<thead><tr>";
    while (list ($k, $v) = each($table['title'])) {
      $rowfield[] = $k;
      $tittle_arr = explode("|", $v);
      $title = $tittle_arr[0];
      $width = $tittle_arr[1];
      $align = $tittle_arr[2];
      $extra = $tittle_arr[3];
      if (! empty($align))
        $align = "align=\"{$align}\"";
      $rowalign[] = $align;
      $rowextra[] = $extra;
      $list .= "<th height=25 {$align} width=\"{$width}\" {$extra}>{$title}</th>";
    }
    $list .= "</tr></thead>\n";
    // End
    $list .= "<tbody>\n";
    // Row
    foreach ($table['row'] as $row) {
      $class = ($row['stt'] % 2 == 0) ? "row0" : "row1";
      $list .= '<tr id="' . $row['row_id'] . '" ' . $row['extra'] . ' class="' . $class . '"  > ';
      for ($i = 0; $i < $numcol; $i ++) {
        $value = $row[$rowfield[$i]];
        $align = $rowalign[$i];
        $extra = $rowextra[$i];
        if ((empty($value)) && ($value != 0))
          $value = "&nbsp;";
        if ($i == 0)
          $class = "row1";
        else
          $class = "row";
        $list .= "<td  {$align} {$extra} >{$value}</td>";
      }
      $list .= "</tr>\n";
    }
    if ($table['extra'])
      $list .= '<tr><td colspan="' . $numcol . '" class="extra">' . $table['extra'] . '</td></tr>';
    $list .= "</tbody>\n";
    //end
    $data['list'] = $list;
    $data['csrf_token'] = $table['csrf_token'];
    // $data['extra'] = '<tfoot><tr><td colspan="' . $numcol . '" class="extra">' . $table['extra'] . '</td></tr></tfoot>';
    $Template->assign("data", $data);
    $Template->parse("box_manage");
    $out = $Template->text("box_manage");
    return $out;
  }

 
  /**
   * @function : getToolbar
   * @param     :
   * @return    :
   */
  function getToolbar ($mod, $act, $lang = "vn",$arr_more=array())
  {
    global $func, $DB, $conf, $vnT;


    $menu = array(
      "add" => array(
        'icon' => "i_add" ,
        'title' => "Add" ,
        'link' => "?mod=$mod&act=$act&sub=add&lang=" . $lang) ,
      "manage" => array(
        'icon' => "i_manage" ,
        'title' => "Manage" ,
        'link' => "?mod=$mod&act=$act&lang=" . $lang) ,
      "help" => array(
        'icon' => "i_help" ,
        'title' => "Help" ,
        'link' => "'help/index.php?mod=$mod&act=$act','AdminCPHelp',1000, 600, 'yes','center'" ,
        'newwin' => 1));

    if(count($arr_more)){
      $menu =  array_merge($arr_more, $menu);
    }


    return $this->getMenu($menu);
  }


  /**
   * @function : getToolbar_Cat  
   * @param 		: 
   * @return		: 
   */
  function getToolbar_Cat ($mod, $act, $lang = "vn",$arr_more=array())
  {
    global $func, $DB, $conf, $vnT;
    $menu = array(
      "add" => array(
        'icon' => "i_add" , 
        'title' => "Add" , 
        'link' => "?mod=$mod&act=$act&sub=add&lang=" . $lang) , 
      "move" => array(
        'icon' => "i_move" , 
        'title' => "Move" , 
        'link' => "?mod=$mod&act=$act&sub=move&lang=" . $lang) , 
      "manage" => array(
        'icon' => "i_manage" , 
        'title' => "Manage" , 
        'link' => "?mod=$mod&act=$act&lang=" . $lang) , 
      "help" => array(
        'icon' => "i_help" , 
        'title' => "Help" , 
        'link' => "'help/index.php?mod=$mod&act=$act','AdminCPHelp',1000, 600, 'yes','center'" , 
        'newwin' => 1));
    if(count($arr_more)){
      $menu =  array_merge($arr_more, $menu);
    }
    
    return $this->getMenu($menu);
  }

  /**
   * @function : getToolbar_Small  
   * @param 		: 
   * @return		: 
   */
  function getToolbar_Small ($mod, $act, $lang = "vn",$arr_more=array())
  {
    global $func, $DB, $conf, $vnT;
    $menu = array(
      "manage" => array(
        'icon' => "i_manage" , 
        'title' => "Manage" , 
        'link' => "?mod=$mod&act=$act&lang=" . $lang) , 
      "help" => array(
        'icon' => "i_help" , 
        'title' => "Help" , 
        'link' => "'help/index.php?mod=$mod&act=$act','AdminCPHelp',1000, 600, 'yes','center'" , 
        'newwin' => 1));
    if(count($arr_more)){
      $menu =  array_merge($arr_more, $menu);
    }
    return $this->getMenu($menu);
  }
	
	/**
   * @function : build_seo_url  
   * @param 		: 
   * @return		: 
   */ 
	function build_seo_url($seo)
  {
    global $func, $DB, $conf, $vnT;
    $textout = array();
    //check
    $textout['existed']=0; 
        
    $friendly_url = $seo['friendly_url'] ; 
    if($seo['sub']=="add")
    {
      $res_ck = $DB->query("SELECT * FROM seo_url WHERE name='".$seo['friendly_url']."' " )  ;      
      if($DB->num_rows($res_ck))
      {
        $friendly_url = $seo['friendly_url']."-".time(); 
        $textout['existed']=1 ; 
        $textout['friendly_url'] = $friendly_url;
      }
      
      $cot['modules'] = $seo['modules'];
      $cot['action'] = $seo['action'];
      $cot['name_id'] = ($seo['name_id']) ? $seo['name_id'] : "itemID";
      $cot['item_id'] = $seo['item_id'];
      $cot['name'] = $friendly_url;
      $cot['query_string'] =  $seo['query_string'];
      $cot['date_post'] = time();
      $query_lang = $DB->query("select name from language ");
      while ($row_lang = $DB->fetch_row($query_lang))
      {

        $cot['lang'] = $row_lang['name'];
        $DB->do_insert("seo_url", $cot);
      }
      
    }
    
    if($seo['sub']=="edit")
    {
      $res_ck = $DB->query("SELECT * FROM seo_url WHERE name='".$seo['friendly_url']."' AND lang='".$seo['lang']."' " )  ;      
      if($row_ck = $DB->fetch_row($res_ck))
      {  
        if($row_ck['modules'] == $seo['modules'] && $row_ck['action'] == $seo['action'] && $row_ck['item_id'] == $seo['item_id'] )
        {
          
        }else{
          $friendly_url = $seo['friendly_url']."-".time(); 
          $textout['existed']=1 ; 
          $textout['friendly_url'] = $friendly_url;   
        }                
      }       

      $seo['name_id'] = ($seo['name_id']) ? $seo['name_id'] : "itemID";

      $sql_up = "UPDATE seo_url SET name='".$friendly_url."' WHERE  lang='".$seo['lang']."' AND  modules='".$seo['modules']."' AND action='".$seo['action']."' AND name_id='".$seo['name_id']."' AND item_id=".$seo['item_id'] ;      
    
      $DB->query($sql_up) ;       
 
    }
    
    
    if($seo['sub']=="del")
    {
      $w_del = ($seo['name_id'])  ? $w_del = "AND name_id='".$seo['name_id']."'" : "" ;
      $sql_del = "DELETE FROM seo_url WHERE modules='".$seo['modules']."' AND action='".$seo['action']."' AND lang='".$seo['lang']."' AND item_id in (".$seo['item_id'].") ".$w_del ;      
      $DB->query($sql_del)  ;
    }
    
    return $textout ;
  }
	
	/**
   * @function : BuildSortingLinks  
   * @param 		: 
   * @return		: 
   */ 
	function BuildSortingLinks($fields, $sortLink, $sortField, $sortOrder)
	{
		global $func, $DB, $conf, $vnT;
		$textout = array();
		if (!is_array($fields)) {
			return;
		}
	
		foreach ($fields as  $field) {
			$sortLinks = '';
			foreach (array('asc', 'desc') as $order) {
				if ($order == "asc") {
					$image = "sortup.gif";
				}
				else {
					$image = "sortdown.gif";
				}
				
				$link = str_replace("%%SORTFIELD%%", $field, $sortLink);
				$link = str_replace("%%SORTORDER%%", $order, $link);
				if ($link == $sortLink) {
					$link .= sprintf("&amp;sortField=%s&amp;sortOrder=%s", $field, $order);
				}
				$title = "Sort by ".$field." ".ucfirst($order);
				
				if ($sortField == $field && $order == $sortOrder) { 
				
					$sortLinks .= sprintf('<a href="%s" title="%s" class="SortLink"><img src="'.$vnT->dir_images.'/active_%s" height="10" width="8" border="0"
					/></a> ', $link, $title, $image);
				} else {
					$sortLinks .= sprintf('<a href="%s" title="%s" class="SortLink"><img src="'.$vnT->dir_images.'/%s" height="10" width="8" border="0"
					/></a> ', $link, $title, $image);
				}
				 
			}
			
			$textout[$field] = $sortLinks;
		}
		
		return $textout ;
	}
  /**
   * @function : insertlog  
   * @param 		: 
   * @return		: 
   */
  function insertlog ($act, $cat = "", $pid = "")
  {
    global $conf, $DB, $vnT;
    // Insert Admin log				
    $uplog['adminid'] = $vnT->admininfo['adminid'];
    $uplog['time'] = time();
    $uplog['ip'] = $_SERVER['REMOTE_ADDR'];
    $uplog['action'] = $act;
    $uplog['cat'] = $cat;
    $uplog['pid'] = $pid;
    $doitlog = $DB->do_insert("adminlogs", $uplog);
    // End	
  }

  /**
   * @function : insertRecycleBin
   * @param 		:
   * @return		:
   */
  function insertRecycleBin ($log)
  {
    global $conf, $DB, $vnT;

    $arr_id = @explode(",",$log['item_id']) ;
    $uplog['module'] = $log['module'];
    $uplog['action'] = $log['action'];
    $uplog['tbl_data'] = $log['tbl_data'];
    $uplog['name_id'] = $log['name_id'];
    $uplog['lang'] = $log['lang'];
    $uplog['datesubmit'] = time();
    foreach ($arr_id as $val){
      $uplog['item_id'] = $val;
      $DB->do_insert("recycle_bin", $uplog);
    }

    //del seo_url
    $seo['sub'] = 'del';
    $seo['modules'] = $log['module'];
    $seo['action'] = $log['action'];
    $seo['item_id'] = $log['item_id'];
    $seo['lang'] = $log['lang'];
    $this->build_seo_url($seo);
    
  }

	
	/**
   * @function : clear_cache  
   * @param 		: 
   * @return		: 
   */
  function clear_cache ()
  {
    global $func, $DB, $conf;
   	if ($conf['cache']) 
		{
			$dir = "../cache/";
			$handle = opendir($dir);
			while (($file = readdir($handle)) != false) 
			{
				if (($file!=".") &&($file!="..") && ($file != ".htaccess") && ($file != "index.html")) {
					if (is_dir($dir.$file) && $file!='static_caches') {
						 $this->clean_dir($dir.$file."/");
					}else{
							@unlink($dir . $file);
					}
				}
				
			}
			closedir($handle);
	  }		
  }
	
	//clean_dir
  function clean_dir ($dir)
  {
		global $conf, $vnT; 
    $handle = opendir($dir);
    while (($file = readdir($handle)) != false) 
		{
			if (($file!=".") &&($file!="..") && ($file != ".htaccess") && ($file != "index.html")) {
				if (is_dir($dir.$file)) {
					 $this->clean_dir($dir.$file."/");
				}else{
						@unlink($dir . $file);
				}
			}
      
    }
    closedir($handle);
  }
	
	
	//clear_cache_file
  function clear_cache_file ($arr_file  = array())
  {
		global $conf, $vnT; 
		$dir = "../cache/";
		$handle = opendir($dir);
    while (($file = readdir($handle)) != false) 
		{
			if (($file!=".") &&($file!="..") && ($file != ".htaccess") && ($file != "index.html")) 
			{				
				if(is_array($arr_file))
				{
					foreach ($arr_file as $file_del)
					{						
						if( strstr($file,$file_del)){
							@unlink($dir . $file);			
						}
					}
				}
			}
      
    }
    closedir($handle);
		
  }
	
	/**
   * @function : clear_cache_static 
   * @param 		: 
   * @return		: 
   */
  function clear_cache_static ($arr_file = array() )
  {
    global $func, $DB, $conf;
		if(is_array($arr_file))
		{
			foreach ($arr_file as $files)
			{
				$file_del = "../cache/static_caches/".$files.".php"; 
				if(file_exists($file_del)) {				
					@unlink($file_del);	
				}
			}		 
		}else{
			$handle = opendir("../cache/static_caches");
			while (($file = readdir($handle)) != false) 
			{
				if (($file!=".") &&($file!="..") && ($file != ".htaccess") && ($file != "index.html")) {					 
					@unlink($dir . $file);					
				}				
			}
			closedir($handle);	
		}
  }
	 

  /**
   * @function : FixQuotes  
   * @param 		: 
   * @return		: 
   */
  function FixQuotes ($what = "")
  {
    $what =  str_replace("'", "''", $what);
    while (strstr("\\\\'", $what)) {
      $what = str_replace("\\\\'", "'", $what);
    }
    return $what;
  }

  /**
   * @function : list_skin_admin  
   * @param 		: 
   * @return		: 
   */
  function list_skin_admin ()
  {
    global $vnT, $func, $conf, $DB;
    $text = "<div class='box_skins'>Select Skin : ";
    $queryString = "";
    if (! empty($_SERVER['QUERY_STRING'])) {
      $params = explode("&", $_SERVER['QUERY_STRING']);
      $newParams = array();
      foreach ($params as $param) {
        if (stristr($param, "skin_acp") == false) {
          array_push($newParams, $param);
        }
      }
      if (count($newParams) != 0) {
        $queryString = "&" . htmlentities(implode("&", $newParams));
      }
    }
    $path = $conf['rootpath'] . "admin/skins";
    if ($dir = opendir($path)) {
      $text .= "<select name=\"skin_acp\"  id=\"skin_acp\"  class='select' onChange=\"javascript:document.location='?skin_acp=' +this.value+'{$queryString}';\" >";
      while (false !== ($file = readdir($dir))) {		
        if (! preg_match( "/(.)(..)/i" , $file) && ($file != "index.html")) {
          if ($conf['skin_acp'] == $file)
            $text .= "<option value=\"{$file}\" selected=\"selected\" >($file)</option>";
          else
            $text .= "<option value=\"{$file}\"  >($file)</option>";
        }
      }
      $text .= "</select>";
    }
    $text .= "</div>";
    return $text;
  }



  /**
   * @function : html_lang
   * @param 		:
   * @return		:
   */
  function box_lang ()
  {
    global $DB, $conf, $vnT;
    $folder_admin = ($conf['folder_admin']) ? $conf['folder_admin'] : "admin";
    if($_GET['mod']){
      if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') {
        $link_vn =  "https://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']."&langcp=vn";
        $link_en =  "https://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']."&langcp=en";
      }else{
        $link_vn =  "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']."&langcp=vn";
        $link_en =  "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']."&langcp=en";
      }

    }else{
      $link_vn =  $conf['rooturl'] .$folder_admin. "/?langcp=vn";
      $link_en =  $conf['rooturl'] .$folder_admin."/?langcp=en";
    }

    $class_vn = ("vn" == $conf['langcp']) ? "class='active'" : "";
    $class_en = ("en" == $conf['langcp']) ? "class='active'" : "";

    $textout = '<div class="box-lang"><a href="'.$link_vn.'" '.$class_vn.'><img src="'.$vnT->dir_images.'/flag_vn.gif" alt="Tiếng Việt">Tiếng Việt</a><a href="'.$link_en.'"  '.$class_en.'><img src="'.$vnT->dir_images.'/flag_en.gif" alt="English">English</a></div>';

    $cur_src = $vnT->dir_images ."/ln-vn.png" ;
    $cur_title = "Tiếng Việt";
    if ($conf['langcp']=="en"){
      $cur_src = $vnT->dir_images ."/ln-en.png" ;
      $cur_title = "English";
    }
    $textout = '<div class="top_langues">
                <div class="langues_title">
                    <div class="aImg"><img src="'.$cur_src.'" /></div>
                    <div class="aTitle">'.$cur_title.'</div>
                </div>
                <div class="langues_menu">
                    <div class="mTitle">Ngôn ngữ</div>
                    <div class="mContent">
                        <ul class="lMenu">
                            <li><a href="'.$link_vn.'"><span class="mImg"><img src="'.$vnT->dir_images.'/ln-vn.png" /></span><span class="mText">Tiếng Việt</span></a></li>
                            <li><a href="'.$link_en.'"><span class="mImg"><img src="'.$vnT->dir_images.'/ln-en.png" /></span><span class="mText">English</span></a></li>
                        </ul>
                    </div>
                </div>
            </div>';
    return  $textout ;
  }


  /**
   * Ham  html_redirect ($url,$mess) 
   * Mo ta   : tu dong redirect den $url voi thong bao $mess
   * @param  : 
   *			$url : duong link chuyen den	
   *			$mess : thong diep  
   * @return :	none
   */
  function html_redirect ($url, $mess, $time_ref = 1)
  {
    global $Template, $conf, $vnT;
    $data['host'] = $_SERVER['HTTP_HOST'];
    $data['url'] = $url;
    $data['mess'] = $mess;
    $data['time_ref'] = $time_ref;
    $Template->assign("data", $data);
    $Template->parse("box_redirect");
    flush();
    $Template->out("box_redirect");
    exit();
  }

  /**
   * @function : html_err
   * @param 		: 
   * @return		: 
   */
  function html_err ($err,$class="")
  {
    global $vnT, $conf;
    $text = '<dl class="message fade" id="system-message" >
            <dt class="message" >' . $vnT->lang['error'] . '</dt>
            <dd class="message" >
            	<ul><li >' . $err . '</li></ul>
            </dd>
            </dl>';

    $text = '<div id="system-message" class="alert alert-danger'.$class.'"><a href="#" class="close" data-dismiss="alert">&times;</a><div class="mess-title">' . $vnT->lang['error'] . ' :</div><div class="mess-text">' . $err . '</div></div>';

    return $text;
  }

  /**
   * @function : html_mess
   * @param 		: 
   * @return		: 
   */
  function html_mess ($mess,$class='')
  {
    global $vnT, $conf;
    $text = '<dl class="message fade" id="system-message" >
            <dt class="message" >' . $vnT->lang['announce'] . '</dt>
            <dd class="message" >
            	<ul><li >' . $mess . '</li></ul>
            </dd>
            </dl>';
    $text = '<div id="system-message" class="alert alert-success'.$class.'"><a href="#" class="close" data-dismiss="alert">&times;</a><div class="mess-title">' . $vnT->lang['announce'] . ' :</div><div class="mess-text">' . $mess . '</div></div>';

    return $text;
  }

  /*
	* This function deletes the given element from a one-dimension array
	* Parameters: $array:    the array (in/out)
	*             $deleteIt: the value which we would like to delete
	*             $useOldKeys: if it is false then the function will re-index the array (from 0, 1, ...)
	*                          if it is true: the function will keep the old keys
	* Returns true, if this value was in the array, otherwise false (in this case the array is same as before)
	*/
  function delete_value_array (&$array, $deleteIt, $useOldKeys = FALSE)
  {
    $tmpArray = array();
    $found = FALSE;
    foreach ($array as $key => $value) {
      if ($value !== $deleteIt) {
        if (FALSE === $useOldKeys) {
          $tmpArray[] = $value;
        } else {
          $tmpArray[$key] = $value;
        }
      } else {
        $found = TRUE;
      }
    }
    $array = $tmpArray;
    return $found;
  }

  /*
	* This function deletes the given element from a one-dimension array
	* Parameters: $array:    the array (in/out)
	*             $deleteIt: the value which we would like to delete
	*             $useOldKeys: if it is false then the function will re-index the array (from 0, 1, ...)
	*                          if it is true: the function will keep the old keys
	* Returns true, if this value was in the array, otherwise false (in this case the array is same as before)
	*/
  function delete_key_array (&$array, $deleteIt)
  {
    $tmpArray = array();
    $found = FALSE;
    foreach ($array as $key => $value) {
      if ($key !== $deleteIt) {
        $tmpArray[$key] = $value;
      } else {
        $found = TRUE;
      }
    }
    $array = $tmpArray;
    return $found;
  }
	
	/*** Ham get_input_tags_js   */
	function get_input_tags_js ($module,$list_key,$more_tag="")
	{
		global $func, $DB, $conf,$vnT;
		$output = "";
		$arr_out = array();
		$array_key = explode(",",$list_key);
		$array_key = array_unique ($array_key);
		$list_key_compare = '';

		$tbl_tag = ($module) ? $module."_tags" : "tags" ;

		foreach ($array_key as $val)
		{
			if($val){
				$list_key_compare .= $vnT->func->get_keyword($val).","	;
			}
		}
		$list_key_compare = substr($list_key_compare,0,-1);
		$arr_name = array();	
		
		$sql = "SELECT tag_id,name,name_compare  
						FROM ".$tbl_tag." 
						WHERE display=1 
						AND ( FIND_IN_SET(tag_id,'".$list_key_compare."')>0  OR FIND_IN_SET(name_compare,'".$list_key_compare."')>0  )
						ORDER BY name ASC";
		
		//echo $sql;
		$query = $vnT->DB->query($sql);
		$arr_check = array();
		while ($row = $vnT->DB->fetch_row($query)) {
			$arr_check[$row["tag_id"]] = $row["name_compare"];
			$arr_name[$row["tag_id"]] = $row['name'];
		}
		$vnT->DB->free_result($query);
	 
		foreach ($array_key as $remove_key => $text_input) 
		{
			$text_input = $vnT->func->get_keyword($text_input);
			if(array_key_exists ($text_input, $arr_check))
			{
				$arr_out[] = $text_input;
				unset($array_key[$remove_key]);			
			}
			elseif(in_array($text_input,$arr_check))
			{
				$k_id = array_search ($text_input,$arr_check);
				$arr_out[] = $k_id;
				unset($array_key[$remove_key]);		
			}
		} 
		
		// Add news_keywords
		$array_key = array_values($array_key);
		
	 
		$j = 0;
		for($j=0;$j<count($array_key);$j++)
		{
			if($array_key[$j]!="")
			{			
				$cot['name'] = trim($array_key[$j]);
				$cot['name_compare'] = $vnT->func->get_keyword($cot['name']);
				$cot['name_search'] = strtolower($vnT->func->utf8_to_ascii($cot['name']));
				$cot['display'] = 1;
				$cot['date_post'] = time();
				$ok = $vnT->DB->do_insert($tbl_tag,$cot);
				if($ok)
				{
					$tag_id = $vnT->DB->insertid();				
					$arr_out[] = $tag_id;
					$arr_name[$tag_id] = $cot['name'];
				}
			}
		}
		
		//more_tag
		if($more_tag)
		{ 
			$more_tag = trim($more_tag);
			$arr_more_tag = @explode("|",$more_tag);
			foreach ($arr_more_tag as $p_name)
			{
				$p_name = trim($p_name);
				$res_ck = $vnT->DB->query("SELECT tag_id FROM ".$tbl_tag."	WHERE name='".$p_name."' ");
				if (!$vnT->DB->fetch_row($res_ck)) 
				{
					$cot_p = array();
					$cot_p['name'] = $p_name;
					$cot_p['name_compare'] = $vnT->func->get_keyword($cot['name']);
					$cot_p['name_search'] = strtolower($vnT->func->utf8_to_ascii($cot['name']));
					$cot_p['display'] = 1;
					$cot_p['is_auto']= 1 ;
					$cot_p['date_post'] = time();			
					$ok1 = $vnT->DB->do_insert($tbl_tag,$cot_p);
					if($ok1){
						$tag_id = $vnT->DB->insertid();				
						$arr_out[] = $tag_id;
						$arr_name[$tag_id] = $cot['name'];
					}
				}
			}
		}
		 
		// End add news_keywords
		
		$output['list_id']= "";
		$output['list_name'] ="" ;
		$arr_out = array_unique ($arr_out);
		if(count($arr_out) > 0)
		{
			$output['list_id'] = @implode(",",$arr_out) ;
			$list_name="";
			foreach ($arr_out as $k){
				$list_name .= $arr_name[$k].", ";
			}
			$list_name = substr($list_name,0,-2);
			$output['list_name'] = $list_name ;
		} 
		 
		return $output;
	}
	
	/*** Ham get_tags_js   */
	function get_tags_js ($module,$input_id="tags",$html_id="p_key", $list_id="" )
	{
		global $func, $DB, $conf, $vnT;
		$list_data = "";
		$list_select = "";
		$arr_id = explode(",",$list_id);

		$tbl_tag = ($module) ? $module."_tags" : "tags" ;
		
		$query = $vnT->DB->query("SELECT tag_id,name	FROM ".$tbl_tag."	WHERE display=1 ORDER BY name ASC");
		if ($num = $vnT->DB->num_rows($query)) {
			$i = 0;
			$j = 0;
			$list_data = "";
			while ($row = $vnT->DB->fetch_row($query)) 
			{
				$i++;
				$list_data .= "{value: '".$row["tag_id"]."', name: '".$row["name"]."'}";
				if($i < $num)
					$list_data .= ",";
				
				if(in_array($row["tag_id"],$arr_id))
				{
					$j++;
					$list_select .= "{value: '".$row["tag_id"]."', name: '".$row["name"]."'}";
					if($j < count($arr_id))
						$list_select .= ",";
				}
			}
		}
		
		$list_data = ($list_data) ? "[".$list_data."]" : "''";
		$preFill = ($list_select) ? ", preFill: [".$list_select."]" : "";
		
		$vnT->html->addScriptDeclaration("
				var data = {items: ".$list_data."};
				$(document).ready(function() {
					$('#".$input_id."').autoSuggest(data.items, {selectedItemProp: 'name' , searchObjProps: 'name',asHtmlID:'".$html_id."'".$preFill.",startText : 'Nhập từ khóa ' ,minChars: 2});
				});
			");
	}
  //end class
}
?>