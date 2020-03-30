<?php  
// HTTP headers for no cache etc
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

session_start();
define('IN_vnT', 1); 
define('DS', DIRECTORY_SEPARATOR);
require_once ("../../../../_config.php");
include ($conf['rootpath'] . "includes/class_db.php");
include ($conf['rootpath'] . "includes/class_functions.php"); 	
$DB = new DB;
$func = new Func_Global;

$gid = (int)$_GET['gid'];

$html = '<div class="vnt_gallery_zoom"><div class="ck_zoom_list_img"><div class="ck_row">';

$res_pic = $DB->query("SELECT * FROM media_files WHERE gid=".$gid." ORDER BY display_order ASC , file_id ASC" );
$stt  = 0;
while ($row_pic = $DB->fetch_row($res_pic)) {
	$stt++;
	$title = $func->HTML($row_pic['file_name']);
	$src = $conf['rooturl']."vnt_upload/".$row_pic['file_src'] ;

	$dir = substr($row_pic['file_src'], 0, strrpos($row_pic['file_src'], "/"));
	$pic_name = substr($row_pic['file_src'], strrpos($row_pic['file_src'], "/") + 1);
	$src_thumb = $conf['rooturl']."vnt_upload/". $dir . "/thumbs/" . $pic_name;

	$html .='<div class="ck_xs_1"><div class="ck_zoom_item" data-img="'.$src.'"><img alt="" src="'.$src.'" /><span class="ck_desc_img">'.$title.'</span></div></div>';

}
$html .= '</div></div></div>';

echo $html ;

$DB->close();
?>