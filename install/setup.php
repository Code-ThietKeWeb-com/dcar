<?php

/*---------------------- config ---------------------*/
require_once(  '../_config.php' );
$rootDir = substr($conf['rootpath'],0,-1);
$rooturl = substr($conf['rooturl'],0,-1);
define('#__', "");
define('__FILE__', $rootDir."/install/domit");
require_once( $rootDir . '/includes/class_db.php' );
$DB = new DB;
require_once( $rootDir . '/install/includes/database.php' );
$database = new database( $conf['host'], $conf['dbuser'], $conf['dbpass'], $conf['dbname'], "" );
require_once( $rootDir . '/install/includes/functions.php' );
require_once( $rootDir . '/install/installer.class.php' );


// XML library
require_once( $rootDir . '/install/domit/xml_domit_lite_include.php' );
require_once( $rootDir . '/install/skin.php' );

/*----------------------end config ---------------------*/
echo HTML_installer::header();
$element=$_GET['element'];
$option=$_GET['option'];
$task=$_GET['task'];
$client=$_GET['client'];
if (isset($_POST['element'])) $element = $_POST['element'];
if (isset($_POST['option'])) $option = $_POST['option'];
if (isset($_POST['task'])) $task = $_POST['task'];
if (isset($_POST['client'])) $client = $_POST['client'];
if($element || $option){
	// map the element to the required derived class
	$classMap = array(
		'module' => 'InstallerModule',
		'block' 	=> 'InstallerBlock',
		'plugin' 	=> 'Installerplugin',
	);
	
	switch ($task) {

		case 'uploadfile':
			require_once( $rootDir . "/install/$element/$element.class.php" );
			uploadPackage( $classMap[$element], $option, $element, $client );
			break;

		case 'remove':
			require_once( $rootDir . "/install/$element/$element.class.php" );
			removeElement( $classMap[$element], $option, $element, $client );
			break;

		default:
			$path = $rootDir . "/install/$element/$element.php";

			if (file_exists( $path )) {
				require $path;
			} else {
				echo "Installer not found for element [$element]";
			}
			break;
	}
}

echo HTML_installer::footer();

/**
* @param string The class name for the installer
* @param string The URL option
* @param string The element name
*/
function uploadPackage( $installerClass, $option, $element, $client ) {
	$installer = new $installerClass();

	// Check if file uploads are enabled
	if (!(bool)ini_get('file_uploads')) {
		HTML_installer::showInstallMessage( "The installer can't continue before file uploads are enabled. Please use the install from directory method.",
			'Installer - Error', $installer->returnTo( $option, $element, $client ) );
		exit();
	}

	// Check that the zlib is available
	if(!extension_loaded('zlib')) {
		HTML_installer::showInstallMessage( "The installer can't continue before zlib is installed",
			'Installer - Error', $installer->returnTo( $option, $element, $client ) );
		exit();
	}

	$userfile = $_FILES['userfile'];
	if (!$userfile) {
		HTML_installer::showInstallMessage( 'No file selected', 'Upload new block - error',
			$installer->returnTo( $option, $element, $client ));
		exit();
	}

	$userfile_name = $userfile['name'];

	$msg = '';
	$resultdir = uploadFile( $userfile['tmp_name'], $userfile['name'], $msg );//move file vao tm media

	if ($resultdir !== false) {
		if (!$installer->upload( $userfile['name'] )) {
			HTML_installer::showInstallMessage( $installer->getError(), 'Upload '.$element.' - Upload Failed 100',
				$installer->returnTo( $option, $element, $client ) );
		}
		$ret = $installer->install();

		HTML_installer::showInstallMessage( $installer->getError(), 'Upload '.$element.' - '.($ret ? 'Success' : 'Failed 105'),
			$installer->returnTo( $option, $element, $client ) );
		cleanupInstall( $userfile['name'], $installer->unpackDir() );
	} else {
		HTML_installer::showInstallMessage( $msg, 'Upload '.$element.' -  Upload Error',
			$installer->returnTo( $option, $element, $client ) );
//echo "<br>kong co resultdir ";
	}
}

/**
* Install a template from a directory
* @param string The URL option
*/
function installFromDirectory( $installerClass, $option, $element, $client ) {
	$userfile = $_REQUEST['userfile'];

	if (!$userfile) {
		mosRedirect( "?option=$option&element=block", "Please select a directory" );
	}

	$installer = new $installerClass();

	$path = cmsPathName( $userfile );
	if (!is_dir( $path )) {
		$path = dirname( $path );
	}

	$ret = $installer->install( $path );
	HTML_installer::showInstallMessage( $installer->getError(), 'Upload new '.$element.' - '.($ret ? 'Success' : 'Error'), $installer->returnTo( $option, $element, $client ) );
}
/**
*
* @param
*/
function removeElement( $installerClass, $option, $element, $client ) {
	$cid = $_REQUEST['cid'];
	if (!is_array( $cid )) {
		$cid = array(0);
	}

	$installer 	= new $installerClass();
	$result 	= false;
	if ($cid[0]) {
		$result = $installer->uninstall( $cid[0], $option, $client );
	}

	//$msg = $installer->getError();
	HTML_installer::showInstallMessage( $installer->getError(), 'Remove  '.$element.' - '.($result ? 'Success' : 'Error'), $installer->returnTo( $option, $element, $client ) );
	
/*	$mess = 'Remove success';

	$url = '?element='.$element;
	flush();
		echo html_redirect($url,$mess);
	exit();
*/	
}
/**
* @param string The name of the php (temporary) uploaded file
* @param string The name of the file to put in the temp directory
* @param string The message to return
*/
function uploadFile( $filename, $userfile_name, &$msg ) {
	global $rootDir;
	$baseDir = cmsPathName( $rootDir . '/install/media' );

	if (file_exists( $baseDir )) {
		if (is_writable( $baseDir )) {
			if (move_uploaded_file( $filename, $baseDir . $userfile_name )) {
				/*if (mosChmod( $baseDir . $userfile_name )) {
					return true;
				} else {
					$msg = 'Failed to change the permissions of the uploaded file.';
				}
				*/
				return true;
			} else {
				$msg = 'Failed to move uploaded file to <code>/media</code> directory.';
			}
		} else {
			$msg = 'Upload failed as <code>/media</code> directory is not writable.';
		}
	} else {
		$msg = 'Upload failed as <code>/media</code> directory does not exist.';
	}
	return false;
}


/*--------------- html_redirect  -----------*/
function html_redirect($url,$mess){
global $conf,$vnT;
$mess_redirect = str_replace("<url>",$url,$vnT->lang['mess_redirect']);
$host_name = $_SERVER['HTTP_HOST'];
return<<<EOF
<html>
<head>
<title>{$mess}</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta http-equiv='refresh' content='1; url={$url}' />
<link href="../style/style.css" rel="stylesheet" type="text/css">
</head>	
<body>
<table width="100%" border="0" cellspacing="2" cellpadding="2" height="100%">
  <tr>
    <td>
	<table width="60%" border="0" align="center" cellpadding="1" cellspacing="1"  class="table_redirect" >
      <tr>
        <td >
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tr height="25">
            <td width="30" height="21" align="left"><img src="../images/thongbao.gif" width="32" height="22" /></td>
            <td class="font_title">&nbsp;<strong>{$vnT->lang['announce']} </strong></td>
          </tr>
        </table>
        </td>
      </tr>
      <tr>
        <td align="center" style="padding:3px 3px 3px 3px" bgcolor="#FEFEF5" >
          <table width="100%" border="0" cellspacing="2" cellpadding="2">
            <tr>
              <td align="center" height="50"><font class="font_err"><strong>{$mess}</strong></font></td>
            </tr>
            <tr>
              <td align="center" height="20"><img src="../images/loading.gif" width="78" height="7" /></td>
            </tr>
            <tr>
              <td align="center" class="font_err">({$mess_redirect})</td>
            </tr>
          </table>
          </td>
      </tr>
      <tr>
        <td align="center" style="padding:3px 3px 3px 3px" class="font_white" ><b>.::[ Copyright &copy; 2007 {$host_name} ]::.</b></td>
      </tr>
    </table>
	</td>
  </tr>
</table>
</body>
</html>
EOF;
}


?>