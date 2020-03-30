<?php
/*================================================================================*\
|| 							Name code : ajax_tellfriend.php	 																	  # ||
||  				Copyright © 2007 by Thai Son - CMS vnTRUST                					# ||
\*================================================================================*/
/**
* @version : 1.0
* @date upgrade : 11/12/2007 by Thai Son
**/

	define('IN_vnT',1);	
	require_once("../../_config.php"); 
	include($conf['rootpath']."includes/class_db.php"); 
	$DB = new DB;	
	include($conf['rootpath']."includes/class_functions.php"); 
	$func = new Func_Global;
	$conf=$func->fetchDbConfig($conf);	
	
	$vnT->lang_name = (isset($_GET['lang'])) ? $_GET['lang']  : "vn" ;
			
	//echo "lang = ".$vnT->lang_name;
	$func->load_language('tellfriend','blocks');
	
	$jsout="";
	$err="";
	if (!empty($_GET['f_email'])) {
		$f_email = $_GET['f_email'];
		$err="";
		if ((!ereg("^[a-zA-Z0-9_.]+@[a-zA-Z0-9\-]+\.[a-zA-Z0-9\-\.]+$", $f_email))or(empty($f_email)))
		{ 
			$err=$vnT->lang['tellfriend']['err_email_invalid'];
		}
		else {
			$subject="Gioi thieu website ".$_SERVER['HTTP_HOST'];
			$message= $func->load_MailTemp('tellfriend');
			@mail($f_email, $subject, $message,$conf['email']);
			$err=$vnT->lang['tellfriend']['send_email_success']; 
		}
	}else $err=$vnT->lang['tellfriend']['err_email_empty'];
	$data['err'] = $err;
	$data['f_email'] = $f_email;
	
	$jsout = result_form($data);

flush();
echo $jsout;
exit();

// result_form	
function result_form($data) {
global $vnT,$DB,$func,$conf;
return<<<EOF
<form name="f_tellfriend" method="post" action="" onsubmit="javascript: return do_sendmail(this,'{$vnT->lang_name}');">
   
<p>{$data['err']}</p>
<table border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td class="input_bg"><input name="f_email" type="text"  id="f_email" class="input_text"  value="{$data['f_email']}"/></td>
		<td width="62"><input type="image" name="imageField" id="imageField" src="blocks/tellfriend/images/input_r.gif" /></td>
	</tr>
</table>


</form>

EOF;
}

?>