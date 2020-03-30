<?php
/**
* @version $Id: module.class.php 4996 2006-09-10 16:33:55Z friesengeist $
* @package Joomla
* @subpackage Installer
* @copyright Copyright (C) 2005 Open Source Matters. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

// no direct access

/**
* Module installer
* @package Joomla
* @subpackage Installer
*/
class InstallerModule extends cmsInstaller {
	var $i_moduleadmindir 	= '';
	var $i_hasinstallfile 		= false;
	var $i_installfile 			= '';

	function AdminDir($p_dirname = null) {
		if(!is_null($p_dirname)) {
			$this->i_moduleadmindir = cmsPathName($p_dirname);
		}
		return $this->i_moduleadmindir;
	}

	/**
	* Custom install method
	* @param boolean True if installing from directory
	*/
	function install($p_fromdir = null) {
		global $rootDir,$database,$DB;

		if (!$this->preInstallCheck( $p_fromdir, 'module' )) {
			return false;
		}

		// aje moved down to here. ??  seemed to be some referencing problems
		$xmlDoc 	= $this->xmlDoc();
		$cmsinstall = &$xmlDoc->documentElement;

		// Set some vars
		$e = &$cmsinstall->getElementsByPath('name', 1);
		$this->elementName($e->getText());
		$this->elementDir( cmsPathName( $rootDir . "/modules/"
			. strtolower( str_replace(" ","",$this->elementName())) . "/" )
		);
		$mod_name				= strtolower(str_replace(" ","",$this->elementName()));
		/*
		$this->AdminDir( cmsPathName( $rootDir . "/admin/modules/"
			. strtolower(  str_replace( " ","",$this->elementName() )."_ad"  ) )
		);
		*/
		$this->AdminDir( cmsPathName( $rootDir . "/admin/modules" )	);

		if (file_exists($this->elementDir())) {
			$this->setError( 1, 'Another module is already using directory: "' . $this->elementDir() . '"' );
			return false;
		}

		if(!file_exists($this->elementDir()) && !cmsMakePath($this->elementDir())) {
			$this->setError( 1, 'Failed to create directory "' . $this->elementDir() . '"' );
			return false;
		}

/*--------- khong tao thu muc mod_ad nua
		if(!file_exists($this->AdminDir()) && !cmsMakePath($this->AdminDir())) {
			$this->setError( 1, 'Failed to create directory "' . $this->AdminDir() . '"' );
			return false;
		}
*/
		// Find files to copy
		if ($this->parseFiles( 'files' ) === false) {
			return false;
		}
		$this->parseFiles( 'images' );
		$this->parseFiles( 'administration/files','','',1 );
		$this->parseFiles( 'administration/images','','',1 );
		
		//folder
		$this->parseFolders( 'folder' );
		$this->parseFolders( 'administration/folder',1 );
		
		//upload
		$uploadFrom = substr(cmsPathName($this->installDir()."/uploads"),0,-1);
		$uploadTo = substr(cmsPathName($rootDir ."/vnt_upload/".$mod_name),0,-1);
		
		cmsMakePath( cmsPathName( $rootDir ."/vnt_upload/".$mod_name) );
		$this->parseDir($uploadFrom,$uploadTo);
		
		//language
		$dir_install_lang_default =  cmsPathName( $this->installDir() . "/language/vn" );
		$res = $DB->query("select name from language  ");
		while($r = $DB->fetch_row($res)){
			$lang_name = $r['name'];
			$dir_language = cmsPathName( $rootDir . "/language/".$lang_name."/modules" );
			$dir_install_lang =  cmsPathName( $this->installDir() . "/language/".$lang_name );
			$file_lang = $mod_name.".php";
			if (file_exists($dir_install_lang."/".$file_lang)) {
				$this->copyFiles($dir_install_lang , $dir_language, array($file_lang));
			}else{
				$this->copyFiles($dir_install_lang_default , $dir_language, array($file_lang));
			}
		}
			
		// Are there any SQL queries??
		$query_element = &$cmsinstall->getElementsByPath('install/queries', 1);
		if (!is_null($query_element)) {
			$queries = $query_element->childNodes;
			foreach($queries as $query)
			{
				/*$database->setQuery( $query->getText());
				if (!$database->query())
				{
					$this->setError( 1, "SQL Error " . $database->stderr( true ) );
					return false;
				}
				*/
				$sql = $query->getText() ;
				$ok = $DB->query($sql) ;
				if(!$ok)
				{
					$this->setError( 1, "SQL Error " . $DB->debug() );
					return false;
				}
			}
		}



	$ntsDirAdminMod = $this->AdminDir()."/". strtolower( str_replace(" ","",$this->elementName())."_ad/");
	// Is there an installfile
		$installfile_elemet = &$cmsinstall->getElementsByPath('installfile', 1);

		if (!is_null($installfile_elemet)) {
			// check if parse files has already copied the install.module.php file (error in 3rd party xml's!)
			if (!file_exists($ntsDirAdminMod.$installfile_elemet->getText())) {
				
				if(!$this->copyFiles($this->installDir(), $ntsDirAdminMod, array($installfile_elemet->getText())))  			{
					$this->setError( 1, 'Could not copy PHP install file.' );
					return false;
				}
			}
			$this->hasInstallfile(true);
			$this->installFile($installfile_elemet->getText());
		}
		// Is there an uninstallfile
		$uninstallfile_elemet = &$cmsinstall->getElementsByPath('uninstallfile',1);
		if(!is_null($uninstallfile_elemet)) {
			if (!file_exists($ntsDirAdminMod.$uninstallfile_elemet->getText())) {
				if(!$this->copyFiles($this->installDir(), $ntsDirAdminMod, array($uninstallfile_elemet->getText()))) {
					$this->setError( 1, 'Could not copy PHP uninstall file' );
					return false;
				}
			}
		}
		
		// Is the menues ?
		$adminmenu_element = &$cmsinstall->getElementsByPath('administration/menu',1);
		if(!is_null($adminmenu_element))
		{
			$mod_name				= strtolower(str_replace(" ","",$this->elementName()));
			$mod_name_vn = $mod_name ;
			if(!is_null( $cmsinstall->getElementsByPath('name_vn', 1))) {				
				$mod_name_vn 	  = strtolower(str_replace(" ","",$cmsinstall->getElementsByPath('name_vn', 1)->getText()));
			}
			$mod_admin_menuname		= $adminmenu_element->getText();
			
			$menugroup_element	= &$cmsinstall->getElementsByPath('administration/menugroup',1);
			$mod_menugroup = $menugroup_element->getText();
			
			$mod_id = $this->createDataModule($mod_admin_menuname,$mod_name,$mod_name_vn);
			$mod_menu_parentid	= $this->createParentMenu($mod_menugroup,$mod_admin_menuname,$mod_id);
			
			$adminsubmenu_element	= &$cmsinstall->getElementsByPath('administration/submenu',1);
			if(!is_null($adminsubmenu_element))
			{
				
				if($mod_menu_parentid === false)
				{
					return false;
				}
			
				$mod_admin_submenus = $adminsubmenu_element->childNodes;

				$displayorder = 0;
				foreach($mod_admin_submenus as $admin_submenu)
				{
					
					$menu['g_name'] = $mod_menugroup;
					$menu['title'] = $admin_submenu->getText();
					$menu['mod'] = $mod_name;
					if ( $admin_submenu->getAttribute("act"))	{
						$menu['act'] = $admin_submenu->getAttribute("act");
					}else{
						$menu['act']="";
					}
					if ( $admin_submenu->getAttribute("sub"))	{
						$menu['sub'] = $admin_submenu->getAttribute("sub");
					}else{
						$menu['sub']="";
					}
					
					$arr_title = @explode("|",$menu['title']);
					$title_vn = trim($arr_title[0]);
					$title_en = (trim($arr_title[1])) ? trim($arr_title[1]) : $title_vn;
					
					
					
					//tao option cho admin_permission
					if (!$admin_submenu->getAttribute("sub"))	{
						 $text_option = $admin_submenu->getAttribute("textOption");
						 $cot['g_name'] = $menu['g_name'];
						 $cot['title_vn'] = $title_vn;
						 $cot['title_en'] = $title_en;
						 $cot['module'] = $mod_name;
						 $cot['act'] = $menu['act'];
						 $cot['text_option'] = $text_option;
						 $ok = $DB->do_insert("admin_permission",$cot);
						 if(!$ok){
						 	$this->setError( 1, $DB->debug() );
							return false;
						 }
					}
					
					// insert admin_menu
					$cot_menu['g_name'] = $menu['g_name'];
					$cot_menu['title_vn'] = $title_vn;
					$cot_menu['title_en'] = $title_en;
					$cot_menu['module'] = $menu['mod'];
					$cot_menu['act'] = $menu['act'];
					$cot_menu['sub'] = $menu['sub'];
					$cot_menu['parentid'] = $mod_menu_parentid;
					$cot_menu['displayorder'] = $displayorder;
					$cot_menu['mod_id'] = $mod_id;
  				$ok = $DB->do_insert("admin_menu",$cot_menu);
					if(!$ok)
					{
						$this->setError( 1, $DB->debug() );
						return false;
					}
					
					$displayorder++;
					
				}
			}	
		}


		if ($this->hasInstallfile()) {
			/*
			if (is_file($this->AdminDir() . '/' . $this->installFile())) {
				require_once($this->AdminDir() . "/" . $this->installFile());
				$ret = mod_install();
				if ($ret != '') {
					$this->setError( 0, $desc . $ret );
				}
			}
			*/
			if (is_file($ntsDirAdminMod . '/' . $this->installFile())) {
				require_once($ntsDirAdminMod . "/" . $this->installFile());
				$ret = mod_install();
				if ($ret != '') {
					$this->setError( 0, $desc . $ret );
				}
			}
		}
		
		//return $this->copySetupFile();
		//copy file xml
		if(!$this->copyFiles($this->installDir(), $ntsDirAdminMod,array( basename( $this->installFilename() ) ), true ) ) 			
		{
				$this->setError( 1, 'Khong the copy file XML setup.' );
				return false;
		}
		
		return true;
	}
	
	//insert vao bang modules
	

	function createParentMenu($g_name,$title,$mod_id) {
		global $DB;
		$arr_title = @explode("|",$title);
		$title_vn = trim($arr_title[0]);
		$title_en = (trim($arr_title[1])) ? trim($arr_title[1]) : $title_vn;
					
		$cot['g_name'] = $g_name;
		$cot['title_vn'] = $title_vn;
		$cot['title_en'] = $title_en;
		$cot['mod_id'] = $mod_id;
		$cot['parentid'] = 0;
		$cot['displayorder'] = 1;

		$ok = $DB->do_insert("admin_menu",$cot);
		if(!$ok)
		{
			$this->setError( 1, $DB->debug() );
			return false;
		}
		$menuid = $DB->insertid();
		return $menuid;
	}
	
	function createDataModule($_menuname,$_modname,$_modname_vn, $_image = "js/ThemeOffice/module.png") {
		global $DB;
		$arr_title = @explode("|",$_menuname);
		$title_vn = trim($arr_title[0]);
		$title_en = (trim($arr_title[1])) ? trim($arr_title[1]) : $title_vn;
		
		$_modname_vn = ($_modname_vn) ? $_modname_vn : $_modname ; 
		$cot['name'] = $title_vn;
		$cot['mod_name'] = $_modname;
		$cot['seo_name'] = $_modname;
		$cot['seo_name_vn'] = $_modname_vn;
		$cot['ordering'] = 0;
		$cot['mod_icon'] = $_image;
		$ok = $DB->do_insert("modules",$cot);

		if(!$ok)
		{
			$this->setError( 1, $DB->debug() );
			return false;
		}
		return $DB->insertid();
	}
	
	/**
	* Custom install method
	* @param int The id of the block
	* @param string The URL option
	* @param int The client id
	*/
	function uninstall( $cid, $option, $client=0 ) {
		global $database,$rootDir,$DB;

		$uninstallret = '';
		$res_ck = $DB->query("select * from modules where id=".(int) $cid." ");
		if (!$row = $DB->fetch_row($res_ck)){
				HTML_installer::showInstallMessage($DB->debug(),'Uninstall -  error',
				$this->returnTo( $option, 'module', $client ) );
			exit();
		}
		
		$sql = "DELETE FROM modules WHERE id = " . (int) $row['id']	;
		$okDel=$DB->query($sql);
		if ($okDel) {
			//xoa admin_menu
			$DB->query("DELETE FROM admin_menu WHERE mod_id=".(int) $row['id']);
			//xoa admin_permission
			$DB->query("DELETE FROM admin_permission WHERE module = '".$row['mod_name']."' ")	;
		}else{
			HTML_installer::showInstallMessage($DB->debug(),'Uninstall -  error',
				$this->returnTo( $option, 'module', $client ) );
			exit();
		}

		// Try to find the uninstall file
		$filesindir = cmsReadDirectory( $rootDir.'/admin/modules/'.$row['mod_name'].'_ad', 'uninstall' );
		if (count( $filesindir ) > 0) {
			$uninstall_file = $filesindir[0];
			if(file_exists($rootDir.'/admin/modules/'.$row['mod_name'] .'_ad/'.$uninstall_file))
			{
				require_once($rootDir.'/admin/modules/'.$row['mod_name'] .'_ad/'.$uninstall_file );
				$uninstallret = mod_uninstall();
			}
		}

		// Try to find the XML file
		$filesindir = cmsReadDirectory( cmsPathName( $rootDir.'/admin/modules/'.$row['mod_name'].'_ad' ), '.xml$');
		if (count($filesindir) > 0) {
			$ismosinstall = false;
			$found = 0;
			foreach ($filesindir as $file) {
				$xmlDoc = new DOMIT_Lite_Document();
				$xmlDoc->resolveErrors( true );
				if (!$xmlDoc->loadXML( $rootDir."/admin/modules/".$row['mod_name'] . "_ad/" . $file, false, true )) {
					return false;
				}
				$root = &$xmlDoc->documentElement;

				if ($root->getTagName() != 'cmsinstall') {
					continue;
				}
				$found = 1;

				$query_element = &$root->getElementsbyPath( 'uninstall/queries', 1 );
				if(!is_null($query_element))
				{
					$queries = $query_element->childNodes;
					foreach($queries as $query)
					{
						$sql = $query->getText() ;
						$ok = $DB->query($sql) ;
 						if (!$ok)
						{
							HTML_installer::showInstallMessage($DB->debug(),'Uninstall -  error',
								$this->returnTo( $option, 'module', $client ) );
							exit();
						}
					}
				}
			}
			if(!$found) {
				HTML_installer::showInstallMessage('XML File invalid','Uninstall -  error',
					$this->returnTo( $option, 'module', $client ) );
				exit();
			}
		} else {
			/*
			HTML_installer::showInstallMessage( 'Could not find XML Setup file in '.$rootDir.'/admin/modules/'.$row->option,
				'Uninstall -  error', $option, 'module' );
			exit();
			*/
		}
		
		//Delete language
		$res = $DB->query("select name from language  ");
		while($r = $DB->fetch_row($res)){
			$lang_name = $r['name'];
			$dir_language = cmsPathName( $rootDir . "/language/".$lang_name."/modules" );
			$file_lang = $dir_language.$row['mod_name'].".php";
			if (file_exists($file_lang)) {
				unlink ($file_lang) ;
			}
		}
		
		// Delete directories

		if (trim( $row['mod_name'] )) {
			$result = 0;
			$path = cmsPathName( $rootDir.'/admin/modules/' .$row['mod_name'].'_ad' );
			if (is_dir( $path )) {
				$result |= deldir( $path );
			}
			$path = cmsPathName( $rootDir.'/modules/'.$row['mod_name'] );
			if (is_dir( $path )) {
				$result |= deldir( $path );
			}
			// del upload
			$path = cmsPathName( $rootDir.'/vnt_upload/'.$row['mod_name'] );
			if (is_dir( $path )) {
				$result |= deldir( $path );
			}
			
			return $result;
		} else {
			HTML_installer::showInstallMessage( 'Option field empty, cannot remove files', 'Uninstall -  error', $option,'module');
			exit();
		}

		return $uninstallret;
	}
}
?>
