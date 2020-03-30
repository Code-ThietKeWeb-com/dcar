<?php
$vnT_Block = new block_statistics ;
class block_statistics extends Blocks {
  //
	function get_title() {
		global $conf,$vnT,$input;
    $this->title = $vnT->lang['statistics']['f_title'];
		return $this->title;
  }
	//
  function get_content() {
	  global $conf,$vnT,$input,$DB;
		//add style 
		
		$content="<ul class='box_statistics'>
							<li>".$vnT->lang['statistics']['online']." : <b id='stats_online'>&nbsp;</b></li>
							<li>".$vnT->lang['statistics']['histcounter']." : <b  id='stats_totals'>&nbsp;</b></li>
							<li>".$vnT->lang['statistics']['member']." : <b  id='stats_member'>&nbsp;</b></li>
						</ul>";
		  
		$data['content'] = $content;
		$this->content = $this->html_box_statistics ($data);
    return $this->content;
  }

//====================== html_box_statistics ===
function html_box_statistics($data){
global $input,$vnT,$conf;
return<<<EOF
{$data['content']}
EOF;
}
//end class
}


?>