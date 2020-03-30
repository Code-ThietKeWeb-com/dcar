<?php
$vnT_Block = new block_maillist ;
class block_maillist extends Blocks {
  var $cache= true;
	var $dirBlock = "blocks/maillist";
	
	 //
	function get_title() {
		global $conf,$vnT,$input;
    $this->title = $vnT->lang['maillist']['f_title'];
		return $this->title;
  }

	function get_content() {
		global $DB,$conf,$func,$vnT,$input;
		$vnT->html->addStyleSheet( ROOT_URL."blocks/maillist/css/block_maillist.css");
		
		if(isset($input['do_maillist']))
		{
				$f_email = trim($input['f_email']);
				$check = $DB->query ("select * from listmail where email='$f_email'");
				if ($DB->num_rows($check)) $err = $vnT->lang['maillist']['err_email_existed'];
				
				if (empty ($err)) 
				{
					$cot['email'] =$f_email ;
					$cot['cat_id'] =1;
					$cot['datesubmit'] =time();
					$ok = $DB->do_insert ("listmail",$cot);
					if( $ok) 
					{ 
						$url =  $vnT->seo_url ;
						$err = $vnT->lang['maillist']['mess_maillist_success'];
					}else{
						$err=  $vnT->lang['maillist']['mess_maillist_failt'];
					}					
				}
				
				$vnT->func->html_redirect($url,$err);
		}
	
		
		$data['f_email'] = ($input['f_email']) ? $input['f_email'] : $vnT->lang['maillist']['enter_your_email'] ;
		
		$this->content = $this->html_box_tellfriend ($data);
		return $this->content;
	}

//====================== html_box_member ===
function html_box_tellfriend($data){
global $input,$vnT,$conf;
return<<<EOF
<script type="text/javascript">
		 
		 function check_maillist (f){
		 	var re =/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,5})$/gi;
			var f_email = f.f_email.value;
			if (f_email == "") 	{
				alert("{$vnT->lang['maillist']['err_email_empty']}");
				f.f_email.focus();
				return false;
			}
			
			if (f_email != "" && f_email.match(re)==null) 	{
				alert("{$vnT->lang['maillist']['err_email_invalid']}");
				f.f_email.focus();
				return false;
			}
	}
	</script>
<form method="post" action="" name="fMaillList"  onsubmit="return check_maillist(this);"  >
<p>{$vnT->lang['maillist']['mess_maillist']}</p>
<table border="0" cellspacing="2" cellpadding="2">
	<tr>
		<td ><input name="f_email" type="text"  id="f_email" class="input_text" onfocus="if(this.value=='{$vnT->lang['maillist']['enter_your_email']}') this.value='';" onblur="if(this.value=='') this.value='{$vnT->lang['maillist']['enter_your_email']}';"  value="{$data['f_email']}"/></td>
		<td  ><input name="btnSend" class="button" type="submit" value="Send" /></td>
	</tr>
</table>
<input name="do_maillist" type="hidden" value="1" />
</form>
EOF;
}
//end class 
}

?>