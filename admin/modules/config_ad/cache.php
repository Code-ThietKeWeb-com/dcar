<?php
/*================================================================================*\
|| 							Name code : cache.php 		 			                      					# ||
||  				Copyright © 2007 by Thai Son - CMS vnTRUST                          # ||
\*================================================================================*/
/**
 * @version : 1.0
 * @date upgrade : 11/12/2007 by Thai Son
 **/
if (! defined('IN_vnT')) {
  die('Hacking attempt!');
}
$act = new sMain($sub);

class sMain
{
  var $html = "";
  var $output = "";
  var $base_url = "";
  var $dir_cache = "../cache/";
	
  function sMain ($sub)
  {
    global $Template, $vnT, $func, $DB, $conf;
		require_once ("function_config.php");
    $this->skin = new XiTemplate(DIR_MODULE . DS . "config_ad" . DS . "html" . DS . "cache.tpl");
    $this->skin->assign('LANG', $vnT->lang);
    $lang = ($vnT->input['lang']) ? $lang = $vnT->input['lang'] : $func->get_lang_default();
    $this->linkUrl = "?mod=config&act=cache";
		
		
		switch ($vnT->input['sub']) {
      case 'edit':
        $nd['content'] = $this->do_Edit($lang);
      break;
			case 'clear_all':
        $nd['content'] = $this->do_ClearAll($lang);
      break;
			case 'clear':
        $nd['content'] = $this->do_Clear($lang);
      break;
			 
      default:
        $nd['f_title'] = "Manage Cache";
        $nd['content'] = $this->do_Manage($lang);
      break;
    } 
				
		$nd['menu'] =  $func->getToolbar_Small($this->module, $this->action, $lang);
		$nd['icon'] = 'icon-'.$this->module;
    $Template->assign("data", $nd);
    $Template->parse("box_main");
    $vnT->output .= $Template->text("box_main"); 
		
  }
	
	/**
   * function do_Edit 
   * Cap nhat gioi thieu 
   **/
  function do_Edit ($lang)
  {
    global $vnT, $func, $DB, $conf; 
    if ($vnT->input['do_submit']) 
		{
      $data = $_POST;
			/*echo "<pre>";
			print_r($data);
			echo "</pre>"; 
			*/
			$cot['cache'] = $_POST['cache'];
			$cot['cache_conf'] = serialize($data['cache_conf']);
      $arr_old = $func->fetchDbConfig();
      $ok = $func->writeDbConfig("config", $cot, $arr_old);
      if ($ok) { 
        $mess = $vnT->lang["edit_success"];
      } else {
        $mess = $vnT->lang["edit_failt"];
      }
			
      $func->insertlog("Update", $_GET['act'], 1);
    }
    $url = $this->linkUrl;
    $func->html_redirect($url, $mess);
  }
	
	/**
   * function do_ClearAll  
   **/
  function do_ClearAll ($lang)
  {
    global $vnT, $func, $DB, $conf; 
    
		$this->clean_dir($this->dir_cache); 
    $func->insertlog("Clear All", $_GET['act'], 1);
    $mess = "Xóa cache thành công";
    $url = $this->linkUrl;
    $func->html_redirect($url, $mess);
  }
	
	/**
   * function do_Clear  
   **/
  function do_Clear ($lang)
  {
    global $vnT, $func, $DB, $conf; 
    $mod_clear = $_GET['mod_clear'];
		$type = $_GET['type'];
 	 
		$this->clean_cache_mod($mod_clear,$type)	;
			 
    $func->insertlog("Clear cache {$mod_clear} ", $_GET['act'], 1);
    $mess = "Xóa cache thành công";
    $url = $this->linkUrl;
    $func->html_redirect($url, $mess);
  }
	
  //=============
  function do_Manage ()
  {
    global $func, $DB, $conf, $vnT;
     
		$data = $func->fetchDbConfig();		
 		/*	echo "<pre>";
			print_r($data);
			echo "</pre>";
		*/	
		$arr_cache_conf = unserialize($data['cache_conf']);
		/*	echo "<pre>";
			print_r($arr_cache_conf);
			echo "</pre>";
		*/
			
		$result = $DB->query("select * from modules WHERE (mod_name!='search' AND mod_name!='member') order by ordering ASC, id ASC");
		while ($row_mod = $DB->fetch_row($result)) 
		{
			//check folder
			$path_dir_mod = $this->dir_cache.$row_mod['mod_name'];
			if (!file_exists($path_dir_mod)){
				@mkdir($path_dir_mod,0777); 
				@chmod($path_dir_mod, 0777);
			}
			
			$dir_info = array();
			$this->get_dir_info($this->dir_cache,$dir_info);
			/*echo "<pre>";
			print_r ($dir_info);
			echo "<pre>";*/
			$row['module_name'] = $row_mod['name'];
			$row['module'] = $row_mod['mod_name'];
			$row['list']['cache_time'] = $arr_cache_conf[$row_mod['mod_name']]['list']['cache_time'];
			$row['list']['cache_size'] =  $func->format_size($dir_info[$row_mod['mod_name']]['list']['size']);
			if($arr_cache_conf[$row_mod['mod_name']]['list']['clear_type']==1)
			{
				$row['list']['checked1'] =  "checked" ;
				$row['list']['checked0'] =  "";	
			}else{
				$row['list']['checked0'] =  "checked" ;
				$row['list']['checked1'] =  "";	
			}
			$row['list']['link_clear'] =  $this->linkUrl."&sub=clear&mod_clear=".$row_mod['mod_name']."&type=list";
			
			$row['detail']['cache_time'] = $arr_cache_conf[$row_mod['mod_name']]['detail']['cache_time'];
			$row['detail']['cache_size'] =  $func->format_size($dir_info[$row_mod['mod_name']]['detail']['size']);
			if($arr_cache_conf[$row_mod['mod_name']]['detail']['clear_type']==1)
			{
				$row['detail']['checked1'] =  "checked" ;
				$row['detail']['checked0'] =  "";	
			}else{
				$row['detail']['checked0'] =  "checked" ;
				$row['detail']['checked1'] =  "";	
			}
			$row['detail']['link_clear'] = $this->linkUrl."&sub=clear&mod_clear=".$row_mod['mod_name']."&type=detail";
			 
			$this->skin->assign('row', $row);
   	  $this->skin->parse("manage.html_mod");
		}
		
		$data['main']['cache_time'] = $arr_cache_conf['main']['cache_time'];
		$data['main']['cache_size'] =  $func->format_size($dir_info['main']['size']);
		
		if($arr_cache_conf['main']['clear_type']==1)
		{
			$data['main']['checked1'] =  "checked" ;
			$data['main']['checked0'] =  "";	
		}else{
			$data['main']['checked0'] =  "checked" ;
			$data['main']['checked1'] =  "";	
		}
		  
		
		$data['other']['cache_time'] = $arr_cache_conf['other']['cache_time'];
		$data['other']['cache_size'] =  $func->format_size($dir_info['other']['size']);
		if($arr_cache_conf['other']['clear_type']==1)
		{
			$data['other']['checked1'] =  "checked" ;
			$data['other']['checked0'] =  "";	
		}else{
			$data['other']['checked0'] =  "checked" ;
			$data['other']['checked1'] =  "";	
		}
		
		
		$data['list_cache'] = vnT_HTML::list_yesno("cache", $data['cache']);
		
    //Get cache info
    $data['total_file'] = $html_info['files']  ;
    $data['total_size'] = $func->format_size($html_info['size']);
		$data['link_clear_main'] = $this->linkUrl."&sub=clear&mod_clear=main";
		$data['link_clear_other'] = $this->linkUrl."&sub=clear&mod_clear=other";
		
		
		if (! is_writable($conf['rootpath']."cache")) {
      $err .= '<p>'.str_replace("{dir}","cache",$vnT->lang['mess_dir_not_write']).'</p>' ; 
    }
		
    $data['err'] = $err;
    $data['link_action'] = $this->linkUrl."&sub=edit";
		$data['link_clear_all'] = $this->linkUrl."&sub=clear_all";
		$data['link_rebuilt'] = $this->linkUrl."&sub=rebuilt";
		
		/*assign the array to a template variable*/
    $this->skin->assign('data', $data);
    $this->skin->parse("manage");
    return $this->skin->text("manage");
		 
  }
	
	//get_dir_info
  function get_dir_info ($dir,&$dir_info,$mod="")
  {
		global $conf, $vnT; 
    $handle = opendir($dir);
    while (($file = readdir($handle)) != false) {
      
			if (($file!=".") &&($file!="..") && ($file != ".htaccess") && ($file != "index.html")) {
				if (is_dir($dir.$file)) {
					 $this->get_dir_info($dir.$file."/",$dir_info ,$file);
				}else{						
					if($mod=="")
					{
						if(strstr($file,"main_")){
							$dir_info['main']['files'] ++;
							$dir_info['main']['size'] += filesize($dir . $file); 
						}else{
							$dir_info['other']['files'] ++;
							$dir_info['other']['size'] += filesize($dir . $file); 
						}
					}else{
						if(strstr($file,"detail_")){
							$dir_info[$mod]['detail']['files'] ++;
							$dir_info[$mod]['detail']['size'] += filesize($dir . $file); 
						}else{
							$dir_info[$mod]['list']['files'] ++;
							$dir_info[$mod]['list']['size'] += filesize($dir . $file); 
						}	
					}
				}
			} 
			
    }
    closedir($handle);
    return $dir_info;
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
	
	//clean_dir
  function clean_cache_mod ($mod, $type="list")
  {
		global $conf, $vnT;
 		if($mod=="main")
		{
			$handle = opendir($this->dir_cache);
			while (($file = readdir($handle)) != false) 
			{
				if (($file!=".") && ($file!="..") && ($file != ".htaccess") && ($file != "index.html")) {
					 if(strstr($file,"main_")){
							@unlink($this->dir_cache . $file);
					 }
					
				}
				
			}
			closedir($handle);	
			
		}elseif ( $mod=="other")	{
			
			$handle = opendir($this->dir_cache);
			while (($file = readdir($handle)) != false) 
			{
				if (($file!=".") && ($file!="..") && ($file != ".htaccess") && ($file != "index.html")) {
				 if(!strstr($file,"main_") && !is_dir($this->dir_cache . $file)){
					@unlink($this->dir_cache . $file);
				 }					
				}				
			}
			closedir($handle);	
			
		}else	{
			$dir_cache_mod = $this->dir_cache.$mod."/" ;
			$handle = opendir($dir_cache_mod);
			while (($file = readdir($handle)) != false) 
			{
				if (($file!=".") && ($file!="..") && ($file != ".htaccess") && ($file != "index.html")) 
				{
					if($type=="detail") 
					{
						 if(strstr($file,"detail_")){
							@unlink($dir_cache_mod . $file);
					 	 }
					}else{						
						if(!strstr($file,"detail_")){
							@unlink($dir_cache_mod . $file);
					 	 }
					}
				}
				
			}
			closedir($handle);	
		}
  }
	
	//get_dir_mod_info
  function get_dir_mod_info ($mod, $type="list")
  {
		global $conf, $vnT;
    $dir_info = array();
    $dir_info['files'] = 0;
    $dir_info['size'] = 0; 
		
		if($mod=="main")
		{
			$handle = opendir($this->dir_cache);
			while (($file = readdir($handle)) != false) 
			{
				if (($file!=".") && ($file!="..") && ($file != ".htaccess") && ($file != "index.html")) {
					 if(strstr($file,"main_")){
							$dir_info['files'] ++;
        			$dir_info['size'] += filesize($this->dir_cache . $file); 
					 }					
				}				
			}
			closedir($handle);	
			
		}elseif ( $mod=="other")	{
			
			$handle = opendir($this->dir_cache);
			while (($file = readdir($handle)) != false) 
			{
				if (($file!=".") && ($file!="..") && ($file != ".htaccess") && ($file != "index.html")) {
				 if(!strstr($file,"main_") && !is_dir($this->dir_cache . $file)){
						$dir_info['files'] ++;
        		$dir_info['size'] += filesize($this->dir_cache . $file); 
				 }					
				}				
			}
			closedir($handle);	
			
		}else	{
			$dir_cache_mod = $this->dir_cache.$mod."/" ;
			$handle = opendir($dir_cache_mod);
			while (($file = readdir($handle)) != false) 
			{
				if (($file!=".") && ($file!="..") && ($file != ".htaccess") && ($file != "index.html")) 
				{
					if($type=="detail") 
					{
						 if(strstr($file,"detail_")){
							$dir_info['files'] ++;
        			$dir_info['size'] += filesize($dir_cache_mod . $file); 
					 	 }
					}else{						
						if(!strstr($file,"detail_")){
							$dir_info['files'] ++;
        			$dir_info['size'] += filesize($dir_cache_mod . $file); 
					 	 }
					}
				}
				
			}
			closedir($handle);	
		}
		 
    return $dir_info;
  }
 
  // end cache
}
?>