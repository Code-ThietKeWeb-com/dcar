<?php
	$vnT->func->include_libraries('vntrust.base.object');
	$vnT->func->include_libraries('vntrust.filesystem.path');
	$vnT->func->include_libraries('vntrust.filesystem.folder');
	$vnT->func->include_libraries('vntrust.filesystem.file');
	define('DIR_IMAGE_MEDIA','modules/media_ad/images');
	
	require_once('includes/class_xml.php');
	$xmlobj = new XMLparser(false, PATH_PLUGINS.DS.$data['folder'].DS."trustvn_playlist.xml");
	$xml =& $xmlobj->parse();
	$arr_song = array();
			
	
	if(is_array($xml['song'][0]))
	{
		foreach ($xml['song'] as $song)	{
		
			$arr_song[$song['id']]['id'] = $song['id'];
			$arr_song[$song['id']]['name'] = $song['name'];
			$arr_song[$song['id']]['artist'] = $song['artist'];
			$arr_song[$song['id']]['file'] = $song['file'];
		}	
	}else{
		$song = $xml['song'];
		$arr_song[$song['id']]['id'] = $song['id'];
		$arr_song[$song['id']]['name'] = $song['name'];
		$arr_song[$song['id']]['artist'] = $song['artist'];
		$arr_song[$song['id']]['file'] = $song['file'];
	}
	
	/*
		echo "<pre>";
		print_r($arr_song);
		echo "</pre>";
	*/
	
	//upload file
	if(isset($_POST['btnUpload']))
	{
		
		if (!empty($_FILES['file_upload']) && ($_FILES['file_upload']['name']!="") )
		{
			
			$up['path']=PATH_PLUGINS.DS.$data['folder'].DS;
			$up['dir']= "music";
			$up['file']= $_FILES['file_upload'];
			$result = $func->Upload($up);
			if (empty($result['err'])) {
				$mess = $func->html_mess("Upload file thành công");
			} else {
				$mess = $func->html_err($result['err']);
			}
		}	
	}	
	
	//update
    if ($vnT->input["do_action"])
    {
      //xoa cache
      $func->clear_cache();
      if ($vnT->input["del_id"]) $h_id = $vnT->input["del_id"];
			$arr_name = $vnT->input["song_name"];
			$arr_artist = $vnT->input["artist"];
			$arr_file = $vnT->input["song_url"];
			
      switch ($vnT->input["do_action"])
      {
				case "do_edit":
            for ($i = 0; $i < count($h_id); $i ++)
            {
							if (array_key_exists($h_id[$i],$arr_song)){
								$arr_song[$h_id[$i]]['name'] = $arr_name[$h_id[$i]];
								$arr_song[$h_id[$i]]['artist'] = $arr_artist[$h_id[$i]];
							}
            } 
		
          break;
        case "do_hidden":
            for ($i = 0; $i < count($h_id); $i ++)
            {
              $func->delete_key_array($arr_song,$h_id[$i]);
            } 

          break;
        case "do_display":
           for ($i = 0; $i < count($h_id); $i ++)
            {
              $arr_song[$h_id[$i]]['id'] = $h_id[$i];
							$arr_song[$h_id[$i]]['name'] = $arr_name[$h_id[$i]];
							$arr_song[$h_id[$i]]['artist'] = $arr_artist[$h_id[$i]];
							$arr_song[$h_id[$i]]['file'] = $arr_file[$h_id[$i]];
            }
          break;
				case "do_del":
            for ($i = 0; $i < count($h_id); $i ++)
            {
              $func->delete_key_array($arr_song,$h_id[$i]);
							
							$file_del = str_replace($conf['rooturl'],"../", $arr_file[$h_id[$i]]);					
							$ok = vnT_File::delete($file_del);
            } 
          break;
      }
									
			$xml_write = new XMLexporter();
			$xml_write->add_group('songs');
			$xml_write->doc.=	"\r\n";
			foreach ($arr_song as $key => $value)	{
				$id = $value['id'];
				$name = $value['name'];
				$file = $value['file'];
				$artist = $value['artist'];
				$xml_write->add_tag('song',"",array('id' => $id,
																			 'name' => $name,
																			 'file' => $file,
																			 'artist' => $artist
																			 ),false);
	
			}
			$xml_write->close_group();
			$xml_write->doc.=	"\r\n";
			
			$content_xml = "<?xml version='1.0' encoding='utf-8'?>\r\n\r\n";
			$content_xml .= $xml_write->output();
			$xml_write = null;
			
			//echo "content_xml = ".$content_xml;
			$path = PATH_PLUGINS.DS.$data['folder'].DS."trustvn_playlist.xml";
			if($handle = @fopen($path, "w")){
				fwrite($handle, $content_xml, strlen($content_xml));
				fclose($handle);
				$mess =   "Cập nhật thành công";
        $url = $data['link_action'];
        $func->html_redirect($url, $mess);
						
			}else{
				$mess =  $func->html_err("Khong mo duoc file trustvn_playlist.xml ");
			}	
			
			
			// cap nhat file setting
			$autoplay = ($params['autoplay']) ? "true" : "false";
			$content_xml_setting ="<?xml version='1.0' encoding='utf-8'?>\r\n<settings>\r\n<configure autoplay=\"{$autoplay}\" default_volume=\"{$params['default_volume']}\" screentext=\"{$params['screentext']}\" />\r\n</settings>";
			$path = PATH_PLUGINS.DS.$data['folder'].DS."trustvn_settings.xml";
			if($handle = @fopen($path, "w")){
				fwrite($handle, $content_xml_setting, strlen($content_xml_setting));
				fclose($handle);
			}	
			
			
    }//end update
	
	
	
?>
<?=$mess?>
<form action="<?php echo $data['link_action'] ?>" method="post" enctype="multipart/form-data" name="myForm" id="myForm"  class="validate">
<table width="100%"  border="0"  cellspacing="1" cellpadding="1" class="admintable">
	<tr  >
    <td  class="row1" >Name : </td>
    <td  class="row0"> <strong class="font_err"><?php echo $data['name']; ?></strong> </td> 
  </tr>
  
  <tr class="form-required" >
    <td  class="row1" width="20%">Title : </td>
    <td  class="row0"><input name="title" type="text" size="60" maxlength="250" value="<?php echo $data['title']; ?>"  >
  </td>
  </tr>
  
  <tr  >
    <td  class="row1" >Folder : </td>
    <td  class="row0"> <strong class="font_err"><?php echo $data['folder']; ?></strong> </td> 
  </tr>

   <tr>
    <td class="row1">Tham số : </td>
    <td  class="row0" >
    
    <table width="100%" cellspacing="1" cellpadding="1" border="0" class="admintable">
    <tr class="row_title">
      <td><strong>DANH SÁCH BÀI NHẠC</strong></td>
      <td width="30%"><strong>CẤU HÌNH</strong></td>
    </tr>
    <tr>
      <td valign="top">
      <p class="font_err" align="center"> <strong>Lưu ý :</strong> Tên bài nhạc không được viết tiếng việt </p>
      <table cellspacing="1" class="adminlist">
      <thead>
      <tr>
          <th width="5%" align="center" ><input type="checkbox" value="all" class="checkbox" name="checkall" id="checkall"/></th>
          <th width="20%" align="left" >Name</th>
          <th width="20%" align="center" >Artist</th>
          <th width="20%" align="left" >File</th>
          <th width="15%" align="center" >Size</th>
          <th width="5%"  align="center" >Display</th>
          </tr>
      </thead>
      <tbody>
			 
      <?php
				$path_music = PATH_PLUGINS.DS.$data['folder'].DS."music";
      	$fileList 	= vnT_Folder::files($path_music);
				if ($fileList !== false) 
				{
					foreach ($fileList as $file)
					{
						$tmp = array();
						$tmp['name'] = $file;
						$tmp['path'] = str_replace(DS, '/', vnT_Path::clean($path_music.DS.$file));
						$tmp['path_relative'] = str_replace($conf['rootpath'], '', $tmp['path']);
						$tmp['size'] = filesize($tmp['path']);
						
						$ext = strtolower(vnT_File::getExt($file));
						
						$iconfile_16 = DIR_IMAGE_MEDIA."/mime-icon-16/".$ext.".png";
						if (file_exists($iconfile_16)) {
							$tmp['icon_16'] = $iconfile_16;
						}else{
							$tmp['icon_16'] = DIR_IMAGE_MEDIA."/con_info.png";	
						} 
						
						$row_id = str_replace(".","_",$tmp['name']);
						
						$song_name = strtolower(substr($tmp['name'],0, strrpos($tmp['name'], ".")));
						$song_name = str_replace("_"," ",$song_name);						
						$song_url =  str_replace($conf['rootpath'], $conf['rooturl'], $tmp['path']);					
						$artist='';
						if (array_key_exists($row_id,$arr_song)){
							$display ="<img src=\"{$vnT->dir_images}/dispay.gif\" width=15  />&nbsp;" ;
							$song_name = $arr_song[$row_id]['name'];
							$artist = $arr_song[$row_id]['artist'];
						}else{
							$display ="<img src=\"{$vnT->dir_images}/nodispay.gif\"  width=15 />&nbsp;" ;
						}
			
				?>
		      <tr class="row0" id="row_<?=$row_id?>" > 
          <td  align="center" ><input type="checkbox" name="del_id[]" value="<?php echo $row_id; ?>" class="checkbox" ></td>
          <td  align="left"><input name="song_name[<?=$row_id?>]" type="text" size="12" value="<?=$song_name?>" class='textfiled' onchange="javascript:do_check('<?=$row_id?>')" /> </td>
          <td  align="left"><input name="artist[<?=$row_id?>]" type="text" size="12" value="<?=$artist?>" class='textfiled' onchange="javascript:do_check('<?=$row_id?>')" /> </td>
          <td  align="left">
						<?=$tmp['name']?>
            <input name="song_url[<?=$row_id?>]" type="hidden" value="<?=$song_url?>" />	
          </td>
          <td  align="center" ><?php echo $func->format_size($tmp['size']); ?></td>
          <td  align="center" >
          <input name="h_id[]" type="hidden" value="<?=$tmp['name']?>" />	
          <?=$display?></td>
          </tr>  
				<?php
					
					}// end for
				}// end if
				
			?>
      	</tbody>
				</table>
        <table  border="0" cellspacing="2" cellpadding="2" >
           <tr>
           <td width="40" align="center" ><img src="<?=$vnT->dir_images?>/arr_bottom.gif" ></td>
           <td>
           <input type="button" onclick="do_submit_cus('do_hidden','myForm')" class="button" value=" Ẩn " name="btnHidden"/>&nbsp; 
           <input type="button" onclick="do_submit_cus('do_display','myForm')" class="button" value=" Hiện " name="btnDisplay"/>&nbsp; 
           <input type="button" onclick="do_submit_cus('do_edit','myForm')" class="button" value=" Cập nhật " name="btnEdit"/>&nbsp; 
           <input type="button" onclick="do_submit_cus('do_del','myForm')" class="button" value=" Xóa " name="btnDel"/>
           
           <input type="hidden" name="do_action" id="do_action" value="" >
           </td>
         </tr>
    	 </table>
      </td>
      <td valign="top">
      	
        <table  border="0" cellspacing="0" cellpadding="0">
        
        <tr>
          <td class="row1">Width</td>
          <td><input name="params[width]" type="text" size="10" maxlength="250" value="<?php echo $params['width']; ?>"  > px</td>
        </tr>
        <tr>
          <td class="row1">Height</td>
          <td><input name="params[height]" type="text" size="10" maxlength="250" value="<?php echo $params['height']; ?>"  > px</td>
        </tr>
        
        <tr>
          <td class="row1">Autoplay</td>
          <td><?php echo vnT_HTML::list_yesno("params[autoplay]",$params['autoplay']); ?></td>
        </tr>
        <tr>
          <td class="row1">Volume</td>
          <td><input name="params[default_volume]" type="text" size="10" maxlength="250" value="<?php echo $params['default_volume']; ?>"  ></td>
        </tr>
        
        <tr>
          <td class="row1">Screentext</td>
          <td><input name="params[screentext]" type="text" size="20" maxlength="250" value="<?php echo $params['screentext']; ?>"  ></td>
        </tr>
        
      </table>
      <br />
<table width="100%" cellspacing="1" cellpadding="1" border="0" class="admintable">
  <tr class="row_title">
    <td  ><strong>Upload Music [ Max = <?=ini_get('upload_max_filesize')?> ] : </strong></td>
  </tr>
  <tr>
    <td ><input type="file" name="file_upload" id="file_upload"  /> </td>

  </tr>
  <tr>
    <td align="center" ><input name="btnUpload" type="submit" class="button" value="Start Upload" /></td>

  </tr>

</table>
  
      </td>
    </tr>
  </table>

    </td>
  </tr>
  
  <tr>
    <td class="row1">Hiển thị : </td>
    <td  class="row0"><?php echo $data['list_display']?></td>
  </tr>



		<tr align="center">
    <td class="row1" >&nbsp; </td>
			<td class="row0" >
				<input type="submit" name="do_submit" value="Submit" class="button">
				<input type="reset" name="btnReset" value="Reset" class="button">
			</td>
		</tr>
	</table>
</form>