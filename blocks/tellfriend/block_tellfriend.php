<?php
$vnT_Block = new block_tellfriend ;
class block_tellfriend extends Blocks {
  var $cache= true;
	 //
	function get_title() {
		global $conf,$vnT,$input;
    $this->title = $vnT->lang['tellfriend']['f_title'];
		return $this->title;
  }

	function get_content() {
		global $DB,$conf,$func,$vnT,$input;
		if(isset($input['do_tellfriend']))
		{
				$f_email = trim($input['f_email']);
				$subject= $vnT->lang['f_subject']." ".$_SERVER['HTTP_HOST'];
				$message= $func->load_MailTemp('tellfriend');
				$vnT->func->doSendMail($f_email, $subject, $message, $vnT->conf["email"]);
				$err=$vnT->lang['tellfriend']['send_email_success']; 
				$url =  "?" . $_SERVER['QUERY_STRING'];				
				$vnT->func->html_redirect($url,$err);
		}
		
		$data['f_email'] = ($input['f_email']) ? $input['f_email'] : $vnT->lang['tellfriend']['enter_your_email'] ;
		$this->content = $this->html_box_tellfriend ($data);
		return $this->content;
	}

//====================== html_box_member ===
function html_box_tellfriend($data){
global $input,$vnT,$conf;
return<<<EOF
<script type="text/javascript">
		 
		 function check_tellfriend (f){
		 	var re =/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,5})$/gi;
			var f_email = f.f_email.value;
			if (f_email == "") 	{
				alert("{$vnT->lang['tellfriend']['err_email_empty']}");
				f.f_email.focus();
				return false;
			}
			
			if (f_email != "" && f_email.match(re)==null) 	{
				alert("{$vnT->lang['tellfriend']['err_email_invalid']}");
				f.f_email.focus();
				return false;
			}
	}
	</script>

<form name="f_tellfriend" method="post" action="" onsubmit="return check_tellfriend(this);">
<p>{$vnT->lang['tellfriend']['mess_tellfriend']}</p>
<table border="0" cellspacing="2" cellpadding="2">
	<tr>
		<td class="input_bg"><input name="f_email" type="text"  id="f_email" class="input_text" onfocus="if(this.value=='{$vnT->lang['tellfriend']['enter_your_email']}') this.value='';" onblur="if(this.value=='') this.value='{$vnT->lang['tellfriend']['enter_your_email']}';"  value="{$data['f_email']}"/></td>
		<td ><input name="btnSend" class="button" type="submit" value="Send" /></td>
	</tr>
</table>
<input name="do_tellfriend" type="hidden" value="1" />
</form>


EOF;
}
//end class 
}

?>