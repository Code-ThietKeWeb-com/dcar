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
	var $action = "remote";

  /**
   * function vntModule ()
   * Khoi tao 
   **/
  function vntModule ()
  {
    global $Template, $vnT, $func, $DB, $conf;
    require_once ("function_media.php");
    $this->skin = new XiTemplate(DIR_MODULE . DS . "media_ad" . DS . "html" . DS . "remote.tpl");
    $this->skin->assign('PATH_ROOT', $conf['rootpath']);
    $this->skin->assign('LANG', $vnT->lang);		
	  $this->skin->assign('CONF', $vnT->conf);
    $this->skin->assign('DIR_IMAGE', $vnT->dir_images);
	  $this->skin->assign('DIR_STYLE', $vnT->dir_style );		 
    $this->skin->assign('DIR_JS', $conf['rooturl'] . "js");
    $this->skin->assign("admininfo", $vnT->admininfo);
    $this->skin->assign('DIR_IMAGE_MEDIA', DIR_IMAGE_MEDIA); 
  
		
		switch ($_GET['do']) {
			case "createfolder" : $jout =  $this->do_CreateFolder();  break;	   
			case "folderlist" : $jout =  $this->do_folderList();  break;	   
			case "imglist" : $jout =  $this->do_imgList();  break;
			case "upload" : $jout =  $this->do_Upload();  break;	   
			case "renameimg" : $jout =  $this->do_RenameImg();  break;	   
			case "dlimg" : $jout =  $this->do_DownloadImg();  break;	   
			case "delimg" : $jout =  $this->do_DeleteImg();  break;


      case "folder_gallery" : $jout =  $this->do_folderGallery();  break;
      case "gallery" : $jout =  $this->do_Gallery();  break;

			
		}
		flush();
		echo  $jout ;
		exit();
  }
	
	
	//do_CreateFolder
	function do_CreateFolder ()
	{
		global $vnT, $func, $DB, $conf;	
 	 	$path = $_POST['path'];
		$file = $_POST['file'];
		$folder_name = $_POST['newname'];
		
		if( empty( $path ) ) { $error =  $vnT->lang['notlevel'] ; };
		if( empty( $folder_name ) ) { $error =  $vnT->lang['name_nonamefolder'] ; };
 		 
		$parentid = $vnT->array_folders[$path];	 
		$folder_path = $path."/".$folder_name;
		
		//check folders
		$res_ck = $vnT->DB->query("SELECT * FROM media_folders WHERE folder_path='".$folder_path."' ");
		if(!$row_ck = $vnT->DB->fetch_row($res_ck))
		{
			if( @is_dir( DIR_MEDIA . '/' . $folder_path ) ) 
			{
				 $error = $vnT->lang['folder_exists'] ; 
			}else{
				$n_dir =  $vnT->func->vnt_mkdir(DIR_MEDIA."/".$path , $folder_name,1);	
				$cot['parentid'] = $parentid;
				$cot['folder_path'] = $folder_path;
				$cot['folder_name'] = $folder_name;
				$cot['date_create'] = time();
				$vnT->DB->do_insert("media_folders",$cot);
			}
			 
		}else{
			$error =  $vnT->lang['folder_exists'] ;	
		}
		
		if(empty($error))
		{ 			
			$textout = $folder_path;	
		}else{
			$textout = "ERROR_".$error;		
		} 
		
		return $textout; 
		
	}
	
	
	//do_Upload
	function do_Upload ()
	{
		global $vnT, $func, $DB, $conf;	
 	 	$path = $_POST['path'];
		$fileurl = trim($_POST['fileurl']); 
		
		$error = ''; $ok_upload = 1;
		if( ! isset( $_FILES, $_FILES['upload'], $_FILES['upload']['tmp_name'] ) &&  empty($fileurl) )
		{
			$error = $vnT->lang['uploadError1'];
			$ok_upload = 0;
		}
		
		if(! empty($fileurl))
		{
			$ok_upload = 1;	
		}
		
		if($ok_upload)
		{
			$module = "" ;
			$folder_id = $vnT->array_folders[$path];
			
			//echo "path = ".$path;
			//echo "<br>folder_id = ".$folder_id;
			
			$is_image = 0 ;
			if (! empty($_FILES['upload']) && ($_FILES['upload']['name'] != "")) 
			{ 
				$file_ext = strtolower(getExt($_FILES['upload']['name']));
								
				if( in_array($file_ext,$vnT->setting['arr_ext_image']) ){
					$file_type = "image"	;
				}elseif ($file_ext == "swf") {
					$file_type = "flash"	;
				}else{
					$file_type = "file"	;
				} 
        
        $up['path'] = DIR_MEDIA."/";
        $up['dir'] = $path;
        $up['file'] = $_FILES['upload'];				
				if($file_type == "image")
				{
					$up['type'] = "hinh";
          $up['w'] = $vnT->setting['upload_max_width'];
          $up['thum'] = 1; 
          $up['w_thum'] = $vnT->setting['upload_thum_width'];
				} 				
				
        $result = $vnT->File->Upload($up);				
				
        if (empty($result['err'])) {
					$file_name = $result['link'];
					$file_src = $path."/".$file_name;
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
					
        } else {
          $error =$result['err'];
        }
      } 
			
			if(! empty($fileurl))
			{ 
				$file_ext = strtolower(getExt($fileurl));
								
				if( in_array($file_ext,$vnT->setting['arr_ext_image']) ){
					$file_type = "image"	;
				}elseif ($file_ext = "swf") {
					$file_type = "flash"	;
				}else{
					$file_type = "file"	;
				} 
        
        $up['path'] = DIR_MEDIA."/";
        $up['dir'] = $path;
        $up['url'] = $fileurl;
				$up['type'] = "hinh";
				if($file_type == "image"){
					
          $up['w'] = $vnT->setting['upload_max_width'];
          $up['thum'] = 1; 
          $up['w_thum'] = $vnT->setting['upload_thum_width'];
				} 
				
				
        $result = $vnT->File->UploadURL($up);								 
        if (empty($result['err'])) {
					$file_name = $result['link'];
					$file_src = $path."/".$file_name;
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
        } else {
          $error = $result['err'];
        }
			}			
			
			if(empty($error))
			{
				$cot['module'] = $module;
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
			}
		}
		
		if(empty($error))
		{
			$textout = $file_name;	
		}else{
			$textout = "ERROR_".$error;		
		}
			
			
		
		return $textout; 
		
	}
	
	//do_RenameImg
	function do_RenameImg ()
	{
		global $vnT, $func, $DB, $conf;	
 	 	$path = $_POST['path'];
		$file = $_POST['file'];
		$newname = $_POST['newname'];
		$newname = string_to_filename( basename( $newname ) );
		$folder_id = $vnT->array_folders[$path];
		
		if( empty( $newname ) ) $error =  "ERROR_" . $vnT->lang['rename_noname'] ;
		
		//check file
		$res_ck = $vnT->DB->query("SELECT * FROM media_files WHERE folder_id=".$folder_id." AND file_name='".$file."' ");
		if($row_ck = $vnT->DB->fetch_row($res_ck))
		{
		
			$ext = getExt( $file );
			$newname = $newname . "." . $ext; 		
			
			if( $file != $newname )
			{
				$newname2 = $newname;
			
				$i = 1;
				while( file_exists( DIR_MEDIA . '/' . $path . '/' . $newname2 ) )
				{
					$newname2 = preg_replace( '/(.*)(\.[a-zA-Z0-9]+)$/', '\1_' . $i . '\2', $newname );
					++$i;
				}
			
				$newname = $newname2;
				if( @rename( DIR_MEDIA . '/' . $path . '/' . $file, DIR_MEDIA . '/' . $path . '/' . $newname ) ) 
				{
					@rename( DIR_MEDIA . '/' . $path . '/thumbs/' . $file, DIR_MEDIA . '/' . $path . '/thumbs/' . $newname ) ;
					
					$file_src = $path."/".$newname; 
					$vnT->DB->query( "UPDATE media_files SET `file_name` = '" . $newname . "', `file_src` = '" . $file_src . "'  WHERE  file_id=".$row_ck['file_id']);
					$vnT->func->insertlog("Rename File", "media", $row_ck['file_id']);          
					
				}else{
					$error = "ERROR_" . $vnT->lang['errorNotRenameFile'] ;
				}  
				
			}  
		}else{
			$error = "ERROR_" . $vnT->lang['errorNotFindFile'] ;	
		}
		
		if(empty($error))
		{ 			
			$textout = $newname;	
		}else{
			$textout = "ERROR_".$error;		
		} 
		
		return $textout; 
		
	}
	
		//do_DownloadImg
	function do_DownloadImg ()
	{
		global $vnT, $func, $DB, $conf;	
 	 	$path = $_POST['path']; 

		$image = $_POST['img'];  
		$image = basename( $image );

		$path_filename = DIR_MEDIA . '/' . $path . "/" . $image;
		
		if( ! empty( $image ) && is_file( $path_filename ) )
		{
			 
		}
		else
		{
			echo 'file not exist !';
		}
		
	}
	
		//do_DeleteImg
	function do_DeleteImg ()
	{
		global $vnT, $func, $DB, $conf;	
 	 	$path = $_POST['path']; 
		$file_name = $_POST['file'];  
		$file_name = str_replace(array("../","./",".."),"",$file_name);
		$file_name = str_replace(".htaccess","",$file_name);
				
		$folder_id = $vnT->array_folders[$path]; 
		$res_ck = $vnT->DB->query("SELECT file_id,file_size FROM media_files WHERE folder_id=".$folder_id." AND file_name='".$file_name."' ");
		if($row_ck = $vnT->DB->fetch_row($res_ck))
		{
			$path_filename = DIR_MEDIA . '/' . $path . "/" . $file_name;
			if( ! empty( $file_name ) && is_file( $path_filename ) )
			{
				 @unlink ($path_filename);
				 $vnT->DB->query("DELETE FROM media_files WHERE file_id=".$row_ck['file_id']);
				 
				 update_info_folder("del",$folder_id,$row_ck['file_size']); 
				 
				 $vnT->func->insertlog("Delete File", "media", $row_ck['file_id']);          
				 $textout = "OK";
			}
			else{
				$textout = $vnT->lang['errorNotSelectFile'] ;
			}
		}else{
			$textout = $vnT->lang['errorNotSelectFile'];
		}
		
		
		return $textout ;
	}
	
	
	


	//do_folderList
	function do_folderList ()
	{
		global $vnT, $func, $DB, $conf;	
 		
		$path = $_GET['path'] ; 
		$currentpath = $_GET['folder'] ;

				
		$data = array();
		$data['style'] = $path == $currentpath ? " style=\"color:red\"" : "";
		$data['class'] = set_dir_class( $check_allow_upload_dir ) . " pos" . string_to_filename( $path );
		$data['title'] = $path;
		$data['titlepath'] = empty( $path ) ? 'ROOT' : $path;
		$data['content'] = viewdirtree( $path, $currentpath );
		
		 $data['path'] = $path ;
		 $data['foldervalue'] = $currentpath ;
		 $data['view_dir'] = 1 ; 
		 $data['create_dir'] = 1 ; 
		 $data['rename_dir'] = 0 ; 
		 $data['delete_dir'] = 0 ; 
		 $data['upload_file'] = 1 ; 
		 $data['create_file'] = 1 ; 
		 $data['rename_file'] = 1 ;
		 $data['delete_file'] = 0 ; 
		 $data['move_file'] = 0 ; 
		 
		$data['currenttime'] = time();

    $this->skin->assign("data", $data);
    $this->skin->parse("html_folder_list");
    $textout = $this->skin->text("html_folder_list");
    
		return $textout ;
		
	}


	//do_imgList
	function do_imgList ()
	{
		global $vnT, $func, $DB, $conf;	
 		
		$path = trim($_GET['path']) ; 		 
		$type = $_GET['type'] ; 		 
		$order = $_GET['order'] ; 		 
		$q = $_GET['q'] ;
		$selectfile = $_GET['imgfile'];
    $arr_selectfile = @explode("|",$selectfile) ;
		$folder_id = $vnT->array_folders[$path];
		
		$where = ""; 
		switch ($type){
			case "image" :  $where .= " AND file_type='image' "	; break ;
			case "flash" :  $where .= " AND file_type='flash' "	;	break ;
		}
		if($q){
			$where .= " AND file_name like '%".$q."%' "	;	
		}
		
		$order_by= '';
		switch ($order)
		{
			case 1 :  $order_by = " date_post ASC "	; break ;
			case 2 :  $order_by = " file_name ASC ,  date_post DESC"	;	break ;
			default :  $order_by = " date_post DESC "	;	break ;
		}
		$sql = "SELECT * FROM media_files WHERE display=1 AND folder_id=".$folder_id." {$where}  ORDER BY {$order_by}  " ;
		//echo "sql = ".$sql;
		$result = $vnT->DB->query($sql);
		if($num = $vnT->DB->num_rows($result))
		{
			while($file = $vnT->DB->fetch_row($result))
			{
				
					$file['data'] = $file['file_id']."|".$file['file_width']."|" . $file['file_height']."|" . $file['file_ext'] . "|" . $file['file_type'] . "|" . convertfromBytes( $file['file_size'] ) . "|" . $file['poster'] . "|" . @date( "H:i ,d/m/Y", $file['date_post'] ) . "|";
				$file['data'] .= ( empty( $q ) ) ? '' : $file['file_name'];
				
				if( $file['file_type'] == "image" or $file['file_ext'] == "swf" )
				{
					$file['size'] = $file['file_width'] . "x" .$file['file_height']." pixels";
				}
				else
				{
					$file['size'] = convertfromBytes( $file['file_size'] );
				}


        $file['sel'] = '';
        if(@in_array($file['file_name'] ,$arr_selectfile)) {
          $file['sel'] =	" imgsel" ;
        }
				
				$file['title'] = $file['file_name']  ;
				$file_name = substr($file['file_name'], 0,  strrpos($file['file_name'], '.'));

				$file['file_name_short'] = $vnT->func->cut_string($file_name,8,1) .".".$file['file_ext'];
        $file['file_name'] = $file_name .".".$file['file_ext'];
				
				if($file['file_type']=="image")
				{
					$src = get_src_thumb ($file['file_src'] );
					
					if($file['file_width'] > $file['file_height'])
					{
						$file['srcwidth'] = 80 ;	
						$file['srcheight'] = "" ;
					}else{
						$file['srcwidth'] = "" ;	
						$file['srcheight'] = 80 ;
					}					
					
				}else{
					$iconfile = DIR_IMAGE_MEDIA . "/icon/" . $file['file_ext'] . ".png";
					if (file_exists($iconfile)) {
						$src = $iconfile;
					} else {
						$src = DIR_IMAGE_MEDIA."/icon/file.png"; 
					}			
					
					$file['srcwidth'] = 32 ;	
					$file['srcheight'] = 32 ;							
				}

				$file['src'] = $src ;
				
				$this->skin->assign( "IMG", $file );
				$this->skin->parse( 'html_img_list.loopimg' );	
			}
		}
		
		$data['currenttime'] = time();
    $this->skin->assign("data", $data);
    $this->skin->parse("html_img_list");
    $textout = $this->skin->text("html_img_list");
    
		return $textout ;
		
	}



  //do_folderGallery
  function do_folderGallery ()
  {
    global $vnT, $func, $DB, $conf;
    $path = $_GET['path'] ;
    $currentpath = $_GET['folder'] ;


    $data = array();
    $data['style'] = $path == $currentpath ? " style=\"color:red\"" : "";
    $data['class'] = set_dir_class( $check_allow_upload_dir ) . " pos" . string_to_filename( $path );
    $data['title'] = $path;
    $data['titlepath'] = empty( $path ) ? 'ROOT' : $path;
    $data['content'] = viewdirtree( $path, $currentpath );

    $data['path'] = $path ;
    $data['foldervalue'] = $currentpath ;
    $data['view_dir'] = 1 ;
    $data['create_dir'] = 1 ;
    $data['rename_dir'] = 0 ;
    $data['delete_dir'] = 0 ;
    $data['upload_file'] = 1 ;
    $data['create_file'] = 1 ;
    $data['rename_file'] = 0 ;
    $data['delete_file'] = 0 ;
    $data['move_file'] = 0 ;

    $data['currenttime'] = time();


    $this->skin->assign("data", $data);
    $this->skin->parse("html_folder_gallery");
    $textout = $this->skin->text("html_folder_gallery");

    return $textout ;

  }



  //do_Gallery
  function do_Gallery ()
  {
    global $vnT, $func, $DB, $conf;

    $path = trim($_GET['path']) ;
    $type = $_GET['type'] ;
    $order = $_GET['order'] ;
    $q = $_GET['q'] ;
    $selectfile = $_GET['imgfile'];

    $arr_selectfile = @explode("|",$selectfile) ;

    $folder_id = $vnT->array_folders[$path];

    $where = "";
    switch ($type){
      case "image" :  $where .= " AND file_type='image' "	; break ;
      case "flash" :  $where .= " AND file_type='flash' "	;	break ;
    }
    if($q){
      $where .= " AND file_name like '%".$q."%' "	;
    }

    $order_by= '';
    switch ($order)
    {
      case 1 :  $order_by = " date_post ASC "	; break ;
      case 2 :  $order_by = " file_name ASC ,  date_post DESC"	;	break ;
      default :  $order_by = " date_post DESC "	;	break ;
    }
    $sql = "SELECT * FROM media_files WHERE display=1 AND folder_id=".$folder_id." {$where}  ORDER BY {$order_by}  " ;
    //echo "sql = ".$sql;
    $result = $vnT->DB->query($sql);
    if($num = $vnT->DB->num_rows($result))
    {
      while($file = $vnT->DB->fetch_row($result))
      {

        $file['data'] = $file['file_id']."|".$file['file_width']."|" . $file['file_height']."|" . $file['file_ext'] . "|" . $file['file_type'] . "|" . convertfromBytes( $file['file_size'] ) . "|" . $file['poster'] . "|" . @date( "H:i ,d/m/Y", $file['date_post'] ) . "|";
        $file['data'] .= ( empty( $q ) ) ? '' : $file['file_name'];

        if( $file['file_type'] == "image" or $file['file_ext'] == "swf" )
        {
          $file['size'] = $file['file_width'] . "x" .$file['file_height']." pixels";
        }
        else
        {
          $file['size'] = convertfromBytes( $file['file_size'] );
        }

        $file['sel'] = '';
        if(in_array($file['file_name'] ,$arr_selectfile)) {
        	$file['sel'] =	" imgsel" ;
				}

        $file['title'] = $file['file_name']  ;

        $file_name = substr($file['file_name'], 0,  strrpos($file['file_name'], '.'));
        $file['file_name'] = $vnT->func->cut_string($file_name,10,1) .".".$file['file_ext'];


        if($file['file_type']=="image")
        {
          $src = get_src_thumb ($file['file_src'] );

          if($file['file_width'] > $file['file_height'])
          {
            $file['srcwidth'] = 80 ;
            $file['srcheight'] = "" ;
          }else{
            $file['srcwidth'] = "" ;
            $file['srcheight'] = 80 ;
          }

        }else{
          $iconfile = DIR_IMAGE_MEDIA . "/icon/" . $file['file_ext'] . ".png";
          if (file_exists($iconfile)) {
            $src = $iconfile;
          } else {
            $src = DIR_IMAGE_MEDIA."/icon/file.png";
          }

          $file['srcwidth'] = 32 ;
          $file['srcheight'] = 32 ;
        }

        $file['src'] = $src ;

        $this->skin->assign( "IMG", $file );
        $this->skin->parse( 'html_gallery.loopimg' );
      }
    }

    $data['currenttime'] = time();
    $this->skin->assign("data", $data);
    $this->skin->parse("html_gallery");
    $textout = $this->skin->text("html_gallery");

    return $textout ;

  }



	 
  // end class
}
?>
