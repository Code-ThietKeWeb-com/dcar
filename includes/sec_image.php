<?php
error_reporting( E_CORE_ERROR | E_CORE_WARNING | E_COMPILE_ERROR | E_ERROR | E_WARNING | E_PARSE | E_USER_ERROR | E_USER_WARNING | E_RECOVERABLE_ERROR );
define(ANIM_FRAMES, 6);
define(ANIM_DELAYS, 20);
function NDK_decode ($t)
{
  global $ndkshop;
  $code = trim($t);
  $code = substr($code, 0, 3) . substr($code, strlen($code) - 5, 3) . substr($code, 3, strlen($code) - 8) . substr($code, strlen($code) - 2);
  $code = substr($code, strlen($code) - 7, 5) . substr($code, 0, strlen($code) - 7) . substr($code, strlen($code) - 2);
  $code = base64_decode($code);
  return $code;
}
$code = NDK_decode($_GET["code"]);
if (strlen($code) != 6)
  $code = "Code Error!";
header('Content-type: image/gif');
$w = (isset($_GET['w'])) ? $_GET['w'] : 90;
$h = (isset($_GET['h'])) ? $_GET['h'] : 28;
$size = (isset($_GET['size'])) ? $_GET['size'] : 14;
$font = "../includes/font.ttf";
// Gifffffffffffff - NDK - 12/10/2007
include "GIFEncoder.class.php";

for ($i = 0; $i < ANIM_FRAMES; $i ++) {
  $image = imagecreatetruecolor($w, $h);
  imagefill($image, 1, 1, imagecolorallocate($image, 0, 0, 0));
  $textcolor = imagecolorallocate($image, 25, 25, 25);
  imagettftext($image, $size, 0, rand(80, 110), 55, $textcolor, $font, "vnTRUST");
  imagettftext($image, $size, 0, rand(5, 20), rand(30, 40), $textcolor, $font, "vnTRUST");
  $textcolor = imagecolorallocate($image, 100, 100, 100);
  imagettftext($image, $size + 8, - rand(5, 10), rand(10, 15), 25, $textcolor, $font, $code);
  $textcolor = imagecolorallocate($image, 246, 255, 0);
  imagettftext($image, $size + 7, 0, rand(10, 15), rand(25, 27), $textcolor, $font, $code);
  if ($i % 3 != 2) {
    //$logoct = imagecreatefrompng("logo.png");
  //$logow = imagesx($logoct) ; $logoh = imagesy($logoct);
  //imagecopyresampled($image,$logoct,0,$h-$logoh,0,0,$logow,$logoh,$logow,$logoh); 
  }
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
