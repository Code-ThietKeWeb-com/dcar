<?php
$vnTRUST_func=new func();
class func{


// Strat func
function txt_HTML($t=""){
//	$t = addslashes($t);
//	$t = preg_replace("/&(?!#[0-9]+;)/s", '&amp;', $t );
	$t = str_replace( "<", "&lt;"  , $t );
	$t = str_replace( ">", "&gt;"  , $t );
	$t = str_replace( '"', "&quot;", $t );
	$t = str_replace( "'", '&#039;', $t );
	return $t;
}
// End func

// Strat func
function txt_unHTML($t=""){
	$t = stripslashes($t);
//	$t = nl2br($t);
	$t = preg_replace("/&(?!#[0-9]+;)/s", '&amp;', $t );
	$t = str_replace( "<", "&lt;"  , $t );
	$t = str_replace( ">", "&gt;"  , $t );
	$t = str_replace( '"', "&quot;", $t );
	$t = str_replace( "'", '&#039;', $t );
	return $t;
}
// End func

function vnT_encode($t){
	$t = trim($t);
	$code = base64_encode($t);
	$code = substr($code,5,strlen($code)-7).substr($code,0,5).substr($code,strlen($code)-2);
	$code = substr($code,0,3).substr($code,6,strlen($code)-8).substr($code,3,3).substr($code,strlen($code)-2);
	return $code;
}

function vnT_decode($t){
	$code=trim($t);
	$code = substr($code,0,3).substr($code,strlen($code)-5,3).substr($code,3,strlen($code)-8).substr($code,strlen($code)-2);
	$code = substr($code,strlen($code)-7,5).substr($code,0,strlen($code)-7).substr($code,strlen($code)-2);
	$code = base64_decode($code);
	return $code;
}

//========================
function md10($txt) {  // MD10 Encode by NDK
    $txt = md5($txt);
	$txt = base64_encode($txt);
	$txt = md5($txt);
    return $txt;
}

}
?>