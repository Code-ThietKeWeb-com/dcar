<?php
/*
Plugin Name: firework !
*/

$res_p = $vnT->DB->query("SELECT * FROM plugins where name='firework' ");
if($row_p = $DB->fetch_row($res_p))
{
	$folder = $row_p['folder'];
	$params = unserialize($row_p['params']); 
	 
	$bits = ($params['bits']) ? (int)$params['bits']  : 90;
	$speed = ($params['speed']) ? (int)$params['speed']  : 33 ;
	$bangs = ($params['bangs']) ? (int)$params['bangs']  : 7 ;
	
	$colours = '"#03f", "#f03", "#0e0", "#93f", "#0cf", "#f93", "#f0c"' ; 
	if($params['colours'])
	{
		$arr_color = @explode(",",$params['colours']);
		$colours ="";
		foreach ($arr_color as $val)
		{
			$colours .= '"'.trim($val).'",';			
		}
		$colours = substr($colours,0,-1);
	} 
}

$fireworkPath = ROOT_URL.'plugins/'.$folder.'/'; 

$vnT->html->addScriptDeclaration("
			var bits = ".$bits.";
			var speed = ".$speed."; 
			var bangs = ".$bangs."; 
			var colours=new Array(".$colours.");
		");
 
$vnT->html->addScript($fireworkPath."firework.js");
 
?>