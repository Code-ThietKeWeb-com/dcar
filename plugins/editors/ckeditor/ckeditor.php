<?php
class plgEditorCkeditor {

	
	/**
	 * Method to handle the onInitEditor event.
	 *  - Initializes the fckeditor Lite WYSIWYG Editor
	 *
	 * @access public
	 * @return string JavaScript Initialization string
	 * @since 1.5
	 */
	function doInit()
	{
		global $vnT,$conf;
		$vnT->html->addScript($conf['rooturl'].'plugins/editors/ckeditor/ckeditor.js'); 
		
	}

	/**
	 * fckeditor Lite WYSIWYG Editor - get the editor content
	 *
	 * @param string 	The name of the editor
	 */
	function doGetContent( $editor ) {
			
		return " CKEDITOR.instances.$editor.getData(); ";
	}

	/**
	 * fckeditor Lite WYSIWYG Editor - set the editor content
	 *
	 * @param string 	The name of the editor
	 */
	function doSetContent( $editor, $html ) {
		//return " oFCKeditor.InsertHtml = '" .  htmlentities($html) . "';alert('".$html."');";
	}

	/**
	 * fckeditor Lite WYSIWYG Editor - copy editor content to form field
	 *
	 * @param string 	The name of the editor
	 */
	function doSave( $editor ) { /* We do not need to test for anything */	}

	/**
	 * fckeditor Lite WYSIWYG Editor - display the editor
	 *
	 * @param string The name of the editor area
	 * @param string The content of the field
	 * @param string The name of the form field
	 * @param string The width of the editor area
	 * @param string The height of the editor area
	 * @param int The number of columns for the editor area
	 * @param int The number of rows for the editor area
	 * @param mixed Can be boolean or array.
	 */
	function doDisplay( $name, $content, $width, $height, $toolbar = "Default" ,$module_name = '' , $folder = '' ,$is_admin=1 )
	{
		global $vnT,$conf;
      /* Generate the Output */
		if (is_numeric( $width )) 	{		$width .= 'px';		}
		if (is_numeric( $height )) 	{		$height .= 'px';	}
		
		if($_SERVER['HTTP_HOST']!="localhost" && strstr($conf['rooturl'],"localhost") )
		{
			$rooturl = str_replace("localhost",$_SERVER['HTTP_HOST'],$conf['rooturl']);
		}else{
			$rooturl = $conf['rooturl'];
		}
		
 		if( empty( $module_name ) )
		{  		
			if(empty($folder))
			{
				$dir="";
				if (@ini_get('safe_mode') && $vnT->conf['ftp_enable'])// safe_mode = On
				{
					$dir = @date("m_Y");
				}else{
					$dir = @date("m_Y");
				}
				
				if($dir)
				{
					$res_ck = $vnT->DB->query("SELECT folder_id FROM media_folders WHERE parentid=1 AND folder_name='".$dir."' ");
					if(!$vnT->DB->num_rows($res_ck))
					{
						$res_dir = $vnT->func->vnt_mkdir($vnT->conf['rootpath']."vnt_upload/File" , $dir,1);
						if($res_dir[0]==1)
						{
							$cot['parentid'] = 1;
							$cot['folder_path'] = "File/".$dir;
							$cot['folder_name'] = $dir;
							$cot['date_create'] = time();
							$vnT->DB->do_insert("media_folders",$cot);	
						}
					}
					$folder = 'File/'.$dir;
				}else{
					$folder = 'File';
				}
			}			
		}else{
			$folder = ($folder) ? $module_name.'/'.$folder : $module_name ;	
		}		 
		
		$html .= "\n".'<textarea name="'.$name.'" id="'.$name.'" cols="75" rows="20" style="width:'.$width.'; height:'.$height.';" >' .$content.   '</textarea>';		
 		
		$html .= "\n"."<script type='text/javascript'> 
		inFormOrLink = false ;
		var text_{$name} = CKEDITOR.replace( '{$name}',
		{  
			language : 'vi', 
			allowedContent: true,
			toolbar : '".$toolbar."',
			autoParagraph :false,
		";			
		if($is_admin)	{
      $folder_admin = ($conf['folder_admin']) ? $conf['folder_admin'] : "admin";
      $html .="filebrowserBrowseUrl : '".$rooturl.$folder_admin."/?mod=media&act=popup_media&module=".$module_name."&folder=".$folder."&type=file',
			filebrowserImageBrowseUrl : '".$rooturl.$folder_admin."/?mod=media&act=popup_media&stype=editor&module=".$module_name."&folder=".$folder."&type=image',
			filebrowserFlashBrowseUrl : '".$rooturl.$folder_admin."/?mod=media&act=popup_media&module=".$module_name."&folder=".$folder."&type=flash'," ;
		 
		}else{			 
			$html .="filebrowserBrowseUrl : false,
			filebrowserImageBrowseUrl : false,
			filebrowserFlashBrowseUrl : false, " ;
		}
		$html .="
			height : '".$height."',
			width : '".$width."' 
		});
		</script> " ;
		return $html;
	}
	

}
?>