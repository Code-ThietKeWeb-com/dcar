<?php
/*================================================================================*\
|| 							Name code : function_media.php 		 			                 				# ||
||  				Copyright © 2008 by Thai Son - CMS vnTRUST                          # ||
\*================================================================================*/
/**
 * @version : 1.0
 * @date upgrade : 28/12/2008 by Thai Son
 **/ 
 
if (! defined('IN_vnT')) {
  die('Hacking attempt!');
}
define('DIR_IMAGE_MEDIA', 'modules/media_ad/images');
define('DIR_MEDIA', '../vnt_upload');
define('URL_MEDIA', $conf['rooturl'] . 'vnt_upload');


$vnT->array_folders = array();
$vnT->array_thumb = array();
$res_dir = $vnT->DB->query("SELECT * FROM media_folders ORDER BY folder_id ASC");
while( $row_dir = $vnT->DB->fetch_row( $res_dir ) )
{
	$vnT->array_folders[$row_dir['folder_path']] = $row_dir['folder_id'];
	if( $row_dir['parentid'] )
	{
		$vnT->array_thumb[$row_dir['folder_path']] = $row_dir;
	} 
}
unset( $vnT->array_folders[''] );

$vnT->setting['arr_ext_image'] = array("jpg","gif","png","jpeg");
$vnT->setting['upload_max_width'] = 2000;
$vnT->setting['upload_thum_width'] = 200;

function getToolbar ($sub = "", $folder_id = "", $lang = "vn")
{
  global $func, $DB, $conf, $vnT;

  $menu = array(
    "add" => array(
      'icon' => "i_add" , 
      'title' => "Add" , 
      'link' => "?mod=media&act=media&sub=add&folder_id=" . $folder_id . "&lang=" . $lang) , 
    "manage" => array(
      'icon' => "i_manage" , 
      'title' => "Manage" , 
      'link' => "?mod=media&act=media&sub=manage&folder_id=" . $folder_id ) , 
    "help" => array(
      'icon' => "i_help" , 
      'title' => "Help" , 
      'link' => "'help/index.php?mod=media&act=media','AdminCPHelp',1000, 600, 'yes','center'" , 
      'newwin' => 1));
  return $func->getMenu($menu);
}


function update_info_folder ($act,$folder_id,$size)
{
	global $vnT;
	$res_ck = $vnT->DB->query("SELECT parentid  FROM media_folders WHERE folder_id=".$folder_id)	;
	if($row_ck = $vnT->DB->fetch_row($res_ck))
	{
		update_info_folder ($act,$row_ck['parentid'],$size)	; 		
	}
	if($act=="del"){
		$vnT->DB->query("UPDATE media_folders SET num_files=num_files-1 , folder_size=folder_size-".$size." WHERE folder_id=".$folder_id);
	}else{
		$vnT->DB->query("UPDATE media_folders SET num_files=num_files+1 , folder_size=folder_size+".$size." WHERE folder_id=".$folder_id);
	}
}

/**
 * is_image()
 *
 * @param mixed $img
 * @return
 */
function is_image( $img )
{
	$typeflag = array();
	$typeflag[1] = array( 'type' => IMAGETYPE_GIF, 'ext' => 'gif' );
	$typeflag[2] = array( 'type' => IMAGETYPE_JPEG, 'ext' => 'jpg' );
	$typeflag[3] = array( 'type' => IMAGETYPE_PNG, 'ext' => 'png' );
	$typeflag[4] = array( 'type' => IMAGETYPE_SWF, 'ext' => 'swf' );
	$typeflag[5] = array( 'type' => IMAGETYPE_PSD, 'ext' => 'psd' );
	$typeflag[6] = array( 'type' => IMAGETYPE_BMP, 'ext' => 'bmp' );
	$typeflag[7] = array( 'type' => IMAGETYPE_TIFF_II, 'ext' => 'tiff' );
	$typeflag[8] = array( 'type' => IMAGETYPE_TIFF_MM, 'ext' => 'tiff' );
	$typeflag[9] = array( 'type' => IMAGETYPE_JPC, 'ext' => 'jpc' );
	$typeflag[10] = array( 'type' => IMAGETYPE_JP2, 'ext' => 'jp2' );
	$typeflag[11] = array( 'type' => IMAGETYPE_JPX, 'ext' => 'jpf' );
	$typeflag[12] = array( 'type' => IMAGETYPE_JB2, 'ext' => 'jb2' );
	$typeflag[13] = array( 'type' => IMAGETYPE_SWC, 'ext' => 'swc' );
	$typeflag[14] = array( 'type' => IMAGETYPE_IFF, 'ext' => 'aiff' );
	$typeflag[15] = array( 'type' => IMAGETYPE_WBMP, 'ext' => 'wbmp' );
	$typeflag[16] = array( 'type' => IMAGETYPE_XBM, 'ext' => 'xbm' );

	$imageinfo = array();
	$file = @getimagesize( $img );
	if( $file )
	{
		$imageinfo['src'] = $img;
		$imageinfo['width'] = $file[0];
		$imageinfo['height'] = $file[1];
		$imageinfo['mime'] = $file['mime'];
		$imageinfo['type'] = $typeflag[$file[2]]['type'];
		$imageinfo['ext'] = $typeflag[$file[2]]['ext'];
		$imageinfo['bits'] = $file['bits'];
		$imageinfo['channels'] = isset( $file['channels'] ) ? intval( $file['channels'] ) : 0;
	}

	return $imageinfo;
}

function getExt($file) {
	$dot = strrpos($file, '.') + 1;
	return substr($file, $dot);
}

function get_src_thumb ($picture)	
{ 
	$dir = substr($picture, 0, strrpos($picture, "/"));
	$pic_name = substr($picture, strrpos($picture, "/") + 1);
	$src = URL_MEDIA ."/". $dir . "/thumbs/" . $pic_name;
	return 	$src ;
}
/**
 * convertfromBytes()
 *
 * @param integer $size
 * @return
 */
function convertfromBytes( $size )
{
	if( $size <= 0 ) return '0 bytes';
	if( $size == 1 ) return '1 byte';
	if( $size < 1024 ) return $size . ' bytes';

	$i = 0;
	$iec = array( "bytes", "KB", "MB", "GB", "TB", "PB", "EB", "ZB", "YB" );

	while( ( $size / 1024 ) > 1 )
	{
		$size = $size / 1024;
		++$i;
	}

	return number_format( $size, 2 ) . ' ' . $iec[$i];
}

 
/**
 * converttoBytes()
 *
 * @param string $string
 * @return
 */
function converttoBytes( $string )
{
	if( preg_match( '/^([0-9\.]+)[ ]*([b|k|m|g|t|p|e|z|y]*)/i', $string, $matches ) )
	{
		if( empty( $matches[2] ) ) return $matches[1];

		$suffixes = array(
			"B" => 0,
			"K" => 1,
			"M" => 2,
			"G" => 3,
			"T" => 4,
			"P" => 5,
			"E" => 6,
			"Z" => 7,
			"Y" => 8
		);

		if( isset( $suffixes[strtoupper( $matches[2] )] ) ) return round( $matches[1] * pow( 1024, $suffixes[strtoupper( $matches[2] )] ) );
	}

	return false;
}

/**
 * string_to_filename()
 *
 * @param mixed $word
 * @return
 */
function string_to_filename( $word )
{
	global $vnT;
	$word = $vnT->func->utf8_to_ascii( $word );
	$word = preg_replace( '/[^a-z0-9\.\-\_ ]/i', '', $word );
	$word = preg_replace( '/^\W+|\W+$/', '', $word );
	$word = preg_replace( '/[ ]+/', '-', $word );
	return strtolower( preg_replace( '/\W-/', '', $word ) );
}


/**
 * check_allow_upload_dir()
 *
 * @param mixed $dir
 * @return
 */
function check_allow_upload_dir( $dir )
{
	global $vnT;

	$dir = trim( $dir );
	if( empty( $dir ) ) return array();

	$dir = str_replace( "\\", "/", $dir );
	$dir = rtrim( $dir, "/" );
	$arr_dir = explode( "/", $dir );
	$level = array();
  
	$level['view_dir'] = true;
	$level['create_file'] = true;
	$level['rename_file'] = false;
	$level['delete_file'] = false;
	$level['move_file'] = false;
	
 
	return $level;
}
 
/**
 * set_dir_class()
 *
 * @param mixed $array
 * @return void
 */
function set_dir_class( $array )
{
	$class = array( "folder" );
	$menu = true;
	if( ! empty( $array ) )
	{
		foreach( $array as $key => $item )
		{
			if( $item ) $class[] = $key;
			if( $key == 'create_dir' and $item ) $menu = true;
			if( $key == 'rename_dir' and $item ) $menu = true;
			if( $key == 'delete_dir' and $item ) $menu = true;
			if( $key == 'rename_file' and $item ) $menu = true;
			if( $key == 'delete_file' and $item ) $menu = true;
			if( $key == 'move_file' and $item ) $menu = true;
		}
	}

	$class = implode( " ", $class );
	if( $menu ) $class .= " menu";
	return $class;
}

/**
 * viewdirtree()
 *
 * @param mixed $dir
 * @param mixed $cur_folder
 * @return
 */
function viewdirtree( $dir, $cur_folder )
{
	global $vnT  ;

 	$pattern = ! empty( $dir ) ? "/^(" . $vnT->func->vnt_preg_quote( $dir ) . ")\/([^\/]+)$/" : "/^([^\/]+)$/";
	$_dirlist = preg_grep( $pattern, array_keys( $vnT->array_folders ) );
 
	 //echo "<br>cur_folder = ".$cur_folder;
	$content = '';
	foreach( $_dirlist as $_dir )
	{
		$check_allow_upload_dir = check_allow_upload_dir( $_dir );

		//if( ! empty( $check_allow_upload_dir ) )
		//{
			//echo "<br>_dir = ".$_dir;
			
			$class_li = ( $_dir == $cur_folder or strpos( $cur_folder, $_dir . '/' ) !== false ) ? "open collapsable" : "expandable";
			$style_color = ( $_dir == $cur_folder ) ? ' style="color:red"' : '';
			
			$tree = array();
			$tree['class1'] = $class_li;
			$tree['class2'] = set_dir_class( $check_allow_upload_dir ) . " pos" . string_to_filename( $dir );
			$tree['style'] = $style_color;
			$tree['title'] = $_dir;
			$tree['titlepath'] = basename( $_dir );

			$list = viewdirtree( $_dir, $cur_folder );
 
			$content .= '<li class="'.$tree['class1'].'">
	<span '.$tree['style'].' class="'.$tree['class2'].'" title="'.$tree['title'].'"> &nbsp;'.$tree['titlepath'].'</span>
	<ul>'.$list.'</ul>
</li>';
		//}
	}

	return $content;
}


/**
 * list_filetype()
 *
 */
function list_option_filetype( $did )
{
  global $vnT  ;
  $arr_filetype = array( 'file' => $vnT->lang['type_file'] , 'image' => $vnT->lang['type_image'] , 'flash' => $vnT->lang['type_flash'] ) ;
  $text ='';
  foreach ($arr_filetype as $key => $value) {
    $selected = ($did==$key) ? ' selected ' : '';
    $text .='<option value="'.$key.'" '.$selected.' >'.$value.'</option>';
  }
  return $text;
}



?>