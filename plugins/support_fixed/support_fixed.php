<?php
/*
Plugin Name: facebook_messenger !
*/
$support_fixed='' ;
$res_p = $vnT->DB->query("SELECT * FROM plugins where name='support_fixed' ");
if($row_p = $vnT->DB->fetch_row($res_p))
{
	$folder = $row_p['folder'];
	$pluginPath = ROOT_URL.'plugins/'.$folder.'/';

	$params = @unserialize($row_p['params']);
	$link_messenger = $params['link_messenger'];
	$link_zalo =  $params['link_zalo'];
  $link_phone =  $params['link_phone'];
	$distance_width = ($params['distance_width']) ? $params['distance_width']  : 30;
	$distance_height = ($params['distance_height']) ? $params['distance_height']  : 30;
	$btn_color = ($params['color']) ? $params['color']  : "#00AEEF";
  $effect_color = ($params['effect_color']) ? $params['effect_color']  : "rgba(0,174,239,0.9)";

  $css_postion = 'bottom:'.$distance_height.'px; ';
  if($params['postion']=="left")
  {
    $css_postion .="left:".$distance_width.'px;';
  }else{
    $css_postion .="right:".$distance_width.'px;';
  }


  $support_fixed .= '<link href="'.$pluginPath.'css/support_fixed.css" rel="stylesheet" type="text/css" />';
  $support_fixed .= '<style >.support-hotline{'.$css_postion.'} .support-hotline .div_title i{background-color:'.$btn_color.';} .support-hotline .div_title span.icon:before{background-color:'.$effect_color.'; } .support-hotline .div_title span.icon:after{border-color:'.$btn_color.' } </style>';
  $support_fixed .= '<script type="text/javascript" src="'.$pluginPath.'js/support_fixed.js"></script>';
  $support_fixed .= '<div class="support-hotline "><div class="div_title"><span class="icon"><i ></i></span></div>';
  $support_fixed .= ' <div class="div_content"><ul>';
  if($link_phone){
    $support_fixed .= '<li><a href="'.$link_phone.'" target="_blank" title="Phone"><span class="icon-phone"></span></a></li>';
  }
  if($link_messenger){
    $support_fixed .= '<li><a href="'.$link_messenger.'" target="_blank" title="Facebook"><span class="icon-facebook"></span></a></li>';
  }
  if($link_zalo){
    $support_fixed .= '<li><a href="'.$link_zalo.'" target="_blank" title="Zalo"><span class="icon-zalo"></span></a></li>';
  }
  $support_fixed .= '</ul></div></div>';

}

$vnT->conf['extra_footer'] .= $support_fixed;
?>