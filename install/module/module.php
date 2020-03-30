<?php


//require_once( $mainframe->getPath( 'installer_html', 'module' ) );
require_once( $rootDir . "/install/$element/$element.html.php" );

HTML_installer::showInstallForm( 'Cài đặt 1 module mới', $option, 'module', '', dirname(__FILE__) );
/*
?>
<table class="content">
<?php
writableCell( 'media' );
writableCell( 'admin/modules' );
writableCell( 'modules' );
writableCell( 'images/stories' );
?>
</table>
<?php
*/
showInstalledModules( $option );

/**
* @param string The URL option
*/
function showInstalledModules( $option ) {
	global $database, $rootDir;

	$query = "SELECT *"
	. "\n FROM #__modules"
	. "\n ORDER BY name"
	;
	$database->setQuery( $query	);
	$rows = $database->loadObjectList();
	// Read the module dir to find modules
	$moduleBaseDir	= cmsPathName( $rootDir . '/admin/modules' );
	$moduleDirs 		= cmsReadDirectory( $moduleBaseDir );

	$n = count( $rows );
	for ($i = 0; $i < $n; $i++) {
		$row =& $rows[$i];

		$dirName = $moduleBaseDir . $row->mod_name.'_ad';
		$xmlFilesInDir = cmsReadDirectory( $dirName, '.xml$' );

		foreach ($xmlFilesInDir as $xmlfile) {
			// Read the file to see if it's a valid module XML file
			$xmlDoc = new DOMIT_Lite_Document();
			$xmlDoc->resolveErrors( true );

			if (!$xmlDoc->loadXML( $dirName . '/' . $xmlfile, false, true )) {
				continue;
			}

			$root = &$xmlDoc->documentElement;

			if ($root->getTagName() != 'cmsinstall') {
				continue;
			}
			if ($root->getAttribute( "type" ) != "module") {
				continue;
			}

			$element 			= &$root->getElementsByPath('creationDate', 1);
			$row->creationdate 	= $element ? $element->getText() : 'Unknown';

			$element 			= &$root->getElementsByPath('author', 1);
			$row->author 		= $element ? $element->getText() : 'Unknown';

			$element 			= &$root->getElementsByPath('copyright', 1);
			$row->copyright 	= $element ? $element->getText() : '';

			$element 			= &$root->getElementsByPath('authorEmail', 1);
			$row->authorEmail 	= $element ? $element->getText() : '';

			$element 			= &$root->getElementsByPath('authorUrl', 1);
			$row->authorUrl 	= $element ? $element->getText() : '';

			$element 			= &$root->getElementsByPath('version', 1);
			$row->version 		= $element ? $element->getText() : '';

			$row->mosname 		= strtolower( str_replace( " ", "_", $row->name ) );
		}
	}

	HTML_module::showInstalledModules( $rows, $option );
}
?>