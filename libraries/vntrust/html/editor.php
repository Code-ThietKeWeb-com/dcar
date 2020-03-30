<?php
/*================================================================================*\
|| 							Name code : editor.php 		 		 																  # ||
||  				Copyright � 2008 by Thai Son - CMS vnTRUST                					# ||
\*================================================================================*/
/**
 * vnT_Editor class to handle WYSIWYG editors
 *
 * @package		libraries
 * @version : 1.0
 * @date upgrade : 11/12/2007 by Thai Son
 */

if (!defined('IN_vnT')){
     die('Hacking attempt!');
}


class vnT_Editor 
{

	/**
	 * Load the editor
	 *
	 * @access	private
	 * @param	array	Associative array of editor config paramaters
	 * @since	1.5
	 */
	 
	function loadEditor()
	{
		global $vnT,$conf ;
		
		// Build the path to the needed editor plugin
		$editor_name = ($conf['editor']) ? $conf['editor'] : "ckeditor" ;
		$path = $conf['rootpath'].'plugins/editors/'.$editor_name."/".$editor_name.'.php';
		
		if(!file_exists($path)){
			$vnT->error[] = "Cannot load the editor ";
			return false;
			
		}
		// Require plugin file
		require_once $path;
		
		// Build editor plugin classname
		$classname = 'plgEditor'. ucfirst($editor_name);
	
		return  new $classname () ;
		
	}

	
}