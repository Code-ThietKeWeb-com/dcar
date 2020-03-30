<?php
/*================================================================================*\
|| 							Name code : class_cache.php 		 		 										  			# ||
||  				Copyright © 2007 by Thai Son - CMS vnTRUST                					# ||
\*================================================================================*/
/**
 * @version : 1.0
 * @date upgrade : 11/12/2007 by Thai Son
 **/
if (! defined('IN_vnT')) {
  die('Hacking attempt!');
}
define('CACHE_PHP_CONTENT', '<?php die("Hacking attemp!"); ?> ');

class Cache
{
  var $turn_on = 1;
  var $option = array();
  var $mod_cache = array();
	var $mod_no_cache = array();
	var $act_no_cache = array();
	var $box_no_cache = array();	
  var $root = "cache/"; 
  var $cache_name = "";
  var $cache_content = "";
  var $expire_time = 0; //86400 seconds <=> 1 days | 0: no expire
  var $max_size = 500000; //(bytes) | 10Mb*1024Kb*1024B
  var $var_count = 0;
  var $var_info = array();
  //For translate date
  var $date_source = array();
  var $date_replace = array();
	var $arr_mod_no_dir = array("main","page","about");
	var $arr_mod_no_preGet = array("main","page","about");

  function Cache ($root = "")
  {
    if (! empty($root)) {
      $this->root = $root;
    }
    if (! is_dir($this->root)) {
      $this->halt("Cache Dir <b>" . $this->root . "</b> does not exist!");
      return false;
    } 
		
    if (substr($this->root, - 1) != "/") {
      $this->root .= "/";
    }
    return true;
  }

  function cache_onoff ($status)
  {
    $this->turn_on = $status ? 1 : 0;
  }

  function set_expiretime ($expire_time)
  {
    $this->expire_time = $expire_time;
  }

  function set_maxsize ($max_size)
  {
    $this->max_size = $max_size;
  }

 
  function set_cache_name ($cache_name)
  {
    $this->cache_name = $cache_name;
  }

  function set_cache_content ($content)
  {
    $this->cache_content .= $content;
  }

  function reset_cache_content ()
  {
    $this->cache_content = "";
  }

  //Begin cache - Check whether there is cached content
  function begin_cache ()
  {
    global $conf, $vnT, $input;
    $this->cache_name = "";
    //Get config options
    if ($vnT->conf['cache']) 
		{
      $this->cache_name = $this->get_page_id();
			 
      if (empty($this->cache_name)) {
        //Turn off
        $this->cache_onoff(0);
      } else {
        //Get cache
				$cached_content = $this->get_cache($this->cache_name) ;
        if ($cached_content != false) 
				{
          $this->refresh_cached_data($cached_content);
					echo $cached_content;
					$vnT->DB->close();
          die();
        }
      }
    } else {
      $this->cache_onoff(0);
    }
  }

  //Refresh some data in cached content
  function refresh_cached_data (&$cached_content)
  {
    global $conf, $vnT, $input, $DB;
    $result = $vnT->DB->query("select * from layout where cache=0 and l_show=1 ");
    while ($row = $DB->fetch_row($result)) {
      $cached_content = preg_replace('#(<!-- Start_' . $row['name'] . ')(.*?)(<!-- End_' . $row['name'] . ' -->)#si', '<!-- Start_' . $row['name'] . ' -->' . $vnT->block->_print_one_block($row['name']) . '<!-- End_' . $row['name'] . ' -->', $cached_content);
    }
		
		//lib
		if(is_array($this->box_no_cache))
		{
			foreach ($this->box_no_cache as $box_name)
			{
				eval('$box_content= $vnT->lib->'.$box_name.'();');
				$cached_content = preg_replace('#(<!-- Start_' . $box_name . ')(.*?)(<!-- End_' . $box_name . ' -->)#si', $box_content , $cached_content);
			}
		}
		
  }

  //End cache
  function end_cache ()
  {
    global $Template, $DB, $vnT, $conf, $input;
    $cache_name = $this->get_page_id();
	 
    //Set html caching
    $this->reset_cache_content();
    $this->set_cache_content($Template->text("body"));
    $this->set_cache($cache_name);
		
    echo $this->get_cache($cache_name);
  }

  //Set new cached content
  function set_cache ($cache_name = "" )
  {
		global $vnT, $conf, $input;
		
		$dir_mod = $input['mod']."/"; 
		
		if(in_array($input['mod'],$this->arr_mod_no_dir)) $dir_mod = "";
    if (! $this->turn_on)
      return false;
      //Clear old cache
    $this->clear_cache();
    if (! empty($cache_name)) {
      $this->cache_name = $cache_name;
    }
    if (empty($cache_name)) {
      $cache_name = $this->get_cache_name();
    }
		$cache_file = $this->root  . $dir_mod  . $cache_name . '.cache';
		
    if (! file_exists($cache_file)) {
      $f = fopen($cache_file, "w") or $this->halt("Couldn't open file $cache_file to write.");
      if (flock($f, LOCK_EX)) {
        fwrite($f, $this->cache_content);
        flock($f, LOCK_UN);
      } else {
        $this->halt("Couldn't lock the file: $cache_name");
      }
      fclose($f);
    }
		 
    return true;
  }

  //Get cached content
  function get_cache ($cache_name = "" )
  {
		global $vnT, $conf, $input;
    if (! $this->turn_on)
      return false;
    if (empty($cache_name)) {
      $cache_name = $this->get_cache_name();
    }
		
		
		$dir_mod = $input['mod']."/"; 
		if(in_array($input['mod'],$this->arr_mod_no_dir)) $dir_mod = "";
		
    $cache_file = $this->root . $dir_mod . $cache_name . '.cache';
		
    if (file_exists($cache_file)) 
		{
			//check tu dong clear
			$arr_cache_conf = @unserialize($vnT->conf['cache_conf']);

			$arr_mod_cache = array();
			foreach ($arr_cache_conf as $k => $cache_conf)
			{
				$arr_mod_cache[] = $k ;	
			}
			$mod_conf = (in_array($input['mod'],$arr_mod_cache) ) ? $input['mod'] : "other";
			
			
			if($mod_conf=="main" || $mod_conf=="other" )
			{
				$clear_type = $arr_cache_conf[$mod_conf]['clear_type'] ;
				$cache_time = (int)$arr_cache_conf[$mod_conf]['cache_time'] ;	
			}else{
				if($input['act']=="detail"){
					$clear_type = $arr_cache_conf[$mod_conf]['detail']['clear_type'] ;
					$cache_time = (int)$arr_cache_conf[$mod_conf]['detail']['cache_time'] ;	
				}else{
					$clear_type = $arr_cache_conf[$mod_conf]['list']['clear_type'] ;
					$cache_time = (int)$arr_cache_conf[$mod_conf]['list']['cache_time'] ;		
				}
			}
			
			
			if($clear_type==1 && $cache_time>0)	
			{
				$cache_time = ($cache_time*3600);	
				$expire_time	= time() - $cache_time;
				$mtime			= filemtime($cache_file);
				if ( $cache_time && ($mtime < $expire_time) ){
					//Delete expired cached files
					@unlink($cache_file);
					return false;
				} 
			} 
			
      $str = implode('', @file($cache_file));			 
			
      return trim($str);
    }
    return false;
  }

  function get_cache_name ()
  {
    $cache_name = isset($_SERVER['REQUEST_URI']) ? addslashes(trim($_SERVER['REQUEST_URI'])) : ' ';
    return md5($cache_name);
  }
 
  //Clear cache data
  function clear_cache ($prefix_name = "" )
  {
    if (! $this->turn_on)
      return false;
    $total_size = 0;
    $file_info = array();
    
    return true;
  }


  function get_page_id ()
  {
    global $vnT, $input, $conf, $vnTRUST;
		
		
    $mod_name = $input['mod'];
    if (empty($mod_name) || ($mod_name == 'main')) {
      $mod_name = 'main';
    }
		
		
		if (in_array($mod_name, $this->mod_no_cache)) {
			return "";
    }
		if (in_array($mod_name."|".$input['act'], $this->act_no_cache) ) {
      return "";
    }
		
		$arr_shopping = array("cart","checkout_address","checkout_shipping","checkout_payment","checkout_method","checkout_process","checkout_confirmation","checkout_finished");
		if (in_array($input['act'], $arr_shopping) ) {
      return "";
    }
					
		if (($input['act']=='load_ajax') || isset($_POST['do_submit']) || isset($_POST['btnSearch']) || isset($_POST['btnSend'])  || $_GET['preview']) {
			return "";
    }
     
    $code = trim($vnTRUST);
    $cmd_arr = @explode("|", $code);

    $pre = "";
    foreach ($cmd_arr as $value) {
      if (! empty($value)) {
        $k = trim(substr($value, 0, strpos($value, ":")));
        $v = trim(substr($value, strpos($value, ":") + 1));
        if ($k != "mod") {
          $pre .= "_" . $k . "-" . $v;
        }
      }
    }
		
		if(!in_array($mod_name,$this->arr_mod_no_preGet))
		{
			$arr_get = $_GET;		
			foreach ($arr_get as $keyG => $valueG) {
				if (! empty($valueG)) {
					
					if ($keyG != $conf['cmd']) {
						$pre .= "_" . $keyG . "-" . $valueG;
					}
				}
			}
		}
		
		$cache_name = $mod_name;	
		if($input['act']=="detail")	{
			$cache_name.="_detail";
		}
		
    if ($pre){
      $cache_name .= "_" . md5($pre);
		}		
    $cache_name .= "_" . $vnT->lang_name;
		
    return $cache_name;
  }

  function halt ($msg)
  {
    echo "<b>Cache Error:</b>\n<br>$msg";
    die();
  }

  // read_cache
  function read_cache ($funcName, $pre_arr = array(), $cache_expiry = -1) // NDK
  {
    global $conf, $vnT; 
    $cache_expiry = ($cache_expiry < 0) ? $this->expire_time : $cache_expiry;
    $res = _NOC;
    $cache_dir = $this->root ;
    $pre = (! empty($pre_arr)) ? "_" . md5(implode("_", $pre_arr)) : "";
    $file = $cache_dir  . $funcName . $pre . "_" . $vnT->lang_name . ".cache";
    if (file_exists($file)) {
      $filemtime = filemtime($file);
      if($cache_expiry>0)
			{
				if (time() - $filemtime < $cache_expiry) {
        	$res = file_get_contents($file);
      	}
			}else{
				$res = file_get_contents($file);
			}
    }
    return $res;
  }

  // save_cache
  function save_cache ($funcName, $content, $pre_arr = array(), $cache_expiry = -1)
  {
    global $conf, $vnT; 
    $cache_expiry = ($cache_expiry < 0) ? $this->expire_time : $cache_expiry;
    $cache_dir = $this->root  ;
    if (! is_writable($cache_dir))
      @chmod($cache_dir, 0777);
    if (is_writable($cache_dir)) {
      $pre = (! empty($pre_arr)) ? "_" . md5(implode("_", $pre_arr)) : "";
      $file = $cache_dir  . $funcName . $pre . "_" . $vnT->lang_name . ".cache";
      $do_save = 1;
      if (file_exists($file)) {        
        if($cache_expiry>0) {
					$filemtime = filemtime($file);
					if (time() - $filemtime < $cache_expiry)
          	$do_save = 0;
				}
      }
      if ($do_save) {
        $fp = @fopen($file, "w+");
        @fwrite($fp, $content);
        @fclose($fp);
      }
    }
  }
	
}
?>