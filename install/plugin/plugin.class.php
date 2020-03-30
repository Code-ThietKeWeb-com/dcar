<?php

class Installerplugin extends cmsInstaller {
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

		if (!$this->preInstallCheck( $p_fromdir, 'plugin' )) {
			return false;
		}

		// aje moved down to here. ??  seemed to be some referencing problems
		$xmlDoc 	= $this->xmlDoc();
		$cmsinstall = &$xmlDoc->documentElement;

		// Set some vars
		$e = &$cmsinstall->getElementsByPath('name', 1);
		$this->elementName($e->getText());
		$this->elementDir( cmsPathName( $rootDir . "/plugins/"
			. strtolower( str_replace(" ","",$this->elementName())) . "/" )
		);
		
		$plugin_name				= strtolower(str_replace(" ","",$this->elementName()));
		
		if (file_exists($this->elementDir())) {
			$this->setError( 1, 'Another plugin is already using directory: "' . $this->elementDir() . '"' );
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
		
		//folder
		$this->parseFolders( 'folder' );
		
		
		$title_element = &$cmsinstall->getElementsByPath('title', 1);
		$plugin_title		= $title_element->getText();
		$params_element	= &$cmsinstall->getElementsByPath('params',1);
		if(!is_null($params_element))
		{
			$param_element = $params_element->childNodes;
			foreach($param_element as $param_item)
			{
				$key = $param_item->getAttribute("name");
				$params[$key] = $param_item->getText();
			}
		}
		$plugin_id = $this->createDataplugin($plugin_name,$plugin_title,$params);
		

		$ret = "plugin ".$plugin_name." Da duoc cai dat thanh cong";
		
		//return $this->copySetupFile();
		//copy file xml
		if(!$this->copyFiles($this->installDir(),$this->elementDir(),array( basename( $this->installFilename() ) ), true ) ) 			
		{
				$this->setError( 1, 'Khong the copy file XML setup.' );
				return false;
		}
		
		return true;
	}
	
	//insert vao bang plugins
	
	function createDataplugin($name,$title,$params=array()) {
		global $DB;
		$cot['name'] = $name;
		$cot['title'] = $title;
		$cot['folder'] = $name;
		$cot['params'] = serialize($params);
		$ok = $DB->do_insert("plugins",$cot);

		if(!$ok)
		{
			$this->setError( 1, $DB->debug() );
			return false;
		}
		return $DB->insertid();
	}
	
	/**
	* Custom install method
	* @param int The id of the plugin
	* @param string The URL option
	* @param int The client id
	*/
	function uninstall( $cid, $option, $client=0 ) {
		global $database,$rootDir,$DB;

		$uninstallret = '';
		$res_ck = $DB->query("select * from  plugins where id=".(int) $cid." ");
		if (!$row = $DB->fetch_row($res_ck)){
				HTML_installer::showInstallMessage($DB->debug(),'Uninstall -  error',
				$this->returnTo( $option, 'plugin', $client ) );
			exit();
		}
		
		$sql = "DELETE FROM plugins WHERE id = " . (int) $row['id']	;
		$okDel=$DB->query($sql);

	
		// Delete directories

		if (trim( $row['name'] )) {
			$result = 0;
			$path = cmsPathName( $rootDir.'/plugins/'.$row['name'] );
			if (is_dir( $path )) {
				$result |= deldir( $path );
			}
			return $result;
		} else {
			HTML_installer::showInstallMessage( 'Option field empty, cannot remove files', 'Uninstall -  error', $option,'plugin');
			exit();
		}

		return $uninstallret;
	}
}
?>
