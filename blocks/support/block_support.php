<?php
$vnT_Block = new block_support ;
class block_support extends Blocks {
  var $dirBlock =  "blocks/support/";
	
	function get_title() {
		global $conf,$vnT,$input;
    $this->title = $vnT->lang['support']['f_title'] ;
		return $this->title;
  }
	
	//
	function get_content() {
		global $DB,$conf,$func,$vnT,$input;
		//add style
		$vnT->html->addStyleSheet( $conf['rooturl']."blocks/support/css/block_support.css");
		$data['hotline'] = str_replace(",","<br>",$vnT->conf['hotline']);
		$content="";		
		$data['content'] = $content;
		$this->content = $this->html_box_support ($data);
		return $this->content;
	}
//====================== html_box_support ===
function html_box_support($data){
global $input,$vnT,$conf;
return<<<EOF
<div class="box_support">
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td  valign="top">
		<table  border="0" cellspacing="5" cellpadding="5" align="center">
			<tr   height="50">
				<td><a href="javascript:;" onClick="NewWindow('{$conf['rooturl']}{$this->dirBlock}popup_skype.php?lang={$vnT->lang_name}','Chat',300,500,'yes','center');"><img src="{$conf['rooturl']}{$this->dirBlock}images/skype.gif"  /></a></td>
				<td><a href="javascript:;" onClick="NewWindow('{$conf['rooturl']}{$this->dirBlock}popup_yahoo.php?lang={$vnT->lang_name}','Chat',300,500,'yes','center');" ><img src="{$conf['rooturl']}{$this->dirBlock}images/yahoo.gif"  /></a></td>
			</tr>
			
		</table>
		
		</td>
  </tr>
  <tr>
    <td  height="23" align="center" >{$vnT->lang['support']['hotline']} : <span class="fHotline">{$data['hotline']}</span></td>
  </tr>
</table>
 
</div>	

EOF;
}
//end class
}

?>