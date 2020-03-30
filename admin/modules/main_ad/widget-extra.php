<?php
define('IN_vnT', 1);
define('DS', DIRECTORY_SEPARATOR);
require_once ("../../../_config.php");
include ($conf['rootpath'] . "includes/class_db.php");
$vnT->DB = $DB = new DB();

//Functions
include ($conf['rootpath'] . 'includes/class_functions.php');
include($conf['rootpath'] . 'includes/admin.class.php');
$vnT->func = $func  = new Func_Admin;
$vnT->conf = $conf = $func->fetchDbConfig($conf);

/** Load Magpie RSS API or custom RSS API */
$func->include_libraries('MagpieRSS.rss_fetch');

@header('Content-Type: text/html; charset=utf-8');
switch ($_GET['do']){
	case "rss_news" : $jsout = rss_news() ;break;
	default  : $jsout ="Error" ;break;
}


//rss_news
function rss_news() {
	global $vnT,$DB,$func,$conf;
  
	$url = 'http://vnexpress.net/rss/tin-moi-nhat.rss';
	$rss = fetch_rss($url);
	$num_items = 5;
	$items = array_slice($rss->items, 0, $num_items);
	
	$textout = '<div class="rss-widget" ><ul>';
	foreach ( $items as $item ) 
	{
 			$textout .= "<li><a href='".$item['link']."' target='_blank' class='rsswidget' >Title: " . $item['title']."</a>";
			$textout .= "<span class='rss-date'>" . date("H:i , d/m/Y", $item['date_timestamp']).'</span>';
			$textout .= '<div class="rssSummary">' .  $func->cut_string($func->check_html($item['description'],'nohtml'),150,1).'</div>';
			
			$textout .= "</li>";
			
	}
	$textout .= '</ul></div>';


 
	return $textout;
}

	
flush();
echo $jsout;
exit();	

 
?>