<?php
$vnT_Block = new block_poll ;
class block_poll extends Blocks {
   // get_title
		function get_title() {
			global $conf,$vnT,$input;
			$this->title = $vnT->lang['poll']['f_title'];
			return $this->title;
		}

    function get_content() {
        global $DB,$conf,$func,$vnT,$input;
				$vnT->html->addStyleSheet( ROOT_URI."blocks/poll/css/poll.css");
				$vnT->html->addStyleSheet( ROOT_URI."blocks/poll/css/ajax-poller.css");
				$vnT->html->addScript($vnT->dir_js."/ajax.js");
				$vnT->html->addScript(ROOT_URI."blocks/poll/ajax-poller.js");		
				
	
				$row_poll="";
				// Retreving poll from database
				$res = $DB->query("select * from poller order by p_order ASC, id DESC LIMIT 0,1");	
				if($inf = $DB->fetch_row($res)){	
					$id = $inf["id"];
					if ($inf['picture'])
						$pic = "<div id=\"img_poll\" ><img src=\"".ROOT_URI."vnt_upload/poll/".$inf['picture']."\" /></div>";
					else
						$pic="";
					$resOptions = $DB->query("select * from poller_option where pollerID='{$id}' order by pollerOrder ASC, id DESC ") ;	// Find poll options, i.e. radio buttons
					while($infOptions = $DB->fetch_row($resOptions)){
						if($infOptions["defaultChecked"])$checked=" checked"; else $checked = "";
							$row_poll.="<li><input$checked type=\"radio\" value=\"".$infOptions["id"]."\" name=\"vote[".$inf["id"]."]\" id=\"pollerOption".$infOptions["ID"]."\" align='absmiddle'> ".$func->fetch_array($infOptions['optionText'])."</li>";	
					}
				}else {
					$text= "<li>Chưa có thăm dò nào</li>";
				}
				
				$data['id']	 =$id ;
				$data['pic'] =$pic ;
				$data['pollerTitle'] = $func->fetch_array($inf['pollerTitle']);
				$data['row_poll'] = $row_poll;
	
			$this->content = $this->html_content ($data);
			return $this->content;
    }
 
//==== html_content 
function html_content($data){
global $input,$vnT,$conf;
return<<<EOF
<script type="text/javascript">
	 var total_votes = "Tổng số bình chọn";
</script>
<a name="poll" id="poll"></a>
<div class="box_poll">
	{$data['pic']}
	<form action="" name="f_poll" method="post">
	<div class="poller_question" id="poller_question{$data['id']}">
		 
		<h3 class="pollerTitle">{$data['pollerTitle']}</h3>		
		<ul>
			{$data['row_poll']}
		</ul>		
		<p class="btn">
			<input value="{$data['id']}" name="poll_id" type="hidden">
			<input value="0" name="option_id" type="hidden">
			<input value="{$vnT->lang_name}" name="lang" type="hidden">
			<a href="#poll" onClick="castMyVote({$data['id']},document.f_poll)"><img src="{$conf['rooturl']}blocks/poll/images/btn_vote.gif" /></a>&nbsp;
			<a href="#poll" onClick="displayResultsWithoutVoting({$data['id']},document.f_poll);"><img src="{$conf['rooturl']}blocks/poll/images/btn_result.gif" /></a>
		</p>
		
	</div>
	<div  class="poller_waitMessage" id="poller_waitMessage{$data['id']}">
		Đang xử lý kết quả . Vui lòng đợi...
	</div>
	<div class="poller_results"   id="poller_results{$data['id']}"> </div>
	</form>
		
<br class="clear"></div>
EOF;
}

//end class
}
?>