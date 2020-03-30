<?php

if (!defined('IN_vnT')){
     die('Hacking attempt!');
}

/**
 * A Folder handling class
 *
 * @static
 * @subpackage	FileSystem
 * @since		1.5
 */
class vnT_Folder
{
	/**
	 * Copies a folder
	 *
	 * @param	string	$src	The path to the source folder
	 * @param	string	$dest	The path to the destination folder
	 * @param	string	$path	An optional base path to prefix to the file names
	 * @param	boolean	$force	Optionally force folder/file overwrites
	 * @return	mixed	JError object on failure or boolean True on success
	 * @since	1.5
	 */
	function copy($src, $dest, $path = '', $force = false)
	{
		global $vnT,$conf,$func;
		// Initialize variables
		if ($path) {
			$src = vnT_Path::clean($path.DS.$src);
			$dest = vnT_Path::clean($path.DS.$dest);
		}

		// Eliminate trailing directory separators, if any
		$src = rtrim($src, DS);
		$dest = rtrim($dest, DS);

		if (!vnT_Folder::exists($src)) {
			return $vnT->func->html_err('Cannot find source folder');
		}
		if (vnT_Folder::exists($dest) && !$force) {
			return $vnT->func->html_err('Folder already exists');
		}

		// Make sure the destination exists
		if (! vnT_Folder::create($dest)) {
			return $vnT->func->html_err('Unable to create target folder');
		}

		if ($FTPOptions['enabled'] == 1)
		{
			// Connect the FTP client
			$vnT->func->include_libraries('vntrust.client.ftp');
			$ftp = & vnT_FTP::getInstance($FTPOptions['host'], $FTPOptions['port'], null, $FTPOptions['user'], $FTPOptions['pass']);

			if(! ($dh = @opendir($src))) {
				return $vnT->func->html_err('Unable to open source folder');
			}
			// Walk through the directory copying files and recursing into folders.
			while (($file = readdir($dh)) !== false) {
				$sfid = $src . DS . $file;
				$dfid = $dest . DS . $file;
				switch (filetype($sfid)) {
					case 'dir':
						if ($file != '.' && $file != '..') {
							$ret = vnT_Folder::copy($sfid, $dfid, null, $force);
							if ($ret !== true) {
								return $ret;
							}
						}
						break;

					case 'file':
						//Translate path for the FTP account
						$dfid = vnT_Path::clean(str_replace(vnT_PATH_ROOT, $FTPOptions['root'], $dfid), '/');
						if (! $ftp->store($sfid, $dfid)) {
							return $vnT->func->html_err('Copy failed');
						}
						break;
				}
			}
		} else {
			if(! ($dh = @opendir($src))) {
				return $vnT->func->html_err('Unable to open source folder'); 
			}
			// Walk through the directory copying files and recursing into folders.
			while (($file = readdir($dh)) !== false) {
				$sfid = $src.DS.$file;
				$dfid = $dest.DS.$file;
				switch (filetype($sfid)) {
					case 'dir':
						if ($file != '.' && $file != '..') {
							$ret = vnT_Folder::copy($sfid, $dfid, null, $force);
							if ($ret !== true) {
								return $ret;
							}
						}
						break;

					case 'file':
						if (!@ copy($sfid, $dfid)) {
							return $vnT->func->html_err('Copy failed');
						}
						break;
				}
			}
		}
		return true;
	}

	/**
	 * Create a folder -- and all necessary parent folders
	 *
	 * @param string $path A path to create from the base path
	 * @param int $mode Directory permissions to set for folders created
	 * @return boolean True if successful
	 * @since 1.5
	 */
	function create($path = '', $mode = 0755)
	{
		global $vnT,$conf,$func;
		// Initialize variables
		$vnT->func->include_libraries('vntrust.myftp.config');

		static $nested = 0;

		// Check to make sure the path valid and clean
		$path = vnT_Path::clean($path);

		// Check if parent dir exists
		$parent = dirname($path);
		if (!vnT_Folder::exists($parent)) {
			// Prevent infinite loops!
			$nested++;
			if (($nested > 20) || ($parent == $path)) {
				$vnT->error[] = 'Infinite loop detected';
				$nested--;
				return false;
			}

			// Create the parent directory
			if (vnT_Folder::create($parent, $mode) !== true) {
				// vnT_Folder::create throws an error
				$nested--;
				return false;
			}

			// OK, parent directory has been created
			$nested--;
		}

		// Check if dir already exists
		if (vnT_Folder::exists($path)) {
			return true;
		}

		// Check for safe mode
		if ($FTPOptions['enabled'] == 1) {
			// Connect the FTP client
			$vnT->func->include_libraries('vntrust.myftp.ftp');
			$ftp = & vnT_FTP::getInstance($FTPOptions['host'], $FTPOptions['port'], null, $FTPOptions['user'], $FTPOptions['pass']);

			// Translate path to FTP path
			$path = vnT_Path::clean(str_replace(vnT_PATH_ROOT, $FTPOptions['root'], $path), '/');
			$ret = $ftp->mkdir($path);
			$ftp->chmod($path, $mode);
		}
		else
		{
			// We need to get and explode the open_basedir paths
			$obd = ini_get('open_basedir');

			// If open_basedir is set we need to get the open_basedir that the path is in
			if ($obd != null)
			{
				if (vnT_Path_ISWIN) {
					$obdSeparator = ";";
				} else {
					$obdSeparator = ":";
				}
				// Create the array of open_basedir paths
				$obdArray = explode($obdSeparator, $obd);
				$inOBD = false;
				// Iterate through open_basedir paths looking for a match
				foreach ($obdArray as $test) {
					$test = vnT_Path::clean($test);
					if (strpos($path, $test) === 0) {
						$obdpath = $test;
						$inOBD = true;
						break;
					}
				}
				if ($inOBD == false) {
					// Return false for vnT_Folder::create because the path to be created is not in open_basedir
					$vnT->error[] = 'Path not in open_basedir paths ';
					return false;
				}
			}

			// First set umask
			$origmask = @ umask(0);

			// Create the path
			if (!$ret = @mkdir($path, $mode)) {
				@ umask($origmask);
				$vnT->error[] = 'Could not create directory Path: '.$path;

				return false;
			}

			// Reset umask
			@ umask($origmask);
		}
		return $ret;
	}

	/**
	 * Delete a folder
	 *
	 * @param string $path The path to the folder to delete
	 * @return boolean True on success
	 * @since 1.5
	 */
	function delete($path)
	{
		global $vnT,$conf,$func;
		// Sanity check
		if ( ! $path ) {
			// Bad programmer! Bad Bad programmer!
			$vnT->error[] = 'vnT_Folder::delete: Attempt to delete base directory ';
			return false;
		}

		// Initialize variables
		$vnT->func->include_libraries('vntrust.myftp.config');

		// Check to make sure the path valid and clean
		$path = vnT_Path::clean($path);

		// Is this really a folder?
		if (!is_dir($path)) {
			$vnT->error[] = 'vnT_Folder::delete: Path is not a folder '.$path;
			return false;
		}

		// Remove all the files in folder if they exist
		$files = vnT_Folder::files($path, '.', false, true, array());
		if (count($files)) {
			$vnT->func->include_libraries('vntrust.filesystem.file');
			if (vnT_File::delete($files) !== true) {
				// vnT_File::delete throws an error
				return false;
			}
		}

		// Remove sub-folders of folder
		$folders = vnT_Folder::folders($path, '.', false, true, array());
		foreach ($folders as $folder) {
			if (vnT_Folder::delete($folder) !== true) {
				// vnT_Folder::delete throws an error
				return false;
			}
		}

		if ($FTPOptions['enabled'] == 1) {
			// Connect the FTP client
			$vnT->func->include_libraries('vntrust.myftp.ftp');
			$ftp = & vnT_FTP::getInstance($FTPOptions['host'], $FTPOptions['port'], null, $FTPOptions['user'], $FTPOptions['pass']);
		}

		// In case of restricted permissions we zap it one way or the other
		// as long as the owner is either the webserver or the ftp
		if (@rmdir($path)) {
			$ret = true;
		} elseif ($FTPOptions['enabled'] == 1) {
			// Translate path and delete
			$path = vnT_Path::clean(str_replace(vnT_PATH_ROOT, $FTPOptions['root'], $path), '/');
			// FTP connector throws an error
			$ret = $ftp->delete($path);
		} else {
			$vnT->error[] = 'vnT_Folder::delete:  Could not delete folder'.$path;
			$ret = false;
		}

		return $ret;
	}

	/**
	 * Moves a folder
	 *
	 * @param string $src The path to the source folder
	 * @param string $dest The path to the destination folder
	 * @param string $path An optional base path to prefix to the file names
	 * @return mixed Error message on false or boolean True on success
	 * @since 1.5
	 */
	function move($src, $dest, $path = '')
	{
		global $vnT,$conf,$func;
		// Initialize variables
		$vnT->func->include_libraries('vntrust.myftp.config');

		if ($path) {
			$src = vnT_Path::clean($path.DS.$src);
			$dest = vnT_Path::clean($path.DS.$dest);
		}

		if (!vnT_Folder::exists($src) && !is_writable($src)) {
			return $vnT->func->html_err('Cannot find source folder');
		}
		if (vnT_Folder::exists($dest)) {
			return $vnT->func->html_err('Folder already exists');
		}

		if ($FTPOptions['enabled'] == 1) {
			// Connect the FTP client
			$vnT->func->include_libraries('vntrust.myftp.ftp');
			$ftp = & vnT_FTP::getInstance($FTPOptions['host'], $FTPOptions['port'], null, $FTPOptions['user'], $FTPOptions['pass']);

			//Translate path for the FTP account
			$src = vnT_Path::clean(str_replace(vnT_PATH_ROOT, $FTPOptions['root'], $src), '/');
			$dest = vnT_Path::clean(str_replace(vnT_PATH_ROOT, $FTPOptions['root'], $dest), '/');

			// Use FTP rename to simulate move
			if (!$ftp->rename($src, $dest)) {
				return $vnT->func->html_err('Rename failed'); 
			}
			$ret = true;
		} else {
			if (!@ rename($src, $dest)) {
				return $vnT->func->html_err('Rename failed'); 
			}
			$ret = true;
		}
		return $ret;
	}

	/**
	 * Wrapper for the standard file_exists function
	 *
	 * @param string $path Folder name relative to installation dir
	 * @return boolean True if path is a folder
	 * @since 1.5
	 */
	function exists($path)
	{
		return is_dir(vnT_Path::clean($path));
	}

	/**
	 * Utility function to read the files in a folder
	 *
	 * @param	string	$path		The path of the folder to read
	 * @param	string	$filter		A filter for file names
	 * @param	mixed	$recurse	True to recursively search into sub-folders, or an integer to specify the maximum depth
	 * @param	boolean	$fullpath	True to return the full path to the file
	 * @param	array	$exclude	Array with names of files which should not be shown in the result
	 * @return	array	Files in the given folder
	 * @since 1.5
	 */
	function files($path, $filter = '.', $recurse = false, $fullpath = false, $exclude = array('.svn', 'CVS'))
	{
		global $vnT,$conf,$func;
		// Initialize variables
		$arr = array ();

		// Check to make sure the path valid and clean
		$path = vnT_Path::clean($path);

		// Is the path a folder?
		if (!is_dir($path)) {
			$vnT->error[] = 'vnT_Folder::files: Path is not a folder'.$path;
			return false;
		}

		// read the source directory
		$handle = opendir($path);
		while (($file = readdir($handle)) !== false)
		{
			$dir = $path.DS.$file;
			$isDir = is_dir($dir);
			if (($file != '.') && ($file != '..') && (!in_array($file, $exclude))) {
				if ($isDir) {
					if ($recurse) {
						if (is_integer($recurse)) {
							$recurse--;
						}
						$arr2 = vnT_Folder::files($dir, $filter, $recurse, $fullpath);
						$arr = array_merge($arr, $arr2);
					}
				} else {
					if (preg_match("/$filter/", $file)) {
						if ($fullpath) {
							$arr[] = $path.DS.$file;
						} else {
							$arr[] = $file;
						}
					}
				}
			}
		}
		closedir($handle);

		asort($arr);
		return $arr;
	}

	/**
	 * Utility function to read the folders in a folder
	 *
	 * @param	string	$path		The path of the folder to read
	 * @param	string	$filter		A filter for folder names
	 * @param	mixed	$recurse	True to recursively search into sub-folders, or an integer to specify the maximum depth
	 * @param	boolean	$fullpath	True to return the full path to the folders
	 * @param	array	$exclude	Array with names of folders which should not be shown in the result
	 * @return	array	Folders in the given folder
	 * @since 1.5
	 */
	function folders($path, $filter = '.', $recurse = false, $fullpath = false, $exclude = array('.svn', 'CVS'))
	{
		global $vnT,$conf,$func;
		
		// Initialize variables
		$arr = array ();

		// Check to make sure the path valid and clean
		$path = vnT_Path::clean($path);

		// Is the path a folder?
		if (!is_dir($path)) {
			$vnT->error[] = 'vnT_Folder::folder: Path is not a folder '.$path; 
			return false;
		}

		// read the source directory
		$handle = opendir($path);
		while (($file = readdir($handle)) !== false)
		{
			$dir = $path.DS.$file;
			$isDir = is_dir($dir);
			if (($file != '.') && ($file != '..') && (!in_array($file, $exclude)) && $isDir) {
				// removes SVN directores from list
				if (preg_match("/$filter/", $file)) {
					if ($fullpath) {
						$arr[] = $dir;
					} else {
						$arr[] = $file;
					}
				}
				if ($recurse) {
					if (is_integer($recurse)) {
						$recurse--;
					}
					$arr2 = vnT_Folder::folders($dir, $filter, $recurse, $fullpath);
					$arr = array_merge($arr, $arr2);
				}
			}
		}
		closedir($handle);

		asort($arr);
		return $arr;
	}

	/**
	 * Lists folder in format suitable for tree display
	 *
	 * @access	public
	 * @param	string	$path		The path of the folder to read
	 * @param	string	$filter		A filter for folder names
	 * @param	integer	$maxLevel	The maximum number of levels to recursively read, default 3
	 * @param	integer	$level		The current level, optional
	 * @param	integer	$parent
	 * @return	array	Folders in the given folder
	 * @since	1.5
	 */
	function listFolderTree($path, $filter, $maxLevel = 3, $level = 0, $parent = 0)
	{
		global $vnT,$conf,$func;
		
		$dirs = array ();
		if ($level == 0) {
			$GLOBALS['vnT_Folder_folder_tree_index'] = 0;
		}
		if ($level < $maxLevel) {
			$folders = vnT_Folder::folders($path, $filter);
			// first path, index foldernames
			for ($i = 0, $n = count($folders); $i < $n; $i ++) {
				$id = ++ $GLOBALS['vnT_Folder_folder_tree_index'];
				$name = $folders[$i];
				$fullName = vnT_Path::clean($path.DS.$name);
				$dirs[] = array ('id' => $id, 'parent' => $parent, 'name' => $name, 'fullname' => $fullName, 'relname' => str_replace(vnT_PATH_ROOT, '', $fullName));
				$dirs2 = vnT_Folder::listFolderTree($fullName, $filter, $maxLevel, $level +1, $id);
				$dirs = array_merge($dirs, $dirs2);
			}
		}
		return $dirs;
	}

	/**
	 * Makes path name safe to use
	 *
	 * @access	public
	 * @param	string $path The full path to sanitise
	 * @return	string The sanitised string
	 * @since	1.5
	 */
	function makeSafe($path)
	{
		$ds		= ( DS == '\\' ) ? '\\'.DS : DS;
		$regex = array('#[^A-Za-z0-9:\_\-'.$ds.' ]#');
		return preg_replace($regex, '', $path);
	}
}