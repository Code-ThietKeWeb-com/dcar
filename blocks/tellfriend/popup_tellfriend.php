<?php
	define('IN_vnT',1);
	define('DS', DIRECTORY_SEPARATOR);
	require_once("../../_config.php"); 
	require_once("../../includes/class_db.php"); 
	require_once("../../includes/class_functions.php"); 	
	$DB = new DB;

	$func = new Func_Global;
	$conf=$func->fetchDbConfig($conf);
	$vnT->conf	=	$conf ;
	
	$vnT->lang_name = (isset($_GET['lang'])) ? $_GET['lang']  : "vn" ;
	$func->load_language('tellfriend','blocks');

	if ($_POST['btnSend']==1) 
	{
		
		//load mailer
		$func->include_libraries('phpmailer.phpmailer',$conf['rootpath'].'libraries');
		$vnT->mailer = new PHPMailer();
	
		$subject= $_POST['subject'] ;
		$message= $func->HTML ($_POST["message"]);
		$send = $func->doSendMail ($_POST['email'], $subject, $message,$conf['email']);
				
		if ($send) {
			$err= $func->html_mess($vnT->lang['tellfriend']['send_email_success']); 
			$ok_send=1;
		}else{
			$err= $func->html_err('Error ! Try again ');
		}
	}
	
	mt_srand ((double) microtime() * 1000000);
	$num  = mt_rand(100000,999999);
	$scode = $func->NDK_encode($num);
	$img_code = "../../includes/sec_image.php?code=$scode";
?>
<html>
<head>
<title>.: TELL FRIEND :.</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<link href="css/popup.css" rel="stylesheet" type="text/css" />

<script language="javascript">

	function checkform(f) 
	{			
		var re =/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,5})$/gi;
		
		email = f.email.value;
		if (email == '') {
			alert('<?=$vnT->lang['tellfriend']['err_email_empty']?>');
			f.email.focus();
			return false;
		}
		if (email != '' && email.match(re)==null) {
			alert('<?=$vnT->lang['tellfriend']['err_email_invalid']?>');
			f.email.focus();
			return false;
		}
		
		subject = f.subject.value;
		if (subject == '') {
			alert('<?=$vnT->lang['tellfriend']['err_subject_empty']?>');
			f.phone.focus();
			return false;
		}	
		
		if (f.h_code.value != f.security_code.value ) {
			alert('<?=$vnT->lang['tellfriend']['err_security_code']?>');
			f.security_code.focus();
			return false;
		}
		
		return true;
	}
</script>
</head>

<body >
<div id="pcontainer"  >
<?php
		if ($ok_send==0){
		$form_tellfriend = '<table width="100%" border="0" cellspacing="2" cellpadding="2">
  <tr>
    <td><strong>'.$vnT->lang['tellfriend']['friend_email'].'</strong>:<br>
				'.$vnT->lang['tellfriend']['note_friend_email'].'	
		 </td>
  </tr>
  <tr>
    <td><input id="email" name="email"  style="width:90%" class="textfiled"/> &nbsp;&nbsp;(<b class="font_err">*</b>)</td>
  </tr>
	
	<tr>
    <td><strong>'.$vnT->lang['tellfriend']['subject'].'</strong>:</td>
  </tr>
  <tr>
    <td><input id="subject" name="subject"  style="width:90%" class="textfiled" value="'.$vnT->lang['tellfriend']['subject_default'].'" /> &nbsp;&nbsp;(<b class="font_err">*</b>)</td>
  </tr>
	
	<tr>
    <td><strong>'.$vnT->lang['tellfriend']['your_message'].'</strong>:</td>
  </tr>
  <tr>
    <td><textarea name="message" rows="5" style="width:90%" class="textarea">'. str_replace("<br>","\r\n", $vnT->lang['tellfriend']['your_message_default']).'</textarea></td>
  </tr>
	
	<tr>
    <td><strong>'.$vnT->lang['tellfriend']['security_code'].'</strong>:  <input id="security_code" name="security_code" size="15" maxlength="6" class="textfiled"/>&nbsp;<img src="'.$img_code.'" align="absmiddle" /> &nbsp;&nbsp;(<b class="font_err">*</b>) </td>
  </tr>
	
</table>';

?>
 <form action="" method="post" name="contact" id="contact" onSubmit="return checkform(this);">
<?php
	echo $form_tellfriend;
?>    
    
   <table border="0" cellspacing="5" cellpadding="5" align="center">
		<td align="center">&nbsp;(<b class="font_err">*</b>) <?php echo $vnT->lang['tellfriend']['required_field']?></td>
    <td align="center"><table  border="0" cellspacing="2" cellpadding="2">
        
        <tr>
          <td>
          <input name="btnSend" type="submit" class="button" value="<?php echo $vnT->lang['tellfriend']['btn_send']?>">
          <input name="btnSend" type="hidden" value="1">
          <input type="hidden" name="h_code" value="<?=$num?>">
          </td>
          
          <td><input name="btnSend" type="button" class="button" value="<?php echo $vnT->lang['tellfriend']['btn_close']?>" onClick="window.close();"></td>
        </tr>
      </table>
     </td>
  </tr>
</table>
</form>
<?php 
	}else{
?>
	<table width="95%"  border="0" cellspacing="2" cellpadding="2" align="center">
        <tr>
    
          <td><?php echo $err ?></td>
        </tr>
        <tr>
    
          <td align="center"><input name="btnSend" type="button" class="button" value="<?php echo $vnT->lang['tellfriend']['btn_close']?>" onClick="window.close();"></td>
        </tr>
      </table>
<?php
	}
	
	?>

    </td>
  </tr>
</table>
<br>

</div>
</body>

</html>