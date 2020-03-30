<?php
function NDK_decode ($t)
{
  global $ndkshop;
  $code = trim($t);
  $code = substr($code, 0, 3) . substr($code, strlen($code) - 5, 3) . substr($code, 3, strlen($code) - 8) . substr($code, strlen($code) - 2);
  $code = substr($code, strlen($code) - 7, 5) . substr($code, 0, strlen($code) - 7) . substr($code, strlen($code) - 2);
  $code = base64_decode($code);
  return $code;
}
$text = NDK_decode($_GET["code"]);
$w = strlen($text) * 10;
$h = 22;
$img = imagecreatetruecolor($w, $h);
$white = imagecolorallocate($img, 200, 200, 200);
imagefill($img, 1, 1, $white);
$size = 13;
$font = "../includes/font.ttf";
//	$textcolor = imagecolorallocate($img,255,25,25);
//	imagettftext($img,$size-7, 0, 85,17, $textcolor, $font, "vnTRUST"); 
//	$textcolor = imagecolorallocate($img,100,100,100);
//	$rand1=rand(5,10);
//	imagettftext($img,$size+10,-$rand1, 20,35, $textcolor, $font, $text); 
$textcolor = imagecolorallocate($img, 32, 12, 0);
imagettftext($img, $size, 3, 7, 18, $textcolor, $font, $text);
header('Content-type: image/jpeg');
imagejpeg($img);
?>