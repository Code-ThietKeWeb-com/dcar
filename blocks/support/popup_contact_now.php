<?php
	session_start();
	define('IN_vnT',1);
	require_once("../../_config.php"); 
	require_once("../../includes/class_db.php"); 
	require_once("../../includes/class_functions.php");  	
	$DB = new DB;
	$vnT->DB = $DB ;
	$func = new Func_Global;
	$conf=$func->fetchDbConfig($conf);
	// array
	include("../../includes/array.inc.php");

	$vnT->lang_name = (isset($_GET['lang'])) ? $_GET['lang']  : "vn" ;
	$func->load_language('support','blocks');
	
	//------- List_DateCall ---------------------
 function List_DateCall ($did){
 	global $input,$conf,$vnT ;
 	$text='<select name="date_call" id="date_call"  class="select" style="text-align:center;">';
	$today =date("d/m/Y");

	for ($i=0;$i<=7;$i++){
		$time_next = time()+($i*24*3600);
		$date_next = date("d/m/Y",$time_next);
		if ($date_next == $did) {
			$text.="<option value=\"{$date_next}\" selected >".$date_next."</option>";
		}else{
			$text.="<option value=\"{$date_next}\" >".$date_next."</option>";
		}
	}
	$text.='</select>';
	return $text;
 }
 
  function List_TimeCall ($did){
 	global $input,$conf,$vnT ;
 	$arr_time = array("07:30","08:00","08:30","09:00","09:30","10:00","10:30","11:00","11:30","12:00","12:30","13:00","13:30","14:00","14:30","15:00","15:30","16:00","16:30","17:00");
	
	$text='<select  name="time_call" id="time_call"   class="select" style="text-align:center;" >';
	foreach ($arr_time as $key => $value)
	{
		if ($value == $did) {
			$text.="<option value=\"{$value}\" selected >".$value."</option>";
		}else{
			$text.="<option value=\"{$value}\" >".$value."</option>";
		}
	}
	$text.='</select>';
	return $text;
 }

 
	$ok_send =0;
	
	$result = $DB->query("select * from product_brand_desc where brand_id=$id and lang='$vnT->lang_name'  ");
	if ($row = $DB->fetch_row($result)){
	
		$brand_name= $func->HTML($row['title']);
	}

	if ($_POST['btnSend']==1) 
	{

		if (empty ($err)) {
			$cot['f_name'] = $func->txt_HTML($_POST['f_name']) ;
			$cot['l_name'] =$func->txt_HTML($_POST['l_name']) ;
			$cot['phone'] =$func->txt_HTML($_POST['phone']) ;
			$cot['email'] =$func->txt_HTML($_POST['email']) ;
			$cot['address'] =$func->txt_HTML($_POST['address']) ;
			$cot['country'] =$func->txt_HTML($_POST['country']) ;
			$cot['date_call'] =$func->txt_HTML($_POST['date_call']) ;
			$cot['time_call'] =$func->txt_HTML($_POST['time_call']) ;
			$cot['note'] =$func->txt_HTML($_POST['note']) ;
			$cot['datesubmit'] =time();
			$ok = $DB->do_insert ("contact_now",$cot);
			if( $ok) { 
				$err= $func->html_mess($vnT->lang['support']['send_contact_now_success']); 
				$ok_send=1;
			}
			else{
				$err= $func->html_err('Error ! Try again ');
			}
		}
	}
	
	mt_srand ((double) microtime() * 1000000);
	$num  = mt_rand(100000,999999);
	$scode = $func->NDK_encode($num);
	$img_code = "../../includes/sec_image.php?code=$scode";
	
	$country ="VN";
	$list_country = $func->List_Array ("country",$country);
	
	$date_call = (isset($_POST['date_call'])) ? $_POST['date_call'] : date("d/m/Y") ;
	$phut = (date("i")>30) ? "30" : "00";
	$time_call = (isset($_POST['time_call'])) ? $_POST['time_call'] : date("H").":".$phut ;
?>
<html>
<head>
<title>.: CONTACT NOW :.</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<link href="css/popup.css" rel="stylesheet" type="text/css" />
<script type="text/javascript">
<!--
function getRefToDivMod( divID, oDoc ) {
  if( !oDoc ) { oDoc = document; }
  if( document.layers ) {
    if( oDoc.layers[divID] ) { return oDoc.layers[divID]; } else {
      for( var x = 0, y; !y && x < oDoc.layers.length; x++ ) {
        y = getRefToDivMod(divID,oDoc.layers[x].document); }
      return y; } }
  if( document.getElementById ) { return oDoc.getElementById(divID); }
  if( document.all ) { return oDoc.all[divID]; }
  return document[divID];
}
function resizeWinTo( idOfDiv ) {
  var oH = getRefToDivMod( idOfDiv ); if( !oH ) { return false; }
  var oW = oH.clip ? oH.clip.width : oH.offsetWidth;
  var oH = oH.clip ? oH.clip.height : oH.offsetHeight; if( !oH ) { return false; }
  var x = window; x.resizeTo( oW + 200, oH + 200 );
  var myW = 0, myH = 0, d = x.document.documentElement, b = x.document.body;
  if( x.innerWidth ) { myW = x.innerWidth; myH = x.innerHeight; }
  else if( d && d.clientWidth ) { myW = d.clientWidth; myH = d.clientHeight; }
  else if( b && b.clientWidth ) { myW = b.clientWidth; myH = b.clientHeight; }
  if( window.opera && !document.childNodes ) { myW += 16; }
  	var myw = oW + ( ( oW + 200 ) - myW )+(5);
	var myh = oH + ( (oH + 200 ) - myH )+(5*2);
	if(myw > screen.availWidth){
		myw = screen.availWidth;
	}
	if(myh > screen.availHeight){
		myh = screen.availHeight;
	} 
  x.resizeTo( myw, myh );
  var scW = screen.availWidth ? screen.availWidth : screen.width;
  var scH = screen.availHeight ? screen.availHeight : screen.height;
  x.moveTo(Math.round((scW-myw)/2),Math.round((scH-myh)/2));
}
// -->
</script>

<script language=javascript>

	function checkform(f) 
	{			
		var re =/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,5})$/gi;
		
		
		
		f_name = f.f_name.value;
		if (f_name == '') {
			alert('<?=$vnT->lang['support']['err_f_name_empty']?>');
			f.f_name.focus();
			return false;
		}
		l_name = f.l_name.value;
		if (l_name == '') {
			alert('<?=$vnT->lang['support']['err_l_name_empty']?>');
			f.l_name.focus();
			return false;
		}
				
		email = f.email.value;
		if (email == '') {
			alert('<?=$vnT->lang['support']['err_email_empty']?>');
			f.email.focus();
			return false;
		}
		if (email != '' && email.match(re)==null) {
			alert('<?=$vnT->lang['support']['err_email_invalid']?>');
			f.email.focus();
			return false;
		}
		
		phone = f.phone.value;
		if (phone == '') {
			alert('<?=$vnT->lang['support']['err_phone_empty']?>');
			f.phone.focus();
			return false;
		}	
		
		var curday = '<?php echo date("d/m/Y"); ?>';
		var curHours = '<?php echo date("H"); ?>';
		 
		date_call = f.date_call.value;
		time_call = f.time_call.value;
		if(date_call == curday)
		{
			tmp_time = time_call.split(":");
			if ( (parseInt(tmp_time[0])-parseInt(curHours)) <2)
			{
				alert ("Pls enter Time call > Time current 2 hours ");
				f.time_call.focus();
				return false;	
			}
		}
		
		if (f.h_code.value != f.security_code.value ) {
			alert('<?=$vnT->lang['support']['err_security_code']?>');
			f.security_code.focus();
			return false;
		}
		
		return true;
	}
</script>
</head>

<body onLoad="resizeWinTo('pcontainer');">
<div id="pcontainer"  >
<table width="580" border="0" cellspacing="0" cellpadding="0" align="center">
  <tr>
    <td >
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td width="4"><img src="images/support_l.gif" width="4" height="29"></td>
        <td align="center" bgcolor="#CFCFCF" class="font_f_title"><?php echo $vnT->lang['support']['we_call_you']; ?></td>
        <td width="10"><img src="images/support_r.gif" width="10" height="29"></td>
      </tr>
    </table></td>
  </tr>
  <tr>
    <td style="padding:0px 10px;">
  	  
   	<?php
			if ($ok_send==0){
		?>
    <p align="justify"><?php echo $vnT->lang['support']['mess_contact_now']?></p>
    <form action="" method="post" name="contact" id="contact" onSubmit="return checkform(this);">
    <table width="100%" border="0" cellspacing="2" cellpadding="2">
  <tr>
    <td width="30%" align="right"><strong><?php echo $vnT->lang['support']['f_name']; ?> : </strong></td>
    <td><input type="text" name="f_name" id="f_name" class="textfiled" size="40">
      &nbsp;&nbsp;(<b class="font_err">*</b>)</td>
  </tr>
  <tr>
    <td width="30%" align="right"><strong><?php echo $vnT->lang['support']['l_name']; ?> : </strong></td>
    <td><input type="text" name="l_name" id="l_name" class="textfiled" size="40">
      &nbsp;&nbsp;(<b class="font_err">*</b>)</td>
  </tr>
  <tr>
    <td width="30%" align="right"><strong>E-mail: </strong></td>
    <td><input type="text" name="email" id="email" class="textfiled" size="50">
      &nbsp;&nbsp;(<b class="font_err">*</b>)</td>
  </tr>
  <tr>
    <td width="30%" align="right"><strong><?php echo $vnT->lang['support']['country']; ?> : </strong></td>
    <td><?php echo $list_country; ?> </td>
  </tr>
  <tr>
    <td width="30%" align="right"><strong><?php echo $vnT->lang['support']['phone_number']; ?> : </strong></td>
    <td><input type="text" name="phone" id="phone" class="textfiled" size="40">
      &nbsp;&nbsp;(<b class="font_err">*</b>)</td>
  </tr>
  <tr>
    <td width="30%" align="right"><strong><?php echo $vnT->lang['support']['call_us_on']; ?> : </strong></td>
    <td>
    <?php echo List_DateCall($date_call);  ?>
      &nbsp; <strong>at</strong> &nbsp;<?php echo List_TimeCall($time_call); ?></td>
  </tr>
  <tr>
    <td width="30%" align="right"><strong><?php echo $vnT->lang['support']['note']; ?> :</strong></td>
    <td><textarea name="note" id="note" style="width:100%" rows="5"></textarea></td>
  </tr>
  <tr>
      <td align="right"><strong><?php echo $vnT->lang['support']['security_code']; ?> :</strong> </td>
      <td><input id="security_code" name="security_code" size="15" maxlength="6" class="textfiled"/>&nbsp;<img src="<?=$img_code?>" align="absmiddle" /> &nbsp;&nbsp;(<b class="font_err">*</b>)
      <input type="hidden" name="h_code" value="<?=$num?>">      </td>
    </tr>
  <tr>
    <td valign="top">&nbsp;</td>
    <td>&nbsp;(<b class="font_err">*</b>) <?php echo $vnT->lang['support']['required_field']?><br>

      <table  border="0" cellspacing="2" cellpadding="2">
        
        <tr>
          <td><input name="btnSend" type="submit" class="button" value="<?php echo $vnT->lang['support']['btn_send']?>">
          <input name="btnSend" type="hidden" value="1">
          </td>
          
          <td><input name="btnSend" type="button" class="button" value="<?php echo $vnT->lang['support']['btn_close']?>" onClick="window.close();"></td>
        </tr>
      </table></td>
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
    
          <td align="center"><input name="btnSend" type="button" class="button" value="<?php echo $vnT->lang['support']['btn_close']?>" onClick="window.close();"></td>
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