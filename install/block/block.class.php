<?php

class InstallerBlock extends cmsInstaller {
	var $i_AdminDir 	= '';
	var $i_hasinstallfile 		= false;
	var $i_installfile 			= '';

	function AdminDir($p_dirname = null) {
		if(!is_null($p_dirname)) {
			$this->i_AdminDir = cmsPathName($p_dirname);
		}
		return $this->i_AdminDir;
	}

	/**
	* Custom install method
	* @param boolean True if installing from directory
	*/
	function install($p_fromdir = null) {
		global $rootDir,$database,$DB;

		if (!$this->preInstallCheck( $p_fromdir, 'block' )) {
			return false;
		}

		// aje moved down to here. ??  seemed to be some referencing problems
		$xmlDoc 	= $this->xmlDoc();
		$cmsinstall = &$xmlDoc->documentElement;

		// Set some vars
		$e = &$cmsinstall->getElementsByPath('name', 1);
		$this->elementName($e->getText());
		$this->elementDir( cmsPathName( $rootDir . "/blocks/"
			. strtolower( str_replace(" ","",$this->elementName())) . "/" )
		);
		
		$block_name				= strtolower(str_replace(" ","",$this->elementName()));
		
		/*
		$this->AdminDir( cmsPathName( $rootDir . "/admin/blocks/"
			. strtolower(  str_replace( " ","",$this->elementName() )."_ad"  ) )
		);
		*/
		$this->AdminDir( cmsPathName( $rootDir . "/admin/blocks" )	);

		if (file_exists($this->elementDir())) {
			$this->setError( 1, 'Another block is already using directory: "' . $this->elementDir() . '"' );
			return false;
		}

		if(!file_exists($this->elementDir()) && !cmsMakePath($this->elementDir())) {
			$this->setError( 1, 'Failed to create directory "' . $this->elementDir() . '"' );
			return false;
		}

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
		
		//language
		$dir_install_lang_default =  cmsPathName( $this->installDir() . "/language/vn" );
		$res = $DB->query("select name from language  ");
		while($r = $DB->fetch_row($res)){
			$lang_name = $r['name'];
			$dir_language = cmsPathName( $rootDir . "/language/".$lang_name."/blocks" );
			$dir_install_lang =  cmsPathName( $this->installDir() . "/language/".$lang_name );
			$file_lang = $block_name.".php";
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
				$database->setQuery( $query->getText());
				if (!$database->query())
				{
					$this->setError( 1, "SQL Error " . $database->stderr( true ) );
					return false;
				}
			}
		}
		
		
		
		$title_element = &$cmsinstall->getElementsByPath('title', 1);
		$block_title		= $title_element->getText();
		
		if ( $title_element->getAttribute("noCache"))	{
			$cache = 0;
		}else{
			$cache =1;
		}
		
		$block_id = $this->createDatablock($block_name,$block_title,$cache);
		
		// Cap nhat menu Admin
		$menugroup_element = &$cmsinstall->getElementsByPath('administration/menugroup',1);
		if(!is_null($menugroup_element))
		{
			$block_menugroup = $menugroup_element->getText();
			$block_menu_parentid	= $this->createParentMenu($block_menugroup,$block_title,$block_id);
			
			$adminsubmenu_element	= &$cmsinstall->getElementsByPath('administration/submenu',1);
			if(!is_null($adminsubmenu_element))
			{
				
				if($mod_menu_parentid === false)
				{
					return false;
				}
			
				$block_admin_submenus = $adminsubmenu_element->childNodes;

				$displayorder = 0;
				foreach($block_admin_submenus as $admin_submenu)
				{
					
					$menu['g_name'] = $block_menugroup;
					$menu['title'] = $admin_submenu->getText();
					$menu['block'] = $block_name;
					if ( $admin_submenu->getAttribute("act"))	{
						$menu['act'] = $admin_submenu->getAttribute("act");
					}
					if ( $admin_submenu->getAttribute("sub"))	{
						$menu['sub'] = $admin_submenu->getAttribute("sub");
					}
					$menu['parentid'] = $block_menu_parentid;
					$menu['displayorder'] = $displayorder;
					$menu['block_id'] = $block_id;
					//$ok = $DB->do_insert("admin_menu",$menu);
					$sql_insert = "INSERT INTO admin_menu 
												 (`g_name`,`title`,`block`,`act`,`sub`,`parentid`,`displayorder`,`block_id`) 
												 VALUES ('".$menu['g_name']."' , '".$menu['title']."' , 
												 '".$menu['block']."' , '".$menu['act']."' , '".$menu['sub']."' ,
												 '".$menu['parentid']."' , '".$menu['displayorder']."' , '".$menu['block_id']."' )";
					
  				$ok = $DB->query($sql_insert);
					if(!$ok)
					{
						$this->setError( 1, $DB->debug() );
						return false;
					}
					
					$displayorder++;
					
				}
			}	
		}


		$ret = "Block ".$block_name." Da duoc cai dat thanh cong";
		
		//return $this->copySetupFile();
		//copy file xml
		if(!$this->copyFiles($this->installDir(),$this->elementDir(),array( basename( $this->installFilename() ) ), true ) ) 			
		{
				$this->setError( 1, 'Khong the copy file XML setup.' );
				return false;
		}
		
		return true;
	}
	
	//insert vao bang blocks
	

	function createParentMenu($g_name,$title,$block_id) {
		global $DB;
		$res_ck =$DB->query("select * from  admin_menu where g_name='$g_name' ");
		if ($row = $DB->fetch_row($res_ck)){
			$menuid = $row['id'];
		}else{
			$cot['g_name'] = $g_name;
			$cot['title'] = $title;
			$cot['block_id'] = $block_id;
			$cot['parentid'] = 0;
			$cot['displayorder'] = 0;
	
			$ok = $DB->do_insert("admin_menu",$cot);
			if(!$ok)
			{
				$this->setError( 1, $DB->debug() );
				return false;
			}
			$menuid = $DB->insertid();
		}
		return $menuid;
	}
	
	function createDatablock($name,$title,$cache=1) {
		global $DB;
		$cot['name'] = $name;
		$cot['title'] = $title;
		$cot['cache'] = $cache;
		$cot['align'] = 'right';
		$ok = $DB->do_insert("layout",$cot);

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
		$res_ck = $DB->query("select * from  layout where id=".(int) $cid." ");
		if (!$row = $DB->fetch_row($res_ck)){
				HTML_installer::showInstallMessage($DB->debug(),'Uninstall -  error',
				$this->returnTo( $option, 'block', $client ) );
			exit();
		}
		
		$sql = "DELETE FROM layout WHERE id = " . (int) $row['id']	;
		$okDel=$DB->query($sql);
		if ($okDel) {
				$DB->query("DELETE FROM admin_menu WHERE block_id=".(int) $row['id']);
		}else{
			HTML_installer::showInstallMessage($DB->debug(),'Uninstall -  error',
				$this->returnTo( $option, 'block', $client ) );
			exit();
		}

		// Try to find the uninstall file
		$filesindir = cmsReadDirectory( $rootDir.'/admin/blocks/'.$row['name'].'_ad', 'uninstall' );
		if (count( $filesindir ) > 0) {
			$uninstall_file = $filesindir[0];
			if(file_exists($rootDir.'/admin/blocks/'.$row['name'] .'_ad/'.$uninstall_file))
			{
				require_once($rootDir.'/admin/blocks/'.$row['name'] .'_ad/'.$uninstall_file );
				$uninstallret = "Block ".$row['name']." da duoc xoa ra khoi he thong";
			}
		}

		// Try to find the XML file
		$filesindir = cmsReadDirectory( cmsPathName( $rootDir.'/blocks/'.$row['name'] ), '.xml$');
		if (count($filesindir) > 0) {
			$ismosinstall = false;
			$found = 0;
			foreach ($filesindir as $file) {
				$xmlDoc = new DOMIT_Lite_Document();
				$xmlDoc->resolveErrors( true );
				if (!$xmlDoc->loadXML( $rootDir."/blocks/".$row['name'] . "/" . $file, false, true )) {
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
						$database->setQuery( $query->getText());
						if (!$database->query())
						{
							HTML_installer::showInstallMessage($database->stderr(true),'Uninstall -  error',
								$this->returnTo( $option, 'block', $client ) );
							exit();
						}
					}
				}
			}
			if(!$found) {
				HTML_installer::showInstallMessage('XML File invalid','Uninstall -  error',
					$this->returnTo( $option, 'block', $client ) );
				exit();
			}
		} else {
			/*
			HTML_installer::showInstallMessage( 'Could not find XML Setup file in '.$rootDir.'/admin/blocks/'.$row->option,
				'Uninstall -  error', $option, 'block' );
			exit();
			*/
		}
		
		//Delete language
		$res = $DB->query("select name from language  ");
		while($r = $DB->fetch_row($res)){
			$lang_name = $r['name'];
			$dir_language = cmsPathName( $rootDir . "/language/".$lang_name."/blocks" );
			$file_lang = $dir_language.$row['name'].".php";
			if (file_exists($file_lang)) {
				unlink ($file_lang) ;
			}
		}
		
		// Delete directories

		if (trim( $row['name'] )) {
			$result = 0;
			$path = cmsPathName( $rootDir.'/admin/blocks/' .$row['name'].'_ad' );
			if (is_dir( $path )) {
				$result |= deldir( $path );
			}
			$path = cmsPathName( $rootDir.'/blocks/'.$row['name'] );
			if (is_dir( $path )) {
				$result |= deldir( $path );
			}
			return $result;
		} else {
			HTML_installer::showInstallMessage( 'Option field empty, cannot remove files', 'Uninstall -  error', $option,'block');
			exit();
		}

		return $uninstallret;
	}
}
?>
