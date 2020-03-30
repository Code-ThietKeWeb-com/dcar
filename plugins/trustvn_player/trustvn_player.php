<?php

		$res_p = $vnT->DB->query("SELECT * FROM plugins where name='trustvn_player' ");
		if($row_p = $DB->fetch_row($res_p))
		{
			$params = unserialize($row_p['params']);
			$src = ROOT_URL.'plugins/'.$row_p['folder'].'/trust_player.swf';			
			$trustvn_player ='<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=9,0,28,0" width="'.$params['width'].'" height="'.$params['height'].'" >
				<param name="movie" value="'.$src.'" />
				<param name="quality" value="high" />
				<embed src="'.$src.'" quality="high" pluginspage="http://www.adobe.com/shockwave/download/download.cgi?P1_Prod_Version=ShockwaveFlash" type="application/x-shockwave-flash" width="'.$params['width'].'" height="'.$params['height'].'"></embed>
			</object>';
		}else{
			$trustvn_player='';
		}
		
		$vnT->plugins['trustvn_player'] = $trustvn_player;
?>
