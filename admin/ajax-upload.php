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
require_once ("../_config.php");
include ($conf['rootpath'] . "includes/class_db.php");
include ($conf['rootpath'] . "includes/class_functions.php"); 	
$DB = new DB;
$func = new Func_Global;


function file_safe_name($text)
{
	global $conf,$func,$DB ;
	$text = $func->utf8_to_ascii($text);
	$text = str_replace(array(' ', '-'), array('_','_'), $text) ;
	$text = preg_replace('/[^A-Za-z0-9_]/', '', $text) ;
	return $text ;							
}
function get_info_image( $img )
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
 
$admin_user = $_POST["auth_user"] ;
$admin_pass = $_POST["auth_pass"] ;
$module  = ($_POST['module']) ? $_POST['module'] : '' ;
$folder = $_POST["folder"] ;
$folder_upload	= $_POST["folder_upload"] ;

$w = ($_POST['w']) ? $_POST['w'] : 1000 ;
$crop = $_POST['crop'] ;
$thumb = $_POST['thumb']; 
$w_thumb = ($_POST['w_thumb']) ? $_POST['w_thumb'] : 200 ;
$media = ($_POST['media']) ? $_POST['media'] : 0 ;
 
// Settings
$targetDir = ($folder_upload) ? $folder_upload : $conf['rootpath'] . "vnt_upload/File";
$folder_id = 0;
if($folder){
  $folder_path = ($module) ? $module."/".$folder : "File/".$folder;
}else{
  $folder_path = ($module) ? $module : "File";
}

$res_folder = $DB->query("SELECT folder_id FROM media_folders WHERE folder_path='".$folder_path."' ");
if($row_folder = $DB->fetch_row($res_folder)){
	$folder_id = $row_folder['folder_id']	;
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
	$info_img = @get_info_image($filePath);
	$file_size = @filesize($filePath) ;
	$file_ext = $info_img['ext'];

 	// Custom UPLOAD
	if($thumb==1)	{ 		
		$file_thumb =  $folder_upload."/thumbs/" . $fileName   ; 
		$func->thum ($filePath, $file_thumb, $w_thumb);					 		 
	} 
		

	$file_src = ($folder) ? $folder."/".$fileName : $fileName;
  $picture = $file_src ;
	$title = substr( $fileName,0,strrpos($fileName,".") );
	$title = str_replace("_"," ",$title);

	//insert databse
	if($media){
		$cot_f = array();
		$cot_f['module'] = $module;
		$cot_f['folder_id'] = $folder_id;
		$cot_f['file_type'] = "image";
		$cot_f['file_name'] = $fileName;
		$cot_f['file_src'] = $folder_path."/".$fileName;
		$cot_f['file_size'] = $file_size;
		$cot_f['file_ext'] = $info_img['ext'];
		$cot_f['file_width'] = $info_img['width'];
		$cot_f['file_height'] = $info_img['height'];
		$cot_f['date_post'] = time();
		$cot_f['poster'] = 1;
		$DB->do_insert("media_files",$cot_f);
	}
	
	die('{"jsonrpc" : "2.0", "picture" : "'.$picture.'", "file_src" : "'.$file_src.'"  ,  "file_name" : "'.$fileName.'" ,  "title" : "'.$title.'" }');
	//END custom
			
}
die('{"jsonrpc" : "2.0", "result" : null, "id" : "id"}');
