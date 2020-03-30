<?php
	// no direct access
	if (!defined('IN_vnT')){
			 die('Hacking attempt!');
	}

	if( strstr($_SERVER['REQUEST_URI'],"search.html" ))
	{
		$pos 				= array_search ('search.html', $vnT->url_array);
		$extra = $vnT->url_array[$pos+1] ;
		if($extra) $ext_link = "|".$extra;
		$_GET[$vnT->conf['cmd']] = "mod:search".$ext_link;
		$QUERY_STRING = $vnT->conf['cmd']."=mod:search".$ext_link;
	}
 
?>