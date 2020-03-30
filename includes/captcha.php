<?php
error_reporting( E_CORE_ERROR | E_CORE_WARNING | E_COMPILE_ERROR | E_ERROR | E_WARNING | E_PARSE | E_USER_ERROR | E_USER_WARNING | E_RECOVERABLE_ERROR );
@ini_set("display_errors", "0");
session_start();
define(ANIM_FRAMES, 6);
define(ANIM_DELAYS, 20);

$num = ($_GET['num']) ? $_GET['num'] : 6 ;
mt_srand((double) microtime() * 1000000);
$maxran = 1000000;
$random_num = mt_rand(1, $maxran);
$datekey = date('F j');
$rcode = strtoupper(md5( $random_num . $datekey));
$code = substr($rcode, 2, $num);

$_SESSION['sec_code'] = $code;

header('Content-type: image/gif');
$w = (isset($_GET['w'])) ? $_GET['w'] : 90;
$h = (isset($_GET['h'])) ? $_GET['h'] : 30;
$size = (isset($_GET['size'])) ? $_GET['size'] : 20;
$x = rand(10, 15);
$y = ($h/2)+ ($size/2) ;

$font = "../includes/font.ttf";
include "GIFEncoder.class.php";

for ($i = 0; $i < ANIM_FRAMES; $i ++) {
  $image = imagecreatetruecolor($w, $h);
  imagefill($image, 1, 1, imagecolorallocate($image, 0, 0, 0));

  $textcolor = imagecolorallocate($image, 25, 25, 25);
  imagettftext($image, $size, 0, $x, $y, $textcolor, $font, "vnTRUST");

  $textcolor = imagecolorallocate($image, 100, 100, 100);
  imagettftext($image, ($size+1)  , - rand(5, 20), rand(10, 15), $y, $textcolor, $font, $code);

  $textcolor = imagecolorallocate($image, 246, 255, 0);
  imagettftext($image, $size  , 0, $x, rand($y, $y+5) , $textcolor, $font, $code);

  // Save Image to Frame
  Ob_Start();
  imageGif($image);
  imageDestroy($image);
  $f_arr[] = Ob_Get_Contents();
  $d_arr[] = ANIM_DELAYS;
  Ob_End_Clean();
  // End
}
$GIF = new GIFEncoder($f_arr, $d_arr, 0, 2, 0, 0, 0, "bin");
echo $GIF->GetAnimation();
// End	
?>
