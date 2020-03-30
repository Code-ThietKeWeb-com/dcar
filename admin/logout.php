<?php
	// Update session
	$upses['act']="Logout";
	$upses['sub']="";
	$upses['pid']="";
	
	$doitupses=$DB->do_update("adminsessions",$upses,"adminid=".$admininfo['adminid']);
	$func->insertlog("Logout");
	// end

	$func->vnt_clear_auth_cookie();
  @session_destroy();

	$mess=  str_replace('{username}',$admin_user,$vnT->lang['logout_sucess']);  
	$url = "index.php";
	$func->html_redirect($url,$mess);
?>