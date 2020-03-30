<?php
$vnT_Block = new block_member ;
class block_member extends Blocks {
    function get_title() {
			global $DB,$conf,$func,$vnT,$input;
      $this->title =  $vnT->lang['member']['f_title'];
			return $this->title;
    }

    function get_content() 
		{
			global $DB,$conf,$func,$vnT,$input;
			//add style
			$vnT->html->addStyleSheet( ROOT_URL."blocks/member/css/block_member.css");

			if ($vnT->user['mem_id']!=0)
			{
				if($vnT->user['avatar']){
					$src ="view_thumb.php?image=".ROOT_URL."vnt_upload/member/avatar/".$vnT->user['avatar']."&w=100";
					$avatar="<img src=\"{$src}\" class='img_border' >";
				}
				else	{ 
					$avatar="<img src=\"".ROOT_URL."vnt_upload/member/avatar/noavatar.gif\" width=100 class='img_border' >";
				}
		
				$data['ip'] = $_SERVER['REMOTE_ADDR'];
				
				if($_SERVER['REQUEST_URI']){
					$url = "/".$vnT->func->base64url_encode($vnT->seo_url);
				}else{
					$url="";
				}
				
				
				
				$link_member = ROOT_URL."member.html";
				$link_changepass = ROOT_URL."member/changepass.html";
				$link_logout =  ROOT_URL."member/logout.html".$url;
				
				
				$text = "<div  class=\"box_member\">
				<p class=\"wellcome\">".$vnT->lang['member']['welcome']." : <span class='username'>{$vnT->user['username']}</span></p>
				<p align=\"center\">".$avatar."</p>
				<ul>
					<li class='ip'><strong>".$data['ip']."</strong></li>
					<li class='member'><a href=\"".$link_member."\">".$vnT->lang['member']['account_infomation']."</a></li>
					<li class='changepass' ><a href=\"".$link_changepass."\">".$vnT->lang['member']['change_pass']."</a></li>
					<li class='logout'><a href=\"".$link_logout."\">".$vnT->lang['member']['logout']."</a></li>
				</ul>
			</div>";
			
				
				$data['content'] = $text;
				$content = $this->html_box_member($data);
			}else{
				
				$data['link_lostpass'] =  ROOT_URL."member/forget_pass.html";
				$data['link_register'] =  ROOT_URL."member/register.html";
				$data['link_action'] = ROOT_URL."member/login.html"; 

				if($_SERVER['REQUEST_URI']){
					$data['url'] = $vnT->func->base64url_encode($vnT->seo_url);
				}else{
					$data['url']="";
				}
				$content = $this->html_box_guest($data);
			}
			$this->content = $content;

			return $this->content;
    }
 
/************************************** box_guest ***************************/
function html_box_guest ($data){
global $DB,$func,$input,$conf,$vnT ;	
return<<<EOF
<div class="box_member">
<form action="{$data['link_action']}" method="post" name="fLogin">
    <table width="100%" border="0" cellspacing="2" cellpadding="2">
    	<tr>
				<td>
				<label>{$vnT->lang['member']['username']}</label>
				<input type="text"  name="username" class="textfiled" style="width:100%;text-align:center" onfocus="if(this.value=='{$vnT->lang['member']['enter_username']}') this.value='';" onblur="if(this.value=='') this.value='{$vnT->lang['member']['enter_username']}';"  value="{$vnT->lang['member']['enter_username']}" /></td>
		</tr>
		<tr>
			<td>
			<label>{$vnT->lang['member']['password']}</label>
			<input type="password" name="password" class="textfiled" style="width:100%;text-align:center"   /></td>
		</tr>
		<tr>
			<td align="center" >
					<input name="btnLogin" type="submit" value="{$vnT->lang['member']['btn_login']}" class='button'  />
					<input type="hidden" name="do_login" value="1" >
					<input type="hidden" name="url" value="{$data['url']}">
			</td>
		</tr>
	</table>
	</form>
<p><a href="{$data['link_register']}">{$vnT->lang['member']['register_new']}</a></p>
<p><a href="{$data['link_lostpass']}">{$vnT->lang['member']['lostpass']}</a></p>
</div>
EOF;
}


//====================== html_box_member ===
function html_box_member($data){
global $input,$vnT,$conf;
return<<<EOF

<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td >{$data['content']}</td>
  </tr>

</table>

EOF;
}

//end class
}
?>