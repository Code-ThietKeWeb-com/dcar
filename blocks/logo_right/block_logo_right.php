<?php
$vnT_Block = new block_logo_right ;
class block_logo_right extends Blocks {
  var $cache= true;
	 //
	function get_title() {
		global $conf,$vnT,$input;
   	$this->title = $vnT->lang['logo_right']['f_title'];
		return $this->title;
  }

	function get_content() {
		global $DB,$conf,$func,$vnT,$input;
 		
		$output = "";
		$marquee = 0 ;
		
		//check 
		$res_ck = $DB->query("select * from ad_pos where name='right'");
		if ($row_ck = $DB->fetch_row($res_ck))
		{
			$width = $row_ck['width'] ;
			if($row_ck['type_show']==0 || $row_ck['type_show']==2 || $row_ck['type_show']==3)
			{
				$type_show = "horizontal" ;
				if($row_ck['type_show']!=0) {
					$marquee = 1 ;
					$direction = ($row_ck['type_show']==3) ? "down" : $direction="up";
				}
			}
			
			if($row_ck['type_show']==1 || $row_ck['type_show']==4 || $row_ck['type_show']==5)
			{
				$type_show = "vertical" ;
				if($row_ck['type_show']!=1) {
					$marquee = 1 ;
					$direction = ($row_ck['type_show']==5) ? "right" : $direction="left";
				}
			} 
			
		}
			
    
		$module = ($input['mod']) ? $input['mod'] : $vnT->conf['module'];
		$result = $vnT->DB->query("select * from advertise where  display=1 and lang='$vnT->lang_name'  and  pos='right'  and  (FIND_IN_SET('$module',module_show) or (module_show='') )  order by type_ad DESC,l_order   ");
		
		
    $html_row=""; $selectbox=0; $html_option="";
		if($DB->num_rows($result))
		{
			if($type_show=='vertical') $html_row .= '<table width="100%" border="0" cellspacing="1" cellpadding="1"><tr align=center>';
			
			while ($row = $vnT->DB->fetch_row($result))
			{	
				$title = $vnT->func->HTML($row['title']);
				$src = ROOT_URL . "vnt_upload/weblink/" . $row['img'];
        $link = (! strstr($row['link'], "http://")) ? $vnT->link_root .$row['link'] : $row['link'] ;
        $target = ($row['target']) ? $row['target'] : "_blank";
				
				switch ($row["type_ad"])
				{
					case 0 :{
						$html_row .= ($type_show=='vertical') ? '<td class=advertise >' : '<p class=advertise >' ;
						
						if ($row["type"]=="swf")	{
							$html_row .='<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,19,0" width="'.$row["width"].'" height="'.$row["height"].'">
							<param name="movie" value="'.$src.'" />
							<param name="quality" value="high" />
							<param name="wmode" value="transparent" />
							<embed src="'.$src.'" quality="high" pluginspage="http://www.macromedia.com/go/getflashplayer" type="application/x-shockwave-flash" width="'.$row["width"].'" height="'.$row["height"].'" ></embed>
							</object>';
						}else{
							$html_row .="<a  onmousedown=\"return rwt(this,'advertise',".$row['l_id'].")\" href='".$link."' target='{$target}' ><img border=0 src=\"{$src}\"  width='".$row['width']."' /></a>";
						}
						
						$html_row .= ($type_show=='vertical') ? '</td>' : '</p>' ;
						
					};break;					
					case 1 :{ 	
						$html_row .= ($type_show=='vertical') ? '<td class=advertise >' : '<p class=advertise >' ;					
						$html_row .= "<a href='".$link."' target='{$target}' >".$title."</a>" ;
						$html_row .= ($type_show=='vertical') ? '</td>' : '</p>' ;
					};break;
					case 2 :{
						$html_option.="<option value=\"{$link}\" >".$title."</option>";
						$selectbox=1;
					} ;break;
				}//end switch
			}
			
			if ($selectbox) $html_row='<p align="center" ><form action="" name="f_link" method="post"><select name="site"  onChange="if (f_link[\'site\'].selectedIndex != 0){window1=window.open(f_link[\'site\'].value)}" style="width:{$width}px"><option>---- Web link ---</option>'.$html_option.'</select></form></p>'.$html_row;
			
			if($marquee)
			{
				$output = "<marquee behavior=\"scroll\" direction=\"{$direction}\" scrollamount=\"2\" scrolldelay=\"2\" onmouseover=\"this.stop()\" onmouseout=\"this.start();\" >".$html_row."</marquee>" ;
			}else{
				$output = $html_row ;
			}
			
			$this->content = $output ;
			return $this->content;
			
		}else{
			return '';
		}
		 
	}

  //end class 
}

?>