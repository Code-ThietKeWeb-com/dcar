<?php

class cmsInstaller {
	// name of the XML file with installation information
	var $i_installfilename	= "";
	var $i_installarchive	= "";
	var $i_installdir		= "";
	var $i_iswin			= false;
	var $i_errno			= 0;
	var $i_error			= "";
	var $i_installtype		= "";
	var $i_unpackdir		= "";
	var $i_docleanup		= true;

	/** @var string The directory where the element is to be installed */
	var $i_elementdir 		= '';
	/** @var string The name of the CMS! element */
	var $i_elementname 		= '';
	/** @var string The name of a special atttibute in a tag */
	var $i_elementspecial 	= '';
	/** @var object A DOMIT XML document */
	var $i_xmldoc			= null;
	var $i_hasinstallfile 	= null;
	var $i_installfile 		= null;

	/**
	* Constructor
	*/
	function cmsInstaller() {
//		$this->i_iswin = (substr(PHP_OS, 0, 3) == 'WIN');
		$this->i_iswin = true;
	}
	/**
	* Uploads and unpacks a file
	* @param string The uploaded package filename or install directory
	* @param boolean True if the file is an archive file
	* @return boolean True on success, False on error
	*/
	function upload($p_filename = null, $p_unpack = true) {
		$this->i_iswin = (substr(PHP_OS, 0, 3) == 'WIN');
		$this->installArchive( $p_filename );

		if ($p_unpack) {
			if ($this->extractArchive()) {
				return $this->findInstallFile();
			} else {
				return false;
			}
		}
	}
	/**
	* Extracts the package archive file
	* @return boolean True on success, False on error
	*/
	function extractArchive() {
		global $rootDir;

		$base_Dir 		= cmsPathName( $rootDir . '/install/media' );

		$archivename 	= $base_Dir . $this->installArchive();
		$tmpdir 		= uniqid( 'install_' );

		$extractdir 	= cmsPathName( $base_Dir . $tmpdir );
		$archivename 	= cmsPathName( $archivename, false );

		$this->unpackDir( $extractdir );

		if (eregi( '.zip$', $archivename )) {
			// Extract functions
			require_once( $rootDir . '/install/includes/pcl/pclzip.lib.php' );
			require_once( $rootDir . '/install/includes/pcl/pclerror.lib.php' );
			//require_once( $rootDir . '/install/includes/pcl/pcltrace.lib.php' );
			//require_once( $rootDir . '/install/includes/pcl/pcltar.lib.php' );
			$zipfile = new PclZip( $archivename );
			if($this->isWindows()) {
				define('OS_WINDOWS',1);
			} else {
				define('OS_WINDOWS',0);
			}

			$ret = $zipfile->extract( PCLZIP_OPT_PATH, $extractdir );
			if($ret == 0) {
				$this->setError( 1, 'Unrecoverable error "'.$zipfile->errorName(true).'"' );
				return false;
			}
		} else {
			require_once( $rootDir . '/install/includes/Archive/Tar.php' );
			$archive = new Archive_Tar( $archivename );
			$archive->setErrorHandling( PEAR_ERROR_PRINT );

			if (!$archive->extractModify( $extractdir, '' )) {
				$this->setError( 1, 'Extract Error' );
				return false;
			}
		}

		$this->installDir( $extractdir );

		// Try to find the correct install dir. in case that the package have subdirs
		// Save the install dir for later cleanup
		$filesindir = cmsReadDirectory( $this->installDir(), '' );

		if (count( $filesindir ) == 1) {
			if (is_dir( $extractdir . $filesindir[0] )) {
				$this->installDir( cmsPathName( $extractdir . $filesindir[0] ) );
			}
		}
		return true;
	}
	/**
	* Tries to find the package XML file
	* @return boolean True on success, False on error
	*/
	function findInstallFile() {
		$found = false;
		// Search the install dir for an xml file
		$files = cmsReadDirectory( $this->installDir(), '.xml$', true, true );

		if (count( $files ) > 0) {
			foreach ($files as $file) {
				$packagefile = $this->isPackageFile( $file );
				if (!is_null( $packagefile ) && !$found ) {
					$this->xmlDoc( $packagefile );
					return true;
				}
			}
			$this->setError( 1, 'ERROR: Could not find a Joomla! XML setup file in the package.' );
			return false;
		} else {
			$this->setError( 1, 'ERROR: Could not find an XML setup file in the package.' );
			return false;
		}
	}
	/**
	* @param string A file path
	* @return object A DOMIT XML document, or null if the file failed to parse
	*/
	function isPackageFile( $p_file ) {
		$xmlDoc = new DOMIT_Lite_Document();
		$xmlDoc->resolveErrors( true );

		if (!$xmlDoc->loadXML( $p_file, false, true )) {
			return null;
		}
		$root = &$xmlDoc->documentElement;

		if ($root->getTagName() != 'cmsinstall') {
			return null;
		}
		// Set the type
		$this->installType( $root->getAttribute( 'type' ) );
		$this->installFilename( $p_file );
		return $xmlDoc;
	}
	/**
	* Loads and parses the XML setup file
	* @return boolean True on success, False on error
	*/
	function readInstallFile() {

		if ($this->installFilename() == "") {
			$this->setError( 1, 'No filename specified' );
			return false;
		}

		$this->i_xmldoc = new DOMIT_Lite_Document();
		$this->i_xmldoc->resolveErrors( true );
		if (!$this->i_xmldoc->loadXML( $this->installFilename(), false, true )) {
			return false;
		}
		$root = &$this->i_xmldoc->documentElement;

		// Check that it's am installation file
		if ($root->getTagName() != 'cmsinstall') {
			$this->setError( 1, 'File :"' . $this->installFilename() . '" is not a valid Joomla! installation file' );
			return false;
		}

		$this->installType( $root->getAttribute( 'type' ) );
		return true;
	}
	/**
	* Abstract install method
	*/
	function install() {
		die( 'Method "install" cannot be called by class ' . strtolower(get_class( $this )) );
	}
	/**
	* Abstract uninstall method
	*/
	function uninstall() {
		die( 'Method "uninstall" cannot be called by class ' . strtolower(get_class( $this )) );
	}
	/**
	* return to method
	*/
	function returnTo( $option, $element ) {
		return "setup.php?option=$option&element=$element";
	}
	/**
	* @param string Install from directory
	* @param string The install type
	* @return boolean
	*/
	function preInstallCheck( $p_fromdir, $type ) {

		if (!is_null($p_fromdir)) {
			$this->installDir($p_fromdir);
		}

		if (!$this->installfile()) {
			$this->findInstallFile();
		}

		if (!$this->readInstallFile()) {
			$this->setError( 1, 'Installation file not found:<br />' . $this->installDir() );
			return false;
		}

		if ($this->installType() != $type) {
			$this->setError( 1, 'XML setup file is not for a "'.$type.'".' );
			return false;
		}

		// In case there where an error doring reading or extracting the archive
		if ($this->errno()) {
			return false;
		}

		return true;
	}
	
	//=== parseFolder
	function parseFolders( $tagName='folder', $admin=0 ) {
		global $rootDir;
		// Find files to copy
		$xmlDoc =& $this->xmlDoc();
		$root =& $xmlDoc->documentElement;
		
		$folders_element =& $root->getElementsByPath( $tagName, 1 );
		
		if (is_null( $folders_element )) {
			return 0;
		}
		
		if (!$folders_element->hasChildNodes()) {
			// no files
			return 0;
		}
		
		$folders = $folders_element->childNodes;
		$copyfiles = array();
		if (count($folders) == 0) {
			// nothing more to do
			return 0;
		}
		
		
		foreach ($folders as $folder) {
			$folder_name = $folder->getText();
			
			if ($admin){
				$installFrom = substr(cmsPathName($this->installDir()."/".$folder_name),0,-1);
				$installTo = substr(cmsPathName($this->AdminDir()."/".$folder_name),0,-1);
				cmsMakePath( cmsPathName($this->AdminDir()."/".$folder_name) );
			}else{
				$installFrom = substr(cmsPathName($this->installDir()."/".$folder_name),0,-1);
				$installTo = substr(cmsPathName($this->elementDir()."/".$folder_name),0,-1);
				cmsMakePath( cmsPathName($this->elementDir()."/".$folder_name) );
			}
				$ok = $this->parseDir($installFrom,$installTo);
	  }		

	
	}	
	
	function parseDir($sourcedir,$destdir) {
	
	  $copyfiles = array();
		if ($handle = opendir("$sourcedir")) {
	   while (false !== ($item = readdir($handle))) {
			 if ($item != "." && $item != "..") {
				 if (is_dir("$sourcedir/$item")) {
					 cmsMakePath(  cmsPathName($destdir."/"), $item );
					 $sourcedir_new = cmsPathName($sourcedir."/".$item);
					 $destdir_new =   cmsPathName($destdir."/".$item);
					 
					 $this->parseDir($sourcedir_new,$destdir_new);
				 } else {
						$filesource	= cmsPathName($sourcedir."/".$item,false);
						$filedest	= cmsPathName($destdir."/".$item,false);
						if( !( copy($filesource,$filedest) ) ) {
								$this->setError( 1, "Failed to copy file: $filesource to $filedest" ); 
								return false;
						}
				 }
			 }
	   }
	   closedir($handle);
	  }
		return  true ;
	}
	
	
	/**
	* @param string The tag name to parse
	* @param string An attribute to search for in a filename element
	* @param string The value of the 'special' element if found
	* @param boolean True for admin modules
	* @return mixed Number of file or False on error
	*/
	function parseFiles( $tagName='files', $special='', $specialError='', $adminFiles=0 ) {
		global $rootDir;
		// Find files to copy
		$xmlDoc =& $this->xmlDoc();
		$root =& $xmlDoc->documentElement;

		$files_element =& $root->getElementsByPath( $tagName, 1 );
		if (is_null( $files_element )) {
			return 0;
		}

		if (!$files_element->hasChildNodes()) {
			// no files
			return 0;
		}
		$files = $files_element->childNodes;
		$copyfiles = array();
		if (count( $files ) == 0) {
			// nothing more to do
			return 0;
		}

		if ($folder = $files_element->getAttribute( 'folder' )) {
			$temp = cmsPathName( $this->unpackDir() . $folder );
			if ($temp == $this->installDir()) {
				// this must be only an admin module
				$installFrom = $this->installDir();
			} else {
				$installFrom = cmsPathName( $this->installDir() . $folder );
			}
		} else {
			$installFrom = $this->installDir();
		}

		foreach ($files as $file) {
			if (basename( $file->getText() ) != $file->getText()) {
				$newdir = dirname( $file->getText() );
				
				if ($adminFiles){
					if (!cmsMakePath( $this->AdminDir(), $newdir )) {
						$this->setError( 1, 'Failed to create directory "' . ($this->AdminDir()) . $newdir . '"' );
						return false;
					}
				} else {
					if (!cmsMakePath( $this->elementDir(), $newdir )) {
						$this->setError( 1, 'Failed to create directory "' . ($this->elementDir()) . $newdir . '"' );
						return false;
					}
				}
			}
			$copyfiles[] = $file->getText();

			// check special for attribute
			if ($file->getAttribute( $special )) {
				$this->elementSpecial( $file->getAttribute( $special ) );
			}
		}

		if ($specialError) {
			if ($this->elementSpecial() == '') {
				$this->setError( 1, $specialError );
				return false;
			}
		}

		if ($tagName == 'media') {
			// media is a special tag
			$installTo = cmsPathName( $rootDir . '/images/stories' );
		} else if ($adminFiles) {
			$installTo = $this->AdminDir();
		} else {
			$installTo = $this->elementDir();
		}
		$result = $this->copyFiles( $installFrom, $installTo, $copyfiles );

		return $result;
	}
	/**
	* @param string Source directory
	* @param string Destination directory
	* @param array array with filenames
	* @param boolean True is existing files can be replaced
	* @return boolean True on success, False on error
	*/
	function copyFiles( $p_sourcedir, $p_destdir, $p_files, $overwrite=false ) {
		if (is_array( $p_files ) && count( $p_files ) > 0) {
			foreach($p_files as $_file) {
				$filesource	= cmsPathName( cmsPathName( $p_sourcedir ) . $_file, false );
				$filedest	= cmsPathName( cmsPathName( $p_destdir ) . $_file, false );
				if (!file_exists( $filesource )) {
					$this->setError( 1, "File $filesource does not exist!" );
					return false;
				} else if (file_exists( $filedest ) && !$overwrite) {
					$this->setError( 1, "There is already a file called $filedest - Are you trying to install the same CMT twice?" );
					return false;
				} else {
					$path_info = pathinfo($_file);
					if (!is_dir( $path_info['dirname'] )){
									cmsMakePath( $p_destdir, $path_info['dirname'] );
					}
//echo "<br>filesource=$filesource filedest=$filedest";
					if( !( copy($filesource,$filedest) ) ) {
						$this->setError( 1, "Failed to copy file: $filesource to $filedest" );
						return false;
					}
				}
			}
		} else {
			return false;
		}
		return count( $p_files );
	}
	/**
	* Copies the XML setup file to the element Admin directory
	* Used by Components/Modules/Mambot Installer Installer
	* @return boolean True on success, False on error
	*/
	function copySetupFile( $where='admin' ) {
		if ($where == 'admin') {
			return $this->copyFiles( $this->installDir(), $this->AdminDir(), array( basename( $this->installFilename() ) ), true );
		} else if ($where == 'front') {
			return $this->copyFiles( $this->installDir(), $this->elementDir(), array( basename( $this->installFilename() ) ), true );
		}
	}

	/**
	* @param int The error number
	* @param string The error message
	*/
	function setError( $p_errno, $p_error ) {
		$this->errno( $p_errno );
		$this->error( $p_error );
	}
	/**
	* @param boolean True to display both number and message
	* @param string The error message
	* @return string
	*/
	function getError($p_full = false) {
		if ($p_full) {
			return $this->errno() . " " . $this->error();
		} else {
			return $this->error();
		}
	}
	/**
	* @param string The name of the property to set/get
	* @param mixed The value of the property to set
	* @return The value of the property
	*/
	function &setVar( $name, $value=null ) {
		if (!is_null( $value )) {
			$this->$name = $value;
		}
		return $this->$name;
	}

	function installFilename( $p_filename = null ) {
		if(!is_null($p_filename)) {
			if($this->isWindows()) {
				$this->i_installfilename = str_replace('/','\\',$p_filename);
			} else {
				$this->i_installfilename = str_replace('\\','/',$p_filename);
			}
		}
		return $this->i_installfilename;
	}

	function installType( $p_installtype = null ) {
		return $this->setVar( 'i_installtype', $p_installtype );
	}

	function error( $p_error = null ) {
		return $this->setVar( 'i_error', $p_error );
	}

	function &xmlDoc( $p_xmldoc = null ) {
		return $this->setVar( 'i_xmldoc', $p_xmldoc );
	}

	function installArchive( $p_filename = null ) {
		return $this->setVar( 'i_installarchive', $p_filename );
	}

	function installDir( $p_dirname = null ) {
		return $this->setVar( 'i_installdir', $p_dirname );
	}

	function unpackDir( $p_dirname = null ) {
		return $this->setVar( 'i_unpackdir', $p_dirname );
	}

	function isWindows() {
		return $this->i_iswin;
	}

	function errno( $p_errno = null ) {
		return $this->setVar( 'i_errno', $p_errno );
	}

	function hasInstallfile( $p_hasinstallfile = null ) {
		return $this->setVar( 'i_hasinstallfile', $p_hasinstallfile );
	}

	function installfile( $p_installfile = null ) {
		return $this->setVar( 'i_installfile', $p_installfile );
	}

	function elementDir( $p_dirname = null )	{
		return $this->setVar( 'i_elementdir', $p_dirname );
	}

	function elementName( $p_name = null )	{
		return $this->setVar( 'i_elementname', $p_name );
	}
	function elementSpecial( $p_name = null )	{
		return $this->setVar( 'i_elementspecial', $p_name );
	}
}

function cleanupInstall( $userfile_name, $resultdir) {
	global $rootDir;

	if (file_exists( $resultdir )) {
		deldir( $resultdir );
		unlink( cmsPathName( $rootDir . '/install/media/' . $userfile_name, false ) );
	}
}

function deldir( $dir ) {
	$current_dir = opendir( $dir );
	$old_umask = umask(0);
	while ($entryname = readdir( $current_dir )) {
		if ($entryname != '.' and $entryname != '..') {
			if (is_dir( $dir . $entryname )) {
				deldir( cmsPathName( $dir . $entryname ) );
			} else {
                @chmod($dir . $entryname, 0777);
				unlink( $dir . $entryname );
			}
		}
	}
	umask($old_umask);
	closedir( $current_dir );
	return rmdir( $dir );
}
?>
