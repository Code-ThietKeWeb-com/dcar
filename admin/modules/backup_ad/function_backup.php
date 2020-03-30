<?php
/*================================================================================*\
|| 							Name code : funtions_about.php 		 			      	         			  # ||
||  				Copyright © 2008 by Thai Son - CMS vnTRUST                          # ||
\*================================================================================*/
/**
 * @version : 2.0
 * @date upgrade : 09/01/2009 by Thai Son
 **/
if (! defined('IN_vnT')) {
  die('Hacking attempt!');
}
define('DIR_IMAGE_MEDIA', 'modules/media_ad/images');
define('DIR_BACKUP', '../vnt_upload');
define('IMAGE_MOD', 'modules/backup_ad/images');
$vnT->func->include_libraries('vntrust.base.object');
$vnT->func->include_libraries('vntrust.filesystem.path');
$vnT->func->include_libraries('vntrust.filesystem.folder');
$vnT->func->include_libraries('vntrust.filesystem.file');

function DateCmp ($a, $b)
{
  if ($a[1] == $b[1]) {
    return 0;
  }
  return ($a[1] > $b[1]) ? - 1 : 1;
}

/**
 * Checks if the file is an image
 * @param string The filename
 * @return boolean
 */
function isImage ($fileName)
{
  static $imageTypes = 'xcf|odg|gif|jpg|png|bmp';
  return preg_match("/$imageTypes/i", $fileName);
}

/**
 * Checks if the file is an image
 * @param string The filename
 * @return boolean
 */
function getTypeIcon ($fileName)
{
  // Get file extension
  return strtolower(substr($fileName, strrpos($fileName, '.') + 1));
}

function parseSize ($size)
{
  if ($size < 1024) {
    return $size . ' bytes';
  } else {
    if ($size >= 1024 && $size < 1024 * 1024) {
      return sprintf('%01.2f', $size / 1024.0) . ' Kb';
    } else {
      return sprintf('%01.2f', $size / (1024.0 * 1024)) . ' Mb';
    }
  }
}

function imageResize ($width, $height, $target)
{
  //takes the larger size of the width and height and applies the
  //formula accordingly...this is so this script will work
  //dynamically with any size image
  if ($width > $height) {
    $percentage = ($target / $width);
  } else {
    $percentage = ($target / $height);
  }
  //gets the new value and applies the percentage, then rounds the value
  $width = round($width * $percentage);
  $height = round($height * $percentage);
  return array(
    $width , 
    $height);
}

function countFiles ($dir)
{
  $total_file = 0;
  $total_dir = 0;
  $total_size = 0;
  if (is_dir($dir)) {
    $d = dir($dir);
    while (false !== ($entry = $d->read())) {
      if (substr($entry, 0, 1) != '.' && is_file($dir . DIRECTORY_SEPARATOR . $entry) && strpos($entry, '.html') === false && strpos($entry, '.php') === false) {
        $total_size += filesize($dir . DIRECTORY_SEPARATOR . $entry);
        $total_file ++;
      }
      if (substr($entry, 0, 1) != '.' && is_dir($dir . DIRECTORY_SEPARATOR . $entry)) {
        $total_dir ++;
      }
    }
    $d->close();
  }
  return array(
    $total_file , 
    $total_dir , 
    $total_size);
}

//===================================
function getdir ($path = ".")
{
  global $dirarray, $conf, $dirsize;
  if ($dir = opendir($path)) {
    while (false !== ($entry = @readdir($dir))) {
      if (($entry != ".") && ($entry != "..")) {
        $lastdot = strrpos($entry, ".");
        $ext = chop(strtolower(substr($entry, $lastdot + 1)));
        $fname = substr($entry, 0, $lastdot);
        if ($path != ".")
          $newpath = $path . "/" . $entry;
        else
          $newpath = $entry;
        $newpath = str_replace("//", "/", $newpath);
        if (($entry != "NDKziper.php") && ($entry != "ndkziper.txt") && ($entry != $conf['dir'])) {
          $dirarray[] = $newpath;
          if ($fsize = @filesize($newpath))
            $dirsize += $fsize;
          if (is_dir($newpath))
            getdir($newpath);
        }
      }
    }
  }
} // end func

//---------- get_folder_backup
function get_folder_backup ($dir, $time_start, $time_end)
{
  global $dirarray;
  if (is_dir($dir)) {
    $d = dir($dir);
    while (false !== ($entry = $d->read())) {
      if (substr($entry, 0, 1) != '.' && is_file($dir . "/" . $entry) && ($entry != "Thumbs.db") && strpos($entry, '.html') === false && strpos($entry, '.php') === false) {
        $files = $dir . "/" . $entry;
        if (filemtime($files) > $time_start && filemtime($files) < $time_end) {
          $dirarray[] = $files;
        }
      }
      if (substr($entry, 0, 1) != '.' && is_dir($dir . "/" . $entry)) {
        $newdir = $dir . "/" . $entry;
        get_folder_backup($newdir, $time_start, $time_end);
      }
    }
    $d->close();
  }
  return $dirarray;
}

//---------- getList
function getList ($current)
{
  static $list;
  // Initialize variables
  if (strlen($current) > 0) {
    $basePath = DIR_BACKUP . DS . $current;
  } else {
    $basePath = DIR_BACKUP;
  }
  $mediaBase = str_replace(DS, '/', DIR_BACKUP . '/');
  $images = array();
  $folders = array();
  $docs = array();
  // Get the list of files and folders from the given folder
  $fileList = vnT_Folder::files($basePath);
  $folderList = vnT_Folder::folders($basePath);
  // Iterate over the files if they exist
  if ($fileList !== false) {
    foreach ($fileList as $file) {
      if (is_file($basePath . DS . $file) && substr($file, 0, 1) != '.' && strtolower($file) !== 'index.html' && (strtolower($file) != 'thumbs.db')) {
        $tmp = array();
        $tmp['name'] = $file;
        $tmp['path'] = str_replace(DS, '/', vnT_Path::clean($basePath . DS . $file));
        $tmp['path_relative'] = str_replace($mediaBase, '', $tmp['path']);
        $tmp['size'] = filesize($tmp['path']);
        $ext = strtolower(vnT_File::getExt($file));
        switch ($ext) {
          // Image
          case 'jpg':
          case 'png':
          case 'gif':
          case 'xcf':
          case 'odg':
          case 'bmp':
          case 'jpeg':
            $info = @getimagesize($tmp['path']);
            $tmp['width'] = @$info[0];
            $tmp['height'] = @$info[1];
            $tmp['type'] = @$info[2];
            $tmp['mime'] = @$info['mime'];
            $filesize = parseSize($tmp['size']);
            if (($info[0] > 60) || ($info[1] > 60)) {
              $dimensions = imageResize($info[0], $info[1], 60);
              $tmp['width_60'] = $dimensions[0];
              $tmp['height_60'] = $dimensions[1];
            } else {
              $tmp['width_60'] = $tmp->width;
              $tmp['height_60'] = $tmp->height;
            }
            if (($info[0] > 16) || ($info[1] > 16)) {
              $dimensions = imageResize($info[0], $info[1], 16);
              $tmp['width_16'] = $dimensions[0];
              $tmp['height_16'] = $dimensions[1];
            } else {
              $tmp['width_16'] = $tmp['width'];
              $tmp['height_16'] = $tmp['height'];
            }
            $images[] = $tmp;
          break;
          // Non-image document
          case 'swf':
            $tmp['is_flash'] = 1;
            $tmp['type'] = "swf";
            $iconfile_32 = DIR_IMAGE_MEDIA . "/mime-icon-32/" . $ext . ".png";
            if (file_exists($iconfile_32)) {
              $tmp['icon_32'] = $iconfile_32;
            } else {
              $tmp['icon_32'] = DIR_IMAGE_MEDIA . "/con_info.png";
            }
            $iconfile_16 = DIR_IMAGE_MEDIA . "/mime-icon-16/" . $ext . ".png";
            if (file_exists($iconfile_16)) {
              $tmp['icon_16'] = $iconfile_16;
            } else {
              $tmp['icon_16'] = DIR_IMAGE_MEDIA . "/con_info.png";
            }
            $images[] = $tmp;
          break;
          default:
            $tmp['is_doc'] = 1;
            $iconfile_32 = DIR_IMAGE_MEDIA . "/mime-icon-32/" . $ext . ".png";
            if (file_exists($iconfile_32)) {
              $tmp['icon_32'] = $iconfile_32;
            } else {
              $tmp['icon_32'] = DIR_IMAGE_MEDIA . "/con_info.png";
            }
            $iconfile_16 = DIR_IMAGE_MEDIA . "/mime-icon-16/" . $ext . ".png";
            if (file_exists($iconfile_16)) {
              $tmp['icon_16'] = $iconfile_16;
            } else {
              $tmp['icon_16'] = DIR_IMAGE_MEDIA . "/con_info.png";
            }
            $docs[] = $tmp;
          break;
        }
      }
    }
  }
  // Iterate over the folders if they exist
  if ($folderList !== false) {
    foreach ($folderList as $folder) {
      $tmp = array();
      $tmp['is_folder'] = 1;
      $tmp['name'] = basename($folder);
      $tmp['path'] = str_replace(DS, '/', vnT_Path::clean($basePath . DS . $folder));
      $tmp['path_relative'] = str_replace($mediaBase, '', $tmp['path']);
      $count = countFiles($tmp['path']);
      $tmp['files'] = $count[0];
      $tmp['folders'] = $count[1];
      $tmp['size'] = parseSize($count[2]);
      $folders[] = $tmp;
    }
  }
  $list = array(
    'folders' => $folders , 
    'docs' => $docs , 
    'images' => $images);
  return $list;
}
?>