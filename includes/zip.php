<?php

/**************************************************************************
 *                                                                        *
 *    4images - A Web Based Image Gallery Management System               *
 *    ----------------------------------------------------------------    *
 *                                                                        *
 *             File: zip.php                                              *
 *        Copyright: (C) 2002 Jan Sorgalla                                *
 *            Email: jan@4homepages.de                                    *
 *              Web: http://www.4homepages.de                             *
 *    Scriptversion: 1.7.1                                                *
 *                                                                        *
 *    Never released without support from: Nicky (http://www.nicky.net)   *
 *                                                                        *
 **************************************************************************
 *                                                                        *
 *    4images ist KEINE Freeware. Bitte lesen Sie die Lizenz-             *
 *    bedingungen (Lizenz.txt) fr weitere Informationen.                 *
 *    ---------------------------------------------------------------     *
 *    4images is NOT freeware! Please read the Copyright Notice           *
 *    (Licence.txt) for further information.                              *
 *                                                                        *
 *************************************************************************/
#
/*
Zip file creation class
makes zip files on the fly...

Based on classes by:

Eric Mueller
http://www.themepark.com

Denis O.Philippov
http://www.atlant.ru
*/
class Zipfile
{
  var $datasec = array(); // array to store compressed data
  var $ctrl_dir = array(); // central directory
  var $eof_ctrl_dir = "\x50\x4b\x05\x06\x00\x00\x00\x00"; //end of Central directory record
  var $old_offset = 0;

  function add_file ($data, $name)
  {
    $name = str_replace('\\', '/', $name);
    //$name = str_replace("\\", "\\\\", $name);
    $fr = "\x50\x4b\x03\x04";
    $fr .= "\x14\x00"; // ver needed to extract
    $fr .= "\x00\x00"; // gen purpose bit flag
    $fr .= "\x08\x00"; // compression method
    $fr .= "\x00\x00\x00\x00"; // last mod time and date
    $unc_len = strlen($data);
    $crc = crc32($data);
    $zdata = gzcompress($data);
    $zdata = substr(substr($zdata, 0, strlen($zdata) - 4), 2); // fix crc bug
    $c_len = strlen($zdata);
    $fr .= pack('V', $crc); // crc32
    $fr .= pack('V', $c_len); //compressed filesize
    $fr .= pack('V', $unc_len); //uncompressed filesize
    $fr .= pack('v', strlen($name)); //length of filename
    $fr .= pack('v', 0); //extra field length
    $fr .= $name;
    // end of "local file header" segment
    // "file data" segment
    $fr .= $zdata;
    // "data descriptor" segment (optional but necessary if archive is not served as file)
    $fr .= pack('V', $crc); //crc32
    $fr .= pack('V', $c_len); //compressed filesize
    $fr .= pack('V', $unc_len); //uncompressed filesize
    // add this entry to array
    $this->datasec[] = $fr;
    $new_offset = strlen(implode('', $this->datasec));
    // now add to central directory record
    $cdrec = "\x50\x4b\x01\x02";
    $cdrec .= "\x00\x00"; // version made by
    $cdrec .= "\x14\x00"; // version needed to extract
    $cdrec .= "\x00\x00"; // gen purpose bit flag
    $cdrec .= "\x08\x00"; // compression method
    $cdrec .= "\x00\x00\x00\x00"; // last mod time & date
    $cdrec .= pack('V', $crc); // crc32
    $cdrec .= pack('V', $c_len); //compressed filesize
    $cdrec .= pack('V', $unc_len); //uncompressed filesize
    $cdrec .= pack('v', strlen($name)); //length of filename
    $cdrec .= pack('v', 0); //extra field length
    $cdrec .= pack('v', 0); //file comment length
    $cdrec .= pack('v', 0); //disk number start
    $cdrec .= pack('v', 0); //internal file attributes
    $cdrec .= pack('V', 32); //external file attributes - 'archive' bit set
    $cdrec .= pack('V', $this->old_offset); //relative offset of local header
    $this->old_offset = $new_offset;
    $cdrec .= $name;
    // optional extra field, file comment goes here
    // save to central directory
    $this->ctrl_dir[] = $cdrec;
  }

  function file ()
  { // dump out file
    $data = implode('', $this->datasec);
    $ctrldir = implode('', $this->ctrl_dir);
    return $data . $ctrldir . $this->eof_ctrl_dir . pack('v', sizeof($this->ctrl_dir)) . // total # of entries "on this disk"
pack('v', sizeof($this->ctrl_dir)) . // total # of entries overall
pack('V', strlen($ctrldir)) . // size of central dir
pack('V', strlen($data)) . // offset to start of central dir
"\x00\x00"; // .zip file comment length
  }
} // end of class
?>