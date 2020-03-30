<?php
error_reporting( E_CORE_ERROR | E_CORE_WARNING | E_COMPILE_ERROR | E_ERROR | E_WARNING | E_PARSE | E_USER_ERROR | E_USER_WARNING | E_RECOVERABLE_ERROR );
@ini_set("display_errors", "0");
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
$vnT->DB = $DB = new DB();
//Functions
include ($conf['rootpath'] . 'includes/class_functions.php');
include($conf['rootpath'] . 'includes/admin.class.php');
$vnT->func = $func  = new Func_Admin;
$vnT->conf = $conf = $func->fetchDbConfig($conf);


$vnT->setting['arr_ext_image'] = array("jpg","jpeg","gif","png","bmp","tif","tiff","jfif","ico","svg");

function file_safe_name($text)
{
  global $conf,$func,$DB ;
  $text = $func->utf8_to_ascii($text);
  $text = str_replace(array(' ', '-'), array('_','_'), $text) ;
  $text = preg_replace('/[^A-Za-z0-9_]/', '', $text) ;
  return $text ;
}
function getExt($file) {
  $dot = strrpos($file, '.') + 1;
  return substr($file, $dot);
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

function get_folder_id ($folder_path)
{
  global $vnT;
  $folder_id = 0 ;
  $res_ck = $vnT->DB->query("SELECT folder_id  FROM media_folders WHERE folder_path='".$folder_path."' " )	;
  if($row_ck = $vnT->DB->fetch_row($res_ck)){
    $folder_id  = $row_ck['folder_id']	;
  }

  return $folder_id ;
}


$folder_id = (int)$_REQUEST["folder_id"] ;
$folder_path	= $_REQUEST["folder_path"] ;

$w = 1500 ;
$thumb = 1 ;
$w_thumb = 200 ;


// Settings
$targetDir = $conf['rootpath'] . "vnt_upload";
if($folder_path) {
  $targetDir .= "/".$folder_path ;
}

if($folder_id==0){
  $folder_id = get_folder_id($folder_path) ;
}

$cleanupTargetDir = true; // Remove old files
$maxFileAge = 5 * 3600; // Temp file age in seconds

// 5 minutes execution time
@set_time_limit(5 * 60);

// Uncomment this one to fake upload time
// usleep(5000);

// Get parameters
$chunk = isset($_REQUEST["chunk"]) ? intval($_REQUEST["chunk"]) : 0;
$chunks = isset($_REQUEST["chunks"]) ? intval($_REQUEST["chunks"]) : 0;
$name = isset($_REQUEST["name"]) ? $_REQUEST["name"] : '';

$type = strtolower(substr($name,strrpos($name,".")+1));
$src_name = substr( $name,0,strrpos($name,".") );
$fileName =  file_safe_name($src_name).".".$type;

// Clean the fileName for security reasons
//$fileName = file_safe_name($fileName) ;
// preg_replace('/[^\w\._]+/', '_', $fileName);


// Make sure the fileName is unique but only if chunking is disabled
if ( file_exists($targetDir . DIRECTORY_SEPARATOR . $fileName)) {
  $ext = strrpos($fileName, '.');
  $fileName_a = substr($fileName, 0, $ext);
  $fileName_b = substr($fileName, $ext);

  $count = 1;
  while (file_exists($targetDir . DIRECTORY_SEPARATOR . $fileName_a . '_' . $count . $fileName_b))
    $count++;

  $fileName = $fileName_a . '_' . $count . $fileName_b;
}

$filePath = $targetDir . DIRECTORY_SEPARATOR . $fileName;

// Create target dir
if (!file_exists($targetDir))
  @mkdir($targetDir);

// Remove old temp files
if ($cleanupTargetDir) {
  if (is_dir($targetDir) && ($dir = opendir($targetDir))) {
    while (($file = readdir($dir)) !== false) {
      $tmpfilePath = $targetDir . DIRECTORY_SEPARATOR . $file;

      // Remove temp file if it is older than the max age and is not the current file
      if (preg_match('/\.part$/', $file) && (filemtime($tmpfilePath) < time() - $maxFileAge) && ($tmpfilePath != "{$filePath}.part")) {
        @unlink($tmpfilePath);
      }
    }
    closedir($dir);
  } else {
    die('{"jsonrpc" : "2.0", "error" : {"code": 100, "message": "Failed to open temp directory."}, "id" : "id"}');
  }
}

// Look for the content type header
if (isset($_SERVER["HTTP_CONTENT_TYPE"]))
  $contentType = $_SERVER["HTTP_CONTENT_TYPE"];

if (isset($_SERVER["CONTENT_TYPE"]))
  $contentType = $_SERVER["CONTENT_TYPE"];

// Handle non multipart uploads older WebKit versions didn't support multipart in HTML5
if (strpos($contentType, "multipart") !== false) {
  if (isset($_FILES['file']['tmp_name']) && is_uploaded_file($_FILES['file']['tmp_name'])) {
    // Open temp file
    $out = @fopen("{$filePath}.part", $chunk == 0 ? "wb" : "ab");
    if ($out) {
      // Read binary input stream and append it to temp file
      $in = @fopen($_FILES['file']['tmp_name'], "rb");

      if ($in) {
        while ($buff = fread($in, 4096))
          fwrite($out, $buff);
      } else
        die('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "Failed to open input stream."}, "id" : "id"}');
      @fclose($in);
      @fclose($out);
      @unlink($_FILES['file']['tmp_name']);

    } else
      die('{"jsonrpc" : "2.0", "error" : {"code": 102, "message": "Failed to open output stream."}, "id" : "id"}');
  } else
    die('{"jsonrpc" : "2.0", "error" : {"code": 103, "message": "Failed to move uploaded file."}, "id" : "id"}');
} else {
  // Open temp file
  $out = @fopen("{$filePath}.part", $chunk == 0 ? "wb" : "ab");
  if ($out) {
    // Read binary input stream and append it to temp file
    $in = @fopen("php://input", "rb");
    if ($in) {
      while ($buff = fread($in, 4096))
        fwrite($out, $buff);
    } else
      die('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "Failed to open input stream."}, "id" : "id"}');

    @fclose($in);
    @fclose($out);
  } else
    die('{"jsonrpc" : "2.0", "error" : {"code": 102, "message": "Failed to open output stream."}, "id" : "id"}');
}

// Check if file has been uploaded
if (!$chunks || $chunk == $chunks - 1) {
  // Strip the temp .part suffix off
  rename("{$filePath}.part", $filePath);

  // Custom UPLOAD
  $file_ext = strtolower(getExt($fileName));


  if( in_array($file_ext,$vnT->setting['arr_ext_image']) ){
    $file_type = "image"	;
  }elseif ($file_ext == "swf") {
    $file_type = "flash"	;
  }else{
    $file_type = "file"	;
  }

  $file_size = @filesize($filePath);
  if($file_type == "image" || $file_type == "flash") {
    @list($file_width, $file_height) = @getimagesize($filePath);
  }

  if($file_type=="image")	{
    $path_thumb = $conf['rootpath'] . "vnt_upload/".$folder_path."/thumbs";
    if(!file_exists($path_thumb)) {
      @mkdir($path_thumb, 0777, true);
    }

    $file_thumb = $path_thumb."/" . $fileName   ;
    $func->thum ($filePath, $file_thumb, $w_thumb);
    if($file_ext=="ico" || $file_ext=="svg"){
      @copy($filePath,$file_thumb);
    }
  }

  //insert databse
  $file_src = ($folder_path) ? $folder_path."/".$fileName : $fileName;
  $title = substr( $fileName,0,strrpos($fileName,".") );
  $title = str_replace("_"," ",$title);



  $cot['module']	= $module;
  $cot['folder_id'] = $folder_id;
  $cot['file_type'] = $file_type;
  $cot['file_ext'] = $file_ext;
  $cot['file_name'] = $fileName;
  $cot['file_src'] = $file_src;
  $cot['file_size'] = $file_size;
  $cot['file_width'] = $file_width;
  $cot['file_height'] = $file_height;
  $cot['date_post'] = time();
  $cot['poster'] = 1;

  $kq = $DB->do_insert("media_files", $cot);
  if($kq){
    $file_id = $DB->insertid();
    update_info_folder("add",$folder_id,$file_size);
    die($fileName);
  }else{
    die('ERROR_Không insert database được');
  }

  //END custom
}else{
  die('ERROR_chunks error');
}
