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
	var $action = "media";
  /**
   * function vntModule ()
   * Khoi tao 
   **/
  function vntModule ()
  {
    global $Template, $vnT, $func, $DB, $conf;
    require_once ("function_media.php");
    $this->skin = new XiTemplate(DIR_MODULE . DS . "media_ad" . DS . "html" . DS . "media.tpl");
    $this->skin->assign('PATH_ROOT', $conf['rootpath']);
    $this->skin->assign('LANG', $vnT->lang);
    $this->skin->assign('DIR_IMAGE', $vnT->dir_images);
    $this->skin->assign('DIR_JS', $conf['rooturl'] . "js");
    $this->skin->assign("admininfo", $vnT->admininfo);
    $this->skin->assign('DIR_IMAGE_MEDIA', DIR_IMAGE_MEDIA);
    $lang = ($vnT->input['lang']) ? $lang = $vnT->input['lang'] : $func->get_lang_default();
		
		$folder_id = ($_GET['folder_id']) ? $_GET['folder_id'] : 0 ;
    $this->linkUrl = "?mod=media&act=media";
    switch ($vnT->input['sub']) {
      case 'add':
        $nd['f_title'] = $vnT->lang['add_media'];
        $nd['content'] = $this->do_Add($lang);
      break;
      case 'del_folder':
        $this->do_Del_Folder($lang);
      break;
      case 'del':
        $this->do_Del($lang);
      break;
      default:
        $nd['f_title'] = $vnT->lang['manage_media'];
        $nd['content'] = $this->do_Manage($lang);
      break;
    }
    $nd['menu'] =  getToolbar($vnT->input['sub'], $folder_id, $lang);
    $Template->assign("data", $nd);
    $Template->parse("box_main");
    $vnT->output .= $Template->text("box_main");
  }

  /**
   * function do_Add 
   * 
   **/
  function do_Add ($lang)
  {
    global $vnT, $func, $DB, $conf;
		$vnT->html->addStyleSheet($vnT->dir_js . "/auto_suggest/autosuggest.css");
		$vnT->html->addScript($vnT->dir_js . "/auto_suggest/bsn.AutoSuggest.js");	
		$vnT->html->addStyleSheet($vnT->dir_js . "/plupload/jquery.plupload.queue/css/jquery.plupload.queue.css");
    $vnT->html->addScript($vnT->dir_js . "/plupload/browserplus-min.js");
    $vnT->html->addScript($vnT->dir_js . "/plupload/plupload.full.js");
    $vnT->html->addScript($vnT->dir_js . "/plupload/i18n/vi.js");
    $vnT->html->addScript($vnT->dir_js . "/plupload/jquery.plupload.queue/jquery.plupload.queue.js");
		
    
		$folder_id = ((int) $vnT->input['folder_id'] ) ? $vnT->input['folder_id'] : 0 ;
		$folder_upload = $conf['rootpath']."vnt_upload" ;		
		$cur_folder = '';
		if($folder_id){
			$result = $vnT->DB->query("SELECT * FROM media_folders WHERE folder_id=".$folder_id)	;
			if($row = $vnT->DB->fetch_row($result))
			{
				$cur_folder = $row['folder_path']	;
				$folder_upload .= "/". $cur_folder ;
				
				if (! file_exists($folder_upload)) {
					 $err = $func->html_err("Thư mục <b>{$folder_upload}</b> không tồn tại");
				}
			
			}else{
				$err = $func->html_err("Thư mục <b>{$folder_upload}</b> không tồn tại");	
			}
		} 	 

		$data['w_pic'] = $vnT->setting['upload_max_width'];
		$data['w_thumb'] = $vnT->setting['upload_thum_width'] ;
		$data['folder_id'] = $folder_id ;
		$data['folder_upload'] = $folder_upload ;
		$data['folderpath'] =   DIR_MEDIA ;
		$data['folder'] = $cur_folder ;
    $data['max_upload'] = ini_get('upload_max_filesize');
		
    $data['err'] = $err;
    $data['link_action'] = $this->linkUrl . "&folder_id=".$folder_id;
    /*assign the array to a template variable*/
    $this->skin->assign('data', $data);
    $this->skin->parse("edit");
    return $this->skin->text("edit");
  }

  /**
   * function do_Del_Folder 
   * 
   **/
  function do_Del_Folder ($lang)
  {
    global $func, $DB, $conf, $vnT;
    $id = $_GET['id'];

    $vnT->func->include_libraries('vntrust.base.object');
		$vnT->func->include_libraries('vntrust.filesystem.path');
		$vnT->func->include_libraries('vntrust.filesystem.folder');
		$vnT->func->include_libraries('vntrust.filesystem.file');

		$res_ck = $DB->query("SELECT * FROM media_folders WHERE folder_id=".$id);
		if($row_ck = $DB->fetch_row($res_ck))
		{
			
			$res = $DB->query("SELECT * FROM media_files WHERE folder_id =" . $row_ck['folder_id']);
			while ($row = $DB->fetch_row($res))  
			{
				$file_src = str_replace(array("../","./",".."),"",$row['file_src']);
				$file_src = str_replace(".htaccess","",$file_src);				
				$path_filename = DIR_MEDIA . '/' . $file_src; 
				if( is_file( $path_filename ) )
				{
					 @unlink ($path_filename);					 
				}	 			 
				$DB->query("DELETE FROM media_files WHERE  file_id=".$row['file_id']."");						 			
			}					 
			
			if($row_ck['parentid']>0){
				$DB->query("UPDATE media_folders SET num_files=num_files=".$row_ck['num_files']." , folder_size=folder_size-".$row_ck['folder_size']." WHERE  folder_id=".$row_ck['parentid']);						 			
			}
			
			
			$DB->query("DELETE FROM media_folders WHERE  folder_id=".$id);	
			
			$folder_path = 	str_replace(array("../","./",".."),"",$row_ck['folder_path']);
			$folder_del =  DIR_MEDIA . "/" . $folder_path;
			$ok = vnT_Folder::delete($folder_del);
			
			$func->insertlog("Del Folder", $_GET['act'], $id);
      $mess = $vnT->lang["del_success"];
						 			
		}	else{
			 $mess = $vnT->lang["del_failt"];	
		} 
		
    $url =  $this->linkUrl;
    $func->html_redirect($url, $mess);
  }

  /**
   * function do_Del 
   * 
   **/
  function do_Del ($lang)
  {
    global $func, $DB, $conf, $vnT;
    $id = (int) $vnT->input['id'];
    $ext = $vnT->input["ext"];
    $del = 0;
    $qr = "";
    if ($id != 0) {
      $ids = $id;
    }
    if (isset($vnT->input["del_id"])) {
      $ids = implode(',', $vnT->input["del_id"]);
    }
    
		$res = $DB->query("SELECT * FROM media_files WHERE file_id IN (" . $ids . ") ");
    if ($DB->num_rows($res))
    {			
      while ($row = $DB->fetch_row($res))  
			{
				$file_src = str_replace(array("../","./",".."),"",$row['file_src']);
				$file_src = str_replace(".htaccess","",$file_src);				
				$path_filename = DIR_MEDIA . '/' . $file_src;
				if( is_file( $path_filename ) )
				{
					 @unlink ($path_filename);					 
				}	
				update_info_folder("del",$row['folder_id'],$row['file_size']); 					 
				$DB->query("DELETE FROM media_files WHERE  file_id=".$row['file_id']."");						 			
      }					 
			$func->insertlog("Del File", $_GET['act'], $ids);
      $mess = $vnT->lang["del_success"];
    } else  {
      $mess = $vnT->lang["del_failt"];
    }  
     
    $ext_page = str_replace("|", "&", $ext);
    $url = $this->linkUrl . "&{$ext_page}";
    $func->html_redirect($url, $mess);
  }

  /**
   * function render_row() 
   * 
   **/
  function render_row ($row_info, $lang)
  {
    global $func, $DB, $conf, $vnT, $vnT;
    $row = $row_info;
    // Xu ly tung ROW
    $id = str_replace(".", "_", $row['name']);
    $row_id = "row_" . $id;
    $output['id'] = $id;
    $output['row_id'] = $row_id;
    $link_view = "#";
    $output['name'] = $row['name'];
    if ($row['is_folder'] == 1) {
      $link_del = ($_GET['folder']) ? "javascript:del_item('" . $this->linkUrl . "&sub=del_folder&folder=" . $_GET['folder'] . "&id=" . $row['name'] . "')" : "javascript:del_item('" . $this->linkUrl . "&sub=del_folder&id=" . $row['name'] . "')";
      $link_folder = ($_GET['folder']) ? $this->linkUrl . "&folder=" . $_GET['folder'] . "/" . $row['name'] : $this->linkUrl . "&folder=" . $row['name'];
      $output['check_box'] = "&nbsp;";
      $output['preview'] = "<a href='" . $link_folder . "'><img src=\"" . DIR_IMAGE_MEDIA . "/folder_sm.png\" /></a>";
      $output['dimensions'] = "---";
      $output['size'] = "---";
      $output['name'] = "<strong>" . $row['name'] . "</strong>";
    } else {
      $link_del = ($_GET['folder']) ? "javascript:del_item('" . $this->linkUrl . "&sub=del&folder=" . $_GET['folder'] . "&id=" . $row['name'] . "')" : "javascript:del_item('" . $this->linkUrl . "&sub=del&id=" . $row['name'] . "')";
      $output['preview'] = "<a href='" . $row['path'] . "' class=\"highslide\" onclick=\"return hs.expand(this)\" ><img src=\"" . $row['path'] . "\" width=" . $row['width_16'] . "  height=" . $row['height_16'] . "  /></a>";
      $output['dimensions'] = $row['width'] . " x " . $row['height'];
      if ($row['is_flash'] == 1) {
        $output['preview'] = "<a  href=\"" . $row['path'] . "\"
				onclick=\"return hs.htmlExpand(this, { objectType: 'swf', contentId: 'highslide-html-10',
					allowSizeReduction: false, wrapperClassName: 'highslide-white', outlineType: 'rounded-white',
					outlineWhileAnimating: true, preserveContent: false, objectWidth: 300, objectHeight: 250} )\"
				class=\"highslide\"><img src=\"" . $row['icon_16'] . "\" /></a>";
        $output['dimensions'] = '---';
      }
      if ($row['is_doc'] == 1) {
        $output['preview'] = "<a  href=\"" . $row['path'] . "\" target='_blank'><img src=\"" . $row['icon_16'] . "\" /></a>";
        $output['dimensions'] = '---';
      }
      $output['size'] = $func->format_size($row['size']);
      $output['check_box'] = "<input type=\"checkbox\" name=\"del_id[]\" value=\"{$id}\" class=\"checkbox\" >";
    }
    $output['action'] = "<input name=\"h_id[]\" type=\"hidden\" value=\"{$id}\" />";
    //$output['action'] .= "<a href=\"{$link_del}\"><img src=\"{$vnT->dir_images}/delete.gif\"  alt=\"Delete \"></a>";
    return $output;
  }

  /**
   * function do_Manage() 
   * Quan ly cac gioi thieu
   **/
  function do_Manage ($lang)
  {
    global $vnT, $func, $DB, $conf;
    $folder_id = ($_GET['folder_id']) ? $_GET['folder_id'] : 0;
		
		$link_up= '#';
		if($folder_id>0) 
		{
			$res_parent = $vnT->DB->query("SELECT * FROM media_folders WHERE folder_id=".$folder_id);
			$row_parent = $vnT->DB->fetch_row($res_parent);						
			$link_up =  ($row_parent['parentid']) ? $this->linkUrl."&folder_id=".$row_parent['parentid'] : $this->linkUrl;
			
			$folderpath = $row_parent['folder_path'];
			$parentid = $row_parent['parentid'];
		}else{
			$folderpath = "";
			$parentid = 0;	
		}
		
		
		if (isset($_POST['btnCreate'])) 
		{
			$folder_name = trim($vnT->func->utf8_to_ascii($_POST['foldername']));
			$folder_name = str_replace(" ","_",$folder_name);			
			$folder_path = ($folderpath) ? $folderpath . "/".$folder_name  : $folder_name  ;
			//check folders
			$res_ck = $vnT->DB->query("SELECT * FROM media_folders WHERE folder_path='".$folder_path."' ");
			if(!$row_ck = $vnT->DB->fetch_row($res_ck))
			{
				if( @is_dir( DIR_MEDIA . '/' . $folder_path ) ) 
				{
					 $err = $vnT->lang['folder_exists'] ; 
				}else{
					if($folderpath){
						$n_dir =  $vnT->func->vnt_mkdir(DIR_MEDIA."/".$folderpath , $folder_name,1);	
					}else{
						$n_dir =  $vnT->func->vnt_mkdir(DIR_MEDIA , $folder_name,1);	
					}
					
					$cot['parentid'] = $folder_id;
					$cot['folder_path'] = $folder_path;
					$cot['folder_name'] = $folder_name;
					$cot['date_create'] = time();
					$vnT->DB->do_insert("media_folders",$cot);
					$err = $func->html_mess("Tạo folder thành công");	
				}				 
			}else{
				$err =  $vnT->lang['folder_exists'] ;	
			}       
    }
		
		
		 //upload file
    if (isset($_POST['btnUpload'])) {
      
			if (! empty($_FILES['file_upload']) && ($_FILES['file_upload']['name'] != "")) 
			{				
				$file_ext = strtolower(getExt($_FILES['file_upload']['name']));
								
				if( in_array($file_ext,$vnT->setting['arr_ext_image']) ){
					$file_type = "image"	;
				}elseif ($file_ext = "swf") {
					$file_type = "flash"	;
				}else{
					$file_type = "file"	;
				}  
        $up['path'] = DIR_MEDIA."/";
        $up['dir'] = $folderpath;
        $up['file'] = $_FILES['file_upload'];				
				if($file_type == "image"){
					$up['type'] = "hinh";
          $up['w'] = $vnT->setting['upload_max_width'];
          $up['thum'] = 1; 
          $up['w_thum'] = $vnT->setting['upload_thum_width'];
				} 
				
				
        $result = $vnT->File->Upload($up);				
				
        if (empty($result['err'])) 
				{
					$file_name = $result['link'];
					$file_src = $folderpath."/".$file_name;
					$file_ext =  $result['type'];
					$file_size = $result['size'];					
					
					if($file_type=="image" || $file_type=="flash")
					{
						$tmp_wh = @explode("x",$result['dimension']);					
						$file_width = $tmp_wh[0];
						$file_height = $tmp_wh[1];
					}
					if($file_type=="flash")	{
						list($file_width, $file_height) = @getimagesize(DIR_MEDIA."/".$file_src);
					}					
					
					$cot['module'] = $row_parent['module'];
					$cot['folder_id'] = $folder_id;
					$cot['file_type'] = $file_type;
					$cot['file_ext'] = $file_ext; 
					$cot['file_name'] = $file_name; 
					$cot['file_src'] = $file_src; 
					$cot['file_size'] = $file_size; 					
					$cot['file_width'] = $file_width;
					$cot['file_height'] = $file_height;
					$cot['date_post'] = time();	
					$cot['poster'] = $vnT->admininfo['adminid'];				 
					$ok = $vnT->DB->do_insert("media_files",$cot);
					
					update_info_folder("add",$folder_id,$file_size); 
					$err = $func->html_mess("Upload file thành công");
					
        } else {
          $err = $vnT->func->html_err($result['err']);
        } 
				
      }// if file_upload
    }
		
		
		
	
		
		
		$res_folder = $vnT->DB->query("SELECT * FROM media_folders WHERE display=1 AND parentid={$folder_id} {$where} ORDER BY folder_name ASC ");
		if($num_folder = $vnT->DB->num_rows($res_folder))
		{
			while ($row_folder = $vnT->DB->fetch_row($res_folder))
			{ 
				$row_info = array();
				$link_del =  "javascript:del_item('" . $this->linkUrl . "&sub=del_folder&id=" . $row_folder['folder_id']. "')" ;
     	  $link_folder = $this->linkUrl . "&folder_id=" . $row_folder['folder_id'] ;
 				$row_info['check_box'] = "&nbsp;";
				$row_info['preview'] = "<a href='" . $link_folder . "'><img src=\"" . DIR_IMAGE_MEDIA . "/folder_sm.png\" /></a>";
				$row_info['dimensions'] = "---";
				$row_info['size'] = convertfromBytes($row_folder['folder_size']);
				$row_info['name'] = "<strong>" . $row_folder['folder_name'] . "</strong>";
				$row_info['action'] = "<a href=\"{$link_del}\"><img src=\"{$vnT->dir_images}/delete.gif\"  alt=\"Delete \"></a>";
			
				$this->skin->assign('row', $row_info);
				$this->skin->parse("manage.html_row");	
			}
		}
		
		
		
		$p = ((int) $vnT->input['p']) ?  $vnT->input['p'] : 1;
    $n = ($conf['record']) ? $conf['record'] : 30;
 			
		$query = $DB->query("SELECT file_id FROM media_files WHERE display=1 AND folder_id={$folder_id} {$where}  ");
    $totals = intval($DB->num_rows($query));
    $num_pages = ceil($totals / $n);
    if ($p > $num_pages)      $p = $num_pages;
    if ($p < 1)      $p = 1;
    $start = ($p - 1) * $n;
    $nav = $func->paginate($totals, $n, $ext, $p);
		
		$res_file = $vnT->DB->query("SELECT * FROM media_files WHERE display=1 AND folder_id={$folder_id} {$where} ORDER BY date_post DESC  LIMIT $start,$n");
		if($num_file = $vnT->DB->num_rows($res_file))
		{
			while ($row_file = $vnT->DB->fetch_row($res_file))
			{ 
				$row_info = array();
				$id = $row_file['file_id'] ;
				$link_del =  "javascript:del_item('" . $this->linkUrl . "&sub=del&id=" . $row_file['file_id']. "')" ;
				
 				$row_info['check_box'] = "<input type=\"checkbox\" name=\"del_id[]\" value=\"{$id}\" class=\"checkbox\" >";				
				
				if($row_file['file_type']=="image")
				{
					$src = get_src_thumb ($row_file['file_src'] );
					
					if($row_file['file_width'] > $row_file['file_height'])
					{
						$srcwidth = 32 ;	
						$srcheight = "" ;
					}else{
						$srcwidth = "" ;	
						$srcheight = 32 ;
					}					
					
				}else{
					$iconfile = DIR_IMAGE_MEDIA . "/icon/" . $row_file['file_ext'] . ".png";
					if (file_exists($iconfile)) {
						$src = $iconfile;
					} else {
						$src = DIR_IMAGE_MEDIA."/icon/file.png"; 
					}								
					$srcwidth= 32 ;	
					$srcheight = 32 ;							
				}
				$file_size =  convertfromBytes($row_file['file_size']);
			  $info_file = $row_file['file_name']."|".$row_file['file_width']."|".$row_file['file_height']."|".$file_size."|".@date("d/m/Y",$row_file['date_post']);
				
				$row_info['preview'] = "<a href='javascript:void(0)' onClick=\"file_preview('".$row_file['file_type']."','".ROOT_URI."vnt_upload/".$row_file['file_src']."','".$info_file."')\"><img src=\"" . $src . "\" width='".$srcwidth."' height='".$srcheight."'   /></a>";	 				
				
				$row_info['dimensions'] = ($row_file['file_type']=="image" || $row_file['file_type']=="flash" ) ? $row_file['file_width']."x".$row_file['file_height'] : "---";
				$row_info['size'] = $file_size ;
				$row_info['name'] = "<strong>" . $row_file['file_name'] . "</strong>";
				$row_info['action'] = "<a href=\"{$link_del}\"><img src=\"{$vnT->dir_images}/delete.gif\"  alt=\"Delete \"></a>";
			
				$this->skin->assign('row', $row_info);
				$this->skin->parse("manage.html_row");	
			}
		}
		
		$data['button'] = '<input type="button" name="btnDel" value=" ' . $vnT->lang['delete'] . ' " class="button" onclick="del_selected(\'' . $this->linkUrl . '&sub=del&ext=' . $ext_page . '\')">';
		$data['nav'] = $nav;
		
		$data['folderpath'] = "vnt_upload/".$folderpath ;
		$data['max_upload'] = ini_get('upload_max_filesize');
		$data['link_up'] = $link_up ;
    $data['link_action'] = $this->linkUrl . "&p=$p";
    /*assign the array to a template variable*/
    $this->skin->assign('data', $data);
    $this->skin->parse("manage");
    return $this->skin->text("manage");
  }
  // end class
}
?>



