<?php
/*
Plugin Name: Let It Snow!
*/

$res_p = $vnT->DB->query("SELECT * FROM plugins where name='snow' ");
if($row_p = $DB->fetch_row($res_p))
{
	$folder = $row_p['folder'];
	$params = unserialize($row_p['params']);
	$imgfolder = ($params['imgfolder']) ? $params['imgfolder']  : "snow" ;
	$usePNG = ($params['usePNG']) ? "true"  : "false" ;
	$flakeTypes = ($params['flakeTypes']) ? (int)$params['flakeTypes']  : 6 ;
	$flakesMax = ($params['flakesMax']) ? (int)$params['flakesMax']  : 60 ;
	$vMax = ($params['vMax']) ? $params['vMax']  : 2.5 ;
	$flakeWidth = ($params['flakeWidth']) ? (int)$params['flakeWidth']  : 5 ;
	$flakeHeight = ($params['flakeHeight']) ? (int)$params['flakeHeight']  : 5 ;
	$snowCollect = ($params['snowCollect']) ? "true"  : "false" ;
	$showStatus = ($params['showStatus']) ? "true"  : "false" ;
}
 
$snowPath = ROOT_URL.'plugins/'.$folder.'/';
$vnT->html->addScriptDeclaration("
			var usePNG = ".$usePNG.";
			var imagePath = '".$snowPath."' + 'image/'+ '".$imgfolder."' +'/'; // relative path to snow images
			var flakeTypes = ".$flakeTypes.";
			var flakesMax = ".$flakesMax.";
			var flakesMaxActive = 60;
			var vMax = ".$vMax.";
			var flakeWidth = ".$flakeWidth.";
			var flakeHeight = ".$flakeHeight.";
			var flakeBottom = null; // Integer for fixed bottom, 0 or null for full-screen snow effect
			var snowCollect = ".$snowCollect.";
			var showStatus = ".$showStatus.";
		");

if($imgfolder=="snow"){
	$vnT->html->addScript($snowPath."animate.js");
}
$vnT->html->addScript($snowPath."snowstorm.js");
$vnT->html->addStyleDeclaration(" #vnt-footer{ padding-bottom:50px;	}	" );

$vnT->html->addScriptDeclaration('
	jQuery(window).ready(function () { jQuery("body").append("<div style=\'width:100%;height:66px;background:url('.$snowPath.'image/bg_footer_'.$imgfolder.'.png) center center no-repeat;position:fixed;bottom:0px;z-index:5\' ></div><div ><img src='.$snowPath.'image/left_'.$imgfolder.'.png	 style=\'position: fixed;z-index:100;bottom:0px; left:0;\' /><img  src='.$snowPath.'image/right_'.$imgfolder.'.png	style=\'position: fixed;z-index:100;bottom:0px;  right:0;\' /></div>")});
		');


?>